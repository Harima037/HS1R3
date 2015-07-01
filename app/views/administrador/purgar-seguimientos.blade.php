@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/administrador/purgar-seguimientos.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default datagrid" id="datagridSeguimientos" data-edit-row="editar">
            <div class="panel-heading"><h4><i class="fa {{ $sys_mod_activo->icono }}"></i> {{ $sys_mod_activo->nombre }}</h4></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label" for="mes">Mes</label>
                            <select id="mes" name="mes" class="form-control">
                                <option value="">Selecciona un mes</option>
                                @foreach($meses as $id => $mes)
                                <option value="{{$id}}" {{($id==$mes_activo)?'selected':''}}>{{$mes}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label" for="clasificacion">Clasificación del Proyecto</label>
                            <select id="clasificacion" name="clasificacion" class="form-control">
                                <option value="">Selecciona una clasificación</option>
                                @foreach($clasificacion as $id => $clase)
                                <option value="{{$id}}">{{$clase}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">Buscar</label>
                            <input type="text" class="form-control txt-quick-search" placeholder="Texto a Buscar">
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <label class="control-label">Filtrar</label>
                        <button class="btn btn-primary btn-block btn-quick-search" type="button"><span class="fa fa-filter"></span></button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="btn-toolbar pull-right" >
                            <div class="btn-group" style="margin:5px">
                                <button type="button" class="btn btn-danger" id="btn-purgar-seguimiento">
                                    <span class="fa fa-eraser"></span> Purgar Seguimientos
                                </button>
                                <button type="button" class="btn btn-success btn-edit-rows">
                                    <span class="fa fa-edit"></span> Ver Detalles del Seguimiento
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
                        <th width="200">Clave Presupuestaria</th>
                        <th>Nombre Técnico</th>
                        <th width="150">Validación</th>
                        <th width="150">Enlace</th>
                        <th width="100">Estado</th>
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
    <div class="modal fade" id="modalSeguimiento" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-85-screen">
            <div class="modal-content modal-content-85-screen">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modalLabel">Nuevo</h4>
                </div>
                <div class="modal-body">
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
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#metas" aria-controls="metas" role="tab" data-toggle="tab">
                                <span class="fa fa-calendar"></span> Seguimiento de Metas
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#beneficiarios" aria-controls="beneficiarios" role="tab" data-toggle="tab">
                                <span class="fa fa-users"></span> Seguimiento de Beneficiarios
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#analisis-funcional" aria-controls="analisis-funcional" role="tab" data-toggle="tab">
                                <span class="fa fa-check-square"></span> Analisis Funcional
                            </a>
                        </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="metas">
                            <br>
                            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                              <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingOne">
                                  <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                      Componente 1
                                    </a>
                                    <a class="pull-right" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne-two" aria-expanded="false" aria-controls="collapseOne-two">
                                      Plan de Acción Mejora 
                                    </a>
                                  </h4>
                                </div>
                                <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                  <div class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <div class="form-group">
                                                <label>Indicador</label>
                                                <p id="indicador" class="form-control-static">asdfasdf</p>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Unidad de Medida</label>
                                                <p id="unidad-medida" class="form-control-static">asdfasdf</p>
                                            </div>
                                        </div>
                                    </div>
                                    <table id="tabla-avances-metas" class="table table-condensed table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" class="text-center">Jurisdicción</th>
                                                <th colspan="2" class="bg-success text-center">Meta Programada</th>
                                                <th colspan="3" class="bg-info text-center">Avance</th>
                                                <th rowspan="2" width="90" class="text-center">Porcentaje Acumulado</th>
                                            </tr>
                                            <tr>
                                                <th class="bg-success text-center">Acumulada</th>
                                                <th class="bg-success text-center" nowrap="nowrap">Mes actual</th>
                                                <th class="bg-info text-center" nowrap="nowrap">Mes actual</th>
                                                <th class="bg-info text-center">Acumulado</th>
                                                <th class="bg-info text-center">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr data-clave-jurisdiccion="OC">
                                                <td class="accion-municipio">
                                                    <span class=""></span> OC - Oficina Central
                                                </td>
                                                <td class="meta-programada bg-success" data-meta="0">0</td>
                                                <td class="meta-del-mes" data-meta-mes="0">0</td>
                                                <td>
                                                    <span class="avance-mes" id="avance_OC" data-jurisdiccion="OC" data-meta-programada="" ></span>
                                                </td>
                                                <td class="avance-acumulado" data-acumulado="0">0</td>
                                                <td class="avance-total bg-info" data-avance-total="0">0.00</td>
                                                <td class="avance-mes"><small class="text-success">0%</small></td>
                                            </tr>
                                                                                    <tr data-clave-jurisdiccion="01">
                                                <td class="accion-municipio">
                                                    <span class=""></span> 01 - TUXTLA GUTIERREZ
                                                </td>
                                                <td class="meta-programada bg-success" data-meta="113">113.00</td>
                                                <td class="meta-del-mes" data-meta-mes="45">45.00</td>
                                                <td>
                                                    <span class="avance-mes" id="avance_01" data-jurisdiccion="01" data-meta-programada="45"></span>
                                                </td>
                                                <td class="avance-acumulado" data-acumulado="0">0</td>
                                                <td class="avance-total bg-info" data-avance-total="0">0.00</td>
                                                <td class="avance-mes"><small class="text-danger"><span class="fa fa-arrow-down"></span> 0%</small></td>
                                            </tr>
                                                                                    <tr data-clave-jurisdiccion="02">
                                                <td class="accion-municipio">
                                                    <span class=""></span> 02 - SAN CRISTÓBAL DE LAS CASAS
                                                </td>
                                                <td class="meta-programada bg-success" data-meta="59">59.00</td>
                                                <td class="meta-del-mes" data-meta-mes="23">23.00</td>
                                                <td>
                                                    <span class="avance-mes" id="avance_02" data-jurisdiccion="02" data-meta-programada="23"></span>
                                                </td>
                                                <td class="avance-acumulado" data-acumulado="0">0</td>
                                                <td class="avance-total bg-info" data-avance-total="0">0.00</td>
                                                <td class="avance-mes"><small class="text-danger"><span class="fa fa-arrow-down"></span> 0%</small></td>
                                            </tr>
                                                                                    <tr data-clave-jurisdiccion="03">
                                                <td class="accion-municipio">
                                                    <span class=""></span> 03 - COMITÁN
                                                </td>
                                                <td class="meta-programada bg-success" data-meta="32">32.00</td>
                                                <td class="meta-del-mes" data-meta-mes="11">11.00</td>
                                                <td>
                                                    <span class="avance-mes" id="avance_03" data-jurisdiccion="03" data-meta-programada="11"></span>
                                                </td>
                                                <td class="avance-acumulado" data-acumulado="0">0</td>
                                                <td class="avance-total bg-info" data-avance-total="0">0.00</td>
                                                <td class="avance-mes"><small class="text-danger"><span class="fa fa-arrow-down"></span> 0%</small></td>
                                            </tr>
                                                                                    <tr data-clave-jurisdiccion="04">
                                                <td class="accion-municipio">
                                                    <span class=""></span> 04 - VILLAFLORES
                                                </td>
                                                <td class="meta-programada bg-success" data-meta="34">34.00</td>
                                                <td class="meta-del-mes" data-meta-mes="11">11.00</td>
                                                <td>
                                                    <span class="avance-mes" id="avance_04" data-jurisdiccion="04" data-meta-programada="11"></span>
                                                </td>
                                                <td class="avance-acumulado" data-acumulado="0">0</td>
                                                <td class="avance-total bg-info" data-avance-total="0">0.00</td>
                                                <td class="avance-mes"><small class="text-danger"><span class="fa fa-arrow-down"></span> 0%</small></td>
                                            </tr>
                                                                                    <tr data-clave-jurisdiccion="05">
                                                <td class="accion-municipio">
                                                    <span class=""></span> 05 - PICHUCALCO
                                                </td>
                                                <td class="meta-programada bg-success" data-meta="32">32.00</td>
                                                <td class="meta-del-mes" data-meta-mes="12">12.00</td>
                                                <td>
                                                    <span class="avance-mes" id="avance_05" data-jurisdiccion="05" data-meta-programada="12"></span>
                                                </td>
                                                <td class="avance-acumulado" data-acumulado="0">0</td>
                                                <td class="avance-total bg-info" data-avance-total="0">0.00</td>
                                                <td class="avance-mes"><small class="text-danger"><span class="fa fa-arrow-down"></span> 0%</small></td>
                                            </tr>
                                                                                    <tr data-clave-jurisdiccion="06">
                                                <td class="accion-municipio">
                                                    <span class=""></span> 06 - PALENQUE
                                                </td>
                                                <td class="meta-programada bg-success" data-meta="38">38.00</td>
                                                <td class="meta-del-mes" data-meta-mes="14">14.00</td>
                                                <td>
                                                    <span class="avance-mes" id="avance_06" data-jurisdiccion="06" data-meta-programada="14"></span>
                                                </td>
                                                <td class="avance-acumulado" data-acumulado="0">0</td>
                                                <td class="avance-total bg-info" data-avance-total="0">0.00</td>
                                                <td class="avance-mes"><small class="text-danger"><span class="fa fa-arrow-down"></span> 0%</small></td>
                                            </tr>
                                                                                    <tr data-clave-jurisdiccion="07">
                                                <td class="accion-municipio">
                                                    <span class=""></span> 07 - TAPACHULA
                                                </td>
                                                <td class="meta-programada bg-success" data-meta="60">60.00</td>
                                                <td class="meta-del-mes" data-meta-mes="25">25.00</td>
                                                <td>
                                                    <span class="avance-mes" id="avance_07" data-jurisdiccion="07" data-meta-programada="25"></span>
                                                </td>
                                                <td class="avance-acumulado" data-acumulado="0">0</td>
                                                <td class="avance-total bg-info" data-avance-total="0">0.00</td>
                                                <td class="avance-mes"><small class="text-danger"><span class="fa fa-arrow-down"></span> 0%</small></td>
                                            </tr>
                                                                                    <tr data-clave-jurisdiccion="08">
                                                <td class="accion-municipio">
                                                    <span class=""></span> 08 - TONALÁ
                                                </td>
                                                <td class="meta-programada bg-success" data-meta="24">24.00</td>
                                                <td class="meta-del-mes" data-meta-mes="8">8.00</td>
                                                <td>
                                                    <span class="avance-mes" id="avance_08" data-jurisdiccion="08" data-meta-programada="8"></span>
                                                </td>
                                                <td class="avance-acumulado" data-acumulado="0">0</td>
                                                <td class="avance-total bg-info" data-avance-total="0">0.00</td>
                                                <td class="avance-mes"><small class="text-danger"><span class="fa fa-arrow-down"></span> 0%</small></td>
                                            </tr>
                                                                                    <tr data-clave-jurisdiccion="09">
                                                <td class="accion-municipio">
                                                    <span class=""></span> 09 - OCOSINGO
                                                </td>
                                                <td class="meta-programada bg-success" data-meta="30">30.00</td>
                                                <td class="meta-del-mes" data-meta-mes="9">9.00</td>
                                                <td>
                                                    <span class="avance-mes" id="avance_09" data-jurisdiccion="09" data-meta-programada="9"></span>
                                                </td>
                                                <td class="avance-acumulado" data-acumulado="0">0</td>
                                                <td class="avance-total bg-info" data-avance-total="0">0.00</td>
                                                <td class="avance-mes"><small class="text-danger"><span class="fa fa-arrow-down"></span> 0%</small></td>
                                            </tr>
                                                                                    <tr data-clave-jurisdiccion="10">
                                                <td class="accion-municipio">
                                                    <span class=""></span> 10 - MOTOZINTLA
                                                </td>
                                                <td class="meta-programada bg-success" data-meta="24">24.00</td>
                                                <td class="meta-del-mes" data-meta-mes="8">8.00</td>
                                                <td>
                                                    <span class="avance-mes" id="avance_10" data-jurisdiccion="10" data-meta-programada="8"></span>
                                                </td>
                                                <td class="avance-acumulado" data-acumulado="0">0</td>
                                                <td class="avance-total bg-info" data-avance-total="0">0.00</td>
                                                <td class="avance-mes"><small class="text-danger"><span class="fa fa-arrow-down"></span> 0%</small></td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                            <th>Totales</th>
                                            <th class="bg-success" id="total-meta-programada" data-total-programado="446">446.00</th>
                                            <th id="total-meta-mes">166.00</th>
                                            <th id="total-avance-mes">0.00</th>
                                            <th id="total-avance-acumulado">0.00</th>
                                            <th class="bg-info" id="total-avance-total" data-total-avance="0">0.00</th>
                                            <th id="total-porcentaje" data-estado-avance="1"><small class="text-danger"><span class="fa fa-arrow-down"></span> 0%</small></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Analisis de Resultados Acumulados</label>
                                                <p id="analisis-resultados" class="form-control-static">asdfasdf</p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Justificacion Acumulada</label>
                                                <p id="justificacion" class="form-control-static">asdfasdf</p>
                                            </div>
                                        </div>
                                    </div>
                                  </div>
                                </div>
                                <div id="collapseOne-two" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                    <div class="panel-body">
                                        <br>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>Acción Mejora</label>
                                                    <p id="accion-mejora" class="form-control-static">asdfasdf</p>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>Grupo de Trabajo</label>
                                                    <p id="grupo-trabajo" class="form-control-static">asdfasdf</p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label>Fecha Inicio</label>
                                                    <p id="fecha-inicio" class="form-control-static">asdfasdf</p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label>Fecha de Termino</label>
                                                    <p id="fecha-termino" class="form-control-static">asdfasdf</p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label>Fecha de Notificación</label>
                                                    <p id="fecha-notificacion" class="form-control-static">asdfasdf</p>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>Documentacion Comprobatoria</label>
                                                    <p id="documentacion-comprobatoria" class="form-control-static">asdfasdf</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="panel-heading" role="tab" id="headingTwo">
                                  <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                      Actividad 1 . 1
                                    </a>
                                    <a class="pull-right" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo-two" aria-expanded="false" aria-controls="collapseTwo-two">
                                      Plan de Acción Mejora 
                                    </a>
                                  </h4>
                                </div>
                                <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                  <div class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <div class="form-group">
                                                <label>Indicador</label>
                                                <p id="indicador" class="form-control-static">asdfasdf</p>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label>Unidad de Medida</label>
                                                <p id="unidad-medida" class="form-control-static">asdfasdf</p>
                                            </div>
                                        </div>
                                    </div>
                                    <table id="tabla-avances-metas" class="table table-condensed table-bordered">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" class="text-center">Jurisdicción</th>
                                                <th colspan="2" class="bg-success text-center">Meta Programada</th>
                                                <th colspan="3" class="bg-info text-center">Avance</th>
                                                <th rowspan="2" width="90" class="text-center">Porcentaje Acumulado</th>
                                            </tr>
                                            <tr>
                                                <th class="bg-success text-center">Acumulada</th>
                                                <th class="bg-success text-center" nowrap="nowrap">Mes actual</th>
                                                <th class="bg-info text-center" nowrap="nowrap">Mes actual</th>
                                                <th class="bg-info text-center">Acumulado</th>
                                                <th class="bg-info text-center">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr data-clave-jurisdiccion="OC">
                                                <td class="accion-municipio">
                                                    <span class=""></span> OC - Oficina Central
                                                </td>
                                                <td class="meta-programada bg-success" data-meta="0">0</td>
                                                <td class="meta-del-mes" data-meta-mes="0">0</td>
                                                <td>
                                                    <span class="avance-mes" id="avance_OC" data-jurisdiccion="OC" data-meta-programada="" ></span>
                                                </td>
                                                <td class="avance-acumulado" data-acumulado="0">0</td>
                                                <td class="avance-total bg-info" data-avance-total="0">0.00</td>
                                                <td class="avance-mes"><small class="text-success">0%</small></td>
                                            </tr>
                                                                                    <tr data-clave-jurisdiccion="01">
                                                <td class="accion-municipio">
                                                    <span class=""></span> 01 - TUXTLA GUTIERREZ
                                                </td>
                                                <td class="meta-programada bg-success" data-meta="113">113.00</td>
                                                <td class="meta-del-mes" data-meta-mes="45">45.00</td>
                                                <td>
                                                    <span class="avance-mes" id="avance_01" data-jurisdiccion="01" data-meta-programada="45"></span>
                                                </td>
                                                <td class="avance-acumulado" data-acumulado="0">0</td>
                                                <td class="avance-total bg-info" data-avance-total="0">0.00</td>
                                                <td class="avance-mes"><small class="text-danger"><span class="fa fa-arrow-down"></span> 0%</small></td>
                                            </tr>
                                                                                    <tr data-clave-jurisdiccion="02">
                                                <td class="accion-municipio">
                                                    <span class=""></span> 02 - SAN CRISTÓBAL DE LAS CASAS
                                                </td>
                                                <td class="meta-programada bg-success" data-meta="59">59.00</td>
                                                <td class="meta-del-mes" data-meta-mes="23">23.00</td>
                                                <td>
                                                    <span class="avance-mes" id="avance_02" data-jurisdiccion="02" data-meta-programada="23"></span>
                                                </td>
                                                <td class="avance-acumulado" data-acumulado="0">0</td>
                                                <td class="avance-total bg-info" data-avance-total="0">0.00</td>
                                                <td class="avance-mes"><small class="text-danger"><span class="fa fa-arrow-down"></span> 0%</small></td>
                                            </tr>
                                                                                    <tr data-clave-jurisdiccion="03">
                                                <td class="accion-municipio">
                                                    <span class=""></span> 03 - COMITÁN
                                                </td>
                                                <td class="meta-programada bg-success" data-meta="32">32.00</td>
                                                <td class="meta-del-mes" data-meta-mes="11">11.00</td>
                                                <td>
                                                    <span class="avance-mes" id="avance_03" data-jurisdiccion="03" data-meta-programada="11"></span>
                                                </td>
                                                <td class="avance-acumulado" data-acumulado="0">0</td>
                                                <td class="avance-total bg-info" data-avance-total="0">0.00</td>
                                                <td class="avance-mes"><small class="text-danger"><span class="fa fa-arrow-down"></span> 0%</small></td>
                                            </tr>
                                                                                    <tr data-clave-jurisdiccion="04">
                                                <td class="accion-municipio">
                                                    <span class=""></span> 04 - VILLAFLORES
                                                </td>
                                                <td class="meta-programada bg-success" data-meta="34">34.00</td>
                                                <td class="meta-del-mes" data-meta-mes="11">11.00</td>
                                                <td>
                                                    <span class="avance-mes" id="avance_04" data-jurisdiccion="04" data-meta-programada="11"></span>
                                                </td>
                                                <td class="avance-acumulado" data-acumulado="0">0</td>
                                                <td class="avance-total bg-info" data-avance-total="0">0.00</td>
                                                <td class="avance-mes"><small class="text-danger"><span class="fa fa-arrow-down"></span> 0%</small></td>
                                            </tr>
                                                                                    <tr data-clave-jurisdiccion="05">
                                                <td class="accion-municipio">
                                                    <span class=""></span> 05 - PICHUCALCO
                                                </td>
                                                <td class="meta-programada bg-success" data-meta="32">32.00</td>
                                                <td class="meta-del-mes" data-meta-mes="12">12.00</td>
                                                <td>
                                                    <span class="avance-mes" id="avance_05" data-jurisdiccion="05" data-meta-programada="12"></span>
                                                </td>
                                                <td class="avance-acumulado" data-acumulado="0">0</td>
                                                <td class="avance-total bg-info" data-avance-total="0">0.00</td>
                                                <td class="avance-mes"><small class="text-danger"><span class="fa fa-arrow-down"></span> 0%</small></td>
                                            </tr>
                                                                                    <tr data-clave-jurisdiccion="06">
                                                <td class="accion-municipio">
                                                    <span class=""></span> 06 - PALENQUE
                                                </td>
                                                <td class="meta-programada bg-success" data-meta="38">38.00</td>
                                                <td class="meta-del-mes" data-meta-mes="14">14.00</td>
                                                <td>
                                                    <span class="avance-mes" id="avance_06" data-jurisdiccion="06" data-meta-programada="14"></span>
                                                </td>
                                                <td class="avance-acumulado" data-acumulado="0">0</td>
                                                <td class="avance-total bg-info" data-avance-total="0">0.00</td>
                                                <td class="avance-mes"><small class="text-danger"><span class="fa fa-arrow-down"></span> 0%</small></td>
                                            </tr>
                                                                                    <tr data-clave-jurisdiccion="07">
                                                <td class="accion-municipio">
                                                    <span class=""></span> 07 - TAPACHULA
                                                </td>
                                                <td class="meta-programada bg-success" data-meta="60">60.00</td>
                                                <td class="meta-del-mes" data-meta-mes="25">25.00</td>
                                                <td>
                                                    <span class="avance-mes" id="avance_07" data-jurisdiccion="07" data-meta-programada="25"></span>
                                                </td>
                                                <td class="avance-acumulado" data-acumulado="0">0</td>
                                                <td class="avance-total bg-info" data-avance-total="0">0.00</td>
                                                <td class="avance-mes"><small class="text-danger"><span class="fa fa-arrow-down"></span> 0%</small></td>
                                            </tr>
                                                                                    <tr data-clave-jurisdiccion="08">
                                                <td class="accion-municipio">
                                                    <span class=""></span> 08 - TONALÁ
                                                </td>
                                                <td class="meta-programada bg-success" data-meta="24">24.00</td>
                                                <td class="meta-del-mes" data-meta-mes="8">8.00</td>
                                                <td>
                                                    <span class="avance-mes" id="avance_08" data-jurisdiccion="08" data-meta-programada="8"></span>
                                                </td>
                                                <td class="avance-acumulado" data-acumulado="0">0</td>
                                                <td class="avance-total bg-info" data-avance-total="0">0.00</td>
                                                <td class="avance-mes"><small class="text-danger"><span class="fa fa-arrow-down"></span> 0%</small></td>
                                            </tr>
                                                                                    <tr data-clave-jurisdiccion="09">
                                                <td class="accion-municipio">
                                                    <span class=""></span> 09 - OCOSINGO
                                                </td>
                                                <td class="meta-programada bg-success" data-meta="30">30.00</td>
                                                <td class="meta-del-mes" data-meta-mes="9">9.00</td>
                                                <td>
                                                    <span class="avance-mes" id="avance_09" data-jurisdiccion="09" data-meta-programada="9"></span>
                                                </td>
                                                <td class="avance-acumulado" data-acumulado="0">0</td>
                                                <td class="avance-total bg-info" data-avance-total="0">0.00</td>
                                                <td class="avance-mes"><small class="text-danger"><span class="fa fa-arrow-down"></span> 0%</small></td>
                                            </tr>
                                                                                    <tr data-clave-jurisdiccion="10">
                                                <td class="accion-municipio">
                                                    <span class=""></span> 10 - MOTOZINTLA
                                                </td>
                                                <td class="meta-programada bg-success" data-meta="24">24.00</td>
                                                <td class="meta-del-mes" data-meta-mes="8">8.00</td>
                                                <td>
                                                    <span class="avance-mes" id="avance_10" data-jurisdiccion="10" data-meta-programada="8"></span>
                                                </td>
                                                <td class="avance-acumulado" data-acumulado="0">0</td>
                                                <td class="avance-total bg-info" data-avance-total="0">0.00</td>
                                                <td class="avance-mes"><small class="text-danger"><span class="fa fa-arrow-down"></span> 0%</small></td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                            <th>Totales</th>
                                            <th class="bg-success" id="total-meta-programada" data-total-programado="446">446.00</th>
                                            <th id="total-meta-mes">166.00</th>
                                            <th id="total-avance-mes">0.00</th>
                                            <th id="total-avance-acumulado">0.00</th>
                                            <th class="bg-info" id="total-avance-total" data-total-avance="0">0.00</th>
                                            <th id="total-porcentaje" data-estado-avance="1"><small class="text-danger"><span class="fa fa-arrow-down"></span> 0%</small></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Analisis de Resultados Acumulados</label>
                                                <p id="analisis-resultados" class="form-control-static">asdfasdf</p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Justificacion Acumulada</label>
                                                <p id="justificacion" class="form-control-static">asdfasdf</p>
                                            </div>
                                        </div>
                                    </div>
                                  </div>
                                </div>
                                <div id="collapseTwo-two" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                    <div class="panel-body">
                                        <br>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>Acción Mejora</label>
                                                    <p id="accion-mejora" class="form-control-static">asdfasdf</p>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>Grupo de Trabajo</label>
                                                    <p id="grupo-trabajo" class="form-control-static">asdfasdf</p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label>Fecha Inicio</label>
                                                    <p id="fecha-inicio" class="form-control-static">asdfasdf</p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label>Fecha de Termino</label>
                                                    <p id="fecha-termino" class="form-control-static">asdfasdf</p>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label>Fecha de Notificación</label>
                                                    <p id="fecha-notificacion" class="form-control-static">asdfasdf</p>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label>Documentacion Comprobatoria</label>
                                                    <p id="documentacion-comprobatoria" class="form-control-static">asdfasdf</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                              <!--/div>
                              <div class="panel panel-default"-->
                                <div class="panel-heading" role="tab" id="headingTwo">
                                  <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                      Collapsible Group Item #2
                                    </a>
                                  </h4>
                                </div>
                                <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                  <div class="panel-body">
                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                                  </div>
                                </div>
                              <!--/div>
                              <div class="panel panel-default"-->
                                <div class="panel-heading" role="tab" id="headingThree">
                                  <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                      Collapsible Group Item #3
                                    </a>
                                  </h4>
                                </div>
                                <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                                  <div class="panel-body">
                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                                  </div>
                                </div>
                              </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="beneficiarios">
                            <br>
                            <div id="datos-beneficiarios" style="overflow-x:auto;">
                                <table id="tabla_beneficiarios" class="table table-stripped table-condensed table-bordered">
                                    <thead>
                                        <tr>
                                            <!--th nowrap="nowrap" rowspan="2">Descripción de Beneficiario</th-->
                                            <th rowspan="2"></th>
                                            <th colspan="2">Zona</th>
                                            <th colspan="4" style="background-color:#DDDDDD;">Población</th>
                                            <th colspan="5">Marginación</th>
                                            <th rowspan="2">Total</th>
                                        </tr>
                                        <tr>
                                            <th>Urbana</th>
                                            <th>Rural</th>
                                            <th  style="background-color:#DDDDDD;">Mestiza</th>
                                            <th  style="background-color:#DDDDDD;">Indigena</th>
                                            <th  style="background-color:#DDDDDD;">Inmigrante</th>
                                            <th  style="background-color:#DDDDDD;">Otros</th>
                                            <th nowrap="nowrap" class="bg-success">Muy alta</th>
                                            <th class="bg-success">Alta</th>
                                            <th class="bg-success">Media</th>
                                            <th class="bg-success">Baja</th>
                                            <th nowrap="nowrap" class="bg-success">Muy baja</th>
                                        </tr>
                                    </thead>
                                    <tbody><tr class="bg-primary"><th colspan="12">Niño</th><th>85,598</th></tr><tr><th><span class="fa fa-female"></span></th><td>13,707</td><td>28,236</td><td class="bg-info">31,323</td><td class="bg-info">10,620</td><td class="bg-info">0</td><td class="bg-info">0</td><td class="bg-success">9,915</td><td class="bg-success">20,057</td><td class="bg-success">5,977</td><td class="bg-success">1,397</td><td class="bg-success">4,597</td><td>41,943</td></tr><tr><th><span class="fa fa-male"></span></th><td>14,266</td><td>29,389</td><td class="bg-info">32,615</td><td class="bg-info">11,040</td><td class="bg-info">0</td><td class="bg-info">0</td><td class="bg-success">10,189</td><td class="bg-success">20,448</td><td class="bg-success">6,409</td><td class="bg-success">1,537</td><td class="bg-success">5,072</td><td>43,655</td></tr><tr class="bg-primary"><th colspan="12">Persona</th><th>168,279</th></tr><tr><th><span class="fa fa-female"></span></th><td>28,047</td><td>57,775</td><td class="bg-info">64,092</td><td class="bg-info">21,730</td><td class="bg-info">0</td><td class="bg-info">0</td><td class="bg-success">20,288</td><td class="bg-success">41,040</td><td class="bg-success">12,230</td><td class="bg-success">2,858</td><td class="bg-success">9,406</td><td>85,822</td></tr><tr><th><span class="fa fa-male"></span></th><td>26,947</td><td>55,510</td><td class="bg-info">61,604</td><td class="bg-info">20,853</td><td class="bg-info">0</td><td class="bg-info">0</td><td class="bg-success">19,245</td><td class="bg-success">38,623</td><td class="bg-success">12,105</td><td class="bg-success">2,902</td><td class="bg-success">9,582</td><td>82,457</td></tr></tbody>
                                </table>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="analisis-funcional">
                            <br>
                            <div class="form-group">
                                <label>Finalidad del Proyecto</label>
                                <p id="finalidad-proyecto" class="form-control-static">asdfasdf</p>
                            </div>
                            <div class="form-group">
                                <label>Analisis de Resultado</label>
                                <p id="analisis-resultados" class="form-control-static">asdfasdf</p>
                            </div>
                            <div class="form-group">
                                <label>Beneficiarios</label>
                                <p id="beneficiarios" class="form-control-static">asdfasdf</p>
                            </div>
                            <div class="form-group">
                                <label>Justificación Global del Proyecto</label>
                                <p id="justificacion-global" class="form-control-static">asdfasdf</p>
                            </div>
                        </div>
                    </div>
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