<form action="" id="form_{{$identificador}}">
    <ul class="nav {{(isset($lista_actividades))?'nav-tabs':'nav-pills'}}" role="tablist" id="lista-tabs-{{$identificador}}">
        <li role="presentation" class="active">
            <a href="#objetivos_{{$identificador}}" role="tab" data-toggle="tab">
                <span class="fa fa-crosshairs"></span> Objetivo
            </a>
        </li>
        <li role="presentation">
            <a href="#indicador_{{$identificador}}" role="tab" data-toggle="tab">
                <span class="fa fa-line-chart"></span> Indicador
            </a>
        </li>
        <li role="presentation">
            <a id="tablink-{{$identificador}}-desgloce-metas"  href="#desglose_{{$identificador}}" role="tab" data-toggle="tab">
                <span class="fa fa-table"></span> Metas
            </a>
        </li>
        
        @if (isset($lista_actividades) && $clasificacion_proyecto == 2)
        <li role="presentation">
            <a id="tablink-{{$identificador}}-presupuesto"  href="#presupuesto_{{$identificador}}" role="tab" data-toggle="tab">
                <span class="fa fa-usd"></span> Presupuesto
            </a>
        </li>
        @endif
        
        @if(isset($lista_actividades))
        <li role="presentation" class="pull-right disabled">
            <a id="tablink-{{$identificador}}-actividades" href="#actividades_{{$identificador}}" role="tab" >
                <span class="fa fa-thumb-tack"></span> Actividades <span id="conteo-actividades" class="badge">0 / 5</span>
            </a>
        </li>
        @endif
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="objetivos_{{$identificador}}">
            <br>
            @if (isset($lista_actividades) && $clasificacion_proyecto == 2)
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="entregable" class="control-label">Entregable</label>
                            {{Form::select('entregable',array(''=>'Seleccione una opción') + $entregables->lists('descripcion','id'),'',array('class'=>'form-control chosen-one','id'=>'entregable'))}}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="tipo-entregable" class="control-label">Tipo</label>
                            <select id="tipo-entregable" name="tipo-entregable" class="form-control chosen-one">
                                <option value="">Seleccione un tipo</option>
                                <option value="NA" data-habilita-id="NA"> N / A </option>
                            @foreach ($entregables_tipos as $tipo)
                                <option value="{{$tipo->id}}" data-habilita-id="{{$tipo->idEntregable}}" class="hidden" disabled>
                                    {{$tipo->descripcion}}
                                </option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="accion-entregable" class="control-label">Acción</label>
                            <select id="accion-entregable" name="accion-entregable" class="form-control chosen-one">
                                <option value="">Seleccione una acción</option>
                            @foreach ($entregables_acciones as $accion)
                                <option value="{{$accion->id}}" data-habilita-id="{{$accion->idEntregable}}" class="hidden" disabled>
                                    {{$accion->descripcion}}
                                </option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="descripcion-obj-{{$identificador}}" class="control-label">Descripción</label>
                        <input type="text" class="form-control" id="descripcion-obj-{{$identificador}}" name="descripcion-obj-{{$identificador}}">
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="verificacion-{{$identificador}}" class="control-label">Medios de Verificación</label>
                        <input type="text" class="form-control" id="verificacion-{{$identificador}}" name="verificacion-{{$identificador}}">
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="supuestos-{{$identificador}}" class="control-label">Supuestos</label>
                        <input type="text" class="form-control" id="supuestos-{{$identificador}}" name="supuestos-{{$identificador}}">
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="indicador_{{$identificador}}">
            <br>
            <div class="row">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="descripcion-ind-{{$identificador}}" class="control-label">Descripción</label>
                                <input type="text" class="form-control" id="descripcion-ind-{{$identificador}}" name="descripcion-ind-{{$identificador}}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="interpretacion-{{$identificador}}" class="control-label">Interpretación</label>
                                <input type="text" class="form-control" id="interpretacion-{{$identificador}}" name="interpretacion-{{$identificador}}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="numerador-ind-{{$identificador}}" class="control-label">Numerador</label>
                                <input type="text" class="form-control" id="numerador-ind-{{$identificador}}" name="numerador-ind-{{$identificador}}">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="denominador-ind-{{$identificador}}" class="control-label">Denominador</label>
                                <input type="text" class="form-control" id="denominador-ind-{{$identificador}}" name="denominador-ind-{{$identificador}}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-4">
                            <div clasS="form-group">
                                <label class="control-label" for="dimension-{{$identificador}}">
                                    Dimensión
                                </label>
                                {{Form::select('dimension-'.$identificador,array(''=>'Seleccione una dimensión') + $dimensiones->lists('descripcion','id'),'',array('class'=>'form-control chosen-one','id'=>'dimension-'.$identificador))}}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div clasS="form-group">
                                <label class="control-label" for="tipo-ind-{{$identificador}}">
                                    Tipo
                                </label>
                                {{Form::select('tipo-ind-'.$identificador,array(''=>'Seleccione un tipo') + $tipos_indicador->lists('descripcion','id'),'',array('class'=>'form-control chosen-one','id'=>'tipo-ind-'.$identificador))}}
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div clasS="form-group">
                                <label class="control-label" for="unidad-medida-{{$identificador}}">
                                    Unidad de Medida
                                </label>
                                {{Form::select('unidad-medida-'.$identificador,array(''=>'Seleccione una unidad') + $unidades_medida->lists('descripcion','id'),'',array('class'=>'form-control chosen-one','id'=>'unidad-medida-'.$identificador))}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="desglose_{{$identificador}}">
            <br>
            <div class="row">
                <div class="col-sm-2">
                    <div class="form-group">
                        <label class="control-label" for="linea-base-{{$identificador}}">Linea Base</label>
                        <input type="text" class="form-control" id="linea-base-{{$identificador}}" name="linea-base-{{$identificador}}">
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label class="control-label" for="anio-base-{{$identificador}}">Año Base</label>
                        <input type="text" class="form-control" id="anio-base-{{$identificador}}" name="anio-base-{{$identificador}}">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div clasS="form-group">
                        <label class="control-label" for="formula-{{$identificador}}">
                            Formula
                        </label>
                        {{Form::select('formula-'.$identificador,array(''=>'Seleccione una formula') + $formulas->lists('descripcion','id'),'',array('class'=>'form-control chosen-one','id'=>'formula-'.$identificador))}}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div clasS="form-group">
                        <label class="control-label" for="frecuencia-{{$identificador}}">
                            Frecuencia
                        </label>
                        {{Form::select('frecuencia-'.$identificador,array(''=>'Seleccione una frecuencia') + $frecuencias->lists('descripcion','id'),'',array('class'=>'form-control chosen-one','id'=>'frecuencia-'.$identificador))}}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table id="tabla-{{$identificador}}-metas-mes" class="table table-condensed table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Mes</th>
                                @foreach ($meses as $clave => $mes)
                                    <th>{{$mes}}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jurisdicciones as $llave => $jurisdiccion)
                                <tr>
                                    <th>{{$jurisdiccion}}</th>
                                    @foreach ($meses as $clave => $mes)
                                        <td>
                                            <input id="mes-{{$identificador}}-{{$llave}}-{{$clave}}" name="mes-{{$identificador}}[{{$llave}}][{{$clave}}]" type="number" class="form-control input-sm metas-mes" data-meta-mes="{{$clave}}" data-meta-jurisdiccion="{{$llave}}" data-meta-identificador="{{$identificador}}" data-meta-id="">
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-6 bg-info">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span class="fa fa-link"></span> Trim 1</label>
                                <span class="form-control control-espejo" id="trim1-{{$identificador}}-lbl"></span>
                                <input type="hidden" id="trim1-{{$identificador}}" name="trim1-{{$identificador}}">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span class="fa fa-link"></span> Trim 2</label>
                                <span class="form-control control-espejo" id="trim2-{{$identificador}}-lbl"></span>
                                <input type="hidden" id="trim2-{{$identificador}}" name="trim2-{{$identificador}}">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span class="fa fa-link"></span> Trim 3</label>
                                <span class="form-control control-espejo" id="trim3-{{$identificador}}-lbl"></span>
                                <input type="hidden" id="trim3-{{$identificador}}" name="trim3-{{$identificador}}">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span class="fa fa-link"></span> Trim 4</label>
                                <span class="form-control control-espejo" id="trim4-{{$identificador}}-lbl"></span>
                                <input type="hidden" id="trim4-{{$identificador}}" name="trim4-{{$identificador}}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label class="control-label"><span class="fa fa-link"></span> Numerador</label>
                        <span class="form-control control-espejo" id="numerador-{{$identificador}}-lbl"></span>
                        <input type="hidden" id="numerador-{{$identificador}}" name="numerador-{{$identificador}}">
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label class="control-label" for="denominador-{{$identificador}}">Denominador</label>
                        <input type="text" class="form-control" id="denominador-{{$identificador}}" name="denominador-{{$identificador}}">
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label class="control-label"><span class="fa fa-link"></span> Meta</label>
                        <span class="form-control control-espejo" data-espejo-id="#meta-{{$identificador}}"></span>
                        <input type="hidden" id="meta-{{$identificador}}" name="meta-{{$identificador}}">
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="id-accion" name="id-accion">
        <input type="hidden" id="id-{{$identificador}}" name="id-{{$identificador}}">
        @if(isset($lista_actividades))
        <div role="tabpanel" class="tab-pane" id="actividades_{{$identificador}}">
            <br>
            {{$lista_actividades}}
        </div>
        @endif
        @if($clasificacion_proyecto == 2 && isset($lista_actividades))
        <div role="tabpanel" class="tab-pane" id="presupuesto_{{$identificador}}">
            <br>
            <label for="objeto-gasto" class="control-label">Partida(s) a capturar</label>
            <div class="row">
                <div class="col-sm-9">
                    <div class="form-group">
                        <select class="form-control chosen-one" id="objeto-gasto-presupuesto">
                            <option value="">Seleciona una partida</option>
                            @foreach ($objetos_gasto as $capitulo)
                                @if(count($capitulo->hijos))
                                <optgroup label="{{$capitulo->clave . ' ' . $capitulo->descripcion}}">
                                    @foreach ($capitulo->hijos as $concepto)
                                        @if(count($concepto->hijos))
                                        <option disabled="disabled">
                                            {{$concepto->clave . ' ' . $concepto->descripcion}}
                                        </option>
                                            @foreach ($concepto->hijos as $generica)
                                                @if(count($generica->hijos))
                                                    <option disabled="disabled">
                                                        &nbsp;&nbsp; {{str_pad($generica->clave,5,'0') . ' ' . $generica->descripcion}}
                                                    </option>
                                                    @foreach ($generica->hijos as $especifica)
                                                        <option value="{{$especifica->id}}">
                                                            &nbsp;&nbsp;&nbsp;&nbsp; {{$especifica->clave}} - {{$especifica->descripcion}}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                </optgroup>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <button class="btn btn-info btn-block" type="button" id="btn-agregar-partida">
                            <span class="fa fa-plus"></span> Agregar Partida
                        </button>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="panel panel-default" id="tabla_componente_partidas">
                        <table class="table table-striped table-hover table-condensed">
                            <thead>
                                <tr>
                                    <th width="1">Clave</th>
                                    <th>Descripción</th>
                                    <th width="1">Control</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12"><label class="control-label">Origen del Presupuesto</label></div>
                <div class="col-sm-12">
                    <div class="row">
                        @foreach ($origenes_financiamiento as $origen)
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="accion-origen-{{$origen->id}}" class="control-label">
                                        {{$origen->descripcion}}
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <span class="fa fa-usd"></span>
                                        </span>
                                        <input type="number" class="form-control accion-origen-financiamiento" id="accion-origen-{{$origen->id}}" name="accion-origen[{{$origen->id}}]" data-origen-id="{{$origen->id}}" data-captura-id="">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        <label for="accion-presupuesto-requerido" class="control-label">
                            <span class="fa fa-link"></span> Presupuesto Requerido
                        </label>
                        <span class="form-control" id="accion-presupuesto-requerido-lbl"></span>
                        <input type="hidden" id="accion-presupuesto-requerido" name="accion-presupuesto-requerido"/>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</form>