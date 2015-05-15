@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/administrador/configurar-seguimiento-metas.js')}}"></script>
@stop


@section('aside')
@stop

@section('content')
<div class="row">
	<div class="col-sm-6 col-sm-offset-3">
		<div class="panel panel-default datagrid" id="datagridModulo" data-edit-row="editar">
            <div class="panel-heading"><h4><i class="fa {{ $sys_mod_activo->icono }}"></i> {{ $sys_mod_activo->nombre }}</h4></div>
            <div class="panel-body">
                <div class="row">
                    <form id="form_configuracion">
                    @foreach($variables as $variable)
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="control-label" for="{{$variable->variable}}">{{$variable->variable}}</label>
                            <input type="text" class="form-control" id="{{$variable->variable}}" name="{{$variable->variable}}" value="{{$variable->valor}}">
                        </div>
                    </div>
                    @endforeach
                    </form>
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-primary pull-right" id="btn-guardar-configuracion">
                            <span class="fa fa-save"></span> Guardar Configuración
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
	
