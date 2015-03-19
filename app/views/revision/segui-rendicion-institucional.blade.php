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
                <span class="fa fa-check-square"></span> Analisis Funcional
            </a>
        </li>
        @endif
    </ul>
    <div class="tab-content">
    	@if($trimestre_activo)
    	<div role="tabpanel" class="tab-pane" id="analisis-funcional">
    		<br>
    		<form id="form_analisis">
	    		<div class="row">
	    			<div class="col-sm-6">
	    				<div class="form-group">
	    					<label class="control-label" for="lbl-analisis-resultado">Analisis de Resultado </label>
	    					<p id="lbl-analisis-resultado" name="lbl-analisis-resultado" rows="6" class="form-control"></p>
                            <p align="right"><button type="button" class="btn btn-default" onclick="escribirComentario('analisis-resultado','Análisis de resultado','lbl-analisis-resultado','4','id-analisis');"><span class="fa fa-edit"></span> Comentar Analisis de Resultado</button></p>
	    				</div>
	    			</div>
	    			<div class="col-sm-6">
	    				<div class="form-group">
	    					<label class="control-label" for="lbl-analisis-beneficiarios">Beneficiarios</label>
	    					<p id="lbl-analisis-beneficiarios" name="lbl-analisis-beneficiarios" rows="6" class="form-control"></p>
                            <p align="right"><button type="button" class="btn btn-default" onclick="escribirComentario('analisis-beneficiarios','Beneficiarios','lbl-analisis-beneficiarios','4','id-analisis');"><span class="fa fa-edit"></span> Comentar Beneficiarios</button></p>
	    				</div>
	    			</div>
	    			<div class="col-sm-12">
	    				<div class="form-group">
	    					<label class="control-label" for="lbl-justificacion-global">Justificación Global del Proyecto</label>
	    					<p id="lbl-justificacion-global" name="lbl-justificacion-global" rows="6" class="form-control"></p>
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
            <button type="button" class="btn btn-default" id="btn-proyecto-cancelar">
                <span class="fa fa-chevron-left"></span> Regresar a la lista de Proyectos
            </button>
            <!--<button type="button" class="btn btn-success" id="btn-enviar-proyecto">
                <span class="fa fa-send-o"></span> Enviar Proyecto a Revisión
            </button>-->
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
									<span class="fa fa-align-left"></span> Analisis y Justificación
								</a>
							</li>
							@if($trimestre_activo)
							<li role="presentation" class="disabled">
								<a href="#panel-plan-mejora" aria-controls="panel-plan-mejora" role="tab" data-toggle="" id="tab-link-plan-mejora">
									<span class="fa fa-file"></span> Plan de Mejora
								</a>
							</li>
							@endif
						</ul>
						<!-- Tab panes -->
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="panel-metas">
								<br>
								<table id="tabla-avances-metas" class="table table-condensed table-hover table-bordered">
			                		<thead>
			                			<th>Jurisdicción</th>
			                			<th>Meta Acumulada</th>
			                			<th>Meta Programada</th>
			                			<th>Avance del Mes</th>
			                			<th>Avance Acumulado</th>
			                			<th width="90">Porcentaje Acumulado</th>
			                		</thead>
			                		<tbody>
			                			<tr data-clave-jurisdiccion="OC">
			                				<td>OC - Oficina Central</td>
			                				<td class="meta-programada" data-meta="0">0</td>
			                				<td class="meta-del-mes" data-meta-mes="0">0</td>
			                				<td>
			                					<div class="form-group" style="margin-bottom:0;">
			                						<input type="number" class="form-control avance-mes" name="avance[OC]" id="avance_OC" data-jurisdiccion="OC" data-meta-programada="">
			                					</div>
			                				</td>
			                				<td class="avance-acumulado" data-acumulado="0">
			                					<span class="vieja-cantidad">0</span> <span class="nueva-cantidad text-primary"></span>
			                				</td>
			                				<td class="avance-mes" data-estado-avance=""></td>
			                			</tr>
			                			@foreach ($jurisdicciones as $jurisdiccion)
			                			<tr data-clave-jurisdiccion="{{$jurisdiccion->clave}}">
			                				<td>{{$jurisdiccion->clave}} - {{$jurisdiccion->nombre}}</td>
			                				<td class="meta-programada" data-meta="0">0</td>
			                				<td class="meta-del-mes" data-meta-mes="0">0</td>
			                				<td>
			                					<div class="form-group" style="margin-bottom:0;">
			                						<input type="number" class="form-control avance-mes" name="avance[{{$jurisdiccion->clave}}]" id="avance_{{$jurisdiccion->clave}}" data-jurisdiccion="{{$jurisdiccion->clave}}" data-meta-programada="">
			                					</div>
			                				</td>
			                				<td class="avance-acumulado" data-acumulado="0">
			                					<span class="vieja-cantidad">0</span> <span class="nueva-cantidad text-primary"></span>
			                				</td>
			                				<td class="avance-mes" data-estado-avance=""></td>
			            				</tr>
			                			@endforeach
			                		</tbody>
			                		<tfoot>
			                			<th>Totales</th>
			                			<th id="total-meta-programada">0</th>
			                			<th id="total-meta-mes">0</th>
			                			<th id="total-avance-mes">0</th>
			                			<th id="total-avance-acumulado">0</th>
			                			<th id="total-porcentaje">0%</th>
			                		</tfoot>
			                	</table>
							</div>
							<div role="tabpanel" class="tab-pane" id="panel-justificacion">
								<br>
								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label class="input-label" for="analisis-resultados">Analisis de Resultados Acumulado</label>
											<textarea rows="6" class="form-control" name="analisis-resultados" id="analisis-resultados"></textarea>
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<label class="input-label" for="justificacion-acumulada">Justificación Acumulada</label>
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
											<label class="input-label" for="accion-mejora">Acción de Mejora</label>
											<textarea rows="4" class="form-control" name="accion-mejora" id="accion-mejora"></textarea>
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<label class="input-label" for="grupo-trabajo">Grupo de Trabajo</label>
											<textarea rows="4" class="form-control" name="grupo-trabajo" id="grupo-trabajo"></textarea>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label class="input-label" for="fecha-inicio">Fecha de Inicio</label>
											<input type="date" class="form-control" name="fecha-inicio" id="fecha-inicio">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label class="input-label" for="fecha-termino">Fecha de Termino</label>
											<input type="date" class="form-control" name="fecha-termino" id="fecha-termino">
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<label class="input-label" for="fecha-notificacion">Fecha de Notificación</label>
											<input type="date" class="form-control" name="fecha-notificacion" id="fecha-notificacion">
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<label class="input-label" for="documentacion-comprobatoria">Documentación Comprobatoria</label>
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
                </form>
             </div>
             <div class="modal-footer">
             	<button type="button" class="btn btn-warning" id="btnGuardarComentario">Guardar comentario</button>
                <button type="button" class="btn btn-danger" id="btnQuitarComentario">Quitar comentario</button>
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
							<th >Indigena</th>
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