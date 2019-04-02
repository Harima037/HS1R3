@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/administrador/lider-proyecto.js')}}"></script>
<script src="{{ URL::to('js/modulos/administrador/catalogo-permisos.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default datagrid" id="datagridResponsables" data-edit-row="editar">
            <div class="panel-heading"><h4><i class="fa {{ $sys_mod_activo->icono }}"></i> {{ $sys_mod_activo->nombre }}</h4></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <input type="text" class="form-control txt-quick-search" placeholder="Buscar">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select class="form-control" id="filtro_activos" name="filtro_activos">
                                <option value="1" selected>Activos</option>
                                <option value="0">Todos</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <button class="btn btn-default btn-quick-search" type="button"><span class="glyphicon glyphicon-search"></span></button>
                    </div>


                    <!--div class="col-lg-6 col-md-6">
                        <div class="input-group" style="margin:5px">                            
                            <input type="text" class="form-control txt-quick-search" placeholder="Buscar">
                            <span class="input-group-btn">
                                <select class="form-control" id="filtro_activos" name="filtro_activos">
                                    <option value="1" selected>Activos</option>
                                    <option value="0">Todos</option>
                                </select>
                            </span>
                            <span class="input-group-btn">
                                <button class="btn btn-default btn-quick-search" type="button"><span class="glyphicon glyphicon-search"></span></button>
                            </span>
                        </div>
                    </div-->
                    <div class="col-md-2">
                        <div class="btn-toolbar pull-right" >
                            <div class="btn-group" style="margin:5px">
                                <button type="button" id="btnRolAgregar" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Agregar</button>
                                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li><a href="#" class="btn-edit-rows"><span class="glyphicon glyphicon-edit"></span> Editar</a></li>
                                    <li><a href="#" class="btn-edit-rows"><span class="glyphicon glyphicon-edit"></span> Dar de baja</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#" class="btn-delete-rows"><span class="glyphicon glyphicon-remove"></span> Eliminar</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!--<div class="col-lg-6">
                        <div class="btn-toolbar pull-right" >
                            <div class="btn-group" style="margin:5px">
                                <button type="button" id="btnRolAgregar" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Agregar</button>
                                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li><a href="#" class="btn-edit-rows"><span class="glyphicon glyphicon-edit"></span> Editar</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#" class="btn-delete-rows"><span class="glyphicon glyphicon-remove"></span> Eliminar</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                -->
                </div>                
            </div>               
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="check-select-all-rows"></th>
                        <th>Nombre</th>
                        <th>Cargo</th>
                        <th width="100">Fecha Inicio</th>
                        <th width="100">Fecha Fin</th>
                    </tr>
                </thead>
                <tbody></tbody>
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
@stop

@section('modals')
    <div class="modal fade" id="modalRol" tabindex="-1" role="dialog" aria-labelledby="modalRolLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modalRolLabel">Nuevo</h4>
                </div>
                <div class="modal-body">
                    <form action="" id="formRol">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="nombre">Nombre</label>
                                    <input type="text" class="form-control" id="name" name="name" maxlength="255" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="email">Correo Electrónico</label>
                                    <input type="text" class="form-control" id="email" name="email" maxlength="255" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="cargo">Area</label>
                                    <select class="form-control" id="area" name="area">
                                        <option value='0'>Seleccione un area</option>
                                        @foreach($areas as $area)
                                            <option value="{{$area['id']}}">{{str_repeat('--',$area['nivelArbol'])}} {{$area['descripcion']}}</option>
                                        @endforeach
                                    </select>
                                    <div id="panel-cargando-cargo-ocupado" class="panel panel-info hidden">
                                        <div class="panel-body bg-info" style="padding:5px;">
                                            <span class="fa fa-cog fa-spin"></span> Cargando...
                                        </div>
                                    </div>
                                    <div id="panel-cargo-ocupado" class="panel panel-warning hidden">
                                        <div class="panel-body bg-warning" style="padding:5px;">
                                            <span class="fa fa-warning"></span> <strong>Cargo asignado:</strong> <span id="nombre-persona-asignada"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="cargo">Cargo</label>
                                    <input type="text" class="form-control" id="cargo" name="cargo" maxlength="255" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="email">Telefono</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono" maxlength="255" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="email">Extensión</label>
                                    <input type="text" class="form-control" id="extension" name="extension" maxlength="255" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="email">Fecha Inicio</label>
                                    <input type="text" class="form-control" id="fecha_inicio" name="fecha_inicio" maxlength="255" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="email">Fecha Fin</label>
                                    <input type="text" class="form-control" id="fecha_fin" name="fecha_fin" maxlength="255"/>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label >Acción:</label>
                                    <button type="button" class="btn btn-info" id="btn-nuevo-cargo">Asignar Nuevo Cargo</button>
                                    <button type="button" class="btn btn-danger" id="btn-terminar-cargo">Terminar Cargo Actual</button>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div id="accion-terminar-cargo" class="well well-sm">
                                fasfsadfdsf
                                </div>
                            </div>
                        </div>
                        <br>
                       
                    <input type="hidden" id="id" name="id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary btn-guardar">Guardar</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- Cargar Catalogo para permisos -->
<!-- Dejar parent al ultimo -->
@parent
@stop