<form action="" id="form_caratula">
	<div class="row">
		<div class="col-sm-8">
			<div class="form-group">
				<label class="control-label" for="nombretecnico">Nombre Técnico</label>
				<input type="text" class="form-control" name="nombretecnico" id="nombretecnico"/>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="form-group">
				<label class="control-label" for="tipoaccion">Tipo de Acción</label>
				{{Form::select('tipoaccion',array('' =>'Selecciona un tipo de acción') + $tipos_acciones->lists('descripcion','id'),0,array('class'=>'form-control selectpicker','id'=>'tipoaccion'))}}
			</div>
		</div>
        <div class="col-sm-12">
            <div class="form-group">
                <label class="control-label" for="vinculacionped">Vinculación al PED (Plan Estatal de Desarrollo)</label>
                <select class="form-control selectpicker" id="vinculacionped" name="vinculacionped" data-live-search="true">
                    <option value="">Seleciona un objetivo</option>
                    <!-- Inicio de ejes -->
                    @foreach ($objetivos_ped as $eje)
                        @if(count($eje->hijos))
                            <optgroup label="{{$eje->clave . ' ' . $eje->descripcion}}">
                                <!-- Inicio de temas -->
                                @foreach ($eje->hijos as $tema)
                                    @if(count($tema->hijos))
                                        <optgroup label="{{$tema->clave . ' ' . $tema->descripcion}}">
                                            <!-- Inicio de politicas -->
                                            @foreach ($tema->hijos as $politica)
                                                @if(count($politica->hijos))
                                                    <optgroup label="{{$politica->clave . ' ' . $politica->descripcion}}">
                                                        <!-- Inicio de objetivos -->    
                                                        @foreach ($politica->hijos as $objetivo)
                                                            <option value="{{$objetivo->id}}">
                                                                {{$objetivo->clave . ' ' . $objetivo->descripcion}}
                                                            </option>
                                                        @endforeach
                                                        <!-- Inicio de objetivos -->
                                                    </optgroup>
                                                @endif
                                            @endforeach
                                            <!-- Fin de politicas -->
                                        </optgroup>
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
                        <div class="col-sm-4">
                            <b>Clave Presupuestaria</b> 
                        </div>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span id="unidad_responsable" class="input-group-addon" title="Unidad Responsable">
                                    --
                                </span>
                                <span id="finalidad" class="input-group-addon" title="Finalidad">
                                    -
                                </span>
                                <span id="funcion" class="input-group-addon" title="Función">
                                    -
                                </span>
                                <span id="subfuncion" class="input-group-addon" title="Subfunción">
                                    -
                                </span>
                                <span id="subsubfuncion" class="input-group-addon" title="SubSubfunción">
                                    -
                                </span>
                                <span id="programa_sectorial" class="input-group-addon" title="Programa Sectorial">
                                    -
                                </span>
                                <span id="programa_presupuestario" class="input-group-addon" title="Programa Presupuestario">
                                    ---
                                </span>
                                <span id="programa_especial" class="input-group-addon" title="Programa Especial">
                                    ---
                                </span>
                                <span id="actividad_institucional" class="input-group-addon" title="Actividad Institucional">
                                    ---
                                </span>
                                <span id="proyecto_estrategico" class="input-group-addon" title="Proyecto Estratégico">
                                    -
                                </span>
                                <span id="no_proyecto_estrategico" class="input-group-addon" title="Número de Proyecto Estratégico">
                                    000
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="unidadresponsable">Unidad Responsable</label>
                            {{Form::select('unidadresponsable',array('' =>'Selecciona una unidad') + $unidades_responsables->lists('descripcion','clave'),0,array('class'=>'form-control selectpicker','id'=>'unidadresponsable'))}}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="funciongasto">
                                Funcion del Gasto (Finalidad,Funcion,Sub Funcion,Sub Sub Funcion)
                            </label>
                            <select class="form-control selectpicker" id="funciongasto" name="funciongasto" data-live-search="true">
                                <option value="">Seleciona una función</option>
                                @foreach ($funciones_gasto as $finalidad)
                                    @if(count($finalidad->hijos))
                                        <optgroup label="{{$finalidad->clave . ' ' . $finalidad->descripcion}}">

                                            @foreach ($finalidad->hijos as $funcion)
                                                @if(count($funcion->hijos))
                                                    <optgroup label="{{$funcion->clave . ' ' . $funcion->descripcion}}">

                                                        @foreach ($funcion->hijos as $subfuncion)
                                                            @if(count($subfuncion->hijos))
                                                                <optgroup label="{{$subfuncion->clave . ' ' . $subfuncion->descripcion}}">
                                                                    
                                                                    @foreach ($subfuncion->hijos as $subsubfuncion)
                                                                        <option value="{{$subsubfuncion->clave}}">
                                                                            {{$subsubfuncion->clave . ' ' . $subsubfuncion->descripcion}}
                                                                        </option>
                                                                    @endforeach

                                                                </optgroup>
                                                            @endif
                                                        @endforeach

                                                    </optgroup>
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
                            {{Form::select('programasectorial',array('' =>'Selecciona un programa sectorial') + $programas_sectoriales->lists('descripcion','clave'),0,array('class'=>'form-control selectpicker','id'=>'programasectorial'))}}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="programapresupuestario">Programa Presupuestario</label>
                            {{Form::select('programapresupuestario',array('' =>'Selecciona un programa presupuestario') + $programas_presupuestarios->lists('descripcion','clave'),0,array('class'=>'form-control selectpicker','id'=>'programapresupuestario','data-live-search'=>'true'))}}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="programaespecial">Programa Especial</label>
                            {{Form::select('programaespecial',array('' =>'Selecciona un programa especial') + $programas_especiales->lists('descripcion','clave'),'',array('class'=>'form-control selectpicker','id'=>'programaespecial','data-live-search'=>'true'))}}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="actividadinstitucional">Actividades Institucionales</label>
                            {{Form::select('actividadinstitucional',array('' =>'Selecciona una actividad institucional') + $actividades_institucionales->lists('descripcion','clave'),'',array('class'=>'form-control selectpicker','id'=>'actividadinstitucional','data-live-search'=>'true'))}}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="proyectoestrategico">Proyecto Estratégico</label>
                            {{Form::select('proyectoestrategico',array('' =>'Selecciona un proyecto estratégico') + $proyectos_estrategicos->lists('descripcion','clave'),'',array('class'=>'form-control selectpicker','id'=>'proyectoestrategico'))}}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="control-label" for="numeroproyectoestrategico">Número de Proyecto Estratégico</label>
                            <input type="number" class="form-control" id="numeroproyectoestrategico" name="numeroproyectoestrategico">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label class="control-label" for="cobertura">Cobertura</label>
                {{Form::select('cobertura',array('' =>'Selecciona una cobertura') + $coberturas->lists('descripcion','id'),'',array('class'=>'form-control selectpicker','id'=>'cobertura'))}}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label class="control-label" for="municipio">Municipio</label>
                {{Form::select('municipio',array('' =>'Selecciona un municipio') + $municipios->lists('nombre','clave'),'',array('class'=>'form-control selectpicker','id'=>'municipio','data-live-search'=>'true','data-container'=>'body'))}}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label class="control-label" for="tipobeneficiario">Beneficiario</label>
                {{Form::select('tipobeneficiario',array('' =>'Selecciona un beneficiario') + $tipos_beneficiarios->lists('descripcion','id'),'',array('class'=>'form-control selectpicker','id'=>'tipobeneficiario','data-live-search'=>'true','data-container'=>'body'))}}
            </div>
        </div>

        <div class="col-sm-3">
            <div class="form-group">
                <label class="control-label">Ver Datos</label>
                <button type="button" class="btn btn-primary form-control"><span class="fa fa-table"></span> Ver</button>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label" for="totalbeneficiariosf">Femenino</label>
                        <div class="input-group">
                            <span class="input-group-addon"><span class="fa fa-female"></span></span>
                            <input type="number" class="form-control" name="totalbeneficiariosf" id="totalbeneficiariosf">
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label" for="totalbeneficiariosm">Masculino</label>
                        <div class="input-group">
                            <span class="input-group-addon"><span class="fa fa-male"></span></span>
                            <input type="number" class="form-control" name="totalbeneficiariosm" id="totalbeneficiariosm">
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label" for="totalbeneficiarios">Total</label>
                        <input type="number" class="form-control" name="totalbeneficiarios" id="totalbeneficiarios">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <table class="table table-bordered table-condensed">
                <tr>
                    <th rowspan="2"></th>
                    <th colspan="2">Zona</th>
                </tr>
                <tr>
                    <th><small>Urbana</small></th>
                    <th><small>Rural</small></th>
                </tr>
                <tr>
                    <td><span class="fa fa-female fa-2x"></span></td>
                    <td><div class="form-group"><input type="number" class="form-control" name="urbanaf" id="urbanaf"></div></td>
                    <td><div class="form-group"><input type="number" class="form-control" name="ruralf" id="ruralf"></div></td>
                </tr>
                <tr>
                    <td><span class="fa fa-male fa-2x"></span></td>
                    <td><div class="form-group"><input type="number" class="form-control" name="urbanam" id="urbanam"></div></td>
                    <td><div class="form-group"><input type="number" class="form-control" name="ruralm" id="ruralm"></div></td>
                </tr>
            </table>
        </div>
        <div class="col-sm-4">
            <table class="table table-bordered table-condensed">
                <tr>
                    <th rowspan="2"></th>
                    <th colspan="4">Población</th>
                </tr>
                <tr>
                    <th><small>Mestiza</small></th>
                    <th><small>Indigena</small></th>
                    <th><small>Inmigrante</small></th>
                    <th><small>Otros</small></th>
                </tr>
                <tr>
                    <td><span class="fa fa-female fa-2x"></span></td>
                    <td><div class="form-group"><input type="number" class="form-control" name="mestizaf" id="mestizaf"></div></td>
                    <td><div class="form-group"><input type="number" class="form-control" name="indigenaf" id="indigenaf"></div></td>
                    <td><div class="form-group"><input type="number" class="form-control" name="inmigrantef" id="inmigrantef"></div></td>
                    <td><div class="form-group"><input type="number" class="form-control" name="otrosf" id="otrosf"></div></td>
                </tr>
                <tr>
                    <td><span class="fa fa-male fa-2x"></span></td>
                    <td><div class="form-group"><input type="number" class="form-control" name="mestizam" id="mestizam"></div></td>
                    <td><div class="form-group"><input type="number" class="form-control" name="indigenam" id="indigenam"></div></td>
                    <td><div class="form-group"><input type="number" class="form-control" name="inmigrantem" id="inmigrantem"></div></td>
                    <td><div class="form-group"><input type="number" class="form-control" name="otrosm" id="otrosm"></div></td>
                </tr>
            </table>
        </div>
        <div class="col-sm-5">
            <table class="table table-bordered table-condensed">
                <tr>
                    <th rowspan="2"></th>
                    <th colspan="5">Marginación</th>
                </tr>
                <tr>
                    <th><small>Muy alta</small></th>
                    <th><small>Alta</small></th>
                    <th><small>Media</small></th>
                    <th><small>Baja</small></th>
                    <th><small>Muy baja</small></th>
                </tr>
                <tr>
                    <td><span class="fa fa-female fa-2x"></span></td>
                    <td><div class="form-group"><input type="number" class="form-control" name="muyaltaf" id="muyaltaf"></div></td>
                    <td><div class="form-group"><input type="number" class="form-control" name="altaf" id="altaf"></div></td>
                    <td><div class="form-group"><input type="number" class="form-control" name="mediaf" id="mediaf"></div></td>
                    <td><div class="form-group"><input type="number" class="form-control" name="bajaf" id="bajaf"></div></td>
                    <td><div class="form-group"><input type="number" class="form-control" name="muybajaf" id="muybajaf"></div></td>
                </tr>
                <tr>
                    <td><span class="fa fa-male fa-2x"></span></td>
                    <td><div class="form-group"><input type="number" class="form-control" name="muyaltam" id="muyaltam"></div></td>
                    <td><div class="form-group"><input type="number" class="form-control" name="altam" id="altam"></div></td>
                    <td><div class="form-group"><input type="number" class="form-control" name="mediam" id="mediam"></div></td>
                    <td><div class="form-group"><input type="number" class="form-control" name="bajam" id="bajam"></div></td>
                    <td><div class="form-group"><input type="number" class="form-control" name="muybajam" id="muybajam"></div></td>
                </tr>
            </table>
        </div>
    </div>
    <input type="hidden" id="id" name="id" value="{{{ $id or '' }}}">
    <input type="hidden" id="clasificacionproyecto" name="clasificacionproyecto" value="{{$clasificacion_proyecto_id}}">
    <input type="hidden" id="tipoproyecto" name="tipoproyecto" value="{{$tipo_proyecto_id}}">
    <div class="panel-footer">
        <div class="row">
            <div class="col-sm-12">
                <button type="button" class="btn btn-primary" id="btn-proyecto-guardar">
                    <span class="fa fa-save"></span> Guardar cambios
                </button>
                <button type="button" class="btn btn-default" id="btn-proyecto-cancelar">
                    <span class="fa fa-chevron-left"></span> Regresar a la lista de Proyectos
                </button>
            </div>
        </div>
    </div>
</form>