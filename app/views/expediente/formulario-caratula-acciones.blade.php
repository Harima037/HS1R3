<h4>Presupuesto Requerido y Propuesta de Financiamiento</h4>
<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <label class="control-label">
                Total Distribuido / Total Requerido :
            </label>
            <span class="text-muted" id="total-presupuesto-distribuido">$ 0.00</span> / <span class="text-muted" id="total-presupuesto-requerido">$ 0.00</span>
            <div class="progress">
                <div id="porcentaje_completo" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"> 0%
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="panel panel-success" id="tabla_presupuesto_partida">
            <table class="table table-striped table-hover">
                <thead>
                    <tr class="bg-success">
                        <th width="100px">Partida</th>
                        <th>Descripción</th>
                        <th width="100px">Monto</th>
                        <th width="90px">Porcentaje</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <div class="col-sm-12"><label class="control-label">Origen del Presupuesto</label></div>
    <div class="col-sm-12">
        <div class="row">
            @foreach ($origenes_financiamiento as $origen)
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label">
                            {{$origen->descripcion}} :
                        </label>
                        <span class="text-muted totales-financiamiento" data-total-origen-id="{{$origen->id}}">
                            $ 0.00
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
<div class="datagrid panel panel-primary" id="datagridAcciones" data-edit-row="editar_accion">
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-6">
                <h4>Componentes</h4>
            </div>
            <div class="col-sm-6">
                <div class="btn-toolbar pull-right" >
                    <div class="btn-group" style="margin:5px">
                        <button type="button" class="btn btn-primary" id="btn-agregar-accion">
                            <span class="glyphicon glyphicon-plus"></span> Agregar Componente
                        </button>
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            <li>
                                <a href="#" class="btn-edit-rows">
                                    <span class="glyphicon glyphicon-edit"></span> Editar
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#" class="btn-delete-rows">
                                    <span class="glyphicon glyphicon-remove"></span> Eliminar
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>    
    </div>
    <table class="table table-condensed">
        <thead>
            <tr>
                <th><input type="checkbox" class="check-select-all-rows"></th>
                <th>Indicador</th>
                <th>Unidad de Medida</th>
                <!--th>Entregable</th>
                <th>Tipo</th>
                <th>Acción</th>
                <th>Modalidad</th-->
                <th>Presupuesto</th>
                <th width="50px"></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div id="datagrid-contenedor" class="hidden">
    <div class="datagrid panel panel-primary" id="datagridDistribucion" data-edit-row="editar_presupuesto" data-selected-id="">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-4">
                    <label class="control-label">Porcentaje Distribuido del Presupuesto</label>
                </div>
                <div class="col-sm-8">
                    <div class="progress">
                        <div id="porcentaje_accion" class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"> 0%
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-8">
                    <div class="input-group" style="margin:5px">                            
                        <input type="text" class="form-control txt-quick-search" placeholder="Buscar">
                        <span class="input-group-btn">
                            <button class="btn btn-default btn-quick-search" type="button">
                                <span class="glyphicon glyphicon-search"></span>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="btn-toolbar pull-right" >
                        <!--div class="btn-group" style="margin:5px">
                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="fa fa-file"></span> Importación <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu pull-right">
                                <li>
                                    <a href="#" id="lnk-descarga-archivo-metas" target="_blank">
                                        <span class="fa fa-download"></span> Descargar Archivo para Metas
                                    </a>
                                </li>
                                <li>
                                    <a href="#" id="lnk-descarga-archivo-presupuesto" target="_blank">
                                        <span class="fa fa-download"></span> Descargar Archivo para Presupuesto
                                    </a>
                                </li>
                                <li>
                                    <a href="#" id="lnk-descarga-archivo-beneficiarios" target="_blank">
                                        <span class="fa fa-download"></span> Descargar Archivo para Beneficiarios
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#" id="lnk-mostrar-subir-archivo">
                                        <span class="fa fa-upload"></span> Cargar Archivo de Importación
                                    </a>
                                </li>
                            </ul>
                        </div-->
                        <div class="btn-group" style="margin:5px">
                            <button type="button" class="btn btn-info" id="btn-agregar-distribucion">
                                <span class="fa fa-plus"></span> Agregar
                            </button>
                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <li>
                                    <a href="#" class="btn-edit-rows">
                                        <span class="fa fa-edit"></span> Editar
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#" class="btn-delete-rows">
                                        <span class="fa fa-times"></span> Eliminar
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th><input type="checkbox" class="check-select-all-rows"></th>
                    <th>Localidad</th>
                    <th>Municipio</th>
                    <th>Jurisdicción</th>
                    <th width="100px">Monto</th>
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

<div class="modal fade" id="modal-presupuesto" tabindex="-1" role="dialog" aria-labelledby="modalPresupLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalPresupLabel">Nuevo</h4>
            </div>
            <div class="modal-body">
                <form id="form-presupuesto">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="jurisdiccion-accion" class="label-control">Jurisdicción</label>
                                <select id="jurisdiccion-accion" name="jurisdiccion-accion" class="form-control chosen-one">
                                    <option value="">Selecciona una Jurisdicción</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="municipio-accion" class="label-control">Municipio</label>
                                <select id="municipio-accion" name="municipio-accion" class="form-control chosen-one" >
                                    <option value="">Selecciona un Municipio</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="localidad-accion" class="label-control">Localidad</label>
                                <select id="localidad-accion" name="localidad-accion" class="form-control chosen-one">
                                    <option value="">Selecciona una Localidad</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#desglose-beneficiarios" aria-controls="desglose-beneficiarios" role="tab" data-toggle="tab">
                                    <span class="fa fa-users"></span> Beneficiarios
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#calendarizado-presupuesto" aria-controls="calendarizado-presupuesto" role="tab" data-toggle="tab">
                                    <span class="fa fa-usd"></span> Presupuesto
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#calendarizado-metas" aria-controls="calendarizado-metas" role="tab" data-toggle="tab">
                                    <span class="fa fa-table"></span> Metas
                                </a>
                            </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="desglose-beneficiarios">
                                <br>
                                <div class="form-group">
                                    <table id="tabla_beneficiarios" class="table table-condensed table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tipo Beneficiario</th>
                                                <th width="20%"><span class="fa fa-female"></span> Femenino</th>
                                                <th width="20%"><span class="fa fa-male"></span> Masculino</th>
                                                <th width="20%">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3"></td>
                                                <td>
                                                    <span class="form-control" id="total-beneficiarios-lbl">0</span>
                                                    <input type="hidden" id="total-beneficiarios" name="total-beneficiarios">
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="calendarizado-presupuesto">
                                <br>
                                <div class="row">
                                    @foreach ($meses as $clave => $mes)
                                        <div class="col-sm-3">
                                            <div class="form-group grupo-partidas" data-grupo-mes="{{$clave}}">
                                                <label class="control-label">{{$mes}}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="clearfix"></div>
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <span class="fa fa-link"></span> Total
                                                </span>
                                                <input type="hidden" id="cantidad-presupuesto" name="cantidad-presupuesto"/>
                                                <span class="form-control" id="cantidad-presupuesto-lbl">0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="calendarizado-metas">
                                <br>
                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <label class="control-label">
                                                Indicador
                                            </label>
                                            <p class="form-control-static" id="indicador_texto">
                                                Descripción del Indicador
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="control-label">
                                                Unidad de Medida
                                            </label>
                                            <p class="form-control-static" id="unidad_medida_texto">
                                                Descripción de la unidad de medida
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="row">
                                            @foreach ($meses as $clave => $mes)
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon">{{$mes}}</span>
                                                            <input id="meta-mes-{{$clave}}" name="meta-mes[{{$clave}}]" type="number" class="form-control input-sm meta-mes" data-meta-mes="{{$clave}}" data-meta-id="">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-sm-9">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label class="control-label"><span class="fa fa-link"></span> Trim 1</label>
                                                    <span class="form-control" id="trim1-lbl">0</span>
                                                    <input type="hidden" id="trim1" name="trim1">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label class="control-label"><span class="fa fa-link"></span> Trim 2</label>
                                                    <span class="form-control" id="trim2-lbl">0</span>
                                                    <input type="hidden" id="trim2" name="trim2">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label class="control-label"><span class="fa fa-link"></span> Trim 3</label>
                                                    <span class="form-control" id="trim3-lbl">0</span>
                                                    <input type="hidden" id="trim3" name="trim3">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label class="control-label"><span class="fa fa-link"></span> Trim 4</label>
                                                    <span class="form-control" id="trim4-lbl">0</span>
                                                    <input type="hidden" id="trim4" name="trim4">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label><span class="fa fa-link"></span> Total</label>
                                            <input type="hidden" id="cantidad-meta" name="cantidad-meta"/>
                                            <span class="form-control" id="cantidad-meta-lbl">0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="id-desglose" id="id-desglose">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-guardar" id="btn-presupuesto-guardar">Guardar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!--      Modal para subida del archivo de metas, beneficiarios y presupuesto      -->

<!--div class="modal fade" id="modal-subir-archivo" tabindex="-1" role="dialog" aria-labelledby="modalArchivoLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalArchivoLabel">Cargar Archivo</h4>
            </div>
            <div class="modal-body">
                <form id="form-subir-archivo">
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label class="control-label" for="tipo-archivo">Tipo de Archivo</label>
                                <select id="tipo-archivo" name="tipo-archivo" class="form-control">
                                    <option value="">Selecciona un tipo</option>
                                    <option value="metas">Archivo para Metas</option>
                                    <option value="presupuesto">Archivo para Presupuesto</option>
                                    <option value="beneficiarios">Archivo para Beneficiarios</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group">
                                <label class="control-label" for="archivo">Archivo</label>
                                <input type="file" id="archivo" name="archivo" accept=".csv" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div id="resultado-carga"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-subir-archivo"><span class="fa fa-upload"></span> Cargar</button>
            </div>
        </div>
    </div>
</div-->

<!--
    Modal para Acción
-->

<div class="modal fade" id="modal-componente" tabindex="-1" role="dialog" aria-labelledby="modalCompLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalCompLabel">Nuevo</h4>
            </div>
            <div class="modal-body">
                {{$formulario_componente}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-grupo-guardar" id="btn-componente-guardar-salir">Guardar</button>
                <button type="button" class="btn btn-success btn-grupo-guardar" id="btn-componente-guardar">Guardar y Agregar Actividades</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="modal-actividad" tabindex="-1" role="dialog" aria-labelledby="modalActLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalActLabel">Nuevo</h4>
            </div>
            <div class="modal-body">
                {{$formulario_actividad}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-guardar" id="btn-actividad-guardar">Guardar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->