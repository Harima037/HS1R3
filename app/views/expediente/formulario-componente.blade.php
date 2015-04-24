<form action="" id="form_{{$identificador}}">
    <ul class="nav {{($identificador != 'actividad')?'nav-tabs':'nav-pills'}}" role="tablist" id="lista-tabs-{{$identificador}}">
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
        @if($clasificacion_proyecto == 2 && isset($lista_actividades))
        <li role="presentation">
            <a id="tablink-{{$identificador}}-desglose" href="#desglose_{{$identificador}}" role="tab" data-toggle="tab">
                <span class="fa fa-list"></span> Desglose <span id="conteo-desglose" class="badge">0</span>
            </a>
        </li>
        @endif
        <li role="presentation">
            <a id="tablink-{{$identificador}}-desgloce-metas"  href="#desgloce_{{$identificador}}" role="tab" data-toggle="tab">
                <span class="fa fa-table"></span> Metas
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
                        <label for="descripcion-obj-{{$identificador}}" class="control-label">Descripción del Objetivo</label>
                        <input type="text" class="form-control" id="descripcion-obj-{{$identificador}}" name="descripcion-obj-{{$identificador}}">
                    </div>
                </div>
            @if ($identificador == 'programa')
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="ambito-{{$identificador}}" class="control-label">Ambito</label>
                        <select class="form-control chosen-one" id="ambito-{{$identificador}}" name="ambito-{{$identificador}}">
                            <option value="">Selecciona un ambito</option>
                            @foreach($ambitos as $ambito)
                                <option value="{{$ambito['clave']}}">{{$ambito['clave']}} {{$ambito['descripcion']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
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
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="descripcion-ind-{{$identificador}}" class="control-label">Descripción del Indicador</label>
                                <input type="text" class="form-control" id="descripcion-ind-{{$identificador}}" name="descripcion-ind-{{$identificador}}">
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
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="interpretacion-{{$identificador}}" class="control-label">Interpretación</label>
                                <input type="text" class="form-control" id="interpretacion-{{$identificador}}" name="interpretacion-{{$identificador}}">
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
                    <div class="form-group">
                        <label class="control-label" for="formula-{{$identificador}}">
                            Formula
                        </label>
                        {{Form::select('formula-'.$identificador,array(''=>'Seleccione una formula') + $formulas->lists('descripcion','id'),'',array('class'=>'form-control chosen-one','id'=>'formula-'.$identificador))}}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="control-label" for="frecuencia-{{$identificador}}">
                            Frecuencia
                        </label>
                        {{Form::select('frecuencia-'.$identificador,array(''=>'Seleccione una frecuencia') + $frecuencias->lists('descripcion','id'),'',array('class'=>'form-control chosen-one','id'=>'frecuencia-'.$identificador))}}
                    </div>
                </div>
            </div>
            <div class="row">
                @if($identificador != 'programa')
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
                                <span class="form-control control-espejo" data-espejo-id="#trim1-{{$identificador}}"></span>
                                <input type="hidden" id="trim1-{{$identificador}}" name="trim1-{{$identificador}}">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span class="fa fa-link"></span> Trim 2</label>
                                <span class="form-control control-espejo" data-espejo-id="#trim2-{{$identificador}}"></span>
                                <input type="hidden" id="trim2-{{$identificador}}" name="trim2-{{$identificador}}">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span class="fa fa-link"></span> Trim 3</label>
                                <span class="form-control control-espejo" data-espejo-id="#trim3-{{$identificador}}"></span>
                                <input type="hidden" id="trim3-{{$identificador}}" name="trim3-{{$identificador}}">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label"><span class="fa fa-link"></span> Trim 4</label>
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
                        <label class="control-label"><span class="fa fa-link"></span> Meta</label>
                        <span class="form-control control-espejo" data-espejo-id="#meta-{{$identificador}}"></span>
                        <input type="hidden" id="meta-{{$identificador}}" name="meta-{{$identificador}}">
                    </div>
                </div>
                @endif
                @if($identificador == 'programa')
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="trim1-{{$identificador}}" class="control-label">Trim 1</label>
                                <input type="number" class="form-control valor-trimestre" id="trim1-{{$identificador}}" name="trim1-{{$identificador}}">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="trim2-{{$identificador}}" class="control-label">Trim 2</label>
                                <input type="number" class="form-control valor-trimestre" id="trim2-{{$identificador}}" name="trim2-{{$identificador}}">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="trim3-{{$identificador}}" class="control-label">Trim 3</label>
                                <input type="number" class="form-control valor-trimestre" id="trim3-{{$identificador}}" name="trim3-{{$identificador}}">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="trim4-{{$identificador}}" class="control-label">Trim 4</label>
                                <input type="number" class="form-control valor-trimestre" id="trim4-{{$identificador}}" name="trim4-{{$identificador}}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="numerador-{{$identificador}}" class="control-label"><span class="fa fa-link"></span> Numerador</label>
                        <span class="form-control" id="lbl-numerador-{{$identificador}}"></span>
                        <input type="hidden" id="numerador-{{$identificador}}" name="numerador-{{$identificador}}">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="control-label" for="denominador-{{$identificador}}">Denominador</label>
                        <input type="number" class="form-control" id="denominador-{{$identificador}}" name="denominador-{{$identificador}}">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="meta-{{$identificador}}" class="control-label"><span class="fa fa-link"></span> Meta</label>
                        <span class="form-control" id="lbl-meta-{{$identificador}}"></span>
                        <input type="hidden" id="meta-{{$identificador}}" name="meta-{{$identificador}}">
                    </div>
                </div>
                @endif
            </div>
        </div>
        <input type="hidden" id="id-{{$identificador}}" name="id-{{$identificador}}">
        @if(isset($lista_actividades))
        <div role="tabpanel" class="tab-pane" id="actividades_{{$identificador}}">
            <br>
            {{$lista_actividades}}
        </div>
        @endif
        @if($clasificacion_proyecto == 2 && isset($lista_actividades))
        <div role="tabpanel" class="tab-pane" id="desglose_{{$identificador}}">
            <br>
            <div class="panel panel-default datagrid" id="datagridDesgloseComponente" data-edit-row="editar_desglose">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="input-group" style="margin:5px">                            
                                <input type="text" class="form-control txt-quick-search" placeholder="Buscar">
                                <span class="input-group-btn">
                                    <button class="btn btn-default btn-quick-search" type="button">
                                        <span class="glyphicon glyphicon-search"></span>
                                    </button>
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="btn-toolbar pull-right" >
                                <div class="btn-group" style="margin:5px">
                                    <button type="button" class="btn btn-primary btn-agregar-desglose">
                                        <span class="glyphicon glyphicon-plus-sign"></span> Agregar Desglose
                                    </button>
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li>
                                            <a href="#" class="btn-edit-rows"><span class="glyphicon glyphicon-edit"></span> Editar</a>
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="#" class="btn-delete-rows"><span class="glyphicon glyphicon-remove"></span> Eliminar</a>
                                        </li>
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
                            <th>Localidad</th>
                            <th>Municipio</th>
                            <th>Jurisdicción</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
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
        @endif
    </div>
</form>