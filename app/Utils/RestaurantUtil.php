<?php
namespace App\Utils;

use Illuminate\Support\Facades\DB;

use Spatie\Permission\Models\Role;

use App\Transaction;
use App\BusinessLocation;
use App\Restaurant\ResTable;
use App\User;

class RestaurantUtil
{
    /**
     * Retrieves all orders/sales
     *
     * @param int $business_id
     * @param array $filter
     * *For new orders order_status is 'received'
     *  Modified By Marco Marin 06-2023
     * @return obj $orders
     */
    public function getAllOrders($business_id, $filter = [])
    {
        $query = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftjoin(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
                ->leftjoin(
                    'res_tables AS rt',
                    'transactions.res_table_id',
                    '=',
                    'rt.id'
                )
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final');

        //For new orders order_status is 'received'
        if (!empty($filter['order_status']) && $filter['order_status'] == 'received') {
            $query->whereNull('res_order_status');
        }

        //for id
        if (!empty($filter['waiter_id']) && empty($filter['date']) && empty($filter['table'])) {
            $query->where('transactions.res_waiter_id', $filter['waiter_id']);
        }
        //for date
        if (empty($filter['waiter_id']) && !empty($filter['date']) && empty($filter['table'])) {
            $query->whereDate('transactions.transaction_date', date('Y-m-d', strtotime($filter['date'])));
        }
        //for table
        if (empty($filter['waiter_id']) && empty($filter['date']) && !empty($filter['table'])) {
            $query->where('transactions.res_table_id', $filter['table']);
        }
        //for id and date
        if (!empty($filter['waiter_id']) && !empty($filter['date']) && empty($filter['table'])) {
            $query->where('transactions.res_waiter_id', $filter['waiter_id']);
            $query->whereDate('transactions.transaction_date', date('Y-m-d', strtotime($filter['date'])));
        }
        //for id and table
        if (!empty($filter['waiter_id']) && empty($filter['date']) && !empty($filter['table'])) {
            $query->where('transactions.res_waiter_id', $filter['waiter_id']);
            $query->where('transactions.res_table_id', $filter['table']);
        }
        //for date and table
        if (empty($filter['waiter_id']) && !empty($filter['date']) && !empty($filter['table'])) {
            $query->whereDate('transactions.transaction_date', date('Y-m-d', strtotime($filter['date'])));
            $query->where('transactions.res_table_id', $filter['table']);
        }
        //for id, date and table
        if (!empty($filter['waiter_id']) && !empty($filter['date']) && !empty($filter['table'])) {
            $query->where('transactions.res_waiter_id', $filter['waiter_id']);
            $query->whereDate('transactions.transaction_date', date('Y-m-d', strtotime($filter['date'])));
            $query->where('transactions.res_table_id', $filter['table']);
        }
                
        $orders =  $query->select(
            'transactions.*',
            'contacts.name as customer_name',
            'bl.name as business_location',
            'rt.name as table_name'
        )
                ->orderBy('created_at', 'desc')
                ->get();

        return $orders;
    }

    public function service_staff_dropdown($business_id)
    {
        //Get all service staff roles
        $service_staff_roles = Role::where('business_id', $business_id)
                                ->where('is_service_staff', 1)
                                ->get()
                                ->pluck('name')
                                ->toArray();

        $service_staff = [];

        //Get all users of service staff roles
        if (!empty($service_staff_roles)) {
            $service_staff = User::where('business_id', $business_id)->role($service_staff_roles)->get()->pluck('first_name', 'id');
        }

        return $service_staff;
    }

    public function is_service_staff($user_id)
    {
        $is_service_staff = false;
        $user = User::find($user_id);
        if ($user->roles->first()->is_service_staff == 1) {
            $is_service_staff = true;
        }

        return $is_service_staff;
    }

    //-------------------------------------------------------------
    //Check the available tables, Add By Marco Marin 06-2023
    public function getTables($business_id){
        $query=ResTable::where('business_id', $business_id)
        ->orderBy('name','asc')
        ->get()
        ->pluck('name', 'id');

        return $query;
    }
}
