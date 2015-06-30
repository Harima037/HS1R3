/*=====================================

    # Nombre:
        reporte-ep-01.js

    # Módulos:
        cargar/reporte-ep-01

    # Descripción:
        Funciones para cargar archivos csv del EP01

=====================================*/

var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/cuenta-publica');
var moduloDatagrid = new Datagrid("#datagridCuentaPublica",moduloResource,{ formatogrid:true, pagina: 1});

moduloDatagrid.init();
moduloDatagrid.actualizar({
	_success: function(response){
        moduloDatagrid.limpiar();

        for(var i in response.data){
            response.data[i].mes = '<div class="text-center">'+parseInt(response.data[i].mes/3)+'</div>';
            if(response.data[i].cuentaPublica){
            	response.data[i].cuentaPublica = '<div class="text-center text-primary"><span class="fa fa-save"></span> Guardada</div>';
            }else{
            	response.data[i].cuentaPublica = '<div class="text-center"><span class="text-muted">Sin Guardar</span></div>';
            }
            delete response.data[i].idUsuarioValidacionSeg;
            delete response.data[i].unidadResponsable;
        }
        moduloDatagrid.cargarDatos(response.data);
        moduloDatagrid.cargarTotalResultados(response.resultados,'<b>Captura(s)</b>');
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

/*$('#btn-capturar-cuenta-publica').on('click', function () {
    $('#modalCuentaPublica').modal('show');
});*/

$('#btn-datos-institucionales').on('click', function () {
    $('#modalDatosInstitucionales').modal('show');
});

$('#btn-reporte-cuenta-publica').on('click', function(){
    window.open(SERVER_HOST+'/v1/reporte-cuenta-publica');
});

$('#modalCuentaPublica').on('hidden.bs.modal',function(){
    Validation.cleanFormErrors('#form_cuenta_publica');
    $('#form_cuenta_publica').get(0).reset();
});

$('#modalDatosInstitucionales').on('hidden.bs.modal',function(){
    Validation.cleanFormErrors('#form_datos_institucionales');
    $('#form_datos_institucionales').get(0).reset();
});

function editar(e){
    moduloResource.get(e,null,{
        _success: function(response){
        	if(response.data.cuentaPublica){
        		$('#modalCuentaPublica').find(".modal-title").html("Editar Cuenta Pública");
        		$('#cuenta-publica').val(response.data.cuentaPublica);
        	}else{
        		$('#modalCuentaPublica').find(".modal-title").html("Nueva Cuenta Pública");
        		$('#cuenta-publica').val(response.data.finalidadProyecto + '\n\n' + response.data.analisisResultado + '\n\n' + response.data.beneficiarios);
        	}
            $('#id').val(response.data.id);
            $('#modalCuentaPublica').modal('show');
        }
    });
}

$('#btn-guardar-datos-institucionales').on('click',function(){
    Validation.cleanFormErrors('#form_datos_institucionales');

    var parametros = $("#form_datos_institucionales").serialize();

    moduloResource.post(parametros,{
        _success: function(response){
            MessageManager.show({data:'Se guardaron correctamente los datos.',timer:5,type:'INF'});
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
});

$('#btn-guardar-cuenta-publica').on('click',function(){
	Validation.cleanFormErrors('#form_cuenta_publica');

    var parametros = $("#form_cuenta_publica").serialize();

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
});