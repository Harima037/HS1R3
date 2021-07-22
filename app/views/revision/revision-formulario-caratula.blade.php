<form action="" id="form_caratula">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
            	<div class="form-group">
        	        <label class="control-label">Lider del Proyecto</label>
    	            <p id="lbl-lider-proyecto" class="form-control-static" style="height:auto"></p>
	            </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label class="control-label">Jefe Inmediato al Lider</label>
                <p id="lbl-jefe-inmediato" class="form-control-static" style="height:auto"></p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label class="control-label">Jefe de Planeación</label>
                <p id="lbl-jefe-planeacion" class="form-control-static" style="height:auto"></p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label class="control-label">Coordinador del Grupo Estratégico</label>
                <p id="lbl-coordinador-grupo" class="form-control-static" style="height:auto"></p>
            </div>
        </div>
    </div>
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group">
            	<label class="control-label" for="lbl-nombretecnico">Nombre Técnico</label>
                <div class="input-group">
                   	<span class="input-group-btn" onclick="escribirComentario('nombretecnico','Nombre técnico','lbl-nombretecnico');">
                       	<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
                   	</span>
                    <p id="lbl-nombretecnico" class="form-control" style="height:auto"></p>
				</div>
			</div>
		</div>
		<div class="col-sm-4">			
            <div class="form-group">
				<label class="control-label" for="lbl-tipoaccion">Tipo de Acción</label>
                <div class="input-group">
                   	<span class="input-group-btn" onclick="escribirComentario('tipoaccion','Tipo de acción','lbl-tipoaccion');">
                       	<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
                   	</span>
                    <p id="lbl-tipoaccion" class="form-control" style="height:auto"></p>
				</div>
				
			</div>
		</div>
        <div class="col-sm-2">
            <div class="form-group">
                <label class="control-label" for="ejercicio">Ejercicio</label>
                <div class="input-group">
                   	<span class="input-group-btn" onclick="escribirComentario('ejercicio','Ejercicio','lbl-ejercicio');">
                       	<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
                   	</span>
                    <p id="lbl-ejercicio" class="form-control" style="height:auto"></p>
				</div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="control-label" for="fechainicio">Fecha Inicio</label>
                        <div class="input-group">
                            <span class="input-group-btn" onclick="escribirComentario('fechainicio','Fecha Inicio','lbl-fechainicio');">
                                <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
                            </span>
                            <p id="lbl-fechainicio" class="form-control" style="height:auto"></p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="control-label" for="fechatermino">Fecha Termino</label>
                        <div class="input-group">
                            <span class="input-group-btn" onclick="escribirComentario('fechatermino','Fecha Termino','lbl-fechatermino');">
                                <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
                            </span>
                            <p id="lbl-fechatermino" class="form-control" style="height:auto"></p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="control-label" for="tipoproyecto">Tipo de Proyecto</label>
                        <div class="input-group">
                            <span class="input-group-btn" onclick="escribirComentario('tipoproyecto','Fecha Termino','lbl-tipoproyecto');">
                                <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
                            </span>
                            <p id="lbl-tipoproyecto" class="form-control" style="height:auto"></p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label" for="lbl-cobertura">Cobertura</label>
                        <div class="input-group">
                            <span class="input-group-btn" onclick="escribirComentario('cobertura','Cobertura','lbl-cobertura');">
                                <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
                            </span>
                            <p id="lbl-cobertura" class="form-control" style="height:auto"></p>
                        </div>
                                        
                    </div>
                </div>
                <div class="col-sm-6">
                    <div id="select-estado-panel" class="form-group">
                        <label class="control-label" for="lbl-estado">Estado</label>
                        <div class="input-group">
                            <span class="input-group-btn" onclick="escribirComentario('estado','Estado','lbl-estado');">
                                <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
                            </span>
                            <p id="lbl-estado" class="form-control" style="height:auto">Chiapas</p>
                        </div>                
                    </div>
                    <div id="select-municipio-panel" class="form-group">
                        <label class="control-label" for="lbl-municipio">Municipio</label>
                        <div class="input-group">
                            <span class="input-group-btn" onclick="escribirComentario('municipio','Municipio','lbl-municipio');">
                                <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
                            </span>
                            <p id="lbl-municipio" class="form-control" style="height:auto"></p>
                        </div>                 
                    </div>
                    <div id="select-region-panel" class="form-group">
                        <label class="control-label" for="lbl-region">Región</label>
                        <div class="input-group">
                            <span class="input-group-btn" onclick="escribirComentario('region','Región','lbl-region');">
                                <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
                            </span>
                            <p id="lbl-region" class="form-control" style="height:auto"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label class="control-label" for="finalidadproyecto">Finalidad del Proyecto</label>
                <div class="input-group">
                   	<span class="input-group-btn" onclick="escribirComentario('finalidadproyecto','Fecha Termino','lbl-finalidadproyecto');">
                       	<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
                   	</span>
                    <p id="lbl-finalidadproyecto" class="form-control" style="height:auto"></p>
				</div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-sm-12">
                            <b>Clave Presupuestaria</b> <br>
                            <strong>
                                <kbd>
                                    <kbd id="unidad_responsable" title="Unidad Responsable">--</kbd>
                                    <kbd id="finalidad" title="Finalidad">-</kbd>
                                    <kbd id="funcion" title="Función">-</kbd>
                                    <kbd id="subfuncion" title="Subfunción">-</kbd>
                                    <kbd id="subsubfuncion" title="SubSubfunción">-</kbd>
                                    <kbd id="programa_sectorial" title="Programa Sectorial">-</kbd>
                                    <kbd id="programa_presupuestario" title="Programa Presupuestario">---</kbd>
                                    <kbd id="origen_asignacion" title="Origen Asignación">--</kbd>
                                    <kbd id="actividad_institucional" title="Actividad Institucional">---</kbd>
                                    <kbd id="proyecto_estrategico" title="Proyecto Estratégico">-</kbd>
                                    <kbd id="no_proyecto_estrategico" title="Número de Proyecto Estratégico">000</kbd>
                                </kbd>
                            </strong>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="lbl-unidadresponsable">Unidad Responsable</label>
                            <div class="input-group">
			                   	<span class="input-group-btn" onclick="escribirComentario('unidadresponsable','Unidad Responsable','lbl-unidadresponsable');">
		    	                   	<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
			                   	</span>
				                <p id="lbl-unidadresponsable" class="form-control" style="height:auto"></p>
							</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="funciongasto">
                                Funcion del Gasto (Finalidad,Funcion,Sub Funcion,Sub Sub Funcion)
                            </label>
	                        <div class="input-group">
    			               	<span class="input-group-btn" onclick="escribirComentario('funciongasto','Funcion del Gasto (Finalidad,Funcion,Sub Funcion,Sub Sub Funcion)','lbl-funciongasto');">
			                       	<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
            			       	</span>
			                    <p id="lbl-funciongasto" class="form-control" style="height:auto"></p>
							</div>                           
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="lbl-programasectorial">Programa Sectorial</label>
                            <div class="input-group" onclick="escribirComentario('programasectorial','Programa Sectorial','lbl-programasectorial');">
			                  	<span class="input-group-btn">
            			           	<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
			                   	</span>
            			        <p id="lbl-programasectorial" class="form-control" style="height:auto"></p>
							</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="lbl-programapresupuestario">Programa Presupuestario</label>
                            <div class="input-group">
			                   	<span class="input-group-btn" onclick="escribirComentario('programapresupuestario','Programa Presupuestario','lbl-programapresupuestario');">
			                       	<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
            			       	</span>
			                    <p id="lbl-programapresupuestario" class="form-control" style="height:auto"></p>
							</div>
                         </div>
                    </div>
                    <div id="panel-programa-seleccionado" class="col-sm-12 panel panel-primary" style="display: none; padding-left:0px; padding-right:0px;">
                        <table id="tabla-indicadores-programa-presupuestario" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="text-align:center;" colspan="3"><span id="titulo-programa-presupuestario">Programa Presupuestario</span></th>
                                </tr>
                                <tr>
                                    <th width="1">Tipo</th>
                                    <th>Indicador</th>
                                    <th>Unidad de Medida</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="lbl-origenasignacion">Origen Asignación</label>
                            <div class="input-group">
        			           	<span class="input-group-btn" onclick="escribirComentario('origenasignacion','Origen Asignación','lbl-origenasignacion');">
			                       	<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
            			       	</span>
			                    <p id="lbl-origenasignacion" class="form-control" style="height:auto"></p>
							</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="lbl-actividadinstitucional">Actividades Institucionales</label>
                            <div class="input-group">
			                   	<span class="input-group-btn" onclick="escribirComentario('actividadinstitucional','Actividades Institucionales','lbl-actividadinstitucional');">
            			           	<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
			                   	</span>
            			        <p id="lbl-actividadinstitucional" class="form-control" style="height:auto"></p>
							</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="lbl-proyectoestrategico">Proyecto Estratégico</label>
                            <div class="input-group">
			                   	<span class="input-group-btn" onclick="escribirComentario('proyectoestrategico','Proyecto Estratégico','lbl-proyectoestrategico');">
		    	                   	<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
			                   	</span>
                			    <p id="lbl-proyectoestrategico" class="form-control" style="height:auto"></p>
							</div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="numeroproyectoestrategico">Número de Proyecto Estratégico</label>
                            <div class="input-group">
			                   	<span class="input-group-btn" onclick="escribirComentario('numeroproyectoestrategico','Número de Proyecto Estratégico','lbl-numeroproyectoestrategico');">
            			           	<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
			                   	</span>
            			        <p class="form-control" id="lbl-numeroproyectoestrategico" style="height:auto">000</p>
							</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label class="control-label" for="lbl-estrategiapnd">Estrategia del Objetivo del Plan Nacional</label>
                <div class="input-group">
                   	<span class="input-group-btn" onclick="escribirComentario('estrategiapnd','Estrategia del Objetivo del Plan Nacional','lbl-estrategiapnd');">
                       	<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
                   	</span>
                    <p id="lbl-estrategiapnd" class="form-control" style="height:auto"></p>
				</div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label class="control-label" for="lbl-objetivoestrategico">Objetivo Estrategico</label>
                <div class="input-group">
                   	<span class="input-group-btn" onclick="escribirComentario('objetivoestrategico','Objetivo Estrategico','lbl-objetivoestrategico');">
                       	<span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
                   	</span>
                    <p id="lbl-objetivoestrategico" class="form-control" style="height:auto"></p>
				</div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label" for="lbl-alineacion">Alineación</label>
                        <div class="input-group">
                            <span class="input-group-btn" onclick="escribirComentario('alineacion','Alineación','lbl-alineacion');">
                                <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
                            </span>
                            <p id="lbl-alineacion" class="form-control" style="height:auto"></p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-9">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label" for="lbl-estrategiaestatal">Estrategia del Plan Estatal</label>
                                <div class="input-group">
                                    <span class="input-group-btn" onclick="escribirComentario('estrategiaestatal','Estrategia del Plan Estatal','lbl-estrategiaestatal');">
                                        <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
                                    </span>
                                    <p id="lbl-estrategiaestatal" class="form-control" style="height:auto"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label" for="lbl-vinculacionped">Objetivo del Plan Estatal</label>
                                <div class="input-group">
                                    <span class="input-group-btn" onclick="escribirComentario('vinculacionped','Objetivo del Plan Estatal','lbl-vinculacionped');">
                                        <span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span>
                                    </span>
                                    <p id="lbl-vinculacionped" class="form-control" style="height:auto"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="id" name="id" value="{{{ $id or '' }}}">
    <input type="hidden" id="id-fibap" name="id-fibap" value="{{{ $fibap_id or '' }}}">
    <input type="hidden" id="clasificacionproyecto" name="clasificacionproyecto" value="{{$clasificacion_proyecto_id}}">
    <input type="hidden" id="tipoproyecto" name="tipoproyecto" value="{{$tipo_proyecto_id}}">
</form>