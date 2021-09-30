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
<script src="{{ URL::to('js/modulos/expediente/estrategia-institucional-formulario.js')}}"></script>
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
                            <a href="#panel-estrategia-institucional" aria-controls="panel-estrategia-institucional" role="tab" data-toggle="tab">
                                Estrategia Institucional
                            </a>
                        </li>
                        
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="panel-estrategia-institucional">
                        <br>
                        <form id="form_estrategia_datos">
                            <div class="row">
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
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label class="control-label" for="programa-sectorial">Programa Sectorial</label>
                                        <select class="form-control chosen-one" id="programa-sectorial" name="programa-sectorial">
                                            <option value="">Selecciona un Programa</option>
                                            @foreach($programas_sectoriales as $programa)
                                                <option value="{{$programa->clave}}">{{$programa->clave}} {{$programa->descripcion}}</option>
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
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label" for="estrategia-pnd">Estrategia del Objetivo del Plan Nacional</label>
                                        {{Form::select('estrategia-pnd',array('' =>'Selecciona una Estrategia') + $estrategias_nacionales->lists('descripcion','id'),0,array('class'=>'form-control chosen-one','id'=>'estrategia-pnd'))}}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label" for="odm">ODS</label>
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
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label" for="vinculacion-ped">Vinculación al PED (Plan Estatal de Desarrollo)</label>
                                        <select class="form-control chosen-one" id="vinculacion-ped" name="vinculacion-ped">
                                            <option value="">Seleciona un objetivo</option>
                                            <!-- Inicio de ejes -->
                                            @foreach ($objetivos_ped as $eje)
                                                @if(count($eje->hijos))
                                                    <optgroup label="{{$eje->clave . ' ' . $eje->descripcion}}">
                                                        <!-- Inicio de temas -->
                                                    @foreach ($eje->hijos as $tema)
                                                        @if(count($tema->hijos))
                                                            <option disabled="disabled">
                                                                {{$tema->clave . ' ' . $tema->descripcion}}
                                                            </option>
                                                            <!-- Inicio de politicas -->
                                                            @foreach ($tema->hijos as $politica)
                                                                @if(count($politica->hijos))
                                                                    <option disabled="disabled">
                                                                        {{$politica->clave . ' ' . $politica->descripcion}}
                                                                    </option>
                                                                    <!-- Inicio de objetivos -->    
                                                                    @foreach ($politica->hijos as $objetivo)
                                                                        <option value="{{$objetivo->id}}">
                                                                            {{$objetivo->clave . ' ' . $objetivo->descripcion}}
                                                                        </option>
                                                                    @endforeach
                                                                    <!-- Inicio de objetivos -->
                                                                    <option data-divider="true"></option>
                                                                @endif
                                                            @endforeach
                                                            <!-- Fin de politicas -->
                                                        @endif
                                                    @endforeach
                                                    <!-- Fin de temas -->
                                                    </optgroup>
                                                @endif
                                            @endforeach
                                            <!-- Fin de ejes -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label  class="control-label" for="descripcion-indicador">Descripción del Indicador</label>
                                        <input type="text" class="form-control" name="descripcion-indicador" id="descripcion-indicador">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="numerador">Numerador</label>
                                        <input type="text" class="form-control" name="numerador" id="numerador">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="denominador">Denominador</label>
                                        <input type="text" class="form-control" name="denominador" id="denominador">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="control-label" for="interpretacion">Interpretación</label>
                                            <input type="text" class="form-control" name="interpretacion" id="interpretacion">
                                        </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div clasS="form-group">
                                                <label class="control-label" for="tipo-ind">
                                                    Tipo
                                                </label>
                                                {{Form::select('tipo-ind',array(''=>'Seleccione un tipo') + $tipos_indicador->lists('descripcion','id'),'',array('class'=>'form-control chosen-one','id'=>'tipo-ind'))}}
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div clasS="form-group">
                                                <label class="control-label" for="dimension">
                                                    Dimensión
                                                </label>
                                                {{Form::select('dimension',array(''=>'Seleccione una dimensión') + $dimensiones->lists('descripcion','id'),'',array('class'=>'form-control chosen-one','id'=>'dimension'))}}
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-5">
                                            <div clasS="form-group">
                                                <label class="control-label" for="unidad-medida">
                                                    Unidad de Medida
                                                </label>
                                                {{Form::select('unidad-medida',array(''=>'Seleccione una unidad') + $unidades_medida->lists('descripcion','id'),'',array('class'=>'form-control chosen-one','id'=>'unidad-medida'))}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label" for="objetivo-estrategico">Objetivo Estratégico:</label>
                                        <textarea class="form-control" id="objetivo-estrategico" name="objetivo-estrategico" rows="5"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label">Información de la Programación de Metas por Trimestre</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="fuente-informacion">Fuente de Información</label>
                                        <input type="text" class="form-control" name="fuente-informacion" id="fuente-informacion">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="responsable">Responsable</label>
                                        <select class="form-control chosen-one" name="responsable" id="responsable">
                                            <option value="">Selecciona un responsable</option>
                                        </select>
                                        <span id="ayuda-responsable" class="help-block"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="linea-base">Línea Base</label>
                                        <input type="number" class="form-control" name="linea-base" id="linea-base">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="anio-base">Año Base</label>
                                        <input type="number" class="form-control" name="anio-base" id="anio-base">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label" for="formula">Fórmula</label>
                                        {{Form::select('formula',array(''=>'Seleccione una formula') + $formulas->lists('descripcion','id'),'',array('class'=>'form-control chosen-one','id'=>'formula'))}}
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="frecuencia" class="control-label">Frecuencia</label>
                                        {{Form::select('frecuencia',array(''=>'Seleccione una frecuencia') + $frecuencias->lists('descripcion','id'),'',array('class'=>'form-control chosen-one','id'=>'frecuencia'))}}
                                    </div>
                                </div> 
                            </div>
                            <div class="row">
                                                                           
                                <div class="col-sm-3"><div class="form-group">
                                        <label for="trim1" class="control-label">Trim 1</label>
                                        <input type="number" class="form-control valor-trimestre" name="trim1" id="trim1">
                                </div></div>
                                <div class="col-sm-3"><div class="form-group">
                                        <label for="trim2" class="control-label">Trim 2</label>
                                        <input type="number" class="form-control valor-trimestre" name="trim2" id="trim2">
                                </div></div>
                                <div class="col-sm-3"><div class="form-group">
                                        <label for="trim3" class="control-label">Trim 3</label>
                                        <input type="number" class="form-control valor-trimestre" name="trim3" id="trim3">
                                </div></div>
                                <div class="col-sm-3"><div class="form-group">
                                        <label for="trim4" class="control-label">Trim 4</label>
                                        <input type="number" class="form-control valor-trimestre" name="trim4" id="trim4">
                                </div></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4"><div class="form-group">
                                        <label for="valor-numerador" class="control-label">Numerador</label>
                                        <input type="text" class="form-control" readOnlye='readOnly' name="valor-numerador" id="valorNumerador">
                                </div></div>
                                <div class="col-sm-4"><div class="form-group">
                                        <label for="valor-denominador" class="control-label">Denominador</label>
                                        <input type="number" class="form-control" name="valor-denominador" id="valorDenominador">
                                </div></div>
                                <div class="col-sm-4"><div class="form-group">
                                        <label for="meta" class="control-label">Meta</label>
                                        <input type="number" class="form-control" name="meta" id="meta">
                                </div></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="well well-sm">
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <table class="table table-condensed table-bordered table-striped" id="tabla-anios-metas-indicadores">
                                                    <thead>
                                                        <tr>
                                                            <th style="text-align:center;" width="1">Año</th>
                                                            <th style="text-align:center;">Numerador</th>
                                                            <th style="text-align:center;">Denominador</th>
                                                            <th style="text-align:center;">Meta del Indicador</th>
                                                            <th style="text-align:center;" width="1">
                                                                <span class="fa fa-exclamation"></span>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="5" style="text-align:center;">
                                                                <div class="input-group" style="width:40%; margin:0 auto 0 auto;">
                                                                    <input type="number" class="form-control" placeholder="Año" id="txt-anio-agregar">
                                                                    <span class="input-group-btn">
                                                                        <button type="button" class="btn btn-info" id="btn-agregar-anio">
                                                                            <span class="fa fa-plus"></span> Agregar Año
                                                                        </button>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="row">
                                                    <input type="hidden" id="lista-meta-anios" name="lista-meta-anios">
                                                    <div class="col-sm-12">
                                                        <div clasS="form-group">
                                                            <label class="control-label" for="comportamiento-meta-estrategia">
                                                                Comportamiento
                                                            </label>
                                                            {{Form::select('comportamiento-meta-estrategia',array(''=>'Seleccione un comportamiento') + $comportamientos_accion->lists('descripcion','id'),'',array('class'=>'form-control chosen-one','id'=>'comportamiento-meta-estrategia'))}}
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <div clasS="form-group">
                                                            <label class="control-label" for="tipo-valor-meta-estrategia">
                                                                Tipo de Valor de la Meta
                                                            </label>
                                                            {{Form::select('tipo-valor-meta-estrategia',array(''=>'Seleccione un tipo') + $tipos_valor_meta->lists('descripcion','id'),'',array('class'=>'form-control chosen-one','id'=>'tipo-valor-meta-estrategia'))}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-sm-12">
                                <button type="button" class="btn btn-primary pull-right" id="btn-estrategia-guardar">
                                    <span class="fa fa-save"></span> Guardar datos de la Estrategia Institucional
                                </button>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-default" id="btn-estrategia-cancelar">
                            <span class="fa fa-chevron-left"></span> Regresar a la lista de estrategias Institucionales
                        </button>
                        <button type="button" class="btn btn-success" id="btn-enviar-estrategia">
                            <span class="fa fa-send-o"></span> Enviar Estrategia a Revisión
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop