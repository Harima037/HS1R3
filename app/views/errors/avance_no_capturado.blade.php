@extends('layouts.Modulo')

@section('title-page') 404 @stop

@section('js')
@parent
@stop

@section('aside')
@stop

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default datagrid">
		    <div class="panel-heading"> <h1><span class="glyphicon glyphicon-warning-sign"></span> 404</h1></div>
	        <div class="panel-body">
				<h2>Acceso denegado.</h2>
				<p>Lo sentimos el avance de este mes no esta disponible.</p>
		    </div>
		</div>
	</div>
</div>
@stop

@section('modals')
@parent
@stop
	