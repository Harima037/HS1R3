@extends('layouts.Modulo')

@section('title-page') Formulario de Captura @stop


@section('css')
@parent
<link href="{{ URL::to('bootstrap/css/bootstrap-select.min.css') }}" rel="stylesheet" media="screen">
@stop

@section('js')
@parent
<script src="{{ URL::to('bootstrap/js/bootstrap-select.min.js') }}"></script>
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/poa/formulario-fibap.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
    	<div class="panel panel-default" id="formulario-fibap">
            <div class="panel-heading">
                <h4><i class="fa fa-file"></i> <b>FIBAP</b> <small>(Ficha de Información Básica del Proyecto)</small></h4>
            </div>
            <div class="panel-body">
                <form action="" id="form-fibap">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="organismopublico" class="control-label">Organismo Publico:</label>
                                <input type="text" class="form-control" id="organismopublico" name="organismopublico" maxlength="255"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="sector" class="control-label">Sector</label>
                                <input type="text" class="form-control" id="sector" name="sector" maxlength="255"/>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="subcomite" class="control-label">Subcomite</label>
                                <input type="text" class="form-control" id="subcomite" name="subcomite" maxlength="255"/>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="grupotrabajo" class="control-label">Grupo de Trabajo</label>
                                <input type="text" class="form-control" id="grupotrabajo" name="grupotrabajo" maxlength="255"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="programapresupuestal" class="control-label">Programa Presupuestario</label>
                                <select class="form-control" name="programapresupuestal" id="programapresupuestal">
                                    <option value=''>Seleccione un elemento</option>
                                    @foreach($programa_presupuestal as $item)
                                        <option value="{{ $item->id }}">{{$item->clave}}{{' '}}{{ $item->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="proyecto" class="control-label">Proyecto</label>
                                <input type="text" class="form-control" id="proyecto" name="proyecto" maxlength="255"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="coberturamunicipio" class="control-label">Cobertura/Municipio</label>
                                <input type="text" class="form-control" id="coberturamunicipio" name="coberturamunicipio" maxlength="255"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="ejerector" class="control-label">Eje Rector</label>
                                <select class="form-control" name="ejerector" id="ejerector">
                                    <option value=''>Seleccione un elemento</option>
                                    @foreach($objetivos_ped as $item)
                                        <option value="{{ $item->id }}">{{$item->clave}}{{' '}}{{ $item->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="politicapublica" class="control-label">Politica pública</label>
                                <select class="form-control" name="politicapublica" id="politicapublica">
                                     @if($objetivos_ped)
                                    <option value=''>Seleccione un elemento</option>
                                        @foreach($objetivos_ped as $item)
                                            <option value="{{ $item->id }}">{{$item->clave}}{{' '}}{{ $item->descripcion }}</option>
                                        @endforeach
                                    @else
                                    <option value=''>No hay elementos creados</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="objetivo" class="control-label">Objetivo</label>
                                <select class="form-control" name="objetivo" id="objetivo">
                                     @if($objetivos_ped)
                                    <option value=''>Seleccione un elemento</option>
                                        @foreach($objetivos_ped as $item)
                                            <option value="{{ $item->id }}">{{$item->clave}}{{' '}}{{ $item->descripcion }}</option>
                                        @endforeach
                                    @else
                                    <option value=''>No hay elementos creados</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="form-group">
                                <label for="documentacionsoporte" class="control-label">DOCUMENTACION SOPORTE</label>
                                
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="beneficiarios" class="control-label">BENEFICIARIOS</label>
                                
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                             <div class="form-group">
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label>
                                              <input type="checkbox"  id="impactoambiental" name="impactoambiental" >Estudio de Impacto ambiental
                                        </label>
                                    </div>
                                    
                                </div>
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox"  id="convenio" name="convenio" > Convenio o acuerdo
                                        </label>
                                    </div>
                                </div>
                             </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                             <div class="form-group">
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label>
                                             <input type="checkbox"  id="aceptacion" name="aceptacion" >Aceptacion de la comunidad
                                        </label>
                                    </div>
                                    
                                </div>
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox"  id="factibilidad" name="factibilidad"> Factibilidad de uso de suelo
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox"  id="validacionsectorial" name="validacionsectorial">  Acta de validacion sectorial
                                        </label>
                                    </div>
                                </div>
                             </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                             <div class="form-group">
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label>
                                             <input type="checkbox"  id="estudiotecnico" name="estudiotecnico" >Estudio tecnico y economico
                                        </label>
                                    </div>
                                    
                                </div>
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox"  id="proyectoejecutivo" name="proyectoejecutivo"> Proyecto ejecutivo
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox"  id="ipc" name="ipc">  Estudio de riesgo emitido por IPC
                                        </label>
                                    </div>
                                </div>
                             </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                             <div class="form-group">
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label>
                                             <input type="checkbox"  id="principalnormatividad" name="principalnormatividad" >Prinicpal normatividad (ROP, manual de procedimientos, manual de operacion)
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox"  id="certificadopropiedad" name="certificadopropiedad"> Certificado de propiedad del terreno
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox"  id="cpr" name="cpr">  Acta de validacion de COPLADER Y CPR
                                        </label>
                                    </div>
                                </div>
                             </div>
                        </div>
                    </div>
                    <input type="hidden" id="id" name="id">
                    <!--button type="submit" class="btn btn-primary btn-guardar">Guardar</button-->
                </form>
            </div>
        </div>
	</div>
</div>
@stop

@section('modals')
<div class="modal fade" id="modal-antecedentes" tabindex="-1" role="dialog" aria-labelledby="modalAntLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-85-screen">
        <div class="modal-content modal-content-85-screen">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalAntLabel">Nuevo</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-guardar" id="btn-antecedente-guardar">Guardar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@parent
@stop