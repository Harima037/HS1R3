/*=====================================

    # Nombre:
        roles.js

    # Módulo:
        administrador/roles

    # Descripción:
        Se utiliza para crear, editar y eliminar roles de usuario

=====================================*/

// Inicialización General para casi cualquier módulo

var moduleResource = new RESTfulRequests(SERVER_HOST+'/v1/purgar-seguimientos');
var moduleDatagrid = new Datagrid("#datagridSeguimientos",moduleResource,{formatogrid:true,pagina:1,clasificacionProyecto:$('#clasificacion').val(),mes:$('#mes').val()});
moduleDatagrid.init();
moduleDatagrid.actualizar({
    _success: function(response){
        moduleDatagrid.limpiar();
        var datos_grid = [];

        for(var i in response.data){
            var item = {};

            item.id = response.data[i].id;
            item.clave = response.data[i].clavePresup;
            item.nombre_tecnico = response.data[i].nombreTecnico;
            item.validacion = response.data[i].validacion;
            item.enlace = response.data[i].enlace;
            
            if(response.data[i].idEstatus == 1){
                item.estado = '<span class="label label-info">'+response.data[i].estatusAvance+'</span>';
            }else if(response.data[i].idEstatus == 2){
                item.estado = '<span class="label label-warning">'+response.data[i].estatusAvance+'</span>';
            }else if(response.data[i].idEstatus == 3){
                item.estado = '<span class="label label-danger">'+response.data[i].estatusAvance+'</span>';
            }else if(response.data[i].idEstatus == 4){
                item.estado = '<span class="label label-primary">'+response.data[i].estatusAvance+'</span>';
            }else if(response.data[i].idEstatus == 5){
                item.estado = '<span class="label label-success">'+response.data[i].estatusAvance+'</span>';
            }
            datos_grid.push(item);
        }
        moduleDatagrid.cargarDatos(datos_grid);                         
        moduleDatagrid.cargarTotalResultados(response.resultados,'<b>Proyecto(s)</b>');
        var total = parseInt(response.resultados/moduleDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%moduleDatagrid.rxpag;
        if(plus>0) 
            total++;
        moduleDatagrid.paginacion(total);
    }
});
/*===================================*/
// Implementación personalizada del módulo

$('#mes').on('change',function(){
    realizar_busqueda();
});

$('#clasificacion').on('change',function(){
    realizar_busqueda();
});

function editar (e){
    //$('#modalSeguimiento').find(".modal-title").html("Detalles Seguimiento");
    //$('#modalSeguimiento').modal('show');
    /*moduleResource.get(e,null,{
        _success: function(response){
            $('#modalSeguimiento').find(".modal-title").html("Detalles Seguimiento");
            $('#modalSeguimiento').modal('show');
        }
    });*/
}

$("#datagridSeguimientos .txt-quick-search").off('keydown');
$("#datagridSeguimientos .txt-quick-search").on('keydown', function(event){
    if (event.which == 13) {
        realizar_busqueda();
    }
});

$('#datagridSeguimientos .btn-quick-search').off('click');
$('#datagridSeguimientos .btn-quick-search').on('click',function(){
    realizar_busqueda();
})

function realizar_busqueda(){
    moduleDatagrid.setPagina(1);
    moduleDatagrid.parametros.buscar = $('.txt-quick-search').val();
    moduleDatagrid.parametros.clasificacionProyecto = $('#clasificacion').val();
    moduleDatagrid.parametros.mes = $('#mes').val();
    moduleDatagrid.actualizar();
}

/*===================================*/
// Configuración General para cualquier módulo
$('#btn-purgar-seguimiento').on('click',function(){
    var mes_seleccionado = $('#mes option:selected').text();
    Confirm.show({
        titulo:"Purgar Seguimientos Incompletos",
        mensaje: "Esta acción eliminara todos los seguimientos del mes de "+mes_seleccionado+" cuyo proceso no haya finalizado, borrando todos los avances capturados ¿Está seguro de continuar?",
        botones: [ 
            {   selector:"btn-modal-confirm-si", 
                nombre: '<span class="fa fa-eraser"></span> Purgar', 
                clase: 'btn-danger', 
                callback: function(){ 
                    moduleResource.put($('#mes').val(), null,{
                        _success: function(response){
                            moduleDatagrid.actualizar();
                            MessageManager.show({data:'Seguimientos purgados con exito',container:'#modalRol .modal-body',type:'OK',timer:4});
                        }
                    });
                }},
            { selector:"btn-modal-confirm-no", nombre: 'Cancelar', clase: 'btn-default', callback: function(){ } }
        ]
    });
});
/*===================================*/
// Funciones adicionales por módulo
