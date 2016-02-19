@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/reportes/evaluacion-proyectos.js') }}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default datagrid" id="datagridProyectos" data-edit-row="editar">
            <div class="panel-heading"><h4><i class="fa {{ $sys_mod_activo->icono }}"></i> {{ $sys_mod_activo->nombre }}</h4></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <input type="text" class="form-control txt-quick-search" placeholder="Buscar">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select class="form-control" id="mes">
                            @foreach($meses as $clave => $mes)
                                <option value="{{$clave}}" @if($clave == $mes_actual) selected @endif>
                                    {{$mes}}
                                </option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <input type="number" class="form-control" id="ejercicio" placeholder="Ejercicio" value="{{$ejercicio}}" />
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <button class="btn btn-default btn-block btn-quick-search" type="button"><span class="fa fa-search"></span></button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary btn-block" id="btn-ver-reporte">
                            <span class="fa fa-file"></span> Reporte
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('modals')
<!-- Dejar parent al ultimo -->
@parent
@stop