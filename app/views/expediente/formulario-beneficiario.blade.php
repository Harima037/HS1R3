<form id="form_beneficiario">
	<input type="hidden" name="id-beneficiario" id="id-beneficiario">
	<div class="row">
		<div class="col-sm-4">
			<div class="form-group">
	            <label class="control-label" for="tipobeneficiario">Tipo de Beneficiario</label>
	            {{Form::select('tipobeneficiario',array('' =>'Selecciona uno') + $tipos_beneficiarios->lists('descripcion','id'),'',array('class'=>'form-control chosen-one','id'=>'tipobeneficiario'))}}
	        </div>
	    </div>
		<div class="col-sm-4">
			<div class="form-group">
	            <label class="control-label" for="tipocaptura">Tipo de Captura</label>
	            {{Form::select('tipocaptura',array('' =>'Selecciona uno') + $tipos_captura->lists('descripcion','id'),'',array('class'=>'form-control chosen-one','id'=>'tipocaptura'))}}
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
						<li>
							<a href="{{ URL::to('archivos/estadistica_de_poblacion_2015.pdf') }}" target="_blank">
							<span class="fa fa-file-pdf-o"></span> Estadistica de Población</a>
						</li>
						<li>
							<a href="{{ URL::to('archivos/poblacion_jurisdiccional_2015.pdf') }}" target="_blank">
							<span class="fa fa-file-pdf-o"></span> Población Jurisdiccional</a>
						</li>
					</ul>
				</div>
	    	</div>
        </div>
    </div>
    <div class="row">
	    <div class="col-sm-4">
            <div class="form-group">
                <label class="control-label" for="totalbeneficiariosf">Femenino</label>
                <div class="input-group">
                    <span class="input-group-addon"><span class="fa fa-female"></span></span>
                    <input type="number" class="form-control benef-totales" name="totalbeneficiariosf" id="totalbeneficiariosf">
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label class="control-label" for="totalbeneficiariosm">Masculino</label>
                <div class="input-group">
                    <span class="input-group-addon"><span class="fa fa-male"></span></span>
                    <input type="number" class="form-control benef-totales" name="totalbeneficiariosm" id="totalbeneficiariosm">
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label class="control-label"><span class="fa fa-link"></span> Total</label>
                <span id="totalbeneficiarios" class="form-control"></span>
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
									<th></th>
									<th>Urbana</th>
									<th>Rural</th>
									<th><span class="fa fa-link"></span> Total</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><span class="fa fa-female fa-2x"></span></td>
									<td>
										<div class="form-group">
											<input type="number" class="form-control sub-total-zona fem" name="urbanaf" id="urbanaf">
										</div>
									</td>
									<td>
										<div class="form-group">
											<input type="number" class="form-control sub-total-zona fem" name="ruralf" id="ruralf">
										</div>
									</td>
									<td>
										<div class="form-group"><span id="total-zona-f" class=""></span></div>
									</td>
								</tr>
								<tr>
									<td><span class="fa fa-male fa-2x"></span></td>
									<td>
										<div class="form-group">
											<input type="number" class="form-control  sub-total-zona masc" name="urbanam" id="urbanam">
										</div>
									</td>
									<td>
										<div class="form-group">
											<input type="number" class="form-control sub-total-zona masc" name="ruralm" id="ruralm">
										</div>
									</td>
									<td>
										<div class="form-group"><span id="total-zona-m" class=""></span></div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div role="tabpanel" class="tab-pane" id="panel-poblacion">
						<table class="table table-striped table-condensed">
							<thead>
								<tr>
									<th></th>
									<th>Mestiza</th>
									<th>Indigena</th>
									<th><span class="fa fa-link"></span> Total</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><span class="fa fa-female fa-2x"></span></td>
									<td>
										<div class="form-group">
											<input type="number" class="form-control sub-total-poblacion fem" name="mestizaf" id="mestizaf">
										</div>
									</td>
									<td>
										<div class="form-group">
											<input type="number" class="form-control sub-total-poblacion fem" name="indigenaf" id="indigenaf">
										</div>
									</td>
									<td>
										<div class="form-group">
											<span id="total-poblacion-f" class=""></span>
										</div>
									</td>
								</tr>
								<tr>
									<td><span class="fa fa-male fa-2x"></span></td>
					                <td>
					                    <div class="form-group">
											<input type="number" class="form-control sub-total-poblacion masc" name="mestizam" id="mestizam">
					                    </div>
					                </td>
					                <td>
					                    <div class="form-group">
											<input type="number" class="form-control sub-total-poblacion masc" name="indigenam" id="indigenam">
					                    </div>
					                </td>
					                <td>
					                	<div class="form-group">
											<span id="total-poblacion-m" class=""></span>
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
									<th width="10"></th>
					                <th>Muy alta</th>
					                <th>Alta</th>
					                <th>Media</th>
					                <th>Baja</th>
					                <th>Muy baja </th>
					            </tr>
							</thead>
				            <tbody>
				            	<tr>
				            		<td><span class="fa fa-female fa-2x"></span></td>
				            		<td>
				            			<div class="form-group">
											<input type="number" class="form-control sub-total-marginacion fem" name="muyaltaf" id="muyaltaf">
				                    	</div>
				                	</td>
				                    <td>
										<div class="form-group">
											<input type="number" class="form-control sub-total-marginacion fem" name="altaf" id="altaf">
				                    	</div>
				                	</td>
				                    <td>
										<div class="form-group">
											<input type="number" class="form-control sub-total-marginacion fem" name="mediaf" id="mediaf">
				                    	</div>
				                	</td>
				                    <td>
										<div class="form-group">
											<input type="number" class="form-control sub-total-marginacion fem" name="bajaf" id="bajaf">
				                    	</div>
				                	</td>
				                    <td>
										<div class="form-group">
											<input type="number" class="form-control sub-total-marginacion fem" name="muybajaf" id="muybajaf">
				                    	</div>
				                	</td>
				            	</tr>
				            	<tr>
				            		<td><span class="fa fa-male fa-2x"></span></td>
					                <td>
					                    <div class="form-group">
											<input type="number" class="form-control sub-total-marginacion masc" name="muyaltam" id="muyaltam">
					                    </div>
					                </td>
					                <td>
					                    <div class="form-group">
											<input type="number" class="form-control sub-total-marginacion masc" name="altam" id="altam">
					                    </div>
					                </td>
					                <td>
					                    <div class="form-group">
											<input type="number" class="form-control sub-total-marginacion masc" name="mediam" id="mediam">
					                    </div>
					                </td>
					                <td>
					                    <div class="form-group">
											<input type="number" class="form-control sub-total-marginacion masc" name="bajam" id="bajam">
					                    </div>
					                </td>
					                <td>
					                    <div class="form-group">
											<input type="number" class="form-control sub-total-marginacion masc" name="muybajam" id="muybajam">
					                    </div>
					                </td>
					            </tr>
				            </tbody>
				            <tfoot>
				            	<tr>
					                <th colspan="2"><span class="fa fa-link"></span> Total</th>
					                <td colspan="2">
					                	<div class="form-group">
					                		<span class="fa fa-female"></span> <span id="total-marginacion-f" class=""></span>
					                	</div>
					                </td>
					                <td colspan="2">
					                	<div class="form-group">
					                		<span class="fa fa-male"></span> <span id="total-marginacion-m" class=""></span>
					                	</div>
					                </td>
					            </tr>
				            </tfoot>
				        </table>
					</div>
				</div>
			</div>
	    </div>
	</div>
</form>