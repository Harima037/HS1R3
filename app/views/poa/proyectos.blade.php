@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/poa/proyectos.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default datagrid" id="datagridCaratulas" data-edit-row="editar">
            <div class="panel-heading"><h4><i class="fa {{ $sys_mod_activo->icono }}"></i> {{ $sys_mod_activo->nombre }}</h4></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="input-group" style="margin:5px">                            
                            <input type="text" class="form-control txt-quick-search" placeholder="Buscar">
                            <span class="input-group-btn">
                                <button class="btn btn-default btn-quick-search" type="button"><span class="glyphicon glyphicon-search"></span></button>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="btn-toolbar pull-right" >
                            @section('panel-botones')
                                <div class="btn-group" style="margin:5px">
                                    <button type="button" class="btn btn-success btn-datagrid-agregar">
                                        <span class="glyphicon glyphicon-plus"></span> Nuevo Proyecto
                                    </button>
                                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li>
                                            <a href="#" class="btn-edit-rows"><span class="glyphicon glyphicon-edit"></span> Editar</a>
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="#" class="btn-delete-rows"><span class="glyphicon glyphicon-remove"></span> Eliminar</a>
                                        </li>
                                    </ul>
                                </div>
                            @show
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="check-select-all-rows"></th>
                        <th>Clave</th>
                        <th>Nombre Técnico</th>
                        <th>Tipo de Proyecto</th>
                        <th>Estatus</th>
                        <th style="text-align:center; width:150px;"><span class="glyphicon glyphicon-user"></span></th>
                        <th style="text-align:center; width:150px;"><span class="glyphicon glyphicon-calendar"></span></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div class="panel-footer">
                <div class="btn-toolbar ">
                    <div class="btn-group pull-right" style="margin-left:5px; margin-bottom:5px;">
                        <button class="btn btn-default btn-back-rows"><i class="glyphicon glyphicon-arrow-left"></i></button>
                        <button class="btn btn-default btn-next-rows"><i class="glyphicon glyphicon-arrow-right"></i></button>
                    </div>
                    <div class="btn-group pull-right " style="width:200px; ">   
                        <div class="input-group" > 
                            <span class="input-group-addon">Pág.</span> 
                            <input type="text" class="txt-go-page form-control" style="text-align:center" value="1" >     
                            <span class="input-group-addon btn-total-paginas" data-pages="0">de 0</span> 
                            <div class="input-group-btn dropup">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
                                <ul class="dropdown-menu pull-right">
                                    <li><a class="btn-go-first-rows" href="#">Primera Página</a></li>
                                    <li><a class="btn-go-last-rows" href="#">Última Página</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('modals')
    <div class="modal fade" id="modalCaratulas" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-75-screen">
            <div class="modal-content modal-content-75-screen">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modalLabel">Nuevo</h4>
                </div>
                <div class="modal-body">
                    <form action="" id="form_caratula">
                        <div class="form-group">
                            <label for="tipo_proyecto" class="control-label">Seleccione el tipo de proyecto</label>
                            {{Form::select('tipo_proyecto',array(''=>'Seleccione una opción') + $tipos_proyectos->lists('descripcion','id'),0,array('class'=>'form-control','id'=>'tipo_proyecto'))}}
                        </div>
                        <div class="form-group">
                            <label for="clasificacion_proyecto" class="control-label">Seleccione la clase de proyecto a capturar</label>
                            {{Form::select('clasificacion_proyecto',array('' =>'Selecciona un tipo de proyecto') + $clasificacion_proyectos->lists('descripcion','id'),0,array('class'=>'form-control','id'=>'clasificacion_proyecto'))}}
                        </div>
                        <div class="form-group hidden" id="opciones_fibap">
                            <div class="help-text">
                                Para poder generar el proyecto de inversión se necesita capturar la Ficha de Información Básica del Proyecto (FIBAP).
                            </div>
                            <div id="orden_fibap" class="btn-group btn-group-justified" data-toggle="buttons">
                                <label class="btn btn-primary active">
                                    <input type="radio" name="fibap" id="fibap_despues" value="despues" autocomplete="off" checked> Capturar FIBAP después
                                </label>
                                <label class="btn btn-primary">
                                    <input type="radio" name="fibap" id="fibap_antes" value="antes" autocomplete="off"> Capturar FIBAP
                                </label>
                            </div>
                        </div>
                    </form>
                    <div id="datos-proyecto">
                        <div class="row">
                            <div class="col-sm-6">
                                <label class="control-label">Nombre Técnico</label>
                                <p id="lbl_nombre_tecnico" class="form-control-static">asdfjk </p>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label">Clave Presupuestaria</label>
                                <p id="lbl_clave_presupuestaria" class="form-control-static">asdfjk </p>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label">Clasificación del Proyecto</label>
                                <p id="lbl_nombre_tecnico" class="form-control-static">asdfjk </p>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label">Estatus</label>
                                <p id="lbl_clave_presupuestaria" class="form-control-static">asdfjk </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary btn-guardar">Ir a la caratula de captura</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<!-- Dejar parent al ultimo -->
@parent
@stop