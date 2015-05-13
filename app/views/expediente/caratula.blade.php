@extends('layouts.Modulo')

@section('title-page') Caratula de Captura @stop


@section('css')
@parent
<link href="{{ URL::to('css/chosen.bootstrap.min.css') }}" rel="stylesheet" type="text/css" media="screen">
@stop

@section('js')
@parent
<script src="{{ URL::to('js/dependencias/chosen.jquery.min.js') }}"></script>
<script src="{{ URL::to('js/dependencias/jquery.csv-0.71.min.js') }}"></script>
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/expediente/fuenteFinanciamiento.js')}}"></script>
<script src="{{ URL::to('js/modulos/expediente/metasMesCSV.js')}}"></script>
<script src="{{ URL::to('js/modulos/expediente/caratula.js')}}"></script>
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
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a id="tablink-caratula" href="#caratula-captura" role="tab" data-toggle="tab">
                            Caratula de Captura
                        </a>
                    </li>
                    <li role="presentation" class="disabled">
                        <a id="tablink-beneficiarios" href="#caratula-beneficiarios" role="tab">
                            Beneficiarios <span class="badge">0</span>
                        </a>
                    </li>
                    <li role="presentation" class="disabled">
                        <a id="tablink-fuentes-financiamiento" href="#caratula-fuentes-financiamiento" role="tab">
                            Financiamiento
                        </a>
                    </li>
                    <li role="presentation" class="disabled">
                        <a id="tablink-componentes" href="#componentes" role="tab">
                            Componentes <span class="badge">0 / 2</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="caratula-captura">
                        <br>
                        {{$formulario}}
                    </div>
                    <div role="tabpanel" class="tab-pane" id="caratula-beneficiarios">
                        <br>
                        {{$grid_beneficiarios}}
                    </div>
                    <div role="tabpanel" class="tab-pane" id="caratula-fuentes-financiamiento">
                        <br>
                        {{$grid_fuentes_financiamiento}}
                    </div>
                    <div role="tabpanel" class="tab-pane" id="componentes">
                        <br>
                        {{$grid_componentes}}
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
                </div>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-default" id="btn-proyecto-cancelar">
                            <span class="fa fa-chevron-left"></span> Regresar a la lista de Proyectos
                        </button>
                        <button type="button" class="btn btn-success" id="btn-enviar-proyecto">
                            <span class="fa fa-send-o"></span> Enviar Proyecto a Revisión
                        </button>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>
@stop

@section('modals')
<div class="modal fade" id="modalComponente" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalLabel">Nuevo</h4>
            </div>
            <div class="modal-body">
                {{$formulario_componente}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-grupo-guardar" id="btn-componente-guardar-salir">Guardar</button>
                <button type="button" class="btn btn-success btn-grupo-guardar" id="btn-componente-guardar">Guardar y Agregar Actividades</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="modalActividad" tabindex="-1" role="dialog" aria-labelledby="modalActLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalActLabel">Nuevo</h4>
            </div>
            <div class="modal-body">
                {{$formulario_actividades}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-guardar" id="btn-actividad-guardar">Guardar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!--div class="modal fade" id="modalBeneficiario" tabindex="-1" role="dialog" aria-labelledby="modalBenefLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalBenefLabel">Nuevo</h4>
            </div>
            <div class="modal-body">
                {d{$formulario_beneficiario}d}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-guardar" id="btn-beneficiario-guardar">Guardar</button>
            </div>
        </div><-f- /.modal-content -f-d>
    </div><-f- /.modal-dialog -f-d>
</div--><!-- /.modal -->
@parent
@stop