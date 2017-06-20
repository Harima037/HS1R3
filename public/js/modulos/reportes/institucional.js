/*=====================================

    # Nombre:
        institucional.js

    # Módulo:
        seguimiento/rend-cuenta-inst

    # Descripción:
        Para seguimiento de metas de proyectos institucionales

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/reporte-seguimiento-inst');
var moduloDatagrid = new Datagrid("#datagridProyectos",moduloResource,{ formatogrid:true, pagina: 1, clasificacionProyecto: 1});
var moduloResourceProyecto= new RESTfulRequests(SERVER_HOST+'/v1/segui-proyectos-inst');