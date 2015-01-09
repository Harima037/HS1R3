@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/expediente/fibap.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default datagrid" id="datagridFibaps" data-edit-row="editar">
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
                                        <span class="glyphicon glyphicon-plus"></span> Agregar
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
                        <th>Proyecto</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Estatus</th>
                        <th style="text-align:center; width:90px;"><span class="glyphicon glyphicon-user"></span></th>
                        <th style="text-align:center; width:120px;"><span class="glyphicon glyphicon-calendar"></span></th>
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
    <div class="modal fade" id="modal-fibap" tabindex="-1" role="dialog" aria-labelledby="modalModuloLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modalModuloLabel">Nuevo</h4>
                </div>
                <div class="modal-body">
                    <form id="form-fibap">
                        <div class="form-group" id="opciones_fibap">
                            <div class="help-text">
                                Datos del proyecto a capturar:
                            </div>
                            <div class="well well-sm">
                                <div>
                                    <label>
                                        <input type="radio" name="proyecto-id" checked="checked" value="">
                                         <span class="fa fa-file-o"></span> Proyecto Nuevo
                                    </label>
                                </div>
                                <div id="lista-proyectos"></div>
                            </div>
                            <div>
                                <button type="button" class="btn btn-info btn-block" id="btn-cargar-proyectos">
                                    <span class="fa fa-upload"></span> Cargar proyectos de inversión existentes disponibles
                                </button>
                            </div>
                        </div>
                        <div class="form-group" id="editar_fibap">
                            <div role="tabpanel">
                            <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#datos-fibap" aria-controls="datos-fibap" role="tab" data-toggle="tab">
                                            <span class="fa fa-file-o"></span> FIBAP
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#datos-proyecto" aria-controls="datos-proyecto" role="tab" data-toggle="tab">
                                            <span class="fa fa-file"></span> Proyecto
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#antecedentes-fibap" aria-controls="antecedentes-fibap" role="tab" data-toggle="tab">
                                            <span class="fa fa-book"></span> Antecedentes
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#presupuesto-fibap" aria-controls="presupuesto-fibap" role="tab" data-toggle="tab">
                                            <span class="fa fa-dollar"></span> Presupuesto
                                        </a>
                                    </li>
                                </ul>
                            <!-- Tab panes -->
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane" id="datos-proyecto">
                                        <br>
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <span class="label label-default">Proyecto</span>
                                                <p id="lbl-proyecto"></p>
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="label label-default">Clave Presupuestaria</span>
                                                <p id="lbl-clave-presupuestaria"></p>
                                            </div>
                                            <div class="col-sm-12">
                                                <span class="label label-default">Programa Presupuestario</span>
                                                <p id="lbl-programa-presupuestario"></p>
                                            </div>
                                            <!--div class="col-sm-4">
                                                <button type="button" class="btn btn-info" id="btn-capturar-proyecto">
                                                    <span class="fa fa-file"></span> Caratula del Proyecto
                                                </button>
                                            </div-->
                                            <div class="col-sm-12">
                                                <span class="label label-default">Alineación al PED</span>
                                                <p id="lbl-alineacion-ped"></p>
                                            </div>
                                            <div class="col-sm-12">
                                                <span class="label label-default">Cobertura / Municipio</span>
                                                <p id="lbl-cobertura-municipio"></p>
                                            </div>
                                            <div class="col-sm-12">
                                                <table class="table table-condensed table-bordered">
                                                    <tr>
                                                        <th>Tipo Beneficiario</th>
                                                        <th>Femenino</th>
                                                        <th>Masculino</th>
                                                        <th>Total</th>
                                                    </tr>
                                                    <tr>
                                                        <td><span id="lbl-tipo-beneficiario"></span></td>
                                                        <td><span id="lbl-beneficiario-f"></span></td>
                                                        <td><span id="lbl-beneficiario-m"></span></td>
                                                        <td><span id="lbl-total-beneficiario"></span></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane active" id="datos-fibap">
                                        <br>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <span class="label label-default">Justificación del Proyecto</span>
                                                <p id="lbl-justificacion-proyecto"></p>
                                            </div>
                                            <div class="col-sm-6">
                                                <span class="label label-default">Descripción del Proyecto</span>
                                                <p id="lbl-descripcion-proyecto"></p>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="row panel panel-default">
                                                    <div class="col-sm-12 panel-heading">
                                                        <b>Alineación a los Objetivos de Desarrollo del Milenio</b>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="label label-default">Alienación Especifica</span>
                                                        <p id="lbl-alineacion-especifica"></p>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="label label-default">Alienación General</span>
                                                        <p id="lbl-alineacion-general"></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <span class="label label-default">Organismo Público</span>
                                                <p id="lbl-organismo-publico"></p>
                                            </div>
                                            <div class="col-sm-6">
                                                <span class="label label-default">Sector</span>
                                                <p id="lbl-sector"></p>
                                            </div>
                                            <div class="col-sm-6">
                                                <span class="label label-default">Subcomite</span>
                                                <p id="lbl-subcomite"></p>
                                            </div>
                                            <div class="col-sm-6">
                                                <span class="label label-default">Grupo de Trabajo</span>
                                                <p id="lbl-grupo-trabajo"></p>
                                            </div>
                                            <div class="col-sm-12">
                                                <span class="label label-default">Documentación de Soporte</span>
                                                <div class="row" id="lbl-lista-documentos"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="antecedentes-fibap">
                                        <br>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <span class="label label-default">Resultados Obtenidos</span>
                                                <p id="lbl-resultados-obtenidos"></p>
                                            </div>
                                            <div class="col-sm-6">
                                                <span class="label label-default">Resultados Esperados</span>
                                                <p id="lbl-resultados-esperados"></p>
                                            </div>
                                            <div class="col-sm-12">
                                                <br>
                                                <b>Antecedentes Financieros</b>
                                                <table class="table table-condensed table-bordered" id="tabla-antecedentes">
                                                    <thead>
                                                        <tr>
                                                            <th>Año</th>
                                                            <th>Autorizado</th>
                                                            <th>Ejercido</th>
                                                            <th>%</th>
                                                            <th>Fecha de Corte</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="presupuesto-fibap">
                                        <br>
                                        <div class="row">
                                            <div class="col-sm-7">
                                                <span class="label label-default">Periodo de Ejecuación</span>
                                                <p id="lbl-periodo-ejecucion"></p>
                                            </div>
                                            <div class="col-sm-5">
                                                <span class="label label-default">Presupuesto Requerido</span>
                                                <p id="lbl-presupuesto-requerido"></p>
                                            </div>
                                            <div class="col-sm-12">
                                                <b>Origenes</b>
                                                <div class="row" id="lbl-origen-financiamiento">
                                                    @foreach ($origenes_financiamiento as $origen)
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <span class="label label-default">{{$origen->descripcion}}</span>
                                                                <p class="valores-origenes" id="lbl-origen-{{$origen->id}}">0</p>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <br>
                                                <b>Distribución del Presupuesto Estatal</b>
                                                <table class="table table-condensed table-bordered" id="tabla-distribucion">
                                                    <thead>
                                                        <tr>
                                                            <th>Partida</th>
                                                            <th>Descripcion</th>
                                                            <th>Cantidad</th>
                                                            <th>%</th>                                                            
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="id" name="id">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary btn-guardar">Ir al Formulario</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<!-- Dejar parent al ultimo -->
@parent
@stop