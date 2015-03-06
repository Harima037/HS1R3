<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title-page')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <!-- Bootstrap -->
    @section('css')
    <link href="{{ URL::to('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" media="screen">
    <link href="{{ URL::to('css/general.css') }}" rel="stylesheet" media="screen">    
    <link href="{{ URL::to('css/font-awesome-4.3.0/css/font-awesome.min.css') }}" rel="stylesheet" media="screen">
    <link rel="shortcut icon" href="{{ URL::to('img/favicon.ico') }}">
    @show
</head>
<body style="margin-top:120px;">
    <div id="loading">
    <div id="loading-icon">
            <img src="{{ URL::to('img/loading.gif') }}" alt="Cargando">	
        </div>
    </div>
    @section('mainmenu')
    @include('layouts.MainMenu')
    @show
    <div class="page-container">
    <div id="mainAppContainer" class="container" >
        @yield('content')
    </div> 
    </div>   
    <footer class="container">
        <div  style="text-align:right;"> 
            <img src="{{ URL::to('img/LogoInstitucional.png') }}" alt="" height="35px"> 
            <img src="{{ URL::to('img/EscudoGobiernoChiapas.png') }}" alt="" height="35px"> 
            <img src="{{ URL::to('img/Marca.png') }}" alt="" height="35px">
        </div>
    </footer>
    
    <!-- Ventanas Modales    -->    
    @section('modals')
    
    <div class="modal fade" id="modalAcercaDe" tabindex="-1" role="dialog" aria-labelledby="modalAcercaDeLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content ">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modalAcercaDeLabel">Acerca de...</h4>
                </div>
                <div class="modal-body">
                    <h2>POA 1.0</h2>
                    <p>Plataforma de administración del POA</p>
                    <p><small> <strong>Instituto de Salud</strong> Tuxtla Gutiérrez, 2014 &copy;. Desarrollado por: Área de Informática</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <div class="modal fade" id="modalAyuda" tabindex="-1" role="dialog" aria-labelledby="modalAyudaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content ">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modalAyudaLabel">Ayuda</h4>
                </div>
                <div class="modal-body">
                    <p>Para cualquier duda o aclaración, comuníquese al Área de Informática:</p>
                    <ul>
                        <li>Vía telefónica: <a href="tel:+529616189250">961 618 9250</a> ext. 44219</li>
                        <li>Vía e-mail: <a href="mailto:hugo.gutierrez@salud.chiapas.gob.mx">hugo.gutierrez@salud.chiapas.gob.mx</a>.</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <div class="modal fade" id="modalNotificaciones" tabindex="-1" role="dialog" aria-labelledby="modalNotificacionesLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content ">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modalNotificacionesLabel">Notificaciones</h4>
                </div>
                <div class="modal-body">
                <p>No hay Notificaciones</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    @show
    
    @section('js')
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="{{ URL::to('js/dependencias/jquery-1.10.2.min.js') }}"></script>
    <script src="{{ URL::to('bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ URL::to('js/lib/MessageManager.js') }}"></script>
    <script type="text/javascript">var SERVER_HOST='{{ URL::to("/") }}';</script>
    <script src="{{ URL::to('js/lib/RESTfulRequests.js') }}"></script>
    <script src="{{ URL::to('js/lib/Datagrid.js') }}"></script>
    @show
</body>
</html>