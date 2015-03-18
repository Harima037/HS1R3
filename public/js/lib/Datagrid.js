 /*=====================================

    # Nombre:
        Datagrid.js

    # Módulo:
        Todos

    # Descripción:
        Se utiliza para la manipulación de los eventos y creación de los datagrid 
        para poder visualizar la información en forma de lista, también manipula
        eliminación de registros directamente del recurso y hacer busquedas rápidas

=====================================*/


// Esta clase hay que instanciarla para poder manipular el datagrid
var Datagrid = function (pSelector, pSource, pParametros, pColumnas) {
	this.selector = pSelector || '';
	this.columnas = pColumnas || {};
	this.rxpag = 10;
	this.source = pSource || new RESTfulRequests('');
	this.parametros = pParametros || { formatogrid:true, pagina: 1};
	this.actualizar_default_callbacks = null;

	this.init = function(){
		// Inicializamos las propiedades del datagrid: paginacion etc.
		var context = this;

		$(this.selector + " .btn-next-rows").off('click');
		$(this.selector + " .btn-next-rows").on('click', function(e) {
			e.preventDefault();	
			var pagina  = parseInt(context.getPagina())+1;
 
		    if(context.getMaxPagina()>=pagina){
		     context.setPagina(pagina);
		     context.cambiarPagina();
		    }
        });

        $(this.selector + " .btn-back-rows").off('click');
		$(this.selector + " .btn-back-rows").on('click',function(e){
			e.preventDefault();	
           
			var pagina  = parseInt(context.getPagina())-1;
		    if(pagina<1)
		      pagina = 1;

		    context.setPagina(pagina);		   
		    context.cambiarPagina();
		});

		$(this.selector + " .btn-go-first-rows").off('click');
		$(this.selector + " .btn-go-first-rows").on('click',function(e){
			e.preventDefault();	
			context.setPagina(1);
		    context.cambiarPagina();
		});

		$(this.selector + " .btn-go-last-rows").off('click');
		$(this.selector + " .btn-go-last-rows").on('click',function(e){
			e.preventDefault();	
			context.setPagina(context.getMaxPagina());
		    context.cambiarPagina();
		});

		$(this.selector + " .txt-go-page").off('keydown');
		$(this.selector + " .txt-go-page").on('keydown', function(event){
			if (event.which == 13) {
				context.cambiarPagina();
		   	}
		});

		$(this.selector + " .txt-quick-search").off('keydown');
		$(this.selector + " .txt-quick-search").on('keydown', function(event){
			if (event.which == 13) {
				context.quickSearch($(this).val());	
		   	}
		});
		
		$(this.selector + " .btn-quick-search").off('click');
		$(this.selector + " .btn-quick-search").on('click',function(e){
			e.preventDefault();		
			context.quickSearch($(context.selector + " .txt-quick-search").val());			
		});

		$(this.selector + " tbody").off('dblclick');
		$(this.selector + " tbody").on('dblclick','tr td:not(.disabled)', function (e) {
	        if ($(this).index() != 0){
	            //var data = $(this).parents(".datagrid").data("edit-row");
	            var data = $(this).closest(".datagrid").data("edit-row");
	            if (data !== null) {
	                var funcion = window[data];
	                funcion($(this).parent().data("id"));
	            }
	        }
		});

		$(this.selector + " .btn-edit-rows").off('click');
		$(this.selector + " .btn-edit-rows").on('click', function (e) {
			e.preventDefault();
	      	
			var data = $(this).closest(".datagrid").data("edit-row");
			//var data = $(this.selector).data("edit-row");
			if (data != null) {
				var funcion = window[data], contador = 0, row_id = null;
				$(this).closest(".datagrid").find("tbody").find("input[type=checkbox]:checked").each(function () {
				//$(this.selector).find("tbody").find("input[type=checkbox]:checked").each(function () {
					contador++;
					row_id = $(this).parent().parent().data("id");
				});

				if (contador == 1) {
					funcion(row_id);
				} else {
					if (contador != 0) {
						MessageManager.show({data:'Solo puedes editar un registro a la vez.',type:'ADV',timer:3});	                   
	                }else {
	                	MessageManager.show({data:'Seleccione un registro.',type:'ADV',timer:3});	
	                }					
				}
			} else {
				MessageManager.show({data:'No hay nada que hacer.',type:'INF',timer:3});	
			}
		});

		$(this.selector + " .check-select-all-rows").off('click');
		$(this.selector + " .check-select-all-rows").on('click', function (e) {
	        if ($(this).is(':checked')) {
			  //$(this).parents(".datagrid").find("tbody").find("input[type=checkbox]").prop('checked', true);
			  $(this).closest(".datagrid").find("tbody").find("input[type=checkbox]").prop('checked', true);
	        } else {
	            //$(this).parents(".datagrid").find("tbody").find("input[type=checkbox]").prop('checked', false);
	            $(this).closest(".datagrid").find("tbody").find("input[type=checkbox]").prop('checked', false);
	        }
		});

		$(this.selector + " .btn-delete-rows").off('click');
		$(this.selector + " .btn-delete-rows").on('click',function(e){
			e.preventDefault();
			
			var rows = [];
			var contador= 0;
            
            $(this).parents(".datagrid").find("tbody").find("input[type=checkbox]:checked").each(function () {
				contador++;
                rows.push($(this).parent().parent().data("id"));
			});      
			

			if(contador>0){
				Confirm.show({
    					titulo:"Eliminar registros",
    					//botones:[], 
    					mensaje: "¿Estás seguro que deseas eliminar los registros seleccionados?",
    					//si: 'Actualizar',
    					//no: 'No, gracias',
    					callback: function(){
    						context.source.delete(rows,{rows: rows},{
		                        _success: function(response){ context.actualizar(); },
		                        _error: function(jqXHR){ 
		                        	MessageManager.show(jqXHR.responseJSON);
		                        }
		        			});
    					}
    			});
			
			}else{
				MessageManager.show({data:'No has seleccionado ningún registro.',type:'ADV',timer:3});
			}
		});

	}

	this.actualizar = function(callbacks){
		var parametros = this.parametros;
		var fnCallbacks = null;
		var context = this;

		if(callbacks!= null){

			fnCallbacks = {
                        _error: callbacks._error || function(jqXHR){
                        	var json = $.parseJSON(jqXHR.responseText);
							if(json.code == "W00"){
								context.limpiar();
								var colspan = $(context.selector + " thead > tr th").length;
								$(context.selector + " tbody").append("<tr><td colspan='"+colspan+"' style='text-align:left'><i class='fa fa-info-circle'></i> "+json.data+"</td></tr>");

							}else{
								MessageManager.show(json);
								context.limpiar();
							}
                        },                        
                        _success: callbacks._success || function(response){							
							context.limpiar();
							context.cargarDatos(response.data);							
                         	var total = parseInt(response.resultados/context.rxpag); 
                            var plus = parseInt(response.resultados)%context.rxpag;
                            if(plus>0) 
                                total++;
                            context.paginacion(total);
                        }
        	}
        	this.actualizar_default_callbacks = fnCallbacks;
		}else{
			if(!this.actualizar_default_callbacks){
				this.actualizar_default_callbacks = {
			        _error: function(jqXHR){
						//console.log('{ error => "'+jqXHR.status +' - '+ jqXHR.statusText +'", response => "'+ jqXHR.responseText +'" }'); 
						var json = $.parseJSON(jqXHR.responseText);
						if(json.code == "W00"){
							context.limpiar();
							var colspan = $(context.selector + " thead > tr th").length;
							$(context.selector + " tbody").append("<tr><td colspan='"+colspan+"' style='text-align:left'><i class='fa fa-info-circle'></i> "+json.data+"</td></tr>");

						}else{
							json.type = 'ERR';
							MessageManager.show(json);
							context.limpiar();
						}
						
			        },  
			        _success: function(response){
						context.limpiar();
						context.cargarDatos(response.data);

			         	var total = parseInt(response.resultados/context.rxpag); 
			            var plus = parseInt(response.resultados)%context.rxpag;
			            if(plus>0) 
			                total++;
			            context.paginacion(total);
			        }
			    };
			}
			fnCallbacks = this.actualizar_default_callbacks;
			/*{
                _error: function(jqXHR){
					//console.log('{ error => "'+jqXHR.status +' - '+ jqXHR.statusText +'", response => "'+ jqXHR.responseText +'" }'); 
					var json = $.parseJSON(jqXHR.responseText);
					if(json.code == "W00"){
						context.limpiar();
						var colspan = $(context.selector + " thead > tr th").length;
						$(context.selector + " tbody").append("<tr><td colspan='"+colspan+"' style='text-align:left'><i class='fa fa-info-circle'></i> "+json.data+"</td></tr>");

					}else{
						json.type = 'ERR';
						MessageManager.show(json);
						context.limpiar();
					}
					
                },  
                _success: function(response){
					context.limpiar();
					context.cargarDatos(response.data);

                 	var total = parseInt(response.resultados/context.rxpag); 
                    var plus = parseInt(response.resultados)%context.rxpag;
                    if(plus>0) 
                        total++;
                    context.paginacion(total);
                }
            }*/
		}
		
		parametros.formatogrid = true;
		parametros.pagina = this.getPagina();
		this.source.get(null,parametros,fnCallbacks);
	}
	this.limpiar = function (){
		$(this.selector + " tbody").empty();
	}
	this.cargarDatos = function(objJSON){
		//alert(objJSON[0]['fecha_inicio']);
		if(objJSON.length>0){
			for(var i=0; i<objJSON.length; i++){
				var row = "<tr>";
				if(objJSON[i].data_obj!=null){
					row = "<tr ";
					for(var key in objJSON[i].data_obj){
						row += ' data-'+key+'="'+objJSON[i].data_obj[key]+'"';					
					}
					row += '><td><input type="checkbox"></td>';					
				}else{
					row = 	'<tr data-id="'+objJSON[i].id+'"><td><input type="checkbox"></td>';
				}			
				
				for(var key in objJSON[i]){
					if(key!="id" && key!="data_obj"){
						if (objJSON[i].hasOwnProperty(key)) {
							////  modificacion del modulo de periodo
								//if(objJSON[i][key]=="fecha_inicio")
					        row += '<td>'+objJSON[i][key]+"</td>";
					    }
					}					
				}
				row += 	'</tr>'; 
				$(this.selector + " tbody").append(row);
                
			}
		}else{
			MessageManager.show({data:"No hay datos que cargar",type:'INF',timer:3});	
		}
	}
	this.actualizarFila = function(e,objJSON){		
		$(this.selector+" tbody").find("tr").each(function(){
			
			if($(this).data("id")==e){	
				if(objJSON.data_obj!=null){
					for(var key in objJSON.data_obj){
						$(this).data(key,objJSON.data_obj[key]);									
					}
					var row = "";
					for(var key in objJSON){
						if(key!="id" && key!="data_obj"){
							row += "<td>"+objJSON[key]+"</td>";
						}									
					}
					$(this).html(row);				
				}	
			}						
		});	

	}	
	this.borrarFila = function(){
		$(this.selector + " tbody").find("input[type=checkbox]:checked").parent().parent().remove();
	}
	this.paginacion = function(total){
		$(this.selector + " .btn-total-paginas").html('de '+total);
		$(this.selector + " .btn-total-paginas").data('pages',total);
	}
	this.getPagina = function(){
		var pagina =  parseInt($(this.selector + " .txt-go-page").val());

		if(isNaN(pagina)){
			return 0;
		}
		return $(this.selector + " .txt-go-page").val()	
	}
	this.setPagina = function(e){
		$(this.selector + " .txt-go-page").val(e)	
	}
	this.getMaxPagina = function(){
		return $(this.selector + " .btn-total-paginas").data('pages');
	}
	this.cambiarPagina = function(){

		var pagina  = parseInt(this.getPagina());

		if(this.getMaxPagina()<pagina && this.getMaxPagina()!=0){
			pagina = this.getMaxPagina();
		}else  if(pagina<=0){
			pagina = 1;
		}

		this.setPagina(pagina);

		if($(this.selector + " .txt-quick-search").val()==""){
			//actualizarDatagridProyectos();
			this.actualizar();
		}else{
			this.quickSearch($(this.selector + " .txt-quick-search").val(),pagina);
		}

	}
	this.getUniqueSeleccion = function(){
		var contador = 0;
		var row_id = null;
		$(this.selector).find("input[type=checkbox]:checked").each(function(){
			contador++;
			row_id = $(this).parent().parent().data("id");
		});

		if(contador==1){
			return row_id;
		}else{
			return false;
		}
	}
	this.getSeleccion = function(){
		var contador = 0;
		var rows= [];
		$(this.selector).find("input[type=checkbox]:checked").each(function(){
			contador++;
			row.push = $(this).parent().parent().data("id");
		});

		return rows;
	}
	this.quickSearch = function(e,pagina){
		var pag = pagina || 1;
		if(pag==1)
		   this.setPagina(1);
		this.parametros.buscar = e;
		this.actualizar();
	}
	this.verificarExistenciaRegistro = function(_datakey,_valor, _excludekey){
		var contador = 0;
		var excluir = _excludekey || null;

		$(this.selector+" tbody").find("tr").each(function(){
			//console.log(_datakey+":"+$(this).data(_datakey))
			if($(this).data(_datakey)==_valor && $(this).data(_datakey) != _excludekey){				
				contador++;
			}						
		});
		if(contador>0)
			return true;
		else
			return false;
	}
	
	this.initBootstrapValidator = function(formSelector,validFields,btnSelector){
		btnSelector = btnSelector || 'button[class*="btn-guardar"]';
		validFields = validFields || {};

		$(formSelector).bootstrapValidator({
		    live:'submitted',
		    trigger:'blur',
		    submitButtons:btnSelector,
		    fields: validFields
		});
	}

	this.isFormValid = function(formSelector){
		$(formSelector).data('bootstrapValidator').validate();
		return $(formSelector).data('bootstrapValidator').isValid();
	}
/*
	this.cleanFormErrors = function(formName,reset){
		if(reset){
			$(formName).data('bootstrapValidator').resetForm(true);
		}
    	$(formName+' .help-block.server-error-msg').remove();
	}*/
}

