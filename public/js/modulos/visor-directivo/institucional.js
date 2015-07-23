/*=====================================

    # Nombre:
        institucional.js

    # Módulo:
        visor-directivo/proyectos-inst

    # Descripción:
        Para seguimiento de metas de proyectos institucionales

=====================================*/

// Inicialización General para casi cualquier módulo
var moduloResource = new RESTfulRequests(SERVER_HOST+'/v1/visor-directivo');
var moduloDatagrid = new Datagrid("#datagridProyectos",moduloResource,{ formatogrid:true, pagina: 1, clasificacionProyecto: 1});