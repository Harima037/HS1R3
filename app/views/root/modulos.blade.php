@extends('layouts.Modulo')

@section('title-page') Administración de Modulos @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/root/modulos.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default datagrid" id="datagridModulos" data-edit-row="editar">
            <div class="panel-heading"><h4><i class="fa fa-code"></i> Administración de Modulos</h4></div>
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
                        <th>Grupo</th>
                        <th>Modulo</th>
                        <th>Permisos</th>
                        <th width="80">Visible</th>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modalModuloLabel">Nuevo</h4>
                </div>
                <div class="modal-body">
                    <form action="" id="form_modulo">
                        <div class="row">
                            <div class="col-sm-12 well well-sm">
                                <div class="form-group">
                                    <label class="control-label" for="grupo">Grupo</label>
                                    <div class="input-group" style="margin:5px">                            
                                        <select class="form-control" id="grupo" name="grupo">
                                            <option value="">Selecciona un grupo</option>
                                            @foreach($sys_sistemas as $grupo)
                                                <option value="{{$grupo->id}}">{{$grupo->key}}::{{$grupo->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <span class="input-group-btn">
                                            <button class="btn btn-success" type="button" title="Editar Grupo" id="btn-editar-grupo"><span class="fa fa-pencil"></span></button>
                                            <button class="btn btn-primary" type="button" title="Agregar Grupo" id="btn-agregar-grupo"><span class="fa fa-plus"></span></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="permiso" class="control-label">Tipo de Permiso</label>
                                    <select class="form-control" id="permiso" name="permiso">
                                        <option value="">Selecciona un Permiso</option>
                                        @foreach($sys_permisos as $permiso)
                                            <option value="{{$permiso->id}}">{{$permiso->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="key">Key</label>
                                    <input type="text" class="form-control" id="key" name="key">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="nombre">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="uri">URI</label>
                                    <input type="text" class="form-control" id="uri" name="uri">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="icono">Icono</label>
                                    <input type="text" class="form-control" id="icono" name="icono" data-nivel="modulo">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <span id="icono-modulo" class="fa fa-5x fa-square"></span>
                            </div>
                            <div class="col-sm-6">
                                <div class="pull-right">
                                    <label class="checkbox">
                                        <input type="checkbox" value="1" name="visible" id="visible"> Visible
                                    </label>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="id" name="id">
                    </form>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" id="btn-eliminar-modulo"><span class="fa fa-trash"></span> Eliminar Modulo</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btn-guardar-modulo">Guardar Modulo</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- Cargar Catalogo para permisos -->
<div class="modal fade" id="modalGrupoModulo" tabindex="-1" role="dialog" aria-labelledby="modalGrupoModuloLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modalGrupoModuloLabel">Nuevo</h4>
                </div>
                <div class="modal-body">
                    <form action="" id="form_grupo">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="grupo-key">Key</label>
                                    <input type="text" class="form-control" id="grupo-key" name="grupo-key">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="grupo-nombre">Nombre</label>
                                    <input type="text" class="form-control" id="grupo-nombre" name="grupo-nombre">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="grupo-uri">URI</label>
                                    <input type="text" class="form-control" id="grupo-uri" name="grupo-uri">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="grupo-icono">Icono</label>
                                    <input type="text" class="form-control" id="grupo-icono" name="grupo-icono" data-nivel="grupo">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <span id="icono-grupo" class="fa fa-5x fa-square"></span>
                            </div>
                            <div class="col-sm-12">
                                <div class="pull-right">
                                    <label class="checkbox">
                                        <input type="checkbox" value="1" name="grupo-visible" id="grupo-visible"> Visible
                                    </label>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="grupo-id" name="grupo-id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left" id="btn-eliminar-grupo"><span class="fa fa-trash"></span> Eliminar Grupo</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btn-guardar-grupo"><span class="fa fa-save"></span> Guardar Grupo</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<!-- Dejar parent al ultimo -->
@parent
@stop