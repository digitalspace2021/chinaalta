<!-- new modal -->
<div class="modal fade" id="miModal" tabindex="-1" role="dialog" aria-labelledby="miModalLabel" aria-hidden="true" >
	<div class="modal-dialog" role="document">
	  <div class="modal-content">
		<div class="modal-header">
		  <h5 class="modal-title" id="miModalLabel">Actualizar estado</h5>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">  
		  <div class="col-sm-6">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-user-secret"></i>
                            </span>
                            <select class="form-control" name="status" id="select_status">
                                <option value="0"></option>
                                <option value="">-------------------</option>
                               @foreach ($status_list as $list_items)
                                <option value="{{$list_items['value']}}">{{$list_items['status']}}</option>
                               @endforeach
                                
                                

                            </select>
                            <input type="hidden" name='id_transaction' id="id_transaction" value="">
                        </div>
                    </div>
            
          </div>
		</div>
		<div class="modal-footer">
		  <button type="button" class="btn btn-secondary" data-dismiss="modal" id="dismiss">Cerrar</button>
		  <button type="button" class="btn btn-primary" onclick="updateStatus()">Guardar</button>
		</div>
	  </div>
	</div>
  </div>
  
<!-- End modal-->

<script>
    
    function updateStatus(){
        var select = document.getElementById('select_status');
        var selectedValue = select.value;
        var transaction = document.getElementById('id_transaction');
        var id = transaction.value;
        var dismiss = document.getElementById('dismiss');
    
        if(selectedValue !== '' || selectedValue !== 0){
            $.ajax({
                url: '/sell/pos/status',
                type: 'POST',
                data: { status: selectedValue,id_transaction:id },
                success: function(response) {
                    // La solicitud ha sido exitosa
                    if(response.success == 1){
						toastr.success(response.msg);
						get_recent_transactions('final', $('div#tab_final'));
                        dismiss.click();
					} else {
						toastr.error(response.msg);
					}
                },
                error: function(xhr, status, error) {
                    // Ocurrió un error en la solicitud
                    toastr.error(error);
                }
        });
        }

    }
</script>