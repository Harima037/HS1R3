@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('css')
@parent
<link href="{{ URL::to('css/chosen.bootstrap.min.css') }}" rel="stylesheet" type="text/css" media="screen">
@stop

@section('js')
@parent
<script src="{{ URL::to('js/dependencias/chosen.jquery.min.js') }}"></script>
<script src="{{ URL::to('js/dependencias/jquery.csv-0.71.min.js') }}"></script>
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/administrador/lider-proyecto.js')}}"></script>
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
                    <div class="col-md-2">
                        <div class="btn-toolbar pull-right" >
                            <div class="btn-group" style="margin:5px">
                                <button type="button" id="btnRolAgregar" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Agregar</button>
                                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li><a href="#" class="btn-edit-rows"><span class="glyphicon glyphicon-edit"></span> Ver</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#" class="btn-delete-rows"><span class="glyphicon glyphicon-remove"></span> Eliminar</a></li>
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
        <div class="modal-dialog" style="width:70%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modalRolLabel">Nuevo</h4>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a id="tablink-responsable" href="#formulario-responsable" role="tab" data-toggle="tab">
                                Responsable - Cargo
                            </a>
                        </li>
                        <li role="presentation" class="bg-info">
                            <a id="tablink-historial" href="#historial-cargos" role="tab" data-toggle="tab">
                                Historial Cargos <span class="badge" id="conteo-historial-cargos">0</span>
                            </a>
                        </li>
                        <li role="presentation" class="pull-right">
                            <a id="tablink-proyectos" href="#proyectos-asignados" role="tab" data-toggle="tab">
                                Proyectos <span class="badge" id="conteo-proyectos-asignados">0</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="formulario-responsable">
                            <br>
                            <form action="" id="formRol">
                                <div class="row">
                                    <div class="col-sm-9">
                                        <div class="form-group">
                                            <label for="cargo">Responsable</label>
                                            <select class="form-control chosen-one form-datos-cargo" id="responsable" name="responsable">
                                                <option value='0'>Seleccione un responsable</option>
                                                @foreach($responsables as $responsable)
                                                    <option value="{{$responsable['id']}}">{{$responsable['nombre']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Acciones (Responsable):</label>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-success btns-responsable" id="btn-nuevo-responsable"><span class="glyphicon glyphicon-plus"></span> Nuevo</button>
                                            <button type="button" class="btn btn-info btns-responsable" id="btn-editar-responsable"><span class="glyphicon glyphicon-pencil"></span> Editar</button>
                                        </div>
                                    </div>
                                    <div class="col-sm-12" id="panel-datos-responsable-cargo" class="panel panel-warning">
                                        <div class="panel-body bg-warning" style="padding:5px;">
                                            <span class="fa fa-warning"></span> <strong>Cargo asignado:</strong> <span id="cargo-asignado-responsable"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-12" id="panel-cargando-datos-responsable" class="panel panel-info">
                                        <div class="panel-body bg-info" style="padding:5px;">
                                            <span class="fa fa-cog fa-spin"></span> Cargando...
                                        </div>
                                    </div>
                                    <div class="col-sm-12" id="panel-datos-responsable">
                                        <div class="panel panel-default">
                                            <div class="panel-body bg-success">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label for="nombre">Nombre</label>
                                                            <input type="text" class="form-control" id="nombre" name="nombre" maxlength="255" />
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
                                                <input type='hidden' id="id_directorio" name="id_directorio" value=''>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <button type="button" class="btn btn-success" id="btn-guardar-datos-responsable">Guardar</button>
                                                            <button type="button" class="btn btn-default" id="btn-cancelar-datos-responsable">Cancelar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="cargo">Area</label>
                                            <select class="form-control chosen-one form-datos-cargo" id="area" name="area">
                                                <option value='0'>Seleccione un area</option>
                                                @foreach($areas as $area)
                                                    <option value="{{$area['id']}}">{{str_repeat('--',$area['nivelArbol'])}} {{$area['descripcion']}}</option>
                                                @endforeach
                                            </select>
                                            <div id="panel-cargando-cargo-ocupado" class="panel panel-info">
                                                <div class="panel-body bg-info" style="padding:5px;">
                                                    <span class="fa fa-cog fa-spin"></span> Cargando...
                                                </div>
                                            </div>
                                            <div id="panel-cargo-ocupado" class="panel panel-warning">
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
                                            <input type="text" class="form-control form-datos-cargo" id="cargo" name="cargo" maxlength="255" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="email">Telefono</label>
                                            <input type="text" class="form-control form-datos-cargo" id="telefono" name="telefono" maxlength="255" />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="email">Extensión</label>
                                            <input type="text" class="form-control form-datos-cargo" id="extension" name="extension" maxlength="255" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="email">Fecha Inicio</label>
                                            <input type="date" class="form-control form-datos-cargo" id="fecha_inicio" name="fecha_inicio" maxlength="255" />
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="email">Fecha Fin</label>
                                            <input type="date" class="form-control form-datos-cargo" id="fecha_fin" name="fecha_fin" maxlength="255"/>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <input type='hidden' id="id_cargo" name="id_cargo" value=''>
                            </form>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="historial-cargos" style="overflow-y:auto; max-height:500px;">
                            <br>
                            <table class="table" id="tabla-historial-cargos">
                                <thead>
                                    <tr>
                                        <th>Cargo</th>
                                        <th width="100">Fecha Inicio</th>
                                        <th width="100">Fecha Fin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td colspan="3">Sin cargos anteriores</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="proyectos-asignados" style="overflow-y:auto; max-height:500px;">
                            <br>
                            <table class="table" id="tabla-proyectos-asignados">
                                <thead>
                                    <tr>
                                        <th width="1">Clave</th>
                                        <th>Nombre</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td colspan="2">Sin proyectos asignados</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div id="botones-accion-modal">
                        <button type="button" class="btn btn-primary" id="btn-editar">Editar</button>
                        <button type="button" class="btn btn-info pull-left" id="btn-nuevo-cargo">Asignar Nuevo Cargo</button>
                        <button type="button" class="btn btn-danger pull-left" id="btn-terminar-cargo">Terminar Cargo Actual</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-success" id="btn-guardar">Guardar</button>
                        <button type="button" class="btn btn-danger" id="btn-finalizar-cargo">Finalizar Cargo Actual</button>
                        <button type="button" class="btn btn-success" id="btn-asignar-cargo">Asignar Nuevo Cargo</button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- Cargar Catalogo para permisos -->
<!-- Dejar parent al ultimo -->
@parent
@stop