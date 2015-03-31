@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('css')
@parent
<link href="{{ URL::to('css/chosen.bootstrap.min.css') }}" rel="stylesheet" type="text/css" media="screen">
@stop

@section('js')
@parent
<script src="{{ URL::to('js/dependencias/chosen.jquery.min.js') }}"></script>
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/expediente/programa-presupuestario-formulario.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="fa {{ $sys_mod_activo->icono }}"></i> {{ $sys_mod_activo->nombre }}</h4></div>
            <div class="panel-body">
                <div role="tabpanel">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#programa-presupuestario" aria-controls="programa-presupuestario" role="tab" data-toggle="tab">
                                Programa Prespuestario
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#diagnostico" aria-controls="diagnostico" role="tab" data-toggle="tab">Diagnóstico</a>
                        </li>
                        <li role="presentation">
                            <a href="#indicadores" aria-controls="indicadores" role="tab" data-toggle="tab">Indicadores</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="programa-presupuestario">
                            <br>
                            <form id="form_programa_datos">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label" for="resultados-esperados">
                                            Resultados Esperados por la Implementación
                                        </label>
                                        <textarea class="form-control" rows="5" id="resultados-esperados" name="resultados-esperados"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-10">
                                    <div class="form-group">
                                        <label class="control-label">Área de Enfoque Potencial</label>
                                        <input type="text" class="form-control" id="enfoque-potencial" name="enfoque-potencial">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label">Cuantificación</label>
                                        <input type="text" class="form-control" id="cuantificacion-potencial" name="cuantificacion-potencial">
                                    </div>
                                </div>
                                <div class="col-sm-10">
                                    <div class="form-group">
                                        <label class="control-label">Área de Enfoque Objetivo</label>
                                        <input type="text" class="form-control" id="enfoque-objetivo" name="enfoque-objetivo">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label">Cuantificación</label>
                                        <input type="text" class="form-control" id="cuantificacion-objetivo" name="cuantificacion-potencial">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label" for="resultados-esperados">Justificación del Programa</label>
                                        <textarea class="form-control" rows="5" id="justificacion-programa" name="justificacion-programa"></textarea>
                                    </div>
                                </div>
                            </div>
                            </form>
                            <div class="row">
                                <div class="col-sm-12">
                                    <button type="button" class="btn btn-primary pull-right" id="btn-programa-guardar">
                                        <span class="fa fa-save"></span> Guardar datos del Programa Presupuestario
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="diagnostico">
                            <br>
                            <div class="panel panel-primary datagrid" id="datagridProblemas" data-edit-row="editar_problema">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="control-label" for="descripcion-problema">Árbol del Problema</label>
                                                <textarea class="form-control" id="descripcion-problema" name="descripcion-problema" rows="5"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <button id="btn-guardar-problema" type="button" class="btn btn-info">
                                                <span class="fa fa-save"></span> Guardar Descripción
                                            </button>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="btn-toolbar pull-right" >
                                                <div class="btn-group" style="margin:5px">
                                                    <button type="button" class="btn btn-success btn-datagrid-agregar">
                                                        <span class="glyphicon glyphicon-plus"></span> Agregar Causa/Efecto
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
                                </div>
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" class="check-select-all-rows"></th>
                                            <th>Causas</th>
                                            <th>Efectos</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            
                            <div class="panel panel-primary datagrid" id="datagridObjetivos" data-edit-row="editar_objetivo">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="control-label" for="descripcion-objetivo">Árbol de Objetivos</label>
                                                <textarea class="form-control" id="descripcion-objetivo" name="descripcion-objetivo" rows="5"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <button id="btn-guardar-objetivo" type="button" class="btn btn-info">
                                                <span class="fa fa-save"></span> Guardar Descripción
                                            </button>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="btn-toolbar pull-right" >
                                                <div class="btn-group" style="margin:5px">
                                                    <button type="button" class="btn btn-success btn-datagrid-agregar">
                                                        <span class="glyphicon glyphicon-plus"></span> Agregar Medio/Fin
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
                                </div>
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" class="check-select-all-rows"></th>
                                            <th>Medios</th>
                                            <th>Fines</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="indicadores">
                            <br>
                            <div class="panel panel-default datagrid" id="datagridIndicadores" data-edit-row="cargar_datos_proyecto">
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
                                                <button type="button" class="btn btn-success btn-datagrid-agregar">
                                                    <span class="glyphicon glyphicon-plus"></span> Agregar Indicador
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
                                        </div>
                                    </div>
                                </div>
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" class="check-select-all-rows"></th>
                                            <th>Clave</th>
                                            <th>Nombre Técnico</th>
                                            <th>Presupuesto</th>
                                            <th style="width:100px;">Estatus</th>
                                            <th style="text-align:center; width:85px;"><span class="glyphicon glyphicon-user"></span></th>
                                            <th style="text-align:center; width:120px;"><span class="glyphicon glyphicon-calendar"></span></th>
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
                </div>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-default" id="btn-programa-cancelar">
                            <span class="fa fa-chevron-left"></span> Regresar a la lista de Programas
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('modals')
<div class="modal fade" id="modal_programa_indicador" tabindex="-1" role="dialog" aria-labelledby="modalIndLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalIndLabel">Nuevo</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <select class="form-control chosen-one" id="tipo-indicador">
                            <option value="">Selecciona una opción</option>
                            <option value="1">Fin</option>
                            <option value="2">Proposito</option>
                        </select>
                    </div>
                </div>
                <br>
                {{$formulario_programa}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-guardar">Ir a la caratula de captura</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade" id="modal_problema" tabindex="-1" role="dialog" aria-labelledby="modalProblemaLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalProblemaLabel">Nuevo</h4>
            </div>
            <div class="modal-body">
                <form id="form_problema">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="causa">Causa</label>
                                <input type="text" class="form-control" id="causa">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="efecto">Efecto</label>
                                <input type="text" class="form-control" id="efecto">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-guardar">Guardar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade" id="modal_objetivo" tabindex="-1" role="dialog" aria-labelledby="modalObjetivoLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalObjetivoLabel">Nuevo</h4>
            </div>
            <div class="modal-body">
                <form id="form_problema">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="medio">Medio</label>
                                <input type="text" class="form-control" id="medio">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="fin">Fin</label>
                                <input type="text" class="form-control" id="fin">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-guardar">Guardar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@parent
@stop