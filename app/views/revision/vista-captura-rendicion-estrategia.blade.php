@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/revision/vista-rendicion-estrategia.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="panel panel-default">
<div class="panel-heading">
	<h3 class="panel-title">Seguimiento de Metas del Trimestre {{$trimestre_activo}}</h3>
</div>
<div class="panel-body" id="panel-rendicion-cuentas">
    <div class="row">
        <div class="col-sm-8">
            <div class="form-group">
                <label class="control-label">Descripción</label>
                <p id="lbl-descripcion" class="form-control-static"></p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label class="control-label">Unidad Responsable</label>
                <p id="lbl-unidad-responsable" class="form-control-static"></p>
            </div>
        </div>
    </div>
	<br>
    <div class="panel panel-default datagrid" id="datagridIndicadores" data-edit-row="seguimiento_metas">
    	<div class="panel-body">
    		<div class="row">
                <div class="col-lg-6 col-md-6"></div>
                <div class="col-lg-6">
                    <div class="btn-toolbar pull-right" >
                        <div class="btn-group" style="margin:5px">
							<button type="button" id="btnAcciones" class="btn btn-primary"><i class="fa fa-gears"></i> Acciones</button>
				    	    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
				        	<ul class="dropdown-menu pull-right" role="menu">
        						<li id="btnAprobarProyecto" name="btnAprobarProyecto"><a href="#" class="btn-default"><i class="fa fa-thumbs-o-up"></i> Aprobar el avance del trimestre</a></li>
					            <li id="btnRegresarCorregir" name="btnRegresarCorregir"><a href="#" class="btn-default"><i class="fa fa-mail-reply-all"></i> Regresar para correción</a></li>
                           	</ul>
                        </div>
                    </div>
                </div>
            </div>
    	</div>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <!--<th><input type="checkbox" class="check-select-all-rows"></th>-->
                    <th>&nbsp;</th>
                    <th>Nivel</th>
                    <th>Indicador</th>
                    <th>Meta Programada</th>
                    <th>Avance Acumulado</th>
					<th>del Trimestre</th>
					<th width="50"></th>
                </tr>
            </thead>
            <tbody>
                <tr><td></td><td colspan="6" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="panel-footer">
    <div class="row">
        <div class="col-sm-12">
            <button type="button" class="btn btn-default" id="btn-estrategia-cancelar" >
                <span class="fa fa-chevron-left"></span> Regresar a la lista de Estrategias Institucionales
            </button>
            <!--<button type="button" class="btn btn-success" id="btn-enviar-programa">
                <span class="fa fa-send-o"></span> Enviar Avance a Revisión
            </button>-->
        </div>
    </div>
</div>
</div>
<input type="hidden" id="id" name="id" value="{{$id}}">
<input type="hidden" id="trimestre" name="trimestre" value="{{$trimestre_activo}}">
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
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="control-label">Indicador</label>
                                    <p class="form-control-static" id="indicador"></p>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="control-label">Unidad de Medida</label>
                                    <p class="form-control-static" id="unidad-medida"></p>
                                </div>
                            </div>
                            <div class="col-sm-3">
                            	<div class="form-group" style="margin-bottom:0;">
                            		<label class="control-label">Meta Total Programada</label>
                            		<p class="form-control-static" id="meta-total"></p>
								</div>
                            </div>
                        </div>
                    </div>
                </div>
                <form action="" id="form_avance">
					<table id="tabla-avances-metas" class="table table-condensed table-bordered">
                		<thead>
                			<tr>
                				<th rowspan="2">Trimestre</th>
	                			<th colspan="2" class="bg-success">Meta Programada</th>
	                			<th colspan="3" class="bg-info">Avance</th>
	                			<th rowspan="2" width="90">Porcentaje Trimestral</th>
                			</tr>
                			<tr>
                				<th class="bg-success">Acumulada</th>
                				<th class="bg-success" nowrap="nowrap">Trimestre</th>
                				<th class="bg-info" nowrap="nowrap">Trimestre</th>
                				<th class="bg-info">Acumulado</th>
                				<th class="bg-info">Total</th>
                			</tr>
                		</thead>
                		<tbody>
                			<tr>
                				<td>Trimestre {{$trimestre_activo}}</td>
                				<td class="bg-success" id="trimestre-acumulada" data-valor="0">0</td>
                				<td id="trimestre-meta">0</td>
                				<td width="20%">
                                    <div class="form-group">
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('avance-trimestre','Avance en el trimestre','lbl-avance-trimestre');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-avance-trimestre" name="lbl-avance-trimestre" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>
                                    </div>
                                
                					<!--<div class="form-group">
                						<input type="number" class="form-control" id="avance-trimestre" name="avance-trimestre">
                					</div>-->
                				</td>
                				<td id="trimestre-avance" data-valor="0">0</td>
                				<td class="bg-info" id="trimestre-total" data-valor="0">0</td>
                				<td id="trimestre-porcentaje">0%</td>
                			</tr>
                		</tbody>
                	</table>
                	<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label class="control-label" for="lbl-analisis-resultados">Analisis de Resultados Acumulado</label>
                               		<div class="form-group">
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('analisis-resultados','Análisis de Resultados Acumulado','lbl-analisis-resultados');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-analisis-resultados" name="lbl-analisis-resultados" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>
                                    </div>
								<!--<textarea rows="4" class="form-control" name="analisis-resultados" id="analisis-resultados"></textarea>-->
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label class="control-label" for="lbl-justificacion-acumulada">Justificación Acumulada</label>
                                	<div class="form-group">
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('justificacion-acumulada','Justificación Acumulada','lbl-justificacion-acumulada');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-justificacion-acumulada" name="lbl-justificacion-acumulada" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>
                                    </div>
								<!--<textarea rows="4" class="form-control" name="justificacion-acumulada" id="justificacion-acumulada" disabled></textarea>-->
							</div>
						</div>
					</div>
					<input type="hidden" name="id-indicador" id="id-indicador">
                </form>
                <input type="hidden" name="id-avance" id="id-avance">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
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
                	<input type="hidden" name="idregistroavancecomentario" id="idregistroavancecomentario">
                	<input type="hidden" name="idregistroavance" id="idregistroavance">
                    <input type="hidden" name="idcampo" id="idcampo">
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



<!-- Dejar parent al ultimo -->
@parent
@stop