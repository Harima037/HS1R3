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
		    <div class="panel-heading"> <h1>Bienvenido <br><small>{{ $usuario->nombreCompleto() }}</small></h1></div>
	        <div class="list-group">
	        	<div class="list-group-item">
	        		<h4>
	        			Unidades Responsables Asignadas: <br><br>
	        			<span class="text-muted">{{$unidad_responsable}}</span>
	        		</h4>
	        	</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
	@if(count($permisos))
		<div class="panel panel-primary">
			<div class="panel-heading"><b>Mes de Captura:</b> {{$mes}} <span class="pull-right">{{date('Y')}}</span></div>
			<table class="table table-condensed">
			@if($mes_activo > 0 && (isset($permisos['RENDINST']) || isset($permisos['RENDINV']) || isset($permisos['VIPROYINST']) ))
				<tr>
					<td>Captura de Seguimiento de Metas</td>
					<td>
						<span class="{{($mes_activo)?'text-success':'text-muted'}}">
							<span class="fa fa-check-circle fa-2x"></span>
						</span>
					</td>
				</tr>
				@if($mes_trimestre == 3)
				<tr>
					<td>Captura de Seguimiento de Beneficiarios</td>
					<td>
						<span class="{{($mes_activo)?'text-success':'text-muted'}}">
							<span class="fa fa-check-circle fa-2x"></span>
						</span>
					</td>
				</tr>
				<tr>
					<td>Captura de Cuenta Pública</td>
					<td>
						<span class="{{($mes_activo)?'text-success':'text-muted'}}">
							<span class="fa fa-check-circle fa-2x"></span>
						</span>
					</td>
				</tr>
				@endif
			@endif
			@if($mes_trimestre == 3)
				@if(isset($permisos['RENDPROG']))
					<tr>
						<td>Captura de Avances de Programas Presupuestarios</td>
						<td>
							<span class="{{($mes_trimestre == 3)?'text-success':'text-muted'}}">
								<span class="fa fa-check-circle fa-2x"></span>
							</span>
						</td>
					</tr>
				@endif
				@if(isset($permisos['RENDFASSA']))
					<tr>
						<td>Captura de Avances de Indicadores de FASSA</td>
						<td>
							<span class="{{($mes_trimestre == 3)?'text-success':'text-muted'}}">
								<span class="fa fa-check-circle fa-2x"></span>
							</span>
						</td>
					</tr>
				@endif
			@endif
			</table>
		</div>
		@if(isset($permisos['VIPROYINST']))
		<div class="panel panel-primary">
			<div class="panel-heading"><b>Mes de Información:</b> {{$mes_info}} <span class="pull-right">{{date('Y')}}</span></div>
		</div>
		@endif
	@endif
	<div class="panel panel-default">
		    <div class="panel-heading"><h4><span class="fa fa-file"></span> Proyectos</h4></div>
		    	<table class="table table-hover table-striped">
		    		<tbody>
		    		@foreach ($clasificaciones as $clasificacion => $descripcion)
			    		<tr>
			    			<td>{{$descripcion}}</td>
			    			<td>{{$total_proyectos[$clasificacion]}}</td>
			    		</tr>
		    		@endforeach
		    		</tbody>
		    		<tfoot>
			    		<tr>
			    			<th>Total</th>
			    			<th>{{$total_proyectos[1] + $total_proyectos[2]}}</th>
			    		</tr>
			    	</tfoot>
		    </table>
		</div>
	</div>
	<div class="col-md-8"></div>
	<div class="col-md-4">
		
	</div>
</div>  



@stop


@section('modals')
@parent
@stop
	
