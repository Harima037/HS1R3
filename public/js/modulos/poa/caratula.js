
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

//***********************     Funcionalidad ejecutada al cargar la página    ************************************
if($('#id').val()){
	$('#tablink-componentes').attr('data-toggle','tab');
	$('#tablink-componentes').parent().removeClass('disabled');
	//load data
	proyectoResource.get($('#id').val(),null,{
        _success: function(response){
            $('#nombretecnico').val(response.data.nombreTecnico);

            var clave = response.data.unidadResponsable + response.data.finalidad + response.data.funcion + response.data.subFuncion +
                        response.data.subSubFuncion + response.data.programaSectorial + response.data.programaPresupuestario +
                        response.data.programaEspecial + response.data.actividadInstitucional + response.data.proyectoEstrategico +
                        response.data.numeroProyectoEstrategico;

            //$('#lbl_clave_presupuestaria').text(clave);
			/*$('#unidadresponsable').val(response.data.datos_unidad_responsable.clave);
            $('#finalidad').val(response.data.datos_finalidad.clave + ' - ' + response.data.datos_finalidad.descripcion);
            $('#funcion').val(response.data.datos_funcion.clave );
            $('#subfuncion').val(response.data.datos_sub_funcion.clave );
            $('#subsubfuncion').val(response.data.datos_sub_sub_funcion.clave );
            $('#programasectorial').val(response.data.datos_programa_sectorial.clave );
            $('#programapresupuestario').val(response.data.datos_programa_presupuestario.clave );
            $('#programaespecial').val(response.data.datos_programa_especial.clave );
            $('#actividadinstitucional').val(response.data.datos_actividad_institucional.clave );
            $('#proyectoestrategico').val(response.data.datos_proyecto_estrategico.clave);*/

            $('#unidadresponsable').selectpicker('val',response.data.datos_unidad_responsable.clave);
            $('#funciongasto').selectpicker('val',response.data.datos_sub_sub_funcion.clave );
            $('#programasectorial').selectpicker('val',response.data.datos_programa_sectorial.clave );
            $('#programapresupuestario').selectpicker('val',response.data.datos_programa_presupuestario.clave );
            $('#programaespecial').selectpicker('val',response.data.datos_programa_especial.clave );
            $('#actividadinstitucional').selectpicker('val',response.data.datos_actividad_institucional.clave );
            $('#proyectoestrategico').selectpicker('val',response.data.datos_proyecto_estrategico.clave);

            $('#cobertura').selectpicker('val',response.data.cobertura.id);
            $('#tipoaccion').selectpicker('val',response.data.tipo_accion.id);

            $('#vinculacionped').selectpicker('val',response.data.objetivo_ped.id);

            $('#tipobeneficiario').selectpicker('val',response.data.tipo_beneficiario.id);
            $('#totalbeneficiarios').val(response.data.totalBeneficiarios);
            $('#totalbeneficiariosf').val(response.data.totalBeneficiariosF);
            $('#totalbeneficiariosm').val(response.data.totalBeneficiariosM);

            var indx;
            var sexo;
            for( indx in response.data.beneficiarios ){
                sexo = response.data.beneficiarios[indx].sexo;
                $('#urbana'+sexo).val(response.data.beneficiarios[indx].urbana);
                $('#rural'+sexo).val(response.data.beneficiarios[indx].rural);
                $('#mestiza'+sexo).val(response.data.beneficiarios[indx].mestiza);
                $('#indigena'+sexo).val(response.data.beneficiarios[indx].indigena);
                $('#inmigrante'+sexo).val(response.data.beneficiarios[indx].inmigrante);
                $('#otros'+sexo).val(response.data.beneficiarios[indx].otros);
                $('#muyalta'+sexo).val(response.data.beneficiarios[indx].muyAlta);
                $('#alta'+sexo).val(response.data.beneficiarios[indx].alta);
                $('#media'+sexo).val(response.data.beneficiarios[indx].media);
                $('#baja'+sexo).val(response.data.beneficiarios[indx].baja);
                $('#muybaja'+sexo).val(response.data.beneficiarios[indx].muyBaja);
            }
        }
    });
}

//***********************     Funcionalidad de botones y elementos del formulario ++++++++++++++++++++++++++++++++++++
$('#btn-proyecto-guardar').on('click',function(){

	var parametros = $(form_caratula).serialize();
	parametros = parametros + '&guardar=proyecto';
	
	if($('#id').val()){
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
	}
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

//***********************     Funciones             +++++++++++++++++++++++++++++++++
/** Actualiza el span correspondiente al select actualizado, para ir construyendo la Clave Presupuestaria **/
function actualiza_clave(id, clave, value){
	if(clave != ''){
		$('#'+id).text(clave);
	}else{
		$('#'+id).text(value);
	}
	
}