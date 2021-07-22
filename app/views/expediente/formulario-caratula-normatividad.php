<div class="row">
    <form id="form_archivos">
        <div class="col-sm-6">
            <div class="form-group">
                <label class="control-label" for="titulo">Titulo</label>
                <input type="text" id="titulo" name="titulo" accept=".pdf" class="form-control">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label class="control-label" for="archivo-normatividad">Archivo (PDF)</label>
                <input type="file" id="archivo-normatividad" name="archivo-normatividad" accept=".pdf" class="form-control">
            </div>
        </div>
    </form>
    <div class="col-sm-12">
        <button type="button" class="btn btn-primary pull-right btn-subir-archivo" id="btn-subir-archivo">
            <span class="fa fa-upload"></span> Subir Archivo
        </button>
    </div>
</div>
<div class="row">
	<div class="col-sm-6 col-sm-offset-3">
		<div class="panel panel-default">
            <div class="panel-heading"><h4>Lista de Archivos Cargados</h4></div>
            <div id="normatividad-revision" class="panel-body text-warning hidden" style="font-weight:bold;"><span class="fa fa-warning"></span> <span id="normatividad-revision-comentario">Comentario de prueba</span></div>
            <table class="table table-striped table-hover table-condensed" id="tabla-lista-archivos">
                <thead>
                    <tr>
                        <th>Titulo</th>
                        <th>Archivo</th>
                        <th width="1">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
	</div>
</div>