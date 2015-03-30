<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default datagrid" id="datagridFuenteFinanciamiento" data-edit-row="editar_fuente_financiamiento">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        
                    </div>
                    <div class="col-lg-6">
                        <div class="btn-toolbar pull-right" >
                            <div class="btn-group" style="margin:5px">
                                <button type="button" class="btn btn-info" id="btn-agregar-fuente">
                                    <span class="glyphicon glyphicon-plus-sign"></span> Agregar Fuente de Financiamiento
                                </button>
                                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li>
                                        <a href="#" class="btn-edit-rows"><span class="glyphicon glyphicon-edit"></span> Editar</a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="#" class="btn-delete-rows"><span class="glyphicon glyphicon-remove"></span> Eliminar</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="check-select-all-rows"></th>
                        <th width="70">Clave</th>
                        <th>Fuente de Financiamiento</th>
                        <th>Destino</th>
                        <th width="150">SubFuentes</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
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
                                    <option value="">Selecciona una fuente de financiamiento</option>
                                    @foreach($fuentes_financiamiento as $fuente)
                                    <option value="{{$fuente->id}}">{{$fuente->clave}}. {{$fuente->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="destino-gasto">Destino del Gasto</label>
                                <select class="form-control chosen-one" id="destino-gasto" name="destino-gasto">
                                    <option value="">Selecciona un destino del gasto</option>
                                    @foreach($destino_gasto as $destino)
                                    <option data-id-fuente="{{$destino->idFuenteFinanciamiento}}" value="{{$destino->id}}" class="hidden" disabled>
                                        {{$destino->descripcion}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading"><b>SubFuente de Financiamiento</b></div>
                                <div class="panel-body">
                                    <div class="row">
                                        @foreach ($subfuentes_financiamiento as $subfuente)
                                            <div class="col-sm-6">
                                                <div class="checkbox">
                                                    <label>
                                                          <input type="checkbox"  id="subfuente_{{$subfuente->id}}" name="subfuente[]" value="{{$subfuente->id}}" >
                                                          <b>{{$subfuente->clave}}</b> {{$subfuente->descripcion}}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
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