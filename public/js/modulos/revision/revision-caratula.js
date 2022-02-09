
/*=====================================

    # Nombre:
        revision-caratula.js

    # Módulo:
        revision/revision-caratula

    # Descripción:
        Para el formulario de revisión de captura (Caratula de captura) de un proyecto

=====================================*/
// Declaracion de variables
var proyectoResource = new RESTfulRequests(SERVER_HOST+'/v1/revision-proyectos');

var componenteDatagrid = new Datagrid("#datagridComponentes",proyectoResource);
var actividadDatagrid = new Datagrid('#datagridActividades',proyectoResource);
var desgloseComponenteDatagrid = new Datagrid('#datagridDesgloseComponente',proyectoResource);

var comentariosArray = [];

var proyectoEstatusId = 0;

componenteDatagrid.init();
actividadDatagrid.init();
desgloseComponenteDatagrid.init();

var modal_componente = '#modalComponente';
var modal_actividad = '#modalActividad';

var grid_componentes = '#datagridComponentes';
var grid_actividades = '#datagridActividades';
var grid_desglose = '#datagridDesgloseComponente';

var form_caratula = '#form_caratula';
var form_componente = '#form_componente';
var form_actividad = '#form_actividad';

$('.chosen-one').chosen({width:'100%'});

window.onload = function () { 
	$('#mensaje-espera').addClass('hidden');
	$('#panel-principal-formulario').removeClass('hidden');
};
//***********************     Funcionalidad ejecutada al cargar la página    ************************************
if($('#id').val()){
	//load data
	proyectoResource.get($('#id').val(),null,{
        _success: function(response){
			proyectoEstatusId = response.data.idEstatusProyecto;

        	//inicializar_comportamiento_caratula();
        	if(response.data.lider_proyecto){
        		$('#lbl-lider-proyecto').html(response.data.lider_proyecto.nombre + '<br><small class="text-muted">'+response.data.lider_proyecto.cargo+'</small>');
        	}else{
        		$('#lbl-lider-proyecto').html('<span class="text-muted">No asignado</span>')
        	}
			if(response.data.jefe_inmediato){
				$('#lbl-jefe-inmediato').html(response.data.jefe_inmediato.nombre + '<br><small class="text-muted">'+response.data.jefe_inmediato.cargo+'</small>');
			}else{
				$('#lbl-jefe-inmediato').html('<span class="text-muted">No asignado</span>')
			}
			if(response.data.jefe_planeacion){
				$('#lbl-jefe-planeacion').html(response.data.jefe_planeacion.nombre + '<br><small class="text-muted">'+response.data.jefe_planeacion.cargo+'</small>');
			}else{
				$('#lbl-jefe-planeacion').html('<span class="text-muted">No asignado</span>')
			}
			if(response.data.coordinador_grupo_estrategico){
				$('#lbl-coordinador-grupo').html(response.data.coordinador_grupo_estrategico.nombre + '<br><small class="text-muted">'+response.data.coordinador_grupo_estrategico.cargo+'</small>');
			}else{
				$('#lbl-coordinador-grupo').html('<span class="text-muted">No asignado</span>')
			}

			$('#lbl-nombretecnico').text(response.data.nombreTecnico);
            $('#lbl-ejercicio').text(response.data.ejercicio);

			$('#lbl-tipoproyecto').text(response.data.tipo_proyecto.descripcion);
			$('#lbl-fechainicio').text(response.data.fechaInicio);
			$('#lbl-fechatermino').text((response.data.fechaTermino)?response.data.fechaTermino:'-');
			$('#lbl-finalidadproyecto').text(response.data.finalidadProyecto);
			
			$('#lbl-tipoaccion').text(response.data.tipo_accion.descripcion);
            $('#lbl-vinculacionped').text(response.data.objetivo_ped.clave+' - '+response.data.objetivo_ped.descripcion);
			
			//Clave presupuestaria
			$('#unidad_responsable').text(response.data.datos_unidad_responsable.clave);
            $('#finalidad').text(response.data.datos_finalidad.clave.slice(-1));
            $('#funcion').text(response.data.datos_funcion.clave.slice(-1));
            $('#subfuncion').text(response.data.datos_sub_funcion.clave.slice(-1));
            $('#subsubfuncion').text(response.data.datos_sub_sub_funcion.clave.slice(-1));
            $('#programa_sectorial').text(response.data.datos_programa_sectorial.clave );

            $('#programa_presupuestario').text(response.data.datos_programa_presupuestario.clave );
			if(response.data.datos_programa_presupuestario_indicadores){
				$('#titulo-programa-presupuestario').text(response.data.datos_programa_presupuestario.descripcion);
				$('#panel-programa-seleccionado').show();
				$('#tabla-indicadores-programa-presupuestario tbody').html('');
				var html_indicadores= '';
				for(var i in response.data.datos_programa_presupuestario_indicadores.indicadores_descripcion){
					var indicador = response.data.datos_programa_presupuestario_indicadores.indicadores_descripcion[i];
					html_indicadores += '<tr>';
					html_indicadores += '<td>'+indicador.claveTipoIndicador+'</td>';
					html_indicadores += '<td>'+indicador.descripcionIndicador+'</td>';
					html_indicadores += '<td>'+indicador.unidadMedida+'</td>';
					html_indicadores += '</tr>';
				}
				$('#tabla-indicadores-programa-presupuestario tbody').html(html_indicadores);
			}
			
            $('#origen_asignacion').text((response.data.datos_origen_asignacion)?response.data.datos_origen_asignacion.clave:'--' );
            $('#actividad_institucional').text((response.data.datos_actividad_institucional)?response.data.datos_actividad_institucional.clave:'--' );
            $('#proyecto_estrategico').text((response.data.datos_proyecto_estrategico)?response.data.datos_proyecto_estrategico.clave:'--');
            $('#no_proyecto_estrategico').text(("000" + response.data.numeroProyectoEstrategico).slice(-3));
			//Termina Clave presupuestaria
			//estrategia_estatal
			$('#lbl-estrategiapnd').text((response.data.estrategia_nacional)?response.data.estrategia_nacional.descripcion:'Sin Datos');
			$('#lbl-objetivoestrategico').text((response.data.objetivo_estrategico)?response.data.objetivo_estrategico.objetivoEstrategico:'Sin Datos');
			$('#lbl-alineacion').text((response.data.estrategia_estatal)?response.data.estrategia_estatal.claveAlineacion:'Sin Datos');
			$('#lbl-estrategiaestatal').text((response.data.estrategia_estatal)?response.data.estrategia_estatal.claveEstrategia+' '+response.data.estrategia_estatal.descripcion:'Sin Datos');
			
			var unidad_responsable_descripcion = '';
            if(response.data.datos_unidad_responsable){
                unidad_responsable_descripcion = response.data.datos_unidad_responsable.clave + ' - ' + response.data.datos_unidad_responsable.descripcion;
            }
            var sub_sub_funcion_descripcion = '';
            if(response.data.datos_sub_sub_funcion){
                sub_sub_funcion_descripcion = response.data.datos_sub_sub_funcion.clave + ' - ' + response.data.datos_sub_sub_funcion.descripcion;
            }
            var programa_sectorial_descripcion = '';
            if(response.data.datos_programa_sectorial){
                programa_sectorial_descripcion = response.data.datos_programa_sectorial.clave + ' - ' + response.data.datos_programa_sectorial.descripcion;
            }
            var programa_presupuestario_descripcion = '';
            if(response.data.datos_programa_presupuestario){
                programa_presupuestario_descripcion = response.data.datos_programa_presupuestario.clave + ' - ' + response.data.datos_programa_presupuestario.descripcion;
            }
            var origen_asignacion_descripcion = '';
            if(response.data.datos_origen_asignacion){
                origen_asignacion_descripcion = response.data.datos_origen_asignacion.clave + ' - ' + response.data.datos_origen_asignacion.descripcion;
            }
            var actividad_institucional_descripcion = '';
            if(response.data.datos_actividad_institucional){
                actividad_institucional_descripcion = response.data.datos_actividad_institucional.clave + ' - ' + response.data.datos_actividad_institucional.descripcion;
            }
            var proyecto_estrategico_descripcion = '';
            if(response.data.datos_proyecto_estrategico){
                proyecto_estrategico_descripcion = response.data.datos_proyecto_estrategico.clave + ' - ' + response.data.datos_proyecto_estrategico.descripcion;
            }

			$('#lbl-unidadresponsable').text(unidad_responsable_descripcion);
			$('#lbl-funciongasto').text(sub_sub_funcion_descripcion);
			$('#lbl-programasectorial').text(programa_sectorial_descripcion);			
			$('#lbl-programapresupuestario').text(programa_presupuestario_descripcion);
			$('#lbl-origenasignacion').text(origen_asignacion_descripcion);			
			$('#lbl-actividadinstitucional').text(actividad_institucional_descripcion);			
			$('#lbl-proyectoestrategico').text(proyecto_estrategico_descripcion);           
            $('#lbl-numeroproyectoestrategico').text(("000" + response.data.numeroProyectoEstrategico).slice(-3));
			
            $('#lbl-cobertura').text(response.data.cobertura.descripcion);
						
			if(response.data.cobertura.id == 1){
				$('#select-estado-panel').show();
				$('#select-municipio-panel').hide();
				$('#select-region-panel').hide();
			}else if(response.data.cobertura.id == 2){
				$('#select-estado-panel').hide();
				$('#select-municipio-panel').show();
				$('#select-region-panel').hide();
				$('#lbl-municipio').text(response.data.municipio.nombre);
			}else if(response.data.cobertura.id == 3){
				$('#select-estado-panel').hide();
				$('#select-municipio-panel').hide();
				$('#select-region-panel').show();
				$('#lbl-region').text(response.data.region.nombre);
			}else{
				$('#select-estado-panel').hide();
				$('#select-municipio-panel').hide();
				$('#select-region-panel').hide();
			}

			var archivos = response.data.archivos_normatividad;
			if(archivos.length > 0){
                var html_body = '';
                for(var i in archivos){
                    var archivo = archivos[i];
                    html_body += '<tr><td>'+archivo.titulo+'</td><td><a href="'+SERVER_HOST+'/ver-archivo/'+archivo.id+'" target="_blank">'+archivo.nombre+'</a></td></tr>';
                }
                $('#tabla-lista-archivos tbody').html(html_body);
            }else{
                $('#tabla-lista-archivos tbody').html('<tr><td colspan="3" style="text-align:center;">No se encontraron archivos</td></tr>')
            }
			
			var cuantasFilasBeneficiarios = response.data.beneficiarios.length;			
			var cadenaHTML = '<table width=100% class="table table-bordered table-condensed"><tr>';
			var encabezado = 0;
			var sePusoTotales = 0;
			var banderaCelda = 0;
			
			for(var cuentabeneficia in response.data.beneficiarios){
				if(encabezado==0){
					if(cuentabeneficia > 0){
						cadenaHTML = cadenaHTML + '<td style="background-color:black;" colspan="13"></td></tr><tr>';
					}
					cadenaHTML = cadenaHTML + '<td colspan="3"><strong> &nbsp;Tipo de Beneficiario: </strong></td><td colspan="7">&nbsp;'+response.data.beneficiarios[cuentabeneficia].tipo_beneficiario.descripcion+'</td><td style="vertical-align:middle" rowspan="2" colspan="2" align="center"> <button type="button" class="btn btn-default" id="beneficiario'+response.data.beneficiarios[cuentabeneficia].idTipoBeneficiario+'" onclick="escribirComentario(\'beneficiario'+response.data.beneficiarios[cuentabeneficia].idTipoBeneficiario+'\',\'Tipo de beneficiario\',\''+response.data.beneficiarios[cuentabeneficia].tipo_beneficiario.descripcion+'\')" ><i class="fa fa-pencil-square-o"></i>Comentar</button></td></tr><tr>';
					if(response.data.beneficiarios[cuentabeneficia].tipo_captura){
						cadenaHTML = cadenaHTML + '<td colspan="3"><strong> &nbsp;Tipo de Captura: </strong></td><td colspan="8">&nbsp;'+response.data.beneficiarios[cuentabeneficia].tipo_captura.descripcion+'</td></tr><tr style="background-color:whitesmoke;">';
					}else{
						cadenaHTML = cadenaHTML + '<td colspan="3"><strong> &nbsp;Tipo de Captura: </strong></td><td colspan="8">&nbsp;Sin Datos</td></tr><tr style="background-color:whitesmoke;">';
					}
					cadenaHTML = cadenaHTML + '<td colspan="2" rowspan="2" align="center"><strong>Total</strong></td><td rowspan="2" align="center"><strong>Género</strong></td><td colspan="2" align="center"><strong>Zona</strong></td><td colspan="2" align="center"><strong>Población</strong></td><td colspan="5" align="center"><strong>Marginación</strong></td></tr><tr style="background-color:whitesmoke;"><td align="center"><strong>Urbana</strong></td><td align="center"><strong>Rural</strong></td><td align="center"><strong>Mestiza</strong></td><td align="center"><strong>Indígena</strong></td><td align="center"><strong>Muy Alta</strong></td><td align="center"><strong>Alta</strong></td><td align="center"><strong>Media</strong></td><td align="center"><strong>Baja</strong></td><td align="center"><strong>Muy Baja</strong></td></tr><tr>';
					encabezado = 1;
				}
																		
				if((parseInt(cuentabeneficia,10)+2)<=cuantasFilasBeneficiarios){
					var siguienteIndice = parseInt(cuentabeneficia,10)+1;										
					if(response.data.beneficiarios[cuentabeneficia].idTipoBeneficiario == response.data.beneficiarios[siguienteIndice].idTipoBeneficiario){
						var sumaTotales = (parseInt(response.data.beneficiarios[cuentabeneficia].total) || 0) + (parseInt(response.data.beneficiarios[siguienteIndice].total) || 0);
						cadenaHTML = cadenaHTML +'<td rowspan="2" align="right">'+sumaTotales.format()+'</td>';
						sePusoTotales = 1;
					}else{
						if(sePusoTotales == 0){
							var sumaTotales = parseInt(response.data.beneficiarios[cuentabeneficia].total) || 0;
							cadenaHTML = cadenaHTML +'<td align="right">'+sumaTotales.format()+'</td>';
						}else{
							sePusoTotales = 0;
							banderaCelda = 1;
						}
						encabezado = 0;
					}
				}else{
					if(sePusoTotales == 0){
						var sumaTotales = parseInt(response.data.beneficiarios[cuentabeneficia].total) || 0;
						cadenaHTML = cadenaHTML +'<td align="right">'+sumaTotales.format()+'</td>';
					}else{
						banderaCelda = 1;
					}
					encabezado = 0;
				}
				
				cadenaHTML = cadenaHTML +'<td align="right">'+parseInt(response.data.beneficiarios[cuentabeneficia].total).format()+'</td>';

				if(response.data.beneficiarios[cuentabeneficia].sexo == 'f')
					cadenaHTML = cadenaHTML +'<td align="center"><span class="fa fa-female"></span> Femenino</td>';
				else
					cadenaHTML = cadenaHTML +'<td align="center"><span class="fa fa-male"></span> Masculino</td>';

				cadenaHTML = cadenaHTML +'<td align="right">'+parseInt(response.data.beneficiarios[cuentabeneficia].urbana).format()+'</td>';
				cadenaHTML = cadenaHTML +'<td align="right">'+parseInt(response.data.beneficiarios[cuentabeneficia].rural).format()+'</td>';
				cadenaHTML = cadenaHTML +'<td align="right">'+parseInt(response.data.beneficiarios[cuentabeneficia].mestiza).format()+'</td>';
				cadenaHTML = cadenaHTML +'<td align="right">'+parseInt(response.data.beneficiarios[cuentabeneficia].indigena).format()+'</td>';
				cadenaHTML = cadenaHTML +'<td align="right">'+parseInt(response.data.beneficiarios[cuentabeneficia].muyAlta).format()+'</td>';
				cadenaHTML = cadenaHTML +'<td align="right">'+parseInt(response.data.beneficiarios[cuentabeneficia].alta).format()+'</td>';
				cadenaHTML = cadenaHTML +'<td align="right">'+parseInt(response.data.beneficiarios[cuentabeneficia].media).format()+'</td>';
				cadenaHTML = cadenaHTML +'<td align="right">'+parseInt(response.data.beneficiarios[cuentabeneficia].baja).format()+'</td>';
				cadenaHTML = cadenaHTML +'<td align="right">'+parseInt(response.data.beneficiarios[cuentabeneficia].muyBaja).format()+'</td>';
				
				if(banderaCelda==1)
				{
					banderaCelda=0;
					//cadenaHTML = cadenaHTML + '<td>&nbsp;</td>';	
				}
				
				cadenaHTML = cadenaHTML + '</tr><tr>';
			}
			cadenaHTML = cadenaHTML + '</table>';
			
			$('#tabla-beneficiarios').html(cadenaHTML);		
			
			
			var financiaHTML = '';
			
			for(var fuente in response.data.fuentes_financiamiento)
			{
				financiaHTML += '<br>';
				financiaHTML += '<div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">';				
				financiaHTML += '<strong>Fuente de financiamiento: ';
				financiaHTML += '</strong>'+response.data.fuentes_financiamiento[fuente].fuente_financiamiento.descripcion;	
				financiaHTML += '<div style="text-align:right; float: right;"><strong> Programa y/o Fondo: </strong>';
				financiaHTML += response.data.fuentes_financiamiento[fuente].fondo_financiamiento.descripcion;				
				financiaHTML += '</div></h3></div><div class="panel-body">';
				
				financiaHTML += '<div class="col-sm-12"><strong>SubFuente de Financiamiento</strong></div>';
				
				for(var subfuente in response.data.fuentes_financiamiento[fuente].sub_fuentes_financiamiento)
				{
					var subf = response.data.fuentes_financiamiento[fuente].sub_fuentes_financiamiento[subfuente];
					
					financiaHTML += '<div class="col-sm-6"><strong>'+subf.clave+'</strong> '+subf.descripcion+'</div>';
				}
				financiaHTML += '<div class="col-sm-12"></div>';
				
				financiaHTML += '</div><div class="panel-footer"><button type="button" class="btn btn-default" id="financiamiento'+response.data.fuentes_financiamiento[fuente].id+'" onclick="escribirComentario(\'financiamiento'+response.data.fuentes_financiamiento[fuente].id+'\',\'Fuente de financiamiento\',\''+response.data.fuentes_financiamiento[fuente].fuente_financiamiento.descripcion+'\')" ><i class="fa fa-pencil-square-o"></i>Comentar</button></div>';
			}
			
			$('#tabla-fuentesfinanciamiento').html(financiaHTML);		
			
			
			if(response.data.idClasificacionProyecto==2)
			{
				var fibapHTML = '';
				
				fibapHTML = fibapHTML + '<div class="col-sm-6"><div class="form-group"><label class="control-label" for="lbl-organismo-publico">Organismo Público</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentario(\'organismo-publico\',\'Organismo Público\',\'lbl-organismo-publico\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-organismo-publico" class="form-control" style="height:auto">'+response.data.fibap.organismoPublico+'</p></div></div></div>';
				fibapHTML = fibapHTML + '<div class="col-sm-6"><div class="form-group"><label class="control-label" for="lbl-sector">Sector</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentario(\'sector\',\'Sector\',\'lbl-sector\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-sector" class="form-control" style="height:auto">'+response.data.fibap.sector+'</p></div></div></div>';
				fibapHTML = fibapHTML + '<div class="col-sm-6"><div class="form-group"><label class="control-label" for="lbl-subcomite">Subcomité</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentario(\'subcomite\',\'Subcomité\',\'lbl-subcomite\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-subcomite" class="form-control" style="height:auto">'+response.data.fibap.subcomite+'</p></div></div></div>';
				fibapHTML = fibapHTML + '<div class="col-sm-6"><div class="form-group"><label class="control-label" for="lbl-grupotrabajo">Grupo de trabajo</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentario(\'grupotrabajo\',\'Grupo de trabajo\',\'lbl-grupotrabajo\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-grupotrabajo" class="form-control" style="height:auto">'+response.data.fibap.grupoTrabajo+'</p></div></div></div>';				
				fibapHTML = fibapHTML + '<div class="col-sm-12"><div class="form-group"><label class="control-label" for="lbl-justificacionproyecto">Justificación del Proyecto</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentario(\'justificacionproyecto\',\'Justificación del Proyecto\',\'lbl-justificacionproyecto\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-justificacionproyecto" class="form-control" style="height:auto">'+response.data.fibap.justificacionProyecto+'</p></div></div></div>';				
				fibapHTML = fibapHTML + '<div class="col-sm-12"><div class="form-group"><label class="control-label" for="lbl-descripcionproyecto">Descripción del Proyecto</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentario(\'descripcionproyecto\',\'Descripción del Proyecto\',\'lbl-descripcionproyecto\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-descripcionproyecto" class="form-control" style="height:auto">'+response.data.fibap.descripcionProyecto+'</p></div></div></div>';
				fibapHTML = fibapHTML + '<div class="col-sm-12"><div class="form-group"><label class="control-label" for="lbl-objetivoproyecto">Objetivo del Proyecto</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentario(\'objetivoproyecto\',\'Objetivo del Proyecto\',\'lbl-objetivoproyecto\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-objetivoproyecto" class="form-control" style="height:auto">'+response.data.fibap.objetivoProyecto+'</p></div></div></div>';
				fibapHTML = fibapHTML + '<div class="col-sm-12"><div class="form-group"><label class="control-label">Alineación a los Objetivos de Desarrollo del Milenio</label></div></div>';
				fibapHTML = fibapHTML + '<div class="col-sm-6"><div class="form-group"><label class="control-label" for="lbl-alineacionespecifica">Alineación específica</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentario(\'alineacionespecifica\',\'Alineación específica\',\'lbl-alineacionespecifica\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-alineacionespecifica" class="form-control" style="height:auto">'+response.data.fibap.alineacionEspecifica+'</p></div></div></div>';
				fibapHTML = fibapHTML + '<div class="col-sm-6"><div class="form-group"><label class="control-label" for="lbl-alineaciongeneral">Alineación general</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentario(\'alineaciongeneral\',\'Alineación general\',\'lbl-alineaciongeneral\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-alineaciongeneral" class="form-control" style="height:auto">'+response.data.fibap.alineacionGeneral+'</p></div></div></div>';
				fibapHTML = fibapHTML + '<div class="col-sm-5"><div class="form-group"><label class="control-label" for="lbl-presupuestorequerido">Presupuesto requerido</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentario(\'presupuestorequerido\',\'Presupuesto requerido\',\'lbl-presupuestorequerido\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-presupuestorequerido" class="form-control" style="height:auto">'+response.data.fibap.presupuestoRequerido+'</p></div></div></div>';
												
				var fechaInicio = response.data.fibap.periodoEjecucionInicio.substr(8,2)+'/'+response.data.fibap.periodoEjecucionInicio.substr(5,2)+'/'+response.data.fibap.periodoEjecucionInicio.substr(0,4);
				var fechaFinal = response.data.fibap.periodoEjecucionFinal.substr(8,2)+'/'+response.data.fibap.periodoEjecucionFinal.substr(5,2)+'/'+response.data.fibap.periodoEjecucionFinal.substr(0,4);
				
				fibapHTML = fibapHTML + '<div class="col-sm-7"><div class="form-group"><label class="control-label" for="lbl-periodoejecucion">Periodo de ejecución</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentario(\'periodoejecucion\',\'Periodo de ejecución\',\'lbl-periodoejecucion\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-periodoejecucion" class="form-control" style="height:auto">Del: '+fechaInicio+' - Al: '+fechaFinal+'</p></div></div></div>';
				
				fibapHTML = fibapHTML +'<div class="row"><div class="col-sm-12"><div class="panel panel-primary"><div class="panel-heading"><b>Documentación de soporte</b></div><div class="panel-body"><div class="row">';
				
				fibapHTML = fibapHTML +'<div class="col-sm-4"><button type="button" class="btn btn-default" id="documentos'+response.data.fibap.id+'" onclick="escribirComentario(\'documentos'+response.data.fibap.id+'\',\'Documentación de soporte\',\''+response.data.fibap.descripcionProyecto+'\')" ><i class="fa fa-pencil-square-o"></i>Comentar</button></div>';
				
				for(var documento in response.data.fibap.documentos)
				{
					fibapHTML = fibapHTML +'<div class="col-sm-4">';
					fibapHTML = fibapHTML + '<div class="form-group"><label class="control-label" id="lbl-documento'+response.data.fibap.documentos[documento].id+'">'+response.data.fibap.documentos[documento].descripcion+'</label></div>';
					fibapHTML = fibapHTML +'</div>';
				}
                fibapHTML = fibapHTML +'</div></div></div></div></div>';		
				
				$('#panelfibap').html(fibapHTML);
				
				var antecedentesHTML = '';
				
				antecedentesHTML = antecedentesHTML +'<div class="row"><div class="col-sm-12"><div class="panel panel-default"><div class="panel-heading"><b>Antecedentes financieros</b></div><div class="panel-body"><div class="row">';
				
				antecedentesHTML = antecedentesHTML +'<div class="col-sm-4"><button type="button" class="btn btn-default" id="antecedentes'+response.data.fibap.id+'" onclick="escribirComentario(\'antecedentes'+response.data.fibap.id+'\',\'Antecedentes financieros\',\''+response.data.fibap.descripcionProyecto+'\')" ><i class="fa fa-pencil-square-o"></i>Comentar</button></div><div class="col-sm-8">&nbsp;</div></div><div class="row">';
				antecedentesHTML = antecedentesHTML +'<div class="col-sm-2"><strong>Año</strong></div>';
				antecedentesHTML = antecedentesHTML +'<div class="col-sm-3"><strong>Autorizado</strong></div>';
				antecedentesHTML = antecedentesHTML +'<div class="col-sm-3"><strong>Ejercido</strong></div>';
				antecedentesHTML = antecedentesHTML +'<div class="col-sm-2"><strong>%</strong></div>';
				antecedentesHTML = antecedentesHTML +'<div class="col-sm-2"><strong>Fecha de corte</strong></div>';
				
				for(var antecedente in response.data.fibap.antecedentes_financieros)
				{
					antecedentesHTML = antecedentesHTML +'<div class="col-sm-2">'+response.data.fibap.antecedentes_financieros[antecedente].anio+'</div>';
					antecedentesHTML = antecedentesHTML +'<div class="col-sm-3">'+response.data.fibap.antecedentes_financieros[antecedente].autorizado+'</div>';
					antecedentesHTML = antecedentesHTML +'<div class="col-sm-3">'+response.data.fibap.antecedentes_financieros[antecedente].ejercido+'</div>';
					antecedentesHTML = antecedentesHTML +'<div class="col-sm-2">'+response.data.fibap.antecedentes_financieros[antecedente].porcentaje+'</div>';
					antecedentesHTML = antecedentesHTML +'<div class="col-sm-2">'+response.data.fibap.antecedentes_financieros[antecedente].fechaCorte+'</div>';
				
				}
                antecedentesHTML = antecedentesHTML +'</div></div></div></div></div>';
							
				antecedentesHTML = antecedentesHTML + '<div class="col-sm-6"><div class="form-group"><label class="control-label" for="lbl-resultadosobtenidos">Resultados obtenidos</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentario(\'resultadosobtenidos\',\'Resultados obtenidos\',\'lbl-resultadosobtenidos\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-resultadosobtenidos" class="form-control" style="height:auto">'+response.data.fibap.resultadosObtenidos+'</p></div></div></div>';
				antecedentesHTML = antecedentesHTML + '<div class="col-sm-6"><div class="form-group"><label class="control-label" for="lbl-resultadosesperados">Resultados esperados</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentario(\'resultadosesperados\',\'Resultados esperados\',\'lbl-resultadosesperados\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-resultadosesperados" class="form-control" style="height:auto">'+response.data.fibap.resultadosEsperados+'</p></div></div></div>';
				$('#panelantecedentes').html(antecedentesHTML);
			}
			else
			{
				$('#fibap').hide();
				$('#tablink-fibap').hide();
				$('#antecedentes').hide();
				$('#tablink-antecedentes').hide();
			}
			
			var TabComponente = [];
			var contadorDeTabs = 1;
			
			for(var cuentaComponentes in response.data.componentes)
			{
				var idComponente = response.data.componentes[cuentaComponentes].id;				
				TabComponente[contadorDeTabs] = '<div class="col-sm-12">';				
				/*COMIENZA SECCIÓN DE OBJETIVOS*/
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<br><div class="col-sm-12 bg-info"><span class="fa fa-crosshairs"></span> <strong>Objetivo</strong></div>';
				if(response.data.idClasificacionProyecto==2)
				{
					if(response.data.componentes[cuentaComponentes].entregable){
						TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-entregable'+idComponente+'">Entregable</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'entregable|'+idComponente+'\',\'Entregable\',\'lbl-entregable'+idComponente+'\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-entregable'+idComponente+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].entregable.descripcion+'</p></div></div></div>';
					}else{
						TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-entregable'+idComponente+'">Entregable</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'entregable|'+idComponente+'\',\'Entregable\',\'lbl-entregable'+idComponente+'\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-entregable'+idComponente+'" class="form-control" style="height:auto"></p></div></div></div>';
					}
					
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-entregabletipo'+idComponente+'">Tipo</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'entregabletipo|'+idComponente+'\',\'Tipo\',\'lbl-entregabletipo'+idComponente+'\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-entregabletipo'+idComponente+'" class="form-control" style="height:auto">';
					if(response.data.componentes[cuentaComponentes].entregable_tipo){
						TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + response.data.componentes[cuentaComponentes].entregable_tipo.descripcion;
					}
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '</p></div></div></div>';

					if(response.data.componentes[cuentaComponentes].entregable_accion){
						TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-entregableaccion'+idComponente+'">Acción</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'entregableaccion|'+idComponente+'\',\'Acción\',\'lbl-entregableaccion'+idComponente+'\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-entregableaccion'+idComponente+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].entregable_accion.descripcion+'</p></div></div></div>';
					}else{
						TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-entregableaccion'+idComponente+'">Acción</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'entregableaccion|'+idComponente+'\',\'Acción\',\'lbl-entregableaccion'+idComponente+'\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-entregableaccion'+idComponente+'" class="form-control" style="height:auto"></p></div></div></div>';
					}
					
				}
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-12"><div class="form-group"><label class="control-label" for="lbl-descripcion'+idComponente+'">Descripción</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'descripcion-obj|'+idComponente+'\',\'Descripción\',\'lbl-descripcion-obj'+idComponente+'\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-descripcion-obj'+idComponente+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].objetivo+'</p></div></div></div>';
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-12"><div class="form-group"><label class="control-label" for="lbl-mediosverificacion'+idComponente+'">Medios de verificación</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'verificacion|'+idComponente+'\',\'Medios de verificación\',\'lbl-verificacion'+idComponente+'\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-verificacion'+idComponente+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].mediosVerificacion+'</p></div></div></div>';
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-12"><div class="form-group"><label class="control-label" for="lbl-supuestos'+idComponente+'">Supuestos</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'supuestos|'+idComponente+'\',\'Supuestos\',\'lbl-supuestos'+idComponente+'\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-supuestos'+idComponente+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].supuestos+'</p></div></div></div>';
				/*TERMINA SECCIÓN DE OBJETIVOS*/
				/*COMIENZA SECCIÓN DE INDICADOR*/				
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-12 bg-info"><span class="fa fa-line-chart"></span> <strong>Indicador</strong></div>';	
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-6"><div class="form-group"><label class="control-label" for="lbl-indicador'+idComponente+'">Descripción</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'descripcion-ind|'+idComponente+'\',\'Descripción\',\'lbl-descripcion-ind'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-descripcion-ind'+idComponente+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].indicador+'</p></div></div></div>';				
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-6"><div class="form-group"><label class="control-label" for="lbl-interpretacion'+idComponente+'">Interpretación</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'interpretacion|'+idComponente+'\',\'Interpretación\',\'lbl-interpretacion'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-interpretacion'+idComponente+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].interpretacion+'</p></div></div></div>';				
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-6"><div class="form-group"><label class="control-label" for="lbl-numerador-ind'+idComponente+'">Numerador</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'numerador-ind|'+idComponente+'\',\'Numerador\',\'lbl-numerador-ind'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-numerador-ind'+idComponente+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].numerador+'</p></div></div></div>';
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-6"><div class="form-group"><label class="control-label" for="lbl-denominador-ind'+idComponente+'">Denominador</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'denominador-ind|'+idComponente+'\',\'Denominador\',\'lbl-denominador-ind'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-denominador-ind'+idComponente+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].denominador+'</p></div></div></div>';
				
				if(response.data.componentes[cuentaComponentes].dimension == null){
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-dimension'+idComponente+'">Dimensión</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'dimension|'+idComponente+'\',\'Dimensión\',\'lbl-dimension'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-dimension'+idComponente+'" class="form-control" style="height:auto">&nbsp;</p></div></div></div>';
				}else{
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-dimension'+idComponente+'">Dimensión</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'dimension|'+idComponente+'\',\'Dimensión\',\'lbl-dimension'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-dimension'+idComponente+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].dimension.descripcion+'</p></div></div></div>';
				}
				if(response.data.componentes[cuentaComponentes].tipo_indicador == null){
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-3"><div class="form-group"><label class="control-label" for="lbl-tipo-ind'+idComponente+'">Tipo</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'tipo-ind|'+idComponente+'\',\'Tipo\',\'lbl-tipo-ind'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-tipo-ind'+idComponente+'" class="form-control" style="height:auto">&nbsp;</p></div></div></div>';
				}else{
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-3"><div class="form-group"><label class="control-label" for="lbl-tipo-ind'+idComponente+'">Tipo</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'tipo-ind|'+idComponente+'\',\'Tipo\',\'lbl-tipo-ind'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-tipo-ind'+idComponente+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].tipo_indicador.descripcion+'</p></div></div></div>';
				}
				
				if(response.data.componentes[cuentaComponentes].unidad_medida == null){
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-5"><div class="form-group"><label class="control-label" for="lbl-unidad-medida'+idComponente+'">Unidad de medida</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'unidad-medida|'+idComponente+'\',\'Unidad de medida\',\'lbl-unidad-medida'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-unidad-medida'+idComponente+'" class="form-control" style="height:auto">&nbsp;</p></div></div></div>';
				}else{
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-5"><div class="form-group"><label class="control-label" for="lbl-unidad-medida'+idComponente+'">Unidad de medida</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'unidad-medida|'+idComponente+'\',\'Unidad de medida\',\'lbl-unidad-medida'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-unidad-medida'+idComponente+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].unidad_medida.descripcion+'</p></div></div></div>';
				}
				

				if(response.data.componentes[cuentaComponentes].comportamiento_accion == null){
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-comportamiento'+idComponente+'">Comportamiento</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'comportamiento|'+idComponente+'\',\'Comportamiento\',\'lbl-comportamiento'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-comportamiento'+idComponente+'" class="form-control" style="height:auto">&nbsp;</p></div></div></div>';
				}else{
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-comportamiento'+idComponente+'">Comportamiento</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'comportamiento|'+idComponente+'\',\'Comportamiento\',\'lbl-comportamiento'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-comportamiento'+idComponente+'" class="form-control" style="height:auto">'+(response.data.componentes[cuentaComponentes].comportamiento_accion.clave + ' ' + response.data.componentes[cuentaComponentes].comportamiento_accion.descripcion)+'</p></div></div></div>';
				}
				
				if(response.data.componentes[cuentaComponentes].tipo_valor_meta == null){
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-tipo-valor-meta'+idComponente+'">Tipo de Valor de la Meta</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'tipo-valor-meta|'+idComponente+'\',\'Tipo de Valor de la Meta\',\'lbl-tipo-valor-meta'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-tipo-valor-meta'+idComponente+'" class="form-control" style="height:auto">&nbsp;</p></div></div></div>';
				}else{
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-tipo-valor-meta'+idComponente+'">Tipo de Valor de la Meta</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'tipo-valor-meta|'+idComponente+'\',\'Tipo de Valor de la Meta\',\'lbl-tipo-valor-meta'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-tipo-valor-meta'+idComponente+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].tipo_valor_meta.descripcion+'</p></div></div></div>';
				}
				
				/*TERMINA SECCIÓN DE INDICADOR*/

				/*COMIENZA SECCIÓN DE METAS*/				
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-12 bg-info"><span class="fa fa-table"></span> <strong> Metas</strong></div>';	
			
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-2"><div class="form-group"><label class="control-label" for="lbl-linea-base'+idComponente+'">Línea base</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'linea-base|'+idComponente+'\',\'Línea base\',\'lbl-linea-base'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-linea-base'+idComponente+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].lineaBase+'</p></div></div></div>';				
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-2"><div class="form-group"><label class="control-label" for="lbl-anio-base'+idComponente+'">Año base</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'anio-base|'+idComponente+'\',\'Año base\',\'lbl-anio-base'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-anio-base'+idComponente+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].anioBase+'</p></div></div></div>';

				if(response.data.componentes[cuentaComponentes].formula == null){
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-formula'+idComponente+'">Fórmula</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'formula|'+idComponente+'\',\'Fórmula\',\'lbl-formula'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-formula'+idComponente+'" class="form-control" style="height:auto">&nbsp;</p></div></div></div>';
				}else{
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-formula'+idComponente+'">Fórmula</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'formula|'+idComponente+'\',\'Fórmula\',\'lbl-formula'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-formula'+idComponente+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].formula.descripcion+'</p></div></div></div>';
				}
				
				if(response.data.componentes[cuentaComponentes].frecuencia == null){
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-frecuencia'+idComponente+'">Frecuencia</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'frecuencia|'+idComponente+'\',\'Frecuencia\',\'lbl-frecuencia'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-frecuencia'+idComponente+'" class="form-control" style="height:auto">&nbsp;</p></div></div></div>';
				}else{
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-frecuencia'+idComponente+'">Frecuencia</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'frecuencia|'+idComponente+'\',\'Frecuencia\',\'lbl-frecuencia'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-frecuencia'+idComponente+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].frecuencia.descripcion+'</p></div></div></div>';
				}				
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-12">';
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<table width=100% class="table table-bordered table-condensed"><tr>';
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<th>Mes</th><th>ENE</th><th>FEB</th><th>MAR</th><th>ABR</th><th>MAY</th><th>JUN</th><th>JUL</th><th>AGO</th><th>SEP</th><th>OCT</th><th>NOV</th><th>DIC</th></tr>';
				
				var trim = {};
				trim[1] = 0.0; trim[2] = 0.0; trim[3] = 0.0; trim[4] = 0.0;
				
				for(var cuentaJuris in response.data.jurisdicciones)
				{					
					var mes = {};
					var juris = response.data.jurisdicciones[cuentaJuris];					
					mes['1'] = '<td>0</td>';mes['2'] = '<td>0</td>';mes['3'] = '<td>0</td>';
					mes['4'] = '<td>0</td>';mes['5'] = '<td>0</td>';mes['6'] = '<td>0</td>';
					mes['7'] = '<td>0</td>';mes['8'] = '<td>0</td>';mes['9'] = '<td>0</td>';
					mes['10'] = '<td>0</td>';mes['11'] = '<td>0</td>';mes['12'] = '<td>0</td>';
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<tr>';
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<td><strong>' + juris + '</strong></td>';
					for(var cuentaMeses in response.data.componentes[cuentaComponentes].metas_mes)										
					{
						//console.log(juris);
						if(juris == response.data.componentes[cuentaComponentes].metas_mes[cuentaMeses].claveJurisdiccion)
						{
							var valorTrim = Math.ceil(parseFloat(response.data.componentes[cuentaComponentes].metas_mes[cuentaMeses].mes)/3) ;
							trim[valorTrim] = trim[valorTrim] + parseFloat(response.data.componentes[cuentaComponentes].metas_mes[cuentaMeses].meta);								
							//console.log(response.data.componentes[cuentaComponentes].metas_mes[cuentaMeses].meta);
							mes[response.data.componentes[cuentaComponentes].metas_mes[cuentaMeses].mes] = '<td>'+response.data.componentes[cuentaComponentes].metas_mes[cuentaMeses].meta+'</td>';
						}
					}
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + mes['1'] + mes['2'] + mes['3'] + mes['4'] + mes['5'] + mes['6'] + mes['7'] + mes['8'] + mes['9'] + mes['10'] + mes['11'] + mes['12']; 
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '</tr>';
				}
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '</table></div>';
				
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-6 bg-success">';
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-3"><div class="form-group"><label class="control-label" for="lbl-trim1|'+idComponente+'">Trim 1</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'trim1'+idComponente+'\',\'Trim 1\',\'lbl-trim1'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-trim1'+idComponente+'" class="form-control" style="height:auto">'+trim[1]+'</p></div></div></div>';
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-3"><div class="form-group"><label class="control-label" for="lbl-trim2|'+idComponente+'">Trim 2</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'trim2'+idComponente+'\',\'Trim 2\',\'lbl-trim2'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-trim2'+idComponente+'" class="form-control" style="height:auto">'+trim[2]+'</p></div></div></div>';				
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-3"><div class="form-group"><label class="control-label" for="lbl-trim3|'+idComponente+'">Trim 3</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'trim3'+idComponente+'\',\'Trim 3\',\'lbl-trim3'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-trim3'+idComponente+'" class="form-control" style="height:auto">'+trim[3]+'</p></div></div></div>';
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-3"><div class="form-group"><label class="control-label" for="lbl-trim4|'+idComponente+'">Trim 4</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'trim4'+idComponente+'\',\'Trim 4\',\'lbl-trim4'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-trim4'+idComponente+'" class="form-control" style="height:auto">'+trim[4]+'</p></div></div></div>';
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '</div>';
				
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-6">';
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-valornumerador'+idComponente+'">Numerador</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'valornumerador|'+idComponente+'\',\'Numerador\',\'lbl-valornumerador'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-valornumerador'+idComponente+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].valorNumerador+'</p></div></div></div>';
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-valordenominador'+idComponente+'">Denominador</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'valordenominador|'+idComponente+'\',\'Denominador\',\'lbl-valordenominador'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-valordenominador'+idComponente+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].valorDenominador+'</p></div></div></div>';
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-metaindicador'+idComponente+'">Meta</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioComponente(\'metaindicador|'+idComponente+'\',\'Meta\',\'lbl-metaindicador'+idComponente+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-metaindicador'+idComponente+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].metaIndicador+'</p></div></div></div>';
				
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '</div>';				
				/*TERMINA SECCIÓN DE METAS*/
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-12"><p>&nbsp;</p></div>';
				/*COMIENZA SECCIÓN DE PRESUPUESTO*/
				if(response.data.idClasificacionProyecto==2)
				{
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<br><div class="col-sm-12 bg-info"><span class="fa fa-usd"></span> <strong>Presupuesto</strong></div>';
					var presupuestoHTML = '';					
					presupuestoHTML = presupuestoHTML +'<div class="col-sm-12"><div class="panel panel-default"><div class="panel-heading"><div class="row"><div class="col-sm-12"><strong>Partidas capturadas</strong></div></div></div><div class="panel-body">';
					
					presupuestoHTML = presupuestoHTML +'<div class="col-sm-12"><button type="button" class="btn btn-default" id="partidas'+idComponente+'" onclick="escribirComentarioComponente(\'partidas|'+idComponente+'\',\'Partidas del componente\',\''+response.data.componentes[cuentaComponentes].objetivo+'\')" ><i class="fa fa-pencil-square-o"></i>Comentar</button></div>';
					presupuestoHTML = presupuestoHTML +'<div class="col-sm-2"><strong>Clave</strong></div>';
					presupuestoHTML = presupuestoHTML +'<div class="col-sm-10"><strong>Descripción</strong></div>';
					for(var partida in response.data.componentes[cuentaComponentes].accion.partidas)
					{
						presupuestoHTML = presupuestoHTML +'<div class="col-sm-2">'+response.data.componentes[cuentaComponentes].accion.partidas[partida].clave+'</div>';
						presupuestoHTML = presupuestoHTML +'<div class="col-sm-10">'+response.data.componentes[cuentaComponentes].accion.partidas[partida].descripcion+'</div>';
					}
					presupuestoHTML = presupuestoHTML +'</div></div></div>';
					presupuestoHTML = presupuestoHTML +'<br><div class="col-sm-12"><strong>Origen del presupuesto</strong></div>';
					
					for(var propuesta in response.data.componentes[cuentaComponentes].accion.propuestas_financiamiento)
					{
						var descripcionpropuesta = response.data.componentes[cuentaComponentes].accion.propuestas_financiamiento[propuesta].origen.descripcion;
						presupuestoHTML = presupuestoHTML + '<div class="col-sm-4">';
						presupuestoHTML = presupuestoHTML + '<div class="form-group">';
						presupuestoHTML = presupuestoHTML + '<label class="control-label" for="lbl-propuesta'+descripcionpropuesta+idComponente+'">'+descripcionpropuesta+'</label>';
						presupuestoHTML = presupuestoHTML + '<div class="input-group">';
						presupuestoHTML = presupuestoHTML + '<span class="input-group-btn" onclick="escribirComentarioComponente(\'propuesta'+descripcionpropuesta+'|'+idComponente+'\',\''+descripcionpropuesta+'\',\'lbl-propuesta'+descripcionpropuesta+idComponente+'\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-propuesta'+response.data.componentes[cuentaComponentes].accion.propuestas_financiamiento[propuesta].origen.descripcion+idComponente+'" class="form-control" style="height:auto"> $'+response.data.componentes[cuentaComponentes].accion.propuestas_financiamiento[propuesta].cantidad+'</p>';
						presupuestoHTML = presupuestoHTML + '</div>';
						presupuestoHTML = presupuestoHTML + '</div>';
						presupuestoHTML = presupuestoHTML + '</div>';
					}
					
					presupuestoHTML = presupuestoHTML +'<br><div class="col-sm-12">&nbsp;</div>';
					
					presupuestoHTML = presupuestoHTML + '<div class="col-sm-4"><div class="form-group">';
					presupuestoHTML = presupuestoHTML + '<label class="control-label" for="lbl-presupuestorequerido'+idComponente+'">Presupuesto Requerido</label>';
					presupuestoHTML = presupuestoHTML + '<div class="input-group">';
					
					presupuestoHTML = presupuestoHTML + '<span class="input-group-btn" onclick="escribirComentarioComponente(\'presupuestorequerido|'+idComponente+'\',\'Presupuesto Requerido\',\'lbl-presupuestorequerido'+idComponente+'\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-presupuestorequerido'+idComponente+'" class="form-control" style="height:auto"> $'+response.data.componentes[cuentaComponentes].accion.presupuestoRequerido+'</p>';
					presupuestoHTML = presupuestoHTML + '</div>';
					presupuestoHTML = presupuestoHTML + '</div></div>';
					
					presupuestoHTML = presupuestoHTML + '<br><div class="col-sm-12 bg-info"><span class="fa fa-usd"></span><span class="fa fa-usd"></span> <strong>Distribución del Presupuesto</strong></div><br>';
					
					presupuestoHTML = presupuestoHTML + '<table class="table table-striped table-hover">';
					presupuestoHTML = presupuestoHTML + '<thead>';
					presupuestoHTML = presupuestoHTML + '<tr>';
					presupuestoHTML = presupuestoHTML + '<th>Localidad</th>';
					presupuestoHTML = presupuestoHTML + '<th>Municipio</th>';
					presupuestoHTML = presupuestoHTML + '<th>Jurisdicción</th>';
					presupuestoHTML = presupuestoHTML + '<th>Monto</th>';
					presupuestoHTML = presupuestoHTML + '<th>Ver</th>';
					presupuestoHTML = presupuestoHTML + '<th>Comentario</th>';
					presupuestoHTML = presupuestoHTML + '</tr></thead>';
					presupuestoHTML = presupuestoHTML + '<tbody>';

					for(var localidad in response.data.componentes[cuentaComponentes].accion.desglose_presupuesto_componente)
					{
						var local = response.data.componentes[cuentaComponentes].accion.desglose_presupuesto_componente[localidad].localidad;
						var munic = response.data.componentes[cuentaComponentes].accion.desglose_presupuesto_componente[localidad].municipio;
						var juris = response.data.componentes[cuentaComponentes].accion.desglose_presupuesto_componente[localidad].jurisdiccion;
						var descripcion = response.data.componentes[cuentaComponentes].indicador;
						var unidadmedida = response.data.componentes[cuentaComponentes].unidad_medida.descripcion;
						var idDelDesglose = response.data.componentes[cuentaComponentes].accion.desglose_presupuesto_componente[localidad].id;
						
						presupuestoHTML = presupuestoHTML +'<tr>';
						presupuestoHTML = presupuestoHTML +'<td>'+local+'</td>';
						presupuestoHTML = presupuestoHTML +'<td>'+munic+'</td>';
						presupuestoHTML = presupuestoHTML +'<td>'+juris+'</td>';
						presupuestoHTML = presupuestoHTML +'<td> $ '+response.data.componentes[cuentaComponentes].accion.desglose_presupuesto_componente[localidad].presupuesto+'</td>';
						presupuestoHTML = presupuestoHTML +'<td><button class="btn btn-info" onclick="verdetallepresupuesto(\'componente\',\''+idDelDesglose+'\',\''+local+'\',\''+munic+'\',\''+juris+'\',\''+descripcion+'\',\''+unidadmedida+'\')"><i class="fa fa-eye"></i> Ver</button></td>';
						presupuestoHTML = presupuestoHTML +'<td><button id="desglose'+idComponente+'-'+idDelDesglose+'" class="btn btn-default" onclick="escribirComentarioComponente(\'desglose|'+idComponente+'-'+idDelDesglose+'\',\'Distribución del presupuesto en la Localidad:\',\''+local+'\');"><i class="fa fa-pencil-square-o"></i> Comentar</button></td>';
						presupuestoHTML = presupuestoHTML +'</tr>';
						//console.log(response.data.componentes[cuentaComponentes].accion.desglose_presupuesto[localidad].id);
					}
					presupuestoHTML = presupuestoHTML + '</tbody></table>';
					
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + presupuestoHTML;
				}				
				/*TERMINA SECCIÓN DE PRESUPUESTO*/
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-12"><p>&nbsp;</p></div>';								
				/*COMIENZA SECCIÓN DE ACTIVIDADES*/				
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-12 bg-info"><span class="fa fa-thumb-tack"></span> <strong> Actividades</strong></div><br>';
				var PanelDeActividades = [];
				var cuantasActividades = response.data.componentes[cuentaComponentes].actividades.length;
				var contadorDeActividades = 1;
				var actividades = '<br>';
				if(cuantasActividades > 0)
				{
					for(var indiceActividad in response.data.componentes[cuentaComponentes].actividades)
					{
						var idActividad = response.data.componentes[cuentaComponentes].actividades[indiceActividad].id;
						
						PanelDeActividades[contadorDeActividades] = '<div class="col-sm-12">';
						/*COMIENZA SECCIÓN DE OBJETIVOS*/
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<br><div class="col-sm-12 bg-info"><span class="fa fa-crosshairs"></span> <strong>Objetivo</strong></div>';						
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-12"><div class="form-group"><label class="control-label" for="lbl-descripcion-objactividad'+idActividad+'">Descripción</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'descripcion-obj|'+idActividad+'\',\'Descripción\',\'lbl-descripcion-objactividad'+idActividad+'\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-descripcion-objactividad'+idActividad+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].objetivo+'</p></div></div></div>';								
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-12"><div class="form-group"><label class="control-label" for="lbl-verificacionactividad'+idActividad+'">Medios de verificación</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'verificacion|'+idActividad+'\',\'Medios de Verificación\',\'lbl-verificacionactividad'+idActividad+'\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-verificacionactividad'+idActividad+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].mediosVerificacion+'</p></div></div></div>';									
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-12"><div class="form-group"><label class="control-label" for="lbl-supuestosactividad'+idActividad+'">Supuestos</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'supuestos|'+idActividad+'\',\'Supuestos\',\'lbl-supuestosactividad'+idActividad+'\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-supuestosactividad'+idActividad+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].supuestos+'</p></div></div></div>';
						/*TERMINA SECCIÓN DE OBJETIVOS*/
						/*COMIENZA SECCIÓN DE INDICADOR*/						
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-12 bg-info"><span class="fa fa-line-chart"></span> <strong>Indicador</strong></div>';	
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-6"><div class="form-group"><label class="control-label" for="lbl-descripcion-indactividad'+idActividad+'">Descripción</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'descripcion-ind|'+idActividad+'\',\'Descripción\',\'lbl-descripcion-indactividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-descripcion-indactividad'+idActividad+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].indicador+'</p></div></div></div>';	
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-6"><div class="form-group"><label class="control-label" for="lbl-interpretacionactividad'+idActividad+'">Interpretación</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'interpretacion|'+idActividad+'\',\'Interpretación\',\'lbl-interpretacionactividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-interpretacionactividad'+idActividad+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].interpretacion+'</p></div></div></div>';						
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-6"><div class="form-group"><label class="control-label" for="lbl-numerador-indactividad'+idActividad+'">Numerador</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'numerador-ind|'+idActividad+'\',\'Numerador\',\'lbl-numerador-indactividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-numerador-indactividad'+idActividad+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].numerador+'</p></div></div></div>';
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-6"><div class="form-group"><label class="control-label" for="lbl-denominador-indactividad'+idActividad+'">Denominador</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'denominador-ind|'+idActividad+'\',\'Denominador\',\'lbl-denominador-indactividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-denominador-indactividad'+idActividad+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].denominador+'</p></div></div></div>';
						if(response.data.componentes[cuentaComponentes].actividades[indiceActividad].dimension == null){
							PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-dimensionactividad'+idActividad+'">Dimensión</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'dimension|'+idActividad+'\',\'Dimensión\',\'lbl-dimensionactividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-dimensionactividad'+idActividad+'" class="form-control" style="height:auto">&nbsp;</p></div></div></div>';
						}else{
							PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-dimensionactividad'+idActividad+'">Dimensión</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'dimension|'+idActividad+'\',\'Dimensión\',\'lbl-dimensionactividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-dimensionactividad'+idActividad+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].dimension.descripcion+'</p></div></div></div>';
						}
						if(response.data.componentes[cuentaComponentes].actividades[indiceActividad].tipo_indicador == null){
							PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-3"><div class="form-group"><label class="control-label" for="lbl-tipo-indactividad'+idActividad+'">Tipo</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'tipo-ind|'+idActividad+'\',\'Tipo\',\'lbl-tipo-indactividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-tipo-indactividad'+idActividad+'" class="form-control" style="height:auto">&nbsp;</p></div></div></div>';
						}else{
							PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-3"><div class="form-group"><label class="control-label" for="lbl-tipo-indactividad'+idActividad+'">Tipo</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'tipo-ind|'+idActividad+'\',\'Tipo\',\'lbl-tipo-indactividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-tipo-indactividad'+idActividad+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].tipo_indicador.descripcion+'</p></div></div></div>';
						}
						if(response.data.componentes[cuentaComponentes].actividades[indiceActividad].unidad_medida == null){
							PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-5"><div class="form-group"><label class="control-label" for="lbl-unidad-medidaactividad'+idActividad+'">Unidad de medida</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'unidad-medida|'+idActividad+'\',\'Unidad de medida\',\'lbl-unidad-medidaactividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-unidad-medidaactividad'+idActividad+'" class="form-control" style="height:auto">&nbsp;</p></div></div></div>';
						}else{
							PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-5"><div class="form-group"><label class="control-label" for="lbl-unidad-medidaactividad'+idActividad+'">Unidad de medida</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'unidad-medida|'+idActividad+'\',\'Unidad de medida\',\'lbl-unidad-medidaactividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-unidad-medidaactividad'+idActividad+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].unidad_medida.descripcion+'</p></div></div></div>';
						}

						if(response.data.componentes[cuentaComponentes].actividades[indiceActividad].comportamiento_accion == null){
							PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-comportamientoactividad'+idActividad+'">Comportamiento</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'comportamiento|'+idActividad+'\',\'Comportamiento\',\'lbl-comportamientoactividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-comportamientoactividad'+idActividad+'" class="form-control" style="height:auto">&nbsp;</p></div></div></div>';
						}else{
							PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-comportamientoactividad'+idActividad+'">Comportamiento</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'comportamiento|'+idActividad+'\',\'Comportamiento\',\'lbl-comportamientoactividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-comportamientoactividad'+idActividad+'" class="form-control" style="height:auto">'+(response.data.componentes[cuentaComponentes].actividades[indiceActividad].comportamiento_accion.clave + ' ' + response.data.componentes[cuentaComponentes].actividades[indiceActividad].comportamiento_accion.descripcion)+'</p></div></div></div>';
						}
						
						if(response.data.componentes[cuentaComponentes].actividades[indiceActividad].tipo_valor_meta == null){
							PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-tipo-valor-metaactividad'+idActividad+'">Tipo de Valor de la Meta</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'tipo-valor-meta|'+idActividad+'\',\'Tipo de Valor de la Meta\',\'lbl-tipo-valor-metaactividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-tipo-valor-metaactividad'+idActividad+'" class="form-control" style="height:auto">&nbsp;</p></div></div></div>';
						}else{
							PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-tipo-valor-metaactividad'+idActividad+'">Tipo de Valor de la Meta</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'tipo-valor-meta|'+idActividad+'\',\'Tipo de Valor de la Meta\',\'lbl-tipo-valor-metaactividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-tipo-valor-metaactividad'+idActividad+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].tipo_valor_meta.descripcion+'</p></div></div></div>';
						}
						
						/*TERMINA SECCIÓN DE INDICADOR*/
						/*COMIENZA SECCIÓN DE METAS*/
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-12 bg-info"><span class="fa fa-table"></span> <strong> Metas</strong></div>';						
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-2"><div class="form-group"><label class="control-label" for="lbl-linea-baseactividad'+idActividad+'">Línea base</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'linea-base|'+idActividad+'\',\'Línea base\',\'lbl-linea-baseactividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-linea-baseactividad'+idActividad+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].lineaBase+'</p></div></div></div>';
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-2"><div class="form-group"><label class="control-label" for="lbl-anio-baseactividad'+idActividad+'">Año base</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'anio-base|'+idActividad+'\',\'Año base\',\'lbl-anio-baseactividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-anio-baseactividad'+idActividad+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].anioBase+'</p></div></div></div>';
						if(response.data.componentes[cuentaComponentes].actividades[indiceActividad].formula == null){
							PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-formulaactividad'+idActividad+'">Fórmula</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'formula|'+idActividad+'\',\'Fórmula\',\'lbl-formulaactividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-formulaactividad'+idActividad+'" class="form-control" style="height:auto">&nbsp;</p></div></div></div>';
						}else{
							PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-formulaactividad'+idActividad+'">Fórmula</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'formula|'+idActividad+'\',\'Fórmula\',\'lbl-formulaactividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-formulaactividad'+idActividad+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].formula.descripcion+'</p></div></div></div>';
						}
						if(response.data.componentes[cuentaComponentes].actividades[indiceActividad].frecuencia == null){
							PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-frecuenciaactividad'+idActividad+'">Frecuencia</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'frecuencia|'+idActividad+'\',\'Frecuencia\',\'lbl-frecuenciaactividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-frecuenciaactividad'+idActividad+'" class="form-control" style="height:auto"></p></div></div></div>';
						}else{
							PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-frecuenciaactividad'+idActividad+'">Frecuencia</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'frecuencia|'+idActividad+'\',\'Frecuencia\',\'lbl-frecuenciaactividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-frecuenciaactividad'+idActividad+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].frecuencia.descripcion+'</p></div></div></div>';
						}						
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-12">';
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<table width=100% class="table table-bordered table-condensed"><tr>';
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<th>Mes</th><th>ENE</th><th>FEB</th><th>MAR</th><th>ABR</th><th>MAY</th><th>JUN</th><th>JUL</th><th>AGO</th><th>SEP</th><th>OCT</th><th>NOV</th><th>DIC</th></tr>';
						
						var trim = {};
						trim[1] = 0.0; trim[2] = 0.0; trim[3] = 0.0; trim[4] = 0.0;
						
						for(var cuentaJuris in response.data.jurisdicciones)
						{
							var mes = {};
							var juris = response.data.jurisdicciones[cuentaJuris];
							mes['1'] = '<td>0</td>';mes['2'] = '<td>0</td>';mes['3'] = '<td>0</td>';
							mes['4'] = '<td>0</td>';mes['5'] = '<td>0</td>';mes['6'] = '<td>0</td>';
							mes['7'] = '<td>0</td>';mes['8'] = '<td>0</td>';mes['9'] = '<td>0</td>';
							mes['10'] = '<td>0</td>';mes['11'] = '<td>0</td>';mes['12'] = '<td>0</td>';
							PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<tr>';
							PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<td><strong>' + juris + '</strong></td>';
							for(var cuentaMeses in response.data.componentes[cuentaComponentes].actividades[indiceActividad].metas_mes)
							{
								if(juris == response.data.componentes[cuentaComponentes].actividades[indiceActividad].metas_mes[cuentaMeses].claveJurisdiccion)
								{
									var valorTrim = Math.ceil(parseFloat(response.data.componentes[cuentaComponentes].actividades[indiceActividad].metas_mes[cuentaMeses].mes)/3) ;
									trim[valorTrim] = trim[valorTrim] + parseFloat(response.data.componentes[cuentaComponentes].actividades[indiceActividad].metas_mes[cuentaMeses].meta);
									mes[response.data.componentes[cuentaComponentes].actividades[indiceActividad].metas_mes[cuentaMeses].mes] = '<td>'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].metas_mes[cuentaMeses].meta+'</td>';
								}
							}
							
							PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + mes['1'] + mes['2'] + mes['3'] + mes['4'] + mes['5'] + mes['6'] + mes['7'] + mes['8'] + mes['9'] + mes['10'] + mes['11'] + mes['12']; 
							PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '</tr>';
						}
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '</table></div>';
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-6 bg-success">';
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades]  + '<div class="col-sm-3"><div class="form-group"><label class="control-label" for="lbl-trim1actividad'+idActividad+'">Trim 1</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'trim1|'+idActividad+'\',\'Trim 1\',\'lbl-trim1actividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-trim1actividad'+idActividad+'" class="form-control" style="height:auto">'+trim[1]+'</p></div></div></div>';
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-3"><div class="form-group"><label class="control-label" for="lbl-trim2actividad'+idActividad+'">Trim 2</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'trim2|'+idActividad+'\',\'Trim 2\',\'lbl-trim2actividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-trim2actividad'+idActividad+'" class="form-control" style="height:auto">'+trim[2]+'</p></div></div></div>';						
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-3"><div class="form-group"><label class="control-label" for="lbl-trim3actividad'+idActividad+'">Trim 3</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'trim3|'+idActividad+'\',\'Trim 3\',\'lbl-trim3actividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-trim3actividad'+idActividad+'" class="form-control" style="height:auto">'+trim[3]+'</p></div></div></div>';
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-3"><div class="form-group"><label class="control-label" for="lbl-trim4actividad'+idActividad+'">Trim 4</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'trim4|'+idActividad+'\',\'Trim 4\',\'lbl-trim4actividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-trim4actividad'+idActividad+'" class="form-control" style="height:auto">'+trim[4]+'</p></div></div></div>';
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '</div>';
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-6">';
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-valornumeradoractividad'+idActividad+'">Numerador</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'valornumerador|'+idActividad+'\',\'Numerador\',\'lbl-valornumeradoractividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-valornumeradoractividad'+idActividad+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].valorNumerador+'</p></div></div></div>';
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-valordenominadoractividad'+idActividad+'">Denominador</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'valordenominador|'+idActividad+'\',\'Denominador\',\'lbl-valordenominadoractividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-valordenominadoractividad'+idActividad+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].valorDenominador+'</p></div></div></div>';
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '<div class="col-sm-4"><div class="form-group"><label class="control-label" for="lbl-metaindicadoractividad'+idActividad+'">Meta</label><div class="input-group"><span class="input-group-btn" onclick="escribirComentarioActividad(\'metaindicador|'+idActividad+'\',\'Meta\',\'lbl-metaindicadoractividad'+idActividad+'\');"><span class="btn btn-default"> <i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-metaindicadoractividad'+idActividad+'" class="form-control" style="height:auto">'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].metaIndicador+'</p></div></div></div>';
						PanelDeActividades[contadorDeActividades] = PanelDeActividades[contadorDeActividades] + '</div>';
						/*TERMINA SECCIÓN DE METAS*/

						PanelDeActividades[contadorDeActividades] += '<div class="col-sm-12"><p>&nbsp;</p></div>';
						/*COMIENZA SECCIÓN DE PRESUPUESTO*/
						if(response.data.idClasificacionProyecto==2)
						{
							PanelDeActividades[contadorDeActividades] += '<br><div class="col-sm-12 bg-info"><span class="fa fa-usd"></span> <strong>Presupuesto</strong></div>';
							var presupuestoHTML = '';					
							presupuestoHTML = presupuestoHTML +'<div class="col-sm-12"><div class="panel panel-default"><div class="panel-heading"><div class="row"><div class="col-sm-12"><strong>Partidas capturadas</strong></div></div></div><div class="panel-body">';
							
							presupuestoHTML = presupuestoHTML +'<div class="col-sm-12"><button type="button" class="btn btn-default" id="partidas'+idActividad+'" onclick="escribirComentarioComponente(\'partidas|'+idActividad+'\',\'Partidas del componente\',\''+response.data.componentes[cuentaComponentes].actividades[indiceActividad].objetivo+'\')" ><i class="fa fa-pencil-square-o"></i>Comentar</button></div>';
							presupuestoHTML = presupuestoHTML +'<div class="col-sm-2"><strong>Clave</strong></div>';
							presupuestoHTML = presupuestoHTML +'<div class="col-sm-10"><strong>Descripción</strong></div>';
							for(var partida in response.data.componentes[cuentaComponentes].actividades[indiceActividad].accion.partidas)
							{
								presupuestoHTML = presupuestoHTML +'<div class="col-sm-2">'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].accion.partidas[partida].clave+'</div>';
								presupuestoHTML = presupuestoHTML +'<div class="col-sm-10">'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].accion.partidas[partida].descripcion+'</div>';
							}
							presupuestoHTML = presupuestoHTML +'</div></div></div>';
							presupuestoHTML = presupuestoHTML +'<br><div class="col-sm-12"><strong>Origen del presupuesto</strong></div>';
							
							for(var propuesta in response.data.componentes[cuentaComponentes].actividades[indiceActividad].accion.propuestas_financiamiento)
							{
								var descripcionpropuesta = response.data.componentes[cuentaComponentes].actividades[indiceActividad].accion.propuestas_financiamiento[propuesta].origen.descripcion;
								presupuestoHTML = presupuestoHTML + '<div class="col-sm-4">';
								presupuestoHTML = presupuestoHTML + '<div class="form-group">';
								presupuestoHTML = presupuestoHTML + '<label class="control-label" for="lbl-propuesta'+descripcionpropuesta+idActividad+'">'+descripcionpropuesta+'</label>';
								presupuestoHTML = presupuestoHTML + '<div class="input-group">';
								presupuestoHTML = presupuestoHTML + '<span class="input-group-btn" onclick="escribirComentarioActividad(\'propuesta'+descripcionpropuesta+'|'+idActividad+'\',\''+descripcionpropuesta+'\',\'lbl-propuesta'+descripcionpropuesta+idActividad+'\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-propuesta'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].accion.propuestas_financiamiento[propuesta].origen.descripcion+idActividad+'" class="form-control" style="height:auto"> $'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].accion.propuestas_financiamiento[propuesta].cantidad+'</p>';
								presupuestoHTML = presupuestoHTML + '</div>';
								presupuestoHTML = presupuestoHTML + '</div>';
								presupuestoHTML = presupuestoHTML + '</div>';
							}
							
							presupuestoHTML = presupuestoHTML +'<br><div class="col-sm-12">&nbsp;</div>';
							
							presupuestoHTML = presupuestoHTML + '<div class="col-sm-4"><div class="form-group">';
							presupuestoHTML = presupuestoHTML + '<label class="control-label" for="lbl-presupuestorequerido'+idActividad+'">Presupuesto Requerido</label>';
							presupuestoHTML = presupuestoHTML + '<div class="input-group">';
							
							presupuestoHTML = presupuestoHTML + '<span class="input-group-btn" onclick="escribirComentarioActividad(\'presupuestorequerido|'+idActividad+'\',\'Presupuesto Requerido\',\'lbl-presupuestorequerido'+idActividad+'\');"><span class="btn btn-default"><i class="fa fa-pencil-square-o"></i></span></span><p id="lbl-presupuestorequerido'+idActividad+'" class="form-control" style="height:auto"> $'+response.data.componentes[cuentaComponentes].actividades[indiceActividad].accion.presupuestoRequerido+'</p>';
							presupuestoHTML = presupuestoHTML + '</div>';
							presupuestoHTML = presupuestoHTML + '</div></div>';
							
							presupuestoHTML = presupuestoHTML + '<br><div class="col-sm-12 bg-info"><span class="fa fa-usd"></span><span class="fa fa-usd"></span> <strong>Distribución del Presupuesto</strong></div><br>';
							
							presupuestoHTML = presupuestoHTML + '<table class="table table-striped table-hover">';
							presupuestoHTML = presupuestoHTML + '<thead>';
							presupuestoHTML = presupuestoHTML + '<tr>';
							presupuestoHTML = presupuestoHTML + '<th>Localidad</th>';
							presupuestoHTML = presupuestoHTML + '<th>Municipio</th>';
							presupuestoHTML = presupuestoHTML + '<th>Jurisdicción</th>';
							presupuestoHTML = presupuestoHTML + '<th>Monto</th>';
							presupuestoHTML = presupuestoHTML + '<th>Ver</th>';
							presupuestoHTML = presupuestoHTML + '<th>Comentario</th>';
							presupuestoHTML = presupuestoHTML + '</tr></thead>';
							presupuestoHTML = presupuestoHTML + '<tbody>';

							for(var localidad in response.data.componentes[cuentaComponentes].actividades[indiceActividad].accion.desglose_presupuesto_actividad)
							{
								var local = response.data.componentes[cuentaComponentes].actividades[indiceActividad].accion.desglose_presupuesto_actividad[localidad].localidad;
								var munic = response.data.componentes[cuentaComponentes].actividades[indiceActividad].accion.desglose_presupuesto_actividad[localidad].municipio;
								var juris = response.data.componentes[cuentaComponentes].actividades[indiceActividad].accion.desglose_presupuesto_actividad[localidad].jurisdiccion;
								var descripcion = response.data.componentes[cuentaComponentes].actividades[indiceActividad].indicador;
								var unidadmedida = response.data.componentes[cuentaComponentes].actividades[indiceActividad].unidad_medida.descripcion;
								var idDelDesglose = response.data.componentes[cuentaComponentes].actividades[indiceActividad].accion.desglose_presupuesto_actividad[localidad].id;
								
								presupuestoHTML += '<tr>';
								presupuestoHTML += '<td>'+local+'</td>';
								presupuestoHTML += '<td>'+munic+'</td>';
								presupuestoHTML += '<td>'+juris+'</td>';
								presupuestoHTML += '<td> $ '+response.data.componentes[cuentaComponentes].actividades[indiceActividad].accion.desglose_presupuesto_actividad[localidad].presupuesto+'</td>';
								presupuestoHTML += '<td><button class="btn btn-info" onclick="verdetallepresupuesto(\'actividad\',\''+idDelDesglose+'\',\''+local+'\',\''+munic+'\',\''+juris+'\',\''+descripcion+'\',\''+unidadmedida+'\')"><i class="fa fa-eye"></i> Ver</button></td>';
								presupuestoHTML += '<td><button id="desgloseactividad'+idActividad+'-'+idDelDesglose+'" class="btn btn-default" onclick="escribirComentarioActividad(\'desglose|'+idActividad+'-'+idDelDesglose+'\',\'Distribución del presupuesto en la Localidad:\',\''+local+'\');"><i class="fa fa-pencil-square-o"></i> Comentar</button></td>';
								presupuestoHTML += '</tr>';
								//console.log(response.data.componentes[cuentaComponentes].actividades[indiceActividad].accion.desglose_presupuesto[localidad].id);
							}
							presupuestoHTML = presupuestoHTML + '</tbody></table>';
							
							PanelDeActividades[contadorDeActividades] += presupuestoHTML;
						}				
						/*TERMINA SECCIÓN DE PRESUPUESTO*/


						PanelDeActividades[contadorDeActividades] += '</div>';
						contadorDeActividades++;
					}
					actividades = '<br><div role="tabpanel">';
					actividades = actividades + '<ul class="nav nav-tabs" role="tablist">';		
					var numeroDeActividad = 1;
					for(var i=0; i<cuantasActividades;i++)
					{
						actividades = actividades + '<li role="presentation" ';
						if(i==0)
							actividades = actividades + 'class="active"';
						actividades = actividades + '><a href="#TabDeActividad'+contadorDeTabs+'-'+numeroDeActividad+'" aria-controls="home" role="tab" data-toggle="tab">Actividad '+numeroDeActividad+'</a></li>';
						numeroDeActividad++;
					}
					actividades = actividades + '</ul>';
					numeroDeActividad = 1;
					actividades = actividades + '<div class="tab-content">';
					
					for(var j=0; j<cuantasActividades;j++)
					{
						actividades = actividades + '<div role="tabpanel" class="tab-pane';
							if(j==0) actividades = actividades + ' active';
						actividades = actividades + '" id="TabDeActividad'+contadorDeTabs+'-'+numeroDeActividad+'">';
						actividades = actividades + PanelDeActividades[numeroDeActividad];
						actividades = actividades + '</div>';
						numeroDeActividad++;
					}
					actividades = actividades + '</div></div>';
					
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + actividades;
				}
				else
				{
					TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '<div class="col-sm-12"> <strong> No se han descrito actividades aún</strong></div>';	
				}
				/*TERMINA SECCIÓN DE ACTIVIDADES*/
				TabComponente[contadorDeTabs] = TabComponente[contadorDeTabs] + '</div>';
				contadorDeTabs++;
			}
			
			var cadHTMLCom = '';
			var numeroDeComponente = 1;
			var cuantosComponentes = response.data.componentes.length;
			
			cadHTMLCom = '<div role="tabpanel">';
			cadHTMLCom = cadHTMLCom + '<ul class="nav nav-tabs" role="tablist">';
			for(var i=0; i<cuantosComponentes;i++)
			{
				cadHTMLCom = cadHTMLCom + '<li role="presentation" ';
				if(i===0)
					cadHTMLCom = cadHTMLCom + 'class="active"';
				cadHTMLCom = cadHTMLCom + '><a href="#TabDeComponente'+numeroDeComponente+'" aria-controls="home" role="tab" data-toggle="tab">Componente '+numeroDeComponente+'</a></li>';
				numeroDeComponente++;
			}
			cadHTMLCom = cadHTMLCom + '</ul>';
			numeroDeComponente = 1;
			cadHTMLCom = cadHTMLCom + '<div class="tab-content">';
			for(var j=0; j<cuantosComponentes;j++)
			{
				cadHTMLCom = cadHTMLCom + '<div role="tabpanel" class="tab-pane';
				if(j===0) cadHTMLCom = cadHTMLCom + ' active';
				cadHTMLCom = cadHTMLCom + '" id="TabDeComponente'+numeroDeComponente+'">';
				cadHTMLCom = cadHTMLCom + TabComponente[numeroDeComponente];
				cadHTMLCom = cadHTMLCom + '</div>';
				numeroDeComponente++;
			}
			cadHTMLCom = cadHTMLCom + '</div></div>';

			$('#panelTabsDeComponentes').html(cadHTMLCom);
			
			if(response.data.fuenteInformacion)
				$('#lbl-fuente-informacion').text(response.data.fuenteInformacion);
			if(response.data.idResponsable)
			{
				$('#lbl-responsable').text(response.data.responsable_informacion.nombre);
				$('#ayuda-responsable').html(response.data.responsable_informacion.cargo);
			}
			
			var alMenosUnElementoBorraron = 0;
			var elementosBorradosHTML = '';
			
			for(var contador in response.data.comentarios)
			{
				var NombreIdCampo = response.data.comentarios[contador]['idCampo'];
				var idCampo = '';
				var elementoExistente = 0;
				var objetoAColorear = '';
				
				for(var i=0; i<NombreIdCampo.length; i++)
					if(NombreIdCampo.substr(i,1)!='|')
						idCampo += NombreIdCampo.substr(i,1);
					else
						if(response.data.comentarios[contador]['tipoComentario']=='3')
							idCampo += 'actividad';
							
				if(response.data.comentarios[contador]['tipoComentario']=='1')//Tipo 1 = Proyecto
				{					
					if(idCampo.substr(0,14)=='financiamiento')
					{
						objetoAColorear = '#'+idCampo;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');
					}
					else if(idCampo.substr(0,12)=='beneficiario')
					{
						objetoAColorear = '#'+idCampo;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');	
					}
					else if(idCampo.substr(0,12)=='normatividad')
					{
						objetoAColorear = '#'+idCampo;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');	
					}
					else if(idCampo.substr(0,10)=='documentos')
					{
						objetoAColorear = '#'+idCampo;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');
					}
					else if(idCampo.substr(0,12)=='antecedentes')
					{
						objetoAColorear = '#'+idCampo;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');
					}
					else
					{
						objetoAColorear = '#lbl-'+idCampo;
						$(objetoAColorear).parent().parent().addClass('has-error has-feedback');
					}					
					comentariosArray.push([response.data.comentarios[contador]['id'],response.data.comentarios[contador]['idCampo'], response.data.comentarios[contador]['observacion'],'1']);
				}
				else if(response.data.comentarios[contador]['tipoComentario']=='2')//Tipo 2 = Componente
				{
					if(idCampo.substr(0,8)=='partidas')
					{
						objetoAColorear = '#'+idCampo;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');					
					}
					else if(idCampo.substr(0,8)=='desglose')
					{
						objetoAColorear = '#'+idCampo;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');					
					}					
					else
					{
						objetoAColorear = '#lbl-'+idCampo;
						$(objetoAColorear).parent().parent().addClass('has-error has-feedback');
					}
					comentariosArray.push([response.data.comentarios[contador]['id'],response.data.comentarios[contador]['idCampo'], response.data.comentarios[contador]['observacion'],'2']);
				}
				else if(response.data.comentarios[contador]['tipoComentario']=='3')//Tipo 3 = Actividad de Componente
				{
					if(idCampo.substr(0,8)=='partidas')
					{
						objetoAColorear = '#'+idCampo;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');					
					}
					else if(idCampo.substr(0,8)=='desglose')
					{
						objetoAColorear = '#'+idCampo;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');					
					}					
					else
					{
						objetoAColorear = '#lbl-'+idCampo;
						$(objetoAColorear).parent().parent().addClass('has-error has-feedback');
					}
					comentariosArray.push([response.data.comentarios[contador]['id'],response.data.comentarios[contador]['idCampo'], response.data.comentarios[contador]['observacion'],'3']);
				}
				
				if ( $(objetoAColorear).length ) { // hacer algo aquí si el elemento existe
					objetoAColorear = '';
				}
				else
				{
					alMenosUnElementoBorraron++;
					elementosBorradosHTML += '<div class="row" id="borrados'+alMenosUnElementoBorraron+'"><div class="col-sm-2">';
					if(response.data.comentarios[contador]['tipoComentario']=='1')
						elementosBorradosHTML += 'Datos de proyecto</div><div class="col-sm-2">'+idCampo+'</div>';
					else if(response.data.comentarios[contador]['tipoComentario']=='2')
						elementosBorradosHTML += 'Componente eliminado</div><div class="col-sm-2">'+idCampo+'</div>';
					else if(response.data.comentarios[contador]['tipoComentario']=='3')
						elementosBorradosHTML += 'Actividad eliminada</div><div class="col-sm-2">'+idCampo+'</div>';
					
					elementosBorradosHTML += '<div class="col-sm-6">'+response.data.comentarios[contador]['observacion']+'</div>';
                   	elementosBorradosHTML += '<div class="col-sm-2"><button type="button" class="btn btn-danger" onclick="elementoBorrado(\''+response.data.comentarios[contador]['id']+'\', \''+idCampo+'\', \''+response.data.comentarios[contador]['observacion']+'\', \'borrados'+alMenosUnElementoBorraron+'\');"><i class="fa fa-trash-o"></i> Eliminar</button></div>';
					elementosBorradosHTML += '</div>';
				}
			}	
			
			if(alMenosUnElementoBorraron>0)
			{
				var insertarHTML = '<div class="row"><div class="col-sm-2"><strong>Comentario de:</strong></div>';
				insertarHTML += '<div class="col-sm-2"><strong>Campo</strong></div>';
				insertarHTML += '<div class="col-sm-6"><strong>Observación</strong></div>';
				insertarHTML += '<div class="col-sm-2"><strong>Descartar comentario</strong></div></div>';

				insertarHTML += elementosBorradosHTML;

				$('#elementos-borrados').html(insertarHTML);
			}		
			else
			{
				$('#mensajes-sin-duenio').addClass('hidden');
			}
        }
    });
}

function elementoBorrado(id, campo, observacion, fila){
	
	Confirm.show({
			titulo:"Eliminar comentario del campo "+campo,
			mensaje: "¿Estás seguro de eliminar el comentario seleccionado?: "+observacion,
				callback: function(){
					proyectoResource.delete(id,null,{
                        _success: function(response){ 
                        	MessageManager.show({data:'Comentario eliminado con éxito.',type:'ADV',timer:3});

							var arrayTemporal = [];
							
							for(var i = 0; i < comentariosArray.length; i++)
								if(comentariosArray[i][0]!=id)
									arrayTemporal.push([comentariosArray[i][0],comentariosArray[i][1],comentariosArray[i][2],comentariosArray[i][3]]);
							comentariosArray.length=0;
							comentariosArray = arrayTemporal;
							$('#'+fila).addClass('hidden');
                        },
                        _error: function(jqXHR){ 
                        	MessageManager.show(jqXHR.responseJSON);
                        }
        			});
				}
		});
	
	
}

function construyebeneficiarios(datos){
	//console.log(datos);
	
    var beneficiarios = [];
    var beneficiario;
	var acumulaF = 0;
	var acumulaM = 0;
	
    for(var indx in datos){
       beneficiario = {};
       beneficiario.id = datos[indx].idTipoBeneficiario;
       beneficiario.tipoBeneficiario = datos[indx].tipo_beneficiario.descripcion;
       beneficiario.totalF = datos[indx].totalF;
       beneficiario.totalM = datos[indx].totalM;

	   acumulaF += beneficiario.totalF;
	   acumulaM += beneficiario.totalM;
	   beneficiarios[datos[indx].idTipoBeneficiario] = beneficiario;
    }
    $('#tabla_beneficiarios > tbody').empty();
    var html = '';
    for(var i in beneficiarios){
        html += '<tr>';
        html += '<td>' + beneficiarios[i].tipoBeneficiario + '</td>';
        html += '<td><div class="form-group"><input type="text" class="form-control" name="beneficiarios[' + beneficiarios[i].id + '][f]" id="beneficiarios-' + beneficiarios[i].id + '-f" data-tipo-beneficiario="' + beneficiarios[i].id + '" value="'+beneficiarios[i].totalF+'"></div></td>';
        html += '<td><div class="form-group"><input type="text" class="form-control" name="beneficiarios[' + beneficiarios[i].id + '][m]" id="beneficiarios-' + beneficiarios[i].id + '-m" data-tipo-beneficiario="' + beneficiarios[i].id + '" value="'+beneficiarios[i].totalM+'"></div></td>';
        html += '<td><span id="beneficiarios-' + beneficiarios[i].id + '-total">';
		html += beneficiarios[i].totalF+beneficiarios[i].totalM;
		'</span></td>';
        html += '</tr>';
    }
    $('#tabla_beneficiarios > tbody').html(html);
	$('#total-beneficiarios-lbl').html(acumulaF+acumulaM);       
}


function verdetallepresupuesto(tipoaccion, iddesglose, localidad, municipio, jurisdiccion, descri, udadmedida)
{
	var parametros = {};	
	parametros['ver'] = 'detalles-presupuesto';
	parametros['tipo-accion'] = tipoaccion;

	proyectoResource.get(iddesglose,parametros,{
        _success: function(response){

			$('#jurisdiccion-accion').val(jurisdiccion);
			$('#municipio-accion').val(municipio);
			$('#localidad-accion').val(localidad);
			construyebeneficiarios(response.data.desglose.beneficiarios);

			var partidaXMes = [];
			var cont = 1;
					
			for(var partida in response.data.partidas)
			{
				var partes = [];
				
				partes[1] = '<div class="input-group grupo-partida-presupuestal">';
				partes[1] += '<span class="input-group-addon" title="';
				partes[1] += response.data.partidas[partida].descripcion+'">';
				partes[1] += response.data.partidas[partida].clave;
				partes[1] += '</span>';
				partes[1] += '<input id="mes-';
				
				partes[2] = '-'+response.data.partidas[partida].id;
				
				partes[3] = '" name="mes-';
				
				partes[4] = '-'+response.data.partidas[partida].id;
				
				partes[5] = '" type="text" class="form-control presupuesto-mes" data-presupuesto-partida="'+response.data.partidas[partida].id;
				
				partes[6] = '" data-presupuesto-mes="';
				
				partes[7] = '" data-presupuesto-id="';
				
				partes[8] = response.data.partidas[partida].id+'"></div>';
				
				partidaXMes[cont] = partes;
				
				cont++;
			}
			
			var htmlPARTIDAS = '';
			var paraElMes = [];
			
			for(cont = 1; cont<=12; cont++)
			{
				var arrayAux = [];
				for(var i in partidaXMes)
				{
					arrayAux[i] = partidaXMes[i][1]+cont+partidaXMes[i][2]+partidaXMes[i][3]+cont+partidaXMes[i][4]+partidaXMes[i][5]+partidaXMes[i][6]+cont+partidaXMes[i][7]+cont+partidaXMes[i][8];
				}
				paraElMes[cont] = arrayAux;
			}
			
			var meses = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'];
			var numerodemes = 1;
			
			for(var mes in meses)
			{			
				htmlPARTIDAS += '<div class="col-sm-3"><div class="form-group grupo-partidas" data-grupo-mes="'+numerodemes+'">';
				htmlPARTIDAS += '<label class="control-label">'+meses[mes]+'</label>';
				for(var x in paraElMes[numerodemes])
					htmlPARTIDAS += paraElMes[numerodemes][x];			
				htmlPARTIDAS += '</div></div>';
				numerodemes++;
			}
			
			$('#calendario-partidas').html(htmlPARTIDAS);

			for(var j in response.data.calendarizado)
			{
				var objetoParaValor = '#mes-'+response.data.calendarizado[j].mes+'-'+response.data.calendarizado[j].idObjetoGasto;
				$(objetoParaValor).val(response.data.calendarizado[j].cantidad);
			}
			
			$('#cantidad-presupuesto-lbl').html(response.data.desglose.presupuesto);
			
			$('#indicador_texto').html(descri);
			$('#unidad_medida_texto').html(udadmedida);
			
			htmlMETAS = '';
			numerodemes = 1;
			for(var mes in meses)
			{
				htmlMETAS += '<div class="col-sm-4">';
				htmlMETAS += '<div class="form-group">';
				htmlMETAS += '<div class="input-group">';
				htmlMETAS += '<span class="input-group-addon">'+meses[mes]+'</span>';
				htmlMETAS += '<input id="meta-mes-'+numerodemes+'" name="meta-mes-'+numerodemes+'" type="text" class="form-control meta-mes" data-meta-mes="'+numerodemes+'" disabled="">' ;
				htmlMETAS += '</div></div></div>';
				numerodemes++;
			}
			
			$('#calendario-metas').html(htmlMETAS);
			
			var acumulaTrim = [];
			acumulaTrim[1] = 0.0; acumulaTrim[2] = 0.0; acumulaTrim[3] = 0.0; acumulaTrim[4] = 0.0;
			
			for(var meta in response.data.desglose.metas_mes)
			{
				var MesEntero = response.data.desglose.metas_mes[meta].mes;
				var objetoParaValor = '#meta-mes-'+MesEntero;
				$(objetoParaValor).val(response.data.desglose.metas_mes[meta].meta);

				var valorTrim = Math.ceil(parseFloat(MesEntero)/3);
				acumulaTrim[valorTrim] += parseFloat(response.data.desglose.metas_mes[meta].meta);
			}
			
			$('#trim1-lbl').html(acumulaTrim[1]);
			$('#trim2-lbl').html(acumulaTrim[2]);
			$('#trim3-lbl').html(acumulaTrim[3]);
			$('#trim4-lbl').html(acumulaTrim[4]);
			$('#cantidad-meta-lbl').html(acumulaTrim[1]+acumulaTrim[2]+acumulaTrim[3]+acumulaTrim[4]);
			
		}
	});
	
	
	$('#modal-presupuesto').modal('show');
}

function escribirComentario(idcampo,nombrecampo,objetoconinformacion)
{	
	if(proyectoEstatusId != 2){
		return false;
	}

	$('#modalComentario').find(".modal-title").html("<i class=\"fa fa-pencil-square-o\"></i> Escribir comentario");    
	$('#lbl-nombredelcampo').html(nombrecampo);
	$('#idcampo').val(idcampo);
	$('#tipocomentario').val('1');
	
	if(idcampo.substr(0,14) == 'financiamiento')
		$('#lbl-informacioncampo').text(objetoconinformacion);
	else if(idcampo.substr(0,12) == 'beneficiario')
		$('#lbl-informacioncampo').text(objetoconinformacion);
	else if(idcampo.substr(0,12) == 'normatividad')
		$('#lbl-informacioncampo').text(objetoconinformacion);
	else if(idcampo.substr(0,10) == 'documentos')
		$('#lbl-informacioncampo').text(objetoconinformacion);
	else if(idcampo.substr(0,12) == 'antecedentes')
		$('#lbl-informacioncampo').text(objetoconinformacion);
	else
		$('#lbl-informacioncampo').text($('#'+objetoconinformacion).text());
	var borrarAreaText = 1;
	
	for(var i = 0; i < comentariosArray.length; i++)
	{
		var arrayTemporal = comentariosArray[i];
		if(arrayTemporal[1]==idcampo)
		{
			$('#idproyectocomentarios').val(arrayTemporal[0]);
			//$('#comentario').text(arrayTemporal[2]);
			$('#comentario').val(arrayTemporal[2]);
			borrarAreaText = 0;
		}
	}
	if(borrarAreaText)
	{
		//$('#comentario').text('');
		$('#comentario').val('');
		$('#idproyectocomentarios').val('');
	}
    $('#modalComentario').modal('show');
}

function escribirComentarioComponente(idcampo,nombrecampo,objetoconinformacion)
{	
	if(proyectoEstatusId != 2){
		return false;
	}

	$('#modalComentario').find(".modal-title").html("<i class=\"fa fa-pencil-square-o\"></i> Escribir comentario");    
	$('#lbl-nombredelcampo').html(nombrecampo);
	$('#idcampo').val(idcampo);
	$('#tipocomentario').val('2');
	
	if(idcampo.substr(0,12) == 'beneficiario')
		$('#lbl-informacioncampo').text(objetoconinformacion);
	else if(idcampo.substr(0,8) == 'partidas')
		$('#lbl-informacioncampo').text(objetoconinformacion);
	else if(idcampo.substr(0,8) == 'desglose')
		$('#lbl-informacioncampo').text(objetoconinformacion);
	else
		$('#lbl-informacioncampo').text($('#'+objetoconinformacion).text());
	var borrarAreaText = 1;
	
	for(var i = 0; i < comentariosArray.length; i++)
	{
		var arrayTemporal = comentariosArray[i];
		if(arrayTemporal[1]==idcampo)
		{
			$('#idproyectocomentarios').val(arrayTemporal[0]);
			//$('#comentario').text(arrayTemporal[2]);
			$('#comentario').val(arrayTemporal[2]);
			borrarAreaText = 0;
		}
	}
	if(borrarAreaText)
	{
		//$('#comentario').text('');
		$('#comentario').val('');
		$('#idproyectocomentarios').val('');
	}
    $('#modalComentario').modal('show');
}

function escribirComentarioActividad(idcampo,nombrecampo,objetoconinformacion)
{	
	if(proyectoEstatusId != 2){
		return false;
	}

	$('#modalComentario').find(".modal-title").html("<i class=\"fa fa-pencil-square-o\"></i> Escribir comentario");    
	$('#lbl-nombredelcampo').html(nombrecampo);
	$('#idcampo').val(idcampo);
	$('#tipocomentario').val('3');
	
	if(idcampo.substr(0,12) == 'beneficiario')
		$('#lbl-informacioncampo').text(objetoconinformacion);
	else if(idcampo.substr(0,8) == 'partidas')
		$('#lbl-informacioncampo').text(objetoconinformacion);
	else if(idcampo.substr(0,8) == 'desglose')
		$('#lbl-informacioncampo').text(objetoconinformacion);
	else
		$('#lbl-informacioncampo').text($('#'+objetoconinformacion).text());
	var borrarAreaText = 1;
	
	for(var i = 0; i < comentariosArray.length; i++)
	{
		var arrayTemporal = comentariosArray[i];
		if(arrayTemporal[1]==idcampo)
		{
			$('#idproyectocomentarios').val(arrayTemporal[0]);
			//$('#comentario').text(arrayTemporal[2]);
			$('#comentario').val(arrayTemporal[2]);
			borrarAreaText = 0;
		}
	}
	if(borrarAreaText)
	{
		//$('#comentario').text('');
		$('#comentario').val('');
		$('#idproyectocomentarios').val('');
	}
    $('#modalComentario').modal('show');
}

$('#btnGuardarComentario').on('click',function(){
	
	var parametros = $(formComentario).serialize();
	parametros = parametros + '&idproyecto=' + $('#id').val()+'&tipocomentario='+$('#tipocomentario').val();
	
	var objetoQueSeColorea = '';
	
	for(var i=0; i<$('#idcampo').val().length; i++)
		if($('#idcampo').val().substr(i,1)!='|')
			objetoQueSeColorea += $('#idcampo').val().substr(i,1);
		else
			if($('#tipocomentario').val() == '3')
				objetoQueSeColorea += 'actividad';

	if($('#comentario').val()=="")
	{
		MessageManager.show({data:'Debe escribir un comentario antes de guardar',type:'ADV',timer:3});		
	}
	else
	{
		if($('#idproyectocomentarios').val()=='')//Nuevo comentario
		{
			proyectoResource.post(parametros,{
		        _success: function(response){
	    	        MessageManager.show({data:'Datos del comentario almacenados con éxito',type:'OK',timer:3});	
					
					
					if($('#idcampo').val().substr(0,14)=='financiamiento')
					{
						var objetoAColorear = '#'+objetoQueSeColorea;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');					
					}
					else if($('#idcampo').val().substr(0,12)=='beneficiario')
					{
						var objetoAColorear = '#'+objetoQueSeColorea;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');	
					}
					else if($('#idcampo').val().substr(0,12)=='normatividad')
					{
						var objetoAColorear = '#'+objetoQueSeColorea;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');	
					}
					else if($('#idcampo').val().substr(0,10)=='documentos')
					{
						var objetoAColorear = '#'+objetoQueSeColorea;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');
					}
					else if($('#idcampo').val().substr(0,12)=='antecedentes')
					{
						var objetoAColorear = '#'+objetoQueSeColorea;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');
					}
					else if($('#idcampo').val().substr(0,8)=='partidas')
					{
						var objetoAColorear = '#'+objetoQueSeColorea;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');
					}
					else if($('#idcampo').val().substr(0,8)=='desglose')
					{
						var objetoAColorear = '#'+objetoQueSeColorea;
						$(objetoAColorear).removeClass('btn-default');
						$(objetoAColorear).addClass('btn-warning');
					}						
					else
					{
						var objetoAColorear = '#lbl-'+objetoQueSeColorea;
						$(objetoAColorear).parent().parent().addClass('has-error has-feedback');
					}
					
					comentariosArray.push([response.data.id, $('#idcampo').val(), $('#comentario').val(), $('#tipocomentario').val()]);
	            	$('#modalComentario').modal('hide');
	    	    },
	        	_error: function(response){
	            	try{
	                	var json = $.parseJSON(response.responseText);
		                if(!json.code)
		                    MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
	    	            else{
	        	        	json.container = modal_actividad + ' .modal-body';
	            	        MessageManager.show(json);
		                }
		                Validation.formValidate(json.data);
	    	        }catch(e){
	        	        console.log(e);
	            	}
				}
		    });
		}
		else //Modificar comentario
		{
			proyectoResource.put($('#idproyectocomentarios').val(),parametros,{
	        	_success: function(response){
	    	        MessageManager.show({data:'Datos del comentario almacenados con éxito',type:'OK',timer:3});					
					for(var i = 0; i < comentariosArray.length; i++)
						if(comentariosArray[i][0]==$('#idproyectocomentarios').val())
							comentariosArray[i][2]=$('#comentario').val();
		            $('#modalComentario').modal('hide');
	        	},
	    	    _error: function(response){
		            try{
		                var json = $.parseJSON(response.responseText);
	                	if(!json.code)
	            	        MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
	        	        else{
	    	            	json.container = modal_actividad + ' .modal-body';
		                    MessageManager.show(json);
		                }
	                	Validation.formValidate(json.data);
	            	}catch(e){
	        	        console.log(e);
	    	        }                       
		        }
		    });
		}		
	}	
});

$('#btnQuitarComentario').on('click',function(){
	if($('#idproyectocomentarios').val()=='')//Nunca se guardó, por eso, no se borrará, solamente se cierra la ventana del modal
	{
		MessageManager.show({data:'Debe almacenar un comentario, antes de eliminarlo',type:'ADV',timer:3});
		$('#modalComentario').modal('hide');
	}
	else
	{
		Confirm.show({
			titulo:"Eliminar comentario",
			mensaje: "¿Estás seguro de eliminar el comentario seleccionado?",
				//si: 'Actualizar',
				//no: 'No, gracias',
				callback: function(){
					proyectoResource.delete($('#idproyectocomentarios').val(),null,{
                        _success: function(response){ 
                        	MessageManager.show({data:'Comentario eliminado con éxito.',type:'ADV',timer:3});

							var arrayTemporal = [];
							
							for(var i = 0; i < comentariosArray.length; i++)
								if(comentariosArray[i][0]!=$('#idproyectocomentarios').val())
									arrayTemporal.push([comentariosArray[i][0],comentariosArray[i][1],comentariosArray[i][2],comentariosArray[i][3]]);

							comentariosArray.length=0;
							comentariosArray = arrayTemporal;							
							//console.log(comentariosArray);
							
							var objetoADesColorear = '';
							for(var i=0; i<$('#idcampo').val().length; i++)
								if($('#idcampo').val().substr(i,1)!='|')
									objetoADesColorear += $('#idcampo').val().substr(i,1);
								else
									if($('#tipocomentario').val() == '3')
										objetoADesColorear += 'actividad';
									
							
							if($('#idcampo').val().substr(0,14)=='financiamiento')
							{
								var objetoADesColorear = '#'+objetoADesColorear;
								$(objetoADesColorear).removeClass('btn-warning');
								$(objetoADesColorear).addClass('btn-default');					
							}
							else if($('#idcampo').val().substr(0,12)=='beneficiario')
							{
								objetoADesColorear = '#'+objetoADesColorear;
								$(objetoADesColorear).removeClass('btn-warning');
								$(objetoADesColorear).addClass('btn-default');	
							}
							else if($('#idcampo').val().substr(0,12)=='normatividad')
							{
								objetoADesColorear = '#'+objetoADesColorear;
								$(objetoADesColorear).removeClass('btn-warning');
								$(objetoADesColorear).addClass('btn-default');	
							}
							else if($('#idcampo').val().substr(0,10)=='documentos')
							{
								objetoADesColorear = '#'+objetoADesColorear;
								$(objetoADesColorear).removeClass('btn-warning');
								$(objetoADesColorear).addClass('btn-default');	
							}
							else if($('#idcampo').val().substr(0,12)=='antecedentes')
							{
								objetoADesColorear = '#'+objetoADesColorear;
								$(objetoADesColorear).removeClass('btn-warning');
								$(objetoADesColorear).addClass('btn-default');	
							}
							else if($('#idcampo').val().substr(0,8)=='partidas')
							{
								objetoADesColorear = '#'+objetoADesColorear;
								$(objetoADesColorear).removeClass('btn-warning');
								$(objetoADesColorear).addClass('btn-default');	
							}
							else if($('#idcampo').val().substr(0,8)=='desglose')
							{
								objetoADesColorear = '#'+objetoADesColorear;
								$(objetoADesColorear).removeClass('btn-warning');
								$(objetoADesColorear).addClass('btn-default');	
							}
							else
							{
								objetoADesColorear = '#lbl-'+objetoADesColorear;
								$(objetoADesColorear).parent().parent().removeClass('has-error has-feedback');
							}
							
							$('#comentario').val('');
							$('#idproyectocomentarios').val('');
							$('#modalComentario').modal('hide');							
                        },
                        _error: function(jqXHR){ 
                        	MessageManager.show(jqXHR.responseJSON);
                        }
        			});
				}
		});
	}
});

$('#btnRegresarCorregir').on('click',function(){
	if(proyectoEstatusId > 3){
		MessageManager.show({data:'El estatus del proyecto no permite esta acción',type:'ADV',timer:3});
	} else if(comentariosArray.length>0){
		Confirm.show({
				titulo:"Regresar el proyecto para correcciones",
				mensaje: "¿Estás seguro que desea devolver el proyecto para que éste sea corregido?",
				callback: function(){
					var parametros = 'actualizarproyecto=regresar';					
					proyectoResource.put($('#id').val(),parametros,{
						_success: function(response){
							window.location = "../revision/revision-proyectos";
							MessageManager.show({data:'El proyecto ha sido devuelto para correcciones',type:'OK',timer:3});					
						},
						_error: function(response){
							try{
								var json = $.parseJSON(response.responseText);
								if(!json.code)
									MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
								else{
									json.container = modal_actividad + ' .modal-body';
									MessageManager.show(json);
								}
								Validation.formValidate(json.data);
							}catch(e){
								console.log(e);
							}
						}
					});
				}
		});
	}
	else
	{
		MessageManager.show({data:'Debe escribir al menos un comentario en algún elemento del proyecto, para devolver a corrección',type:'ADV',timer:3});
	}
});


$('#btnAprobarProyecto').on('click',function(){
	if(proyectoEstatusId > 3){
		MessageManager.show({data:'El estatus del proyecto no permite esta acción',type:'ADV',timer:3});
	} else if(comentariosArray.length>0){
		MessageManager.show({data:'Existen comentarios sobre el proyecto, si desea autorizarlos, por favor, elimine los comentarios',type:'ADV',timer:3});		
	}
	else
	{
		Confirm.show({
				titulo:"Aprobar el proyecto",
				//botones:[], 
				mensaje: "¿Estás seguro que deseas aprobar toda la información del proyecto?",
				//si: 'Actualizar',
				//no: 'No, gracias',
				callback: function(){
					var parametros = 'actualizarproyecto=aprobar';					
					proyectoResource.put($('#id').val(),parametros,{
						_success: function(response){
							window.location = "../revision/revision-proyectos";
							MessageManager.show({data:'El proyecto ha sido validado en la información con que cuenta',type:'OK',timer:3});					
						},
						_error: function(response){
							try{
								var json = $.parseJSON(response.responseText);
								if(!json.code)
									MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
								else{
									json.container = modal_actividad + ' .modal-body';
									MessageManager.show(json);
								}
								Validation.formValidate(json.data);
							}catch(e){
								console.log(e);
							}
						}
					});
				}
		});
	}
});


/*REVISAR DESDE ACÁ HACIA ABAJO CUÁLES FUNCIONES REALMENTE SE UTILIZAN, LAS QUE NO, QUE SE BORREN*/

function inicializar_comportamiento_caratula(){
	/*$('#entregable').on('change',function(){
		habilita_opciones('#tipo-entregable',$(this).val(),'NA');
		habilita_opciones('#accion-entregable',$(this).val());
	});
	$('.control-espejo').each(function(){
		var control_id = $(this).data('espejo-id');
		$(control_id).on('change',function(){
			$('.control-espejo[data-espejo-id="'+control_id+'"]').text($(this).val());
		});
	});
	$('#denominador-componente').on('keyup',function(){
		ejecutar_formula('componente');
	});
	$('#denominador-actividad').on('keyup',function(){
		ejecutar_formula('actividad');
	});
	$('.benef-totales').on('keyup',function(){
		if($(this).attr('id') == 'totalbeneficiariosf'){
			var totalm = parseInt($('#totalbeneficiariosm').val());
			$('#totalbeneficiarios').text(totalm + parseInt($(this).val()));
		}
		if($(this).attr('id') == 'totalbeneficiariosm'){
			var totalf = parseInt($('#totalbeneficiariosf').val());
			$('#totalbeneficiarios').text(totalf + parseInt($(this).val()));
		}
	});
	$('.sub-total-zona').on('change',function(){
		if($(this).hasClass('fem')){
			sumar_totales('.sub-total-zona.fem','total-zona-f','totalbeneficiariosf','Los subtotales de Zona no concuerdan.');
		}
		if($(this).hasClass('masc')){
			sumar_totales('.sub-total-zona.masc','total-zona-m','totalbeneficiariosm','Los subtotales de Zona no concuerdan.');
		}
	});
	$('.sub-total-poblacion').on('change',function(){
		if($(this).hasClass('fem')){
			sumar_totales('.sub-total-poblacion.fem','total-poblacion-f','totalbeneficiariosf','Los subtotales de Población no concuerdan.');
		}
		if($(this).hasClass('masc')){
			sumar_totales('.sub-total-poblacion.masc','total-poblacion-m','totalbeneficiariosm','Los subtotales de Población no concuerdan.');
		}
	});
	$('.sub-total-marginacion').on('change',function(){
		if($(this).hasClass('fem')){
			sumar_totales('.sub-total-marginacion.fem','total-marginacion-f','totalbeneficiariosf','Los subtotales de Marginación no concuerdan.');
		}
		if($(this).hasClass('masc')){
			sumar_totales('.sub-total-marginacion.masc','total-marginacion-m','totalbeneficiariosm','Los subtotales de Marginación no concuerdan.');
		}
	});*/
}

/***********************************           Comportamiento de los datagrids          ***********************************/
function editar_componente(e){
	/*var parametros = {'ver':'componente'};
	proyectoResource.get(e,parametros,{
        _success: function(response){
            var titulo_modal = 'Editar Componente';
            $(modal_componente).find(".modal-title").html(titulo_modal);

 			$('#descripcion-obj-componente').val(response.data.objetivo);
 			$('#verificacion-componente').val(response.data.mediosVerificacion);
 			$('#supuestos-componente').val(response.data.supuestos);
 			$('#descripcion-ind-componente').val(response.data.indicador);
 			$('#numerador-ind-componente').val(response.data.numerador);
 			$('#denominador-ind-componente').val(response.data.denominador);
 			$('#interpretacion-componente').val(response.data.interpretacion);
 			$('#denominador-componente').val(response.data.valorDenominador).change();
 			$('#linea-base-componente').val(response.data.lineaBase);
 			$('#anio-base-componente').val(response.data.anioBase);
 			$('#formula-componente').val(response.data.idFormula);
			$('#dimension-componente').val(response.data.idDimensionIndicador);
			$('#frecuencia-componente').val(response.data.idFrecuenciaIndicador);
			$('#tipo-ind-componente').val(response.data.idTipoIndicador);
			$('#unidad-medida-componente').val(response.data.idUnidadMedida);

			$('#formula-componente').trigger('chosen:updated');
			$('#dimension-componente').trigger('chosen:updated');
			$('#frecuencia-componente').trigger('chosen:updated');
			$('#tipo-ind-componente').trigger('chosen:updated');
			$('#unidad-medida-componente').trigger('chosen:updated');

			$('#entregable').val(response.data.idEntregable);
			$('#entregable').chosen().change();
 			$('#tipo-entregable').val(response.data.idEntregableTipo || 'NA');
 			$('#accion-entregable').val(response.data.idEntregableAccion);

			$('#entregable').trigger('chosen:updated');
			$('#tipo-entregable').trigger('chosen:updated');
 			$('#accion-entregable').trigger('chosen:updated');

            $('#id-componente').val(response.data.id);
    		$('#tablink-componente-actividades').attr('data-toggle','tab');
			$('#tablink-componente-actividades').parent().removeClass('disabled');

			$(form_componente + ' .metas-mes').attr('data-meta-id','');
			
			actualizar_metas('componente',response.data.metas_mes);
			
			var tab_id = cargar_formulario_componente_actividad('componente',response.data);

			$(tab_id).tab('show');

			actualizar_grid_actividades(response.data.actividades);

			if(response.data.idEntregable){
				busqueda_rapida_desglose(e,{});
			}else{
				actualizar_grid_desglose([]);
				$(modal_componente).modal('show');
			}
        }
    },'Cargando los datos del componente');*/
}

///*****  Comportamiento_del_grid_de_Desglose
/*
$(grid_desglose + " .btn-next-rows").off('click');
$(grid_desglose + " .btn-next-rows").on('click', function(e) {           
	e.preventDefault();	
	var pagina  = parseInt(desgloseComponenteDatagrid.getPagina())+1;

    if(desgloseComponenteDatagrid.getMaxPagina()>=pagina){
    	buscar_pagina_desglose(pagina);
    }
});
$(grid_desglose + " .btn-back-rows").off('click');
$(grid_desglose + " .btn-back-rows").on('click',function(e){
	e.preventDefault();	
	var pagina  = parseInt(desgloseComponenteDatagrid.getPagina())-1;
    if(pagina<1)
      pagina = 1;
  	
	buscar_pagina_desglose(pagina);
});
$(grid_desglose + " .btn-go-first-rows").off('click');
$(grid_desglose + " .btn-go-first-rows").on('click',function(e){           
	e.preventDefault();
	buscar_pagina_desglose(1);
});
$(grid_desglose + " .btn-go-last-rows").off('click');
$(grid_desglose + " .btn-go-last-rows").on('click',function(e){
	e.preventDefault();
	buscar_pagina_desglose(desgloseComponenteDatagrid.getMaxPagina() || 1);
});
$(grid_desglose + " .txt-go-page").off('keydown');
$(grid_desglose + " .txt-go-page").on('keydown', function(event){
	if (event.which == 13) {
		buscar_pagina_desglose($(this.selector + " .txt-go-page").val());
   	}
});
$(grid_desglose + " .txt-quick-search").off('keydown');
$(grid_desglose + " .txt-quick-search").on('keydown', function(event){
	if (event.which == 13) {
		busqueda_rapida_desglose($('#id-componente').val(),{buscar:$(this).val()});
   	}
});
$(grid_desglose + " .btn-quick-search").off('click');
$(grid_desglose + " .btn-quick-search").on('click',function(e){
	e.preventDefault();		
	busqueda_rapida_desglose($('#id-componente').val(),{buscar:$(grid_desglose + " .txt-quick-search").val()});
});
function buscar_pagina_desglose(pagina){
	var max_pagina = desgloseComponenteDatagrid.getMaxPagina() || 1;
	if(pagina > max_pagina){
		pagina = max_pagina;
	}else if(pagina <= 0){
		pagina = 1;
	}
	busqueda_rapida_desglose($('#id-componente').val(),{pagina:pagina})
}
function busqueda_rapida_desglose(id_componente,parametros){
	var param_buscar = {ver:'lista-desglose',pagina:1};
	if(parametros.buscar){
		param_buscar.buscar = parametros.buscar;
	}
	if(parametros.pagina){
		param_buscar.pagina = parametros.pagina;
	}
	proyectoResource.get(id_componente,param_buscar,{
		_success: function(response){
			actualizar_grid_desglose(response.data, response.total, param_buscar.pagina);
			$(modal_componente).modal('show');
		}
	},'Cargando el desglose del componente...');
}*/
///***** Termina Comportamiento_del_grid_de_Desglose

function cargar_formulario_componente_actividad(identificador,datos){
	/*var errores_metas = false;
	if($('#trim1-'+identificador).val() != datos.numeroTrim1 && datos.numeroTrim1 != null){
		Validation.printFieldsErrors('trim1-'+identificador,'Valor anterior de '+datos.numeroTrim1+'.');
		errores_metas = true;
	}
	if($('#trim2-'+identificador).val() != datos.numeroTrim2 && datos.numeroTrim2 != null){
		Validation.printFieldsErrors('trim2-'+identificador,'Valor anterior de '+datos.numeroTrim2+'.');
		errores_metas = true;
	}
	if($('#trim3-'+identificador).val() != datos.numeroTrim3 && datos.numeroTrim3 != null){
		Validation.printFieldsErrors('trim3-'+identificador,'Valor anterior de '+datos.numeroTrim3+'.');
		errores_metas = true;
	}
	if($('#trim4-'+identificador).val() != datos.numeroTrim4 && datos.numeroTrim4 != null){
		Validation.printFieldsErrors('trim4-'+identificador,'Valor anterior de '+datos.numeroTrim4+'.');
		errores_metas = true;
	}
	if($('#numerador-'+identificador).val() != datos.valorNumerador && datos.valorNumerador != null){
		Validation.printFieldsErrors('numerador-'+identificador,'Valor anterior de '+datos.valorNumerador+'.');
		errores_metas = true;
	}
	if($('#meta-'+identificador).val() != datos.metaIndicador && datos.metaIndicador != null){
		Validation.printFieldsErrors('meta-'+identificador,'Valor anterior de '+datos.metaIndicador+'.');
		errores_metas = true;
	}

	if(errores_metas){
		if(identificador == 'actividad'){
			var modal_identificador = modal_actividad;
		}else{
			var modal_identificador = modal_componente;
		}
		MessageManager.show({data:'Se ha detectado una irregularidad en los totales de los trimestres, esto puede deberse a que las jurisdicciones pertenecientes a la cobertura del proyecto cambiaron, por favor corrobore que la información sea correcta y de ser necesario actualize los valores requeridos para poder resolver el conflicto.',container:modal_identificador + ' .modal-body',type:'ADV'});
		return '#tablink-'+identificador+'-desgloce-metas';
	}else{
		return '#tablink-'+identificador+'-actividades';
	}*/
}

function editar_actividad(e){
	/*var parametros = {'ver':'actividad'};
	proyectoResource.get(e,parametros,{
        _success: function(response){
            var titulo_modal = 'Editar Actividad';
            $(modal_actividad).find(".modal-title").html(titulo_modal);

            $('#id-actividad').val(response.data.id);
 			$('#descripcion-obj-actividad').val(response.data.objetivo);
 			$('#verificacion-actividad').val(response.data.mediosVerificacion);
 			$('#supuestos-actividad').val(response.data.supuestos);
 			$('#descripcion-ind-actividad').val(response.data.indicador);
 			$('#numerador-ind-actividad').val(response.data.numerador);
 			$('#denominador-ind-actividad').val(response.data.denominador);
 			$('#interpretacion-actividad').val(response.data.interpretacion);
 			$('#denominador-actividad').val(response.data.valorDenominador).change();
 			$('#linea-base-actividad').val(response.data.lineaBase);
 			$('#anio-base-actividad').val(response.data.anioBase);

 			$('#meta-actividad').val(response.data.metaIndicador).change();
 			$('#trim1-actividad').val(response.data.numeroTrim1).change();
 			$('#trim2-actividad').val(response.data.numeroTrim2).change();
 			$('#trim3-actividad').val(response.data.numeroTrim3).change();
 			$('#trim4-actividad').val(response.data.numeroTrim4).change();
 			$('#numerador-actividad').val(response.data.valorNumerador).change();

 			$('#formula-actividad').val(response.data.idFormula);
			$('#dimension-actividad').val(response.data.idDimensionIndicador);
			$('#frecuencia-actividad').val(response.data.idFrecuenciaIndicador);
			$('#tipo-ind-actividad').val(response.data.idTipoIndicador);
			$('#unidad-medida-actividad').val(response.data.idUnidadMedida);

			$('#formula-actividad').trigger('chosen:updated');
			$('#dimension-actividad').trigger('chosen:updated');
			$('#frecuencia-actividad').trigger('chosen:updated');
			$('#tipo-ind-actividad').trigger('chosen:updated');
			$('#unidad-medida-actividad').trigger('chosen:updated');

			$(form_actividad + ' .metas-mes').attr('data-meta-id','');

			actualizar_metas('actividad',response.data.metas_mes);

			var tab_id = cargar_formulario_componente_actividad('actividad',response.data);

			$(tab_id).tab('show');

            $(modal_actividad).modal('show');
        }
    });*/
}

//***********************     Funcionalidad de botones y elementos del formulario ++++++++++++++++++++++++++++++++++++
/*
$('#btn-componente-guardar-salir').on('click',function(){
	guardar_datos_componente(true);
});

$('#btn-componente-guardar').on('click',function(){
	guardar_datos_componente(false);
});

$('#btn-actividad-guardar').on('click',function(){
	Validation.cleanFormErrors(form_actividad);
	var parametros = $(form_actividad).serialize();
	parametros = parametros + '&guardar=actividad&id-componente=' + $('#id-componente').val();

	if($('#id-actividad').val()){
		var cadena_metas = '';
		$(form_actividad + ' .metas-mes').each(function(){
			if($(this).data('meta-id')){
				cadena_metas = cadena_metas + '&mes-actividad-id['+$(this).data('meta-jurisdiccion')+']['+$(this).data('meta-mes')+']='+$(this).data('meta-id');
			}
		});
		parametros = parametros + cadena_metas;
		proyectoResource.put($('#id-actividad').val(),parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Datos de la actividad almacenados con éxito',type:'OK',timer:3});
	            $(modal_actividad).modal('hide');
				actualizar_grid_actividades(response.actividades);
				//actualizar_metas('actividad',response.metas);
	        },
	        _error: function(response){
	            try{
	                var json = $.parseJSON(response.responseText);
	                if(!json.code)
	                    MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
	                else{
	                	json.container = modal_actividad + ' .modal-body';
	                    MessageManager.show(json);
	                }
	                Validation.formValidate(json.data);
	            }catch(e){
	                console.log(e);
	            }                       
	        }
	    });
	}else{
		proyectoResource.post(parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Datos de la actividad almacenados con éxito',type:'OK',timer:3});
	            $(form_actividad + ' #id-actividad').val(response.data.id);
	            $(modal_actividad).modal('hide');
				actualizar_grid_actividades(response.actividades);
				//actualizar_metas('actividad',response.metas);
	        },
	        _error: function(response){
	            try{
	                var json = $.parseJSON(response.responseText);
	                if(!json.code)
	                    MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
	                else{
	                	json.container = modal_actividad + ' .modal-body';
	                    MessageManager.show(json);
	                }
	                Validation.formValidate(json.data);
	            }catch(e){
	                console.log(e);
	            }                       
	        }
	    });
	}
});*/

$('#btn-proyecto-guardar').on('click',function(){

	/*Validation.cleanFormErrors(form_caratula);
	if(checar_error_totales()){
		return false;
	}

	var parametros = $(form_caratula).serialize();
	parametros = parametros + '&guardar=proyecto';
	
	if($('#id').val()){
		proyectoResource.put($('#id').val(),parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Datos del proyecto almacenados con éxito',type:'OK',timer:3});
	            if(response.data.jurisdicciones){
	            	actualizar_tabla_metas('actividad',response.data.jurisdicciones);
            		actualizar_tabla_metas('componente',response.data.jurisdicciones);
	            }
	        },
	        _error: function(response){
	            try{
	                var json = $.parseJSON(response.responseText);
	                if(!json.code)
	                    MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
	                else{
	                    MessageManager.show(json);
	                }
	                Validation.formValidate(json.data);
	            }catch(e){
	                console.log(e);
	            }                       
	        }
	    });
	}else{
		proyectoResource.post(parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Datos del proyecto almacenados con éxito',type:'OK',timer:3});
	            $(form_caratula + ' #id').val(response.data.id);
	            $(form_caratula + ' #no_proyecto_estrategico').text(("000" + response.data.numeroProyectoEstrategico).slice(-3));
	            $(form_caratula + ' #numeroproyectoestrategico').text(("000" + response.data.numeroProyectoEstrategico).slice(-3));

	            if(response.data.componentes){
	            	actualizar_grid_componentes(response.data.componentes);
	            }

	            actualizar_tabla_metas('actividad',response.data.jurisdicciones);
            	actualizar_tabla_metas('componente',response.data.jurisdicciones);

	            $('#tablink-componentes').attr('data-toggle','tab');
				$('#tablink-componentes').parent().removeClass('disabled');
	        },
	        _error: function(response){
	            try{
	                var json = $.parseJSON(response.responseText);
	                if(!json.code)
	                    MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
	                else{
	                    MessageManager.show(json);
	                }
	                Validation.formValidate(json.data);
	            }catch(e){
	                console.log(e);
	            }                       
	        }
	    });
	}*/
});

$('#btn-proyecto-cancelar').on('click',function(){
	window.location.href = "revision-proyectos";
});

$('#cobertura').on('change',function(){
	deshabilita_paneles($(this).val());
});

$('#formula-componente').on('change',function(){
	ejecutar_formula('componente');
});

$('#formula-actividad').on('change',function(){
	ejecutar_formula('actividad');
});

/** Botones para mostrar los modales de Componente y Actividad **/
$('.btn-agregar-actividad').on('click',function(){
	var actividades = $('#conteo-actividades').text().split('/');

	if(parseInt(actividades[0]) >= parseInt(actividades[1])){
		MessageManager.show({code:'S03',data:"Las actividades para este componente ya estan completas.",timer:2});
	}else{
		$(modal_actividad).find(".modal-title").html("Nueva Actividad");
		$(modal_actividad).modal('show');
	}
});

$('.btn-agregar-componente').on('click',function(){
	var componentes = $('#tablink-componentes > span').text().split('/');

	if(parseInt(componentes[0]) >= parseInt(componentes[1])){
		MessageManager.show({code:'S03',data:"Los componentes para este proyecto ya estan completos.",timer:2});
	}else{
		$('#tablink-componente-actividades').attr('data-toggle','');
		$('#tablink-componente-actividades').parent().addClass('disabled');
		$('#lista-tabs-componente a:first').tab('show');
		$(modal_componente).find(".modal-title").html("Nuevo Componente");
		$(modal_componente).modal('show');
	}
});

/** Selects para actualizar la Clave Presupuestaria **/
$('#unidadresponsable').on('change',function(){
	actualiza_clave('unidad_responsable',$(this).val(),'--');
});
$('#funciongasto').on('change',function(){
	var funcion_gasto = ['','','',''];

	if($(this).val() != ''){
		 funcion_gasto = $(this).val().split('.');
	}
	
	actualiza_clave('finalidad', funcion_gasto[0],'-');
	actualiza_clave('funcion', funcion_gasto[1],'-');
	actualiza_clave('subfuncion', funcion_gasto[2],'-');
	actualiza_clave('subsubfuncion', funcion_gasto[3],'-');

});
$('#programasectorial').on('change',function(){
	actualiza_clave('programa_sectorial',$(this).val(),'-');
});
$('#programapresupuestario').on('change',function(){
	actualiza_clave('programa_presupuestario',$(this).val(),'---');
});
$('#origenasignacion').on('change',function(){
	actualiza_clave('origen_asignacion',$(this).val(),'--');
});
$('#actividadinstitucional').on('change',function(){
	actualiza_clave('actividad_institucional',$(this).val(),'---');
});
$('#proyectoestrategico').on('change',function(){
	actualiza_clave('proyecto_estrategico',$(this).val(),'-');
});

/*******************************      Funcionalidad de los lementos de la pag     ********************************************/
$(modal_componente + ' #lista-tabs-componente').on('show.bs.tab',function(event){
	var id = event.target.id;
	if(id == 'tablink-componente-actividades'){
		$('.btn-grupo-guardar').hide();
	}else{
		$('.btn-grupo-guardar').show();
	}
});

$(modal_componente).on('hide.bs.modal',function(e){
	reset_modal_form(form_componente);
});

$(modal_actividad).on('hide.bs.modal',function(e){
	reset_modal_form(form_actividad);
});

//***********************     Funciones             +++++++++++++++++++++++++++++++++
function guardar_datos_componente(cerrar){
/*	
	Validation.cleanFormErrors(form_componente);
	var parametros = $(form_componente).serialize();
	parametros = parametros + '&guardar=componente';
	parametros = parametros + '&id-proyecto='+$('#id').val();
	parametros = parametros + '&clasificacion='+$('#clasificacionproyecto').val();

	if($('#id-componente').val()){
		var cadena_metas = '';
		$(form_componente + ' .metas-mes').each(function(){
			if($(this).data('meta-id')){
				cadena_metas = cadena_metas + '&mes-componente-id['+$(this).data('meta-jurisdiccion')+']['+$(this).data('meta-mes')+']='+$(this).data('meta-id');
			}
		});
		parametros = parametros + cadena_metas;
		proyectoResource.put($('#id-componente').val(),parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Datos del componente almacenados con éxito',type:'OK',timer:3});
	            if(cerrar){
					$(modal_componente).modal('hide');
				}else{
					$('#tablink-componente-actividades').tab('show');
				}
				actualizar_grid_componentes(response.componentes);
				actualizar_metas('componente',response.metas);
	        },
	        _error: function(response){
	            try{
	                var json = $.parseJSON(response.responseText);
	                if(!json.code)
	                    MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
	                else{
	                    MessageManager.show(json);
	                }
	                Validation.formValidate(json.data);
	            }catch(e){
	                console.log(e);
	            }                       
	        }
	    });
	}else{
		proyectoResource.post(parametros,{
	        _success: function(response){
	            MessageManager.show({data:'Datos del componente almacenados con éxito',type:'OK',timer:3});
	            $(form_componente + ' #id-componente').val(response.data.id);
	            
	            if(cerrar){
					$(modal_componente).modal('hide');
				}else{
					$('#tablink-componente-actividades').attr('data-toggle','tab');
					$('#tablink-componente-actividades').parent().removeClass('disabled');
					$('#tablink-componente-actividades').tab('show');
				}
				actualizar_grid_componentes(response.componentes);
				actualizar_metas('componente',response.metas);
	        },
	        _error: function(response){
	            try{
	                var json = $.parseJSON(response.responseText);
	                if(!json.code)
	                    MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
	                else{
	                    MessageManager.show(json);
	                }
	                Validation.formValidate(json.data);
	            }catch(e){
	                console.log(e);
	            }                       
	        }
	    });
	}*/
}

/**                            Reescribiendo comportamiento del datagrid                                 **/
$(grid_componentes + " .btn-delete-rows").unbind('click');
$(grid_componentes + " .btn-delete-rows").on('click',function(e){
	e.preventDefault();
	var rows = [];
	var contador= 0;
    
    $(this).parents(".datagrid").find("tbody").find("input[type=checkbox]:checked").each(function () {
		contador++;
        rows.push($(this).parent().parent().data("id"));
	});

	if(contador>0){
		Confirm.show({
				titulo:"Eliminar componente",
				//botones:[], 
				mensaje: "¿Estás seguro que deseas eliminar los componentes seleccionados?",
				//si: 'Actualizar',
				//no: 'No, gracias',
				callback: function(){
					proyectoResource.delete(rows,{'rows': rows, 'eliminar': 'componente', 'id-proyecto': $('#id').val()},{
                        _success: function(response){ 
                        	actualizar_grid_componentes(response.componentes);
                        	MessageManager.show({data:'Componente eliminado con éxito.',timer:3});
                        },
                        _error: function(jqXHR){ 
                        	MessageManager.show(jqXHR.responseJSON);
                        }
        			});
				}
		});
	}else{
		MessageManager.show({data:'No has seleccionado ningún registro.',type:'ADV',timer:3});
	}
});

$(grid_actividades + " .btn-delete-rows").unbind('click');
$(grid_actividades + " .btn-delete-rows").on('click',function(e){
	e.preventDefault();
	var rows = [];
	var contador= 0;
    
    $(this).parents(".datagrid").find("tbody").find("input[type=checkbox]:checked").each(function () {
		contador++;
        rows.push($(this).parent().parent().data("id"));
	});

	if(contador>0){
		Confirm.show({
				titulo:"Eliminar actividad",
				//botones:[], 
				mensaje: "¿Estás seguro que deseas eliminar las actividades seleccionadas?",
				//si: 'Actualizar',
				//no: 'No, gracias',
				callback: function(){
					proyectoResource.delete(rows,{'rows': rows, 'eliminar': 'actividad', 'id-componente': $('#id-componente').val()},{
                        _success: function(response){ 
                        	actualizar_grid_actividades(response.actividades);
                        	MessageManager.show({data:'Actividad eliminada con éxito.',timer:3});
                        },
                        _error: function(jqXHR){ 
                        	MessageManager.show(jqXHR.responseJSON);
                        }
        			});
				}
		});
	}else{
		MessageManager.show({data:'No has seleccionado ningún registro.',type:'ADV',timer:3});
	}
});

/** Actualiza el span correspondiente al select actualizado, para ir construyendo la Clave Presupuestaria **/
function actualiza_clave(id, clave, value){
	if(clave != ''){
		$('#'+id).text(clave);
	}else{
		$('#'+id).text(value);
	}	
}

function deshabilita_paneles(id){
	if(id == 1){
		$('#select-estado-panel').show();
		$('#select-municipio-panel').hide();
		$('#select-region-panel').hide();
	}else if(id == 2){
		$('#select-estado-panel').hide();
		$('#select-municipio-panel').show();
		$('#select-region-panel').hide();
	}else if(id == 3){
		$('#select-estado-panel').hide();
		$('#select-municipio-panel').hide();
		$('#select-region-panel').show();
	}else{
		$('#select-estado-panel').hide();
		$('#select-municipio-panel').hide();
		$('#select-region-panel').hide();
	}
}

function actualizar_tabla_metas(identificador,jurisdicciones){
	var tabla_id = '#tabla-'+identificador+'-metas-mes';
	var meses = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'];

	var html = '';
	var indx,idx;
	var llaves = Object.keys(jurisdicciones).sort(); //Se ordenan las llaves

	for(var index in llaves){
		indx = llaves[index];
		html += '<tr>';
		html += '<th>'+jurisdicciones[indx]+'</th>';
		for(idx in meses){
			id_mes = parseInt(idx) + 1;
			html += '<td><input id="mes-'+identificador+'-'+indx+'-'+id_mes+'" name="mes-'+identificador+'['+indx+']['+id_mes+']" type="number" class="form-control input-sm metas-mes" data-meta-mes="'+id_mes+'" data-meta-jurisdiccion="'+indx+'" data-meta-identificador="'+identificador+'" data-meta-id=""></td>';
		}
		html += '</tr>';
	}

	$(tabla_id + ' tbody').empty();
	$(tabla_id + ' tbody').html(html);
	actualizar_eventos_metas();
}

function actualizar_metas(identificador,metas){
	var indx;
	for(indx in metas){
		$('#mes-'+identificador+'-'+metas[indx].claveJurisdiccion+'-'+metas[indx].mes).val(metas[indx].meta);
		$('#mes-'+identificador+'-'+metas[indx].claveJurisdiccion+'-'+metas[indx].mes).attr('data-meta-id',metas[indx].id);
	}
	$('.metas-mes[data-meta-jurisdiccion="OC"][data-meta-identificador="'+identificador+'"]').change();
}

function actualizar_grid_desglose(datos,total_resultados,pagina){
	$(grid_desglose + ' > table > tbody').empty();
	var desglose_componente = [];
	for(indx in datos){
		var desglose = {};

		desglose.id = datos[indx].id;
		desglose.localidad = datos[indx].localidad || 'OFICINA CENTRAL';
		desglose.municipio = datos[indx].municipio || 'OFICINA CENTRAL';
		desglose.jurisdiccion = datos[indx].jurisdiccion || 'OFICINA CENTRAL';
		desglose.meta = datos[indx].meta;

		desglose_componente.push(desglose);
	}

	$('#conteo-desglose').text(desglose_componente.length);

	if(desglose_componente.length == 0){
		$(grid_desglose + ' > table > tbody').html('<tr><td colspan="5" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
		desgloseComponenteDatagrid.paginacion(1);
	}else{
		desgloseComponenteDatagrid.cargarDatos(desglose_componente);
		var total = parseInt(total_resultados/10); 
        if((parseInt(total_resultados)%10) > 0) 
        	total++;
        desgloseComponenteDatagrid.paginacion(total);
        desgloseComponenteDatagrid.setPagina(pagina);
	}
}

function actualizar_grid_actividades(datos){
	$(grid_actividades + ' > table > tbody').empty();
	var actividades = [];
	for(indx in datos){
		var actividad = {};

		actividad.id = datos[indx].id;
		actividad.indicador = datos[indx].indicador;
		actividad.interpretacion = datos[indx].interpretacion;
		actividad.unidad_medida = datos[indx].unidad_medida.descripcion;
		actividad.creadoPor = datos[indx].usuario.username;
		actividad.creadoAl = datos[indx].creadoAl.substring(0,11);

		actividades.push(actividad);
	}

	$('#conteo-actividades').text(' ' + actividades.length + ' / 5 ');

	if(actividades.length == 0){
		$(grid_actividades + ' > table > tbody').html('<tr><td colspan="6" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
	}else{
		actividadDatagrid.cargarDatos(actividades);
	}
}

function actualizar_grid_componentes(datos){
	$(grid_componentes + ' > table > tbody').empty();
	var componentes = [];
	for(indx in datos){
		var componente = {};

		componente.id = datos[indx].id;
		componente.indicador = datos[indx].indicador;
		componente.interpretacion = datos[indx].interpretacion || '---';
		componente.unidad_medida = datos[indx].unidad_medida.descripcion;
		componente.creadoPor = datos[indx].usuario.username;
		componente.creadoAl = datos[indx].creadoAl.substring(0,11);

		componentes.push(componente);
	}

	$('#tablink-componentes > span').text(componentes.length + ' / 2');

	if(componentes.length == 0){
		$(grid_componentes + ' > table > tbody').append('<tr><td colspan="6" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
	}else{
		componenteDatagrid.cargarDatos(componentes);
	}
}

function reset_modal_form(formulario){
    $(formulario).get(0).reset();
    $(formulario + ' input[type="hidden"]').val('');
    $(formulario + ' input[type="hidden"]').change();
    
    $(formulario + ' .chosen-one').trigger('chosen:updated');
    Validation.cleanFormErrors(formulario);
    if(formulario == form_componente){
    	$(modal_componente + ' .alert').remove();
    	$('#id-componente').val('');
    	//$(grid_actividades + ' > table > tbody').empty();
    	$('#conteo-actividades').text(' 0 / 5 ');
    	$(grid_actividades + ' > table > tbody').html('<tr><td colspan="6" style="text-align:left"><i class="fa fa-info-circle"></i> No hay datos</td></tr>');
    }
    if(formulario == form_actividad){
    	$('#id-actividad').val('');
    	$('#lista-tabs-actividad a:first').tab('show');
    	$(modal_actividad + ' .alert').remove();
    }
}

//
function checar_error_totales(){
	var errores = false;
	if(parseInt($('#totalbeneficiariosf').val()) != parseInt($('#total-zona-f').text())){
		Validation.printFieldsErrors('total-zona-f','Los subtotales de Zona no concuerdan.');
		errores = true;
	}
	if(parseInt($('#totalbeneficiariosm').val()) != parseInt($('#total-zona-m').text())){
		Validation.printFieldsErrors('total-zona-m','Los subtotales de Zona no concuerdan.');
		errores = true;
	}
	if(parseInt($('#totalbeneficiariosf').val()) != parseInt($('#total-poblacion-f').text())){
		Validation.printFieldsErrors('total-poblacion-f','Los subtotales de Población no concuerdan.');
		errores = true;
	}
	if(parseInt($('#totalbeneficiariosm').val()) != parseInt($('#total-poblacion-m').text())){
		Validation.printFieldsErrors('total-poblacion-m','Los subtotales de Población no concuerdan.');
		errores = true;
	}
	if(parseInt($('#totalbeneficiariosf').val()) != parseInt($('#total-marginacion-f').text())){
		Validation.printFieldsErrors('total-marginacion-f','Los subtotales de Marginación no concuerdan.');
		errores = true;
	}
	if(parseInt($('#totalbeneficiariosm').val()) != parseInt($('#total-marginacion-m').text())){
		Validation.printFieldsErrors('total-marginacion-m','Los subtotales de Marginación no concuerdan.');
		errores = true;
	}
	return errores;
}

//
function sumar_totales(tipo,campo_suma,campo_total,mensaje){
	var sub_total = 0;
	$(tipo).each(function(){
		sub_total += parseInt($(this).val()) || 0;
	});
	$('#'+campo_suma).text(sub_total);
	if(parseInt($('#'+campo_total).val()) != sub_total){
		Validation.printFieldsErrors(campo_suma,mensaje);
	}else{
		Validation.cleanFieldErrors(campo_suma);
	}
}

function ejecutar_formula(identificador){	
	var numerador = parseInt($('#numerador-'+identificador).val()) || 0;
	var denominador = parseInt($('#denominador-'+identificador).val()) || 1;
	var total;
	var id_formula = $('#formula-'+identificador).val();
	switch(id_formula){
		case '1':
			//(Numerador / Denominador) * 100
			total = (numerador/denominador)*100;
			break;
		case '2':
			//((Numerador / Denominador) - 1) * 100
			total = ((numerador/denominador)-1)*100;
			break;
		case '3':
			//(Numerador / Denominador)
			total = (numerador/denominador);
			break;
		case '4':
			//(Numerador - 1,000) / Denominador
			total = (numerador*1000)/denominador;
			break;
		case '5':
			//(Numerador / 10,000) / Denominador
			total = (numerador*10000)/denominador;
			break;
		case '6':
			//(Numerador / 100,000) / Denominador
			total = (numerador*100000)/denominador;
			break;
		case '7':
			//Indicador simple
			total = numerador;
			break;
		default:
			total = '';
			break;
	}
	$('#meta-'+identificador).val(total).change();
}

function actualizar_eventos_metas(){
	$('.metas-mes').on('change',function(){
		var mes = $(this).data('meta-mes');
		var trimestre = Math.ceil(mes/3);
		var identificador = $(this).data('meta-identificador');
		
		var suma = 0;
		var mes_inicio = 0;
		var mes_fin = 0;

		if(trimestre == 1){
			mes_inicio = 1;
			mes_fin = 3;
		}else if(trimestre == 2){
			mes_inicio = 4;
			mes_fin = 6;
		}else if(trimestre == 3){
			mes_inicio = 7;
			mes_fin = 9;
		}else if(trimestre == 4){
			mes_inicio = 10;
			mes_fin = 12;
		}

		for(var i = mes_inicio; i <= mes_fin; i++) {
			$('.metas-mes[data-meta-mes="' + i + '"][data-meta-identificador="' + identificador + '"]').each(function(){
				suma += parseInt($(this).val()) || 0;
			});
		}
		
		$('#trim'+trimestre+'-'+identificador).val(suma).change();

		var trim1 = parseInt($('#trim1-'+identificador).val()) || 0;
		var trim2 = parseInt($('#trim2-'+identificador).val()) || 0;
		var trim3 = parseInt($('#trim3-'+identificador).val()) || 0;
		var trim4 = parseInt($('#trim4-'+identificador).val()) || 0;

		suma = trim1 + trim2 + trim3 + trim4;

		$('#numerador-'+identificador).val(suma).change();
		ejecutar_formula(identificador);
	});
}

function cargar_totales(){
	sumar_totales('.sub-total-zona.fem','total-zona-f','totalbeneficiariosf','Los subtotales de Zona no concuerdan.');
	sumar_totales('.sub-total-zona.masc','total-zona-m','totalbeneficiariosm','Los subtotales de Zona no concuerdan.');
	sumar_totales('.sub-total-poblacion.fem','total-poblacion-f','totalbeneficiariosf','Los subtotales de Población no concuerdan.');
	sumar_totales('.sub-total-poblacion.masc','total-poblacion-m','totalbeneficiariosm','Los subtotales de Población no concuerdan.');
	sumar_totales('.sub-total-marginacion.fem','total-marginacion-f','totalbeneficiariosf','Los subtotales de Marginación no concuerdan.');
	sumar_totales('.sub-total-marginacion.masc','total-marginacion-m','totalbeneficiariosm','Los subtotales de Marginación no concuerdan.');
}

function bloquear_controles(){
	$('.control-bloqueado').each(function(){
		$(this).prop('disabled',true);
		$('label[for="' + $(this).attr('id') + '"]').prepend('<span class="fa fa-lock"></span> ');
	});
}

function habilita_opciones(selector,habilitar_id,default_id){
	var suma = $(selector + ' option[data-habilita-id="' + habilitar_id + '"]').length;

	$(selector + ' option[data-habilita-id]').attr('disabled',true).addClass('hidden');
	$(selector + ' option[data-habilita-id="' + habilitar_id + '"]').attr('disabled',false).removeClass('hidden');

	if(suma == 0 && default_id){
		$(selector + ' option[data-habilita-id="' + default_id + '"]').attr('disabled',false).removeClass('hidden');
	}

	$(selector).val('');
	$(selector).change();

	if($(selector).hasClass('chosen-one')){
		$(selector).trigger("chosen:updated");
	}
}

/**
 * Number.prototype.format(n, x)
 * 
 * @param integer n: length of decimal
 * @param integer x: length of sections
 */
 Number.prototype.format = function(n, x) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    //return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
    var formateado = this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
    var partes = formateado.split('.');
    if(parseInt(partes[1]) == 0){
        return partes[0];
    }else{
        return formateado;
    }
};