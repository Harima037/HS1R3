@extends('layouts.Modulo')

@section('title-page') Formulario de Captura @stop


@section('css')
@parent
<link href="{{ URL::to('bootstrap/css/bootstrap-select.min.css') }}" rel="stylesheet" media="screen">
@stop

@section('js')
@parent
<script src="{{ URL::to('bootstrap/js/bootstrap-select.min.js') }}"></script>
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/poa/formulario-fibap.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="panel panel-default" id="formulario-fibap">
    <div class="panel-heading">
        <h4>
            <i class="fa fa-file"></i> <b>FIBAP</b> <small>(Ficha de Información Básica del Proyecto)</small>
            <span class="pull-right"><small><b>{{(isset($clave_presupuestal))?$clave_presupuestal:'Proyecto Nuevo'}}</b></small></span>
        </h4>
    </div>
    <div class="panel-body">
    <div role="tabpanel">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#datos-fibap" aria-controls="datos-fibap" role="tab" data-toggle="tab" id="tab-link-datos-fibap">
                    <span class="fa fa-square-o"></span> Datos del Proyecto
                </a>
            </li>
            <li role="presentation" class="disabled">
                <a href="#antecedentes-fibap" aria-controls="antecedentes-fibap" role="tab" id="tab-link-antecedentes-fibap">
                    <span class="fa fa-square-o"></span> Antecedentes
                </a>
            </li>
            <li role="presentation" class="disabled">
                <a href="#presupuesto-fibap" aria-controls="presupuesto-fibap" role="tab" id="tab-link-presupuesto-fibap">
                    <span class="fa fa-square-o"></span> Presupuesto
                </a>
            </li>
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
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="justificacion-proyecto" class="control-label">Justificación del Proyecto</label>
                    <textarea class="form-control" id="justificacion-proyecto" name="justificacion-proyecto"></textarea>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="descripcion-proyecto" class="control-label">Descripción del Proyecto</label>
                    <textarea class="form-control" id="descripcion-proyecto" name="descripcion-proyecto"></textarea>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="programa-presupuestal" class="control-label">Programa Presupuestario</label>
                    <select class="form-control selectpicker" name="programa-presupuestal" id="programa-presupuestal" data-live-search="true">
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
                    <select class="form-control selectpicker" id="vinculacion-ped" name="vinculacion-ped" data-live-search="true">
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
                    {{Form::select('cobertura',array('' =>'Selecciona una cobertura') + $coberturas->lists('descripcion','id'),'',array('class'=>'form-control selectpicker','id'=>'cobertura'))}}
                </div>
            </div>
            <div class="col-sm-4">
                <div id="select-estado-panel" class="form-group">
                    <label class="control-label">Estado</label>
                    <p class="form-control-static">Chiapas</p>
                </div>
                <div id="select-municipio-panel" class="form-group">
                    <label class="control-label" for="municipio">Municipio</label>
                    {{Form::select('municipio',array('' =>'Selecciona un municipio') + $municipios->lists('nombre','clave'),'',array('class'=>'form-control selectpicker','id'=>'municipio','data-live-search'=>'true','data-container'=>'body'))}}
                </div>
                <div id="select-region-panel" class="form-group">
                    <label class="control-label" for="region">Región</label>
                    {{Form::select('region',array('' =>'Selecciona una región') + $regiones->lists('nombre','region'),'',array('class'=>'form-control selectpicker','id'=>'region','data-container'=>'body'))}}
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="control-label" for="tipo-beneficiario">Tipo de Beneficiario</label>
                    {{Form::select('tipo-beneficiario',array('' =>'Selecciona un beneficiario') + $tipos_beneficiarios->lists('descripcion','id'),'',array('class'=>'form-control selectpicker','id'=>'tipo-beneficiario','data-live-search'=>'true','data-container'=>'body'))}}
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
                            <th>Autorizados</th>
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

    <!--  Begin Tab Panel: Presupuesto y Propuesta  -->
    <div role="tabpanel" class="tab-pane" id="presupuesto-fibap" data-form-id="form-fibap-presupuesto">
    <form id="form-fibap-presupuesto">
        <h4>Presupuesto Requerido y Propuesta de Financiamiento</h4>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="presupuesto-requerido" class="control-label">Presupuesto Requerido</label>
                    <input type="text" class="form-control" id="presupuesto-requerido" name="presupuesto-requerido"/>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="periodo-ejecucion" class="control-label">Periodo de Ejecución</label>
                    <input type="text" class="form-control" id="periodo-ejecucion" name="periodo-ejecucion"/>
                </div>
            </div>
            <div class="col-sm-1"><label class="control-label">Origen</label></div>
            <div class="col-sm-11">
                <div class="row">
                    @foreach ($origenes_financiamiento as $origen)
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="origen_{{$origen->id}}" class="control-label">{{$origen->descripcion}}</label>
                                <input type="text" class="form-control origen-financiamiento" id="origen_{{$origen->id}}" name="origen[{{$origen->id}}]" data-origen-id="{{$origen->id}}" data-captura-id="">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-primary datagrid" id="datagridPresupuesto" data-edit-row="editar_presupuesto">
                    <div class="panel-heading">
                        <b>Distribución del Presupuesto Estatal</b>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="btn-toolbar pull-right" >
                                    <div class="btn-group" style="margin:5px">
                                        <button type="button" class="btn btn-success" id="btn-agregar-presupuesto">
                                            <span class="glyphicon glyphicon-plus"></span> Agregar
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
                    </div>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th><input type="checkbox" class="check-select-all-rows"></th>
                                <th>Capitulo</th>
                                <th>Concepto</th>
                                <th>Partida</th>
                                <th>Descripción</th>
                                <th>Cantidad</th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
    </div><!-- tab-pane -->
    <!--  End Tab Panel: Presupuesto y Propuesta  -->

        </div><!-- tab-content -->
    </div><!-- tab-panel -->
    
    <input type="hidden" id="id" name="id" value="{{{ $id or '' }}}">
    <input type="hidden" id="proyecto-id" name="proyecto-id">
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
                                <input type="text" class="form-control" id="anio-antecedente" name="anio-antecedente"/>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="autorizado-antecedente" class="control-label">Autorizado</label>
                                <input type="text" class="form-control" id="autorizado-antecedente" name="autorizado-antecedente"/>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="ejercido-antecedente" class="control-label">Ejercido</label>
                                <input type="text" class="form-control" id="ejercido-antecedente" name="ejercido-antecedente"/>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="fecha-corte-antecedente" class="control-label">Fecha de Corte</label>
                                <input type="date" class="form-control" id="fecha-corte-antecedente" name="fecha-corte-antecedente"/>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalPresupLabel">Nuevo</h4>
            </div>
            <div class="modal-body">
                <form id="form-presupuesto">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="capitulo-presupuesto" class="control-label">Capitulo, Concepto, Partida</label>
                                <input type="text" class="form-control" id="capitulo-presupuesto" name="capitulo-presupuesto"/>
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group">
                                <label for="descripcion-presupuesto" class="control-label">Descripción</label>
                                <input type="text" class="form-control" id="descripcion-presupuesto" name="descripcion-presupuesto"/>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label for="cantidad-presupuesto" class="control-label">Cantidad</label>
                                <input type="text" class="form-control" id="cantidad-presupuesto" name="cantidad-presupuesto"/>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-guardar" id="btn-presupuesto-guardar">Guardar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@parent
@stop