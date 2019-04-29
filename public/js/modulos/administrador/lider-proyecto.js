/*=====================================

    # Nombre:
        roles.js

    # Módulo:
        administrador/roles

    # Descripción:
        Se utiliza para crear, editar y eliminar roles de usuario

=====================================*/

// Inicialización General para casi cualquier módulo

var permisos = [];
var responsablesResource = new RESTfulRequests(SERVER_HOST+'/v1/lideres-proyectos');
var areasResource = new RESTfulRequests(SERVER_HOST+'/v1/buscar-responsables-area');
var cargosResponsable = new RESTfulRequests(SERVER_HOST+'/v1/buscar-areas-responsable');
var datosResponsable = new RESTfulRequests(SERVER_HOST+'/v1/datos-responsable');
var guardarResponsable = new RESTfulRequests(SERVER_HOST+'/v1/guardar-responsable');

var responsablesDatagrid = new Datagrid("#datagridResponsables",responsablesResource);

$('.chosen-one').chosen({width:'100%',search_contains:true,enable_split_word_search:true,no_results_text: "No se econtraron resultados para "});

responsablesDatagrid.init();
responsablesDatagrid.parametros.filtro_activos = $('#filtro_activos').val();
responsablesDatagrid.actualizar({
    _success: function(response){
        responsablesDatagrid.limpiar();
        var datos_grid = [];
        for(var i in response.data){
            var item = response.data[i];
            if(!item.fechaFin){
                item.fechaFin = '-';
            }
            datos_grid.push(item);
        }
        responsablesDatagrid.cargarDatos(datos_grid);                         
        responsablesDatagrid.cargarTotalResultados(response.resultados,'<b>Responsables(s)</b>');
        var total = parseInt(response.resultados/responsablesDatagrid.rxpag); 
        var plus = parseInt(response.resultados)%responsablesDatagrid.rxpag;
        if(plus>0) 
            total++;
        responsablesDatagrid.paginacion(total);
    }
});

$('#modalRol').on('shown.bs.modal', function () {
    $('#modalRol').find('input').eq(0).focus();
});
$('#modalRol').on('show.bs.modal', function () {
    Validation.cleanFormErrors("#formRol");
    MessageManager.dismissAlert('#modalRol .modal-body');    
});

/*===================================*/
// Implementación personalizada del módulo

/*
function bloquear_controles(){
	$('input,textarea,select').each(function(){
		$(this).prop('disabled',true);
		$('label[for="' + $(this).attr('id') + '"]').prepend('<span class="fa fa-lock"></span> ');
		if($(this).hasClass('chosen-one')){
			$(this).trigger('chosen:updated');
		}
	});
}
*/

$('#btnRolAgregar').on('click', function () {
    $('#modalRol').find(".modal-title").html("Nuevo");

    $('#btn-nuevo-cargo').hide();
    $('#btn-terminar-cargo').hide();
    $('#btn-finalizar-cargo').hide();
    $('#btn-asignar-cargo').hide();
    $('#btn-editar').hide();
    $('#btn-guardar').show();
    $('.btns-responsable').attr('disabled',false);
    $('#panel-cargando-cargo-ocupado').hide();
    $('#panel-cargo-ocupado').hide();
    $('#panel-datos-responsable').hide();
    $('#formRol input, #formRol select').attr('disabled',false);
    $('#formRol select.chosen-one').trigger('chosen:updated');
    $('#panel-cargando-datos-responsable').hide();
    $('#panel-datos-responsable-cargo').hide();

    $('#tabla-historial-cargos tbody').html('<tr><td colspan="3">Sin cargos anteriores</td></tr>');
    $('#conteo-historial-cargos').html(0);

    $('#tabla-proyectos-asignados tbody').html('<tr><td colspan="2">Sin proyectos asignados</td></tr>');
    $('#conteo-proyectos-asignados').html(0);

    $('#modalRol').modal('show');
});

function editar (e){
    $('#btn-nuevo-cargo').hide();
    $('#btn-terminar-cargo').hide();
    $('#btn-finalizar-cargo').hide();
    $('#btn-asignar-cargo').hide();
    $('#btn-guardar').hide();
    $('#btn-editar').show();
    $('.btns-responsable').attr('disabled',true);
    $('#panel-cargando-cargo-ocupado').hide();
    $('#panel-cargo-ocupado').hide();
    $('#panel-datos-responsable').hide();
    $('#panel-cargando-datos-responsable').hide();
    $('#panel-datos-responsable-cargo').hide();
    
    responsablesResource.get(e,null,{
        _success: function(response){
            
            $('#formRol #id_cargo').val(response.data.recurso.id); //Cambiar el id que se usa en idResponsableInformacion al id del cargo en lugar del id del directorio
            $('#formRol #responsable').val(response.data.recurso.idDirectorio);
            $('#formRol #cargo').val(response.data.recurso.cargo);
            $('#formRol #telefono').val(response.data.recurso.telefono);
            $('#formRol #extension').val(response.data.recurso.extension);
            $('#formRol #area').val(response.data.recurso.idArea);
            $('#formRol #fecha_inicio').val(response.data.recurso.fechaInicio);

            if(response.data.recurso.fechaFin){
                $('#formRol #fecha_fin').val(response.data.recurso.fechaFin);
            }
            
            if(response.data.recurso.fechaFin){
                $('#btn-nuevo-cargo').show();
            }else{
                $('#btn-terminar-cargo').show();
            }

            $('#formRol input,#formRol select').attr('disabled',true);
            $('#formRol select.chosen-one').trigger('chosen:updated');

            var historial = response.data.historial;
            var historial_body = '<tr><td colspan="3">Sin cargos anteriores</td></tr>';
            var conteo_historial = historial.length;

            if(historial.length > 0){
                historial_body = '';
                for(var i in historial){
                    historial_body += '<tr><td>'+historial[i].descripcion+'</td><td>'+historial[i].fechaInicio+'</td><td>'+((historial[i].fechaFin)?historial[i].fechaFin:'-')+'</td></tr>';
                }
            }

            $('#tabla-historial-cargos tbody').html(historial_body);
            $('#conteo-historial-cargos').html(conteo_historial);

            var proyectos = response.data.proyectos;
            var proyectos_body = '<tr><td colspan="2">Sin proyectos asignados</td></tr>';
            var conteo_proyectos = proyectos.length;

            if(proyectos.length){
                proyectos_body = '';
                for(var i in proyectos){
                    proyectos_body += '<tr><td>'+proyectos[i].ClavePresupuestaria+'</td><td>'+proyectos[i].nombreTecnico+'</td></tr>';
                }
            }
            
            $('#tabla-proyectos-asignados tbody').html(proyectos_body);
            $('#conteo-proyectos-asignados').html(conteo_proyectos);

            $('#modalRol').find(".modal-title").html("Editar");
            $('#modalRol').modal('show');
        }
    }); 
}

$('#area').on('change',function(){
    if($('#fecha_fin').val() == ''){
        $('#panel-cargando-cargo-ocupado').show();
        $('#panel-cargo-ocupado').hide();
        var area_id = $('#area').val();
        areasResource.get(area_id,null,{
            _success: function(response){
                console.log(response);
                if(response.data.length){
                    var nombres = [];
                    var id_persona_asignada = 0;

                    for (let index = 0; index < response.data.length; index++) {
                        if(!response.data[index].fechaFin){
                            nombres.push(response.data[index].nombre);
                            id_persona_asignada = response.data[index].idDirectorio;
                        }
                    }

                    //if(response.data[0].idDirectorio != $('#responsable').val()){
                    if(nombres.length > 1){
                        $('#nombre-persona-asignada').html(nombres.join(', '));
                        $('#panel-cargo-ocupado').show();
                    }else if(nombres.length == 1 && id_persona_asignada != $('#responsable').val()){
                        $('#nombre-persona-asignada').html(nombres.join(', '));
                        $('#panel-cargo-ocupado').show();
                    }else{
                        $('#nombre-persona-asignada').html('');
                    }
                    
                    $('#cargo').val(response.data[0].cargo);
                    
                    $('#telefono').val(response.data[0].telefono);
                    $('#extension').val(response.data[0].extension); 
                }else{
                    $('#cargo').val('');
                    $('#telefono').val('');
                    $('#extension').val(''); 
                }
                $('#panel-cargando-cargo-ocupado').hide();
            }
        }); 
    }
});

$('#responsable').on('change',function(){
    $('#panel-cargando-datos-responsable').show();
    $('#panel-datos-responsable-cargo').hide();
    var parametros = {solo_activos:0};
    var responsable_id = $('#responsable').val();
    cargosResponsable.get(responsable_id,parametros,{
        _success: function(response){
            var historial_body = '<tr><td colspan="3">Sin cargos anteriores</td></tr>';
            var conteo_historial = response.data.length;
            if(response.data.length){
                historial_body = '';

                for(var i in response.data){
                    var cargo = response.data[i];
                    historial_body += '<tr><td>'+cargo.descripcion+'</td><td>'+cargo.fechaInicio+'</td><td>'+((cargo.fechaFin)?cargo.fechaFin:'-')+'</td></tr>';
                    if(!cargo.fechaFin){
                        if(cargo.id != $('#id_cargo').val()){
                            $('#cargo-asignado-responsable').html(response.data[0].descripcion);
                            $('#panel-datos-responsable-cargo').show();
                        }
                    }
                }
            }
            $('#tabla-historial-cargos tbody').html(historial_body);
            $('#conteo-historial-cargos').html(conteo_historial);

            $('#panel-cargando-datos-responsable').hide();
        }
    }); 
});

$('#btn-nuevo-responsable').on('click',function(){
    $('#panel-datos-responsable').show();
    $('#formRol .form-datos-cargo').attr('disabled',true);
    $('#formRol select.chosen-one').trigger('chosen:updated');
    $('#id_directorio').val('');
    $('#nombre').val('');
    $('#email').val('');
});

$('#btn-editar-responsable').on('click',function(){
    if($('#responsable').val() > 0){
        $('#formRol .form-datos-cargo').attr('disabled',true);
        $('#formRol select.chosen-one').trigger('chosen:updated');
        $('#panel-cargando-datos-responsable').show();

        datosResponsable.get($('#responsable').val(),null,{
            _success: function(response){
                console.log(response);
                $('#id_directorio').val(response.data.id);
                $('#nombre').val(response.data.nombre);
                $('#email').val(response.data.email); 
                $('#panel-cargando-datos-responsable').hide();
                $('#panel-datos-responsable').show();
            }
        });
    }else{
        MessageManager.show({code:'S03',data:"Seleccione un responsable para poder editar."});
    }
});

$('#btn-guardar-datos-responsable').on('click',function(){
    Validation.cleanFormErrors("#formRol");
    MessageManager.dismissAlert('body');

    var parametros = $("#formRol").serialize();

    guardarResponsable.post(parametros,{
        _success: function(response){
            console.log(response);
            responsablesDatagrid.actualizar();
            MessageManager.show({data:'Datos guardados con éxito',container:'#modalRol .modal-body',type:'OK',timer:4});

            if($('#id_directorio').val()){
                $('#responsable option[value="'+$('#id_directorio').val()+'"]').text(response.data.nombre);
            }else{
                $('#formRol #id_directorio').val(response.data.id);
                $('#responsable').append(new Option(response.data.nombre, response.data.id));
            }
            $('#responsable').trigger('chosen:updated');
        },
        _error: function(response){
            try{
                var json = $.parseJSON(response.responseText);
                if(!json.code)
                    MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
                else{
                    json.container = '#modalRol .modal-body';
                    //json.timer = 4;
                    MessageManager.show(json);
                }
                Validation.formValidate(json.data);
            }catch(e){
                console.log(e);
            }                       
        }
    });

});

$('#btn-cancelar-datos-responsable').on('click',function(){
    $('#panel-datos-responsable').hide();
    $('#formRol .form-datos-cargo').attr('disabled',false);
    $('#formRol select.chosen-one').trigger('chosen:updated');
    if($('#formRol #fecha_fin').val() == ''){
        $('#formRol #fecha_fin').attr('disabled',true);
    }
});

$('#btn-nuevo-cargo').on('click',function(){
    $('#formRol input, #formRol select').attr('disabled',false);
    $('#area').val(0);
    $('#formRol select.chosen-one').trigger('chosen:updated');
    $('#cargo').val('');
    $('#telefono').val('');
    $('#extension').val(''); 
    $('#fecha_inicio').val(''); 
    $('#fecha_fin').val('');
    $('#id_cargo').val(''); //Reseteamos idCargo, asi sabemos que estamos asignando nuevos valores en lugar de editar el ya existente
    $('#formRol #fecha_fin').attr('disabled',true);
    $('#panel-cargo-ocupado').hide();
    $('#panel-datos-responsable').hide();
    $('#btn-guardar').hide();
    $('#btn-editar').hide();
    $('#btn-nuevo-cargo').hide();
    $('#btn-asignar-cargo').show();
    $('.btns-responsable').attr('disabled',false);
});

$('#btn-terminar-cargo').on('click',function(){
    $('#fecha_fin').attr('disabled',false);
    $('#btn-terminar-cargo').hide();
    $('#btn-guardar').hide();
    $('#btn-editar').hide();
    $('#btn-finalizar-cargo').show();
});

$('#btn-editar').on('click',function(){
    $('#formRol input, #formRol select').attr('disabled',false);
    $('#formRol select.chosen-one').trigger('chosen:updated');
    $('#btn-guardar').show();
    $('#btn-editar').hide();
    $('#btn-terminar-cargo').hide();
    $('#btn-nuevo-cargo').hide();
    $('.btns-responsable').attr('disabled',false);
    if($('#fecha_fin').val() == ''){
        $('#formRol #fecha_fin').attr('disabled',true);
    }
});

$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    var target = $(e.target).attr("href") // activated tab
    if(target == '#formulario-responsable'){
        $('#botones-accion-modal').show();
    }else{
        $('#botones-accion-modal').hide();
    }
});

$("#datagridResponsables .txt-quick-search").off('keydown');
$("#datagridResponsables .txt-quick-search").on('keydown', function(event){
    if (event.which == 13) {
        realizar_busqueda();
    }
});

$('#datagridResponsables .btn-quick-search').off('click');
$('#datagridResponsables .btn-quick-search').on('click',function(){
    realizar_busqueda();
})

function realizar_busqueda(){
    responsablesDatagrid.setPagina(1);
    responsablesDatagrid.parametros.buscar = $('.txt-quick-search').val();
    responsablesDatagrid.parametros.filtro_activos = $('#filtro_activos').val();
    responsablesDatagrid.actualizar();
}

$('#filtro_activos').on('change',function(){
    realizar_busqueda();
});

/*===================================*/
// Configuración General para cualquier módulo
$('#modalRol').on('hide.bs.modal', function () {
    resetModalRolForm();
});

$('#btn-guardar').on('click', function (e) {
    e.preventDefault();
    submitModulo();
}); 

$('#btn-asignar-cargo').on('click', function (e) {
    e.preventDefault();
    submitModulo();
}); 

$('#btn-finalizar-cargo').on('click',function(e){
    e.preventDefault();
    submitModulo('TERMINAR');
});

function submitModulo(accion = ''){
    Validation.cleanFormErrors("#formRol");
    MessageManager.dismissAlert('body');

    var parametros = $("#formRol").serialize();

    if(accion == 'TERMINAR'){
        parametros = {fecha_fin:$('#fecha_fin').val(),terminar_cargo:1};
    }

    if($('#formRol #id_cargo').val() == ""){
        responsablesResource.post(parametros,{
            _success: function(response){
                responsablesDatagrid.actualizar();
                MessageManager.show({data:'Datos guardados con éxito',container:'#modalRol .modal-body',type:'OK',timer:4});
                $('#formRol #id_cargo').val(response.data.id);
            },
            _error: function(response){
                try{
                    var json = $.parseJSON(response.responseText);
                    if(!json.code)
                        MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
                    else{
                        json.container = '#modalRol .modal-body';
                        //json.timer = 4;
                        MessageManager.show(json);
                    }
                    Validation.formValidate(json.data);
                }catch(e){
                    console.log(e);
                }                       
            }
        });
    }else{
        responsablesResource.put($('#formRol #id_cargo').val(), parametros,{
            _success: function(response){
                console.log(response);
                responsablesDatagrid.actualizar();
                MessageManager.show({data:'Datos actualizados con éxito',container:'#modalRol .modal-body',type:'OK',timer:4});
            },
            _error: function(response){
                try{
                    var json = $.parseJSON(response.responseText);
                    if(!json.code)
                        MessageManager.show({code:'S03',data:"Hubo un problema al realizar la transacción, inténtelo de nuevo o contacte con soporte técnico."});
                    else{
                        json.container = '#modalRol .modal-body';
                        //json.timer = 4;
                        MessageManager.show(json);
                    }
                    Validation.formValidate(json.data);
                }catch(e){
                    console.log(e);
                }                       
            }
        });
    }
}
/*===================================*/
// Funciones adicionales por módulo

function resetModalRolForm(){
    $('#formRol').get(0).reset();
    $('#formRol #id_cargo').val("");
    $('#formRol #id_directorio').val("");
}
