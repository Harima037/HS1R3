
/*=====================================

    # Nombre:
        indicadoresFassa.js

    # Módulo:
        administrador/indicadoresFASSA

    # Descripción:
        

=====================================*/

// Inicialización General para casi cualquier módulo
//var proyectoResource = new RESTfulRequests(SERVER_HOST+'/v1/admin-proyectos-avances');

var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/estrategia-institucional');
var indicadorResource = new RESTfulRequests(SERVER_HOST+'/v1/admin-indicadoresFASSA-avances');
var moduloDatagrid = new Datagrid("#datagridEstrategia",moduloResource,{ formatogrid:true, pagina: 1});

moduloDatagrid.init();
//moduloDatagrid.actualizar();

var avancesNuevosStatus = {};
moduloDatagrid.actualizar({
    _success: function(response){
        moduloDatagrid.limpiar();
        var datos_grid = [];
        for(var i in response.data){
            var item = {};
            var registro = response.data[i];
            var clase_label = 'label-info';
            if(registro.idEstatus == 2){
                clase_label = 'label-warning';
            }else if(registro.idEstatus == 3){
                clase_label = 'label-danger';
            }else if(registro.idEstatus == 4){
                clase_label = 'label-primary';
            }else if(registro.idEstatus == 5){
                clase_label = 'label-success';
            }

            item.id = registro.id;
            item.programa = registro.claveProgramaPresupuestario + ' ' + registro.programa_presupuestario.descripcion;
            //item.clave = registro.claveProgramaPresupuestario+" - "+registro.claveProgramaSectorial+" - "+registro.claveProgramaPresupuestario;
            item.descripcion = registro.descripcionIndicador;
            //item.fecha_termino = registro.fechaTermino;
            item.estatus = '<span class="label ' + clase_label + '">' + registro.estatus.descripcion + '</span>';
            //item.boton = '<button onClick="cargar_datos_indicador('+response.data[i].id+')" type="button" class="btn btn-info"><span class="fa fa-edit"></span></button>';
            item.usuario = registro.usuario.username;
            item.fecha_modificado = registro.modificadoAl.substring(0,11);

            datos_grid.push(item);
        }
        
        moduloDatagrid.cargarDatos(datos_grid);
        moduloDatagrid.cargarTotalResultados(response.resultados,'<b>Estrategias Institucionales(s)</b>');
        var total = parseInt(response.resultados/moduloDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%moduloDatagrid.rxpag;
        if(plus>0) 
            total++;
        moduloDatagrid.paginacion(total);
    },
    _error: function(jqXHR){
        //console.log('{ error => "'+jqXHR.status +' - '+ jqXHR.statusText +'", response => "'+ jqXHR.responseText +'" }'); 
        var json = $.parseJSON(jqXHR.responseText);
        if(json.code == "W00"){
            moduloDatagrid.limpiar();
            moduloDatagrid.cargarTotalResultados(0,'<b>Indicador(es)</b>');
            var colspan = $(moduloDatagrid.selector + " thead > tr th").length;
            $(moduloDatagrid.selector + " tbody").append("<tr><td colspan='"+colspan+"' style='text-align:left'><i class='fa fa-info-circle'></i> "+json.data+"</td></tr>");

        }else{
            json.type = 'ERR';
            MessageManager.show(json);
            moduloDatagrid.limpiar();
        }
        
    }
});

/*function cargar_datos_indicador(e){
    indicadorResource.get(e,null,{
        _success: function(response){

            var nivel = '';
            switch(response.data.claveNivel){
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

            var tipo = '';
            switch(response.data.claveTipoFormula){
                case 'T':
                    tipo = 'Tasa';
                    break;
                case 'P':
                    tipo = 'Porcentaje';
                    break;
                
            }
            $('#modalDatosSeguimiento').find(".modal-title").html('Nivel: <small>' + nivel + '</small>');
            $('#nombre-indicador').text(response.data.indicador);
            $('#formula').text(response.data.formula);
            $('#tipo-formula').text(tipo);
            $('#indicador-id').val(response.data.id);

            if(response.data.tasa != null)
                $('#tasa').text(response.data.tasa);
            else    
                $('#tasa').text("N/A");
            
            var meses_capturados = {1:false,2:false,3:false,4:false,5:false,6:false,7:false,8:false,9:false,10:false,11:false,12:false};
            //console.log(response.data);
            for(var i in response.data.metas[0].registro_avance){
                evaluacion = response.data.metas[0].registro_avance[i];
                
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
                $('#estatus-avance-'+evaluacion.mes).val(evaluacion.idEstatus);
                $('#estatus-avance-'+evaluacion.mes).prop('disabled',false);
                $('#estatus-avance-'+evaluacion.mes).attr('avance-id',evaluacion.id);

                meses_capturados[evaluacion.mes] = true;
            }

            for(var i = 1; i <= 12; i++) {
                if(!meses_capturados[i]){
                    $('#estatus-avance-'+i).val(0);
                    $('#estatus-avance-'+i).prop('disabled',true);
                }
            }

            $('#modalDatosSeguimiento').modal('show');
        }
    });
}

function poner_estatus(mes){
    var estatus_id = $('#estatus-avance-'+mes).val();
    var avance_id = $('#estatus-avance-'+mes).attr('avance-id');
    avancesNuevosStatus[avance_id] = estatus_id;
}*/

function editar (e){
    moduloResource.get(e,{ "cambia_estatus": 1},{
        _success: function(response){
            $('#modalEditarEstrategia').find(".modal-title").text('Editar Proyecto');

            $('#nombre-programa').text(response.data.programa_presupuestario.descripcion);

            $('#estatus-programa').val(response.data.idEstatus);

            $('#id').val(response.data.id);
            
            $('#modalEditarEstrategia').modal('show');
        }
    });
}

$('#btn-guardar').on('click',function(){
    var parametros = $('#form_programa').serialize()+"&accion=admin";
    if($('#id').val()){
        moduloResource.put($('#id').val(),parametros,{
            _success: function(response){
                moduloDatagrid.actualizar();
                $('#modalEditarEstrategia').modal('hide');
            }
        });
    }
});

/*$('#btn-cambiar-estatus-avance').on('click',function(){
    
    if(Object.keys(avancesNuevosStatus).length > 0)
    {
        if($('#indicador-id').val()){
            indicadorResource.put($('#indicador-id').val(),{estatus: avancesNuevosStatus},{
                _success: function(response){
                    moduloDatagrid.actualizar();
                    $('#modalDatosSeguimiento').modal('hide');
                }
            });
        }
    }esle
    {
        alert("No se ha detectado ningún cambio de estatus");
    }
});

$('#modalDatosSeguimiento').on('hide.bs.modal',function(e){
    $('#tabla-reportes tbody .status-avance').val(0);
    $('#tabla-reportes tbody .status-avance').prop('disabled',false);
    $('#tabla-reportes tbody .status-avance').removeAttr('avance-id');
    avancesNuevosStatus = {};
});

$("#datagridIndicadores .txt-quick-search").off('keydown');
$("#datagridIndicadores .txt-quick-search").on('keydown', function(event){
    if (event.which == 13) {
        realizar_busqueda();
    }
});

$('#datagridIndicadores .btn-quick-search').off('click');
$('#datagridIndicadores .btn-quick-search').on('click',function(){
    realizar_busqueda();
})

function realizar_busqueda(){
    moduloDatagrid.setPagina(1);
    moduloDatagrid.parametros.buscar = $('.txt-quick-search').val();
    if($('#filtrar-ejercicio').prop('checked')){
        moduloDatagrid.parametros.ejercicio = $('#ejercicio').val();
    }else{
        delete moduloDatagrid.parametros.ejercicio;
    }
    moduloDatagrid.actualizar();
}*/