/*=====================================

    # Nombre:
        metasMesCSV.js

    # Módulo:
        expediente/caratula

    # Descripción:
        Funciones para subir archivos CSV

=====================================*/

var metasMesCSV = {};

(function(context){

context.init = function(){
	document.getElementById('archivo-componente-csv').addEventListener('change', upload, false);
	document.getElementById('archivo-actividad-csv').addEventListener('change', upload, false);
};

function browserSupportFileUpload() {
    var isCompatible = false;
    if (window.File && window.FileReader && window.FileList && window.Blob) {
    isCompatible = true;
    }
    return isCompatible;
}

// Method that reads and processes the selected file
function upload(evt) {
    try{
        var texto_resultado = '';
        var identificador = $(this).attr('data-identificador');
        //var field_id = evt.srcElement.id;
        //var identificador = $('#'+field_id).attr('data-identificador');
        
        if (!browserSupportFileUpload()) {
            texto_resultado = '<span class="alert text-danger">Este navegador no tiene soporte para cargar archivos.</span>';
            $('#mensajes-'+identificador+'-importar-csv').html(texto_resultado);
        } else {
            var meses = {'ENE':1,'FEB':2,'MAR':3,'ABR':4,'MAY':5,'JUN':6,'JUL':7,'AGO':8,'SEP':9,'OCT':10,'NOV':11,'DIC':12};
            var data = null;
            var file = evt.target.files[0];
            var reader = new FileReader();
            
            reader.readAsText(file);
            reader.onload = function(event) {
                $('#desgloce_'+identificador+' input.metas-mes').val('');
                var csvData = event.target.result;
                data = $.csv.toObjects(csvData);
                if (data && data.length > 0) {
                    for(var i in data){
                        var element = data[i];
                        for(var j in meses){
                            var value = parseFloat(element[j]);
                            value = +value.toFixed(2);
                            $('#mes-'+identificador+'-'+element['JURISDICCION']+'-'+meses[j]).val(value);
                        }
                    }
                    texto_resultado += '<span class="alert text-success">Se importaron satisfactoriamente ' + data.length + ' lineas del archivo.</span>';
                } else {
                    texto_resultado += '<span class="alert text-danger">Ocurrió un error al intentar importar el archivo.</span>';
                }
                $('#mes-'+identificador+'-OC-1').change();
                $('#mes-'+identificador+'-OC-4').change();
                $('#mes-'+identificador+'-OC-7').change();
                $('#mes-'+identificador+'-OC-10').change();
                $('#mensajes-'+identificador+'-importar-csv').html(texto_resultado);
            };
            reader.onerror = function() {
                texto_resultado += '<span class="alert text-danger">No se puede leer el archivo ' + file.fileName + '.</span>';
                $('#mensajes-'+identificador+'-importar-csv').html(texto_resultado);
            };
        }
    }catch(err){
        texto_resultado = '<span class="alert text-danger">Ocurrio un error al intentar importar el archivo.<br> Mensaje: ';
        texto_resultado += '<small>'+err.message+'</small></span>'
        $('#mensajes-'+identificador+'-importar-csv').html(texto_resultado);
    }
}

})(metasMesCSV);