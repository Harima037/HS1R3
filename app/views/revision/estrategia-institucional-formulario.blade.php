@extends('layouts.Modulo')

@section('title-page') {{ $sys_mod_activo->nombre }} @stop

@section('css')
@parent
<link href="{{ URL::to('css/chosen.bootstrap.min.css') }}" rel="stylesheet" type="text/css" media="screen">
@stop

@section('js')
@parent
<script src="{{ URL::to('js/dependencias/chosen.jquery.min.js') }}"></script>
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/revision/estrategia-institucional-formulario.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<input type="hidden" id="id" name="id" value="{{$id}}">
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="fa {{ $sys_mod_activo->icono }}"></i> {{ $sys_mod_activo->nombre }}</h4></div>
            <div class="panel-body">
                <div class="btn-toolbar pull-right">
                    <div class="btn-group" style="margin:5px">
                        <button type="button" id="btnAcciones" class="btn btn-primary"><i class="fa fa-gears"></i> Acciones</button>
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            <li id="btnAprobarEstrategia" name="btnAprobarEstrategia"><a href="#" class="btn-default"><i class="fa fa-thumbs-o-up"></i> Aprobar el estrategia</a></li>
                            <li id="btnRegresarCorregir" name="btnRegresarCorregir"><a href="#" class="btn-default"><i class="fa fa-mail-reply-all"></i> Regresar para correción</a></li>
                            <li id="btnFirmarEstrategia" name="btnFirmarEstrategia"><a href="#" class="btn-default">
                            <span class="glyphicon glyphicon-pencil"></span> Firmar estrategia</a></li>
                        </ul>
                    </div>
                </div>
                <div role="tabpanel">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#panel-estrategia-institucional" aria-controls="panel-estrategia-institucional" role="tab" data-toggle="tab">
                                Estrategia Institucional
                            </a>
                        </li>
                    </ul>
                    
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="panel-estrategia-institucional">
                            <br>
                            <form id="form_estrategia_datos">
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-unidad-responsable">Unidad Responsable</label>
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('unidad-responsable','Unidad Responsable','lbl-unidad-responsable');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-unidad-responsable" name="lbl-unidad-responsable" class="form-control" style="height:auto"></p>
                                        </div>       
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-programa-sectorial">Programa Sectorial</label>
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('programa-sectorial','Programa Sectorial','lbl-programa-sectorial');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-programa-sectorial" name="lbl-programa-sectorial" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-ejercicio">Ejercicio</label>
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('ejercicio','Ejercicio','lbl-ejercicio');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-ejercicio" name="lbl-ejercicio" class="form-control" style="height:auto"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-estrategia-pnd">Estrategia del Objetivo del Plan Nacional</label>
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('estrategia-pnd','Estrategia del Objetivo del Plan Nacional','lbl-estrategia-pnd');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-estrategia-pnd" name="lbl-estrategia-pnd" class="form-control" style="height:auto"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-odm">ODM</label>
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('odm','ODM','lbl-odm');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-odm" name="lbl-odm" class="form-control" style="height:auto"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-vinculacion-ped">Vinculación al PED (Plan Estatal de Desarrollo)</label>
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('vinculacion-ped','Vinculación al PED (Plan Estatal de Desarrollo)','lbl-vinculacion-ped');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-vinculacion-ped" name="lbl-vinculacion-ped" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-descripcion-indicador">Descripción del Indicador</label>
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('descripcion-indicador','Descripción Indicador','lbl-descripcion-indicador');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-descripcion-indicador" name="lbl-descripcion-indicador" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-numerador">Numerador</label>
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('numerador','Numerador','lbl-numerador');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-numerador" name="lbl-numerador" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-denominador">Denominador</label>
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('denominador','Denominador','lbl-denominador');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-denominador" name="lbl-denominador" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-interpretacion">Interpretación</label>
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('interpretacion','Interpretacion','lbl-interpretacion');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-interpretacion" name="lbl-interpretacion" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label class="control-label" for="lbl-tipo-ind">Tipo</label>
                                                <div class="input-group">
                                                    <span class="input-group-btn" onclick="escribirComentario('tipo-ind','Tipo','lbl-tipo-ind');">
                                                    <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                                    <p id="lbl-tipo-ind" name="lbl-tipo-ind" class="form-control" style="height:auto">&nbsp;</p>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label" for="lbl-dimension">Dimensión</label>
                                                <div class="input-group">
                                                    <span class="input-group-btn" onclick="escribirComentario('dimension','Dimensión','lbl-dimension');">
                                                    <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                                    <p id="lbl-dimension" name="lbl-dimension" class="form-control" style="height:auto">&nbsp;</p>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        
                                        <div class="col-sm-5">
                                            <div class="form-group">
                                                <label class="control-label" for="lbl-unidad-medida">Unidad de Medida</label>
                                                <div class="input-group">
                                                    <span class="input-group-btn" onclick="escribirComentario('unidad-medida','Unidad de medida','lbl-unidad-medida');">
                                                    <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                                    <p id="lbl-unidad-medida" name="lbl-unidad-medida" class="form-control" style="height:auto">&nbsp;</p>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-objetivo-estrategico">Objetivo Estratégico:</label>
                                        <textarea class="form-control" id="lbl-objetivo-estrategico" name="lbl-objetivo-estrategico" rows="5" class="disabled"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <button type="button" class="btn btn-default" id="btnvision" onclick="escribirComentario('objetivo-estrategico','Objetivo Estratégico','lbl-objetivo-estrategico')" ><i class="fa fa-pencil-square-o"></i> Comentar Objetivo Estratégico</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label">Información de la Programación de Metas por Trimestre</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-fuente-informacion">Fuente de Información</label>
                                        <div class="input-group">
                                            <span class="input-group-btn" onclick="escribirComentario('fuente-informacion','Fuente de Información','lbl-fuente-informacion');">
                                            <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-fuente-informacion" name="lbl-fuente-informacion" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-responsable">Responsable</label>
                                        <div class="input-group">
                                            <span class="input-group-btn" onclick="escribirComentario('responsable','Responsable','lbl-responsable');">
                                            <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-responsable" name="lbl-responsable" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>
                                        <span id="ayuda-responsable" class="help-block"></span>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-linea-base">Linea Base</label>
                                        <div class="input-group">
                                            <span class="input-group-btn" onclick="escribirComentario('linea-base','Linea Base','lbl-linea-base');">
                                            <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-linea-base" name="lbl-linea-base" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>
                                    </div>
                                   
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-anio-base">Anio Base</label>
                                        <div class="input-group">
                                            <span class="input-group-btn" onclick="escribirComentario('anio-base','Anio Base','lbl-anio-base');">
                                            <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-anio-base" name="lbl-anio-base" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-formula">Formula</label>
                                        <div class="input-group">
                                            <span class="input-group-btn" onclick="escribirComentario('formula','Formula','lbl-formula');">
                                            <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-formula" name="lbl-formula" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>
                                    </div>
                                   
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-frecuencia">Frecuencia</label>
                                        <div class="input-group">
                                            <span class="input-group-btn" onclick="escribirComentario('frecuencia','Frecuencia','lbl-frecuencia');">
                                            <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-frecuencia" name="lbl-frecuencia" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>
                                    </div>
                                    
                                </div> 
                            </div>
                            <div class="row">
                                                                           
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-trim1">Trim 1</label>
                                        <div class="input-group">
                                            <span class="input-group-btn" onclick="escribirComentario('trim1','Trim 1','lbl-trim1');">
                                            <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-trim1" name="lbl-trim1" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-trim2">Trim 2</label>
                                        <div class="input-group">
                                            <span class="input-group-btn" onclick="escribirComentario('trim2','Trim 2','lbl-trim2');">
                                            <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-trim2" name="lbl-trim2" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-trim3">Trim 3</label>
                                        <div class="input-group">
                                            <span class="input-group-btn" onclick="escribirComentario('trim3','Trim 3','lbl-trim3');">
                                            <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-trim3" name="lbl-trim3" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-sm-3">  
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-trim4">Trim 4</label>
                                        <div class="input-group">
                                            <span class="input-group-btn" onclick="escribirComentario('trim4','Trim 4','lbl-trim4');">
                                            <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-trim4" name="lbl-trim4" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="row">
                                
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-valorNumerador">Numerador</label>
                                        <div class="input-group">
                                            <span class="input-group-btn" onclick="escribirComentario('valorNumerador','Numerador','lbl-valorNumerador');">
                                            <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-valorNumerador" name="lbl-valorNumerador" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-valorDenominador">Denominador</label>
                                        <div class="input-group">
                                            <span class="input-group-btn" onclick="escribirComentario('valorDenominador','Denominador','lbl-valorDenominador');">
                                            <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-valorDenominador" name="lbl-valorDenominador" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-meta">Meta</label>
                                        <div class="input-group">
                                            <span class="input-group-btn" onclick="escribirComentario('meta','Meta','lbl-meta');">
                                            <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-meta" name="lbl-meta" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>
                                    </div>
                                    
                                </div>          
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="well well-sm">
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <table class="table table-condensed table-bordered table-striped" id="tabla-anios-metas-indicadores">
                                                    <thead>
                                                        <tr>
                                                            <th style="text-align:center;" width="1">Año</th>
                                                            <th style="text-align:center;">Numerador</th>
                                                            <th style="text-align:center;">Denominador</th>
                                                            <th style="text-align:center;">Meta del Indicador</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="4" style="text-align:center;">
                                                                <button type="button" class="btn btn-default" id="btnaniosmetas" onclick="escribirComentario('anios-metas','Metas por Año','lbl-anios-metas')" ><i class="fa fa-pencil-square-o"></i> Comentar Metas por Año</button>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label class="control-label" for="lbl-comportamiento-meta-estrategia">Comportamiento</label>
                                                            <div class="input-group">
                                                                <span class="input-group-btn" onclick="escribirComentario('comportamiento-meta-estrategia','Comportamiento','lbl-comportamiento-meta-estrategia');">
                                                                <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                                                <p id="lbl-comportamiento-meta-estrategia" name="lbl-comportamiento-meta-estrategia" class="form-control" style="height:auto">&nbsp;</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label class="control-label" for="lbl-tipo-valor-meta-estrategia">Tipo de Valor de la Meta</label>
                                                            <div class="input-group">
                                                                <span class="input-group-btn" onclick="escribirComentario('tipo-valor-meta-estrategia','Tipo de Valor de la Meta','lbl-tipo-valor-meta-estrategia');">
                                                                <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                                                <p id="lbl-tipo-valor-meta-estrategia" name="lbl-tipo-valor-meta-estrategia" class="form-control" style="height:auto">&nbsp;</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            </form>
                            
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-default" id="btn-estrategia-cancelar">
                            <span class="fa fa-chevron-left"></span> Regresar a la lista de estrategias Institucionales
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('modals')

<div class="modal fade" id="modalComentario" tabindex="-1" role="dialog" aria-labelledby="modalComentarioLabel" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog">
    	<div class="modal-content">
        	<div class="modal-header">
            	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalComentarioLabel">Escribir comentario </h4>
			</div>
            <div class="modal-body"> 
            	<form action="" id="formComentario">
                	<input type="hidden" name="idestrategiacomentarios" id="idestrategiacomentarios">
                    <input type="hidden" name="idcampo" id="idcampo">
                    <input type="hidden" name="tipocomentario" id="tipocomentario">
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


@parent
@stop