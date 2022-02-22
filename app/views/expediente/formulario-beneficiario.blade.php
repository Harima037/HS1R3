<form id="form_beneficiario">
	<input type="hidden" name="id-beneficiario" id="id-beneficiario">
	<div class="row">
		<div class="col-sm-4">
			<div class="form-group">
	            <label class="control-label" for="tipobeneficiariocabecera">Tipo de Beneficiario</label>
	            {{Form::select('tipobeneficiariocabecera',array('' =>'Seleccione uno') + $tipos_beneficiarios_cabecera->lists('descripcion','clave'),'',array('class'=>'form-control chosen-one','id'=>'tipobeneficiariocabecera'))}}
	        </div>
	    </div>
		<div class="col-sm-8">
			<div class="form-group">
	            <label class="control-label" for="tipobeneficiario">Detalle Beneficiario</label>
				<select class="form-control chosen-one" id="tipobeneficiario" name="tipobeneficiario">
					<option value="" class="option-default-label">Seleccione primero un Tipo de Beneficiario</option>
					@foreach ($tipos_beneficiarios as $detalle)
                                <option value="{{$detalle->id}}" data-habilita-id="{{$detalle->clave_grupo}}" class="hidden" disabled>
								{{$detalle->clave}} {{$detalle->descripcion}}
                                </option>
                    @endforeach
				</select>
	        </div>
	    </div>
    </div>
    <div class="row">
		<div class="col-sm-4">
			<div class="form-group">
	            <label class="control-label" for="tipocaptura">Tipo de Captura</label>
	            {{Form::select('tipocaptura',array('' =>'Selecciona uno') + $tipos_captura->lists('descripcion','id'),'',array('class'=>'form-control chosen-one','id'=>'tipocaptura'))}}
	        </div>
	    </div>
		<div class="col-sm-4">
			<div class="form-group">
                <label class="control-label" for="totalbeneficiarios">Total</label>
                <div class="form-group">
                    <input type="number" class="form-control benef-totales" name="totalbeneficiarios" id="totalbeneficiarios">
                </div>
            </div>
        </div>
        <div class="col-sm-4">
			<div class="form-group">
	    		<label class="control-label">Estadisticas de Población</label><br>
	        	<div class="btn-group">
					<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<span class="fa fa-download"></span> Descargar Archivos <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						@foreach($archivos as $archivo)
							<li>
								<a href="{{ URL::to('ver-archivo/'.$archivo->id) }}" target="_blank">
								<span class="fa fa-file-pdf-o"></span> {{$archivo->titulo}}</a>
							</li>
                        @endforeach
					</ul>
				</div>
	    	</div>
        </div>
	    <div class="col-sm-12">
	    	<div role="tabpanel">
				<!-- Nav tabs -->
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active">
						<a href="#panel-zona" aria-controls="panel-zona" role="tab" data-toggle="tab">Zona</a>
					</li>
					<li role="presentation">
						<a href="#panel-poblacion" aria-controls="panel-poblacion" role="tab" data-toggle="tab">Población</a>
					</li>
					<li role="presentation">
						<a href="#panel-marginacion" aria-controls="panel-marginacion" role="tab" data-toggle="tab">Marginación</a>
					</li>
				</ul>
				<!-- Tab panes -->
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="panel-zona">
						<table class="table table-striped table-condensed">
							<thead>
								<tr>
									<th>Urbana</th>
									<th>Rural</th>
									<th><span class="fa fa-link"></span> Total</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										<div class="form-group">
											<input type="number" class="form-control benef-desglose sub-total-zona" name="urbana" id="urbana">
										</div>
									</td>
									<td>
										<div class="form-group">
											<input type="number" class="form-control benef-desglose sub-total-zona" name="rural" id="rural">
										</div>
									</td>
									<td>
										<div class="form-group"><span id="total-zona" class=""></span></div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div role="tabpanel" class="tab-pane" id="panel-poblacion">
						<table class="table table-striped table-condensed">
							<thead>
								<tr>
									<th>Mestiza</th>
									<th>Indigena</th>
									<th><span class="fa fa-link"></span> Total</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										<div class="form-group">
											<input type="number" class="form-control benef-desglose sub-total-poblacion" name="mestiza" id="mestiza">
										</div>
									</td>
									<td>
										<div class="form-group">
											<input type="number" class="form-control benef-desglose sub-total-poblacion" name="indigena" id="indigena">
										</div>
									</td>
									<td>
										<div class="form-group">
											<span id="total-poblacion" class=""></span>
										</div>
									</td>
								</tr>
							</tbody>
				        </table>
					</div>

					<div role="tabpanel" class="tab-pane" id="panel-marginacion">
						<table class="table table-striped table-condensed">
							<thead>
								<tr>
					                <th>Muy alta</th>
					                <th>Alta</th>
					                <th>Media</th>
					                <th>Baja</th>
					                <th>Muy baja </th>
					            </tr>
							</thead>
				            <tbody>
				            	<tr>
				            		<td>
				            			<div class="form-group">
											<input type="number" class="form-control benef-desglose sub-total-marginacion" name="muyalta" id="muyalta">
				                    	</div>
				                	</td>
				                    <td>
										<div class="form-group">
											<input type="number" class="form-control benef-desglose sub-total-marginacion" name="alta" id="alta">
				                    	</div>
				                	</td>
				                    <td>
										<div class="form-group">
											<input type="number" class="form-control benef-desglose sub-total-marginacion" name="media" id="media">
				                    	</div>
				                	</td>
				                    <td>
										<div class="form-group">
											<input type="number" class="form-control benef-desglose sub-total-marginacion" name="baja" id="baja">
				                    	</div>
				                	</td>
				                    <td>
										<div class="form-group">
											<input type="number" class="form-control benef-desglose sub-total-marginacion" name="muybaja" id="muybaja">
				                    	</div>
				                	</td>
				            	</tr>
				            </tbody>
				            <tfoot>
				            	<tr>
					                <th><span class="fa fa-link"></span> Total</th>
					                <td colspan="2">
					                	<div class="form-group">
					                		<span id="total-marginacion" class=""></span>
					                	</div>
					                </td>
					                <td colspan="2"></td>
					            </tr>
				            </tfoot>
				        </table>
					</div>
				</div>
			</div>
	    </div>
	</div>
</form>