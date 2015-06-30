@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/revision/cuenta-publica.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default datagrid" id="datagridCuentaPublica" data-edit-row="editar">
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
                            <button type="button" class="btn btn-info" id="btn-datos-institucionales">
                                <span class="fa fa-building"></span> Datos Institucionales
                            </button>
                            <button type="button" class="btn btn-info" id="btn-reporte-cuenta-publica">
                                <span class="fa fa-file-word-o"></span> Reporte
                            </button>
                            <button type="button" class="btn btn-success btn-edit-rows">
                                <span class="fa fa-pencil"></span> Cuenta Pública
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="check-select-all-rows"></th>
                        <th width="200">Clave Presupuestaria</th>
                        <th>Nombre Técnico</th>
                        <th style="text-align:center; width:50px;">Trim</th>
                        <th style="text-align:center; width:130px;">Cuenta Pública</th>
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
<div class="modal fade" id="modalDatosInstitucionales" tabindex="-1" role="dialog" aria-labelledby="modalDatosLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalDatosLabel">Datos Institucionales</h4>
            </div>
            <div class="modal-body">
                <form id="form_datos_institucionales">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label" for="clave-institucional">Clave Institucional:</label>
                                <input type="text" class="form-control" id="clave-institucional" name="clave-institucional" value="{{$datos['clave-institucional']}}"/>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label" for="mision">Misión:</label>
                                <textarea class="form-control" id="mision" name="mision" rows="5">{{$datos['mision']}}</textarea>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label" for="vision">Visión:</label>
                                <textarea class="form-control" id="vision" name="vision" rows="5">{{$datos['vision']}}</textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-guardar-datos-institucionales">
                    <span class="fa fa-save"></span> Guardar
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="modalCuentaPublica" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalLabel">Cuenta Pública</h4>
            </div>
            <div class="modal-body">
                <form id="form_cuenta_publica">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label" for="cuenta-publica">Cuenta Pública</label>
                                <textarea class="form-control" id="cuenta-publica" name="cuenta-publica" rows="10"></textarea>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="id" name="id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-guardar-cuenta-publica">
                    <span class="fa fa-save"></span> Guardar
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Dejar parent al ultimo -->
@parent
@stop