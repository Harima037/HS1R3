/*=====================================

    # Nombre:
        reporte-regionalizado.js

    # Módulos:
        cargar/reporte-regionalizado

    # Descripción:
        Funciones para cargar archivos csv del EP01 regionalizado

=====================================*/

var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/reporte-regionalizado');
var moduloDatagrid = new Datagrid("#datagridCarga",moduloResource,{ formatogrid:true, pagina: 1});

moduloDatagrid.init();
moduloDatagrid.actualizar({
    _success: function(response){
        moduloDatagrid.limpiar();
        var meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        for(var i in response.data){
            response.data[i].mes = meses[response.data[i].mes-1];
            response.data[i].modificadoAl = response.data[i].modificadoAl.substring(0,11);
            response.data[i].totalRegistros = parseFloat(response.data[i].totalRegistros).format(2);
            response.data[i].totalImporte = '$ ' + parseFloat(response.data[i].totalImporte).format(2);
        }
        moduloDatagrid.cargarDatos(response.data);
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
            var colspan = $(moduloDatagrid.selector + " thead > tr th").length;
            $(moduloDatagrid.selector + " tbody").append("<tr><td colspan='"+colspan+"' style='text-align:left'><i class='fa fa-info-circle'></i> "+json.data+"</td></tr>");

        }else{
            json.type = 'ERR';
            MessageManager.show(json);
            moduloDatagrid.limpiar();
        }
        
    }
});

$('#btn-cargar-achivo').on('click', function () {
    $('#modalCargar').find(".modal-title").html("Nueva Carga");
    $('#modalCargar').modal('show');
});

$('#modalCargar').on('hidden.bs.modal',function(){
    $('#form_carga').get(0).reset();
    $('#form_carga #id').val("");
    $('#tabla-conteo-totales tbody').empty();
    $('#tabla-conteo-totales tbody').html('<tr><td>0</td><td>$ 0.00</td></tr>');
    $('#archivo').prop('disabled',false);
    $('#ejercicio').prop('disabled',false);
    Validation.cleanFormErrors('#form_carga');
});

$('#btn-subir-archivo').on('click',function(){
    Validation.cleanFormErrors('#form_carga');

    var parametros = $("#form_carga").serialize();
    if($('#id').val()==""){
        var cuantosArchivos = document.getElementById("archivo").files.length;  
        if(cuantosArchivos>0)
            subirArchivoConDatos();         
        else
            MessageManager.show({data:'Debe seleccionar un archivo a subir.',timer:10,type:'ERR'});
    }else{
        moduloResource.put($('#id').val(),parametros,{
            _success: function(response){
                MessageManager.show({data:'Se guardaron correctamente los datos.',timer:5,type:'INF'});
                moduloDatagrid.actualizar();
            },
            _error: function( response ){
                try{
                    var json = $.parseJSON(response.responseText);
                    if(!json.code)
                        MessageManager.show({code:'S03',timer:10,data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
                    else{
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

function editar(e){
    moduloResource.get(e,null,{
        _success: function(response){
            $('#modalCargar').find(".modal-title").html('Editar Carga');

            $('#mes').val(response.data.mes);
            $('#ejercicio').val(response.data.ejercicio);
            
            $('#archivo').prop('disabled',true);
            $('#ejercicio').prop('disabled',true);
            
            var html_row = '<tr>' +
            '<td>' + parseFloat(response.data.totalRegistros).format() + '</td>' +
            '<td>$ ' + parseFloat(response.data.totalImporte).format(2) + '</td>' +
            '</tr>';

            $('#tabla-conteo-totales tbody').html(html_row);
            
            $('#id').val(response.data.id);
            $('#modalCargar').modal('show');
        }
    });
}

function subirArchivoConDatos(){
    var data  = new FormData();
    
    var archivo = document.getElementById("archivo").files;
        
    $("#loading").fadeIn();
    data.append('datoscsv', archivo[0]); 
    data.append('mes', $('#mes').val());
    data.append('ejercicio', $('#ejercicio').val());
    
    $.ajax({
        url: SERVER_HOST+'/v1/reporte-regionalizado', //Url a donde la enviaremos
        type:'POST', //Metodo que usaremos
        dataType:'json',
        contentType:false, //Debe estar en false para que pase el objeto sin procesar
        data:data, //Le pasamos el objeto que creamos con los archivos
        processData:false, //Debe estar en false para que JQuery no procese los datos a enviar
        cache:false, //Para que el formulario no guarde cache,
        success: function(response){ 
            $("#loading").fadeOut();            
            MessageManager.show({data:'Se guardaron correctamente los datos. Se almacenaron '+response.data.totalRegistros+' registros',timer:5,type:'INF'});

            var html_row = '<tr>' +
            '<td>' + parseFloat(response.data.totalRegistros).format() + '</td>' +
            '<td>$ ' + parseFloat(response.data.totalImporte).format(2) + '</td>' +
            '</tr>';

            $('#tabla-conteo-totales tbody').empty();
            $('#tabla-conteo-totales tbody').html(html_row);
            $('#archivo').prop('disabled',true);
            $('#ejercicio').prop('disabled',true);
            $('#id').val(response.data.id);
            moduloDatagrid.actualizar();
        },
        error: function( response ){
            $("#loading").fadeOut(function(){ 
                try{
                    var json = $.parseJSON(response.responseText);
                    if(!json.code)
                        MessageManager.show({code:'S03',timer:10,data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
                    else{
                        MessageManager.show(json);
                    }
                    Validation.formValidate(json.data);
                }catch(e){
                    console.log(e);
                }
            });
        }   
    });
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