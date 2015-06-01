<!-- Fixed navbar -->     
<div  class="navbar navbar-default navbar-fixed-top" style="margin-top:51px">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#collapse-main-menu">
                <i class="fa fa-bars fa-lg"></i>
            </button>
        </div>
        <div class="navbar-collapse collapse" id="collapse-main-menu">
            <ul class="nav navbar-nav">  
                @if(isset($sys_sistemas))
                @foreach($sys_sistemas as $sistema)
                    @if($sistema->visible)
                        @if(Sentry::hasAnyAccess(SysGrupoModulo::getPermisos($sistema->id)))
                            @if($sys_activo)
                            <li class="dropdown @if($sistema->key===$sys_activo->key) {{ "active" }} @endif">
                            @else
                            <li class="dropdown">
                            @endif

                            @if(count($sistema->modulos)>0)
                            <a href="{{ URL::to($sistema->uri) }}" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa {{ $sistema->icono }}"></i> {{ $sistema->nombre }} <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu">                       
                            @foreach($sistema->modulos as $modulo)
                            @if(Sentry::hasAccess($sistema->key . '.' . $modulo->key . '.R') && $modulo->visible)
                                <li><a href="{{ URL::to($sistema->uri.'/'.$modulo->uri) }}"><i class="fa {{ $modulo->icono }}"></i> {{ $modulo->nombre }}</a></li>      
                            @endif
                            @endforeach
                            </ul>
                            @else
                            <a href="{{ URL::to($sistema->uri) }}"><i class="fa {{ $sistema->icono }}"></i> {{ $sistema->nombre }}</a>
                            @endif
                            </li>
                        @endif
                    @endif
                   
                   @endforeach
                @endif                   
            </ul>  
        </div><!--/.nav-collapse -->
    </div>
</div> 
<div class="navbar navbar-default navbar-fixed-top" style="background:#FFF;">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#collapse-top-main-menu">
                <i class="fa fa-th-large fa-lg"></i>
            </button> 
            <a class="navbar-brand" href="{{ URL::to('/') }}">
                <img src="{{ URL::to('img/imgApple/icono_57x57.png') }}" alt="" height="30px" style="border-radius:5px;"> SIRE
            </a>
        </div>
        @if(isset($usuario))
        <div class="navbar-collapse collapse" id="collapse-top-main-menu">
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="{{ URL::to('/') }}" class="hidden-xs"><i class="fa fa-dashboard fa-lg"></i></a>
                    <a href="{{ URL::to('/') }}" class="visible-xs"><i class="fa fa-dashboard"></i>&nbsp;Dashboard</a>
                </li>
                
                <li id="notificaciones-panel">
                    <a href="#modalNotificaciones" data-toggle="modal"  class="hidden-xs"><i class="fa fa-bell fa-lg"></i></a>
                    <a  href="#modalNotificaciones" data-toggle="modal" class="visible-xs"><i class="fa fa-bell"></i>&nbsp;Notificaciones</a>
                </li>
               
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user fa-lg"></i>&nbsp; {{ $usuario->nombreCompleto() }} <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ URL::to('configurar/cuenta') }}"><i class="fa fa-cog fa-lg"></i> Configurar mi cuenta</a></li>
                        <li class="divider"></li>
                        <li><a href="#modalAyuda" data-toggle="modal"><i class="fa fa-question-circle fa-lg"></i> Ayuda</a></li>
                        <li><a href="#modalAcercaDe" data-toggle="modal"><i class="fa fa-info-circle fa-lg"></i> Acerca de</a></li>
                        <li class="divider"></li>
                        <li><a href="{{ URL::to('logout') }}"><i class="fa fa-sign-out fa-lg"></i> Cerrar Sesión</a></li>
                    </ul>
                </li>
            </ul>        
        </div><!--/.nav-collapse -->
        @endif
    </div>
    </div>