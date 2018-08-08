@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/administrador/admin-proyectos.js')}}"></script>
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
                            <div class="btn-group" style="margin:5px">
                                <button type="button" class="btn btn-success btn-datagrid-agregar btn-edit-rows">
                                    <span class="fa fa-edit"></span> Cambiar Estatus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="check-select-all-rows"></th>
                        <th width="190px">Clave</th>
                        <th>Nombre Técnico</th>
                        <th style="width:120px;">Estatus</th>
                        <th style="width:75px;">Avances</th>
                        <th style="text-align:center; width:85px;"><span class="fa fa-user"></span></th>
                        <th style="text-align:center; width:120px;"><span class="fa fa-calendar"></span></th>
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
<div class="modal fade" id="modalEditarProyecto" tabindex="-1" role="dialog" aria-labelledby="modalProyectoLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalProyectoLabel">Nuevo</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <label class="control-label">Clave Presupuestaria</label>
                        <p class="form-control-static" id="clave-presupuestaria"></p>
                    </div>
                    <div class="col-sm-12">
                        <label class="control-label">Nombre Técnico</label>
                        <p class="form-control-static" id="nombre-tecnico"></p>
                    </div>
                    <form action="" id="form_proyecto">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="estatus-proyecto" class="control-label">Estatus</label>
                                <select id="estatus-proyecto" name="estatus-proyecto" class="form-control">
                                    @foreach($estatus_proyectos as $estatus)
                                        <option value="{{$estatus->id}}">{{$estatus->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                    <input type="hidden" id="id" value="">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-guardar">Guardar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

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
                                    <p class="form-control-static" id="nombre-proyecto"></p>
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
                            <input type="hidden" id="proyecto-id" value="">
                        </div>
                    </div>
                </div>
                <table id="tabla-reportes" class="table table-condensed table-hover table-striped">
                    <tbody>
                        @for($i = 0 ; $i < 6 ; $i++)
                        <tr>
                            <th class="text-center">{{$meses[$i]['abrev']}}</th>
                            <td class="text-center" id="rep_metas_{{$meses[$i]['clave']}}">
                                <select id="estatus-avance-{{$meses[$i]['clave']}}" name="estatus-avance-{{$meses[$i]['clave']}}" class="form-control estatus-avance" onChange="poner_estatus({{$meses[$i]['clave']}})">
                                    <option value="0">Sin capturar</option>
                                @foreach($estatus_proyectos as $estatus)
                                    <option value="{{$estatus->id}}">{{$estatus->descripcion}}</option>
                                @endforeach
                                </select>
                            </td>
                            <th class="text-center">{{$meses[$i+6]['abrev']}}</th>
                            <td class="text-center" id="rep_metas_{{$meses[$i+6]['clave']}}">
                                <select id="estatus-avance-{{$meses[$i+6]['clave']}}" name="estatus-avance-{{$meses[$i+6]['clave']}}" class="form-control estatus-avance" onChange="poner_estatus({{$meses[$i+6]['clave']}})">
                                    <option value="0">Sin capturar</option>
                                @foreach($estatus_proyectos as $estatus)
                                    <option value="{{$estatus->id}}">{{$estatus->descripcion}}</option>
                                @endforeach
                                </select>
                            </td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-cambiar-estatus-avance">Guardar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@parent
@stop