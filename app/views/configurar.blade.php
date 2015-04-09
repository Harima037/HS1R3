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
                <h3 class="panel-title">Cambiar mes de Captura</h3>
            </div>
            <div class="panel-body">
                <form id="formCambiarMes" class="form form-vertical" role="form" method="post">
                    <div class="form-group">
                        <label for="mes-captura">Mes de Captura</label>
                        <select id="mes-captura" name="mes-captura" class="form-control">
                            <option value="0" {{($usuario->mesCaptura == NULL)?'selected="selected"':''}}>Usar mes del sistema</option>
                            <option value="1" {{($usuario->mesCaptura == 1)?'selected="selected"':''}}>Enero</option>
                            <option value="2" {{($usuario->mesCaptura == 2)?'selected="selected"':''}}>Febrero</option>
                            <option value="3" {{($usuario->mesCaptura == 3)?'selected="selected"':''}}>Marzo</option>
                            <option value="4" {{($usuario->mesCaptura == 4)?'selected="selected"':''}}>Abril</option>
                            <option value="5" {{($usuario->mesCaptura == 5)?'selected="selected"':''}}>Mayo</option>
                            <option value="6" {{($usuario->mesCaptura == 6)?'selected="selected"':''}}>Junio</option>
                            <option value="7" {{($usuario->mesCaptura == 7)?'selected="selected"':''}}>Julio</option>
                            <option value="8" {{($usuario->mesCaptura == 8)?'selected="selected"':''}}>Agosto</option>
                            <option value="9" {{($usuario->mesCaptura == 9)?'selected="selected"':''}}>Septiembre</option>
                            <option value="10" {{($usuario->mesCaptura == 10)?'selected="selected"':''}}>Octubre</option>
                            <option value="11" {{($usuario->mesCaptura == 11)?'selected="selected"':''}}>Noviembre</option>
                            <option value="12" {{($usuario->mesCaptura == 12)?'selected="selected"':''}}>Diciembre</option>
                        </select>
                    </div>
                    <button id="btnCambiarMes" type="button" class="btn btn-primary">
                        Cambiar Mes
                    </button>
                </form>
            </div>
        </div>
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