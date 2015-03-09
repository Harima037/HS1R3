@extends('layouts.Modulo')

@section('title-page') Caratula de Captura @stop


@section('css')
@parent
<link href="{{ URL::to('css/chosen.bootstrap.min.css') }}" rel="stylesheet" type="text/css" media="screen">
@stop

@section('js')
@parent
<script src="{{ URL::to('js/dependencias/chosen.jquery.min.js') }}"></script>
<script src="{{ URL::to('js/lib/Confirm.js')}}"></script>
<script src="{{ URL::to('js/lib/Validation.js')}}"></script>
<script src="{{ URL::to('js/modulos/revision/revision-caratula.js')}}"></script>
@stop

@section('aside')
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
    	<div class="panel panel-default" id="datagridCaratulas">
            <div class="panel-heading">
                <h4><i class="fa fa-file"></i> <b>{{$clasificacion_proyecto}}</b> <small>({{$tipo_proyecto}})</small></h4>
            </div>
            <div class="panel-body" id="mensaje-espera">
                <div class="alert alert-info">
                    <h2><span class="fa fa-cog fa-spin"></span> Cargando información del formulario... por favor espere...</h2>
                </div>
            </div>
            <div class="panel-body hidden" id="panel-principal-formulario">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a id="tablink-caratula" href="#caratula-captura" role="tab" data-toggle="tab">
                            Carátula de Captura
                        </a>
                    </li>
                    <li role="presentation">
                        <a id="tablink-beneficiarios" href="#beneficiarios" role="tab" data-toggle="tab">
                            Beneficiarios 
                        </a>
                    </li>
                    <li role="presentation">
                        <a id="tablink-componentes" href="#componentes" role="tab" data-toggle="tab">
                            Componentes <span class="badge">0 / 2</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="caratula-captura">
                        <br>
                        {{$formulario}}
                    </div>
                    <div role="tabpanel" class="tab-pane" id="beneficiarios">
                        <br>
                        {{$beneficiarios}}
                    </div>
                    <div role="tabpanel" class="tab-pane" id="componentes">
                        <br>
                        {{$grid_componentes}}
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-default" id="btn-proyecto-cancelar">
                            <span class="fa fa-chevron-left"></span> Regresar a la lista de Proyectos
                        </button>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>
@stop

@section('modals')


<div class="modal fade" id="modalComentario" tabindex="-1" role="dialog" aria-labelledby="modalComentarioLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modalComentarioLabel">Escribir comentario </h4>
                </div>
                <div class="modal-body"> 
                    <form action="" id="formComentario">
                    	<input type="hidden" name="idproyectocomentarios" id="idproyectocomentarios">
                    	<input type="hidden" name="idcampo" id="idcampo">
                        <input type="hidden" name="tipocomentario" id="tipocomentario">
						<div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="informacioncampo" class="control-label" id="lbl-nombredelcampo"></label>
                                    <p id="lbl-informacioncampo" class="form-control" style="height:auto"></p>                                
                                 </div>                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="comentario" class="control-label">Comentario</label>
                                    <textarea class="form-control" name="comentario" id="comentario" rows="4"></textarea>
                                </div>                                
                            </div>
                        </div>
                        
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" id="btnGuardarComentario">Guardar comentario</button>
                    <button type="button" class="btn btn-danger" id="btnQuitarComentario">Quitar comentario</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
         
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->




@parent
@stop