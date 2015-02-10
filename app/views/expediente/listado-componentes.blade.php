<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default datagrid" id="datagridComponentes" data-edit-row="editar_componente">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        
                    </div>
                    <div class="col-lg-6">
                        <div class="btn-toolbar pull-right" >
                            @section('panel-botones')
                                <div class="btn-group" style="margin:5px">
                                    <button type="button" class="btn btn-success btn-agregar-componente">
                                        <span class="glyphicon glyphicon-plus"></span> Agregar componente
                                    </button>
                                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
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
                            @show
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="check-select-all-rows"></th>
                        <th>Indicador</th>
                        <th>Interpretación</th>
                        <th>Unidad de Medida</th>
                        <th style="text-align:center; width:100px;"><span class="glyphicon glyphicon-user"></span></th>
                        <th style="text-align:center; width:100px;"><span class="glyphicon glyphicon-calendar"></span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="6" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>