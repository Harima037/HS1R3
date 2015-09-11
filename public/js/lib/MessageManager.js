/*=====================================

    # Nombre:
        messageManager.js

    # Módulo:
        Todos

    # Descripción:
        Se utiliza para el manejo de los mensajes de error, warnings y mensajes informativos 
        utilizando las clases "alert" de bootstrap

=====================================*/

var CatalogoErrores = {
	'U00': {tipo:'ERR', titulo:'Validación de Campos'},
	'U01': {tipo:'ERR', titulo:'Permisos no Válidos'},
	'U02': {tipo:'ERR', titulo:'Error de inicio de sesión'},
	'U03': {tipo:'ERR', titulo:'Sesión no válida'},
	'U04': {tipo:'ERR', titulo:'Tiempo de espera agotado'},
	'U05': {tipo:'ERR', titulo:'Error de conexión de red'},
	'U06': {tipo:'ERR', titulo:'El recurso no existe'},

	'S00': {tipo:'ERR', titulo:'Error en la conexión con la base de datos'},
	'S01': {tipo:'ERR', titulo:'Error en la consulta la base de datos'},
	'S02': {tipo:'ERR', titulo:'Error en tiempo de ejecución'},
	'S03': {tipo:'ERR', titulo:'Error del servidor'},
	'S04': {tipo:'ERR', titulo:'Error en el envío de Email'},

	'W00': {tipo:'ADV', titulo:'No hay datos'},
	'W01': {tipo:'ADV', titulo:'Usuario bloqueado'},
	'W02': {tipo:'ADV', titulo:'Usuario suspendido'}
}

var MessageManager = {
	ERROR:'ERR', ADVERTENCIA:'ADV',INFO:'INF',
	show:function(params){
		var data = params.data || '';
		var code = params.code || null;
		var type = params.type || 'INF';
		var timer = params.timer || null;
		var container = params.container || null;

		if(type=="ERR" && code == null){
			
			code = 'S03';
		}

		if(code == 'U00'){
			//this.setFormValidation(data);
			data = "Por favor, verifica tus datos.";
			timer = 4;
		}
		var item = {};
		if(code){
			item = CatalogoErrores[code];
			item.mensaje = '<b>'+code+' :</b> ';
			if($.isArray(data)){
				var mensaje = "";
				for(i in data ){
					mensaje += data[i] + "<br>";
				}
				item.mensaje += mensaje;
			}else{
				if(data !== ''){
					item.mensaje += data;
				}else{
					item.mensaje += item.titulo;
				}
			}
		}else{
			item = {tipo:type};
			item.mensaje = data;
		}
		
		this.printAlert(item, container);

		if(timer){
			if(this.container==null){
				setTimeout(function() { $('.alert',container).alert('close'); }, (timer * 1000));
			}
			else{
				setTimeout(function() { $('.alert','.alert-container').alert('close'); }, (timer * 1000));		
			}
		}
			
	},
	printAlert:function(item, container){
		var alert_class;

		switch(item.tipo){
			case 'ERR': alert_class = 'alert-danger'; break;
			case 'ADV': alert_class = 'alert-warning'; break;
			case 'INF': alert_class = "alert-info"; break;
			default: alert_class = 'alert-success'; break; //En caso de no ser error o advertencia se toma como un mensaje informativo
		}
		if(item.mensaje==""){
			item.mensaje = "No se ha especificado un mensaje para esta notificación. Verifique si hay un problema, de lo contrario haga caso omiso.";
		}
		
		var alert_html ='<div class="alert '+alert_class+' alert-dismissible fade in" rol="alert">'+
						'<button type="button" class="close" data-dismiss="alert">'+
						'<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>'+
						'</button><p>' + item.mensaje + '</p></div>';
		if(container==null){
			if(!$('.alert-container').length){
				$('body').prepend('<div class="alert-container"></div>');
			}
			$('.alert-container').empty();		
			$('.alert-container').append(alert_html);
		}else{
			try{
				// Borramos cualquier alert en el contenedor
				$(container+' .alert').remove();
				$(container).prepend(alert_html);
			}catch(e){ 	console.log(e);	}
			
			//console.log($(container));
		}
		
	},
	dismissAlert: function(container){
		$(container+' .alert').remove();
	}/*,
	setFormValidation:function(json_data){
		if(!$.isArray(json_data)){
			temp = json_data;
			json_data = [];
			json_data[0] = temp;
		}
		
		for(i in json_data){
			var item = $.parseJSON(json_data[i]);

			this.setFieldValidation(item.field,item.error);

			if(item.other){
				this.setFieldValidation(item.other,item.error);
			}
		}
		
		$('input','.has-error').first().focus();
	},
	setFieldValidation:function(form_field,error_message){
		console.log(form_field)
		field_parents = $('#'+form_field.replace(" ","_")).parents('div[class*="form-group"]'); //Se optiene el contenedor del campo
		if(field_parents.hasClass('has-success')){
			field_parents.removeClass('has-success');
		}
		field_parents.addClass('has-error');
		field_parents.append('<small class="help-block server-error-msg">'+error_message+'</small>');
	}*/
}