@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script type="text/javascript"> $("#loading").fadeIn(); </script>
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="{{ URL::to('js/modulos/visor/presupuesto.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default" >
            <div class="panel-heading"><h4><i class="fa {{ $sys_mod_activo->icono }}"></i> {{ $sys_mod_activo->nombre }} {{$anio_captura}}</h4></div>
            <div class="panel-body">
            	<div class="row">
                    <div class="col-sm-12">
                        <h3 id="titulo_grafica"></h3>
                    </div>
            		<div class="col-sm-12">
            			<div id="grafica-presupuesto" style="width:100%; height:600px;">
            				<div class="alert alert-info"><span class="fa fa-spinner fa-spin"></span> Cargando Librerias... Por favor espere... </div>
            			</div>
                        <div id="tabla-presupuesto" style="width:100%;overflow:auto;">
                        </div>
            		</div>
                    <div class="col-sm-12">
                        <form id="form-grafica" action="" method="POST" target="_blank">
                            <input type="hidden" value="" name="imagen" id="imagen">
                            <input type="hidden" value="" name="titulo" id="titulo">
                            <input type="hidden" value="tabla" name="tipo" id="tipo">
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