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
*	@author 			Mario Cabrera, Donaldo Rios
*	@package 			cium
*	@version 			1.0 
*	@comment 			
*/
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

//Procesa el formulario e identifica al usuario
Route::any('/login', array('uses' => 'AutenticacionController@login'));
//Desconecta al usuario
Route::get('/logout', array('uses' => 'AutenticacionController@logout', 'before' => 'auth.sentry'));

Route::get('cambiar-contrasena/{token}', array('uses' => 'AutenticacionController@cambiarContrasena'));

Route::group(array('before'=>'auth.sentry'), function(){
	Route::get('/', array('uses' => 'DashboardController@index'));

	Route::get('configurar/cuenta',array('uses'=>'CuentaController@index'));

	Route::group(array('prefix'=>'administrador'), function(){
		Route::get('usuarios', array('uses' => 'UsuariosController@index'));
		Route::get('roles', array('uses' => 'RolesController@index'));
		Route::post('usuarios', array('uses' => 'UsuariosController@validar'));
	});

	Route::group(array('prefix'=>'poa'), function(){
		Route::get('proyectos',array('uses'=>'ProyectosController@index'));
	});

	Route::group(array('prefix'=>"v1"),function(){
		Route::get('/', function()
		{
			return "RESTful API Plataforma Base beta 1.0";
		});

		Route::resource('usuarios',			'V1\UsuariosController');
		Route::resource('roles',			'V1\RolesController');
		Route::resource('permisos',			'V1\PermisosController');
		Route::resource('cuenta',			'V1\CuentaController');
		
		Route::resource('proyectos',		'V1\ProyectosController');
	});
});


App::missing(function($exception)
{
	if (!Sentry::check()) {
		return Redirect::to('login');
	}

	$sys_sistemas = SysGrupoModulo::all();	
	$usuario = Sentry::getUser();
	
    return Response::view('errors.404', array('usuario'=>$usuario,'sys_activo'=>null,'sys_sistemas'=>$sys_sistemas,'sys_mod_activo'=>null), 404);
    						
});