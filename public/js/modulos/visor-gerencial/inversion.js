/*=====================================

    # Nombre:
        inversion.js

    # Módulo:
        visor-gerencial/proyectos-inv

    # Descripción:
        Para seguimiento de metas de proyectos de inversión

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/visor-gerencial');
var moduloDatagrid = new Datagrid("#datagridProyectos",moduloResource,{ formatogrid:true, pagina: 1, clasificacionProyecto: 2});