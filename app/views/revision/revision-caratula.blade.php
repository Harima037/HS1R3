@extends('layouts.Modulo')

@section('title-page') Caratula de Captura @stop


@section('css')
@parent
<link href="{{ URL::to('css/chosen.bootstrap.min.css') }}" rel="stylesheet" type="text/css" media="screen">
@stop

@section('js')
@parent
<script src="{{ URL::to('js/dependencias/chosen.jquery.min.js') }}"></script>
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/expediente/fuenteFinanciamiento.js')}}"></script>
<script src="{{ URL::to('js/modulos/revision/revision-caratula.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
    	<div class="panel panel-default" id="datagridCaratulas">
            <div class="panel-heading">
                <h4><i class="fa fa-file"></i> <b>{{$clasificacion_proyecto}}</b> <small>({{$tipo_proyecto}})</small></h4>
            </div>
            <div class="panel-body" id="mensaje-espera">
                <div class="alert alert-info">
                    <h2><span class="fa fa-cog fa-spin"></span> Cargando información del formulario... por favor espere...</h2>
                </div>
            </div>
            <div class="panel-body hidden" id="panel-principal-formulario">


    			        <div class="btn-toolbar pull-right">
	                            <div class="btn-group" style="margin:5px">
                                <button type="button" id="btnAcciones" class="btn btn-primary"><i class="fa fa-gears"></i> Acciones</button>
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li id="btnAprobarProyecto" name="btnAprobarProyecto"><a href="#" class="btn-default"><i class="fa fa-thumbs-o-up"></i> Aprobar el proyecto</a></li>
                                    <li id="btnRegresarCorregir" name="btnRegresarCorregir"><a href="#" class="btn-default"><i class="fa fa-mail-reply-all"></i> Regresar para correción</a></li>                                    
                                </ul>
                            </div>
                        </div>
                        
                        
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a id="tablink-caratula" href="#caratula-captura" role="tab" data-toggle="tab">
                            Carátula de Captura
                        </a>
                    </li>
                    <li role="presentation">
                        <a id="tablink-beneficiarios" href="#beneficiarios" role="tab" data-toggle="tab">
                            Beneficiarios 
                        </a>
                    </li>
                    <li role="presentation">
                        <a id="tablink-fuentes-financiamiento" href="#financiamiento" role="tab" data-toggle="tab">
                            Financiamiento
                        </a>
                    </li>
                    <li role="presentation">
                        <a id="tablink-fibap" href="#fibap" role="tab" data-toggle="tab">
                            FIBAP 
                        </a>
                    </li>
                    <li role="presentation">
                        <a id="tablink-antecedentes" href="#antecedentes" role="tab" data-toggle="tab">
                            Antecedentes 
                        </a>
                    </li>
                    <li role="presentation">
                        <a id="tablink-componentes" href="#componentes" role="tab" data-toggle="tab">
                            Componentes 
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="caratula-captura">
                        <br>
                        {{$formulario}}
                    </div>
                    <div role="tabpanel" class="tab-pane" id="beneficiarios">
                        <br>
                        {{$beneficiarios}}
                    </div>
                    <div role="tabpanel" class="tab-pane" id="financiamiento">
                        <br>
                        {{$grid_fuentes_financiamiento}}
                    </div>
                    <div role="tabpanel" class="tab-pane" id="fibap">
                        <br>
                        <div id="panelfibap"></div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="antecedentes">
                        <br>
                        <div id="panelantecedentes"></div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="componentes">
                        <br>
                        {{$grid_componentes}}
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
	</div>
</div>
@stop

@section('modals')


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
    
    
    <div class="modal fade" id="modal-presupuesto" tabindex="-1" role="dialog" aria-labelledby="modalPresupLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalPresupLabel">Detalle del Presupuesto</h4>
            </div>
            <div class="modal-body">
                <form id="form-presupuesto">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="jurisdiccion-accion" class="label-control">Jurisdicción</label>
                                <input type="text" id="jurisdiccion-accion" name="jurisdiccion-accion" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="municipio-accion" class="label-control">Municipio</label>
                                <input type="text" id="municipio-accion" name="municipio-accion" class="form-control" >
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="localidad-accion" class="label-control">Localidad</label>
                                <input type="text" id="localidad-accion" name="localidad-accion" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <table id="tabla_beneficiarios" class="table table-condensed table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tipo Beneficiario</th>
                                            <th width="20%"><span class="fa fa-female"></span> Femenino</th>
                                            <th width="20%"><span class="fa fa-male"></span> Masculino</th>
                                            <th width="20%">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3"></td>
                                            <td>
                                                <span class="form-control" id="total-beneficiarios-lbl">0</span>
                                                <input type="hidden" id="total-beneficiarios" name="total-beneficiarios">
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Calenderización</label>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#calendarizado-presupuesto" aria-controls="calendarizado-presupuesto" role="tab" data-toggle="tab">
                                    <span class="fa fa-usd"></span> Presupuesto
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#calendarizado-metas" aria-controls="calendarizado-metas" role="tab" data-toggle="tab">
                                    <span class="fa fa-table"></span> Metas
                                </a>
                            </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="calendarizado-presupuesto">
                                <br>
                                <div class="row">
                                   <!-- pARTE 1 -->
                                   <div class="col-sm-12">
                                    <div id="calendario-partidas" name="calendario-partidas"></div>                                
                                   </div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <span class="fa fa-link"></span> Total
                                                </span>
                                                <span class="form-control" id="cantidad-presupuesto-lbl">0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="calendarizado-metas">
                                <br>
                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <label class="control-label">
                                                Indicador
                                            </label>
                                            <p class="form-control-static" id="indicador_texto">
                                                Descripción del Indicador
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label">
                                                Unidad de Medida
                                            </label>
                                            <p class="form-control-static" id="unidad_medida_texto">
                                                Descripción de la unidad de medida
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="row">                                        
                                        <!-- pARTE 2 -->
            	                            <div class="col-sm-12">
    		    	                            <div id="calendario-metas" name="calendario-metas"></div>                                
	                	                   </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label class="control-label"><span class="fa fa-link"></span> Trim 1</label>
                                                    <span class="form-control" id="trim1-lbl">0</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label class="control-label"><span class="fa fa-link"></span> Trim 2</label>
                                                    <span class="form-control" id="trim2-lbl">0</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label class="control-label"><span class="fa fa-link"></span> Trim 3</label>
                                                    <span class="form-control" id="trim3-lbl">0</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label class="control-label"><span class="fa fa-link"></span> Trim 4</label>
                                                    <span class="form-control" id="trim4-lbl">0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label><span class="fa fa-link"></span> Total</label>
                                            <span class="form-control" id="cantidad-meta-lbl">0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->




@parent
@stop