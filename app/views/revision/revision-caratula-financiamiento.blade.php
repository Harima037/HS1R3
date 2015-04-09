<div class="row">
        <div class="col-sm-12">
            <div class="form-group">
            	<div class="form-group">
        	        <label class="control-label"> Fuentes de financiamiento </label>
                    <p id="tabla-fuentesfinanciamiento" style="height:auto; width:100%;"></p>
	            </div>
            </div>
        </div>
    </div>       
	            
    <div class="row">
        <div class="col-sm-12">
            <!--<button type="button" class="btn btn-primary pull-right" id="btn-proyecto-guardar">
                <span class="fa fa-save"></span> Guardar cambios
            </button>-->
        </div>
    </div>



<div class="modal fade" id="modalFuenteFinanciamiento" tabindex="-1" role="dialog" aria-labelledby="modalFuenteLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalFuenteLabel">Nuevo</h4>
            </div>
            <div class="modal-body">
                <form id="form-fuente">
                    <input type="hidden" name="id-financiamiento" id="id-financiamiento" value="">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="fuente-financiamiento">Fuente de Financiamiento</label>
                                <select class="form-control chosen-one" id="fuente-financiamiento" name="fuente-financiamiento">
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="destino-gasto">Destino del Gasto</label>
                                <select class="form-control chosen-one" id="destino-gasto" name="destino-gasto">
                                    <option value="">Selecciona un destino del gasto</option>
                                    
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading"><b>SubFuente de Financiamiento</b></div>
                                <div class="panel-body">
                                    <div class="row">
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" id="subfuente">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-guardar" id="btn-fuente-guardar">Guardar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->