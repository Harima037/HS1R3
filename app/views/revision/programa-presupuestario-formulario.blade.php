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
<script src="{{ URL::to('js/modulos/revision/programa-presupuestario-formulario.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<input type="hidden" id="id" name="id" value="{{$id}}">
<input type="hidden" id="idEstatusPrograma" name="idEstatusPrograma">

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
            	        	<li id="btnAprobarPrograma" name="btnAprobarPrograma"><a href="#" class="btn-default"><i class="fa fa-thumbs-o-up"></i> Aprobar el programa</a></li>
                	        <li id="btnRegresarCorregir" name="btnRegresarCorregir"><a href="#" class="btn-default"><i class="fa fa-mail-reply-all"></i> Regresar para correción</a></li>
                            <li id="btnFirmarPrograma" name="btnFirmarPrograma"><a href="#" class="btn-default">
                            <span class="glyphicon glyphicon-pencil"></span> Firmar programa</a></li>
                    	</ul>
                    </div>
                </div>
                            
                <div role="tabpanel">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#panel-programa-presupuestario" aria-controls="panel-programa-presupuestario" role="tab" data-toggle="tab">
                                Programa Prespuestario
                            </a>
                        </li>
                        <li role="presentation" class="disabled">
                            <a href="#diagnostico" aria-controls="diagnostico" role="tab" data-toggle="" id="tab-link-diagnostico">Diagnóstico</a>
                        </li>
                        <li role="presentation" class="disabled">
                            <a href="#indicadores" aria-controls="indicadores" role="tab" data-toggle="" id="tab-link-indicadores">Objetivos e Indicadores</a>
                        </li>
                        <li role="presentation">
                            <a href="#datos-informacion" aria-controls="datos-informacion" role="tab" data-toggle="tab" id="tab-link-datos-informacion">Información</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="panel-programa-presupuestario">
                            <br>
                            <form id="form_programa_datos">
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
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-programa-presupuestario">Programa Presupuestario</label>
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('programa-presupuestario','Programa Presupuestario','lbl-programa-presupuestario');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-programa-presupuestario" name="lbl-programa-presupuestario" class="form-control" style="height:auto"></p>
                                        </div>
                                    </div>
                                </div>                                
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
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-vinculacion-pnd">Vinculación al PND (Plan Nacional de Desarrollo)</label>
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('vinculacion-pnd','Vinculación al PND (Plan Nacional de Desarrollo)','lbl-vinculacion-pnd');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-vinculacion-pnd" name="lbl-vinculacion-pnd" class="form-control" style="height:auto">&nbsp;</p>
                                        </div>
                                    </div>
                                </div> 
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-modalidad">Modalidad</label>
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('modalidad','Modalidad','lbl-modalidad');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-modalidad" name="lbl-modalidad" class="form-control" style="height:auto"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-fecha-inicio">Fecha de Inicio</label>
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('fecha-inicio','Fecha de Inicio','lbl-fecha-inicio');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-fecha-inicio" name="lbl-fecha-inicio" class="form-control" style="height:auto"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label" for="fecha-termino">Fecha de Término</label>
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('fecha-termino','Fecha de Término','lbl-fecha-termino');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-fecha-termino" name="lbl-fecha-termino" class="form-control" style="height:auto"></p>
                                        </div>                                    
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-resultados-esperados">Resultados Esperados por la Implementación
                                        </label>
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('resultados-esperados','Resultados Esperados por la Implementación','lbl-resultados-esperados');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-resultados-esperados" name="lbl-resultados-esperados" class="form-control" style="height:auto"></p>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="col-sm-9">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-enfoque-potencial">Área de Enfoque Potencial</label>
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('enfoque-potencial','Área de enfoque potencial','lbl-enfoque-potencial');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-enfoque-potencial" name="lbl-enfoque-potencial" class="form-control" style="height:auto"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-cuantificacion-potencial">Cuantificación</label>
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('cuantificacion-potencial','Cuantificación','lbl-cuantificacion-potencial');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-cuantificacion-potencial" name="lbl-cuantificacion-potencial" class="form-control" style="height:auto"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-9">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-enfoque-objetivo">Área de Enfoque Objetivo</label>
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('enfoque-objetivo','Área de enfoque objetivo','lbl-enfoque-objetivo');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-enfoque-objetivo" name="lbl-enfoque-objetivo" class="form-control" style="height:auto"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-cuantificacion-objetivo">Cuantificación</label>
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('cuantificacion-objetivo','Cuantificación','lbl-cuantificacion-objetivo');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-cuantificacion-objetivo" name="lbl-cuantificacion-objetivo" class="form-control" style="height:auto"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label" for="lbl-justificacion-programa">Justificación del Programa</label>
                                        <div class="input-group">
						                   	<span class="input-group-btn" onclick="escribirComentario('justificacion-programa','Justificación del Programa','lbl-justificacion-programa');">
                       						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                                            <p id="lbl-justificacion-programa" name="lbl-justificacion-programa" class="form-control" style="height:auto"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                            <div class="row">
                                <div class="col-sm-12">
                                    <!--<button type="button" class="btn btn-primary pull-right" id="btn-programa-guardar">
                                        <span class="fa fa-save"></span> Guardar datos del Programa Presupuestario
                                    </button>-->
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="diagnostico">
                            <br>
                            <div class="panel panel-primary" id="datagridProblemas">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <form id="form_problema">
                                            <div class="form-group">
                                                <label class="control-label" for="descripcion-problema">Árbol del Problema</label>
                                                <textarea class="form-control" id="descripcion-problema" name="descripcion-problema" rows="5"></textarea>
                                            </div>
                                            </form>
                                        </div>
                                        <div class="col-sm-6">
                                        	<button type="button" class="btn btn-default" id="btnarbolproblema" onclick="escribirComentario('arbolproblema','Árbol del problema','descripcion-problema')" ><i class="fa fa-pencil-square-o"></i> Comentar Árbol del Problema</button>
                                            <!--<button id="btn-guardar-problema" type="button" class="btn btn-info">
                                                <span class="fa fa-save"></span> Guardar Descripción
                                            </button>--> 
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="btn-toolbar pull-right" >

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <table class="table table-striped table-hover" id="tablaCausasEfectos" name="tablaCausasEfectos">
                                    <thead>
                                        <tr>
                                            <th>Causas</th>
                                            <th>Efectos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    
                                    </tbody>
                                </table>
                            </div>
                            <div class="panel panel-primary" id="datagridObjetivos">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <form id="form_objetivo">
                                            <div class="form-group">
                                                <label class="control-label" for="descripcion-objetivo">Árbol de Objetivos</label>
                                                <textarea class="form-control" id="descripcion-objetivo" name="descripcion-objetivo" rows="5"></textarea>
                                            </div>
                                            </form>
                                        </div>
                                        <div class="col-sm-6">
                 	                       <button type="button" class="btn btn-default" id="btnarbolobjetivo" onclick="escribirComentario('arbolobjetivo','Árbol de objetivos','descripcion-objetivo')" ><i class="fa fa-pencil-square-o"></i> Comentar Árbol de Objetivos</button>
                                            <!--<button id="btn-guardar-objetivo" type="button" class="btn btn-info">
                                                <span class="fa fa-save"></span> Guardar Descripción
                                            </button>-->
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="btn-toolbar pull-right" >
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <table class="table table-striped table-hover" id="tablaMediosFines" name="tablaMediosFines">
                                    <thead>
                                        <tr>
                                            <th>Medios</th>
                                            <th>Fines</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="indicadores">
                            <br>
                            <div role="tabpanel">
                            	<ul class="nav nav-tabs" role="tablist">
                                	<li role="presentation" class="active">
                                    	<a href="#panel-fin" aria-controls="panel-fin" role="tab" data-toggle="tab">
                                        	Tipo de Indicador: Fin
                                        </a>
                                    </li>
                                    <li role="presentation">
                                    	<a href="#panel-proposito" aria-controls="panel-proposito" role="tab" data-toggle="tab">
                                        	Tipo de Indicador: Propósito
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                	<div role="tabpanel" class="tab-pane active" id="panel-fin">
                                    	<br />
                                        <div class="row">
                                        	<div class="col-sm-12 bg-info">
                                            <strong><span class="fa fa-crosshairs"></span> Objetivo</strong>
                                            </div>
											<div class="col-sm-12">
												<div class="form-group">
											    	<label for="lbl-descripcion-obj-F" class="control-label">Descripción del Objetivo</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('descripcion-obj-F','Descripción del Objetivo','lbl-descripcion-obj-F');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-descripcion-obj-F" name="lbl-descripcion-obj-F" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
												<div class="form-group">
											    	<label for="lbl-ambito-F" class="control-label">Ámbito</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('ambito-F','Ámbito','lbl-ambito-F');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-ambito-F" name="lbl-ambito-F" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
												<div class="form-group">
											    	<label for="lbl-verificacion-F" class="control-label">Medios de verificación</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('verificacion-F','Medios de Verificación','lbl-verificacion-F');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-verificacion-F" name="lbl-verificacion-F" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
												<div class="form-group">
											    	<label for="lbl-supuestos-F" class="control-label">Supuestos</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('supuestos-F','Supuestos','lbl-supuestos-F');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-supuestos-F" name="lbl-supuestos-F" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 bg-info">
                                            <strong><span class="fa fa-line-chart"></span> Indicador</strong>
                                            </div>
                                            <div class="col-sm-12">
												<div class="form-group">
											    	<label for="lbl-descripcion-ind-F" class="control-label">Descripción del Indicador</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('descripcion-ind-F','Descripción del Indicador','lbl-descripcion-ind-F');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-descripcion-ind-F" name="lbl-descripcion-ind-F" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
												<div class="form-group">
											    	<label for="lbl-numerador-ind-F" class="control-label">Numerador</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('numerador-ind-F','Numerador','lbl-numerador-ind-F');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-numerador-ind-F" name="lbl-numerador-ind-F" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
												<div class="form-group">
											    	<label for="lbl-denominador-ind-F" class="control-label">Denominador</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('denominador-ind-F','Denominador','lbl-denominador-ind-F');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-denominador-ind-F" name="lbl-denominador-ind-F" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
												<div class="form-group">
											    	<label for="lbl-interpretacion-ind-F" class="control-label">Interpretación</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('interpretacion-ind-F','Interpretación','lbl-interpretacion-ind-F');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-interpretacion-ind-F" name="lbl-interpretacion-ind-F" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
												<div class="form-group">
											    	<label for="lbl-dimension-F" class="control-label">Dimensión</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('dimension-F','Dimensión','lbl-dimension-F');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-dimension-F" name="lbl-dimension-F" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
												<div class="form-group">
											    	<label for="lbl-tipo-ind-F" class="control-label">Tipo</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('tipo-ind-F','Tipo','lbl-tipo-ind-F');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-tipo-ind-F" name="lbl-tipo-ind-F" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-5">
												<div class="form-group">
											    	<label for="lbl-unidad-medida-F" class="control-label">Unidad de medida</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('unidad-medida-F','Unidad de Medida','lbl-unidad-medida-F');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-unidad-medida-F" name="lbl-unidad-medida-F" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 bg-info">
                                            <strong><span class="fa fa-table"></span> Metas</strong>
                                            </div>
                                            <div class="col-sm-2">
												<div class="form-group">
											    	<label for="lbl-linea-base-F" class="control-label">Línea Base</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('linea-base-F','Línea Base','lbl-linea-base-F');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-linea-base-F" name="lbl-linea-base-F" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
												<div class="form-group">
											    	<label for="lbl-anio-base-F" class="control-label">Año Base</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('anio-base-F','Año Base','lbl-anio-base-F');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-anio-base-F" name="lbl-anio-base-F" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
												<div class="form-group">
											    	<label for="lbl-formula-F" class="control-label">Fórmula</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('formula-F','Fórmula','lbl-formula-F');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-formula-F" name="lbl-formula-F" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
												<div class="form-group">
											    	<label for="lbl-frecuencia-F" class="control-label">Frecuencia</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('frecuencia-F','Frecuencia','lbl-frecuencia-F');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-frecuencia-F" name="lbl-frecuencia-F" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>                                            
                                            <div class="col-sm-3"><div class="form-group">
											    	<label for="lbl-trim1-F" class="control-label">Trim 1</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('trim1-F','Trim 1','lbl-trim1-F');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-trim1-F" name="lbl-trim1-F" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                            </div></div>
											<div class="col-sm-3"><div class="form-group">
											    	<label for="lbl-trim2-F" class="control-label">Trim 2</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('trim2-F','Trim 2','lbl-trim2-F');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-trim2-F" name="lbl-trim2-F" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                            </div></div>
                                            <div class="col-sm-3"><div class="form-group">
											    	<label for="lbl-trim3-F" class="control-label">Trim 3</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('trim3-F','Trim 3','lbl-trim3-F');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-trim3-F" name="lbl-trim3-F" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                            </div></div>
                                            <div class="col-sm-3"><div class="form-group">
											    	<label for="lbl-trim4-F" class="control-label">Trim 4</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('trim4-F','Trim 4','lbl-trim4-F');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-trim4-F" name="lbl-trim4-F" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div> 
                                            </div></div>
                                            <div class="col-sm-4"><div class="form-group">
											    	<label for="lbl-numerador-F" class="control-label">Numerador</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('numerador-F','Numerador','lbl-numerador-F');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-numerador-F" name="lbl-numerador-F" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div> 
                                            </div></div>
                                            <div class="col-sm-4"><div class="form-group">
											    	<label for="lbl-denominador-F" class="control-label">Denominador</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('denominador-F','Denominador','lbl-denominador-F');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-denominador-F" name="lbl-denominador-F" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div> 
                                            </div></div>
                                            <div class="col-sm-4"><div class="form-group">
											    	<label for="lbl-meta-F" class="control-label">Meta</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('meta-F','Meta','lbl-meta-F');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-meta-F" name="lbl-meta-F" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div> 
                                            </div></div>
										</div>
                                    </div> 
                                    <div role="tabpanel" class="tab-pane" id="panel-proposito">
                                    	<br />
                                        <div class="row">
                                        	<div class="col-sm-12 bg-info">
                                            <strong><span class="fa fa-crosshairs"></span> Objetivo</strong>
                                            </div>
											<div class="col-sm-12">
												<div class="form-group">
											    	<label for="lbl-descripcion-obj-P" class="control-label">Descripción del Objetivo</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('descripcion-obj-P','Descripción del Objetivo','lbl-descripcion-obj-P');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-descripcion-obj-P" name="lbl-descripcion-obj-P" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
												<div class="form-group">
											    	<label for="lbl-ambito-P" class="control-label">Ámbito</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('ambito-P','Ámbito','lbl-ambito-P');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-ambito-P" name="lbl-ambito-P" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
												<div class="form-group">
											    	<label for="lbl-verificacion-P" class="control-label">Medios de verificación</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('verificacion-P','Medios de Verificación','lbl-verificacion-P');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-verificacion-P" name="lbl-verificacion-P" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
												<div class="form-group">
											    	<label for="lbl-supuestos-P" class="control-label">Supuestos</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('supuestos-P','Supuestos','lbl-supuestos-P');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-supuestos-P" name="lbl-supuestos-P" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-sm-12 bg-info">
                                            <strong><span class="fa fa-line-chart"></span> Indicador</strong>
                                            </div>
                                            <div class="col-sm-12">
												<div class="form-group">
											    	<label for="lbl-descripcion-ind-P" class="control-label">Descripción del Indicador</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('descripcion-ind-P','Descripción del Indicador','lbl-descripcion-ind-P');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-descripcion-ind-P" name="lbl-descripcion-ind-P" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
												<div class="form-group">
											    	<label for="lbl-numerador-ind-P" class="control-label">Numerador</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('numerador-ind-P','Numerador','lbl-numerador-ind-P');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-numerador-ind-P" name="lbl-numerador-ind-P" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
												<div class="form-group">
											    	<label for="lbl-denominador-ind-P" class="control-label">Denominador</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('denominador-ind-P','Denominador','lbl-denominador-ind-P');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-denominador-ind-P" name="lbl-denominador-ind-P" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
												<div class="form-group">
											    	<label for="lbl-interpretacion-ind-P" class="control-label">Interpretación</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('interpretacion-ind-P','Interpretación','lbl-interpretacion-ind-P');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-interpretacion-ind-P" name="lbl-interpretacion-ind-P" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
												<div class="form-group">
											    	<label for="lbl-dimension-P" class="control-label">Dimensión</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('dimension-P','Dimensión','lbl-dimension-P');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-dimension-P" name="lbl-dimension-P" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
												<div class="form-group">
											    	<label for="lbl-tipo-ind-P" class="control-label">Tipo</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('tipo-ind-P','Tipo','lbl-tipo-ind-P');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-tipo-ind-P" name="lbl-tipo-ind-P" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-5">
												<div class="form-group">
											    	<label for="lbl-unidad-medida-P" class="control-label">Unidad de medida</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('unidad-medida-P','Unidad de Medida','lbl-unidad-medida-P');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-unidad-medida-P" name="lbl-unidad-medida-P" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-sm-12 bg-info">
                                            <strong><span class="fa fa-table"></span> Metas</strong>
                                            </div>
                                                                                        
                                            <div class="col-sm-2">
												<div class="form-group">
											    	<label for="lbl-linea-base-P" class="control-label">Línea Base</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('linea-base-P','Línea Base','lbl-linea-base-P');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-linea-base-P" name="lbl-linea-base-P" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
												<div class="form-group">
											    	<label for="lbl-anio-base-P" class="control-label">Año Base</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('anio-base-P','Año Base','lbl-anio-base-P');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-anio-base-P" name="lbl-anio-base-P" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
												<div class="form-group">
											    	<label for="lbl-formula-P" class="control-label">Fórmula</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('formula-P','Fórmula','lbl-formula-P');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-formula-P" name="lbl-formula-P" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
												<div class="form-group">
											    	<label for="lbl-frecuencia-P" class="control-label">Frecuencia</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('frecuencia-P','Frecuencia','lbl-frecuencia-P');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-frecuencia-P" name="lbl-frecuencia-P" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-sm-3"><div class="form-group">
											    	<label for="lbl-trim1-P" class="control-label">Trim 1</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('trim1-P','Trim 1','lbl-trim1-P');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-trim1-P" name="lbl-trim1-P" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                            </div></div>
											<div class="col-sm-3"><div class="form-group">
											    	<label for="lbl-trim2-P" class="control-label">Trim 2</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('trim2-P','Trim 2','lbl-trim2-P');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-trim2-P" name="lbl-trim2-P" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                            </div></div>
                                            <div class="col-sm-3"><div class="form-group">
											    	<label for="lbl-trim3-P" class="control-label">Trim 3</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('trim3-P','Trim 3','lbl-trim3-P');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-trim3-P" name="lbl-trim3-P" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div>
                                            </div></div>
                                            <div class="col-sm-3"><div class="form-group">
											    	<label for="lbl-trim4-P" class="control-label">Trim 4</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('trim4-P','Trim 4','lbl-trim4-P');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-trim4-P" name="lbl-trim4-P" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div> 
                                            </div></div>

                                            <div class="col-sm-4"><div class="form-group">
											    	<label for="lbl-numerador-P" class="control-label">Numerador</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('numerador-P','Numerador','lbl-numerador-P');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-numerador-P" name="lbl-numerador-P" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div> 
                                            </div></div>
                                            <div class="col-sm-4"><div class="form-group">
											    	<label for="lbl-denominador-P" class="control-label">Denominador</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('denominador-P','Denominador','lbl-denominador-P');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-denominador-P" name="lbl-denominador-P" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div> 
                                            </div></div>
                                            <div class="col-sm-4"><div class="form-group">
											    	<label for="lbl-meta-P" class="control-label">Meta</label>
                                                    <div class="input-group">
									                   	<span class="input-group-btn" onclick="escribirComentario('meta-P','Meta','lbl-meta-P');">
            			           						<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span>
                        			                    <p id="lbl-meta-P" name="lbl-meta-P" class="form-control" style="height:auto">&nbsp;</p>
                                    			    </div> 
                                            </div></div>

										</div> <!--ROW-->
                                    </div> <!--Panel Proposito-->
                                </div> <!--Tab-Content-->
                            </div> <!--TabPanel-->
                        </div> <!--TabPanel Indicadores-->
                        <div role="tabpanel" class="tab-pane" id="datos-informacion">
	                        <br>
                        
    	                    <div class="panel panel-default">
        	                    <div class="panel-heading">
            	                    Información de la Programación de Metas por Trimestre
                	            </div>
                    	        <div class="panel-body">
	                                <form id="form_fuente_informacion">
    	                                <div class="row">
        	                                <div class="col-sm-6">
            	                                <div class="form-group">
                	                                <label class="control-label" for="fuente-informacion">Fuente de Información</label>
                    	                            <div class="input-group">
                        	                        	<span class="input-group-btn" onclick="escribirComentario('fuente-informacion','Fuente de Información','lbl-fuente-informacion');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span>
                            	                        <p id="lbl-fuente-informacion" class="form-control" style="height:auto">&nbsp;</p>
                                	                </div>
                                    	        </div>
                                        	</div>
	                                        <div class="col-sm-6">
    	                                        <div class="form-group">
        	                                    	<div class="form-group">
    	    	                                        <label class="control-label" for="responsable">Responsable</label>
                	                                    <div class="input-group">
    	            	                                	<span class="input-group-btn" onclick="escribirComentario('responsable','Responsable','lbl-responsable');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span>
															<p id="lbl-responsable" class="form-control" style="height:auto">&nbsp;</p>    	                                                 </div>
        	                                            <span id="ayuda-responsable" class="help-block"></span>
	        	                                    </div>
	                                            </div>
	                                        </div>                                            
	                                    </div>
	                                </form>
	                            </div>
                                <div class="panel-footer">
                                                                
	                            </div>
    	                   	</div>
    	                </div>
                    </div>
                </div>
                
                <div class="panel-body" id="mensajes-sin-duenio">
	                <div class="panel panel-warning">
						<div class="panel-heading">
        	            	Mensajes anteriores de elementos que fueron borrados (verificar que se hayan atendido para descartarlos).
            	        </div>
                	    <div class="panel-body">
                    	    <div id="elementos-borrados"></div>
						</div>
    	            </div>
                </div>
                
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-default" id="btn-programa-cancelar">
                            <span class="fa fa-chevron-left"></span> Regresar a la lista de Programas
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
                	<input type="hidden" name="idprogramacomentarios" id="idprogramacomentarios">
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