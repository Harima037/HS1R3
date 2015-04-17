<div class="modal fade" id="modalDetalleProyecto" tabindex="-1" role="dialog" aria-labelledby="modalDetalleProyectoLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalDetalleProyectoLabel">Nuevo</h4>
            </div>
            <div class="modal-body">
                <div role="tabpanel">
                <!-- Nav tabs -->
                    <ul id="proyecto-tab-panel-list" class="nav nav-tabs" role="tablist">
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
                        <li role="presentation" class="hidden">
                            <a id="tab-link-fibap" href="#tab-fibap" aria-controls="tab-fibap" role="tab" data-toggle="tab">
                                <span class="fa fa-file-o"></span> FIBAP
                            </a>
                        </li>
                    </ul>

                <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tab-proyecto">
                            <br>
                            <div class="row">
                                <div class="col-sm-5 col-xs-12">
                                    <!--label class="control-label"></label-->
                                    <span class="label label-default"><b>Nombre Técnico</b></span>
                                    <p id="lbl_nombre_tecnico"></p>
                                </div>
                                <div class="col-sm-4 col-xs-6">
                                    <!--label class="control-label"></label-->
                                    <span class="label label-default"><b>Cobertura</b></span>
                                    <p id="lbl_cobertura"></p>
                                </div>
                                <div class="col-sm-3 col-xs-6">
                                    <!--label class="control-label"></label-->
                                    <span class="label label-default"><b>Tipo de Acción</b></span>
                                    <p id="lbl_tipo_accion"></p>
                                </div>
                                <div class="clearfix"></div>
                                <div class="col-sm-12">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading">
                                            <b>Clave:</b> 
                                            [<span id="lbl_clave_presupuestaria" class="form-control-static"></span>]
                                            <button type="button" class="btn btn-info pull-right" data-toggle="collapse" data-target="#clave-desgloce" aria-expanded="true" aria-controls="clave-desgloce">
                                                Desgloce de la clave <span class="fa fa-toggle-down"></span>
                                            </button>
                                        </div>
                                        <div id="clave-desgloce" class="panel-body collapse">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <!--label class="control-label"></label-->
                                                    <span class="label label-default"><b>Unidad Responsable</b></span>
                                                    <p id="lbl_unidad_responsable"></p>
                                                </div>
                                                <div class="col-sm-12">
                                                    <!--label class="control-label"></label-->
                                                    <span class="label label-default"> 
                                                        <b>Finalidad / Función / SubFunción / SubSubFunción</b>
                                                    </span>
                                                    <p>
                                                        <span id="lbl_finalidad"></span><br>
                                                        <span id="lbl_funcion"></span><br>
                                                        <span id="lbl_sub_funcion"></span><br>
                                                        <span id="lbl_sub_sub_funcion"></span>
                                                    </p>
                                                </div>
                                                <div class="col-sm-4">
                                                    <!--label class="control-label"></label-->
                                                    <span class="label label-default"><b>Programa Sectorial</b></span>
                                                    <p id="lbl_programa_sectorial"></p>
                                                </div>
                                                <div class="col-sm-8">
                                                    <!--label class="control-label"></label-->
                                                    <span class="label label-default"><b>Programa Presupuestario</b></span>
                                                    <p id="lbl_programa_presupuestario"></p>
                                                </div>
                                                <div class="col-sm-4">
                                                    <!--label class="control-label"></label-->
                                                    <span class="label label-default"><b>Programa Especial</b></span>
                                                    <p id="lbl_programa_especial"></p>
                                                </div>
                                                <div class="col-sm-5">
                                                    <!--label class="control-label"></label-->
                                                    <span class="label label-default"><b>Actividad Institucional</b></span>
                                                    <p id="lbl_actividad_institucional"></p>
                                                </div>
                                                <div class="col-sm-3">
                                                    <!--label class="control-label"></label-->
                                                    <span class="label label-default"><b>Proyecto Estratégico</b></span>
                                                    <p id="lbl_proyecto_estrategico"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <!--label class="control-label"></label-->
                                    <span class="label label-default"><b>Vinculacion al PED</b></span>
                                    <p id="lbl_vinculacion_ped"></p>
                                </div>

                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <!--label class="control-label"></label-->
                                    <span class="label label-default"><b>Lider del Proyecto</b></span>
                                    <p id="lbl_lider_proyecto"></p>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <!--label class="control-label"></label-->
                                    <span class="label label-default"><b>Jefe Inmediato al Lider</b></span>
                                    <p id="lbl_jefe_lider"></p>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <!--label class="control-label"></label-->
                                    <span class="label label-default"><b>Jefe de Planeación</b></span>
                                    <p id="lbl_jefe_ṕlaneacion"></p>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <!--label class="control-label"></label-->
                                    <span class="label label-default"><b>Coordinador del Grupo Estratégico</b></span>
                                    <p id="lbl_coordinador_grupo"></p>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="tab-beneficiarios">
                            <br>
                            <div id="datos-beneficiarios" style="overflow-x:auto;">
                                <table id='tabla_beneficiarios' class="table table-stripped table-condensed table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th nowrap="nowrap" rowspan="2">Descripción de Beneficiario</th>
                                            <th colspan="2" rowspan="2">Total</th>
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
                        <div role="tabpanel" class="tab-pane" id="tab-componente"></div>
                        <div role="tabpanel" class="tab-pane" id="tab-fibap">
                            <br>
                            <div id="datos-alerta-fibap">
                                <div class="alert alert-info">No ha sido asignada ningun FIBAP a este proyecto.</div>
                            </div>
                            <div class="row" id="datos-capturados-fibap">
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
                                    <div role="tabpanel">
                                    <!-- Nav tabs -->
                                        <ul class="nav nav-pills" role="tablist">
                                            <li role="presentation" class="active">
                                                <a href="#fibap-doc-soporte" aria-controls="fibap-doc-soporte" role="tab" data-toggle="tab">
                                                    Documentos Soporte
                                                </a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#fibap-antecedentes" aria-controls="fibap-antecedentes" role="tab" data-toggle="tab">
                                                    Antecedentes
                                                </a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#fibap-presupuesto" aria-controls="fibap-presupuesto" role="tab" data-toggle="tab">
                                                    Presupuesto
                                                </a>
                                            </li>
                                        </ul>
                                    <!-- Tab panes -->
                                        <div class="tab-content bg-info">
                                            <div role="tabpanel" class="tab-pane active" id="fibap-doc-soporte">
                                                <br>
                                                <div class="row" id="lbl-lista-documentos"></div>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="fibap-antecedentes">
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
                                            <div role="tabpanel" class="tab-pane" id="fibap-presupuesto">
                                                <br>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <span class="label label-default">Periodo de Ejecuación</span>
                                                        <p id="lbl-periodo-ejecucion"></p>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="label label-default">Presupuesto Requerido</span>
                                                        <p id="lbl-presupuesto-requerido"></p>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <b>Origenes</b>
                                                        <div class="row" id="lbl-origen-financiamiento">
                                                            @foreach ($origenes_financiamiento as $origen)
                                                                <div class="col-sm-3">
                                                                    <div class="form-group">
                                                                        <span class="label label-default">
                                                                            {{$origen->descripcion}}
                                                                        </span>
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="panel-btns-reporte">
                    <br>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <span class="fa fa-file"></span> Imprimir Reporte <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="#" id="btn-reporte-caratula-proyecto">Caratula Proyecto</a></li>
                            <li><a href="#" id="btn-reporte-fibap">FIBAP</a></li>
                        </ul>
                    </div>
                    <!--button type="button" class="btn btn-success" id="btn-exportar-excel">
                        <span class="fa fa-file-excel-o"></span> Imprimir Reporte
                    </button-->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btn-editar-proyecto">
                    <span class="fa fa-pencil-square-o"></span> Editar Proyecto
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->