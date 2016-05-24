<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="{{ URL::to('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" media="screen">
    <link href="{{ URL::to('css/font-awesome-4.3.0/css/font-awesome.min.css') }}" rel="stylesheet" media="screen">
    <link href="{{ URL::to('css/login.css') }}" rel="stylesheet" media="screen">
</head>
<body>
    <div class="container">

    <form class="form-signin" method="post">
        <h2 class="form-signin-heading"><big>SIRE - 2015</big></h2>
        @if($error)
        <div class="alert alert-danger"><span class="glyphicon glyphicon-ban-circle"></span> {{ $error }}</div>
        @endif
        @if($info)
        <div class="alert alert-info"><span class="glyphicon glyphicon-envelope"></span> {{ $info }}</div>
        @endif
        <input type="text" name="usuario" class="form-control" placeholder="Usuario" value="{{$usuario}}" autofocus>
        <input type="password"  name="password" class="form-control" placeholder="Contraseña">
        <!--<label class="checkbox"><input type="checkbox" value="remember-me"> Remember me</label>-->
        <button class="btn btn-lg btn-danger btn-block" type="submit"><span class="fa fa-power-off"></span> Entrar</button>
       
        <br>
        <div  style="text-align:center;"> 
            <!--a href="#"><span class="glyphicon glyphicon-lock"></span> Olvidé mi contraseña.</a-->
            <button type="button" class="btn-link" data-toggle="modal" data-target=".login-forgotpass-modal-sm">
                <span class="fa fa-lock"></span> Olvidé mi contraseña.
            </button><br><br>
        </div>
    </form>
    
    <div class="modal fade login-forgotpass-modal-sm" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="loginModalLabel">Olvidé mi contraseña</h4>
                </div>
                <form class="form-vertical" role="form" method="post">
                <div class="modal-body">
                    <p class="help-block">Por favor proporcione su nombre de usuario o la dirección de correo electronico con el que está dado de alta en el sistema. Se le enviará un correo electrónico con los pasos a seguir para recuperar su contraseña.</p>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="fa fa-user"></span></span>
                            <input type="text" name="username" class="form-control" placeholder="Usuario">
                        </div>
                    </div>
                    <div class="form-group">
                        <span class="center-block"> o </span>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="fa fa-envelope"></span></span>
                            <input type="email" name="email" class="form-control" placeholder="Correo Electronico">
                        </div>
                    </div>
                    <input type="hidden" name="recuperar-contrasena" value="1">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" data-loading-text='Enviando'>
                        <span class="fa fa-send"></span> Enviar
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>

    </div> 
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/dependencias/jquery-1.10.2.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>