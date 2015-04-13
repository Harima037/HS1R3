/*=====================================

    # Nombre:
        catalogo-permisos.js

    # Módulo:
        administrador/catalogo-permisos

    # Descripción:
        Se utiliza para seleccionar los permisos a asignar al modulo o usuario, devuelve json con el arreglo de permisos

=====================================*/

var catalogoPermisosResource = new RESTfulRequests(SERVER_HOST+'/v1/permisos');
var icons = {'C':'fa fa-plus','R':'fa fa-eye','U':'fa fa-pencil','D':'fa fa-trash-o','S':'fa fa-sliders'};


$(document).ready(function(){
    $('#permissions-help').click(function(){
        $(this).focus();
    })
   $('#permissions-help').popover({html:true,trigger:'focus'});
  
});

function cargarCatalogoPermisos(response){

    cleanQuickSearchInput();
	var html = generarHTML(response.data.modulos);


    $('#table-list-permission tbody').empty();
    $('#table-list-permission tbody').append(html);
    $('input[type="checkbox"]','#table-list-permission').change(function(){
        if($(this).prop('checked')){
            $(this).parent().removeClass('btn-danger');
            $(this).parent().removeClass('btn-default');
            if($(this).data('rol'))
                $(this).parent().addClass('btn-info');
            else
                $(this).parent().addClass('btn-success');
        }else{
            $(this).parent().removeClass('btn-info');
            $(this).parent().removeClass('btn-default');
            $(this).parent().removeClass('btn-success');

            if($(this).data('rol'))
                $(this).parent().addClass('btn-danger');
            else
                $(this).parent().addClass('btn-default');
        }         
        
    });
    selectChecks(response.data.rol,'rol');
    selectChecks(response.data.user,'user');
    
}

$('#modalCatalogoPermisos .txt-quick-search').change(function(){
    var filter = ($(this).val().trim()).toUpperCase();
    if(filter.length > 0){
        if(!$('#modalCatalogoPermisos .cleanButton').length){
            $('<button type="button" onclick="cleanQuickSearchInput();" class="btn btn-default cleanButton"><span class="fa fa-eraser"></span></button>').insertBefore($('#modalCatalogoPermisos .btn-quick-search'));
        }
        $("#table-list-permission > tbody > tr").each(function(){
            var grupoText = $("td",this).eq(0).text();
            var moduloText = $("td",this).eq(1).text();
            if(grupoText.indexOf(filter) == 0 || moduloText.indexOf(filter) == 0){
                $(this).slideDown();
            }else{
                $(this).slideUp();
            }
        });
    }else{
        if($('#modalCatalogoPermisos .cleanButton').length){
            $('#modalCatalogoPermisos .cleanButton').remove();
        }
        $("#table-list-permission > tbody > tr").each(function(){
            $(this).slideDown()
        });
    }
}).keyup(function(){
    $(this).change();
});



function parsePermisos(){
    var permisos = {};
    $('input[type="checkbox"]','#formPermisos').each(function(){
        if(!$(this).data('rol')){
            var keys = $(this).attr('id').split('.');       

            if(!permisos[keys[0]]){
                permisos[keys[0]] = {}   
            }
            if(!permisos[keys[0]][keys[1]]){
                permisos[keys[0]][keys[1]] = {}   
            }
            if(!permisos[keys[0]][keys[1]][keys[2]]){
                permisos[keys[0]][keys[1]][keys[2]] = {}   
            }

            if($(this).prop('checked'))
                permisos[keys[0]][keys[1]][keys[2]] = 1;
            else
                permisos[keys[0]][keys[1]][keys[2]] = 0;

        }else{
           var keys = $(this).attr('id').split('.');       
            if(!$(this).prop('checked')){

                if(!permisos[keys[0]]){
                    permisos[keys[0]] = {}   
                }
                if(!permisos[keys[0]][keys[1]]){
                    permisos[keys[0]][keys[1]] = {}   
                }
                if(!permisos[keys[0]][keys[1]][keys[2]]){
                    permisos[keys[0]][keys[1]][keys[2]] = {}   
                }

           
                permisos[keys[0]][keys[1]][keys[2]] = -1;
            }
            
        }
    });
    
    //quitar todos los permisos individuales vacíos
    for(i in permisos){
        var bandera_j = false;
        for(j in permisos[i]){            
            var bandera_i = false;
            for(k in permisos[i][j]){
                if(permisos[i][j][k]!=0){
                    bandera_i = true;                
                }
            }
            if(bandera_i){
                bandera_j = true;
            }
            else{
                delete permisos[i][j];
            }
        }
        if(!bandera_j){
            delete permisos[i];
        }
    }
    return permisos;
}

function selectChecks(seleccion,tipo){ 
    for(i in seleccion){
       
        for(j in seleccion[i]){            
            
            for(k in seleccion[i][j]){            
                if(tipo=='rol'){
                    $('input[name="permisos['+i+"."+j+"."+k+']"]','#formPermisos').data('rol','true');   
                    $('input[name="permisos['+i+"."+j+"."+k+']"]','#formPermisos').prop('checked',true);         
                }else{
              
                    if(seleccion[i][j][k]=='1'){
                        $('input[name="permisos['+i+"."+j+"."+k+']"]','#formPermisos').prop('checked',true);
                    }else{
                        $('input[name="permisos['+i+"."+j+"."+k+']"]','#formPermisos').prop('checked',false);
                    }
                }        
                $('input[name="permisos['+i+"."+j+"."+k+']"]','#formPermisos').change();                     
            }
        }
    }
}

function generarHTML(modulos){
    var html_rows = '';
    if(modulos){            
        for(i in modulos){
            if(modulos[i]){   
                html_rows += '<tr><th colspan="3">'+i+'</th></tr>'            ;
                for(j in modulos[i]){
                    html_rows += '<tr><td></td><td>'+j+'</td>';
                    html_rows += '<td>';
                    html_rows += '<div class="btn-group">'
                    
                    for(k in modulos[i][j]){  
                        id = i+'.'+j+'.'+k;
                        html_rows += '<label class="btn btn-default ">';
                        html_rows += '<span class="'+icons[k]+'"></span>'
                        html_rows += '<input type="checkbox" id="'+id+'" class="hidden" name="permisos['+id+']" value="1" />';
                        html_rows += '</label>';
                    }
                    html_rows += '</div>';
                    html_rows += '</td>';
                    html_rows += '</tr>';
                }                
            }           
        }
    }else{
        html_rows += '<tr><td><b>'+i+'</b></td><td colspan="2"></td></tr>';
    }
    return html_rows;
}

function cleanQuickSearchInput(){
    $('#modalCatalogoPermisos .txt-quick-search').val('');
    $('#modalCatalogoPermisos .txt-quick-search').change();
}