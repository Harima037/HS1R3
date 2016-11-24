@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script type="text/javascript"> $("#loading").fadeIn(); </script>
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="{{ URL::to('js/modulos/visor/vista-avance-indicadores.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="panel panel-default">
<div class="panel-heading">
	<h3 class="panel-title">Estado de los indicadores al mes de {{$mes}}</h3>
</div>
<div class="panel-body" id="panel-rendicion-cuentas">
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#seguimiento-metas" aria-controls="seguimiento-metas" role="tab" data-toggle="tab">
                <span class="fa fa-calendar"></span> Seguimiento de Metas
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="seguimiento-metas">
        	<br>
            <div class="panel panel-default datagrid" id="datagridAcciones" data-edit-row="seguimiento_metas">
            	<div class="panel-body">
            		<div class="row">
		                <div class="col-sm-6">
		                			@if($jurisdiccion || $usuario->claveJurisdiccion)
									<div class="pull-left">
										<button type="button" class="btn btn-default" id="btn-imprimir-plan-mejora"><span class="fa fa-print"></span> Imprimir Plan de Acción de Mejora</button>
									</div>
									@endif
								</div>
		                <div class="col-sm-6">
		                    <div class="btn-toolbar pull-right" >
		                        <div class="btn-group" style="margin:5px">
		                            <button type="button" class="btn btn-primary btn-edit-rows">
		                                <span class="fa fa-eye"></span> Ver Avance de Metas
		                            </button>
		                        </div>
		                    </div>
		                </div>
		            </div>
            	</div>
	            <table class="table table-striped table-hover">
	                <thead>
	                    <tr>
	                        <th><input type="checkbox" class="check-select-all-rows"></th>
	                        <th width="60">Nivel</th>
	                        <th>Indicador</th>
	                        <th width="135" class="text-center">Meta Anual<br>Programada</th>
	                        <th width="135" class="text-center">Meta al Mes<br>Acumulada</th>
	                        <th width="135" class="text-center">Avance<br>Acumulado</th>
							<th width="135" class="text-center">Avances<br>del Mes</th>
							<th width="28"></th>
	                    </tr>
	                </thead>
	                <tbody>
	                    <tr><td></td><td colspan="6" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>
	                </tbody>
	            </table>
	        </div>
        </div>
    </div>
</div>

<div class="panel-footer">
    <div class="row">
        <div class="col-sm-12">
            <button type="button" class="btn btn-default" id="btn-proyecto-cancelar" data-clase-proyecto="{{$id_clasificacion}}" data-unidad="{{$unidad}}" data-jurisdiccion="{{$jurisdiccion}}">
                <span class="fa fa-chevron-left"></span> Regresar a la lista de Proyectos
            </button>
        </div>
    </div>
</div>
</div>
<input type="hidden" id="id" name="id" value="{{$id}}">
<input type="hidden" id="mes" name="mes" value="{{$mes_clave}}">
@stop

@section('modals')
<div class="modal fade" id="modalEditarAvance" tabindex="-1" role="dialog" aria-labelledby="modalEditarAvanceLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalEditarAvanceLabel">Seguimiento de Metas</h4>
            </div>
            <div class="modal-body">
            	<div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label">Indicador</label>
                                    <p class="form-control-static" id="indicador"></p>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">Unidad de Medida</label>
                                    <p class="form-control-static" id="unidad-medida"></p>
                                </div>
                            </div>
                            <div class="col-sm-3">
                            	<div class="form-group">
                            		<label class="control-label">Meta Total Programada</label>
                            		<p class="form-control-static" id="meta-total"></p>
								</div>
                            </div>
                        </div>
                    </div>
                </div>
				<div role="tabpanel">
					<ul class="nav nav-tabs" role="tablist" id="tabs-seguimiento-metas">
						<li role="presentation" class="active">
							<a href="#panel-metas" aria-controls="panel-metas" role="tab" data-toggle="tab">
								<span class="fa fa-table"></span> Metas
							</a>
						</li>
						<li role="presentation" id="tab-link-plan-mejora">
							<a href="#panel-plan-mejora" aria-controls="panel-plan-mejora" role="tab" data-toggle="tab">
								<span class="fa fa-file"></span> Plan de Acción de Mejora
							</a>
						</li>
						<li role="presentation">
							<a href="#cumplimiento-mensual" aria-controls="cumplimiento-mensual" id="tablink-cumplimiento-mensual" role="tab" data-toggle="tab">
								<span class="fa fa-line-chart"></span> Cumplimiento Mensual
							</a>
						</li>
						<li role="presentation">
							<a href="#cumplimiento-jurisdiccion" aria-controls="cumplimiento-jurisdiccion" id="tablink-cumplimiento-jurisdiccion" role="tab" data-toggle="tab">
								<span class="fa fa-bar-chart"></span> Cumplimiento por Jurisdicción
							</a>
						</li>
						<li role="presentation" id="tab-link-plan-mejora-jurisdicciones" class="hidden">
							<a href="#panel-plan-mejora-jurisdicciones" aria-controls="panel-plan-mejora-jurisdicciones" role="tab" data-toggle="tab">
								<span class="fa fa-file"></span> Plan de Acción de Mejora
							</a>
						</li>
						<li role="presentation" class="pull-right">
							<a href="#panel-observaciones" aria-controls="panel-observaciones" role="tab" data-toggle="tab">
								<span class="fa fa-comment"></span> Observaciones <span id="conteo-observaciones" class="badge">0</span>
							</a>
						</li>
					</ul>
					<!-- Tab panes -->
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="panel-metas">
							<br>
							<table id="tabla-avances-metas" class="table table-condensed table-bordered">
		                		<thead>
		                			<tr>
		                				<th rowspan="2" class="text-center">
		                				@if(isset($jurisdicciones))
		                					Jurisdicción
										@else
											Mes
										@endif
		                				</th>
			                			<th colspan="2" class="bg-success text-center">Meta Programada</th>
			                			<th colspan="3" class="bg-info text-center">Avance</th>
			                			<th rowspan="2" width="90" class="text-center">Porcentaje Acumulado</th>
		                			</tr>
		                			<tr>
		                				<th class="bg-success text-center">Mes</th>
		                				<th class="bg-success text-center">Acumulada</th>
		                				<th class="bg-info text-center">Acumulado</th>
		                				<th class="bg-info text-center">Mes</th>
		                				<th class="bg-info text-center">Total</th>
		                			</tr>
		                		</thead>
		                		@if(isset($jurisdicciones))
		                		<tbody>
		                			@foreach ($jurisdicciones as $clave => $jurisdiccion)
		                			<tr data-clave-jurisdiccion="{{$clave}}">
		                				<td class="accion-municipio">
		                					<span class=""></span> {{$clave}} - {{$jurisdiccion}}
		                				</td>
		                				<td id="meta-mes-{{$clave}}" class="valores"><span class="text-muted">0</span></td>
		                				<td id="meta-acumulada-{{$clave}}" class="valores bg-success"><span class="text-muted">0</span></td>
		                				<td id="avance-acumulado-{{$clave}}" class="valores"><span class="text-muted">0</span></td>
		                				<td id="avance-mes-{{$clave}}" class="valores"><span class="text-muted">0</span></td>
		                				<td id="avance-total-{{$clave}}" class="valores bg-info"><span class="text-muted">0</span></td>
		                				<td id="porcentaje-mes-{{$clave}}" class="valores"><span class="text-muted">0</span></td>
		            				</tr>
		                			@endforeach
		                		</tbody>
		                		<tfoot>
		                			<th>Totales</th>
		                			<th id="total-meta-mes">0</th>
		                			<th id="total-meta-acumulada" class="bg-success">0</th>
		                			<th id="total-avance-acumulado">0</th>
		                			<th id="total-avance-mes">0</th>
		                			<th id="total-avance-total" class="bg-info">0</th>
		                			<th id="total-porcentaje-mes">0%</th>
		                		</tfoot>
		                		@else
		                		<tbody>
		                		@foreach($meses as $clave => $mes)
		                			<tr {{($mes_clave == $clave)?'style="font-weight:bold;"':''}}>
		                				<td {{($mes_clave == $clave)?'style="border-bottom:3px solid black;"':''}} width="135" >{{$mes}}</td>
		                				<td {{($mes_clave == $clave)?'style="border-bottom:3px solid black;"':''}} width="135" class="valores" id="meta-mes-{{$clave}}"></td>
		                				<td {{($mes_clave == $clave)?'style="border-bottom:3px solid black;"':''}} width="135" class="valores bg-success" id="meta-acumulada-{{$clave}}" class="bg-success"></td>
		                				<td {{($mes_clave == $clave)?'style="border-bottom:3px solid black;"':''}} width="135" class="valores" id="avance-acumulado-{{$clave}}"></td>
		                				<td {{($mes_clave == $clave)?'style="border-bottom:3px solid black;"':''}} width="135" class="valores" id="avance-mes-{{$clave}}"></td>
		                				<td {{($mes_clave == $clave)?'style="border-bottom:3px solid black;"':''}} width="135" class="valores bg-info" id="avance-total-{{$clave}}" class="bg-info"></td>
		                				<td {{($mes_clave == $clave)?'style="border-bottom:3px solid black;"':''}} class="valores" id="porcentaje-acumulado-{{$clave}}"></td>
		                			</tr>
		                		@endforeach
		                		</tbody>
		                		@endif
		                	</table>
		                	<div id="panel-estructura-localidades" class="hidden">
		                		<table class="table table-condensed">
			                		<thead>
			                			<tr>
				                			<th>Municipios</th>
				                			<th>
				                				<select class="form-control select-lista-municipios">
				                					<option value="">Selecciona un municipio</option>
				                				</select>
				                			</th>
				                			<th width="1"><button type="button" class="btn btn-primary btn-guardar-avance-localidades">Guardar</button></th>
				                			<th width="1"><button type="button" class="btn btn-link btn-ocultar-avance-localidades">Ocultar</button></th>
				                		</tr>
			                		</thead>
			                	</table>
			                	<div style="max-height:300px; overflow-y:auto;">
			                		<table class="table table-condensed table-bordered tabla-avance-localidades" style="margin-bottom:0;">
			                			<thead>
				                			<tr>
				                				<th rowspan="2">Localidad</th>
					                			<th colspan="2" class="bg-success">Meta Programada</th>
					                			<th colspan="3" class="bg-info">Avance</th>
				                			</tr>
				                			<tr>
				                				<th class="bg-success">Acumulada</th>
				                				<th class="bg-success" nowrap="nowrap">Mes actual</th>
				                				<th class="bg-info" nowrap="nowrap">Mes actual</th>
				                				<th class="bg-info">Acumulado</th>
				                				<th class="bg-info">Total</th>
				                			</tr>
				                		</thead>
				                		<tbody></tbody>
				                	</table>
			                	</div>
			                	<table class="table table-condensed tabla-totales-municipio">
			                		<tfoot>
			                			<tr>
				                			<th>Meta Programada Acumulada</th>
				                			<td class="total-municipio-meta">0</td>
				                			<th>Avance Acumulado</th>
				                			<td class="total-municipio-avance">0</td>
				                			<th>Porcentaje de Avance</th>
				                			<td class="total-municipio-porcentaje">0%</td>
				                		</tr>
			                		</tfoot>
			                	</table>
		                	</div>
		                	@if(isset($jurisdicciones))
		                	<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label class="control-label" for="analisis-resultados">Análisis de Resultados Acumulados</label>
										<p class="form-control-static" id="analisis-resultados"></p>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="control-label" for="justificacion-acumulada">Justificación Acumulada</label>
										<p class="form-control-static" id="justificacion-acumulada"></p>
									</div>
								</div>
							</div>
							@else
							<div class="form-group hidden" id="mensaje-alerta">
		                		<label class="control-label">Observaciones:</label>
		                		<p class="form-control-static" style="font-size:bigger;">
		                			<big>
		                				De acuerdo a los resultados acumulados del indicador es necesario implementar un Plan de Acción de Mejora
		                			</big>
		                		</p>
		                	</div>
							@endif
						</div>
						<div role="tabpanel" class="tab-pane" id="panel-plan-mejora">
							<br>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<label class="control-label" for="accion-mejora">Acción de Mejora</label>
										<p class="form-control-static" id="accion-mejora"></p>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label class="control-label" for="grupo-trabajo">Grupo de Trabajo</label>
										<p class="form-control-static" id="grupo-trabajo"></p>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label class="control-label" for="fecha-inicio">Fecha de Inicio</label>
										<p class="form-control-static" id="fecha-inicio"></p>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label class="control-label" for="fecha-termino">Fecha de Término</label>
										<p class="form-control-static" id="fecha-termino"></p>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label class="control-label" for="fecha-notificacion">Fecha de Notificación</label>
										<p class="form-control-static" id="fecha-notificacion"></p>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="form-group">
										<label class="control-label" for="documentacion-comprobatoria">Documentación Comprobatoria</label>
										<p class="form-control-static" id="documentacion-comprobatoria"></p>
									</div>
								</div>
							</div>
						</div>
						<div role="tabpanel" class="tab-pane" id="cumplimiento-mensual">
							<br>
							<div id="grafica_cumplimiento_mensual" style="width:100%; height:300px;"></div>
							<div class="form-group" id="mensaje-alerta-cumplimiento">
		                		<p class="form-control-static" style="font-size:bigger;">
		                			<big id="mensaje-alerta-cumplimiento-texto"></big>
		                		</p>
		                	</div>
						</div>
						<div role="tabpanel" class="tab-pane" id="cumplimiento-jurisdiccion">
							<br>
							<div id="grafica_cumplimiento_jurisdiccion" style="width:100%; height:300px;"></div>
						</div>
						<div role="tabpanel" class="tab-pane" id="panel-plan-mejora-jurisdicciones">
							<br>
							<div class="row">
								<form action="" id="form_plan_mejora">
									<div class="col-sm-12">
										<div class="form-group">
											<label class="control-label" for="justificacion-acumulada-jurisdiccion">Justificación</label>
											<textarea rows="4" class="form-control" name="justificacion-acumulada-jurisdiccion" id="justificacion-acumulada-jurisdiccion"></textarea>
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<label class="control-label" for="accion-mejora-jurisdiccion">Acción de Mejora</label>
											<textarea rows="4" class="form-control" name="accion-mejora-jurisdiccion" id="accion-mejora-jurisdiccion"></textarea>
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<label class="control-label" for="grupo-trabajo-jurisdiccion">Grupo de Trabajo</label>
											<textarea rows="4" class="form-control" name="grupo-trabajo-jurisdiccion" id="grupo-trabajo-jurisdiccion"></textarea>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label class="control-label" for="fecha-inicio-jurisdiccion">Fecha de Inicio</label>
											<input type="date" placeholder="aaaa-mm-dd" class="form-control" name="fecha-inicio-jurisdiccion" id="fecha-inicio-jurisdiccion">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label class="control-label" for="fecha-termino-jurisdiccion">Fecha de Término</label>
											<input type="date" placeholder="aaaa-mm-dd" class="form-control" name="fecha-termino-jurisdiccion" id="fecha-termino-jurisdiccion">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label class="control-label" for="fecha-notificacion-jurisdiccion">Fecha de Notificación</label>
											<input type="date" placeholder="aaaa-mm-dd" class="form-control" name="fecha-notificacion-jurisdiccion" id="fecha-notificacion-jurisdiccion">
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<label class="control-label" for="documentacion-comprobatoria-jurisdiccion">Documentación Comprobatoria</label>
											<textarea rows="4" class="form-control" name="documentacion-comprobatoria-jurisdiccion" id="documentacion-comprobatoria-jurisdiccion"></textarea>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label class="control-label" for="responsable-informacion">Responsable de la Información</label>
											<input type="text" name="responsable-informacion" id="responsable-informacion" class="form-control">
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label class="control-label" for="cargo-responsable-informacion">Cargo del Responsable de la Información</label>
											<input type="text" name="cargo-responsable-informacion" id="cargo-responsable-informacion" class="form-control">
										</div>
									</div>
									<input type="hidden" id="mes-plan-mejora" name="mes-plan-mejora">
									<input type="hidden" id="jurisdiccion-plan-mejora" name="jurisdiccion-plan-mejora">
									<input type="hidden" id="id-proyecto-plan-mejora" name="id-proyecto-plan-mejora">
									<input type="hidden" id="id-accion-plan-mejora" name="id-accion-plan-mejora">
									<input type="hidden" id="id-plan-mejora-jurisdiccion">
								</form>
								<div class="col-sm-12">
									<div class="pull-right">
										<button type="button" class="btn btn-success" id="btn-guardar-plan-mejora"><span class="fa fa-save"></span> Guardar</button>
									</div>
								</div>
							</div>
						</div>
						<div role="tabpanel" class="tab-pane" id="panel-observaciones">
							<br>
							<table id="tabla-lista-observaciones" class="table table-condensed table-striped table-hover">
								<thead>
									<tr>
										<th>Observación</th>
										<th width="170px">Fecha</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
            </div>
            <div class="modal-footer">
            	<div class="pull-left">
                    <form id="form-grafica" action="" method="POST" target="_blank">
                        <input type="hidden" value="" name="grafica-mensual" id="grafica-mensual">
                        <input type="hidden" value="" name="grafica-jurisdiccional" id="grafica-jurisdiccional">
                        <input type="hidden" value="" name='nivel' id='nivel'>
						<input type="hidden" value="detalles-avance-indicador" name='mostrar' id='mostrar'>
						<input type="hidden" value="" name='jurisdiccion' id='jurisdiccion'>
						<input type="hidden" value="" name='id-indicador' id='id-indicador'>
                    </form>
                    <button type="button" class="btn btn-primary pull-right" id="btn-imprimir-grafica"><span class="fa fa-print"></span> Imprimir</button>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Dejar parent al ultimo -->
@parent
@stop