<div class="row">
    <div class="col-md-12">
        <!--div id="panel-actividad" style="position: absolute;top: 0;left: 0;width: 100%;height: 100%;z-index: 10; margin:10px;">
            <small>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <span class="fa fa-thumb-tack"></span> Nueva Actividad
                        <button type="button" class="close">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Cerrar</span>
                        </button>
                    </div>
                    <div class="panel-body">
                        @{{$formulario_actividades}}
                    </div>
                    <div class="panel-footer">
                        <button type="button" class="btn btn-sm btn-default" id="btn-cancelar-actividad">Cancelar</button>
                        <button type="button" class="btn btn-sm btn-primary" id="btn-guardar-actividad">Guardar</button>
                    </div>
                </div>
            </small>
        </div-->

        <div class="panel panel-default datagrid" id="datagridActividades" data-edit-row="editar">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="input-group" style="margin:5px">                            
                            <input type="text" class="form-control txt-quick-search" placeholder="Buscar">
                            <span class="input-group-btn">
                                <button class="btn btn-default btn-quick-search" type="button"><span class="glyphicon glyphicon-search"></span></button>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="btn-toolbar pull-right" >
                            <div class="btn-group" style="margin:5px">
                                <button type="button" class="btn btn-primary btn-agregar-actividad">
                                    <span class="glyphicon glyphicon-plus-sign"></span> Agregar Actividad
                                </button>
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
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
                        <th>Descripción</th>
                        <th>Tipo</th>
                        <th>Indicador</th>
                        <th style="text-align:center; width:150px;"><span class="glyphicon glyphicon-user"></span></th>
                        <th style="text-align:center; width:150px;"><span class="glyphicon glyphicon-calendar"></span></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="panel-footer">
                <div class="btn-toolbar ">
                    <div class="btn-group pull-right" style="margin-left:5px; margin-bottom:5px;">
                        <button class="btn btn-default btn-back-rows"><i class="glyphicon glyphicon-arrow-left"></i></button>
                        <button class="btn btn-default btn-next-rows"><i class="glyphicon glyphicon-arrow-right"></i></button>
                    </div>
                    <div class="btn-group pull-right " style="width:200px; ">   
                        <div class="input-group" > 
                            <span class="input-group-addon">Pág.</span> 
                            <input type="text" class="txt-go-page form-control" style="text-align:center" value="1" >     
                            <span class="input-group-addon btn-total-paginas" data-pages="0">de 0</span> 
                            <div class="input-group-btn dropup">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
                                <ul class="dropdown-menu pull-right">
                                    <li><a class="btn-go-first-rows" href="#">Primera Página</a></li>
                                    <li><a class="btn-go-last-rows" href="#">Última Página</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>