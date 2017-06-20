/*=====================================

    # Nombre:
        lista-proyectos-rendicion.js

    # Módulos:
        seguimiento/seguimiento-inst
        seguimiento/seguimiento-inv

    # Descripción:
        Funciones para seguimiento de metas de proyectos institucionales y de inversión

=====================================*/
moduloDatagrid.init();
moduloDatagrid.actualizar({
    _success: function(response){
        moduloDatagrid.limpiar();
        var datos_grid = [];
        var mes_activo = parseInt($('#datagridProyectos').attr('data-mes-activo'));
        var mes_actual = parseInt($('#datagridProyectos').attr('data-mes-actual'));
        var evaluacion;
        var trimestre = $('#datagridProyectos').attr('data-trim-activo');

        for(var i in response.data){
            var item = {};

            item.id = response.data[i].id;
            item.clave = response.data[i].clavePresup;
            item.nombre_tecnico = response.data[i].nombreTecnico;
            //item.revisor = response.data[i].nombreRevisor || '<span class="text-muted">Sin revisor asignado</span>';
            datos_grid.push(item);
        }
        moduloDatagrid.cargarDatos(datos_grid);                         
        moduloDatagrid.cargarTotalResultados(response.resultados,'<b>Proyecto(s)</b>');
        var total = parseInt(response.resultados/moduloDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%moduloDatagrid.rxpag;
        if(plus>0) 
            total++;
        moduloDatagrid.paginacion(total);
    }
});

function cargar_datos_proyecto(e){
    moduloResource.get(e,null,{
        _success: function(response){
            $('#modalDatosSeguimiento').find(".modal-title").html('Clave: <small>' + response.data.ClavePresupuestaria + '</small>');

            $('#nombre-tecnico').text(response.data.nombreTecnico);
            $('#programa-presupuestario').text(response.data.datos_programa_presupuestario.clave + ' ' + response.data.datos_programa_presupuestario.descripcion);
            $('#funcion').text(response.data.datos_funcion.clave + ' ' + response.data.datos_funcion.descripcion);
            $('#subfuncion').text(response.data.datos_sub_funcion.clave + ' ' + response.data.datos_sub_funcion.descripcion);
            $('#id').val(response.data.id);

            for(var i in response.data.evaluacion_meses){
                evaluacion = response.data.evaluacion_meses[i];
                var icono = 'fa-file-pdf-o';
                var clase = 'btn-default';
                var firmar='';

                if(evaluacion.idEstatus == 4){
                    icono = 'fa-check';
                    clase = 'btn-primary';
                    firmar='<li><a href="#" onClick="firmarProyecto('+evaluacion.mes+')" class="btn-edit-rows"><span class="glyphicon glyphicon-edit"></span> Firmar</a> </li>';
                }else{
                    icono = 'fa-pencil';
                    clase = 'btn-success';
                    firmar='';
                }
                $('#rep_metas_'+evaluacion.mes).html('<div class="btn-group"><button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button><ul class="dropdown-menu pull-right" role="menu"><li><a href="#" onClick="cargarReporte(\'seg-metas\','+evaluacion.mes+')" class="btn-edit-rows" type="button"><span class="fa '+icono+'"></span> Imprimir</a></li>'+firmar+'</ul></div>');
                    
                if(evaluacion.mes%3 == 0){

                    $('#rep_metas_trim_'+evaluacion.mes).html('<button onClick="cargarReporte(\'seg-metas-trimestre\','+evaluacion.mes+')" class="btn '+clase+'" type="button"><span class="fa '+icono+'"></span></button>');
                    $('#rep_benef_'+evaluacion.mes).html('<button onClick="cargarReporte(\'seg-beneficiarios\','+evaluacion.mes+')" class="btn '+clase+'" type="button"><span class="fa '+icono+'"></span></button>');
                    $('#rep_plan_'+evaluacion.mes).html('<button onClick="cargarReporte(\'plan-mejora\','+evaluacion.mes+')" class="btn '+clase+'" type="button"><span class="fa '+icono+'"></span></button>');
                    $('#rep_cuenta_'+evaluacion.mes).html('<button onClick="cargarReporte(\'analisis\','+evaluacion.mes+')" class="btn '+clase+'" type="button"><span class="fa '+icono+'"></span></button>');
                }
            }

            $('#modalDatosSeguimiento').modal('show');
        }
    });
}
$('#modalDatosSeguimiento').on('hide.bs.modal',function(e){
    $('#tabla-reportes tbody td.reporte-boton').html('<span class="fa fa-times"></span>');
});

function cargarReporte(tipo,mes){
    if($('#id').val()){
        var parametros = $('#id').val() + '?tipo='+tipo+'&mes='+mes;
        window.open(SERVER_HOST+'/v1/reporte-evaluacion/'+parametros);
    }
}

function firmarProyecto(mes){
    
    Confirm.show({
        titulo:"¿Poner el programa en el estatus de firma?",
        mensaje: "¿Estás seguro que desea poner el estatus de firma? Una vez hecho esto, el programa ya no es modificable, y se entiende que se aprobó y firmó.",
        callback: function(){
            var parametros = 'actualizarproyecto=firmar&mes='+mes;  
            console.log("el id "+ $('#id').val()+" mes"+parametros);                
            
            moduloResourceProyecto.put($('#id').val(),parametros,{
                        _success: function(response){
                            //window.location = "../revision/segui-proyectos-inst";
                            moduloDatagrid.actualizar();
                            $('#modalDatosSeguimiento').modal('hide');
                            MessageManager.show({data:'El programa ha sido ha sido puesto en el estatus de firma',type:'OK',timer:3});                  
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


$("#datagridProyectos .txt-quick-search").off('keydown');
$("#datagridProyectos .txt-quick-search").on('keydown', function(event){
    if (event.which == 13) {
        realizar_busqueda();
    }
});

$('#datagridProyectos .btn-quick-search').off('click');
$('#datagridProyectos .btn-quick-search').on('click',function(){
    realizar_busqueda();
})

function realizar_busqueda(){
    moduloDatagrid.setPagina(1);
    moduloDatagrid.parametros.buscar = $('.txt-quick-search').val();
    moduloDatagrid.parametros.ejercicio = $('#ejercicio').val();
    moduloDatagrid.actualizar();
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