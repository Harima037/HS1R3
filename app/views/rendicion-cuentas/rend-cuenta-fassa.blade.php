@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/rendicion-cuentas/lista-fassa-rendicion.js') }}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default datagrid" id="datagridIndicadoresFASSA" data-edit-row="editar_avance" data-trim-activo="{{($trimestre_activo)?$trimestre_avance:0}}" data-mes-activo="{{$mes_avance}}">
            <div class="panel-heading"><h4><i class="fa {{ $sys_mod_activo->icono }}"></i> {{ $sys_mod_activo->nombre }}</h4></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6 col-md-6"></div>
                    <div class="col-lg-6">
                        <div class="btn-toolbar pull-right" >
                            <div class="btn-group" style="margin:5px">
                                <button type="button" class="btn btn-success btn-edit-rows" id="btn-detalles-proyecto">
                                    <span class="fa fa-edit"></span> Editar Avance del Indicador
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-striped table-hover">
                <thead>
                    <tr height="50">
                        <th><input type="checkbox" class="check-select-all-rows"></th>
                        <th>Indicador</th>
                        <th width="70">Trim 1</th>
                        <th width="70">Trim 2</th>
                        <th width="70">Trim 3</th>
                        <th width="70">Trim 4</th>
                        <th width="100">Estado</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('modals')
    <div class="modal fade" id="modalDatosSeguimiento" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-85-screen">
            <div class="modal-content modal-content-85-screen">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modalLabel">Nuevo</h4>
                </div>
                <div class="modal-body">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Programa Presupuestario</label>
                                        <p class="form-control-static" id="programa-presupuestario"></p>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Unidad Responsable</label>
                                        <p class="form-control-static" id="unidad-responsable"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel">
                        <ul class="nav nav-pills" role="tablist">
                            @for($i = 1 ; $i <= 4 ; $i++)
                            <li role="presentation" class="{{($i == 1)?'active':''}}">
                                <a href="#panel-trim-{{$i}}" aria-controls="panel-trim-{{$i}}" role="tab" data-toggle="tab">
                                    <span class="fa {{($trimestre_avance == $i)?'fa-calendar-o':'fa-calendar'}}"></span> Trim {{$i}}
                                </a>
                            </li>
                            @endfor
                        </ul>
                        <div class="tab-content">
                            <br>
                            @for($trim = 1 ; $trim <= 4 ; $trim++)
                            <div role="tabpanel" class="tab-pane {{($trim == 1)?'active':''}}" id="panel-trim-{{$trim}}">
                                <br>
                                <table id="avance-trim-{{$trim}}" class="table table-condensed tabla-avance-trim">
                                    <thead>
                                        <tr>
                                            <th>Nivel</th>
                                            <th>Indicador</th>
                                            <th>Meta Programada</th>
                                            <th>Avance del Trimestre</th>
                                            <th class="bg-success">Avance Acumulado</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success pull-left" id="btn-reporte">
                        <span class="fa fa-file-excel-o"></span> Imprimir Reporte
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn-editar-avance">
                        <span class="fa fa-pencil"></span> Capturar Avance
                    </button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<!-- Dejar parent al ultimo -->
@parent
@stop