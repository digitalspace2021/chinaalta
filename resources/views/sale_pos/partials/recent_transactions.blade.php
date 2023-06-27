@php
//add by marco Marin 08-06-2023
	$status_act=null;
	$status_list = [
    ['status' => 'En Cocina', 'value' => 1],
    ['status' => 'Cocinado', 'value' => 2],
    ['status' => 'Despachado', 'value' => 3]
];
$status=["1"=>"En Cocina","2"=>"Cocinado","3"=>"Despachado","4"=>"Pagado"];
@endphp
<!-- filters add By Marco Marin 11-06-2023-->
<div class="container">
	<h3>Filtros</h3>
	<div style="row">
				
		@if (!empty($tables))
		
			<div class="col-md-2">
				<label for="">Por mesa</label>
				<select name="table_filter" id="table_filter">
					<option value="">Seleccione una mesa</option>
					@foreach($tables as $id=>$table)
						<option value="{{$id}}">{{$table}}</option>
					@endforeach
				</select>
			</div>
		@endif
		

		<div class="col-md-2">
			<label for="">Por estado</label>
			<select name="status_filter" id="status_filter">
				<option value="">Se leccione un estado</option>
				@foreach($status as $id=>$sta)
					<option value="{{$id}}">{{$sta}}</option>
				@endforeach
			</select>
			</div>
	</div>
	
</div>
<br>
<!-- end filters-->

@if(!empty($transactions))
	<table class="table table-slim no-border">
		<thead>
			<tr>
			  <th scope="col">#</th>
			  <th scope="col">Factura</th>
			  <th scope="col">Cliente</th>
			  <th scope="col">Mesero</th>
			  <th scope="col">Mesa</th>
			  <th scope="col">Fecha</th>
			  <th scope="col">Total</th></th>
			  <th scope="col">Estado</th>
			  <th scope="col">Acciones</th>
			</tr>
		  </thead>
		  <tbody>
		@foreach ($transactions as $transaction)
		
			<tr class="cursor-pointer" 
	    		data-toggle="tooltip"
	    		data-html="true"
	    		title="Customer: {{optional($transaction->contact)->name}} 
		    		@if(!empty($transaction->contact->mobile) && $transaction->contact->is_default == 0)
		    			<br/>Mobile: {{$transaction->contact->mobile}}
		    		@endif
	    		" >
				<td>
					{{ $loop->iteration}}.
				</td>
				<td>
					{{ $transaction->invoice_no }} 
				</td>
				<td>
					{{optional($transaction->contact)->name}}
				</td>
				<td>
					{{optional($transaction->service_staff)->first_name}}
				</td>
				<td>
					{{optional($transaction->table)->name}}
				</td>
				<td>
					{{$transaction->transaction_date}}
				</td>
				<td class="display_currency">
					{{ $transaction->final_total }}
				</td>
				<td>
					
					@if(empty($transaction->res_order_status) && empty($transaction->payment_status)) 
						<a id="{{$transaction->id}}" href="#" onclick="modal({{$transaction->id}},'En Cocina')"  ><p class="bg-danger">En cocina</p></a>	
					@elseif($transaction->res_order_status == 'cooked' && empty($transaction->payment_status)) 
						<a id="{{$transaction->id}}" href="#" onclick="modal({{$transaction->id}},'Cocinado')" "><p class="bg-warning">Cocinado</p></a>		
					@elseif($transaction->res_order_status == 'served' && empty($transaction->payment_status)) 
						<a id="{{$transaction->id}}" href="#" onclick="modal({{$transaction->id}},'Despachado')" ><p class="bg-info">Despachado</p></a>							
					@elseif((!empty($transaction->res_order_status) || empty($transaction->res_order_status)) && $transaction->payment_status == 'paid')	
						<p class="bg-success">Pagado</p>	
					@elseif((!empty($transaction->res_order_status) || empty($transaction->res_order_status)) && $transaction->payment_status == 'due')	
						<p class="bg-danger">Debe</p>
					@endif
					
				</td>
				<td style="display: flex; align-items: stretch;">
					<!-- -----------Add by Marco Marin 11/05/2023 ----------------- -->
					<a href="#" class="btn-modal" data-href="{{ action('SellController@show', [$transaction->id])}}" data-container=".view_modal"> 
						<i class="fa fa-arrow-circle-right text-muted" title="Detalle del pedido"></i>
					</a>
					<!-- ---------------------------------------------------------------------------------- -->
					<a href="{{action('SellPosController@edit', [$transaction->id])}}" style="padding-left: 10px">
	    				<i class="fa fa-pencil text-muted" aria-hidden="true" title="{{__('lang_v1.click_to_edit')}}"></i>
	    			</a>
	    			
	    			<a href="{{action('SellPosController@destroy', [$transaction->id])}}" class="delete-sale" style="padding-left: 10px"><i class="fa fa-trash text-danger" title="{{__('lang_v1.click_to_delete')}}"></i></a>
				</td>
			</tr>
		@endforeach
		  </tbody>
	</table>
@else
	<p>@lang('sale.no_recent_transactions')</p>
@endif


<!--include modal, and send values ​​to fill the select-->
@include('sale_pos.partials.status_modal',['status_list'=>$status_list])

<script>
	function modal(id,status_act){
		//open Modal
		$('#miModal').modal('show');
		//assign value to the input, with the transaction id
		$('#id_transaction').val(id);
		//assign value to the first option of the select, with the current state
		$('#select_status option[value="0"]').text(status_act);
	}
</script>

<!-- Add new script for filtres-->
<script src="{{asset('js/filters.js')}}"></script>
<!-- End new script for filtres-->