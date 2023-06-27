<?php

namespace App\Http\Controllers\Restaurant;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use App\Transaction;
use App\User;

use App\Utils\Util;
use App\Utils\RestaurantUtil;

class OrderController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $restUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @param RestaurantUtil $restUtil
     * @return void
     */
    public function __construct(Util $commonUtil, RestaurantUtil $restUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->restUtil = $restUtil;
    }

    /**
     * Display a listing of the resource.
     * Modified By Marco Marin 06-2023 add new filters, by date and by table.
     * @return Response
     */
    public function index()
    {
        // if (!auth()->user()->can('sell.view')) {
        //     abort(403, 'Unauthorized action.');
        // }
        $business_id = request()->session()->get('user.business_id');
        $user_id = request()->session()->get('user.id');

        $is_service_staff = false;
        $orders = [];
        $service_staff = [];
        
        //for service staff
        if ($this->restUtil->is_service_staff($user_id)) {
            $is_service_staff = true;
            //for user loged
            //for id user
            if (empty(request()->service_staff) && empty(request()->date) && empty(request()->table)) {
                $orders = $this->restUtil->getAllOrders($business_id, ['waiter_id' => $user_id]);
            }
            //for date
            elseif (empty(request()->service_staff) && !empty(request()->date) && empty(request()->table)) {
                $orders = $this->restUtil->getAllOrders($business_id, ['waiter_id' => $user_id,'date' => request()->date]);
            }
            //for table
            elseif (empty(request()->service_staff) && empty(request()->date) && !empty(request()->table)) {
                $orders = $this->restUtil->getAllOrders($business_id, ['waiter_id' => $user_id,'table' => request()->table]);
            }
            //for date and table
            elseif(empty(request()->service_staff) && !empty(request()->date) && !empty(request()->table)){
                $orders = $this->restUtil->getAllOrders($business_id, ['waiter_id' => $user_id,'date'=>request()->date,'table'=>request()->table]);
            }  
            //------end user loged---------
            
            //for user selected
                //for id user
                elseif (!empty(request()->service_staff) && empty(request()->date) && empty(request()->table)) {
                    $orders = $this->restUtil->getAllOrders($business_id, ['waiter_id' => request()->service_staff]);
                }
                //for id user and date
                elseif(!empty(request()->service_staff) && !empty(request()->date) && empty(request()->table)){
                    $orders = $this->restUtil->getAllOrders($business_id, ['waiter_id' => request()->service_staff,'date'=>request()->date]);
                }
                //for id user and table
                elseif(!empty(request()->service_staff) && empty(request()->date) && !empty(request()->table)){
                    $orders = $this->restUtil->getAllOrders($business_id, ['waiter_id' => request()->service_staff,'table'=>request()->table]);
                }
                //for id user, date and table
                elseif(!empty(request()->service_staff) && !empty(request()->date) && !empty(request()->table)){
                    $orders = $this->restUtil->getAllOrders($business_id, ['waiter_id' => request()->service_staff,'date'=>request()->date,'table'=>request()->table]);
                }
            //------ end user selected-------------
        }

        //for id user
        elseif (!empty(request()->service_staff) && empty(request()->date) && empty(request()->table)) {
            $orders = $this->restUtil->getAllOrders($business_id, ['waiter_id' => request()->service_staff]);
        }
        //for date
        elseif (empty(request()->service_staff) && !empty(request()->date) && empty(request()->table)) {
            $orders = $this->restUtil->getAllOrders($business_id, ['date' => request()->date]);
        }
        //for table
        elseif (empty(request()->service_staff) && empty(request()->date) && !empty(request()->table)) {
            $orders = $this->restUtil->getAllOrders($business_id, ['table' => request()->table]);
        }
        //for id user and date
        elseif(!empty(request()->service_staff) && !empty(request()->date) && empty(request()->table)){
            $orders = $this->restUtil->getAllOrders($business_id, ['waiter_id' => request()->service_staff,'date'=>request()->date]);
        }
        //for id user and table
        elseif(!empty(request()->service_staff) && empty(request()->date) && !empty(request()->table)){
            $orders = $this->restUtil->getAllOrders($business_id, ['waiter_id' => request()->service_staff,'table'=>request()->table]);
        }
        //for date user and table
        elseif(empty(request()->service_staff) && !empty(request()->date) && !empty(request()->table)){
            $orders = $this->restUtil->getAllOrders($business_id, ['date' => request()->date,'table'=>request()->table]);
        }
        //for id user, date and table
        elseif(!empty(request()->service_staff) && !empty(request()->date) && !empty(request()->table)){
            $orders = $this->restUtil->getAllOrders($business_id, ['waiter_id' => request()->service_staff,'date'=>request()->date,'table'=>request()->table]);
        }
        
        //for all
        else{
            $orders = $this->restUtil->getAllOrders($business_id, []);
        }
        

        if (!$is_service_staff) {
            $service_staff = $this->restUtil->service_staff_dropdown($business_id);
        }

        //get all tables
        $tables=[];
        $tables=$this->restUtil->getTables($business_id);

        return view('restaurant.orders.index', compact('orders', 'is_service_staff', 'service_staff','tables'));
    }

    /**
     * Marks an order as served
     * @return json $output
     */
    public function markAsServed($id)
    {
        // if (!auth()->user()->can('sell.update')) {
        //     abort(403, 'Unauthorized action.');
        // }
        try {
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $transaction = Transaction::where('business_id', $business_id)
                            ->where('type', 'sell')
                            ->where('res_waiter_id', $user_id)
                            ->find($id);
            if (!empty($transaction)) {
                $transaction->res_order_status = 'served';
                $transaction->save();
                $output = ['success' => 1,
                            'msg' => trans("restaurant.order_successfully_marked_served")
                        ];
            } else {
                $output = ['success' => 0,
                            'msg' => trans("messages.something_went_wrong")
                        ];
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => trans("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    //------------------------Add by Marco Marin 10/05/2023--------------------------------
    /**
     * Marks an order as served by admin
     * @return json $output
     */
    public function markAsServedAdmin($id,$id_user)
    {
        try {
            
            $business_id = request()->session()->get('user.business_id');
            
            if(!empty($id_user) && $id_user != null){
                $user_id=$id_user;
            }
            else{
                $user_id = request()->session()->get('user.id');
            }
            

            $transaction = Transaction::where('business_id', $business_id)
                            ->where('type', 'sell')
                            ->where('res_waiter_id', $user_id)
                            ->find($id);
            if (!empty($transaction)) {
                $transaction->res_order_status = 'served';
                $transaction->save();
                $output = ['success' => 1,
                            'msg' => trans("restaurant.order_successfully_marked_served")
                        ];
            } else {
                $output = ['success' => 0,
                            'msg' => trans("messages.something_went_wrong")
                        ];
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => trans("messages.something_went_wrong")
                        ];
        }

        return $output;
    }
    //-------------------------------------------------------------------------------------------------------

    

    
}
