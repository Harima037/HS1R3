@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/visor/inversion.js')}}"></script>
<script src="{{ URL::to('js/modulos/visor/lista-proyectos.js') }}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default datagrid" id="datagridProyectos" data-edit-row="cargar_datos_proyecto" data-trim-activo="{{$trimestre_avance}}" data-mes-activo="{{$mes_avance}}" data-mes-actual="{{$mes_actual}}">
            <div class="panel-heading"><h4><i class="fa {{ $sys_mod_activo->icono }}"></i> Proyectos de Inversión</h4></div>
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
                            <th width="30" class="{{ ($mes['clave'] == $mes_actual)?'bg-info':'' }}">
                                <p class="texto-vertical">{{$mes['abrev']}}</p>
                            </th>
                        @endforeach
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
@parent
@stop