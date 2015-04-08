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
<input type="hidden" id="id" name="id" value="{{$id}}">
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="fa {{ $sys_mod_activo->icono }}"></i> {{ $sys_mod_activo->nombre }}</h4></div>
            <div class="panel-body">
                <div role="tabpanel">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#panel-programa-presupuestario" aria-controls="panel-programa-presupuestario" role="tab" data-toggle="tab">
                                Programa Prespuestario
                            </a>
                        </li>
                        <li role="presentation" class="disabled">
                            <a href="#diagnostico" aria-controls="diagnostico" role="tab" data-toggle="" id="tab-link-diagnostico">Diagnóstico</a>
                        </li>
                        <li role="presentation" class="disabled">
                            <a href="#indicadores" aria-controls="indicadores" role="tab" data-toggle="" id="tab-link-indicadores">Objetivos e Indicadores</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="panel-programa-presupuestario">
                            <br>
                            <form id="form_programa_datos">
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label class="control-label" for="programa-presupuestario">Programa Presupuestario</label>
                                        <select class="form-control chosen-one" id="programa-presupuestario" name="programa-presupuestario">
                                            <option value="">Selecciona un Programa</option>
                                            @foreach($programas_presupuestarios as $programa)
                                                <option value="{{$programa->clave}}">{{$programa->clave}} {{$programa->descripcion}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label class="control-label" for="unidad-responsable">Unidad Responsable</label>
                                        <select class="form-control chosen-one" id="unidad-responsable" name="unidad-responsable">
                                            <option value="">Selecciona una Unidad</option>
                                            @foreach($unidades_responsables as $unidad)
                                                <option value="{{$unidad->clave}}">{{$unidad->clave}} {{$unidad->descripcion}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="ejercicio">Ejercicio</label>
                                        <input type="number" class="form-control" id="ejercicio" name="ejercicio">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label" for="odm">ODM</label>
                                        <select class="form-control chosen-one" id="odm" name="odm">
                                            <option value="">Selecciona un Objetivo</option>
                                            @foreach($odm as $objetivo)
                                                @if(count($objetivo->hijos))
                                                    <optgroup label="{{$objetivo->clave}} {{$objetivo->descripcion}}">
                                                        @foreach($objetivo->hijos as $hijo)
                                                            <option value="{{$hijo->id}}">{{$hijo->clave}} {{$hijo->descripcion}}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @else
                                                    <option value="{{$objetivo->id}}">{{$objetivo->clave}} {{$objetivo->descripcion}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="modalidad">Modalidad</label>
                                        <select class="form-control chosen-one" id="modalidad" name="modalidad">
                                            <option value="">Selecciona una Modalidad</option>
                                            @foreach($modalidades as $modalidad)
                                                <option value="{{$modalidad->id}}">{{$modalidad->clave}} {{$modalidad->descripcion}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label" for="fecha-inicio">Fecha de Inicio</label>
                                        <input type="date" class="form-control" id="fecha-inicio" name="fecha-inicio">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label" for="fecha-termino">Fecha de Termino</label>
                                        <input type="date" class="form-control" id="fecha-termino" name="fecha-termino">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label" for="resultados-esperados">
                                            Resultados Esperados por la Implementación
                                        </label>
                                        <textarea class="form-control" rows="5" id="resultados-esperados" name="resultados-esperados"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-9">
                                    <div class="form-group">
                                        <label class="control-label" for="enfoque-potencial">Área de Enfoque Potencial</label>
                                        <input type="text" class="form-control" id="enfoque-potencial" name="enfoque-potencial">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label" for="cuantificacion-potencial">Cuantificación</label>
                                        <input type="number" class="form-control" id="cuantificacion-potencial" name="cuantificacion-potencial">
                                    </div>
                                </div>
                                <div class="col-sm-9">
                                    <div class="form-group">
                                        <label class="control-label" for="enfoque-objetivo">Área de Enfoque Objetivo</label>
                                        <input type="text" class="form-control" id="enfoque-objetivo" name="enfoque-objetivo">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label" for="cuantificacion-objetivo">Cuantificación</label>
                                        <input type="number" class="form-control" id="cuantificacion-objetivo" name="cuantificacion-objetivo">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label" for="justificacion-programa">Justificación del Programa</label>
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
                                            <form id="form_problema">
                                            <div class="form-group">
                                                <label class="control-label" for="descripcion-problema">Árbol del Problema</label>
                                                <textarea class="form-control" id="descripcion-problema" name="descripcion-problema" rows="5"></textarea>
                                            </div>
                                            </form>
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
                                            <form id="form_objetivo">
                                            <div class="form-group">
                                                <label class="control-label" for="descripcion-objetivo">Árbol de Objetivos</label>
                                                <textarea class="form-control" id="descripcion-objetivo" name="descripcion-objetivo" rows="5"></textarea>
                                            </div>
                                            </form>
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
                            <div class="panel panel-default datagrid" id="datagridIndicadores" data-edit-row="editar_indicador">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
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
                                            <th width="100">Tipo</th>
                                            <th>Indicador</th>
                                            <th width="200">Unidad de Medida</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
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
                <input type="hidden" id="id-indicador" value="">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="tipo-indicador" class="control-label">Tipo de Indicador</label>
                            <select class="form-control chosen-one" id="tipo-indicador">
                                <option value="">Selecciona una opción</option>
                                <option value="F">Fin</option>
                                <option value="P">Proposito</option>
                            </select>
                        </div>
                    </div>
                </div>
                <br>
                {{$formulario_programa}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-guardar-indicador">Guardar Indicador</button>
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
                <form id="form_causa_efecto">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="causa">Causa</label>
                                <textarea class="form-control" id="causa" name="causa" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="efecto">Efecto</label>
                                <textarea class="form-control" id="efecto" name="efecto" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
                <input type="hidden" id="id-causa-efecto" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-guardar-causa-efecto">Guardar</button>
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
                <form id="form_medio_fin">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="medio">Medio</label>
                                <textarea class="form-control" id="medio" name="medio" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="fin">Fin</label>
                                <textarea class="form-control" id="fin" name="fin" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
                <input type="hidden" id="id-medio-fin" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-guardar-medio-fin">Guardar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@parent
@stop