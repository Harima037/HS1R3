<?php
/* 
*	CIUM
*	Captura de Indicadores de Unidades Médicas
*
*	PHP version 5.5.3
*
* 	Área de Informática, Dirección de Planeación y Desarrollo.
*
*	@copyright			Copyright 2014, Instituto de Salud.
*	@author 			Donaldo Rios
*	@package 			cium
*	@version 			1.0 
*	@comment 			
*/

class AutenticacionController extends Controller {

	public function login(){
		$error = '';
		$info = '';
		$usuario = '';
		if (Sentry::check()) {
			return Redirect::intended('/');
		}

		if(Input::all()){
			$usuario = Input::get('usuario');
			$parametros = Input::all();
			try{
				if(isset($parametros['recuperar-contrasena'])){
					$rows = Sentry::getUserProvider()->createModel();
					if($parametros['username']){
						$rows = $rows->where('username','=',$parametros['username']);
					}
					if($parametros['email']){
						$rows = $rows->where('email','=',$parametros['email']);
					}

					$User = $rows->get();

					if(count($User) == 1){
						$User = Sentry::findUserByID($User[0]->id);
					}else{
						$User = null;
					}

					if($User) {
						$data['usuario'] = $User;
						$data['token'] = $User->getResetPasswordCode();
						Mail::send('emails.auth.reset_pass', $data, function($message) use ($User){
							$message->to($User->email,$User->nombres)->subject('SIRE:: Recuperar Contraseña');
						});
						$info = 'Se ha enviado un correo electrónico a ['.$User->email.'] con los pasos a seguir para recuperar su contraseña.';
					}else{
						$error = 'No se encontraron los datos del usuario.';
					}
				}else{
					if($User = Sentry::authenticate(array('username'=>$parametros['usuario'],'password'=>$parametros['password']),false)){
						$redirect = Session::get('loginRedirect', '/');
						Session::forget('loginRedirect');
						return Redirect::intended($redirect);
					}
				}
			}catch (\Cartalyst\Sentry\Users\LoginRequiredException $e) {
				$error = 'El usuario es requerido.';
			}catch (\Cartalyst\Sentry\Users\PasswordRequiredException $e){
				$error = 'La contraseña es requerida.';
			}catch (\Cartalyst\Sentry\Users\WrongPasswordException $e){
				$error = 'La contraseña es incorrecta.';
			}catch (\Cartalyst\Sentry\Users\UserNotFoundException $e){
				$error = 'El usuario no fue encontrado.';
			}catch (\Cartalyst\Sentry\Users\UserNotActivatedException $e){
				$error = 'El usuario no esta activo.';
			}catch (\Cartalyst\Sentry\Throttling\UserSuspendedException $e){
				$error = 'El usuario está suspendido.';
			}catch (\Cartalyst\Sentry\Throttling\UserBannedException $e){
				$error = 'El usuario está bloqueado.';
			}catch (\Exception $e){
				$error = 'Ocurrio un error al intentar recuperar la contraseña.';
			}
		}

		return View::make('login')->with(array('error'=>$error,'info'=>$info,'usuario'=>$usuario));
	}

	public function logout(){
		Session::flush();
		Sentry::logout();
		return Redirect::to('login');
	}

	public function cambiarContrasena($token){
		try{
			if($user = Sentry::findUserByResetPasswordCode($token)){
				Sentry::login($user,false);
				return Redirect::to('configurar/cuenta');
			}else{
				$error = 'Usuario no encontrado';
			}
		}catch (\Cartalyst\Sentry\Users\UserNotFoundException $e){
	    	$error = 'Código no encontrado.';
		}catch (\Cartalyst\Sentry\Users\UserNotActivatedException $e){
			$error =  'El usuario no esta activo.';
		}catch (\Cartalyst\Sentry\Throttling\UserSuspendedException $e){
			$error = 'El usuario está suspendido.';
		}catch (\Cartalyst\Sentry\Throttling\UserBannedException $e){
			$error = 'El usuario está bloqueado.';
		}
		return View::make('login')->with(array('error'=>$error,'info'=>'','usuario'=>''));
	}
}