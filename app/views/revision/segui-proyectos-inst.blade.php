@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/revision/seguimiento-institucional.js')}}"></script>
<script src="{{ URL::to('js/modulos/expediente/detallesProyecto.js') }}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default datagrid" id="datagridProyectos" data-edit-row="cargar_datos_proyecto" data-trim-activo="{{$trimestre_avance}}" data-mes-activo="{{$mes_avance}}">
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
                                <button type="button" class="btn btn-info" id="btn-reporte-seguimiento">
                                    <span class="fa fa-file-excel-o"></span> Reporte Seguimiento
                                </button>
                                <button type="button" class="btn btn-success btn-edit-rows" id="btn-detalles-proyecto">
                                    <span class="glyphicon glyphicon-eye-open"></span> Ver Detalles del Proyecto
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
                        <th>Clave</th>
                        <th>Nombre Técnico</th>
                        @foreach ($meses as $mes)
                            <th width="30"><p class="texto-vertical">{{$mes[0]['abrev']}} </p></th>
                            <th width="30"><p class="texto-vertical">{{$mes[1]['abrev']}} </p></th>
                            <th width="30"><p class="texto-vertical">{{$mes[2]['abrev']}} </p></th>
                        @endforeach
                        <th width="100">Estado</th>
                        <th width="50"></th>
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
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#panel-metas" aria-controls="panel-metas" role="tab" data-toggle="tab">
                                    <span class="fa fa-table"></span> Seguimiento de Metas
                                </a>
                            </li>
                            <li role="presentation" class="pull-right">
                                <a href="#panel-informacion" aria-controls="panel-informacion" role="tab" data-toggle="tab">
                                    <span class="fa fa-info-circle"></span> Información
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane" id="panel-informacion">
                                <br>
                                <form id="form_fuente_informacion">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="control-label" for="fuente-informacion">
                                                    Fuente de la Información
                                                </label>
                                                <input type="text" class="form-control" id="fuente-informacion" name="fuente-informacion" />
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="control-label" for="responsable-informacion">
                                                    Responsable de la Información
                                                </label>
                                                <select class="form-control" id="responsable-informacion" name="responsable-informacion">
                                                    <option value="">Seleccione un responsable</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <button type="button" class="btn btn-success" id="btn-guardar-informacion">
                                                <span class="fa fa-save"></span> Guardar Información
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div role="tabpanel" class="tab-pane active" id="panel-metas">
                                <div role="tabpanel">
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-pills" role="tablist">
                                        @for($i = 1 ; $i <= 4 ; $i++)
                                        <li role="presentation" class="{{($i == 1)?'active':''}}">
                                            <a href="#trim{{$i}}" aria-controls="trim{{$i}}" role="tab" data-toggle="tab">
                                                <span class="fa fa-calendar"></span> Trim {{$i}}
                                            </a>
                                        </li>
                                        @endfor
                                    </ul>
                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                        @for($i = 1 ; $i <= 4 ; $i++)
                                        <div role="tabpanel" class="tab-pane {{($i == 1)?'active':''}}" id="trim{{$i}}">
                                            <table id="avance-trim-{{$i}}" class="table table-hover table-condensed table-stripped tabla-avance-trim">
                                                <thead>
                                                    <tr>
                                                        <th>Nivel</th>
                                                        <th>Indicador</th>
                                                        <th>{{$meses[$i][0]['mes']}}</th>
                                                        <th>{{$meses[$i][1]['mes']}}</th>
                                                        <th>{{$meses[$i][2]['mes']}}</th>
                                                        <th class="bg-success">Totales</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                	<button type="button" class="btn btn-success pull-left" id="btn-firmar">
                        <span class="fa fa-pencil"></span> Firmar Avance Mensual
                    </button>
                    
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btn-comentar-avance">Comentar Avances</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<!-- Dejar parent al ultimo -->
@parent
@stop