
/*=====================================

    # Nombre:
        inversion.js

    # Módulo:
        expediente/inversion

    # Descripción:
        Se utiliza para crear, editar y eliminar caratulas de captura para proyectos de inversión

=====================================*/

// Inicialización General para casi cualquier módulo
var fibapResource = new RESTfulRequests(SERVER_HOST+'/v1/fibap');
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/inversion');
var moduloDatagrid = new Datagrid("#datagridCaratulas",moduloResource);
moduloDatagrid.init();
moduloDatagrid.actualizar({
    _success: function(response){
        moduloDatagrid.limpiar();
        var datos_grid = [];
        for(var i in response.data){
            var item = {};
            var clase_label = 'label-info';
            if(response.data[i].idEstatusProyecto == 2){
                clase_label = 'label-warning';
            }else if(response.data[i].idEstatusProyecto == 3){
                clase_label = 'label-danger';
            }

            item.id = response.data[i].id;
            item.clave = response.data[i].clavePresup;
            item.nombre_tecnico = response.data[i].nombreTecnico;
            var presupuesto = response.data[i].presupuestoRequerido || 0;
            item.presupuesto = '$ ' + presupuesto.format();
            item.estatus = '<span class="label ' + clase_label + '">' + response.data[i].estatusProyecto + '</span>';
            item.usuario = response.data[i].username;
            item.fecha_modificado = response.data[i].modificadoAl.substring(0,11);

            datos_grid.push(item);
        }
        moduloDatagrid.cargarDatos(datos_grid);                         
        var total = parseInt(response.resultados/moduloDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%moduloDatagrid.rxpag;
        if(plus>0) 
            total++;
        moduloDatagrid.paginacion(total);
    }
});
var modal_name = '#modalNuevoProyecto';
var form_name = '#form_proyecto';

/*===================================*/
// Configuración General para cualquier módulo

$(modal_name).on('shown.bs.modal', function () {
    $(modal_name).find('input').eq(0).focus();
});

$(modal_name).on('hide.bs.modal',function(e){ 
    resetModalModuloForm();
});

$('.btn-datagrid-agregar').on('click', function () {
    $(modal_name).find(".modal-title").html("Nuevo Proyecto de Inversión");
    $(modal_name).modal('show');
});

$('#btn-editar-proyecto').on('click',function(){
    window.location.href = SERVER_HOST+'/expediente/caratula-inversion/' + $('#btn-editar-proyecto').attr('data-id-proyecto');
});

$(modal_name+' .btn-guardar').on('click', function (e) {
    e.preventDefault();
    $(form_name).attr('action',SERVER_HOST+'/expediente/caratula-inversion');
    $(form_name).attr('method','POST');
    $(form_name).submit();
});

function cargar_datos_proyecto(e){
    var parametros = {'mostrar':'detalles-proyecto'};
    moduloResource.get(e,parametros,{
        _success: function(response) {
            detallesProyecto.mostrar_datos(response.data);
        }
    });
}

function resetModalModuloForm(){
    Validation.cleanFormErrors(form_name);
    $(form_name).get(0).reset();
    $(form_name +' #id').val("");
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