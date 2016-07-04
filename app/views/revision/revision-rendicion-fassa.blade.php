@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('js')
@parent
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<!--<script src="{{ URL::to('js/modulos/rendicion-cuentas/lista-fassa-rendicion.js') }}"></script>-->
<script src="{{ URL::to('js/modulos/revision/revision-fassa-rendicion.js') }}"></script>
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
                                <span class="fa fa-edit"></span> Ver / Comentar Indicador
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
                        <th class="text-center" style="width:60px;">Trim 1</th>
                        <th class="text-center" style="width:60px;">Trim 2</th>
                        <th class="text-center" style="width:60px;">Trim 3</th>
                        <th class="text-center" style="width:60px;">Trim 4</th>
                        <th class="text-center" style="width:60px;">Cierre</th>
                        <th style="width:110px;">Metas</th>
                        <th style="width:110px;">Avance</th>
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
            <div class="modal-body" style="padding-bottom:0;">
                <div class="row">
                    <div class="col-sm-6">
                        <label class="control-label" for="indicador">Indicador</label>
                        <p class="help-block" id="indicador"></p>
                    </div>
                    <div class="col-sm-6">
                        <label class="control-label" for="formula">Formula</label>
                        <p class="help-block" id="formula"></p>
                    </div>
                </div>
                <form id="form_indicador_fassa">
                    <div class="row" id="panel-programacion-fassa">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">Programación</label>
                                <div id="estatus-programacion"></div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <table id="table-programacion-trimestres" class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Numerador</th>
                                        <th>Denominador</th>
                                        <th>Porcentaje</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row" id="panel-avance-fassa" style="background-color:#E4E4E4; padding-top:15px;">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">Meta del Trimestre</label>
                                <div id="estatus-programacion-trimestre"></div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label">Numerador</label>
                                <span class="form-control" id="numerador-trimestre"></span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label">Denominador</label>
                                <span class="form-control" id="denominador-trimestre"></span>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">Porcentaje</label>
                                <span class="form-control" id="porcentaje-trimestre">%</span>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label">Avance <span id="label-mes-avance"></span></label>
                                <div id="estatus-avance"></div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="avance-numerador">Numerador</label>
                                <div class="input-group">
                                	<span class="input-group-btn" onclick="escribirComentario('avance-numerador','Numerador','avance-numerador','avance');">
                                    	<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
                                    </span>
                                    <p id="avance-numerador" name="avance-numerador" class="form-control informacion-avance" style="height:auto">&nbsp;</p>
                  			    </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="avance-denominador">Denominador</label>
                                <div class="input-group">
                                	<span class="input-group-btn" onclick="escribirComentario('avance-denominador','Denominador','avance-denominador','avance');">
                                    	<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
                                    </span>
                                    <p id="avance-denominador" name="avance-denominador" class="form-control informacion-avance" style="height:auto">&nbsp;</p>
                  			    </div>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label" for="avance-porcentaje">Porcentaje</label>
                                <span class="form-control" id="avance-porcentaje">%</span>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label" for="porcentaje-total">Desempeño</label>
                                <div class="form-control-static" id="porcentaje-total">%</div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label" for="justificacion">Justificación Acumulada</label>
                                <div class="input-group">
                                	<span class="input-group-btn" onclick="escribirComentario('justificacion','Justificación Acumulada','justificacion','avance');">
                                    	<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
                                    </span>
                                    <p id="justificacion" name="justificacion" class="form-control informacion-avance" style="height:auto">&nbsp;</p>
                  			    </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="id-avance" name="id-avance">
                    <input type="hidden" id="id" name="id">
                    
                    <input type="hidden" id="id-estatus-meta" name="id-estatus-meta">
                    <input type="hidden" id="id-estatus-avance" name="id-estatus-avance">
                    
                </form>
            </div>
            <div class="modal-footer">
                <div class="btn-toolbar pull-right">
					<div class="btn-group" style="margin:5px">
                    	<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                		<button type="button" id="btnAcciones" class="btn btn-primary"><i class="fa fa-gears"></i> Acciones</button>
	                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span>
    	                </button>
        	            <ul class="dropdown-menu pull-right" role="menu">
            	        	<li id="btnAprobar" name="btnAprobar"><a href="#" class="btn-default"><i class="fa fa-thumbs-o-up"></i> Aprobar meta/avance</a></li>
                	        <li id="btnRegresar" name="btnRegresar"><a href="#" class="btn-default"><i class="fa fa-mail-reply-all"></i> Regresar para corrección</a></li>
                            <li id="btnFirmar" name="btnFirmar"><a href="#" class="btn-default">
                            <span class="glyphicon glyphicon-pencil"></span> Firmar meta/avance</a></li>
                    	</ul>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



	<div class="modal fade" id="modalComentario" tabindex="-1" role="dialog" aria-labelledby="modalComentarioLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modalComentarioLabel">Escribir comentario </h4>
                </div>
                <div class="modal-body"> 
                    <form action="" id="formComentario">
                    	<input type="hidden" name="idproyectocomentarios" id="idproyectocomentarios">
                    	<input type="hidden" name="idcampo" id="idcampo">
                    	<input type="hidden" name="idavance" id="idavance">

						<div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="informacioncampo" class="control-label" id="lbl-nombredelcampo"></label>
                                    <p id="lbl-informacioncampo" class="form-control" style="height:auto"></p>                                
                                 </div>                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="comentario" class="control-label">Comentario</label>
                                    <textarea class="form-control" name="comentario" id="comentario" rows="4"></textarea>
                                </div>                                
                            </div>
                        </div>
                        
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" id="btnGuardarComentario">Guardar comentario</button>
                    <button type="button" class="btn btn-danger" id="btnQuitarComentario">Quitar comentario</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
         
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->







<!-- Dejar parent al ultimo -->
@parent
@stop