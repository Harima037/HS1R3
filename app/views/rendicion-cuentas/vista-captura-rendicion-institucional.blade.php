@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/rendicion-cuentas/vista-rendicion-cuentas.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')

<div class="panel-body" id="panel-rendicion-cuentas">
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#seguimiento-metas" aria-controls="seguimiento-metas" role="tab" data-toggle="tab">
                <span class="fa fa-calendar"></span> Seguimiento de Metas
            </a>
        </li>
        <li role="presentation">
            <a href="#seguimiento-beneficiarios" aria-controls="seguimiento-beneficiarios" role="tab" data-toggle="tab" id="tab-link-beneficiarios">
                <span class="fa fa-users"></span> Seguimiento de Beneficiarios
            </a>
        </li>
    </ul>
    <div class="tab-content">
    	<div role="tabpanel" class="tab-pane" id="seguimiento-beneficiarios">
    		<div class="panel panel-default datagrid" id="datagridBeneficiarios" data-edit-row="seguimiento_beneficiarios">
	            <div class="panel-body">
	                <div class="row">
	                    <div class="col-lg-6 col-md-6"></div>
	                    <div class="col-lg-6">
	                        <div class="btn-toolbar pull-right" >
	                            @section('panel-botones')
	                                <div class="btn-group" style="margin:5px">
	                                    <button type="button" class="btn btn-primary btn-edit-rows">
	                                        <span class="glyphicon glyphicon-plus"></span> Actualizar Seguimiento
	                                    </button>
	                                </div>
	                            @show
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <table class="table table-striped table-hover">
	                <thead>
	                    <tr>
	                        <th><input type="checkbox" class="check-select-all-rows"></th>
	                        <th>Tipo de Beneficiario</th>
	                        <th>Total F</th>
							<th>Avance F</th>
	                        <th>Total M</th>
	                        <th>Avance M</th>
	                        <th>Total</th>
	                        <th>Avance</th>
	                    </tr>
	                </thead>
	                <tbody>
	                    <tr><td></td><td colspan="6" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>
	                </tbody>
	            </table>
	        </div>
    	</div>
        <div role="tabpanel" class="tab-pane active" id="seguimiento-metas">
            <div class="panel panel-default datagrid" id="datagridAcciones" data-edit-row="seguimiento_metas">
	            <div class="panel-body">
	                <div class="row">
	                    <div class="col-lg-6 col-md-6"></div>
	                    <div class="col-lg-6">
	                        <div class="btn-toolbar pull-right" >
	                            @section('panel-botones')
	                                <div class="btn-group" style="margin:5px">
	                                    <button type="button" class="btn btn-primary btn-edit-rows">
	                                        <span class="glyphicon glyphicon-plus"></span> Actualizar Seguimiento
	                                    </button>
	                                </div>
	                            @show
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
<input type="hidden" id="id" name="id" value="{{$id}}">
@stop

@section('modals')
<div class="modal fade" id="modalEditarAvance" tabindex="-1" role="dialog" aria-labelledby="modalEditarAvanceLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalEditarAvanceLabel">Nuevo</h4>
            </div>
            <div class="modal-body">
                <form action="" id="form_avance">
					<div role="tabpanel">
						<ul class="nav nav-tabs" role="tablist" id="tabs-seguimiento-metas">
							<li role="presentation" class="active">
								<a href="#panel-metas" aria-controls="panel-metas" role="tab" data-toggle="tab">
									<span class="fa fa-table"></span> Metas
								</a>
							</li>
							<li role="presentation" class="disabled">
								<a href="#panel-justificacion" aria-controls="panel-justificacion" role="tab" data-toggle="" id="tab-link-justificacion">
									<span class="fa fa-align-left"></span> Justificación
								</a>
							</li>
						</ul>
						<!-- Tab panes -->
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="panel-metas">
								<br>
								<table id="tabla-avances-metas" class="table table-condensed table-hover table-bordered">
			                		<thead>
			                			<th>Jurisdicción</th>
			                			<th>Meta Programada</th>
			                			<th>Avance del Mes</th>
			                			<th>Avance Acumulado</th>
			                			<th>Porcentaje Acumulado</th>
			                			<th width="1">Avance al Mes</th>
			                		</thead>
			                		<tbody>
			                			<tr data-clave-jurisdiccion="OC">
			                				<td>OC - Oficina Central</td>
			                				<td class="meta-programada" data-meta="0">0</td>
			                				<td>
			                					<div class="form-group">
			                						<input type="number" class="form-control avance-mes" name="avance[OC]" id="avance_OC" data-jurisdiccion="OC" disabled>
			                					</div>
			                				</td>
			                				<td class="avance-acumulado" data-acumulado="0">
			                					<span class="vieja-cantidad">0</span> <span class="nueva-cantidad text-primary"></span>
			                				</td>
			                				<td class="porcentaje-acumulado" data-porcentaje="0">
			                					<span class="vieja-cantidad">0</span> <span class="nueva-cantidad text-primary"></span>
			                				</td>
			                				<td class="avance-mes" data-estado-avance=""></td>
			                			</tr>
			                			@foreach ($jurisdicciones as $jurisdiccion)
			                			<tr data-clave-jurisdiccion="{{$jurisdiccion->clave}}">
			                				<td>{{$jurisdiccion->clave}} - {{$jurisdiccion->nombre}}</td>
			                				<td class="meta-programada" data-meta="0">0</td>
			                				<td>
			                					<div class="form-group">
			                						<input type="number" class="form-control avance-mes" name="avance[{{$jurisdiccion->clave}}]" id="avance_{{$jurisdiccion->clave}}" data-jurisdiccion="{{$jurisdiccion->clave}}" disabled>
			                					</div>
			                				</td>
			                				<td class="avance-acumulado" data-acumulado="0">
			                					<span class="vieja-cantidad">0</span> <span class="nueva-cantidad text-primary"></span>
			                				</td>
			                				<td class="porcentaje-acumulado" data-porcentaje="0">
			                					<span class="vieja-cantidad">0</span> <span class="nueva-cantidad text-primary"></span>
			                				</td>
			                				<td class="avance-mes" data-estado-avance=""></td>
			            				</tr>
			                			@endforeach
			                		</tbody>
			                		<tfoot>
			                			<th>Totales</th>
			                			<th id="total-meta-programada">0</th>
			                			<th id="total-avance-mes">0</th>
			                			<th id="total-avance-acumulado">0</th>
			                			<th id="total-porcentaje">0%</th>
			                			<th></th>
			                		</tfoot>
			                	</table>
							</div>
							<div role="tabpanel" class="tab-pane" id="panel-justificacion">
								<br>
								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<label class="input-label">Analisis de Resultados Acumulado</label>
											<textarea rows="6" class="form-control" name="analisis-resultados" id="analisis-resultados"></textarea>
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<label class="input-label">Justificación Acumulada</label>
											<textarea rows="6" class="form-control" name="justificacion-acumulada" id="justificacion-acumulada"></textarea>
										</div>
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
                <button type="button" class="btn btn-success" id="btn-guardar-avance">Guardar Avance</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="modalBeneficiario" tabindex="-1" role="dialog" aria-labelledby="modalBenefLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalBenefLabel">Nuevo</h4>
            </div>
            <div class="modal-body">

<form id="form_beneficiario">
	<input type="hidden" name="id-beneficiario" id="id-beneficiario">
	<div class="row">
		<div class="col-sm-12">
			<table class="table table-bordered table-condensed">
				<tr>
					<th colspan="4">Zona</th>
				</tr>
				<tr>
					<th colspan="2">Urbana</th>
					<th colspan="2">Rural</th>
				</tr>
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
				<tr>
					<th colspan="2">
						<span class="fa fa-link"></span> Totales
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
			</table>
		</div>
		<div class="col-sm-12">
			<table class="table table-bordered table-condensed">
				<tr>
					<th colspan="4">Población</th>
				</tr>
				<tr>
					<th >Mestiza</th>
					<th >Indigena</th>
					<th >Inmigrante</th>
					<th >Otros</th>
				</tr>
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
	            <tr>
	                <th colspan="2">
	                	<span class="fa fa-link"></span> Totales
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
	        </table>
		</div>
		<div class="col-sm-12">
			<table class="table table-bordered table-condensed">
	            <tr>
	                <th colspan="5">Marginación</th>
	            </tr>
	            <tr>
	                <th>Muy alta</th>
	                <th>Alta</th>
	                <th>Media</th>
	                <th>Baja</th>
	                <th>Muy baja </th>
	            </tr>
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
	            <tr>
	                <th colspan="3"><span class="fa fa-link"></span> Totales</th>
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
	        </table>
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
<!-- Dejar parent al ultimo -->
@parent
@stop