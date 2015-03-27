<?php
/* 
*	POA
*	Programa Operativo Anual
*
*	PHP version 5.5.3
*
* 	Área de Informática, Dirección de Planeación y Desarrollo.
*
*	@copyright			Copyright 2015, Instituto de Salud.
*	@author 			Mario Cabrera, Donaldo Ríos
*	@package 			poa
*	@version 			1.0 
*	@comment 			
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

	Route::group(array('prefix'=>'expediente'), function(){
		Route::get('proyectos',array('uses'=>'ProyectosController@index'));
		Route::any('caratula/{id?}',array('uses'=>'ProyectosController@caratula'));
		
		Route::get('inversion',array('uses'=>'InversionController@index'));
		Route::any('caratula-inversion/{id?}',array('uses'=>'InversionController@caratula'));
		
		Route::get('fibap',array('uses'=>'FibapController@index'));
		Route::any('formulario-fibap',array('uses'=>'FibapController@formulario'));

		Route::get('programas-presupuestarios',array('uses'=>'ProgramaPresupuestarioController@index'));
		Route::get('editar-programa/{id?}',array('uses'=>'ProgramaPresupuestarioController@editar'));
	});
	
	Route::group(array('prefix'=>'revision'), function(){
		Route::get('revision-proyectos',array('uses'=>'RevisionController@index'));
		Route::any('revision-caratula',array('uses'=>'RevisionController@caratula'));		
		
		Route::get('segui-proyectos-inst',array('uses'=>'SeguimientoInstitucionalController@index'));
		Route::get('comentar-avance/{id}',array('uses'=>'SeguimientoInstitucionalController@rendicionCuentas'));
		Route::get('segui-proyectos-inv',array('uses'=>'SeguimientoInstitucionalController@indexInversion'));
		
		
		
	});

	Route::group(array('prefix'=>'rendicion-cuentas'),function(){
		Route::get('rend-cuenta-inst',array('uses'=>'SeguimientoController@indexInstitucional'));
		Route::get('rend-cuenta-inv',array('uses'=>'SeguimientoController@indexInversion'));

		Route::get('editar-avance/{id}',array('uses'=>'SeguimientoController@rendicionCuentas'));
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
		Route::resource('inversion',		'V1\InversionController');
		Route::resource('reporteProyecto',	'V1\ReporteProyectoController', array('only' => array('show')));
		Route::resource('fibap',			'V1\FibapController');

		Route::resource('rend-cuenta-inst', 	'V1\SeguimientoController');
		Route::resource('rend-cuenta-inv', 		'V1\SeguimientoController');

		Route::resource('revision-proyectos',	'V1\RevisionController');
		Route::resource('segui-proyectos-inst', 'V1\SeguimientoInstitucionalController');
		Route::resource('segui-proyectos-inv', 'V1\SeguimientoInstitucionalController');

		Route::resource('programas-presupuestarios','V1\ProgramaPresupuestarioController');

		Route::resource('reporte-evaluacion',	'V1\ReporteEvaluacionController', array('only'=>array('show')));
	});
});


App::missing(function($exception)
{
	if (!Sentry::check()) {
		return Redirect::to('login');
	}

	if(Request::ajax()){
		return Response::json(array('data'=>'El recurso que solicita no se encuentra o no esta disponible. Si el problema persiste por favor consulte con sorpote técnico'),404);
	}

	$sys_sistemas = SysGrupoModulo::all();	
	$usuario = Sentry::getUser();

    return Response::view('errors.404', array('usuario'=>$usuario,'sys_activo'=>null,'sys_sistemas'=>$sys_sistemas,'sys_mod_activo'=>null), 404);
    						
});