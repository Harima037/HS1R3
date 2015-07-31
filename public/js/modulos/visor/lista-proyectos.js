/*=====================================

    # Nombre:
        lista-proyectos.js

    # Módulos:
        visor/proyectos-inst
        visor/proyectos-inv

    # Descripción:
        Funciones para seguimiento de metas de proyectos institucionales y de inversión

=====================================*/
moduloDatagrid.init();

if($('#filtro-unidad').length){
    if($('#filtro-unidad').val() != ''){
        moduloDatagrid.parametros.unidad = $('#filtro-unidad').val();
    }
    $('#filtro-unidad').on('change',function(){
        moduloDatagrid.parametros.unidad = $('#filtro-unidad').val();
        $('#datagridProyectos .txt-go-page').val(1);
        moduloDatagrid.actualizar();
    });
}

if($('#filtro-jurisdiccion').length){
    if($('#filtro-jurisdiccion').val() != ''){
        moduloDatagrid.parametros.jurisdiccion = $('#filtro-jurisdiccion').val();
    }
    $('#filtro-jurisdiccion').on('change',function(){
        moduloDatagrid.parametros.jurisdiccion = $('#filtro-jurisdiccion').val();
        $('#datagridProyectos .txt-go-page').val(1);
        moduloDatagrid.actualizar();
    });
}

moduloDatagrid.actualizar({
    _success: function(response){
        moduloDatagrid.limpiar();
        var datos_grid = [];
        var mes_activo = response.mesActivo;
        var mes_actual = response.mesActual;
        
        for(var i in response.data){
            var proyecto = response.data[i];
            var item = {};
            item.id = proyecto.id;
            item.clave = proyecto.clavePresupuestaria;
            item.nombre_tecnico = proyecto.nombreTecnico;

            for(var mes = 1; mes <= 12 ; mes++){
                var icono = ''; //icono del mes
                var fondo = ''; //fondo del mes
                var estatus = '';
                if(mes < mes_actual){
                    icono = 'fa-times';
                    estatus = 'text-muted';
                }else{
                    icono = 'fa-lock';
                }

                if(mes == mes_activo){
                    icono = 'fa-unlock';
                }

                if(proyecto.meses[mes]){
                    if(proyecto.meses[mes].programado){
                        fondo = 'style="background-color:#DDDDDD"';
                    }
                    if(proyecto.meses[mes].estatus > 0){
                        if(proyecto.meses[mes].avance > 1){
                            estatus = 'text-danger';
                        }else if(proyecto.meses[mes].avance == 1){
                            estatus = 'text-success';
                        }else{
                            estatus = 'text-muted';
                        }
                        if(mes < mes_actual && proyecto.meses[mes].estatus == 6){
                            icono = 'fa-circle-o';
                        }else if(mes < mes_actual){
                            icono = 'fa-circle';
                        }else if(proyecto.meses[mes].estatus == 4 || proyecto.meses[mes].estatus == 5){
                            icono = 'fa-lock';
                        }
                    }
                }
                item['mes_'+mes] = '<div class="text-center '+estatus+'" '+fondo+'><span class="fa '+icono+'"></span></div>';
            }
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
    var parametros = [];
    if($('#filtro-unidad').length){
        if($('#filtro-unidad').val() != ''){
            parametros.push('u='+$('#filtro-unidad').val());
        }
    }
    if($('#filtro-jurisdiccion').length){
        if($('#filtro-jurisdiccion').val() != ''){
            parametros.push('j='+$('#filtro-jurisdiccion').val());
        }
    }

    if(parametros.length){
        var params = '?' + parametros.join('&');
    }else{
        var params = ''
    }
    
    window.location.href = SERVER_HOST+'/visor/avance-indicadores/' + e + params;
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