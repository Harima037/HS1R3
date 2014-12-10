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
            <a id="tablink-{{$identificador}}-desgloce-metas"  href="#desgloce_{{$identificador}}" role="tab" data-toggle="tab">
                <span class="fa fa-table"></span> Desglose de Metas
            </a>
        </li>
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
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="entregable-{{$identificador}}" class="control-label">Entregable</label>
                            {{Form::select('entregable-'.$identificador,array(''=>'Seleccione una opción') + $entregables->lists('descripcion','id'),'',array('class'=>'form-control selectpicker','id'=>'entregable-'.$identificador,'data-live-search'=>'true'))}}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="tipo-obj-{{$identificador}}" class="control-label">Tipo</label>
                            <input type="text" class="form-control" id="tipo-obj-{{$identificador}}" name="tipo-obj-{{$identificador}}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="accion-{{$identificador}}" class="control-label">Acción</label>
                            <input type="text" class="form-control" id="accion-{{$identificador}}" name="accion-{{$identificador}}">
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
                                {{Form::select('dimension-'.$identificador,array(''=>'Seleccione una dimensión') + $dimensiones->lists('descripcion','id'),'',array('class'=>'form-control selectpicker','id'=>'dimension-'.$identificador))}}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div clasS="form-group">
                                <label class="control-label" for="tipo-ind-{{$identificador}}">
                                    Tipo
                                </label>
                                {{Form::select('tipo-ind-'.$identificador,array(''=>'Seleccione un tipo') + $tipos_indicador->lists('descripcion','id'),'',array('class'=>'form-control selectpicker','id'=>'tipo-ind-'.$identificador))}}
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div clasS="form-group">
                                <label class="control-label" for="unidad-medida-{{$identificador}}">
                                    Unidad de Medida
                                </label>
                                {{Form::select('unidad-medida-'.$identificador,array(''=>'Seleccione una unidad') + $unidades_medida->lists('descripcion','id'),'',array('class'=>'form-control selectpicker','id'=>'unidad-medida-'.$identificador,'data-live-search'=>'true'))}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="desgloce_{{$identificador}}">
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
                        {{Form::select('formula-'.$identificador,array(''=>'Seleccione una formula') + $formulas->lists('descripcion','id'),'',array('class'=>'form-control selectpicker','id'=>'formula-'.$identificador))}}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div clasS="form-group">
                        <label class="control-label" for="frecuencia-{{$identificador}}">
                            Frecuencia
                        </label>
                        {{Form::select('frecuencia-'.$identificador,array(''=>'Seleccione una frecuencia') + $frecuencias->lists('descripcion','id'),'',array('class'=>'form-control selectpicker','id'=>'frecuencia-'.$identificador))}}
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
                                    <th>{{$clave}}</th>
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
                                <label class="control-label"><span class="fa fa-link"></span> Trimestre 1</label>
                                <span class="form-control control-espejo" data-espejo-id="#trim1-{{$identificador}}"></span>
                                <input type="hidden" id="trim1-{{$identificador}}" name="trim1-{{$identificador}}">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span class="fa fa-link"></span> Trimestre 2</label>
                                <span class="form-control control-espejo" data-espejo-id="#trim2-{{$identificador}}"></span>
                                <input type="hidden" id="trim2-{{$identificador}}" name="trim2-{{$identificador}}">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span class="fa fa-link"></span> Trimestre 3</label>
                                <span class="form-control control-espejo" data-espejo-id="#trim3-{{$identificador}}"></span>
                                <input type="hidden" id="trim3-{{$identificador}}" name="trim3-{{$identificador}}">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span class="fa fa-link"></span> Trimestre 4</label>
                                <span class="form-control control-espejo" data-espejo-id="#trim4-{{$identificador}}"></span>
                                <input type="hidden" id="trim4-{{$identificador}}" name="trim4-{{$identificador}}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label class="control-label"><span class="fa fa-link"></span> Numerador</label>
                        <span class="form-control control-espejo" data-espejo-id="#numerador-{{$identificador}}"></span>
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
                        <label class="control-label"><span class="fa fa-link"></span> Meta Indicador</label>
                        <span class="form-control control-espejo" data-espejo-id="#meta-{{$identificador}}"></span>
                        <input type="hidden" id="meta-{{$identificador}}" name="meta-{{$identificador}}">
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="id-{{$identificador}}" name="id-{{$identificador}}">
        @if(isset($lista_actividades))
        <div role="tabpanel" class="tab-pane" id="actividades_{{$identificador}}">
            <br>
            {{$lista_actividades}}
        </div>
        @endif
    </div>
</form>