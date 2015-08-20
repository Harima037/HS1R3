@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="{{ URL::to('js/modulos/visor/jurisdicciones.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default" >
            <div class="panel-heading"><h4><i class="fa {{ $sys_mod_activo->icono }}"></i> {{ $sys_mod_activo->nombre }}</h4></div>
            <div class="panel-body">
                <div class="input-group input-group-lg">
                    <select class="form-control" id="jurisdiccion">
                        <option value="">Todas las Jurisdicciones</option>
                        @foreach($jurisdicciones as $clave => $jurisdiccion)
                            <option value="{{$clave}}">{{$clave}} {{$jurisdiccion}}</option>
                        @endforeach
                    </select>
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-primary" id="btn-metas-cumplidas">
                            Ver % de Metas Cumplidas
                        </button>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 id="titulo_grafica"></h3>
                    </div>
                    <div class="col-sm-12">
                        <div id="area-graficas" style="width:100%;height:500px;">
                            <div id="mensaje-carga-librerias" class="alert alert-info">
                                <span class="fa fa-2x fa-spinner fa-spin"></span> <big>Cargando Librerias... Por favor espere...</big>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <form id="form-grafica" action="" method="POST" target="_blank">
                            <input type="hidden" value="" name="imagen" id="imagen">
                            <input type="hidden" value="" name="imagen2" id="imagen2">
                            <input type="hidden" value="" name="titulo" id="titulo">
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