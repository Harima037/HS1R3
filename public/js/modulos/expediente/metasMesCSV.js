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
    if (!browserSupportFileUpload()) {
        alert('The File APIs are not fully supported in this browser!');
    } else {
    	var meses = {'ENE':1,'FEB':2,'MAR':3,'ABR':4,'MAY':5,'JUN':6,'JUL':7,'AGO':8,'SEP':9,'OCT':10,'NOV':11,'DIC':12};
    	var field_id = evt.srcElement.id;
    	var identificador = $('#'+field_id).attr('data-identificador');
        var data = null;
        var file = evt.target.files[0];
        var reader = new FileReader();
        reader.readAsText(file);
        reader.onload = function(event) {
            var csvData = event.target.result;
            data = $.csv.toObjects(csvData);
            if (data && data.length > 0) {
            	for(var i in data){
            		var element = data[i];
            		for(var j in meses){
            			$('#mes-'+identificador+'-'+element['JURISDICCION']+'-'+meses[j]).val(element[j]);
            		}
            	}
              	console.log('Imported -' + data.length + '- rows successfully!');
            } else {
                console.log('No data to import!');
            }
            console.log(data);
        };
        reader.onerror = function() {
            alert('Unable to read ' + file.fileName);
        };
    }
}

})(metasMesCSV);