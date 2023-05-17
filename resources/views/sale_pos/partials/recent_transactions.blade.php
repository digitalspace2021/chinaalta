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
					@if(empty($transaction->res_order_status))
						<p class="bg-danger">En cocina</p> 
					@elseif($transaction->res_order_status == 'cooked')
						<p class="bg-warning">Cocinado</p>
					@elseif($transaction->res_order_status == 'served' && empty($transaction->payment_status)) 
						<p class="bg-info">Despachado</p>
					@elseif($transaction->res_order_status == 'served' && $transaction->payment_status == 'paid')	
						<p class="bg-success">Pagado</p>
					@elseif($transaction->res_order_status == 'served' && $transaction->payment_status == 'due')	
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