@extends('layouts.restaurant')
@section('title', __( 'restaurant.kitchen' ))

@section('content')
<!-- Main content -->
<section class="content min-height-90hv no-print">

    <div class="row">
        <div class="col-md-12 text-center">
            <h3>@lang( 'restaurant.all_orders' ) - @lang( 'restaurant.kitchen' ) @show_tooltip(__('lang_v1.tooltip_kitchen'))</h3>
        </div>
    </div>

    <!-- new module filters //Add By marco marin 06-2023-->
    <div class="box">
        <div class="box-body">
            <div class="col-sm-6">
                {!! Form::open(['url' => action('Restaurant\KitchenController@index'), 'method' => 'get', 'id' => 'select_service_kitchen_form' ]) !!}
                
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-calendar "></i></span>
                        {!!Form::input('date','date',null,['class'=>'form-control','id'=>'date_kitchen_order'])!!}
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-cutlery  "></i></span>
                        {!! Form::select('table', $tables, null, ['class'=>'form-control','id'=>'table_kitchen_order','placeholder'=>'Seleccione una mesa']) !!}
                    </div>
                </div> 
                {!! Form::close() !!} 

            </div>
            
        </div>
    </div>
    <!-- end new module-->

	<div class="box">
        <div class="box-header">
            <button type="button" class="btn btn-sm btn-primary pull-right" id="refresh_orders"><i class="fa fa-refresh"></i> @lang( 'restaurant.refresh' )</button>
        </div>
        <div class="box-body">
            
            <input type="hidden" id="orders_for" value="kitchen">
        	<div class="row" id="orders_div">
             @include('restaurant.partials.show_orders', array('orders_for' => 'kitchen'))   
            </div>
        </div>
        <div class="overlay hide">
          <i class="fa fa-refresh fa-spin"></i>
        </div>
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function(){
            //validate when there are changes in the selects
            //Add By marco marin 06-2023
            $('input#date_kitchen_order').change( function(){
                $('form#select_service_kitchen_form').submit();
            });

            $('select#table_kitchen_order').change( function(){
                $('form#select_service_kitchen_form').submit();
            });
            //-------------------------------------------------

            $(document).on('click', 'a.mark_as_cooked_btn', function(e){
                e.preventDefault();
                swal({
                  title: LANG.sure,
                  icon: "info",
                  buttons: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var _this = $(this);
                        var href = _this.data('href');
                        $.ajax({
                            method: "GET",
                            url: href,
                            dataType: "json",
                            success: function(result){
                                if(result.success == true){
                                    toastr.success(result.msg);
                                    _this.closest('.order_div').remove();
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection