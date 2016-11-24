@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/visor/indicadores-resultados.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default datagrid" id="datagridProyectos" data-edit-row="cargar_datos_proyecto" data-trim-activo="{{$trimestre_avance}}" data-mes-activo="{{$mes_avance}}" data-mes-actual="{{$mes_actual}}">
            <div class="panel-heading"><h4><i class="fa {{ $sys_mod_activo->icono }}"></i> Indicadores de Resultados {{$anio_captura}}</h4></div>
            @if(isset($mostrar_filtrado))
            <div class="panel-body bg-info">
                <div class="row">
                    <div class="col-sm-7">
                        <label class="control-label pull-right" style="margin-top:5px;margin-bottom:5px;" for="filtro-jurisdiccion">
                            Nivel : 
                        </label>
                    </div>
                    <div class="col-sm-5">
                        <select class="form-control" id="filtro-jurisdiccion">
                            <option value="">Estatal</option>
                            @foreach($jurisdicciones as $clave => $jurisdiccion)
                            <option value="{{$clave}}" {{($jurisdiccion_select==$clave)?'selected':''}}>{{$clave}} {{$jurisdiccion}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            @endif
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-5">
                        <!--div class="input-group">
                            <input type="text" class="form-control txt-quick-search" placeholder="Buscar">
                            <span class="input-group-btn">
                                <button class="btn btn-default btn-quick-search" type="button"><span class="glyphicon glyphicon-search"></span></button>
                            </span>
                        </div-->
                    </div>
                    <div class="col-sm-5">
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-block btn-success btn-edit-rows" id="btn-justificacion-jurisdiccion">
                            <span class="fa fa-file-excel-o"></span> Descargar Excel
                        </button>
                    </div>
                </div>
            </div>
            <table class="table table-striped table-hover table-condensed">
                <thead>
                    <tr height="50">
                        <th></th>
                        <th colspan="2" >Clave Presupuestaria - Nombre Técnico / Indicador</th>
                        <th width="80" class="text-center">Meta</th>
                        <th width="80" class="text-center">Avance</th>
                        <th width="80" class="text-center">%</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div class="panel-footer">
                <div class="btn-toolbar"></div>
            </div>
        </div>
    </div>
</div>
@stop

@section('modals')
@parent
@stop