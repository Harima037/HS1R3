@extends('layouts.Modulo')

@section('title-page') Dashboard @stop

@section('js')
@parent	
@stop


@section('aside')
@stop

@section('content')
<div class="row">
	<div class="col-md-6">
		<div class="panel panel-default datagrid">
		    <div class="panel-heading"> <h1>Bienvenido</h1></div>
	        
	        <div class="list-group">
			    <a class="list-group-item" href="#" ><h4><span class="glyphicon glyphicon-file"></span> Reporte General</h4></a>			    
			    
			</div>  
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel panel-default datagrid">
		    <div class="panel-heading"> <h4>Panel A</h4></div>
	        <ul class="list-group">
			    <li class="list-group-item">Inicio: <h4 class="pull-right" style="margin:0px; padding:0px"><span class="label label-default " id="inicio-periodo"></span> </h4></li>
			    <li class="list-group-item">Fin: <h4 class="pull-right" style="margin:0px; padding:0px"><span class="label label-default " id="fin-periodo"></span> </h4></li>
				<li class="list-group-item">Ejercicio: <h4 class="pull-right" style="margin:0px; padding:0px"><span class="label label-default " id="ejercicioActivo"></span> </h4></li>			    			    
			</ul>  
		</div>
	</div>
</div>  



@stop


@section('modals')
@parent
@stop
	
