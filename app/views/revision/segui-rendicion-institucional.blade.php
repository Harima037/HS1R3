@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/revision/seguimiento-rendicion-cuentas.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="panel panel-default">
<div class="panel-heading">
	<h3 class="panel-title">Rendición de cuentas del mes de {{$mes}}</h3>
</div>
<div class="panel-body" id="panel-rendicion-cuentas">
	@if($evaluacion_capturada)
	<div class="btn-toolbar pull-right">
		<div class="btn-group" style="margin:5px">
	    	<button type="button" id="btnAcciones" class="btn btn-primary"><i class="fa fa-gears"></i> Acciones</button>
    	    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
        	<ul class="dropdown-menu pull-right" role="menu">
        		<li id="btnAprobarProyecto" name="btnAprobarProyecto"><a href="#" class="btn-default"><i class="fa fa-thumbs-o-up"></i> Aprobar el avance del mes</a></li>
	            <li id="btnRegresarCorregir" name="btnRegresarCorregir"><a href="#" class="btn-default"><i class="fa fa-mail-reply-all"></i> Regresar para corrección</a></li>
    	    </ul>
		</div>
	</div>
	@endif
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
        <li role="presentation">
        	<a href="#datos-informacion" aria-controls="datos-informacion" role="tab" data-toggle="tab" id="tab-link-analisis-funcional">
                <span class="fa fa-info-circle"></span> Información
            </a>
        </li>
    </ul>
    <div class="tab-content">
    	@if($trimestre_activo)
    	<div role="tabpanel" class="tab-pane" id="analisis-funcional">
    		<br>
    		<form id="form_analisis">
	    		<div class="row">
                
                	<div class="col-sm-12">
	    				<div class="form-group">
	    					<label class="control-label" for="lbl-analisis-resultado">Finalidad del proyecto </label>
	    					<p id="lbl-finalidad" name="lbl-finalidad" style="height:auto" class="form-control"></p>
                            <p align="right"><button type="button" class="btn btn-default" onclick="escribirComentario('finalidad','Finalidad del Proyecto','lbl-finalidad','4','id-analisis');"><span class="fa fa-edit"></span> Comentar Finalidad del proyecto</button></p>
	    				</div>
	    			</div>
                
                
	    			<div class="col-sm-12">
	    				<div class="form-group">
	    					<label class="control-label" for="lbl-analisis-resultado">Análisis de Resultado </label>
	    					<p id="lbl-analisis-resultado" name="lbl-analisis-resultado" style="height:auto" class="form-control"></p>
                            <p align="right"><button type="button" class="btn btn-default" onclick="escribirComentario('analisis-resultado','Análisis de resultado','lbl-analisis-resultado','4','id-analisis');"><span class="fa fa-edit"></span> Comentar Análisis de Resultado</button></p>
	    				</div>
	    			</div>
	    			<div class="col-sm-12">
	    				<div class="form-group">
	    					<label class="control-label" for="lbl-analisis-beneficiarios">Beneficiarios</label>
	    					<p id="lbl-analisis-beneficiarios" name="lbl-analisis-beneficiarios" style="height:auto" class="form-control"></p>
                            <p align="right"><button type="button" class="btn btn-default" onclick="escribirComentario('analisis-beneficiarios','Beneficiarios','lbl-analisis-beneficiarios','4','id-analisis');"><span class="fa fa-edit"></span> Comentar Beneficiarios</button></p>
	    				</div>
	    			</div>
	    			<div class="col-sm-12">
	    				<div class="form-group">
	    					<label class="control-label" for="lbl-justificacion-global">Justificación Global del Proyecto</label>
	    					<p id="lbl-justificacion-global" name="lbl-justificacion-global" style="height:auto" class="form-control"></p>
                            <p align="right"><button type="button" class="btn btn-default" onclick="escribirComentario('justificacion-global','Justificación Global del Proyecto','lbl-justificacion-global','4','id-analisis');"><span class="fa fa-edit"></span> Comentar Justificación Global del Proyecto</button></p>
	    				</div>
	    			</div>
	    		</div>
    		</form>
    		<input type="hidden" name="id-analisis" id="id-analisis" value="{{$id_analisis}}">
    		<div class="row">
    			<div class="col-sm-6">
    				<!--<button type="button" class="btn btn-primary" id="btn-guadar-analisis-funcional">
    					<span class="fa fa-floppy-o"></span> Guardar Analisis Funcional
    				</button>-->
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
		                                <span class="fa fa-eye"></span> Ver Información de los Beneficiarios
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
                            <th rowspan="2">Comentarios</th>
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
		                                <span class="fa fa-edit"></span> Ver detalles para comentar
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
	                        <th width="70px">Nivel</th>
	                        <th>Indicador</th>
	                        <th width="143px">Meta Programada</th>
	                        <th width="153px">Avance Acumulado</th>
							<th width="143px">Avances del Mes</th>
							<th width="65px"></th>
	                    </tr>
	                </thead>
	                <tbody>
	                    <tr><td></td><td colspan="6" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>
	                </tbody>
	            </table>
	        </div>
        </div>
        
        
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
									<div class="input-group">
                                       	<span class="input-group-btn" onclick="escribirComentario('fuente-informacion','Fuente de Información','lbl-fuente-informacion','4','id-analisis');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span>
                                        <p id="lbl-fuente-informacion" class="form-control" style="height:auto">&nbsp;</p>
                                     </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
	                            <div class="form-group">
									<div class="form-group">
                                    	<label class="control-label" for="responsable">Responsable</label>
                                        <div class="input-group">
                                        	<span class="input-group-btn" onclick="escribirComentario('responsable','Responsable','lbl-responsable','4','id-analisis');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-responsable" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>
                                        <span id="ayuda-responsable" class="help-block"></span>
                                    </div>
                                </div>
                            </div>
						</div>
					</form>
				</div>
                <div class="panel-footer">
                </div>
			</div>
        </div>
    </div>
    
    
    
</div>

<div class="panel-footer">
    <div class="row">
        <div class="col-sm-12">
            <button type="button" class="btn btn-default" id="btn-proyecto-cancelar">
                <span class="fa fa-chevron-left"></span> Regresar a la lista de Proyectos
            </button>
        </div>
    </div>
</div>
</div>
<input type="hidden" id="id" name="id" value="{{$id}}">
<input type="hidden" id="id_clasificacion" name="id_clasificacion" value="{{$id_clasificacion}}">
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
									<span class="fa fa-file"></span> Plan de Mejora
								</a>
							</li>
							@endif
							<li role="presentation" class="pull-right">
								<a href="#panel-observaciones" aria-controls="panel-observaciones" role="tab" data-toggle="tab" id="tab-link-observaciones">
									<span class="fa fa-comment"></span> Observaciones <span id="conteo-observaciones" class="badge">0</span>
								</a>
							</li>
						</ul>
						<!-- Tab panes -->
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="panel-metas">
								<br>
                                <button type="button" class="btn btn-default" id="avancesmetas" name="avancesmetas" onclick="escribirComentario('avancesmetas','Avances de las metas','indicador','nivel','nivel');"><span class="fa fa-edit"></span> Comentar Avances de las Metas</button>
                                <p></p>
								<table id="tabla-avances-metas" class="table table-condensed table-bordered">
			                		<thead>
			                			<tr>
			                				<th rowspan="2">Jurisdicción</th>
				                			<th colspan="2" class="bg-success">Meta Programada</th>
				                			<th colspan="3" class="bg-info">Avance</th>
				                			<th rowspan="2" width="90">Porcentaje Acumulado</th>
			                			</tr>
			                			<tr>
			                				<th class="bg-success">Acumulada</th>
			                				<th class="bg-success" nowrap="nowrap">Mes actual</th>
			                				<th class="bg-info" nowrap="nowrap">Mes actual</th>
			                				<th class="bg-info">Acumulado</th>
			                				<th class="bg-info">Total</th>
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
			                						<input type="text" class="form-control avance-mes" name="avance[{{$clave}}]" id="avance_{{$clave}}" data-jurisdiccion="{{$clave}}" data-meta-programada="" disabled="disabled">
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
					                			<th width="1"><button type="button" class="btn btn-link btn-ocultar-avance-localidades">Ocultar</button></th>
					                		</tr>
				                		</thead>
				                	</table>
				                	<div style="max-height:300px; overflow-x:auto;">
				                		<table class="table table-condensed table-bordered tabla-avance-localidades">
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
											<label class="input-label" for="lbl-analisis-resultados">Análisis de Resultados Acumulados</label>
											<p class="form-control" name="lbl-analisis-resultados" id="lbl-analisis-resultados" style="height:auto;"></p>
                                            <button type="button" class="btn btn-default" onclick="escribirComentario('analisis-resultados','Análisis de Resultados Acumulado','lbl-analisis-resultados','nivel','nivel');"><span class="fa fa-edit"></span> Comentar Análisis de Resultados Acumulado</button>
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<label class="input-label" for="lbl-justificacion-acumulada">Justificación Acumulada</label>
											<p class="form-control" name="lbl-justificacion-acumulada" id="lbl-justificacion-acumulada" style="height:auto;"></p>
                                            <button type="button" class="btn btn-default" onclick="escribirComentario('justificacion-acumulada','Justificación Acumulada','lbl-justificacion-acumulada','nivel','nivel');"><span class="fa fa-edit"></span> Comentar Justificación Acumulada</button>
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
											<label class="input-label" for="lbl-accion-mejora">Acción de Mejora</label>
                                            <p id="lbl-accion-mejora" name="lbl-accion-mejora" class="form-control" style="height:auto"></p>
                                            <button type="button" class="btn btn-default" onclick="escribirComentario('accion-mejora','Acción de mejora','lbl-accion-mejora','nivel','nivel');"><span class="fa fa-edit"></span> Comentar Acción de Mejora</button>
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<label class="input-label" for="lbl-grupo-trabajo">Grupo de Trabajo</label>
											<p class="form-control" name="lbl-grupo-trabajo" id="lbl-grupo-trabajo" style="height:auto"></p>
                                            <button type="button" class="btn btn-default" onclick="escribirComentario('grupo-trabajo','Grupo de Trabajo','lbl-grupo-trabajo','nivel','nivel');"><span class="fa fa-edit"></span> Comentar Grupo de Trabajo</button>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label class="input-label" for="lbl-fecha-inicio">Fecha de Inicio</label>
											<p class="form-control" name="lbl-fecha-inicio" id="lbl-fecha-inicio" style="height:auto"></p>
                                            <button type="button" class="btn btn-default" onclick="escribirComentario('fecha-inicio','Fecha de Inicio','lbl-fecha-inicio','nivel','nivel');"><span class="fa fa-edit"></span> Comentar Fecha de Inicio</button>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label class="input-label" for="lbl-fecha-termino">Fecha de Término</label>
											<p class="form-control" name="lbl-fecha-termino" id="lbl-fecha-termino" style="height:auto"></p>
                                            <button type="button" class="btn btn-default" onclick="escribirComentario('fecha-termino','Fecha de Término','lbl-fecha-termino','nivel','nivel');"><span class="fa fa-edit"></span> Comentar Fecha de Término</button>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label class="input-label" for="lbl-fecha-notificacion">Fecha de Notificación</label>
											<p class="form-control" name="lbl-fecha-notificacion" id="lbl-fecha-notificacion" style="height:auto"></p>
                                            <button type="button" class="btn btn-default" onclick="escribirComentario('fecha-notificacion','Fecha de Notificación','lbl-fecha-notificacion','nivel','nivel');"><span class="fa fa-edit"></span> Comentar Fecha de Notificación</button>
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<label class="input-label" for="lbl-documentacion-comprobatoria">Documentación Comprobatoria</label>
											<p class="form-control" name="lbl-documentacion-comprobatoria" id="lbl-documentacion-comprobatoria" style="height:auto"></p>
                                            <button type="button" class="btn btn-default" onclick="escribirComentario('documentacion-comprobatoria','Documentación Comprobatoria','lbl-documentacion-comprobatoria','nivel','nivel');"><span class="fa fa-edit"></span> Comentar Documentación Comprobatoria</button>
										</div>
									</div>
								</div>
							</div>
							@endif
							<div role="tabpanel" class="tab-pane" id="panel-observaciones">
								<br>
								<div class="row">
									<div class="col-sm-12">
										<button type="button" class="btn btn-primary pull-right" id="btn-agregar-observacion">
											<span class="fa fa-plus-circle"></span> Agregar Observación
										</button>
									</div>
									<div class="col-sm-12">
										<table id="tabla-lista-observaciones" class="table table-condensed table-striped table-hover">
											<thead>
												<tr>
													<th>Observación</th>
													<th width="170px">Fecha</th>
													<th width="100px">Opciones</th>
												</tr>
											</thead>
											<tbody>
											</tbody>
											<tfoot>
												<tr id="formulario-observacion" class="hidden">
													<td colspan="2">
														<textarea id="observacion" class="form-control" rows="4"></textarea>
														<input type="hidden" id="id-observacion">
													</td>
													<td>
														<br>
														<button type="button" class="btn btn-success btn-block" id="btn-guardar-observacion">
															<span class="fa fa-save"></span> Guardar
														</button>
														<button type="button" class="btn btn-default btn-block" id="btn-cancelar-observacion">
															Cancelar
														</button>
													</td>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					<input type="hidden" name="id-avance" id="id-avance">
					<input type="hidden" name="nivel" id="nivel">
					<input type="hidden" name="id-accion" id="id-accion">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <!--<button type="button" class="btn btn-success" id="btn-guardar-avance">Guardar Avance</button>-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="modalComentario" tabindex="-1" role="dialog" aria-labelledby="modalComentarioLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
    	<div class="modal-content">
        	<div class="modal-header">
            	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalComentarioLabel">Escribir comentario </h4>
             </div>
             <div class="modal-body">
             	<form action="" id="formComentario">
                	<input type="hidden" name="idproyectocomentarios" id="idproyectocomentarios">
                    <input type="hidden" name="idcampo" id="idcampo">
                    <input type="hidden" name="tipocomentario" id="tipocomentario">
                    <input type="hidden" name="idelemento" id="idelemento">
                    @if($evaluacion_capturada)
                    <div class="row">
                    	<div class="col-sm-12">
                        	<div class="form-group">
                            	<label for="informacioncampo" class="control-label" id="lbl-nombredelcampo"></label>
                                <p id="lbl-informacioncampo" class="form-control" style="height:auto"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    	<div class="col-sm-12">
                        	<div class="form-group">
                            	<label for="comentario" class="control-label">Comentario</label>
                                <textarea class="form-control" name="comentario" id="comentario" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="row">
                    	<div class="col-sm-12">
                    		<p class="help-text">La captura de comentarios se encuentra desactivada en este momento.</p>
                    	</div>
                    </div>
                    @endif
                </form>
             </div>
             <div class="modal-footer">
             	@if($evaluacion_capturada)
             	<button type="button" class="btn btn-warning" id="btnGuardarComentario">Guardar comentario</button>
                <button type="button" class="btn btn-danger" id="btnQuitarComentario">Quitar comentario</button>
                @endif
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
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
    				<th width="240px"></th>
					<th width="143px">Total Femenino</th>
					<th width="143px">Total Masculino</th>
					<th width="143px">Total</th>
    			</tr>
    		</thead>
    		<tbody>
    			<tr class="bg-info">
    				<td rowspan="3" id="tipo-beneficiario" data-valor="0"></td>
    				<th class="text-right">Programado</th>
    				<td class="cant-benficiarios" id="total-f" data-valor="0">0</td>
    				<td class="cant-benficiarios" id="total-m" data-valor="0">0</td>
    				<td class="cant-benficiarios" id="total-beneficiario" data-valor="0">0</td>
				</tr>
				<tr>
					<th class="text-right">Acumulado al Trimestre Anterior</th>
    				<td class="cant-benficiarios" id="acumulado-f" data-valor="0">0</td>
    				<td class="cant-benficiarios" id="acumulado-m" data-valor="0">0</td>
    				<td class="cant-benficiarios" id="acumulado-beneficiario" data-valor="0">0</td>
				</tr>
				<tr class="bg-info">
					<th class="text-right">Total Acumulado</th>
    				<td class="cant-benficiarios" id="total-acumulado-f" data-valor="0">0</td>
    				<td class="cant-benficiarios" id="total-acumulado-m" data-valor="0">0</td>
    				<td class="cant-benficiarios" id="total-acumulado-beneficiario" data-valor="0">0</td>
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
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <!--<button type="button" class="btn btn-primary btn-guardar" id="btn-beneficiario-guardar">Guardar</button>-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endif
<!-- Dejar parent al ultimo -->
@parent
@stop