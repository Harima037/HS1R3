/*=====================================

    # Nombre:
        indicadores-resultados.js

    # Módulo:
        reportes/indicadores-resultados

    # Descripción:
        Para imprimir reporte de indicadores de resultados

=====================================*/

// Inicialización General para casi cualquier módulo

var moduleResource = new RESTfulRequests(SERVER_HOST+'/v1/indicadores-resultados');
var moduleDatagrid = new Datagrid("#datagridProyectos",moduleResource,{formatogrid:true,pagina:1,ejercicio:$('#ejercicio').val(),mes:$('#mes').val()});
moduleDatagrid.init();
moduleDatagrid.actualizar({
    _success: function(response){
        moduleDatagrid.limpiar();
        var datos_grid = [];
        for(var i in response.data){
            var item = {};

            item.id = response.data[i].id;
            item.clave = response.data[i].ClavePresupuestaria;
            item.nombre_tecnico = response.data[i].nombreTecnico;
            item.beneficiarios = '<div class="text-center">'+response.data[i].beneficiarios+'</div>';
            var estatus = '';

            if(response.data[i].idEstatusAvance){
                if(response.data[i].beneficiarios == 1 || (response.data[i].beneficiarios > 1 && response.data[i].indicadorResultadoBeneficiarios)){
                    estatus = ' <span class="text-success fa fa-check"></span>';
                }else{
                    estatus = ' <span class="text-muted fa fa-minus"></span>';
                }
            }else{
                estatus = ' <span class="text-muted fa fa-times"></span>';
            }

            item.estatus = estatus;
            
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

function editar (e){
    var parametros = {mes:$('#mes').val()}
    moduleResource.get(e,parametros,{
        _success: function(response){
            $('#clave-presupuestaria').text(response.data.ClavePresupuestaria);
            $('#nombre-tecnico').text(response.data.nombreTecnico);
            $('#sub-funcion').text(response.data.subFuncionDescripcion);

            $('#tabla_beneficiarios tbody').empty();

            var beneficiarios = {};
            for(var i in response.data.beneficiarios_descripcion){
                var beneficiario = response.data.beneficiarios_descripcion[i];

                if(!beneficiarios[beneficiario.idTipoBeneficiario]){
                    beneficiarios[beneficiario.idTipoBeneficiario] = {
                        tipo:beneficiario.tipoBeneficiario,
                        f:0,
                        m:0,
                        total:0
                    };
                }
                if(beneficiario.sexo){
                    beneficiarios[beneficiario.idTipoBeneficiario][beneficiario.sexo] = parseInt(beneficiario.avanceBeneficiario);
                    beneficiarios[beneficiario.idTipoBeneficiario].total += parseInt(beneficiario.avanceBeneficiario);
                }
            }
            var html = '';
            for(var i in beneficiarios){
                html += '<tr>';
                html += '<td>'+beneficiarios[i].tipo+'</td>';
                html += '<td>'+beneficiarios[i].f+'</td>';
                html += '<td>'+beneficiarios[i].m+'</td>';
                html += '<td>'+beneficiarios[i].total+'</td>';
                html += '</tr>';
            }
            $('#tabla_beneficiarios tbody').html(html);

            if(response.data.idAvanceMes){
                $('#beneficiarios').prop('disabled',false);
                $('#beneficiarios').val(response.data.indicadorResultadoBeneficiarios);
            }else{
                $('#beneficiarios').prop('disabled',true);
                $('#beneficiarios').val('');
            }

            if(response.data.idAvanceMes){
                $('#id').val(response.data.idAvanceMes);
            }else{
                $('#id').val('');
            }
            
            $('#modalDatosReporte').modal('show');
        }
    });
}

$('#btn-guardar-beneficiarios').on('click',function (e){
    e.preventDefault();

    if($('#id').val()){
        if(parseInt($('#beneficiarios').val())){
            var parametros = {beneficiarios:$('#beneficiarios').val()};
        }else{
            var parametros = {beneficiarios:''};
        }
        moduleResource.put($('#id').val(),parametros,{
            _success:function(response){
                moduleDatagrid.actualizar();
                $('#modalDatosReporte').modal('hide');
            }
        });
    }
});

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
    moduleDatagrid.setPagina(1);
    moduleDatagrid.parametros.buscar = $('.txt-quick-search').val();
    moduleDatagrid.parametros.ejercicio = $('#ejercicio').val();
    moduleDatagrid.parametros.mes = $('#mes').val();
    moduleDatagrid.actualizar();
}

/*===================================*/
// Configuración General para cualquier módulo
$('#btn-descargar-reporte').on('click',function(){
    var parametros = '?mes='+$('#mes').val()+'&ejercicio='+$('#ejercicio').val();
    if($('.txt-quick-search').val()){
        parametros += '&buscar='+$('.txt-quick-search').val();
    }
    window.open(SERVER_HOST+'/v1/rep-indicadores-resultados'+parametros);
});
/*===================================*/
// Funciones adicionales por módulo

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
