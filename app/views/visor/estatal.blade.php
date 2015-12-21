@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="{{ URL::to('js/modulos/visor/estatal.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default" >
            <div class="panel-heading"><h4><i class="fa {{ $sys_mod_activo->icono }}"></i> {{ $sys_mod_activo->nombre }} {{$anio_captura}}</h4></div>
            <div class="panel-body">
                <div class="btn-group btn-group-lg btn-group-justified" role="group" aria-label="estadisticas-estatales">
                    <div class="btn-group btn-group-lg" role="group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fa fa-folder"></span> Proyectos <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a id="lnk-proy-unidad" href="#"><span class="fa fa-pie-chart"></span> Proyectos por Dirección</a></li>
                            <li><a id="lnk-proy-tipos" href="#"><span class="fa fa-pie-chart"></span> Proyectos por Tipología</a></li>
                        </ul>
                    </div>
                    <div class="btn-group btn-group-lg" role="group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fa fa-list"></span> Metas <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            <li><a id="lnk-metas-unidad" href="#"><span class="fa fa-pie-chart"></span> Metas por Dirección</a></li>
                            <li><a id="lnk-metas-cumplidas" href="#"><span class="fa fa-pie-chart"></span> Cumplimiento de Metas</a></li>
                        </ul>
                    </div>
                    <div class="btn-group btn-group-lg" role="group">
                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fa fa-dollar"></span> Presupuesto <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            <li><a id="lnk-presup-fuente" href="#"><span class="fa fa-pie-chart"></span> Presupuesto por Fuente de Financiamiento</a></li>
                            <li><a id="lnk-presup-ejercido" href="#"><span class="fa fa-pie-chart"></span> Presupuesto Ejercido</a></li>
                            <li><a id="lnk-presup-ejercido-capitulo" href="#"><span class="fa fa-pie-chart"></span> Presupuesto Ejercido Por Capitulo</a></li>
                            <li class="divider"></li>
                            <li><a id="lnk-resumen-presupuesto" href="#"><span class="fa fa-table"></span> Resumen del Presupuesto</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div id="filtro-unidades" class="col-sm-6 hidden">
                        <select id="unidad" class="form-control">
                            <option value="">Todas las unidades</option>
                            @foreach($unidades_responsables as $clave => $unidad)
                            <option value="{{$clave}}">{{$unidad}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="filtro-jurisdicciones" class="col-sm-5 hidden">
                        <select id="jurisdiccion" class="form-control">
                            <option value="">Todas las jurisdicciones</option>
                            @foreach($jurisdicciones as $clave => $jurisdiccion)
                            <option value="{{$clave}}">{{$jurisdiccion}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="panel-btn-filtro" class="col-sm-1 hidden">
                        <button type="button" class="btn btn-default" id="btn-filtro" data-grafica="">
                            <span class="fa fa-filter"></span>
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <h3 id="titulo_grafica"></h3>
                    </div>
                    <div class="col-sm-12">
                        <div id="area-graficas" style="width:100%;min-height:600px;">
                            <div id="mensaje-carga-librerias" class="alert alert-info">
                                <span class="fa fa-2x fa-spinner fa-spin"></span> <big>Cargando Librerias... Por favor espere...</big>
                            </div>
                        </div>
                        <div id="area-tablas" style="width:100%;overflow:auto;">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <form id="form-grafica" action="" method="POST" target="_blank">
                            <input type="hidden" value="" name="imagen" id="imagen">
                            <input type="hidden" value="" name="imagen2" id="imagen2">
                            <input type="hidden" value="" name="titulo" id="titulo">
                            <input type="hidden" value="" name="tipo" id="tipo">
                        </form>
                        <button type="button" class="btn btn-primary pull-right" id="btn-imprimir-grafica"><span class="fa fa-print"></span> Imprimir</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('modals')
@parent
@stop