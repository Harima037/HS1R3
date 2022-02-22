@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/rendicion-cuentas/inversion.js')}}"></script>
<script src="{{ URL::to('js/modulos/rendicion-cuentas/lista-proyectos-rendicion.js') }}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default datagrid" id="datagridProyectos" data-edit-row="cargar_datos_proyecto" data-trim-activo="{{$trimestre_avance}}" data-mes-activo="{{$mes_avance}}" data-mes-actual="{{$mes_actual}}">
            <div class="panel-heading"><h4><i class="fa {{ $sys_mod_activo->icono }}"></i> {{ $sys_mod_activo->nombre }} {{$anio_captura}}</h4></div>
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
                                <button type="button" class="btn btn-success btn-edit-rows" id="btn-detalles-proyecto">
                                    <span class="fa fa-edit"></span> Ver Detalles del Proyecto
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-striped table-hover table-condensed">
                <thead>
                    <tr height="50">
                        <th><input type="checkbox" class="check-select-all-rows"></th>
                        <th width="190">Clave Presupuestaria</th>
                        <th>Nombre Técnico</th>
                        @foreach ($meses as $mes)
                            <th width="30" class="{{ ($mes[0]['clave'] == $mes_actual)?'bg-info':'' }}"><p class="texto-vertical">{{$mes[0]['abrev']}} </p></th>
                            <th width="30" class="{{ ($mes[1]['clave'] == $mes_actual)?'bg-info':'' }}"><p class="texto-vertical">{{$mes[1]['abrev']}} </p></th>
                            <th width="30" class="{{ ($mes[2]['clave'] == $mes_actual)?'bg-info':'' }}"><p class="texto-vertical">{{$mes[2]['abrev']}} </p></th>
                        @endforeach
                        <th width="108">Estado</th>
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
                        <ul class="nav nav-pills" role="tablist">
                            @for($i = 1 ; $i <= 4 ; $i++)
                            <li role="presentation" class="{{($i == 1)?'active':''}}">
                                <a href="#panel-trim-{{$i}}" aria-controls="panel-trim-{{$i}}" role="tab" data-toggle="tab">
                                    <span class="fa {{($trimestre_avance == $i)?'fa-calendar-o':'fa-calendar'}}"></span> Trim {{$i}}
                                </a>
                            </li>
                            @endfor
                        </ul>
                        <div class="tab-content">
                            <br>
                            @for($trim = 1 ; $trim <= 4 ; $trim++)
                            <div role="tabpanel" class="tab-pane {{($trim == 1)?'active':''}}" id="panel-trim-{{$trim}}">
                                <div role="tabpanel">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li role="presentation" class="active">
                                            <a href="#panel-metas-trim-{{$trim}}" aria-controls="panel-metas-trim-{{$trim}}" role="tab" data-toggle="tab">
                                                <span class="fa fa-table"></span> Seguimiento de Metas
                                            </a>
                                        </li>
                                        <li role="presentation" >
                                            <a href="#panel-beneficiarios-trim-{{$trim}}" aria-controls="panel-beneficiarios-trim-{{$trim}}" role="tab" data-toggle="tab">
                                                <span class="fa fa-users"></span> Seguimiento de Beneficiarios
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane active" id="panel-metas-trim-{{$trim}}">
                                            <table id="avance-trim-{{$trim}}" class="table table-hover table-condensed table-stripped tabla-avance-trim">
                                                <thead>
                                                    <tr>
                                                        <th>Nivel</th>
                                                        <th>Indicador</th>
                                                        <th>{{$meses[$trim][0]['mes']}}</th>
                                                        <th>{{$meses[$trim][1]['mes']}}</th>
                                                        <th>{{$meses[$trim][2]['mes']}}</th>
                                                        <th class="bg-success">Totales</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                        <div role="tabpanel" class="tab-pane" id="panel-beneficiarios-trim-{{$trim}}">
                                            <div style="overflow-x:auto;">
                                                <table id='beneficiarios-trim-{{$trim}}' class="table table-stripped table-condensed table-hover tabla-avance-beneficiarios">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">Grupo</th>
                                                            <th rowspan="2">Descripción de Beneficiario</th>
                                                            <th rowspan="2">Total</th>
                                                            <th rowspan="2">Genero</th>
                                                            <th colspan="2">Zona</th>
                                                            <th colspan="4">Población</th>
                                                            <th colspan="5">Marginación</th>
                                                        </tr>
                                                        <tr>
                                                            <th>Urbana</th>
                                                            <th>Rural</th>
                                                            <th>Mestiza</th>
                                                            <th>Indigena</th>
                                                            <th>Inmigrante</th>
                                                            <th>Otros</th>
                                                            <th nowrap="nowrap">Muy alta</th>
                                                            <th>Alta</th>
                                                            <th>Media</th>
                                                            <th>Baja</th>
                                                            <th nowrap="nowrap">Muy baja</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div id="btn-reporte" class="pull-left">
                        <div class="btn-group">
                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="fa fa-file"></span> Imprimir Reporte <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="#" id="btn-reporte-general">
                                        <span class="fa fa-file-pdf-o"></span> Seguimiento de Metas
                                    </a>
                                </li>
                                @if(($mes_actual % 3) == 0)
                                <li>
                                    <a href="#" id="btn-reporte-trimestral">
                                        <span class="fa fa-file-pdf-o"></span> Seguimiento de Metas Trimestral
                                    </a>
                                </li>
                                <li>
                                    <a href="#" id="btn-reporte-beneficiarios">
                                        <span class="fa fa-file-pdf-o"></span> Seguimiento de Beneficiarios
                                    </a>
                                </li>
                                <li>
                                    <a href="#" id="btn-reporte-plan-mejora">
                                        <span class="fa fa-file-pdf-o"></span> Plan de Acción de Mejora
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#" id="btn-reporte-analisis">
                                        <span class="fa fa-file-pdf-o"></span> Cuenta Pública
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn-editar-avance">
                        <span class="fa fa-pencil"></span> Capturar Avance
                    </button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<!-- Dejar parent al ultimo -->
@parent
@stop