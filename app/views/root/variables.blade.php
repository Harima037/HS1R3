@extends('layouts.Modulo')

@section('title-page') Administración de Variables @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/root/variables.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default datagrid" id="datagridVariables" data-edit-row="editar">
            <div class="panel-heading"><h4><i class="fa fa-code"></i> Administración de Variables</h4></div>
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
                                <button type="button" id="btnVariableAgregar" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Agregar</button>
                                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li><a href="#" class="btn-edit-rows"><span class="glyphicon glyphicon-edit"></span> Editar</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#" class="btn-delete-rows"><span class="glyphicon glyphicon-remove"></span> Eliminar</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>                
            </div>               
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="check-select-all-rows"></th>
                        <th width="250">Variable</th>
                        <th>Valor</th>
                        <th style="text-align:center; width:150px;"><span class="glyphicon glyphicon-time"></span></th>
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
    <div class="modal fade" id="modalVariable" tabindex="-1" role="dialog" aria-labelledby="modalVariableLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modalVariableLabel">Nuevo</h4>
                </div>
                <div class="modal-body">
                    <form action="" id="form_variable">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="nombre">Variable</label>
                                    <input type="text" class="form-control" id="variable" name="variable" maxlength="255" />
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="nombre">Valor</label>
                                    <textarea class="form-control" id="valor" name="valor" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="id" name="id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary btn-guardar">Guardar</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- Cargar Catalogo para permisos -->
@include('modulos-generales.catalogo-permisos')
<!-- Dejar parent al ultimo -->
@parent
@stop