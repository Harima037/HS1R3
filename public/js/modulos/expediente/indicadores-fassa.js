/*=====================================

    # Nombre:
        indicadores-fassa.js

    # Módulos:
        expediente/indicadores-fassa

    # Descripción:
        Funciones para captura de indicadores de FASSA

=====================================*/

var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/indicadores-fassa');
var moduloDatagrid = new Datagrid("#datagridIndicadores",moduloResource,{ formatogrid:true, pagina: 1});

moduloDatagrid.init();
//moduloDatagrid.actualizar();
moduloDatagrid.actualizar({
    _success: function(response){
        moduloDatagrid.limpiar();
        for(var i in response.data){
            var nivel = '';
            switch(response.data[i].claveNivel){
                case 'F':
                    nivel = 'Fin';
                    break;
                case 'P':
                    nivel = 'Proposito';
                    break;
                case 'C':
                    nivel = 'Componente';
                    break;
                case 'A':
                    nivel = 'Actividad';
                    break;
            }
            response.data[i].claveNivel = nivel;
        }
        moduloDatagrid.cargarDatos(response.data);                         
        var total = parseInt(response.resultados/moduloDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%moduloDatagrid.rxpag;
        if(plus>0) 
            total++;
        moduloDatagrid.paginacion(total);
    }
});

function editar(e){
    moduloResource.get(e,null,{
        _success: function(response){
            $('#modalIndicador').find(".modal-title").html('Editar Indicador');

            $('#nivel-indicador').val(response.data.claveNivel);
            $('#indicador').val(response.data.indicador);
            $('#tipo-formula').val(response.data.claveTipoFormula);
            $('#formula').val(response.data.formula);
            $('#fuente-informacion').val(response.data.fuenteInformacion);
            $('#numerador').val(response.data.numerador);
            $('#denominador').val(response.data.denominador);
            $('#unidad-responsable').val(response.data.claveUnidadResponsable);
            $('#porcentaje').text(parseFloat(response.data.porcentaje).format(2) + ' %');

            $('#responsable-informacion').empty();
            var html_opciones = '<option value="">Selecciona un responsable</option>';
            for(var i in response.data.responsables){
                html_opciones += '<option value="'+response.data.responsables[i].id+'">'+response.data.responsables[i].nombre+'</option>';
            }
            $('#responsable-informacion').html(html_opciones);
            $('#responsable-informacion').val(response.data.idResponsableInformacion);

            $('#id').val(response.data.id);
            $('#modalIndicador').modal('show');
        }
    });
}

$('#modalIndicador').on('shown.bs.modal', function () {
    $('#modalIndicador').find('input').eq(0).focus();
});

$('#modalIndicador').on('hidden.bs.modal',function(){
    $('#form_indicador_fassa').get(0).reset();
    $('#form_indicador_fassa #id').val("");
    $('#unidad-responsable').change();
    $('#porcentaje').text('%');
    Validation.cleanFormErrors('#form_indicador_fassa');
});

$('#btn-agregar-indicador').on('click', function () {
    $('#modalIndicador').find(".modal-title").html("Nuevo Indicador");
    $('#modalIndicador').modal('show');
});

$('#unidad-responsable').on('change',function(){
    if($(this).val()){
        var parametros = {'cargar-responsables':1,'unidad-responsable':$(this).val()};
        moduloResource.get(null,parametros,{
            _success: function(response){
                $('#responsable-informacion').empty();
                var html_opciones = '<option value="">Selecciona un responsable</option>';
                for(var i in response.data){
                    html_opciones += '<option value="'+response.data[i].id+'">'+response.data[i].nombre+'</option>';
                }
                $('#responsable-informacion').html(html_opciones);
            }
        });
    }else{
        $('#responsable-informacion').empty();
        var html_opciones = '<option value="">Selecciona una unidad</option>';
        $('#responsable-informacion').html(html_opciones);
    }
});

$('#btn-guardar-indicador').on('click',function(e){
    e.preventDefault();
    Validation.cleanFormErrors('#form_indicador_fassa');

    var parametros = $("#form_indicador_fassa").serialize();
    if($('#id').val()){
        moduloResource.put($('#id').val(), parametros,{
            _success: function(response){
                moduloDatagrid.actualizar();
                MessageManager.show({data:'Elemento actualizado con éxito',timer:4});
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
        },'Guardando');
    }else{
        moduloResource.post(parametros,{
            _success: function(response){
                moduloDatagrid.actualizar();
                $('#id').val(response.data.id);
                MessageManager.show({data:'Elemento creado con éxito',timer:4});
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
        },'Guardando');
    }
});

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