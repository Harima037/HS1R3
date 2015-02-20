<form id="form_beneficiario">
	<input type="hidden" name="id-beneficiario" id="id-beneficiario">
	<div class="row">
		<div class="col-sm-4">
			<div class="form-group">
	            <label class="control-label" for="tipobeneficiario">Tipo de Beneficiario</label>
	            {{Form::select('tipobeneficiario',array('' =>'Selecciona uno') + $tipos_beneficiarios->lists('descripcion','id'),'',array('class'=>'form-control chosen-one','id'=>'tipobeneficiario'))}}
	        </div>
	    </div>
		<div class="col-sm-12">
	        <div class="row">
	            <div class="col-sm-3">
	                <div class="form-group">
		                <label class="control-label">Estadistica de Población</label>
		                <button type="button" class="btn btn-primary form-control"><span class="fa fa-table"></span> Ver</button>
		            </div>
	            </div>
	            <div class="col-sm-3">
	                <div class="form-group">
	                    <label class="control-label" for="totalbeneficiariosf">Femenino</label>
	                    <div class="input-group">
	                        <span class="input-group-addon"><span class="fa fa-female"></span></span>
	                        <input type="number" class="form-control benef-totales" name="totalbeneficiariosf" id="totalbeneficiariosf">
	                    </div>
	                </div>
	            </div>
	            <div class="col-sm-3">
	                <div class="form-group">
	                    <label class="control-label" for="totalbeneficiariosm">Masculino</label>
	                    <div class="input-group">
	                        <span class="input-group-addon"><span class="fa fa-male"></span></span>
	                        <input type="number" class="form-control benef-totales" name="totalbeneficiariosm" id="totalbeneficiariosm">
	                    </div>
	                </div>
	            </div>
	            <div class="col-sm-3">
	                <div class="form-group">
	                    <label class="control-label"><span class="fa fa-link"></span> Total</label>
	                    <span id="totalbeneficiarios" class="form-control"></span>
	                </div>
	            </div>
	        </div>
	    </div>
		<div class="col-sm-4">
	        <table class="table table-bordered table-condensed">
	            <tr><th colspan="2">Zona</th></tr>
	            <tr>
	                <th><span class="fa fa-female fa-2x"></span></th>
	                <th><span class="fa fa-male fa-2x"></span></th>
	            </tr>
	            <tr>
	                <th colspan="2">Urbana</th>
	            </tr>
	            <tr>
	                <td>
	                    <div class="form-group">
	                        <input type="number" class="form-control sub-total-zona fem" name="urbanaf" id="urbanaf">
	                    </div>
	                </td>
	                <td>
	                    <div class="form-group">
	                        <input type="number" class="form-control  sub-total-zona masc" name="urbanam" id="urbanam">
	                    </div>
	                </td>
	            </tr>
	            <tr>
	                <th colspan="2">Rural</th>
	            </tr>
	            <tr>
	                <td>
	                    <div class="form-group">
	                        <input type="number" class="form-control sub-total-zona fem" name="ruralf" id="ruralf">
	                    </div>
	                </td>
	                <td>
	                    <div class="form-group">
	                        <input type="number" class="form-control sub-total-zona masc" name="ruralm" id="ruralm">
	                    </div>
	                </td>
	            </tr>
	            <tr>
	                <th colspan="2"><span class="fa fa-link"></span> Totales</th>
	            </tr>
	            <tr>
	                <td><div class="form-group"><span id="total-zona-f" class="form-control"></span></div></td>
	                <td><div class="form-group"><span id="total-zona-m" class="form-control"></span></div></td>
	            </tr>
	        </table>
	    </div>

	    <div class="col-sm-4">
	        <table class="table table-bordered table-condensed">
	            <tr>
	                <th colspan="2">Población</th>
	            </tr>
	            <tr>
	                <th><span class="fa fa-female fa-2x"></span></th>
	                <th><span class="fa fa-male fa-2x"></span></th>
	            </tr>
	            <tr>
	                <th colspan="2">Mestiza</th>
	            </tr>
	            <tr>
	                <td>
	                    <div class="form-group">
	                        <input type="number" class="form-control sub-total-poblacion fem" name="mestizaf" id="mestizaf">
	                    </div>
	                </td>
	                <td>
	                    <div class="form-group">
	                        <input type="number" class="form-control sub-total-poblacion masc" name="mestizam" id="mestizam">
	                    </div>
	                </td>
	            </tr>
	            <tr>
	                <th colspan="2">Indigena</th>
	            </tr>
	            <tr>
	                <td>
	                    <div class="form-group">
	                        <input type="number" class="form-control sub-total-poblacion fem" name="indigenaf" id="indigenaf">
	                    </div>
	                </td>
	                <td>
	                    <div class="form-group">
	                        <input type="number" class="form-control sub-total-poblacion masc" name="indigenam" id="indigenam">
	                    </div>
	                </td>
	            </tr>
	            <tr>
	                <th colspan="2">Inmigrante</th>
	            </tr>
	            <tr>
	                <td>
	                    <div class="form-group">
	                        <input type="number" class="form-control sub-total-poblacion fem" name="inmigrantef" id="inmigrantef">
	                    </div>
	                </td>
	                <td>
	                    <div class="form-group">
	                        <input type="number" class="form-control sub-total-poblacion masc" name="inmigrantem" id="inmigrantem">
	                    </div>
	                </td>
	            </tr>
	            <tr>
	                <th colspan="2">Otros</th>
	            </tr>
	            <tr>
	                <td>
	                    <div class="form-group">
	                        <input type="number" class="form-control sub-total-poblacion fem" name="otrosf" id="otrosf">
	                    </div>
	                </td>
	                <td>
	                    <div class="form-group">
	                        <input type="number" class="form-control sub-total-poblacion masc" name="otrosm" id="otrosm">
	                    </div>
	                </td>
	            </tr>
	            <tr>
	                <th colspan="2"><span class="fa fa-link"></span> Totales</th>
	            </tr>
	            <tr>
	                <td><div class="form-group"><span id="total-poblacion-f" class="form-control"></span></div></td>
	                <td><div class="form-group"><span id="total-poblacion-m" class="form-control"></span></div></td>
	            </tr>
	        </table>
	    </div>

	    <div class="col-sm-4">
	        <table class="table table-bordered table-condensed">
	            <tr>
	                <th colspan="2">Marginación</th>
	            </tr>
	            <tr>
	                <th><span class="fa fa-female fa-2x"></span></th>
	                <th><span class="fa fa-male fa-2x"></span></th>
	            </tr>
	            <tr>
	                <th colspan="2">Muy alta</th>
	            </tr>
	            <tr>
	                <td>
	                    <div class="form-group">
	                        <input type="number" class="form-control sub-total-marginacion fem" name="muyaltaf" id="muyaltaf">
	                    </div>
	                </td>
	                <td>
	                    <div class="form-group">
	                        <input type="number" class="form-control sub-total-marginacion masc" name="muyaltam" id="muyaltam">
	                    </div>
	                </td>
	            </tr>
	            <tr>
	                <th colspan="2">Alta</th>
	            </tr>
	            <tr>
	                <td>
	                    <div class="form-group">
	                        <input type="number" class="form-control sub-total-marginacion fem" name="altaf" id="altaf">
	                    </div>
	                </td>
	                <td>
	                    <div class="form-group">
	                        <input type="number" class="form-control sub-total-marginacion masc" name="altam" id="altam">
	                    </div>
	                </td>
	            </tr>
	            <tr>
	                <th colspan="2">Media</th>
	            </tr>
	            <tr>
	                <td>
	                    <div class="form-group">
	                        <input type="number" class="form-control sub-total-marginacion fem" name="mediaf" id="mediaf">
	                    </div>
	                </td>
	                <td>
	                    <div class="form-group">
	                        <input type="number" class="form-control sub-total-marginacion masc" name="mediam" id="mediam">
	                    </div>
	                </td>
	            </tr>
	            <tr>
	                <th colspan="2">Baja</th>
	            </tr>
	            <tr>
	                <td>
	                    <div class="form-group">
	                        <input type="number" class="form-control sub-total-marginacion fem" name="bajaf" id="bajaf">
	                    </div>
	                </td>
	                <td>
	                    <div class="form-group">
	                        <input type="number" class="form-control sub-total-marginacion masc" name="bajam" id="bajam">
	                    </div>
	                </td>
	            </tr>
	            <tr>
	                <th colspan="2">Muy baja </th>
	            </tr>
	            <tr>
	                <td>
	                    <div class="form-group">
	                        <input type="number" class="form-control sub-total-marginacion fem" name="muybajaf" id="muybajaf">
	                    </div>
	                </td>
	                <td>
	                    <div class="form-group">
	                        <input type="number" class="form-control sub-total-marginacion masc" name="muybajam" id="muybajam">
	                    </div>
	                </td>
	            </tr>
	            <tr>
	                <th colspan="2"><span class="fa fa-link"></span> Totales</th>
	            </tr>
	            <tr>
	                <td><div class="form-group"><span id="total-marginacion-f" class="form-control"></span></div></td>
	                <td><div class="form-group"><span id="total-marginacion-m" class="form-control"></span></div></td>
	            </tr>
	        </table>
	    </div>
	</div>
</form>