
/*=====================================

    # Nombre:
        caratula.js

    # Módulo:
        poa/caratula

    # Descripción:
        Para el formulario de captura (Caratula de captura) de un proyecto

=====================================*/
// Declaracion de variables
var proyectoResource = new RESTfulRequests(SERVER_HOST+'/v1/proyectos');
var componenteDatagrid = new Datagrid("#datagridComponentes",proyectoResource);
var modal_componente = '#modalComponente';
var modal_actividad = '#modalActividad';
var grid_componentes = '#datagridComponentes';
var form_caratula = '#form_caratula';

//Funcionalidad ejecutada al cargar la página
if($('#id').val()){
	$('#tablink-componentes').attr('data-toggle','tab');
	$('#tablink-componentes').parent().removeClass('disabled');
}

//Funcionalidad de botones y elementos del formulario
$('#btn-proyecto-guardar').on('click',function(){

	var parametros = $(form_caratula).serialize();
	parametros = parametros + '&guardar=proyecto';
	
	proyectoResource.post(parametros,{
        _success: function(response){
            MessageManager.show({data:'Datos del proyecto almacenados con éxito',type:'OK',timer:3});
            $(form_caratula + ' #id').val(response.data.id);
            
            $('#tablink-componentes').attr('data-toggle','tab');
			$('#tablink-componentes').parent().removeClass('disabled');
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
    });
});


/** Botones para mostrar los modales de Componente y Actividad **/
$('.btn-agregar-actividad').on('click',function(){
	$(modal_actividad).find(".modal-title").html("Nueva Actividad");
	$(modal_actividad).modal('show');
});
$('.btn-agregar-componente').on('click',function(){
	$(modal_componente).find(".modal-title").html("Nuevo Componente");
	$(modal_componente).modal('show');
});

/** Selects para actualizar la Clave Presupuestaria **/
$('#unidadresponsable').on('change',function(){
	actualiza_clave('unidad_responsable',$(this).val(),'--');
});
$('#funciongasto').on('change',function(){
	var funcion_gasto = ['','','',''];

	if($(this).val() != ''){
		 funcion_gasto = $(this).val().split('.');
	}
	
	actualiza_clave('finalidad', funcion_gasto[0],'-');
	actualiza_clave('funcion', funcion_gasto[1],'-');
	actualiza_clave('subfuncion', funcion_gasto[2],'-');
	actualiza_clave('subsubfuncion', funcion_gasto[3],'-');

});
$('#programasectorial').on('change',function(){
	actualiza_clave('programa_sectorial',$(this).val(),'-');
});
$('#programapresupuestario').on('change',function(){
	actualiza_clave('programa_presupuestario',$(this).val(),'---');
});
$('#programaespecial').on('change',function(){
	actualiza_clave('programa_especial',$(this).val(),'---');
});
$('#actividadinstitucional').on('change',function(){
	actualiza_clave('actividad_institucional',$(this).val(),'---');
});
$('#proyectoestrategico').on('change',function(){
	actualiza_clave('proyecto_estrategico',$(this).val(),'-');
});

//Funciones
/** Actualiza el span correspondiente al select actualizado, para ir construyendo la Clave Presupuestaria **/
function actualiza_clave(id, clave, value){
	if(clave != ''){
		$('#'+id).text(clave);
	}else{
		$('#'+id).text(value);
	}
	
}