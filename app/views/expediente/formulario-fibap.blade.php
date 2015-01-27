@extends('layouts.Modulo')

@section('title-page') Formulario de Captura @stop


@section('css')
@parent
<link href="{{ URL::to('bootstrap/css/bootstrap-select.min.css') }}" rel="stylesheet" media="screen">
<link href="{{ URL::to('css/chosen.bootstrap.min.css') }}" rel="stylesheet" type="text/css" media="screen">
@stop

@section('js')
@parent
<script src="{{ URL::to('bootstrap/js/bootstrap-select.min.js') }}"></script>
<script src="{{ URL::to('js/dependencias/chosen.jquery.min.js') }}"></script>
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/expediente/formulario-fibap.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="panel panel-default" id="formulario-fibap">
    <div class="panel-heading">
        <h4>
            <i class="fa fa-file"></i> <b>FIBAP</b> <small>(Ficha de Información Básica del Proyecto)</small>
            <span class="pull-right">
                <small><b><span id="clave-presupuestaria">Proyecto Nuevo</span></b></small>
            </span>
        </h4>
    </div>
    <div class="panel-body" id="mensaje-espera">
        <div class="alert alert-info">
            <h2><span class="fa fa-cog fa-spin"></span> Cargando información del formulario... por favor espere...</h2>
        </div>
    </div>
    <div class="panel-body hidden" id="panel-principal-formulario">
        <div role="tabpanel">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#datos-fibap" aria-controls="datos-fibap" role="tab" data-toggle="tab" id="tab-link-datos-fibap">
                        <span class="fa fa-square-o"></span> Datos Generales
                    </a>
                </li>
                <li role="presentation" class="disabled">
                    <a href="#antecedentes-fibap" aria-controls="antecedentes-fibap" role="tab" id="tab-link-antecedentes-fibap">
                        <span class="fa fa-square-o"></span> Antecedentes
                    </a>
                </li>
                <li role="presentation" class="disabled">
                    <a href="#acciones-fibap" aria-controls="acciones-fibap" role="tab" id="tab-link-acciones-fibap">
                        <span class="fa fa-square-o"></span> Acciones / Presupuesto
                    </a>
                </li>
                <!--li role="presentation" class="disabled">
                    <a href="#info-proyecto" aria-controls="info-proyecto" role="tab" id="tab-link-info-proyecto">
                        <span class="fa fa-info-circle"></span> Información del Proyecto
                    </a>
                </li-->
            </ul>

            <div class="tab-content" id="fibap-grupo-formularios">
        <!--  Begin Tab Panel: Datos del proyecto  -->
        <div role="tabpanel" class="tab-pane active" id="datos-fibap" data-form-id="form-fibap-datos">
        <form id="form-fibap-datos">
            <br>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="organismo-publico" class="control-label">Organismo Público</label>
                        <input type="text" class="form-control" id="organismo-publico" name="organismo-publico"/>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="sector" class="control-label">Sector</label>
                        <input type="text" class="form-control" id="sector" name="sector" maxlength="255"/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="subcomite" class="control-label">Subcomite</label>
                        <input type="text" class="form-control" id="subcomite" name="subcomite" maxlength="255"/>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="grupo-trabajo" class="control-label">Grupo de Trabajo</label>
                        <input type="text" class="form-control" id="grupo-trabajo" name="grupo-trabajo" maxlength="255"/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="tipo-proyecto" class="control-label">Tipo de proyecto</label>
                        {{Form::select('tipo-proyecto',array(''=>'Seleccione una opción') + $tipos_proyectos->lists('descripcion','id'),0,array('class'=>'form-control','id'=>'tipo-proyecto'))}}
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="form-group">
                        <label for="proyecto" class="control-label">Proyecto</label>
                        <input type="text" class="form-control" id="proyecto" name="proyecto" maxlength="255"/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="justificacion-proyecto" class="control-label">Justificación del Proyecto</label>
                        <textarea class="form-control" id="justificacion-proyecto" name="justificacion-proyecto"></textarea>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="descripcion-proyecto" class="control-label">Descripción del Proyecto</label>
                        <textarea class="form-control" id="descripcion-proyecto" name="descripcion-proyecto"></textarea>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="objetivo-proyecto" class="control-label">Objetivo del Proyecto</label>
                        <textarea class="form-control" id="objetivo-proyecto" name="objetivo-proyecto"></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="programa-presupuestal" class="control-label">Programa Presupuestario</label>
                        <select class="form-control selectpicker" name="programa-presupuestal" id="programa-presupuestal" data-live-search="true" data-size="8">
                            <option value=''>Seleccione un elemento</option>
                            @foreach($programa_presupuestario as $item)
                                <option value="{{ $item->clave }}">{{$item->clave}}{{' '}}{{ $item->descripcion }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label" for="vinculacion-ped">Alineación al PED (Plan Estatal de Desarrollo)</label>
                        <select class="form-control selectpicker" id="vinculacion-ped" name="vinculacion-ped" data-live-search="true" data-size="5">
                            <option value="">Seleciona un objetivo</option>
                            <!-- Inicio de ejes -->
                            @foreach ($objetivos_ped as $eje)
                                @if(count($eje->hijos))
                                    <optgroup label="{{$eje->clave . ' ' . $eje->descripcion}}">
                                        <!-- Inicio de temas -->
                                        @foreach ($eje->hijos as $tema)
                                            @if(count($tema->hijos))
                                                <optgroup label="{{$tema->clave . ' ' . $tema->descripcion}}">
                                                    <!-- Inicio de politicas -->
                                                    @foreach ($tema->hijos as $politica)
                                                        @if(count($politica->hijos))
                                                            <optgroup label="{{$politica->clave . ' ' . $politica->descripcion}}">
                                                                <!-- Inicio de objetivos -->
                                                                @foreach ($politica->hijos as $objetivo)
                                                                    <option value="{{$objetivo->id}}">
                                                                        {{$objetivo->clave . ' ' . $objetivo->descripcion}}
                                                                    </option>
                                                                @endforeach
                                                                <!-- Inicio de objetivos -->
                                                            </optgroup>
                                                        @endif
                                                    @endforeach
                                                    <!-- Fin de politicas -->
                                                </optgroup>
                                            @endif
                                        @endforeach
                                        <!-- Fin de temas -->
                                    </optgroup>
                                @endif
                            @endforeach
                            <!-- Fin de ejes -->
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <label class="control-label">Alineación a los objetivos de Desarrollo del milenio</label>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <small>
                            <label class="control-label" for="alineacion-especifica">Alineación especifica</label>
                        </small>
                        <input type="text" class="form-control" name="alineacion-especifica" id="alineacion-especifica">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <small>
                        <label class="control-label" for="alineacion-general">Alineación general</label>
                        </small>
                        <input type="text" class="form-control" name="alineacion-general" id="alineacion-general">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="control-label" for="cobertura">Cobertura</label>
                        {{Form::select('cobertura',array('' =>'Selecciona una cobertura') + $coberturas->lists('descripcion','id'),'',array('class'=>'form-control','id'=>'cobertura'))}}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div id="select-estado-panel" class="form-group">
                        <label class="control-label">Estado</label>
                        <p class="form-control-static">Chiapas</p>
                    </div>
                    <div id="select-municipio-panel" class="form-group">
                        <label class="control-label" for="municipio">Municipio</label>
                        {{Form::select('municipio',array('' =>'Selecciona un municipio') + $municipios->lists('nombre','clave'),'',array('class'=>'form-control selectpicker','id'=>'municipio','data-live-search'=>'true','data-container'=>'body','data-size'=>'8'))}}
                    </div>
                    <div id="select-region-panel" class="form-group">
                        <label class="control-label" for="region">Región</label>
                        {{Form::select('region',array('' =>'Selecciona una región') + $regiones->lists('nombre','region'),'',array('class'=>'form-control','id'=>'region'))}}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="control-label" for="tipo-beneficiario">Tipo de Beneficiario</label>
                        {{Form::select('tipo-beneficiario',array('' =>'Selecciona un beneficiario') + $tipos_beneficiarios->lists('descripcion','id'),'',array('class'=>'form-control selectpicker','id'=>'tipo-beneficiario','data-live-search'=>'true','data-container'=>'body','data-size'=>'8'))}}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label">Estadistica de Población</label>
                        <button type="button" class="btn btn-primary form-control"><span class="fa fa-table"></span> Ver</button>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label" for="total-beneficiarios-f">Femenino</label>
                        <div class="input-group">
                            <span class="input-group-addon"><span class="fa fa-female"></span></span>
                            <input type="number" class="form-control benef-totales" name="total-beneficiarios-f" id="total-beneficiarios-f">
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label" for="total-beneficiarios-m">Masculino</label>
                        <div class="input-group">
                            <span class="input-group-addon"><span class="fa fa-male"></span></span>
                            <input type="number" class="form-control benef-totales" name="total-beneficiarios-m" id="total-beneficiarios-m">
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label"><span class="fa fa-link"></span> Total</label>
                        <span id="total-beneficiarios" class="form-control"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-5">
                    <div class="form-group">
                        <label for="presupuesto-requerido" class="control-label">
                            Presupuesto Requerido
                        </label>
                        <input type="number" class="form-control" id="presupuesto-requerido" name="presupuesto-requerido"/>
                    </div>
                </div>
                <div class="col-sm-7">
                    <div class="form-group">
                        <label class="control-label">Periodo de Ejecución</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                Del
                            </span>
                            <input type="date" placeholder="aaaa-mm-dd" class="form-control" id="periodo-ejecucion-inicio" name="periodo-ejecucion-inicio">
                            <span class="input-group-addon">
                                Al
                            </span>
                            <input type="date" placeholder="aaaa-mm-dd" class="form-control" id="periodo-ejecucion-final" name="periodo-ejecucion-final">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><b>Documentación de soporte</b></div>
                        <div class="panel-body">
                            <div class="row">
                            @foreach ($documentos_soporte as $documento)
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                              <input type="checkbox"  id="documento_{{$documento->id}}" name="documento-soporte[]" value="{{$documento->id}}" >
                                              {{$documento->descripcion}}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        </div><!-- tab-pane -->
        <!--  End Tab Panel: Datos del proyecto  -->

        <!--  Begin Tab Panel: Antecedentes Financieros  -->
        <div role="tabpanel" class="tab-pane" id="antecedentes-fibap" data-form-id="form-fibap-antecedentes">
        <form id="form-fibap-antecedentes">
            <h4>Antecedentes Financieros</h4>
            <div class="row">
                <div class="col-sm-12 datagrid" id="datagridAntecedentes" data-edit-row="editar_antecedente">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="btn-toolbar pull-right" >
                                <div class="btn-group" style="margin:5px">
                                    <button type="button" class="btn btn-success" id="btn-agregar-antecedente">
                                        <span class="glyphicon glyphicon-plus"></span> Agregar antecedente
                                    </button>
                                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li>
                                            <a href="#" class="btn-edit-rows">
                                                <span class="glyphicon glyphicon-edit"></span> Editar
                                            </a>
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="#" class="btn-delete-rows">
                                                <span class="glyphicon glyphicon-remove"></span> Eliminar
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th><input type="checkbox" class="check-select-all-rows"></th>
                                <th>Año</th>
                                <th>Autorizado</th>
                                <th>Ejercido</th>
                                <th>%</th>
                                <th>Fecha de Corte</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="resultados-obtenidos">Resultados Obtenidos</label>
                                <textarea class="form-control" name="resultados-obtenidos" id="resultados-obtenidos" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="resultados-esperados">Resultados Esperados</label>
                                <textarea class="form-control" name="resultados-esperados" id="resultados-esperados" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        </div><!-- tab-pane -->
        <!--  End Tab Panel: Antecedentes Financieros  -->

        <!--  Begin Tab Panel: Acciones y distribucion de presupuesto  -->
        <div role="tabpanel" class="tab-pane" id="acciones-fibap">
            <h4>Presupuesto Requerido y Propuesta de Financiamiento</h4>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label">
                            Total Distribuido / Total Requerido :
                        </label>
                        <span class="text-muted" id="total-presupuesto-distribuido">$ 0.00</span> / <span class="text-muted" id="total-presupuesto-requerido">$ 0.00</span>
                        <div class="progress">
                            <div id="porcentaje_completo" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"> 0%
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="panel panel-success" id="tabla_presupuesto_partida">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr class="bg-success">
                                    <th width="100px">Partida</th>
                                    <th>Descripción</th>
                                    <th width="100px">Monto</th>
                                    <th width="90px">Porcentaje</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="col-sm-12"><label class="control-label">Origen del Presupuesto</label></div>
                <div class="col-sm-12">
                    <div class="row">
                        @foreach ($origenes_financiamiento as $origen)
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{$origen->descripcion}} :
                                    </label>
                                    <span class="text-muted totales-financiamiento" data-total-origen-id="{{$origen->id}}">
                                        $ 0.00
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="datagrid panel panel-primary" id="datagridAcciones" data-edit-row="editar_accion">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <h4>Acciones</h4>
                        </div>
                        <div class="col-sm-6">
                            <div class="btn-toolbar pull-right" >
                                <div class="btn-group" style="margin:5px">
                                    <button type="button" class="btn btn-primary" id="btn-agregar-accion">
                                        <span class="glyphicon glyphicon-plus"></span> Agregar Acción
                                    </button>
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li>
                                            <a href="#" class="btn-edit-rows">
                                                <span class="glyphicon glyphicon-edit"></span> Editar
                                            </a>
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="#" class="btn-delete-rows">
                                                <span class="glyphicon glyphicon-remove"></span> Eliminar
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>    
                </div>
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th><input type="checkbox" class="check-select-all-rows"></th>
                            <th>Entregable</th>
                            <th>Tipo</th>
                            <th>Acción</th>
                            <th>Modalidad</th>
                            <th>Presupuesto</th>
                            <th width="50px"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            
            <div id="datagrid-contenedor" class="hidden">
                <div class="datagrid panel panel-primary" id="datagridDistribucion" data-edit-row="editar_presupuesto" data-selected-id="">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-8">
                                <label class="control-label">Distribución del Presupuesto</label>
                                <div class="progress">
                                    <div id="porcentaje_accion" class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"> 0%
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="btn-toolbar pull-right" >
                                    <div class="btn-group" style="margin:5px">
                                        <button type="button" class="btn btn-info" id="btn-agregar-distribucion">
                                            <span class="glyphicon glyphicon-plus"></span> Agregar Presupuesto
                                        </button>
                                        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu pull-right" role="menu">
                                            <li>
                                                <a href="#" class="btn-edit-rows">
                                                    <span class="glyphicon glyphicon-edit"></span> Editar
                                                </a>
                                            </li>
                                            <li class="divider"></li>
                                            <li>
                                                <a href="#" class="btn-delete-rows">
                                                    <span class="glyphicon glyphicon-remove"></span> Eliminar
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th><input type="checkbox" class="check-select-all-rows"></th>
                                <th>Localidad</th>
                                <th>Municipio</th>
                                <th>Jurisdicción</th>
                                <th width="100px">Monto</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--  End Tab Panel: Acciones y distribucion de presupuesto  -->

        <!--  Begin Tab Panel: Información del Proyecto  -->
        <!--div role="tabpanel" class="tab-pane" id="info-proyecto" data-from-id="form-info-proyecto">
        
        </div-->
        <!--  End Tab Panel: Información del Proyecto  -->

            </div><!-- tab-content -->
        </div><!-- tab-panel -->

        <input type="hidden" id="id" name="id" value="{{{ $id or '' }}}">
        <input type="hidden" id="proyecto-id" name="proyecto-id" value="{{{ $proyecto_id or '' }}}">
        <div class="panel-footer">
            <div class="row">
                <div class="col-sm-12">
                    <button type="button" class="btn btn-primary" id="btn-fibap-guardar">
                        <span class="fa fa-save"></span> Guardar cambios
                    </button>
                    <button type="button" class="btn btn-default" id="btn-fibap-cancelar">
                        <span class="fa fa-chevron-left"></span> Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('modals')
<div class="modal fade" id="modal-antecedente" tabindex="-1" role="dialog" aria-labelledby="modalAntLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalAntLabel">Nuevo</h4>
            </div>
            <div class="modal-body">
                <form id="form-antecedente">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="anio-antecedente" class="control-label">Año</label>
                                <input type="number" class="form-control" id="anio-antecedente" name="anio-antecedente"/>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="autorizado-antecedente" class="control-label">Autorizado</label>
                                <input type="number" class="form-control" id="autorizado-antecedente" name="autorizado-antecedente"/>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="ejercido-antecedente" class="control-label">Ejercido</label>
                                <input type="number" class="form-control" id="ejercido-antecedente" name="ejercido-antecedente"/>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="fecha-corte-antecedente" class="control-label">Fecha de Corte </label>
                                <input type="date" placeholder="aaaa-mm-dd"  class="form-control" id="fecha-corte-antecedente" name="fecha-corte-antecedente"/>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="id-antecedente" id="id-antecedente">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-guardar" id="btn-antecedente-guardar">Guardar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade" id="modal-presupuesto" tabindex="-1" role="dialog" aria-labelledby="modalPresupLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalPresupLabel">Nuevo</h4>
            </div>
            <div class="modal-body">
                <form id="form-presupuesto">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="jurisdiccion-accion" class="label-control">Jurisdicción</label>
                                <select id="jurisdiccion-accion" name="jurisdiccion-accion" class="form-control chosen-one">
                                    <option value="">Selecciona una Jurisdicción</option>
                                    @foreach($jurisdicciones as $llave => $valor)
                                        <option value="{{$llave}}">{{$valor}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="municipio-accion" class="label-control">Municipio</label>
                                <select id="municipio-accion" name="municipio-accion" class="form-control chosen-one" >
                                    <option value="">Selecciona un Municipio</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="localidad-accion" class="label-control">Localidad</label>
                                <select id="localidad-accion" name="localidad-accion" class="form-control chosen-one">
                                    <option value="">Selecciona una Localidad</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label">
                                    Beneficiarios
                                </label>
                                <p class="form-control-static" id="tipo_beneficiario_texto">
                                    Tipo Beneficiario
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="beneficiarios-f">Femenino</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="fa fa-female"></span></span>
                                    <input type="number" class="form-control benef-totales-accion" name="beneficiarios-f" id="beneficiarios-f">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="beneficiarios-m">Masculino</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="fa fa-male"></span></span>
                                    <input type="number" class="form-control benef-totales-accion" name="beneficiarios-m" id="beneficiarios-m">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span class="fa fa-link"></span> Total</label>
                                <span class="form-control control-espejo" data-espejo-id="#total-beneficiarios-accion"></span>
                                <input type="hidden" id="total-beneficiarios-accion">
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
                                    @foreach ($meses as $clave => $mes)
                                        <div class="col-sm-3">
                                            <div class="form-group" data-grupo-mes="{{$clave}}">
                                                <label class="control-label">{{$mes}}</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon clave-partida-1">000</span>
                                                    <input id="mes-1-{{$clave}}" name="mes[1][{{$clave}}]" type="number" class="form-control input-sm presupuesto-mes valor-partida-1" data-presupuesto-mes="{{$clave}}" data-presupuesto-id="">
                                                </div>
                                                <div class="input-group">
                                                    <span class="input-group-addon clave-partida-2">000</span>
                                                    <input id="mes-2-{{$clave}}" name="mes[2][{{$clave}}]" type="number" class="form-control input-sm presupuesto-mes valor-partida-2" data-presupuesto-mes="{{$clave}}" data-presupuesto-id="">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="clearfix"></div>
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <span class="fa fa-link"></span> Total
                                                </span>
                                                <input type="hidden" id="cantidad-presupuesto" name="cantidad-presupuesto"/>
                                                <span class="form-control control-espejo" data-espejo-id="#cantidad-presupuesto"></span>
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
                                            @foreach ($meses as $clave => $mes)
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon">{{$mes}}</span>
                                                            <input id="meta-mes-{{$clave}}" name="meta-mes[{{$clave}}]" type="number" class="form-control input-sm meta-mes" data-meta-mes="{{$clave}}" data-meta-id="">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label class="control-label"><span class="fa fa-link"></span> Trim 1</label>
                                                    <span class="form-control control-espejo" data-espejo-id="#trim1"></span>
                                                    <input type="hidden" id="trim1" name="trim1">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label class="control-label"><span class="fa fa-link"></span> Trim 2</label>
                                                    <span class="form-control control-espejo" data-espejo-id="#trim2"></span>
                                                    <input type="hidden" id="trim2" name="trim2">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label class="control-label"><span class="fa fa-link"></span> Trim 3</label>
                                                    <span class="form-control control-espejo" data-espejo-id="#trim3"></span>
                                                    <input type="hidden" id="trim3" name="trim3">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label class="control-label"><span class="fa fa-link"></span> Trim 4</label>
                                                    <span class="form-control control-espejo" data-espejo-id="#trim4"></span>
                                                    <input type="hidden" id="trim4" name="trim4">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label><span class="fa fa-link"></span> Total</label>
                                            <input type="hidden" id="cantidad-meta" name="cantidad-meta"/>
                                            <span class="form-control control-espejo" data-espejo-id="#cantidad-meta"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="id-desglose" id="id-desglose">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-guardar" id="btn-presupuesto-guardar">Guardar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!--
    Modal para Acción
-->

<div class="modal fade" id="modal-accion" tabindex="-1" role="dialog" aria-labelledby="modalAccionLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalAccionLabel">Nuevo</h4>
            </div>
            <div class="modal-body">
                <form id="form-accion">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="objeto-gasto-presupuesto" class="control-label">Capitulo, Concepto, Partida</label>
                                <select class="form-control selectpicker" id="objeto-gasto-presupuesto_1" name="objeto-gasto-presupuesto[]" data-live-search="true" data-size="8">
                                    <option value="">Seleciona una partida</option>
                                    @foreach ($objetos_gasto as $capitulo)
                                        @if(count($capitulo->hijos))
                                            @foreach ($capitulo->hijos as $concepto)
                                                @if(count($concepto->hijos))
                                                    @foreach ($concepto->hijos as $generica)
                                                        @if(count($generica->hijos))
                                                            <optgroup label="{{$capitulo->clave . ' ' . $capitulo->descripcion . '<br>' . $concepto->clave . ' ' . $concepto->descripcion . '<br>' . $generica->clave . ' ' . $generica->descripcion}}">
                                                                @foreach ($generica->hijos as $especifica)
                                                                    <option value="{{$especifica->id}}">
                                                                        {{$especifica->clave}} - {{$especifica->descripcion}}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control selectpicker" id="objeto-gasto-presupuesto_2" name="objeto-gasto-presupuesto[]" data-live-search="true" data-size="8">
                                    <option value="">Seleciona una partida</option>
                                    @foreach ($objetos_gasto as $capitulo)
                                        @if(count($capitulo->hijos))
                                            @foreach ($capitulo->hijos as $concepto)
                                                @if(count($concepto->hijos))
                                                    @foreach ($concepto->hijos as $generica)
                                                        @if(count($generica->hijos))
                                                            <optgroup label="{{$capitulo->clave . ' ' . $capitulo->descripcion . '<br>' . $concepto->clave . ' ' . $concepto->descripcion . '<br>' . $generica->clave . ' ' . $generica->descripcion}}">
                                                                @foreach ($generica->hijos as $especifica)
                                                                    <option value="{{$especifica->id}}">
                                                                        {{$especifica->clave}} - {{$especifica->descripcion}}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="form-group">
                                <label for="indicador" class="control-label">Indicador</label>
                                <input type="text" class="form-control" name="indicador" id="indicador">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="unidad-medida" class="control-label">Unidad de Medida</label>
                                {{Form::select('unidad-medida',array(''=>'Seleccione una unidad') + $unidades_medida->lists('descripcion','id'),'',array('class'=>'form-control selectpicker','id'=>'unidad-medida','data-live-search'=>'true','data-size'=>'8'))}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="entregable" class="control-label">Entregable</label>
                                {{Form::select('entregable',array(''=>'Seleccione una opción') + $entregables->lists('descripcion','id'),'',array('class'=>'form-control chosen-one','id'=>'entregable'))}}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="tipo-componente" class="control-label">Tipo</label>
                                <select id="tipo-componente" name="tipo-componente" class="form-control chosen-one">
                                    <option value="">Seleccione un tipo</option>
                                    <option value="NA" data-habilita-id="NA"> N / A </option>
                                @foreach ($entregables_tipos as $tipo)
                                    <option value="{{$tipo->id}}" data-habilita-id="{{$tipo->idEntregable}}" class="hidden" disabled>
                                        {{$tipo->descripcion}}
                                    </option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="accion-componente" class="control-label">Acción</label>
                                <select id="accion-componente" name="accion-componente" class="form-control chosen-one">
                                    <option value="">Seleccione una acción</option>
                                @foreach ($entregables_acciones as $accion)
                                    <option value="{{$accion->id}}" data-habilita-id="{{$accion->idEntregable}}" class="hidden" disabled>
                                        {{$accion->descripcion}}
                                    </option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12"><label class="control-label">Origen del Presupuesto</label></div>
                        <div class="col-sm-12">
                            <div class="row">
                                @foreach ($origenes_financiamiento as $origen)
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="accion-origen-{{$origen->id}}" class="control-label">{{$origen->descripcion}}</label>
                                            <input type="number" class="form-control accion-origen-financiamiento" id="accion-origen-{{$origen->id}}" name="accion-origen[{{$origen->id}}]" data-origen-id="{{$origen->id}}" data-captura-id="">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label for="accion-presupuesto-requerido" class="control-label">
                                    <span class="fa fa-link"></span> Presupuesto Requerido
                                </label>
                                <span class="form-control control-espejo" data-espejo-id="#accion-presupuesto-requerido"></span>
                                <input type="hidden" id="accion-presupuesto-requerido" name="accion-presupuesto-requerido"/>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="id-accion" id="id-accion">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-guardar" id="btn-accion-guardar">Guardar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@parent
@stop
