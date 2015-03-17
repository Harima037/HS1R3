@extends('layouts.Modulo')

@section('title-page') 403 @stop

@section('js')
@parent
@stop


@section('aside')
@stop

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default datagrid">
		    <div class="panel-heading"> <h1><span class="glyphicon glyphicon-warning-sign"></span> 403</h1></div>
	        <div class="panel-body">
				<h2>Acceso denegado.</h2>
				<p>Lo sentimos el mes de captura no se encuentra activo. Las fechas de captura para el seguimiento de metas son los 10 primeros días del mes posterior al que se desea capturar.</p>
		    </div> 
		</div>
	</div>
</div> 	

@stop


@section('modals')
@parent
@stop
	