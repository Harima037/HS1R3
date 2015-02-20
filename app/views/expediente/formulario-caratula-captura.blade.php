<form action="" id="form_caratula">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label class="control-label">Lider del Proyecto</label>
                <p id="lbl-lider-proyecto" class="form-control-static">{{$firmas['LiderProyecto']->nombre}}</p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label class="control-label">Jefe Inmediato al Lider</label>
                <p id="lbl-jefe-inmediato" class="form-control-static">{{$firmas['JefeInmediato']->nombre}}</p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label class="control-label">Jefe de Planeación</label>
                <p id="lbl-jefe-planeacion" class="form-control-static">{{$firmas['JefePlaneacion']->nombre}}</p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label class="control-label">Coordinador del Grupo Estratégico</label>
                <p id="lbl-coordinador-grupo" class="form-control-static">{{$firmas['CoordinadorGrupo']->nombre}}</p>
            </div>
        </div>
    </div>
	<div class="row">
		<div class="col-sm-6">
			<div class="form-group">
				<label class="control-label" for="nombretecnico">Nombre Técnico</label>
				<input type="text" class="form-control" name="nombretecnico" id="nombretecnico"/>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="form-group">
				<label class="control-label" for="tipoaccion">Tipo de Acción</label>
				{{Form::select('tipoaccion',$tipos_acciones->lists('descripcion','id'),7,array('class'=>'form-control chosen-one','id'=>'tipoaccion'))}}
			</div>
		</div>
        <div class="col-sm-2">
            <div class="form-group">
                <label class="control-label" for="ejercicio">Ejercicio</label>
                <input type="text" class="form-control" name="ejercicio" id="ejercicio"/>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label class="control-label" for="vinculacionped">Vinculación al PED (Plan Estatal de Desarrollo)</label>
                <select class="form-control chosen-one" id="vinculacionped" name="vinculacionped">
                    <option value="">Seleciona un objetivo</option>
                    <!-- Inicio de ejes -->
                    @foreach ($objetivos_ped as $eje)
                        @if(count($eje->hijos))
                            <optgroup label="{{$eje->clave . ' ' . $eje->descripcion}}">
                                <!-- Inicio de temas -->
                            @foreach ($eje->hijos as $tema)
                                @if(count($tema->hijos))
                                    <option disabled="disabled">
                                        {{$tema->clave . ' ' . $tema->descripcion}}
                                    </option>
                                    <!-- Inicio de politicas -->
                                    @foreach ($tema->hijos as $politica)
                                        @if(count($politica->hijos))
                                            <option disabled="disabled">
                                                {{$politica->clave . ' ' . $politica->descripcion}}
                                            </option>
                                            <!-- Inicio de objetivos -->    
                                            @foreach ($politica->hijos as $objetivo)
                                                <option value="{{$objetivo->id}}">
                                                    {{$objetivo->clave . ' ' . $objetivo->descripcion}}
                                                </option>
                                            @endforeach
                                            <!-- Inicio de objetivos -->
                                            <option data-divider="true"></option>
                                        @endif
                                    @endforeach
                                    <!-- Fin de politicas -->
                                @endif
                            @endforeach
                            <!-- Fin de temas -->
                            </optgroup>
                        @endif
                    @endforeach
                    <!-- Fin de ejes -->
                </select>
            </div>
        </div>
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
                                    <kbd id="programa_especial" title="Programa Especial">---</kbd>
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
                            <label class="control-label" for="unidadresponsable">Unidad Responsable</label>
                            {{Form::select('unidadresponsable',array('' =>'Selecciona una unidad') + $unidades_responsables->lists('descripcion','clave'),0,array('class'=>'form-control chosen-one','id'=>'unidadresponsable'))}}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="funciongasto">
                                Finalidad - Funcion - SubFuncion - SubSubFuncion
                            </label>
                            <select class="form-control chosen-one" id="funciongasto" name="funciongasto">
                                <option value="">Seleciona una función</option>
                                @foreach ($funciones_gasto as $finalidad)
                                    @if(count($finalidad->hijos))
                                        <optgroup label="{{$finalidad->clave . ' ' . $finalidad->descripcion}}">

                                            @foreach ($finalidad->hijos as $funcion)
                                                @if(count($funcion->hijos))
                                                    <option disabled="disabled">
                                                        {{$funcion->clave . ' ' . $funcion->descripcion}}
                                                    </option>

                                                    @foreach ($funcion->hijos as $subfuncion)
                                                        @if(count($subfuncion->hijos))
                                                            <option disabled="disabled">
                                                                {{$subfuncion->clave . ' ' . $subfuncion->descripcion}}
                                                            </option>

                                                            @foreach ($subfuncion->hijos as $subsubfuncion)
                                                                <option value="{{$subsubfuncion->clave}}">
                                                                    {{$subsubfuncion->clave . ' ' . $subsubfuncion->descripcion}}
                                                                </option>
                                                            @endforeach
                                                            <!--/optgroup-->
                                                        @endif
                                                    @endforeach
                                                    <!--/optgroup-->
                                                @endif
                                            @endforeach

                                        </optgroup>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="programasectorial">Programa Sectorial</label>
                            {{Form::select('programasectorial',array('' =>'Selecciona un programa sectorial') + $programas_sectoriales->lists('descripcion','clave'),0,array('class'=>'form-control chosen-one','id'=>'programasectorial'))}}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="programapresupuestario">Programa Presupuestario</label>
                            {{Form::select('programapresupuestario',array('' =>'Selecciona un programa presupuestario') + $programas_presupuestarios->lists('descripcion','clave'),0,array('class'=>'form-control chosen-one','id'=>'programapresupuestario'))}}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="programaespecial">Programa Especial</label>
                            {{Form::select('programaespecial',array('' =>'Selecciona un programa especial') + $programas_especiales->lists('descripcion','clave'),'',array('class'=>'form-control chosen-one','id'=>'programaespecial'))}}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="actividadinstitucional">Actividades Institucionales</label>
                            {{Form::select('actividadinstitucional',array('' =>'Selecciona una actividad institucional') + $actividades_institucionales->lists('descripcion','clave'),'',array('class'=>'form-control chosen-one','id'=>'actividadinstitucional'))}}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="proyectoestrategico">Proyecto Estratégico</label>
                            {{Form::select('proyectoestrategico',array('' =>'Selecciona un proyecto estratégico') + $proyectos_estrategicos->lists('descripcion','clave'),'',array('class'=>'form-control chosen-one','id'=>'proyectoestrategico'))}}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="numeroproyectoestrategico">Número de Proyecto Estratégico</label>
                            <p class="form-control-static" id="numeroproyectoestrategico">000</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label class="control-label" for="cobertura">Cobertura</label>
                {{Form::select('cobertura',array('' =>'Selecciona una cobertura') + $coberturas->lists('descripcion','id'),'',array('class'=>'form-control chosen-one','id'=>'cobertura'))}}
            </div>
        </div>
        <div class="col-sm-4">
            <div id="select-estado-panel" class="form-group">
                <label class="control-label">Estado</label>
                <p class="form-control-static">Chiapas</p>
            </div>
            <div id="select-municipio-panel" class="form-group">
                <label class="control-label" for="municipio">Municipio</label>
                {{Form::select('municipio',array('' =>'Selecciona un municipio') + $municipios->lists('nombre','clave'),'',array('class'=>'form-control chosen-one','id'=>'municipio'))}}
            </div>
            <div id="select-region-panel" class="form-group">
                <label class="control-label" for="region">Región</label>
                {{Form::select('region',array('' =>'Selecciona una región') + $regiones->lists('nombre','region'),'',array('class'=>'form-control chosen-one','id'=>'region'))}}
            </div>
        </div>
    </div>
    <input type="hidden" id="id" name="id" value="{{{ $id or '' }}}">
    <!--input type="hidden" id="id-fibap" name="id-fibap" value="{d{s{ $fibap_id or '' }d}d}"-->
    <input type="hidden" id="clasificacionproyecto" name="clasificacionproyecto" value="{{$clasificacion_proyecto_id}}">
    <input type="hidden" id="tipoproyecto" name="tipoproyecto" value="{{$tipo_proyecto_id}}">
    <div class="row">
        <div class="col-sm-12">
            <button type="button" class="btn btn-primary pull-right" id="btn-proyecto-guardar">
                <span class="fa fa-save"></span> Guardar datos del Proyecto
            </button>
        </div>
    </div>
</form>