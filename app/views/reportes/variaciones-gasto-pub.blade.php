@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/reportes/variaciones-gasto-pub.js') }}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default datagrid" id="datagridProyectos" data-edit-row="editar">
            <div class="panel-heading"><h4><i class="fa {{ $sys_mod_activo->icono }}"></i> {{ $sys_mod_activo->nombre }}</h4></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <input type="text" class="form-control txt-quick-search" placeholder="Buscar">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select class="form-control" id="mes">
                            @foreach($meses as $clave => $mes)
                                <option value="{{$clave}}" @if($clave == $mes_actual) selected @endif>
                                    {{$mes}}
                                </option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <input type="number" class="form-control" id="ejercicio" placeholder="Ejercicio" value="{{$ejercicio}}" />
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <button class="btn btn-default btn-block btn-quick-search" type="button"><span class="fa fa-search"></span></button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary btn-block" id="btn-ver-cedulas">
                            <span class="fa fa-file-pdf-o"></span> Ver Reporte
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="btn-toolbar pull-right" >
                            <div class="btn-group">
                                <button type="button" class="btn btn-success btn-edit-rows">
                                    <span class="fa fa-pencil"></span> Editar Razones
                                </button>
                            </div>
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
                        <th width="150">Presupuesto<br>Aprobado</th>
                        <th width="150">Presupuesto<br>Modificado</th>
                        <th width="150">Presupuesto<br>Devengado</th>
                        <th width="100">Razones</th>
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
<!-- Dejar parent al ultimo -->
<div class="modal fade" id="modalRazones" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modalLabel">Razones</h4>
                </div>
                <div class="modal-body">
                <form action="" id="formRazones">
                	<input type="hidden" id="id">
                    <input type="hidden" id="id-proyecto">                            
                    <input type="hidden" id="mes-razones">
                    <input type="hidden" id="clave-presupuestaria" value="">

						<div class="row">
                        	<div class="col-sm-12">
                        	<div class="form-group">
                        	<label for="razones">Razones por diferencia entre presupuesto Modificado y Aprobado</label>
                            <textarea class="form-control" name="razones" id="razones" rows="8"></textarea>
                     	   	</div>
                            </div>
                        </div>
                        						<div class="row">
                        	<div class="col-sm-12">
                        	<div class="form-group">
                        	<label for="razones2">Razones por diferencia entre presupuesto Modificado y Devengado</label>
                            <textarea class="form-control" name="razones2" id="razones2" rows="8"></textarea>
                     	   	</div>
                            </div>
                        </div>

                </form>
                </div>
                <div class="modal-footer">
	                <button type="button" class="btn btn-success" id="btnGuardarRazones">Guardar razones</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->



@parent
@stop