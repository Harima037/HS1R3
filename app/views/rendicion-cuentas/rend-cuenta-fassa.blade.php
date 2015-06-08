@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/rendicion-cuentas/lista-fassa-rendicion.js') }}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default datagrid" id="datagridIndicadores" data-edit-row="editar">
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
                            <button type="button" class="btn btn-success btn-edit-rows">
                                <span class="fa fa-edit"></span> Editar Indicador
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="check-select-all-rows"></th>
                        <th>Indicador</th>
                        <th style="width:250px;">Unidad Responsable</th>
                        <th style="width:120px;">Nivel</th>
                        <th style="width:100px;">Estatus</th>
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
<div class="modal fade" id="modalIndicador" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalLabel">Nuevo</h4>
            </div>
            <input type="hidden" id="tipo-formula">
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <label class="control-label" for="indicador">Indicador</label>
                        <p class="help-block" id="indicador"></p>
                    </div>
                    <div class="col-sm-6">
                        <label class="control-label" for="formula">Formula</label>
                        <p class="help-block" id="formula"></p>
                    </div>
                    <!--div class="col-sm-12">
                        <label class="control-label" for="fuente-informacion">Fuente de Información</label>
                        <p class="help-block" id="fuente-informacion" ></p>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="lider-programa">Lider del Programa</label>
                            <p class="help-block" id="lider-programa"></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="responsable-informacion">Responsable de la Información</label>
                            <p class="help-block" id="responsable-informacion"></p>
                        </div>
                    </div-->
                </div>
                <form id="form_indicador_fassa">
                    <div class="row well well-sm">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <p class="form-control-static">
                                    <b><big>Programación</big></b>
                                </p>
                                <div id="estatus-programacion"></div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="numerador">Numerador</label>
                                <input type="number" min="0" class="form-control informacion-meta" id="numerador" name="numerador">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="denominador">Denominador</label>
                                <input type="number" min="0" class="form-control informacion-meta" id="denominador" name="denominador">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label" for="porcentaje">Porcentaje</label>
                                <span class="form-control" id="porcentaje">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <p class="form-control-static">
                                    <b><big>Avance</big></b>
                                </p>
                                <div id="estatus-avance"></div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="avance-numerador">Numerador</label>
                                <input type="number" min="0" class="form-control informacion-avance" id="avance-numerador" name="avance-numerador">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="avance-denominador">Denominador</label>
                                <input type="number" min="0" class="form-control informacion-avance" id="avance-denominador" name="avance-denominador">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label" for="porcentaje">Porcentaje</label>
                                <span class="form-control" id="porcentaje">%</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="analisis-resultados">Analisis de Resultados Acumulado</label>
                                <textarea class="form-control informacion-avance" id="analisis-resultados" name="analisis-resultados" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="justificacion">Justificación Acumulada</label>
                                <textarea class="form-control informacion-avance" id="justificacion" name="justificacion" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="id-avance" name="id-avance">
                    <input type="hidden" id="id" name="id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-guardar-indicador"><span class="fa fa-save"></span> Guardar</button>
                <button type="button" class="btn btn-success" id="btn-guardar-validar-indicador">
                    <span class="fa fa-send-o"></span> Guardar y Enviar a Validar
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Dejar parent al ultimo -->
@parent
@stop