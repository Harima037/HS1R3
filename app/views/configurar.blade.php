@extends('layouts.Modulo')

@section('title-page') Cambiar Contraseña @stop

@section('js')
@parent
<script src="{{ URL::to('js/modulos/configurar/cuenta.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-sm-6 col-sm-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Cambiar Contraseña</h3>
            </div>
            <div class="panel-body">
                <form id="formCambiarPass" class="form form-vertical" role="form" method="post">
                    <input type="hidden" name="id" id="id" value="{{$usuario->id}}">
                    <div class="form-group">
                        <label for="password">Nueva Contraseña</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="repeat_password">Confirmar Contraseña</label>
                        <input type="password" name="password_confirm" id="password_confirm" class="form-control">
                    </div>
                    <button id="btnGuardarContrasena" type="button" class="btn btn-primary">
                        Cambiar Contraseña
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('modals')
@parent
@stop