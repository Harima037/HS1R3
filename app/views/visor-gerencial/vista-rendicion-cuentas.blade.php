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
<script src="{{ URL::to('js/modulos/visor-gerencial/vista-rendicion-cuentas.js')}}"></script>
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
		                				<th rowspan="2" class="text-center">Mes</th>
			                			<th colspan="2" class="bg-success text-center">Meta Programada</th>
			                			<th colspan="3" class="bg-info text-center">Avance</th>
			                			<th rowspan="2" class="text-center" width="90">Porcentaje Acumulado</th>
		                			</tr>
		                			<tr>
		                				<th width="135" class="bg-success text-center" nowrap="nowrap">Mes</th>
		                				<th width="135" class="bg-success text-center">Acumulada</th>
		                				<th width="135" class="bg-info text-center">Acumulado</th>
		                				<th width="135" class="bg-info text-center" nowrap="nowrap">Mes</th>
		                				<th width="135" class="bg-info text-center">Total</th>
		                			</tr>
		                		</thead>
		                		<tbody>
		                		@foreach($meses as $clave => $mes)
		                			<tr {{($mes_clave == $clave)?'style="font-weight:bold;"':''}}>
		                				<td {{($mes_clave == $clave)?'style="border-bottom:3px solid black;"':''}}>{{$mes}}</td>
		                				<td {{($mes_clave == $clave)?'style="border-bottom:3px solid black;"':''}} class="valores" id="meta-mes-{{$clave}}"></td>
		                				<td {{($mes_clave == $clave)?'style="border-bottom:3px solid black;"':''}} class="valores bg-success" id="meta-acumulada-{{$clave}}" class="bg-success"></td>
		                				<td {{($mes_clave == $clave)?'style="border-bottom:3px solid black;"':''}} class="valores" id="avance-acumulado-{{$clave}}"></td>
		                				<td {{($mes_clave == $clave)?'style="border-bottom:3px solid black;"':''}} class="valores" id="avance-mes-{{$clave}}"></td>
		                				<td {{($mes_clave == $clave)?'style="border-bottom:3px solid black;"':''}} class="valores bg-info" id="avance-total-{{$clave}}" class="bg-info"></td>
		                				<td {{($mes_clave == $clave)?'style="border-bottom:3px solid black;"':''}} class="valores" id="porcentaje-acumulado-{{$clave}}"></td>
		                			</tr>
		                		@endforeach
		                		</tbody>
		                	</table>
		                	<div class="form-group hidden" id="mensaje-alerta">
		                		<label class="control-label">Observaciones:</label>
		                		<p class="form-control-static" style="font-size:bigger;">
		                			<big>
		                				De acuerdo a los resultados acumulados del indicador es necesario implementar un Plan de Acción de Mejora
		                			</big>
		                		</p>
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
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Dejar parent al ultimo -->
@parent
@stop