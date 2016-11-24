@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/reportes/inversion.js')}}"></script>
<script src="{{ URL::to('js/modulos/reportes/lista-proyectos-rendicion.js') }}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default datagrid" id="datagridProyectos" data-edit-row="cargar_datos_proyecto">
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
                            <input type="number" class="form-control" id="ejercicio" placeholder="Ejercicio" value="{{$anio_captura}}" />
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <button class="btn btn-default btn-block btn-quick-search" type="button"><span class="fa fa-search"></span></button>
                    </div>
                    <div class="col-md-1">
                        
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-success btn-block btn-edit-rows" id="btn-detalles-proyecto">
                            <span class="fa fa-edit"></span> Ver Detalles del Proyecto
                        </button>
                    </div>
                </div>
            </div>
            <table class="table table-striped table-hover table-condensed">
                <thead>
                    <tr height="50">
                        <th><input type="checkbox" class="check-select-all-rows"></th>
                        <th width="200">Clave Presupuestaria</th>
                        <th>Nombre Técnico</th>
                        <!--th width="230">Revisor</th-->
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
                                        <label class="control-label">Nombre Técnico</label>
                                        <p class="form-control-static" id="nombre-tecnico"></p>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Programa Presupuestario</label>
                                        <p class="form-control-static" id="programa-presupuestario"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label">Función</label>
                                        <p class="form-control-static" id="funcion"></p>
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <label class="control-label">Subfunción</label>
                                        <p class="form-control-static" id="subfuncion"></p>
                                    </div>
                                </div>
                                <input type="hidden" id="id" value="">
                            </div>
                        </div>
                    </div>
                    <table id="tabla-reportes" class="table table-condensed table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Tipo de Reporte</th>
                                @foreach($meses as $mes)
                                <th class="text-center" width="50">{{$mes['abrev']}}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Seguimiento de Metas</td>
                                @foreach($meses as $mes)
                                <td class="text-center reporte-boton" id="rep_metas_{{$mes['clave']}}">
                                    <span class="fa fa-times"></span>
                                </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td>Seguimiento de Metas Trimestral</td>
                                @foreach($meses as $mes)
                                <td class="text-center reporte-boton {{(($mes['clave']%3 != 0))?'text-muted':''}}" id="rep_metas_trim_{{$mes['clave']}}">
                                    <span class="fa fa-times"></span>
                                </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td>Seguimiento de Beneficiarios</td>
                                @foreach($meses as $mes)
                                <td class="text-center reporte-boton {{(($mes['clave']%3 != 0))?'text-muted':''}}" id="rep_benef_{{$mes['clave']}}">
                                    <span class="fa fa-times"></span>
                                </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td>Plan de Acción de Mejora</td>
                                @foreach($meses as $mes)
                                <td class="text-center reporte-boton {{(($mes['clave']%3 != 0))?'text-muted':''}}" id="rep_plan_{{$mes['clave']}}">
                                    <span class="fa fa-times"></span>
                                </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td>Cuenta Pública</td>
                                @foreach($meses as $mes)
                                <td class="text-center reporte-boton {{(($mes['clave']%3 != 0))?'text-muted':''}}" id="rep_cuenta_{{$mes['clave']}}">
                                    <span class="fa fa-times"></span>
                                </td>
                                @endforeach
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="13" class="text-right">
                                    <b>Estatus del Reporte: </b>
                                    <span class="btn btn-primary btn-xs"><span class="fa fa-check"></span></span> Registrado 
                                    <span class="btn btn-success btn-xs"><span class="fa fa-pencil"></span></span> Firmado
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<!-- Dejar parent al ultimo -->
@parent
@stop