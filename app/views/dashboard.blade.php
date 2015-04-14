@extends('layouts.Modulo')

@section('title-page') Dashboard @stop

@section('js')
@parent	
@stop


@section('aside')
@stop

@section('content')
<div class="row">
	<div class="col-md-8">
		<div class="panel panel-default datagrid">
		    <div class="panel-heading"> <h1>Bienvenido</h1></div>
	        <div class="list-group">
	        	<div class="list-group-item">
	        		<h4>Unidad Responsable: <small>{{$unidad_responsable}}</small></h4>
	        	</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="panel panel-primary">
			<div class="panel-heading"><b>Mes Actual:</b> {{$mes}} <span class="pull-right">{{date('Y')}}</span></div>
			<table class="table table-condensed">
				<tr>
					<td>Captura de avances por mes de Proyectos Institucionales y de Inversión</td>
					<td>
						<span class="{{($mes_activo)?'text-success':'text-muted'}}">
							<span class="fa {{($mes_activo)?'fa fa-check-circle':'fa fa-clock-o'}} fa-2x"></span>
						</span>
					</td>
				</tr>
				<tr>
					<td>Captura de avances por trimestre de Programas Presupuestarios</td>
					<td>
						<span class="{{($mes_trimestre == 3)?'text-success':'text-muted'}}">
							<span class="fa {{($mes_trimestre == 3)?'fa fa-check-circle':'fa fa-clock-o'}} fa-2x"></span>
						</span>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="col-md-8"></div>
	<div class="col-md-4">
		<div class="panel panel-default">
		    <div class="panel-heading"><h4><span class="fa fa-file"></span> Proyectos Capturados</h4></div>
		    @foreach ($clasificaciones as $clasificacion => $descripcion)
		    	<table class="table table-hover table-striped">
			    	<thead>
			    		<tr>
			    			<th colspan="2">{{$descripcion}}</th>
			    		</tr>
			    	</thead>
			    	<tbody>
			    		@foreach ($proyectos[$clasificacion] as $proyecto)
			    			<tr>
			    				<td>{{$proyecto['estatus']}}</td>
			    				<td><span>{{$proyecto['conteo']}}</span></td>
			    			</tr>
			    		@endforeach
			    	</tbody>
			    	<tfoot>
			    		<tr>
			    			<th>Total</th>
			    			<th>{{$total_proyectos[$clasificacion]}}</th>
			    		</tr>
			    	</tfoot>
			    </table>
		    @endforeach
		</div>
	</div>
</div>  



@stop


@section('modals')
@parent
@stop
	
