<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request){
	if(Sentry::check()){
		$path = explode('/', $request->path());

		if(count($path) > 1){
			$permission = SysGrupoModulo::getPermisoBase($path[1]);
		}else{
			$permission = SysGrupoModulo::getPermisoBase($path[0]);
		}
		
		if($permission){
		
			$isValid = true;
			$permiso_str = '';
			if(Request::isMethod('get')){
				if(!Sentry::hasAccess($permission.'.R')){
					$isValid = false;
					$permiso_str = 'lectura';
				}
			}elseif(Request::isMethod('post')){
				if(!Sentry::hasAccess($permission.'.C')){
					$isValid = false;
					$permiso_str = 'escritura';
				}
			}elseif(Request::isMethod('put')){
				if(!Sentry::hasAccess($permission.'.U')){
					$isValid = false;
					$permiso_str = 'edición';
				}
			}elseif(Request::isMethod('delete')){
				if(!Sentry::hasAccess($permission.'.D')){
					$isValid = false;
					$permiso_str = 'borrado';
				}
			}
			if(!$isValid){
				if(!$request->ajax()){
					$sys_sistemas = SysGrupoModulo::all();	
					$usuario = Sentry::getUser();
					return Response::view('errors.403', array('usuario'=>$usuario,'sys_activo'=>null,'sys_sistemas'=>$sys_sistemas,'sys_mod_activo'=>null), 403);
				}

				$httpStatus = 403;
				$httpData = array(
					"data"=>"No tienes los permisos de $permiso_str necesarios para realizar esta acción.",
					'code'=>'U01'
				);
				return Response::json($httpData,$httpStatus);
			}
		}
	}
});

App::after(function($request,$response){
	if(Sentry::check() && !Request::isMethod('get') && !$response->isRedirect() && $response->getStatusCode() == 200){
		$bitacora = new SysBitacora;

		$bitacora->tipo = $request->method();
		$bitacora->idUsuario = Sentry::getUser()->id;
		$bitacora->info = $request->server('HTTP_USER_AGENT');
		$bitacora->ip = $request->getClientIp();
		$path_array = explode('/', $request->path());
		$bitacora->controlador = $request->path();
		if(isset($path_array[2])){
			$bitacora->idRecurso = $path_array[2];
		}
		
		$bitacora->save();
	}
	
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth.sentry', function()
{
	if (!Sentry::check()){
		Session::put('loginRedirect',Request::url());
		return Redirect::to('login');
	}
});

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::guest('login');
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});