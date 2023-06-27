@extends('layouts.restaurant')
@section('title', __( 'restaurant.orders' ))

@section('content')

<!-- Main content -->
<section class="content min-height-90hv no-print">
    
    <div class="row">
        <div class="col-md-12 text-center">
            <h3>@lang( 'restaurant.all_orders' ) @show_tooltip(__('lang_v1.tooltip_serviceorder'))</h3>
        </div>
    </div>

    <?php
    //add by Marco Marin 10/05/2023
    $items=[];
    //-----------------------------
    ?>
    
    @if(!$is_service_staff)

    <?php 
        //add by Marco Marin 10/05/2023
        $items=$service_staff->all();
        //--------------
    ?>
        <div class="box">
            <div class="box-body">
                <div class="col-sm-6">
                    {!! Form::open(['url' => action('Restaurant\OrderController@index'), 'method' => 'get', 'id' => 'select_service_staff_form' ]) !!}
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user-secret"></i>
                            </span>
                            {!! Form::select('service_staff', $service_staff, null, ['class' => 'form-control select2', 'placeholder' => __('restaurant.select_service_staff'), 'id' => 'service_staff_id']); !!}
                        </div>
                    </div>
                    <!-- New fields for filter, add By Marco Marin 06-2023 -->
                    <div class="form-group">
                        <div class="input-group">
                             <span class="input-group-addon" id="basic-addon1"><i class="fa fa-calendar "></i></span>
                             {!! Form::date('date',null,['class'=>'form-control','id'=>'date-order']); !!}
                        </div>
                   </div>
                   <div class="form-group">
                        <div class="input-group">
                             <span class="input-group-addon" id="basic-addon1"><i class="fa fa-cutlery  "></i></span>
                             {!! Form::select('table',$tables,null,['class'=>'form-control','placeholder'=>'Filtrar por mesa','id'=>'table-order']); !!}
                        </div>
                   </div>
                    <!-- End fields -->
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    @endif

	<div class="box">
        <div class="box-header">
        	<h3 class="box-title">@lang( 'restaurant.all_your_orders' )</h3>
            <button type="button" class="btn btn-sm btn-primary pull-right" id="refresh_orders"><i class="fa fa-refresh"></i> @lang( 'restaurant.refresh' )</button>
        </div>
        <div class="box-body">
        	 <input type="hidden" id="orders_for" value="waiter">
        
            <div class="row" id="orders_div">
             @include('restaurant.partials.show_orders', array('orders_for' => 'waiter','user' => key($items)))   
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
        $('select#service_staff_id').change( function(){
            $('form#select_service_staff_form').submit();
        });

        //validate when a filter option is changed, added by Marco Marin 06-2023
        $('input#date-order').change( function(){
            $('form#select_service_staff_form').submit();
        });
        $('select#table-order').change( function(){
            $('form#select_service_staff_form').submit();
        });
        //End validate

        $(document).ready(function(){
            $(document).on('click', 'a.mark_as_served_btn', function(e){
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
                                    refresh_orders();
                                    toastr.success(result.msg);
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