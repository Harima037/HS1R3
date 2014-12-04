/*=====================================

    # Nombre:
        Confirm.js

    # Módulo:
        Todos

    # Descripción:
        Sustituye la función confirm de javascript agregando más opciones de personalización.

=====================================*/
var Confirm = {
	/* Opciones default */
	selector: 'modal-confirm',
	opciones: {
				titulo:"Confirmación",
				mensaje:"¿Está seguro de realizar esta acción?",
				focus: "btn-modal-confirm-si",
				callback: function(){ },				
				callbackNo: function(){ },
				botones: [ 
					{ selector:"btn-modal-confirm-si", nombre: "Si", clase: 'btn-primary', callback: function(){ }},
					{ selector:"btn-modal-confirm-no", nombre: "No", clase: 'btn-default', callback: function(){ } }
				],
				
			},
	/* Método devuelve el maquetado de los botones y genera los eventos de los botones */
	initButtons: function(){
		var markup = '';
		for(i in this.opciones.botones){
			markup += '<button id="'+this.opciones.botones[i].selector+'" type="button" class="btn '+this.opciones.botones[i].clase+'" data-dismiss="modal">'+this.opciones.botones[i].nombre+'</button> ';				
			$(document).off("click","#"+this.opciones.botones[i].selector);
			$(document).on("click","#"+this.opciones.botones[i].selector,this.opciones.botones[i].callback);
		}
		return markup;
	},
	/* Método para ejecutar el confirm */
	show: function(parametros){
		//Si envían el arreglo de botones vacíos, preevenimos la sobreescritura
		if (typeof parametros.botones === 'undefined' || parametros.botones.length==0 ) {
			delete parametros.botones;
		}

		this.opciones = $.extend(this.opciones,parametros || {});

		// Si no se pasa ningun botón, inicializamos el callback para que reaccione al si|no
		if(parametros.botones == null){
			if(this.opciones.si!=null){
				this.opciones.botones[0].nombre = this.opciones.si;
			}
			if(this.opciones.no!=null){
				this.opciones.botones[1].nombre = this.opciones.no;
			}
			this.opciones.botones[0].callback = this.opciones.callback;
			this.opciones.botones[1].callback = this.opciones.callbackNo;
		}

		// Inicializamos los botones definidos por el usuario
		var markup_botones = this.initButtons();
		var markup = '<div class="modal fade" id="'+this.selector+'" tabindex="-1" role="dialog" aria-labelledby="modal-confirm-label" aria-hidden="true">'
					+	'<div class="modal-dialog">'
					+		'<div class="modal-content">'
					+			'<div class="modal-header">'
					+				'<h4 class="modal-title" id="modal-confirm-label">'+this.opciones.titulo+'</h4>'
					+			'</div>'
					+			'<div class="modal-body">'
					+				'<p>'+this.opciones.mensaje+'</p>'
					+			'</div>'
					+			'<div class="modal-footer">'
					+			markup_botones
					+			'</div>'
					+		'</div>'
					+	'</div>'
					+'</div>';

		$("div#"+this.selector).remove();
		var $modal_confirm = $(markup).appendTo('body');	

		//var $modal_confirm = $('> div#'+this.selector, 'body').length ? $('> div#'+this.selector, 'body') : $(markup).appendTo('body');		
		
		var context = this;
		$modal_confirm.on('shown.bs.modal', function () { $('#'+context.opciones.focus).focus();});
		$modal_confirm.modal('show');
	},
};