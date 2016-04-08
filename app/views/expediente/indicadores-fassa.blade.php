@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/expediente/indicadores-fassa.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default datagrid" id="datagridIndicadores" data-edit-row="editar">
            <div class="panel-heading"><h4><i class="fa {{ $sys_mod_activo->icono }}"></i> {{ $sys_mod_activo->nombre }}</h4></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-md-7">
                                <div class="form-group">
                                    <input type="text" class="form-control txt-quick-search" placeholder="Buscar">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="checkbox" id="filtrar-ejercicio" checked="checked" aria-label="filtrar ejercicio">
                                    </span>
                                    <input type="number" class="form-control" id="ejercicio" placeholder="Ejercicio" value="{{$ejercicio}}" />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-default btn-block btn-quick-search" type="button"><span class="fa fa-search"></span></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="btn-toolbar pull-right" >
                            @section('panel-botones')
                                <div class="btn-group" style="margin:5px">
                                    <button type="button" class="btn btn-success" id="btn-agregar-indicador">
                                        <span class="glyphicon glyphicon-plus"></span> Nuevo Indicador de FASSA
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
                        <th style="width:120px;">Nivel</th>
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
@stop

@section('modals')
<div class="modal fade" id="modalIndicador" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalLabel">Nuevo</h4>
            </div>
            <div class="modal-body">
                <form id="form_indicador_fassa">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="nivel-indicador">Nivel</label>
                                <select class="form-control informacion-indicador" id="nivel-indicador" name="nivel-indicador">
                                    <option value="">Selecciona un nivel</option>
                                    @foreach($niveles as $clave => $nivel)
                                    <option value="{{$clave}}">{{$nivel}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="form-group">
                                <label class="control-label" for="indicador">Indicador</label>
                                <input type="text" class="form-control informacion-indicador" id="indicador" name="indicador">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="tipo-formula">Tipo de Fórmula</label>
                                <select class="form-control informacion-indicador" id="tipo-formula" name="tipo-formula">
                                    <option value="">Selecciona un tipo de formula</option>
                                    @foreach($tipos_formulas as $clave => $tipo)
                                    <option value="{{$clave}}">{{$tipo}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="form-group">
                                <label class="control-label" for="formula">Fórmula</label>
                                <input type="text" class="form-control informacion-indicador" id="formula" name="formula">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label" for="fuente-informacion">Fuente de Información</label>
                                <textarea class="form-control informacion-indicador" id="fuente-informacion" name="fuente-informacion" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel">
                        <ul class="nav nav-tabs" role="tablist" id="tab-lista-ejercicios">
                            <li role="presentation">
                                <a href="#formulario-meta" aria-controls="formulario-meta" role="tab" data-toggle="tab">
                                    {{$ejercicio}}
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content" id="tab-lista-metas">
                            <div role="tabpanel" class="tab-pane" id="formulario-meta">
                                <br>
                                <div id="lbl-estatus" class="row">
                                </div>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <label class="control-label" for="unidad-responsable">Unidad Responsable</label>
                                            <select class="form-control informacion-meta" id="unidad-responsable" name="unidad-responsable">
                                                <option value="">Selecciona una unidad</option>
                                                @foreach($unidades as $unidad)
                                                <option value="{{$unidad->clave}}">{{$unidad->descripcion}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <label class="control-label" for="responsable-informacion">Responsable de la Información</label>
                                            <select class="form-control informacion-meta" id="responsable-informacion" name="responsable-informacion">
                                                <option value="">Selecciona una unidad</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="control-label" for="frecuencia">Frecuencia</label>
                                            <select class="form-control informacion-meta" id="frecuencia" name="frecuencia">
                                                <option value="A">Anual</option>
                                                <option value="S">Semestral</option>
                                                <option value="T">Trimestral</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label"><span class="fa fa-lock"></span> Numerador</label>
                                            <span class="form-control" id="numerador" disabled></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label"><span class="fa fa-lock"></span> Denominador</label>
                                            <span class="form-control" id="denominador" disabled></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="control-label" for="porcentaje"><span class="fa fa-lock"></span> Porcentaje</label>
                                            <span class="form-control" id="porcentaje" disabled>%</span>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="id-meta" name="id-meta">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="id" name="id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-guardar-indicador">Guardar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Dejar parent al ultimo -->
@parent
@stop