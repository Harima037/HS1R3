/*=====================================

    # Nombre:
        inversion.js

    # Módulo:
        seguimiento/seguimiento-inv

    # Descripción:
        Para seguimiento de metas de proyectos de inversión

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/seguimiento');
var moduloDatagrid = new Datagrid("#datagridProyectos",moduloResource,{ formatogrid:true, pagina: 1, clasificacionProyecto: 2});
moduloDatagrid.init();
moduloDatagrid.actualizar();