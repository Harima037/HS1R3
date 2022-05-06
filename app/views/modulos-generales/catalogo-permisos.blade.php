<div class="modal fade" id="modalCatalogoPermisos" tabindex="-1" role="dialog" aria-labelledby="modalModuloLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Gestión de permisos</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="input-group" style="margin:5px">                            
                                            <input type="text" class="form-control txt-quick-search" placeholder="Buscar">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default btn-quick-search" type="button">
                                                    <span class="glyphicon glyphicon-search"></span>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="overflow:auto; height:500px;">
                                <form id="formPermisos">
                                    <table id="table-list-permission" class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Grupo</th>
                                                <th>Modulo</th>
                                                <th>Permisos <a rel="popover" class="popover-dismiss" href="#" id="permissions-help" data-toggle="popover"  data-placement="bottom" data-content="<span class='label label-info'>Asignado x Rol</span> <span class='label label-success'>Permitir</span> <span class='label label-danger'>Denegar</span>" title="Colores de permisos"><i class="fa fa-question-circle"></i></a> </th>
                                                <!--th>C</th><th>R</th><th>U</th><th>D</th-->
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary btn-seleccionar">Seleccionar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->