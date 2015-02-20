
/*=====================================

    # Nombre:
        fibap.js

    # Módulo:
        expediente/fibap

    # Descripción:
        Para el formulario de captura de la Ficha de Información Básica del Proyecto de Inversión

=====================================*/
// Declaracion de variables
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/fibap');
var proyectoResource = new RESTfulRequests(SERVER_HOST+'/v1/proyectos');
var moduloDatagrid = new Datagrid("#datagridFibaps",moduloResource);
moduloDatagrid.init();
moduloDatagrid.actualizar();
var modal_name = '#modal-fibap';
var form_name = '#form-fibap';

/*===================================*/
// Configuración General para cualquier módulo
function editar(e){
    $('#opciones_fibap').hide();
    $('#editar_fibap').show();
    moduloResource.get(e,null,{
        _success: function(response){
            console.log(response);
            var fibap = response.data;
            var proyecto;
            if(response.data.datos_proyecto_completo){
                proyecto = response.data.datos_proyecto_completo;
            }else{
                proyecto = response.data.proyecto_completo;
            }

            var titulo_modal = proyecto.clasificacion_proyecto.descripcion + ' <small>' + proyecto.tipo_proyecto.descripcion + '</small>';
            $(modal_name).find(".modal-title").html(titulo_modal);

            $('#lbl-justificacion-proyecto').text(fibap.justificacionProyecto);
            $('#lbl-descripcion-proyecto').text(fibap.descripcionProyecto);
            $('#lbl-alineacion-especifica').html(fibap.alineacionEspecifica);
            $('#lbl-alineacion-general').html(fibap.alineacionGeneral || '&nbsp;');
            $('#lbl-organismo-publico').text(fibap.organismoPublico);
            $('#lbl-sector').text(fibap.sector);
            $('#lbl-subcomite').text(fibap.subcomite);
            $('#lbl-grupo-trabajo').text(fibap.grupoTrabajo);
            $('#lbl-resultados-obtenidos').text(fibap.resultadosObtenidos || '');
            $('#lbl-resultados-esperados').text(fibap.resultadosEsperados || '');
            var periodo_ejecucion = '';
            if(fibap.periodoEjecucionInicio){
                periodo_ejecucion = 'Del ' + fibap.periodoEjecucionInicio + ' al ' + fibap.periodoEjecucionFinal;
            }
            $('#lbl-periodo-ejecucion').text(periodo_ejecucion);
            var presupuesto_requerido = fibap.presupuestoRequerido || 0;
            $('#lbl-presupuesto-requerido').text(presupuesto_requerido.format());

            var html_antecedentes = '';
            for(var i in fibap.antecedentes_financieros){
                var porcentaje = (fibap.antecedentes_financieros[i].ejercido * 100) / fibap.antecedentes_financieros[i].autorizado;
                html_antecedentes += '<tr>';
                html_antecedentes += '<td>' + fibap.antecedentes_financieros[i].anio + '</td>';
                html_antecedentes += '<td>' + fibap.antecedentes_financieros[i].autorizado.format() + '</td>';
                html_antecedentes += '<td>' + fibap.antecedentes_financieros[i].ejercido.format() + '</td>';
                html_antecedentes += '<td>' + parseFloat(porcentaje.toFixed(2)) + '% </td>';
                html_antecedentes += '<td>' + fibap.antecedentes_financieros[i].fechaCorte + '</td>';
                html_antecedentes += '</tr>';
            }
            $('#tabla-antecedentes > tbody').html(html_antecedentes);

            var html_distribucion = '';
            for(var i in fibap.distribucion_presupuesto_agrupado){
                var presupuesto = fibap.distribucion_presupuesto_agrupado[i];
                var porcentaje = (presupuesto.cantidad * 100) / presupuesto_requerido;
                html_distribucion += '<tr>';
                html_distribucion += '<td>' + presupuesto.objeto_gasto.clave + '</td>';
                html_distribucion += '<td>' + presupuesto.objeto_gasto.descripcion + '</td>';
                html_distribucion += '<td>' + presupuesto.cantidad.format() + '</td>';
                html_distribucion += '<td>' + parseFloat(porcentaje.toFixed(2)) + '% </td>';
                html_distribucion += '</tr>';
            }
            $('#tabla-distribucion > tbody').html(html_distribucion);

            $('.valores-origenes').text('0');

            for(var i in fibap.propuestas_financiamiento){
                $('#lbl-origen-'+fibap.propuestas_financiamiento[i].idOrigenFinanciamiento).text(fibap.propuestas_financiamiento[i].cantidad.format());
            }

            if(response.clavePresupuestaria){
                $('#lbl-clave-presupuestaria').text(response.clavePresupuestaria);
                $('#lbl-clave-presupuestaria').parent().show();
            }else{
                $('#lbl-clave-presupuestaria').text('');
                $('#lbl-clave-presupuestaria').parent().hide();
            }

            $('#lbl-proyecto').text(proyecto.nombreTecnico);
            $('#lbl-programa-presupuestario').text(proyecto.datos_programa_presupuestario.descripcion);
            $('#lbl-alineacion-ped').text(proyecto.objetivo_ped.descripcion);
            var cobertura_detalle = '';
            if(proyecto.claveMunicipio){
                cobertura_detalle = proyecto.municipio.nombre;
            }else if(proyecto.claveRegion){
                cobertura_detalle = proyecto.region.nombre;
            }else{
                cobertura_detalle = 'Chiapas';
            }
            $('#lbl-cobertura-municipio').text(proyecto.cobertura.descripcion + ' / ' + cobertura_detalle);
            
            $('#lbl-beneficiario-f').text(proyecto.totalBeneficiariosF.format());
            $('#lbl-beneficiario-m').text(proyecto.totalBeneficiariosM.format());
            /*$('#lbl-tipo-beneficiario').text(proyecto.tipo_beneficiario.descripcion);
            $('#lbl-total-beneficiario').text(proyecto.totalBeneficiarios.format());*/

            var html_list = '';
            for(var indx in fibap.documentos){
                html_list += '<div class="col-sm-4"><span class="fa fa-file-o"></span> '+fibap.documentos[indx].descripcion+'</div>';
            }
            $('#lbl-lista-documentos').html(html_list);

            $('#id').val(fibap.id);
            $(modal_name).modal('show');
        }
    });
};

$(modal_name).on('shown.bs.modal', function () {
    $(modal_name).find('input').eq(0).focus();
});

$(modal_name).on('hide.bs.modal',function(e){ 
    resetModalModuloForm();
});

$('#btn-cargar-proyectos').on('click',function(){
    var parametros = {proyectos_inversion:1};
    proyectoResource.get(null,parametros,{
        _success: function(response){
            $('#lista-proyectos').empty();
            var lista_radios = '';
            for(var i in response.data){
                lista_radios += '<div><label><input type="radio" name="proyecto-id" value="'+response.data[i].id+'" > <span class="fa fa-file"></span> ' + response.data[i].nombreTecnico + ' ['+response.data[i].clavePresup+']</label></div>';
            }
            $('#lista-proyectos').html(lista_radios);
        }
    });
});

$('.btn-datagrid-agregar').on('click', function () {
    $('#opciones_fibap').show();
    $('#editar_fibap').hide();
    $(modal_name).find(".modal-title").html("Nuevo FIBAP");
    $(modal_name).modal('show');
});

$(modal_name+' .btn-guardar').on('click', function (e) {
    e.preventDefault();
    submitModulo();
});

function resetModalModuloForm(){
    //Validation.cleanFormErrors(form_name);
    $(form_name).get(0).reset();
    $(form_name +' #id').val("");
}

function submitModulo(save_next){
    $(form_name).attr('action',SERVER_HOST+'/expediente/formulario-fibap');
    $(form_name).attr('method','POST');
    $(form_name).submit();
}

/*             Extras               */
/**
 * Number.prototype.format(n, x)
 * 
 * @param integer n: length of decimal
 * @param integer x: length of sections
 */
Number.prototype.format = function(n, x) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
};