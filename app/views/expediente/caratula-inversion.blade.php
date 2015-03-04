@extends('layouts.Modulo')

@section('title-page') Caratula de Captura de Proyectos de Inversión @stop


@section('css')
@parent
<link href="{{ URL::to('css/chosen.bootstrap.min.css') }}" rel="stylesheet" type="text/css" media="screen">
@stop

@section('js')
@parent
<script src="{{ URL::to('js/dependencias/chosen.jquery.min.js') }}"></script>
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/expediente/caratulaProyecto.js')}}"></script>
<script src="{{ URL::to('js/modulos/expediente/caratulaBeneficiario.js')}}"></script>
<script src="{{ URL::to('js/modulos/expediente/caratulaFibap.js')}}"></script>
<script src="{{ URL::to('js/modulos/expediente/fibapAntecedentes.js')}}"></script>
<script src="{{ URL::to('js/modulos/expediente/fibapAcciones.js')}}"></script>
<script src="{{ URL::to('js/modulos/expediente/caratula-inversion.js')}}"></script>
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
                        <a href="#caratula-captura" aria-controls="caratula-captura" role="tab" data-toggle="tab" id="tab-link-caratula">
                            <span class="fa fa-square-o"></span> Caratula
                        </a>
                    </li>
                    <li role="presentation" class="disabled">
                        <a href="#caratula-beneficiarios" aria-controls="caratula-beneficiarios" role="tab" data-toggle="" id="tab-link-caratula-beneficiarios">
                            <span class="fa fa-square-o"></span> Beneficiarios <span class="badge">0</span>
                        </a>
                    </li>
                    <li role="presentation" class="disabled">
                        <a href="#caratula-fibap" aria-controls="caratula-fibap" role="tab" data-toggle="" id="tab-link-datos-fibap">
                            <span class="fa fa-square-o"></span> FIBAP
                        </a>
                    </li>
                    <li role="presentation" class="disabled">
                        <a href="#antecedentes-fibap" aria-controls="antecedentes-fibap" role="tab" data-toggle="" id="tab-link-antecedentes-fibap">
                            <span class="fa fa-square-o"></span> Antecedentes
                        </a>
                    </li>
                    <li role="presentation" class="disabled pull-right">
                        <a href="#acciones-fibap" aria-controls="acciones-fibap" role="tab" data-toggle="" id="tab-link-acciones-fibap">
                            <span class="fa fa-square-o"></span> Componentes <span class="badge">0 / 2</span>
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
                    <div role="tabpanel" class="tab-pane" id="caratula-fibap">
                        <br>
                        {{$formulario_fibap}}
                    </div>
                    <div role="tabpanel" class="tab-pane" id="antecedentes-fibap">
                        <br>
                        {{$formulario_antecedentes}}
                    </div>
                    <div role="tabpanel" class="tab-pane" id="acciones-fibap">
                        <br>
                        {{$formulario_acciones}}
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

@parent
@stop