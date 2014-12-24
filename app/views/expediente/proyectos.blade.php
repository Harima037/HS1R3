@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/expediente/proyectos.js')}}"></script>
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
                        <th style="text-align:center; width:85px;"><span class="glyphicon glyphicon-user"></span></th>
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
    <div class="modal fade" id="modalCaratulas" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-75-screen">
            <div class="modal-content modal-content-75-screen">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modalLabel">Nuevo</h4>
                </div>
                <div class="modal-body">
                    <div class="row" id="datos-formulario">
                        <form action="" id="form_caratula">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="tipo_proyecto" class="control-label">Tipo de proyecto</label>
                                    {{Form::select('tipo_proyecto',$tipos_proyectos->lists('descripcion','id'),0,array('class'=>'form-control','id'=>'tipo_proyecto'))}}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="clasificacion_proyecto" class="control-label">Clase de proyecto a capturar</label>
                                    {{Form::select('clasificacion_proyecto',$clasificacion_proyectos->lists('descripcion','id'),0,array('class'=>'form-control','id'=>'clasificacion_proyecto'))}}
                                </div>
                            </div>
                            
                            <div class="col-sm-12">
                                <div class="form-group hidden" id="opciones_fibap">
                                    <div class="help-text">
                                        Para poder generar el proyecto de inversión es necesaria la Ficha de Información Básica del Proyecto (FIBAP). Puede seleccionar una dando click en el siguiente boton.
                                        <br>
                                        <button type="button" id="btn-seleccionar-fibap" class="btn btn-info btn-block">
                                            <span class="fa fa-file"></span> Seleccionar FIBAP
                                        </button>
                                        <br>
                                        Si desea utilizar una nueva FIBAP debe hacer click en el siguiente enlace para iniciar la captura.
                                        <br>
                                        <button type="button" id="btn-capturar-fibap" class="btn btn-link btn-block">
                                            <span class="fa fa-file"></span> Ir al Formulario de Captura de la FIBAP
                                        </button>
                                        <br>
                                        También se puede capturar después de capturar los datos del proyecto de inversión, solo debe hacer click en el boton "Ir a la caratula de captura", para continuar con la captura de los datos del proyecto de Inversión.
                                    </div>
                                </div>
                                <div class="well well-sm hidden" id="lista_fibap"></div>
                            </div>
                            <input type="hidden" id="id" name="id">
                        </form>
                    </div>
                    <div id="datos-proyecto">
                        <div role="tabpanel">
                        <!-- Nav tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#tab-proyecto" aria-controls="tab-proyecto" role="tab" data-toggle="tab">
                                        <span class="fa fa-file"></span> Proyecto
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#tab-beneficiarios" aria-controls="tab-componente" role="tab" data-toggle="tab">
                                        <span class="fa fa-group"></span> Beneficiarios
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#tab-componente" aria-controls="tab-componente" role="tab" data-toggle="tab">
                                        <span class="fa fa-cogs"></span> Componentes
                                    </a>
                                </li>
                            </ul>

                        <!-- Tab panes -->
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="tab-proyecto">
                                    <br>
                                    <div class="row">
                                        <div class="col-sm-5 col-xs-12">
                                            <label class="control-label">Nombre Técnico</label>
                                            <p id="lbl_nombre_tecnico"></p>
                                        </div>
                                        <div class="col-sm-4 col-xs-6">
                                            <label class="control-label">Cobertura</label>
                                            <p id="lbl_cobertura"></p>
                                        </div>
                                        <div class="col-sm-3 col-xs-6">
                                            <label class="control-label">Tipo de Acción</label>
                                            <p id="lbl_tipo_accion"></p>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-sm-12">
                                            <div class="panel panel-primary">
                                                <div class="panel-heading">
                                                    <span id="lbl_clave_presupuestaria" class="form-control-static"></span>
                                                    <button type="button" class="btn btn-info pull-right" data-toggle="collapse" data-target="#clave-desgloce" aria-expanded="true" aria-controls="clave-desgloce">
                                                        Desgloce de la clave <span class="fa fa-toggle-down"></span>
                                                    </button>
                                                </div>
                                                <div id="clave-desgloce" class="panel-body collapse">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <label class="control-label">Unidad Responsable</label>
                                                            <p id="lbl_unidad_responsable"></p>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <label class="control-label">Finalidad/Función/SubFunción/SubSubFunción</label>
                                                            <ul>
                                                                <li>
                                                                    <p id="lbl_finalidad"></p>
                                                                    <ul>
                                                                        <li>
                                                                            <p id="lbl_funcion"></p>
                                                                            <ul>
                                                                                <li>
                                                                                    <p id="lbl_sub_funcion"></p>
                                                                                    <ul>
                                                                                        <li>
                                                                                            <p id="lbl_sub_sub_funcion"></p>
                                                                                        </li>
                                                                                    </ul>
                                                                                </li>
                                                                            </ul>
                                                                        </li>
                                                                    </ul>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <label class="control-label">Programa Sectorial</label>
                                                            <p id="lbl_programa_sectorial"></p>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <label class="control-label">Programa Presupuestario</label>
                                                            <p id="lbl_programa_presupuestario"></p>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="control-label">Programa Especial</label>
                                                            <p id="lbl_programa_especial"></p>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="control-label">Actividad Institucional</label>
                                                            <p id="lbl_actividad_institucional"></p>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="control-label">Proyecto Estratégico</label>
                                                            <p id="lbl_proyecto_estrategico"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <label class="control-label">Vinculacion al PED</label>
                                            <p id="lbl_vinculacion_ped"></p>
                                        </div>

                                        <div class="col-sm-6">
                                            <label class="control-label">Lider del Proyecto</label>
                                            <p id="lbl_lider_proyecto"></p>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="control-label">Jefe Inmediato al Lider</label>
                                            <p id="lbl_jefe_lider"></p>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="control-label">Jefe de Planeación</label>
                                            <p id="lbl_jefe_ṕlaneacion"></p>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="control-label">Coordinador del Grupo Estratégico</label>
                                            <p id="lbl_coordinador_grupo"></p>
                                        </div>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="tab-beneficiarios">
                                    <br>
                                    <div id="datos-beneficiarios">
                                        <table class="table table-condensed table-bordered">
                                            <tr>
                                                <th width="1" style="white-space:nowrap;">Descripción de Beneficiario</th>
                                                <th>Total</th>
                                                <th colspan="2">Por Genero</th>
                                            </tr>
                                            <tr>
                                                <td rowspan="2"><p id="lbl_tipo_beneficiario" class="form-control-static"></p></td>
                                                <td rowspan="2"><p id="lbl_total_beneficiarios"></p></td>
                                                <td width="1"><span class="fa fa-female fa-2x"></span></td>
                                                <td><p class="form-control-static" id="lbl_beneficiarios_f"></p></td>
                                            </tr>
                                            <tr>
                                                <td><span class="fa fa-male fa-2x"></span></td>
                                                <td><p class="form-control-static" id="lbl_beneficiarios_m"></p></td>
                                            </tr>
                                        </table>

                                        <div role="tabpanel">
                                            <!-- Nav tabs -->
                                            <ul class="nav nav-pills" role="tablist">
                                                <li role="presentation" class="active">
                                                    <a href="#benef-zona" aria-controls="benef-zona" role="tab" data-toggle="pill">
                                                        Zona
                                                    </a>
                                                </li>
                                                <li role="presentation">
                                                    <a href="#benef-pob" aria-controls="benef-pob" role="tab" data-toggle="pill">
                                                        Población
                                                    </a>
                                                </li>
                                                <li role="presentation">
                                                    <a href="#benef-marg" aria-controls="benef-marg" role="tab" data-toggle="pill">
                                                        Marginación
                                                    </a>
                                                </li>
                                            </ul>
                                            <br>
                                            <!-- Tab panes -->
                                            <div class="tab-content">
                                                <div role="tabpanel" class="tab-pane active" id="benef-zona">
                                                    <table class="table table-condensed table-bordered">
                                                        <tr>
                                                            <th width="1">Generos</th>
                                                            <th>Urbana</th>
                                                            <th>Rural</th>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="fa fa-female fa-2x"></span></td>
                                                            <td><p class="form-control-static" id="lbl_benef_urbana_f"></p></td>
                                                            <td><p class="form-control-static" id="lbl_benef_rural_f"></p></td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="fa fa-male fa-2x"></span></td>
                                                            <td><p class="form-control-static" id="lbl_benef_urbana_m"></p></td>
                                                            <td><p class="form-control-static" id="lbl_benef_rural_m"></p></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div role="tabpanel" class="tab-pane" id="benef-pob">
                                                    <table class="table table-condensed table-bordered">
                                                        <tr>
                                                            <th width="1">Generos</th>
                                                            <th>Mestiza</th>
                                                            <th>Indigena</th>
                                                            <th>Inmigrante</th>
                                                            <th>Otros</th>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="fa fa-female fa-2x"></span></td>
                                                            <td><p class="form-control-static" id="lbl_benef_mestiza_f"></p></td>
                                                            <td><p class="form-control-static" id="lbl_benef_indigena_f"></p></td>
                                                            <td><p class="form-control-static" id="lbl_benef_inmigrante_f"></p></td>
                                                            <td><p class="form-control-static" id="lbl_benef_otros_f"></p></td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="fa fa-male fa-2x"></span></td>
                                                            <td><p class="form-control-static" id="lbl_benef_mestiza_m"></p></td>
                                                            <td><p class="form-control-static" id="lbl_benef_indigena_m"></p></td>
                                                            <td><p class="form-control-static" id="lbl_benef_inmigrante_m"></p></td>
                                                            <td><p class="form-control-static" id="lbl_benef_otros_m"></p></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div role="tabpanel" class="tab-pane" id="benef-marg">
                                                    <table class="table table-condensed table-bordered">
                                                        <tr>
                                                            <th width="1">Generos</th>
                                                            <th>Muy Alta</th>
                                                            <th>Alta</th>
                                                            <th>Media</th>
                                                            <th>Baja</th>
                                                            <th>Muy Baja</th>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="fa fa-female fa-2x"></span></td>
                                                            <td><p class="form-control-static" id="lbl_benef_muy_alta_f"></p></td>
                                                            <td><p class="form-control-static" id="lbl_benef_alta_f"></p></td>
                                                            <td><p class="form-control-static" id="lbl_benef_media_f"></p></td>
                                                            <td><p class="form-control-static" id="lbl_benef_baja_f"></p></td>
                                                            <td><p class="form-control-static" id="lbl_benef_muy_baja_f"></p></td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="fa fa-male fa-2x"></span></td>
                                                            <td><p class="form-control-static" id="lbl_benef_muy_alta_m"></p></td>
                                                            <td><p class="form-control-static" id="lbl_benef_alta_m"></p></td>
                                                            <td><p class="form-control-static" id="lbl_benef_media_m"></p></td>
                                                            <td><p class="form-control-static" id="lbl_benef_baja_m"></p></td>
                                                            <td><p class="form-control-static" id="lbl_benef_muy_baja_m"></p></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="tab-componente"></div>
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