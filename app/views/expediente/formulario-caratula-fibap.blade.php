<form id="form-fibap-datos">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="organismo-publico" class="control-label">Organismo Público</label>
                <input type="text" class="form-control" id="organismo-publico" name="organismo-publico"/>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="sector" class="control-label">Sector</label>
                <input type="text" class="form-control" id="sector" name="sector" maxlength="255"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="subcomite" class="control-label">Subcomite</label>
                <input type="text" class="form-control" id="subcomite" name="subcomite" maxlength="255"/>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="grupo-trabajo" class="control-label">Grupo de Trabajo</label>
                <input type="text" class="form-control" id="grupo-trabajo" name="grupo-trabajo" maxlength="255"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="justificacion-proyecto" class="control-label">Justificación del Proyecto</label>
                <textarea class="form-control" id="justificacion-proyecto" name="justificacion-proyecto"></textarea>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label for="descripcion-proyecto" class="control-label">Descripción del Proyecto</label>
                <textarea class="form-control" id="descripcion-proyecto" name="descripcion-proyecto"></textarea>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label for="objetivo-proyecto" class="control-label">Objetivo del Proyecto</label>
                <textarea class="form-control" id="objetivo-proyecto" name="objetivo-proyecto"></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <label class="control-label">Alineación a los objetivos de Desarrollo del milenio</label>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <small>
                    <label class="control-label" for="alineacion-especifica">Alineación especifica</label>
                </small>
                <input type="text" class="form-control" name="alineacion-especifica" id="alineacion-especifica">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <small>
                <label class="control-label" for="alineacion-general">Alineación general</label>
                </small>
                <input type="text" class="form-control" name="alineacion-general" id="alineacion-general">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-5">
            <div class="form-group">
                <label for="presupuesto-requerido" class="control-label">
                    Presupuesto Requerido
                </label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="fa fa-usd"></span>
                    </span>
                    <input type="number" class="form-control" id="presupuesto-requerido" name="presupuesto-requerido"/>
                </div>
            </div>
        </div>
        <div class="col-sm-7">
            <div class="form-group">
                <label for="periodoejecucion" class="control-label">Periodo de Ejecución</label>
                <div class="input-group" id="periodoejecucion">
                    <span class="input-group-addon">
                        Del
                    </span>
                    <input type="date" placeholder="aaaa-mm-dd" class="form-control" id="periodo-ejecucion-inicio" name="periodo-ejecucion-inicio">
                    <span class="input-group-addon">
                        Al
                    </span>
                    <input type="date" placeholder="aaaa-mm-dd" class="form-control" id="periodo-ejecucion-final" name="periodo-ejecucion-final">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-primary">
                <div class="panel-heading"><b>Documentación de soporte</b></div>
                <div class="panel-body" id="listado_documentos">
                    <div class="row">
                    @foreach ($documentos_soporte as $documento)
                        <div class="col-sm-4">
                            <div class="checkbox">
                                <label>
                                      <input type="checkbox"  id="documento_{{$documento->id}}" name="documento-soporte[]" value="{{$documento->id}}" >
                                      {{$documento->descripcion}}
                                </label>
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="id-fibap" name="id-fibap">
</form>
<div class="pull-right">
    <button type="button" class="btn btn-primary" id="btn-fibap-guardar">
        <span class="fa fa-save"></span> Guardar datos de la FIBAP
    </button>
</div>