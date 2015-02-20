<form id="form-fibap-antecedentes">
    <h4>Antecedentes Financieros</h4>
    <div class="row">
        <div class="col-sm-12 datagrid" id="datagridAntecedentes" data-edit-row="editar_antecedente">
            <div class="row">
                <div class="col-lg-12">
                    <div class="btn-toolbar pull-right" >
                        <div class="btn-group" style="margin:5px">
                            <button type="button" class="btn btn-success" id="btn-agregar-antecedente">
                                <span class="glyphicon glyphicon-plus"></span> Agregar antecedente
                            </button>
                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <li>
                                    <a href="#" class="btn-edit-rows">
                                        <span class="glyphicon glyphicon-edit"></span> Editar
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#" class="btn-delete-rows">
                                        <span class="glyphicon glyphicon-remove"></span> Eliminar
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="check-select-all-rows"></th>
                        <th>Año</th>
                        <th>Autorizado</th>
                        <th>Ejercido</th>
                        <th>%</th>
                        <th>Fecha de Corte</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label" for="resultados-obtenidos">Resultados Obtenidos</label>
                        <textarea class="form-control" name="resultados-obtenidos" id="resultados-obtenidos" rows="3"></textarea>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label" for="resultados-esperados">Resultados Esperados</label>
                        <textarea class="form-control" name="resultados-esperados" id="resultados-esperados" rows="3"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="pull-right">
    <button type="button" class="btn btn-primary" id="btn-fibap-antecedente-guardar">
        <span class="fa fa-save"></span> Guardar datos de la FIBAP
    </button>
</div>

<div class="modal fade" id="modal-antecedente" tabindex="-1" role="dialog" aria-labelledby="modalAntLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalAntLabel">Nuevo</h4>
            </div>
            <div class="modal-body">
                <form id="form-antecedente">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="anio-antecedente" class="control-label">Año</label>
                                <input type="number" class="form-control" id="anio-antecedente" name="anio-antecedente"/>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="fecha-corte-antecedente" class="control-label">Fecha de Corte </label>
                                <input type="date" placeholder="aaaa-mm-dd"  class="form-control" id="fecha-corte-antecedente" name="fecha-corte-antecedente"/>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="autorizado-antecedente" class="control-label">Autorizado</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="fa fa-usd"></span></span>
                                    <input type="number" class="form-control" id="autorizado-antecedente" name="autorizado-antecedente"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="ejercido-antecedente" class="control-label">Ejercido</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="fa fa-usd"></span></span>
                                    <input type="number" class="form-control" id="ejercido-antecedente" name="ejercido-antecedente"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="id-antecedente" id="id-antecedente">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-guardar" id="btn-antecedente-guardar">Guardar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->