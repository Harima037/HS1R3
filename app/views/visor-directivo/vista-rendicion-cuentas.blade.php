@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script type="text/javascript">
	$("#loading").fadeIn();
</script>
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="{{ URL::to('js/modulos/visor-directivo/vista-rendicion-cuentas.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="panel panel-default">
<div class="panel-heading">
	<h3 class="panel-title">Rendición de cuentas del mes de {{$mes}}</h3>
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
		                <div class="col-lg-6 col-md-6"></div>
		                <div class="col-lg-6">
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
            <button type="button" class="btn btn-default" id="btn-proyecto-cancelar" data-clase-proyecto="{{$id_clasificacion}}">
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
					</ul>
					<!-- Tab panes -->
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="panel-metas">
							<br>
							<table id="tabla-avances-metas" class="table table-condensed table-bordered">
		                		<thead>
		                			<tr>
		                				<th rowspan="2" class="text-center">Jurisdicción</th>
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
						</div>
						<div role="tabpanel" class="tab-pane" id="cumplimiento-jurisdiccion">
							<br>
							<div id="grafica_cumplimiento_jurisdiccion" style="width:100%; height:300px;"></div>
						</div>
					</div>
				</div>
				<input type="hidden" name="id-avance" id="id-avance">
				<input type="hidden" name="nivel" id="nivel">
				<input type="hidden" name="id-accion" id="id-accion">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@if($trimestre_activo)
<div class="modal fade" id="modalBeneficiario" tabindex="-1" role="dialog" aria-labelledby="modalBenefLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalBenefLabel">Beneficiarios</h4>
            </div>
            <div class="modal-body">

<form id="form_beneficiario">
	<input type="hidden" name="id-beneficiario" id="id-beneficiario">
	<input type="hidden" name="hay-avance" id="hay-avance" value="0">
	<div class="panel panel-default">
		<table class="table table-condensed table-bordered">
    		<thead>
    			<tr>
    				<th>Beneficiario</th>
    				<th></th>
					<th>Total Femenino</th>
					<th>Total Masculino</th>
					<th>Total</th>
    			</tr>
    		</thead>
    		<tbody>
    			<tr>
    				<td rowspan="2" id="tipo-beneficiario" data-valor="0"></td>
    				<td>P</td>
    				<td class="cant-benficiarios" id="total-f" data-valor="0">0</td>
    				<td class="cant-benficiarios" id="total-m" data-valor="0">0</td>
    				<td class="cant-benficiarios" id="total-beneficiario" data-valor="0">0</td>
				</tr>
				<tr>
					<td>A</td>
    				<td class="cant-benficiarios" id="acumulado-f" data-valor="0">0</td>
    				<td class="cant-benficiarios" id="acumulado-m" data-valor="0">0</td>
    				<td class="cant-benficiarios" id="acumulado-beneficiario" data-valor="0">0</td>
				</tr>
    		</tbody>
    	</table>
    </div>
    <div class="form-group">
    	<input type="hidden" id="errorbeneficiarios">
    </div>
    <div role="tabpanel">
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active">
				<a href="#panel-zona" aria-controls="panel-zona" role="tab" data-toggle="tab">Zona</a>
			</li>
			<li role="presentation">
				<a href="#panel-poblacion" aria-controls="panel-poblacion" role="tab" data-toggle="tab">Población</a>
			</li>
			<li role="presentation">
				<a href="#panel-marginacion" aria-controls="panel-marginacion" role="tab" data-toggle="tab">Marginación</a>
			</li>
		</ul>
		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="panel-zona">
				<table class="table table-striped table-condensed">
					<thead>
						<tr>
							<th colspan="2">Urbana</th>
							<th colspan="2">Rural</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon">
											<span class="fa fa-female"></span>
										</span>
										<input type="number" class="form-control sub-total-zona fem" name="urbanaf" id="urbanaf">
									</div>
			                    </div>
							</td>
							<td>
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon">
											<span class="fa fa-male"></span>
										</span>
										<input type="number" class="form-control  sub-total-zona masc" name="urbanam" id="urbanam">
									</div>
			                    </div>
							</td>
							<td>
			                    <div class="form-group">
			                    	<div class="input-group">
										<span class="input-group-addon">
											<span class="fa fa-female"></span>
										</span>
										<input type="number" class="form-control sub-total-zona fem" name="ruralf" id="ruralf">
									</div>
			                    </div>
			                </td>
			                <td>
			                    <div class="form-group">
			                    	<div class="input-group">
										<span class="input-group-addon">
											<span class="fa fa-male"></span>
										</span>
										<input type="number" class="form-control sub-total-zona masc" name="ruralm" id="ruralm">
									</div>
			                    </div>
			                </td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="2">
								<span class="fa fa-link"></span> Total
							</th>
							<td>
								<div class="input-group">
									<span class="input-group-addon">
										<span class="fa fa-female"></span>
									</span>
									<div class="form-group"><span id="total-zona-f" class="form-control"></span></div>
								</div>
							</td>
							<td>
								<div class="input-group">
									<span class="input-group-addon">
										<span class="fa fa-male"></span>
									</span>
									<div class="form-group"><span id="total-zona-m" class="form-control"></span></div>
								</div>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
			<div role="tabpanel" class="tab-pane" id="panel-poblacion">
				<table class="table table-striped table-condensed">
					<thead>
						<tr>
							<th >Mestiza</th>
							<th >Indígena</th>
							<th >Inmigrante</th>
							<th >Otros</th>
						</tr>
					</thead>
					<tbody>
						<tr>
			                <td>
			                    <div class="form-group">
			                    	<div class="input-group">
										<span class="input-group-addon">
											<span class="fa fa-female"></span>
										</span>
										<input type="number" class="form-control sub-total-poblacion fem" name="mestizaf" id="mestizaf">
									</div>
			                    </div>
			                    <div class="form-group">
			                    	<div class="input-group">
										<span class="input-group-addon">
											<span class="fa fa-male"></span>
										</span>
										<input type="number" class="form-control sub-total-poblacion masc" name="mestizam" id="mestizam">
									</div>
			                    </div>
			                </td>
			                <td>
			                    <div class="form-group">
			                    	<div class="input-group">
										<span class="input-group-addon">
											<span class="fa fa-female"></span>
										</span>
										<input type="number" class="form-control sub-total-poblacion fem" name="indigenaf" id="indigenaf">
									</div>
			                    </div>
			                    <div class="form-group">
			                    	<div class="input-group">
										<span class="input-group-addon">
											<span class="fa fa-male"></span>
										</span>
										<input type="number" class="form-control sub-total-poblacion masc" name="indigenam" id="indigenam">
									</div>
			                    </div>
			                </td>
			                <td>
			                    <div class="form-group">
			                    	<div class="input-group">
										<span class="input-group-addon">
											<span class="fa fa-female"></span>
										</span>
										<input type="number" class="form-control sub-total-poblacion fem" name="inmigrantef" id="inmigrantef">
									</div>
			                    </div>
			                    <div class="form-group">
			                    	<div class="input-group">
										<span class="input-group-addon">
											<span class="fa fa-male"></span>
										</span>
										<input type="number" class="form-control sub-total-poblacion masc" name="inmigrantem" id="inmigrantem">
									</div>
			                    </div>
			                </td>	           
			                <td>
			                    <div class="form-group">
			                    	<div class="input-group">
										<span class="input-group-addon">
											<span class="fa fa-female"></span>
										</span>
										<input type="number" class="form-control sub-total-poblacion fem" name="otrosf" id="otrosf">
									</div>
			                    </div>
			                    <div class="form-group">
			                    	<div class="input-group">
										<span class="input-group-addon">
											<span class="fa fa-male"></span>
										</span>
										<input type="number" class="form-control sub-total-poblacion masc" name="otrosm" id="otrosm">
									</div>
			                    </div>
			                </td>
			            </tr>
					</tbody>
		            <tfoot>
		            	<tr>
			                <th colspan="2">
			                	<span class="fa fa-link"></span> Total
			                </th>
			                <td>
			                	<div class="input-group">
									<span class="input-group-addon">
										<span class="fa fa-female"></span>
									</span>
									<span id="total-poblacion-f" class="form-control"></span>
								</div>
			                </td>
			                <td>
			                	<div class="input-group">
									<span class="input-group-addon">
										<span class="fa fa-male"></span>
									</span>
									<span id="total-poblacion-m" class="form-control"></span>
								</div>
			                </td>
			            </tr>
		            </tfoot>
		        </table>
			</div>
			<div role="tabpanel" class="tab-pane" id="panel-marginacion">
				<table class="table table-striped table-condensed">
					<thead>
						<tr>
			                <th>Muy alta</th>
			                <th>Alta</th>
			                <th>Media</th>
			                <th>Baja</th>
			                <th>Muy baja </th>
			            </tr>
					</thead>
		            <tbody>
		            	<tr>
			                <td>
			                    <div class="form-group">
			                    	<div class="input-group">
										<span class="input-group-addon">
											<span class="fa fa-female"></span>
										</span>
										<input type="number" class="form-control sub-total-marginacion fem" name="muyaltaf" id="muyaltaf">
									</div>
			                    </div>
			                    <div class="form-group">
			                    	<div class="input-group">
										<span class="input-group-addon">
											<span class="fa fa-male"></span>
										</span>
										<input type="number" class="form-control sub-total-marginacion masc" name="muyaltam" id="muyaltam">
									</div>
			                    </div>
			                </td>
			                <td>
			                    <div class="form-group">
			                    	<div class="input-group">
										<span class="input-group-addon">
											<span class="fa fa-female"></span>
										</span>
										<input type="number" class="form-control sub-total-marginacion fem" name="altaf" id="altaf">
									</div>
			                    </div>
			                    <div class="form-group">
			                    	<div class="input-group">
										<span class="input-group-addon">
											<span class="fa fa-male"></span>
										</span>
										<input type="number" class="form-control sub-total-marginacion masc" name="altam" id="altam">
									</div>
			                    </div>
			                </td>
			                <td>
			                    <div class="form-group">
			                    	<div class="input-group">
										<span class="input-group-addon">
											<span class="fa fa-female"></span>
										</span>
										<input type="number" class="form-control sub-total-marginacion fem" name="mediaf" id="mediaf">
									</div>
			                    </div>
			                    <div class="form-group">
			                    	<div class="input-group">
										<span class="input-group-addon">
											<span class="fa fa-male"></span>
										</span>
										<input type="number" class="form-control sub-total-marginacion masc" name="mediam" id="mediam">
									</div>
			                    </div>
			                </td>
			                <td>
			                    <div class="form-group">
			                    	<div class="input-group">
										<span class="input-group-addon">
											<span class="fa fa-female"></span>
										</span>
										<input type="number" class="form-control sub-total-marginacion fem" name="bajaf" id="bajaf">
									</div>
			                    </div>
			                    <div class="form-group">
			                    	<div class="input-group">
										<span class="input-group-addon">
											<span class="fa fa-male"></span>
										</span>
										<input type="number" class="form-control sub-total-marginacion masc" name="bajam" id="bajam">
									</div>
			                    </div>
			                </td>
			                <td>
			                    <div class="form-group">
			                    	<div class="input-group">
										<span class="input-group-addon">
											<span class="fa fa-female"></span>
										</span>
										<input type="number" class="form-control sub-total-marginacion fem" name="muybajaf" id="muybajaf">
									</div>
			                    </div>
			                    <div class="form-group">
			                    	<div class="input-group">
										<span class="input-group-addon">
											<span class="fa fa-male"></span>
										</span>
										<input type="number" class="form-control sub-total-marginacion masc" name="muybajam" id="muybajam">
									</div>
			                    </div>
			                </td>
			            </tr>
		            </tbody>
		            <tfoot>
		            	<tr>
			                <th colspan="3"><span class="fa fa-link"></span> Total</th>
			                <td>
			                	<div class="input-group">
									<span class="input-group-addon">
										<span class="fa fa-female"></span>
									</span>
									<span id="total-marginacion-f" class="form-control"></span>
								</div>
			                </td>
			                <td>
			                	<div class="input-group">
									<span class="input-group-addon">
										<span class="fa fa-male"></span>
									</span>
									<span id="total-marginacion-m" class="form-control"></span>
								</div>
			                </td>
			            </tr>
		            </tfoot>
		        </table>
			</div>
		</div>
	</div>
</form>




            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-guardar" id="btn-beneficiario-guardar">Guardar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endif
<!-- Dejar parent al ultimo -->
@parent
@stop