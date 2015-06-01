@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('css')
@parent
<link href="{{ URL::to('css/chosen.bootstrap.min.css') }}" rel="stylesheet" type="text/css" media="screen">
<link href="{{ URL::to('css/typeahead.css') }}" rel="stylesheet" media="screen">
@stop

@section('js')
@parent
<script src="{{ URL::to('js/dependencias/chosen.jquery.min.js') }}"></script>
<script src="{{ URL::to('js/dependencias/typeahead.bundle.min.js') }}"></script>
<script src="{{ URL::to('js/dependencias/handlebars-v1.3.0.js') }}"></script>
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/administrador/usuarios.js')}}"></script>
<script src="{{ URL::to('js/modulos/administrador/catalogo-permisos.js')}}"></script>
@stop


@section('aside')
@stop

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default datagrid" id="datagridModulo" data-edit-row="editar">
            <div class="panel-heading"><h4><i class="fa {{ $sys_mod_activo->icono }}"></i> {{ $sys_mod_activo->nombre }}</h4></div>
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
                                <button type="button" id="btnModuloBloquear" class="btn btn-warning">
                                    <span class="glyphicon glyphicon-ban-circle"></span> Bloquear / Desbloquear
                                </button>
                            </div>
                            <div class="btn-group" style="margin:5px">
                                <button type="button" id="btnModuloAgregar" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Agregar</button>
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
                </div>
                
            </div>               
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="check-select-all-rows"></th>
                        <th>Usuario</th>
                        <th>Nombre</th>
                        <th>Estatus</th>
                        <th>Roles</th>
                        <th style="text-align:center; width:150px;"><span class="glyphicon glyphicon-time"></span></th>
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

    <div class="modal fade" id="modalModulo" tabindex="-1" role="dialog" aria-labelledby="modalModuloLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-75-screen">
            <div class="modal-content modal-content-75-screen">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modalModuloLabel">Nuevo</h4>
                </div>
                <div class="modal-body">

                    <form action="" id="formModulo">
                        <ul class="nav nav-tabs" role="tablist">
                            <li>
                                <a href="#tab-datos" role="tab" data-toggle="tab">
                                    <span class="fa fa-user"></span> Datos
                                </a>
                            </li>
                            <li>
                                <a href="#tab-seguridad" id="nav-tab-seguridad" role="tab" data-toggle="tab">
                                    <span class="fa fa-shield"></span> Seguridad
                                </a>
                            </li>
                            <li>
                                <a href="#tab-proyectos" role="tab" data-toggle="tab">
                                    <span class="fa fa-file"></span> Proyectos
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab-datos">
                                <br>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="nombres" class="control-label">Nombre(s)</label>
                                            <input type="text" class="form-control" id="nombres" name="nombres" maxlength="255"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="apellido-paterno" class="control-label">Apellido Paterno</label>
                                            <input type="text" class="form-control" id="apellido-paterno" name="apellido-paterno" maxlength="255"/>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="apellido-materno" class="control-label">Apellido Materno</label>
                                            <input type="text" class="form-control" id="apellido-materno" name="apellido-materno" maxlength="255"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <label for="cargo" class="control-label">Cargo</label>
                                            <input type="text" class="form-control" id="cargo" name="cargo" maxlength="255"/>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="cargo" class="control-label">Departamento</label>
                                            <select class="form-control" id="departamento" name="departamento">
                                                @if($departamentos)
                                                    <option value="">Ninguno</option>
                                                    @foreach($departamentos as $item)
                                                        <option value="{{ $item->id }}">{{ $item->descripcion }}</option>
                                                    @endforeach
                                                @else
                                                <option value=''>No hay departamentos creados</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="unidad" class="control-label ">Unidad Asignada</label>
                                            {{Form::select('unidad[]',$unidades_responsables->lists('descripcion','clave'),0,array('class'=>'form-control chosen-one','id'=>'unidad','multiple'=>'multiple','data-placeholder'=>'Selecciona las unidades a asignar'))}}
                                            <p class="help-block">Si no selecciona ninguna unidad, el usuario tendrá acceso a todas las unidades disponibles.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12"><label class="control-label">Información de Contacto</label></div>
                                    <div class="col-sm-7">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                                <input type="email" class="form-control" id="email" name="email" placeholder="e-mail" maxlength="255">
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                                <input type="telefono" class="form-control" id="telefono" name="telefono" placeholder="Teléfono" maxlength="255">
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="username" class="control-label"><span class="fa fa-keyboard-o"></span> Datos de acceso</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                                <input type="text" class="form-control" id="username" name="username" placeholder="Nombre de usuario" maxlength="25"/>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                                <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" maxlength="12">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Confirmar Contraseña" maxlength="12" data-match="#password">
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="tab-pane" id="tab-seguridad">
                                <br>
                                <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="rol" class="control-label">Rol</label>
                                                <select class="form-control chosen-one" multiple onchange="cleanPermissionPanel()" name="rol[]" id="rol" data-placeholder="Selecciona los roles a asignar al usuario">
                                                    @if($sys_roles)
                                                        @foreach($sys_roles as $item)
                                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                        @endforeach
                                                    @else
                                                    <option value=''>No hay roles creados</option>
                                                    @endif
                                                </select>
                                                <p class="help-block">Se pueden seleccionar mas de un rol para el usuario.</p>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <span class="panel-title">
                                                        Permisos individuales de usuario
                                                        <div class="btn-group pull-right">
                                                            <button type="button" title="Limpiar Permisos" class="btn btn-default" id="btn-limpiar-permisos">
                                                                    <span class="fa fa-trash-o"></span>
                                                            </button>
                                                            <button type="button" title="Personalizar Permisos" class="btn btn-warning " id="btn-cargar-cat-permisos">
                                                                <span class="fa fa-shield"></span>
                                                            </button>
                                                        </div>
                                                    </span>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <table class="table" id="pnlPermissions" >                                        
                                                    <tr><td>Aún no hay permisos individuales asignados.</td></tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                            </div>

                            <div class="tab-pane" id="tab-proyectos">
                                <br>
                                <label class="control-label">
                                    <span class="fa fa-search"></span> Buscar Proyecto
                                </label>
                                <span id="estatus-busqueda-proyecto" class="pull-right"></span>
                                <input type="text" class="form-control" id="buscar-proyecto" autocomplete="off">
                                <table id="tabla-lista-proyectos" class="table table-hover table-condensed">
                                    <thead>
                                        <tr>
                                            <th>Clave</th>
                                            <th>Nombre Técnico</th>
                                            <th width="55">Quitar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="tr-proyectos-vacio">
                                            <td colspan="3"><span class="fa fa-info-circle"></span> No hay proyectos asignados</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div>
                                    <span class="pull-right">
                                        <span class="badge" id="conteo-proyectos-seleccionados">0</span> Proyecto(s) seleccionados
                                    </span>
                                </div>
                                <button type="button" class="btn btn-danger" id="btn-limpiar-proyectos">
                                    <span class="fa fa-trash"></span> Quitar todos
                                </button>
                            </div>
                        </div>      
                        <input type="hidden" id="id" name="id">
                        <!--button type="submit" class="btn btn-primary btn-guardar">Guardar</button-->
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
@include('modulos-generales.catalogo-permisos')
<!-- Dejar parent al ultimo -->
@parent
@stop
	
