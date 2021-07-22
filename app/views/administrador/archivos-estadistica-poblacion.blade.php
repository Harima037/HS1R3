@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/administrador/control-archivos-poblacion.js')}}"></script>
@stop


@section('aside')
@stop

@section('content')
<div class="row">
	<div class="col-sm-6 col-sm-offset-3">
		<div class="panel panel-default">
            <div class="panel-heading"><h4><i class="fa {{ $sys_mod_activo->icono }}"></i> {{ $sys_mod_activo->nombre }}</h4></div>
            <div class="panel-body">
                <div class="row">
                    <form id="form_archivos">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label" for="titulo">Titulo</label>
                                <input type="text" id="titulo" name="titulo" accept=".pdf" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label" for="archivo-poblacion">Archivo (PDF)</label>
                                <input type="file" id="archivo-poblacion" name="archivo-poblacion" accept=".pdf" class="form-control">
                            </div>
                        </div>
                    </form>
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-primary pull-right" id="btn-subir-archivo">
                            <span class="fa fa-upload"></span> Subir Archivo
                        </button>
                    </div>
                </div>
                <div class="row">
                    <table class="table table-striped table-hover table-condensed" id="tabla-lista-archivos">
                        <thead>
                            <tr>
                                <th>Titulo</th>
                                <th>Archivo</th>
                                <th width="1">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
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
	
