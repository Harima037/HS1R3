var Validation = {
	formValidate:function(json_data){
		if(!$.isArray(json_data)){
			temp = json_data;
			json_data = [];
			json_data[0] = temp;
		}
		for(i in json_data){
			var item = $.parseJSON(json_data[i]);
			this.printFieldsErrors(item.field,item.error);
			if(item.other){
				this.printFieldsErrors(item.other,item.error);
			}
		}
		
		$('input','.has-error').first().focus();
	},
	printFieldsErrors:function(form_field,error_message){
		var $field_parents = $('#'+form_field.replace(" ","_")).closest('div[class*="form-group"]'); //Se optiene el contenedor del campo
		if($field_parents.hasClass('has-success')){
			$field_parents.removeClass('has-success');
		}
		$field_parents.addClass('has-error');
		$field_parents.find('.server-error-msg').remove();
		$field_parents.append('<small class="help-block server-error-msg">'+error_message+'</small>');
	},
	cleanFormErrors: function(formName){
		$(formName+' .help-block.server-error-msg').remove();
		$(formName).find('.form-group').removeClass('has-error');
	},
	cleanFieldErrors:function(form_field){
		$('#'+form_field).parents('div[class*="form-group"]').find('.help-block.server-error-msg').remove();
		$('#'+form_field).parents('div[class*="form-group"]').removeClass('has-error');
	}
}