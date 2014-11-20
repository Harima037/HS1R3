@extends('layouts.Modulo')

@section('title-page') Caratula de Captura @stop


@section('css')
@parent
<link href="{{ URL::to('bootstrap/css/bootstrap-select.min.css') }}" rel="stylesheet" media="screen">
@stop

@section('js')
@parent
<script src="{{ URL::to('bootstrap/js/bootstrap-select.min.js') }}"></script>
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/poa/caratula.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
    	<div class="panel panel-default datagrid" id="datagridCaratulas" data-edit-row="editar">
            <div class="panel-heading"><h4><i class="fa fa-file"></i> <b>{{$clasificacion_proyecto}}</b> <small>({{$tipo_proyecto}})</small></h4></div>
            <div class="panel-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a id="tablink-caratula" href="#caratula-captura" role="tab" data-toggle="tab">
                            Caratula de Captura
                        </a>
                    </li>
                    <li role="presentation" class="disabled">
                        <a id="tablink-componentes" href="#componentes" role="tab">
                            Componentes <span class="badge">0</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="caratula-captura">
                        <br>
                        {{$formulario}}
                    </div>
                    <div role="tabpanel" class="tab-pane" id="componentes">
                        <br>
                        {{$grid_componentes}}
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
                <button type="button" class="btn btn-primary" id="btn-componente-guardar-salir">Guardar</button>
                <button type="button" class="btn btn-success" id="btn-componente-guardar">Guardar y Agregar Actividades</button>
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
@parent
@stop