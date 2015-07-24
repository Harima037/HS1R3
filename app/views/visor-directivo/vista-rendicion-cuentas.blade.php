@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
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
        @if($trimestre_activo)
        <li role="presentation">
            <a href="#seguimiento-beneficiarios" aria-controls="seguimiento-beneficiarios" role="tab" data-toggle="tab" id="tab-link-beneficiarios">
                <span class="fa fa-users"></span> Seguimiento de Beneficiarios
            </a>
        </li>
        <li role="presentation">
        	<a href="#analisis-funcional" aria-controls="analisis-funcional" role="tab" data-toggle="tab" id="tab-link-analisis-funcional">
                <span class="fa fa-check-square"></span> Análisis Funcional
            </a>
        </li>
        @endif
        <li role="presentation" class="pull-right">
        	<a href="#datos-informacion" aria-controls="datos-informacion" role="tab" data-toggle="tab">
        		<span class="fa fa-info-circle"></span> Información
        	</a>
        </li>
    </ul>
    <div class="tab-content">
    	<div role="tabpanel" class="tab-pane" id="datos-informacion">
    		<br>
            <div class="panel panel-default">
                <div class="panel-heading">
                    Información de la Programación de Metas por Mes/Jurisdicción
                </div>
                <div class="panel-body">
                    <form id="form_fuente_informacion">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="fuente-informacion">Fuente de Información</label>
                                    <input type="text" class="form-control" name="fuente-informacion" id="fuente-informacion">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="responsable">Responsable</label>
                                    <select class="form-control chosen-one" name="responsable" id="responsable">
                                        <option value="">Selecciona un responsable</option>
                                    </select>
                                    <span id="ayuda-responsable" class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="panel-footer">
                    <button type="button" class="btn btn-primary" id="btn-fuente-informacion-guardar">
                        <span class="fa fa-save"></span> Guardar Información
                    </button>
                </div>
            </div>
    	</div>
    	@if($trimestre_activo)
    	<div role="tabpanel" class="tab-pane" id="analisis-funcional">
    		<br>
    		<form id="form_analisis">
	    		<div class="row">
	    			<div class="col-sm-12">
	    				<div class="form-group">
	    					<label class="control-label" for="finalidad">Finalidad del Proyecto</label>
	    					<textarea id="finalidad" name="finalidad" rows="4" class="form-control"></textarea>
	    				</div>
	    			</div>
	    			<div class="col-sm-12">
	    				<div class="form-group">
	    					<label class="control-label" for="analisis-resultado">Análisis de Resultado</label>
	    					<textarea id="analisis-resultado" name="analisis-resultado" rows="4" class="form-control"></textarea>
	    				</div>
	    			</div>
	    			<div class="col-sm-12">
	    				<div class="form-group">
	    					<label class="control-label" for="analisis-beneficiarios">Beneficiarios</label>
	    					<textarea id="analisis-beneficiarios" name="analisis-beneficiarios" rows="4" class="form-control"></textarea>
	    				</div>
	    			</div>
	    			<div class="col-sm-12">
	    				<div class="form-group">
	    					<label class="control-label" for="justificacion-global">Justificación Global del Proyecto</label>
	    					<textarea id="justificacion-global" name="justificacion-global" rows="4" class="form-control"></textarea>
	    				</div>
	    			</div>
	    		</div>
    		</form>
    		<input type="hidden" name="id-analisis" id="id-analisis" value="{{$id_analisis}}">
    		<div class="row">
    			<div class="col-sm-6">
    				<button type="button" class="btn btn-primary" id="btn-guadar-analisis-funcional">
    					<span class="fa fa-floppy-o"></span> Guardar Análisis Funcional
    				</button>
    			</div>
    		</div>
    	</div>
    	<div role="tabpanel" class="tab-pane" id="seguimiento-beneficiarios">
    		<br>
    		<div class="panel panel-default datagrid" id="datagridBeneficiarios" data-edit-row="seguimiento_beneficiarios">
    			<div class="panel-body">
    				<div class="row">
		                <div class="col-lg-6 col-md-6"></div>
		                <div class="col-lg-6">
		                    <div class="btn-toolbar pull-right" >
		                        <div class="btn-group" style="margin:5px">
		                            <button type="button" class="btn btn-primary btn-edit-rows">
		                                <span class="fa fa-edit"></span> Actualizar Beneficiarios
		                            </button>
		                        </div>
		                    </div>
		                </div>
		            </div>
    			</div>
	            <table class="table table-striped table-hover table-bordered">
	                <thead>
	                	<tr>
	                		<th rowspan="2"><input type="checkbox" class="check-select-all-rows"></th>
	                        <th rowspan="2">Beneficiario</th>
	                        <th colspan="2">Femenino</th>
	                        <th colspan="2">Masculino</th>
	                        <th rowspan="2">Total</th>
	                        <th rowspan="2">Acumulado</th>
	                	</tr>
	                    <tr>
	                        <th>Total</th>
							<th>Acumulado</th>
	                        <th>Total</th>
	                        <th>Acumulado</th>
	                    </tr>
	                </thead>
	                <tbody>
	                    <tr><td></td><td colspan="6" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>
	                </tbody>
	            </table>
	        </div>
    	</div>
    	@endif
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
		                                <span class="fa fa-edit"></span> Actualizar Metas
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
	                        <th>Nivel</th>
	                        <th>Indicador</th>
	                        <th>Meta Programada</th>
	                        <th>Avance Acumulado</th>
							<th>Avances del Mes</th>
							<th width="50"></th>
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
            <button type="button" class="btn btn-success" id="btn-enviar-proyecto">
                <span class="fa fa-send-o"></span> Enviar Proyecto a Revisión
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
                <form action="" id="form_avance">
					<div role="tabpanel">
						<ul class="nav nav-tabs" role="tablist" id="tabs-seguimiento-metas">
							<li role="presentation" class="active">
								<a href="#panel-metas" aria-controls="panel-metas" role="tab" data-toggle="tab">
									<span class="fa fa-table"></span> Metas
								</a>
							</li>
							<li role="presentation">
								<a href="#panel-justificacion" aria-controls="panel-justificacion" role="tab" data-toggle="tab" id="tab-link-justificacion">
									<span class="fa fa-align-left"></span> Análisis y Justificación
								</a>
							</li>
							@if($trimestre_activo)
							<li role="presentation" class="disabled">
								<a href="#panel-plan-mejora" aria-controls="panel-plan-mejora" role="tab" data-toggle="" id="tab-link-plan-mejora">
									<span class="fa fa-file"></span> Plan de Acción de Mejora
								</a>
							</li>
							@endif
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
			                				<th class="bg-success text-center">Acumulada</th>
			                				<th class="bg-success text-center" nowrap="nowrap">Mes actual</th>
			                				<th class="bg-info text-center" nowrap="nowrap">Mes actual</th>
			                				<th class="bg-info text-center">Acumulado</th>
			                				<th class="bg-info text-center">Total</th>
			                			</tr>
			                		</thead>
			                		<tbody>
			                			@foreach ($jurisdicciones as $clave => $jurisdiccion)
			                			<tr data-clave-jurisdiccion="{{$clave}}">
			                				<td class="accion-municipio">
			                					<span class=""></span> {{$clave}} - {{$jurisdiccion}}
			                				</td>
			                				<td class="meta-programada bg-success" data-meta="0">0</td>
			                				<td class="meta-del-mes" data-meta-mes="0">0</td>
			                				<td>
			                					<div class="form-group" style="margin-bottom:0;">
			                						<input type="number" class="form-control avance-mes" name="avance[{{$clave}}]" id="avance_{{$clave}}" data-jurisdiccion="{{$clave}}" data-meta-programada="" min="0">
			                					</div>
			                				</td>
			                				<td class="avance-acumulado" data-acumulado="0">0</td>
			                				<td class="avance-total bg-info" data-avance-total="0">0</td>
			                				<td class="avance-mes"></td>
			            				</tr>
			                			@endforeach
			                		</tbody>
			                		<tfoot>
			                			<th>Totales</th>
			                			<th class="bg-success" id="total-meta-programada">0</th>
			                			<th id="total-meta-mes">0</th>
			                			<th id="total-avance-mes">0</th>
			                			<th id="total-avance-acumulado">0</th>
			                			<th class="bg-info" id="total-avance-total">0</th>
			                			<th id="total-porcentaje" data-estado-avance="">0%</th>
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
							</div>
							<div role="tabpanel" class="tab-pane" id="panel-justificacion">
								<br>
								<div class="row">
									<div class="col-sm-12">
										<table id="tabla-avances-metas" class="table table-condensed table-bordered">
					                		<thead>
					                			<tr>
						                			<th colspan="2" class="bg-success text-center">Meta Programada</th>
						                			<th colspan="3" class="bg-info text-center">Avance</th>
						                			<th rowspan="2" width="90" class="text-center">Porcentaje Acumulado</th>
					                			</tr>
					                			<tr>
					                				<th class="bg-success text-center">Acumulada</th>
					                				<th class="bg-success text-center" nowrap="nowrap">Mes actual</th>
					                				<th class="bg-info text-center" nowrap="nowrap">Mes actual</th>
					                				<th class="bg-info text-center">Acumulado</th>
					                				<th class="bg-info text-center">Total</th>
					                			</tr>
					                		</thead>
											<tfoot>
					                			<th class="bg-success" id="total-meta-programada-analisis">0</th>
					                			<th id="total-meta-mes-analisis">0</th>
					                			<th id="total-avance-mes-analisis">0</th>
					                			<th id="total-avance-acumulado-analisis">0</th>
					                			<th class="bg-info" id="total-avance-total-analisis">0</th>
					                			<th id="total-porcentaje-analisis">0%</th>
					                		</tfoot>
					                	</table>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<label class="control-label" for="analisis-resultados">Análisis de Resultados Acumulados</label>
											<textarea rows="6" class="form-control" name="analisis-resultados" id="analisis-resultados"></textarea>
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<label class="control-label" for="justificacion-acumulada">Justificación Acumulada</label>
											<textarea rows="6" class="form-control" name="justificacion-acumulada" id="justificacion-acumulada" disabled></textarea>
										</div>
									</div>
								</div>
							</div>
							@if($trimestre_activo)
							<div role="tabpanel" class="tab-pane" id="panel-plan-mejora">
								<br>
								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label class="control-label" for="accion-mejora">Acción de Mejora</label>
											<textarea rows="4" class="form-control" name="accion-mejora" id="accion-mejora"></textarea>
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<label class="control-label" for="grupo-trabajo">Grupo de Trabajo</label>
											<textarea rows="4" class="form-control" name="grupo-trabajo" id="grupo-trabajo"></textarea>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label class="control-label" for="fecha-inicio">Fecha de Inicio</label>
											<input type="date" placeholder="aaaa-mm-dd" class="form-control" name="fecha-inicio" id="fecha-inicio">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label class="control-label" for="fecha-termino">Fecha de Término</label>
											<input type="date" placeholder="aaaa-mm-dd" class="form-control" name="fecha-termino" id="fecha-termino">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label class="control-label" for="fecha-notificacion">Fecha de Notificación</label>
											<input type="date" placeholder="aaaa-mm-dd" class="form-control" name="fecha-notificacion" id="fecha-notificacion">
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<label class="control-label" for="documentacion-comprobatoria">Documentación Comprobatoria</label>
											<textarea rows="4" class="form-control" name="documentacion-comprobatoria" id="documentacion-comprobatoria"></textarea>
										</div>
									</div>
								</div>
							</div>
							@endif
						</div>
					</div>
					<input type="hidden" name="id-avance" id="id-avance">
					<input type="hidden" name="nivel" id="nivel">
					<input type="hidden" name="id-accion" id="id-accion">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btn-guardar-avance">Guardar Avance</button>
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