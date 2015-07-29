@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script type="text/javascript"> $("#loading").fadeIn(); </script>
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="{{ URL::to('js/modulos/visor/general.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default" >
            <div class="panel-heading"><h4><i class="fa {{ $sys_mod_activo->icono }}"></i> {{ $sys_mod_activo->nombre }}</h4></div>
            <div class="panel-body">
            	<div class="row">
            		<div class="col-sm-6">
            			<div id="grafica-general" style="width:100%; height:500px;">
            				<div class="alert alert-info"><span class="fa fa-spinner fa-spin"></span> Cargando Librerias... Por favor espere... </div>
            			</div>
            		</div>
            		<div class="col-sm-6">
            			<div id="grafica-avance" style="width:100%; height:500px;">
            				<div class="alert alert-info"><span class="fa fa-spinner fa-spin"></span> Cargando Librerias... Por favor espere... </div>
            			</div>
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