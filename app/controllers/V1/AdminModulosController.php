<?php

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry;
use SysModulo, SysGrupoModulo, Hash;

class AdminModulosController extends \BaseController {
	private $reglasModulo = array(
		'grupo' 	=> 'required',
		'permiso' 	=> 'required',
		'key' 		=> 'required',
		'nombre' 	=> 'required',
		'uri' 		=> 'required',
		'icono' 	=> 'required'
	);

	private $reglasGrupo = array(
		'grupo-key' 	=> 'required',
		'grupo-nombre' 	=> 'required',
		'grupo-uri' 	=> 'required',
		'grupo-icono' 	=> 'required'
	);

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		$http_status = 200;
		$data = array();

		$parametros = Input::all();
		if(isset($parametros['formatogrid'])){
			$rows = SysModulo::getModel();

			if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
		
			$rows = $rows->leftjoin('sysGruposModulos','sysGruposModulos.id','=','sysModulos.idSysGrupoModulo')
						->join('sysPermisos','sysPermisos.id','=','sysModulos.idSysPermiso');

			if(isset($parametros['buscar'])){				
				$rows = $rows->where(function($query){
					$query->where('sysGruposModulos.key','like','%'.$parametros['buscar'].'%')
						->orWhere('sysGruposModulos.nombre','like','%'.$parametros['buscar'].'%')
						->orWhere('sysGruposModulos.uri','like','%'.$parametros['buscar'].'%')
						->orWhere('sysModulos.key','like','%'.$parametros['buscar'].'%')
						->orWhere('sysModulos.nombre','like','%'.$parametros['buscar'].'%')
						->orWhere('sysModulos.uri','like','%'.$parametros['buscar'].'%');
				});
				$total = $rows->count();
			}else{
				$total = $rows->count();						
			}

			$rows = $rows->select('sysModulos.id','sysGruposModulos.icono AS grupoIcono',
								'sysGruposModulos.nombre AS grupo',
								'sysModulos.icono AS moduloIcono','sysModulos.nombre AS modulo',
								'sysPermisos.descripcion AS permisos','sysModulos.visible')
								->orderBy('id', 'desc')
								->skip(($parametros['pagina']-1)*10)->take(10)
								->get();
			
			$data = array('resultados'=>$total,'data'=>$rows);

			if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No se encontraron variables",'code'=>'W00');
			}			
			
			return Response::json($data,$http_status);
		}	

		$rows = SysModulo::join('sysGruposModulos','sysGruposModulos.id','=','sysModulos.idSysGrupoModulo')
						->get();
		
		if(count($rows) == 0){
			$http_status = 404;
			$data = array("data"=>"No se encontraron variables",'code'=>'W00');
		}else{
			$data = array("data"=>$rows->toArray());
		}
		return Response::json($data,$http_status);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
		$http_status = 200;
		$data = array();

		try{
			$parametros = Input::all();
			if($parametros['editar'] == 'modulo'){
				$recurso = SysModulo::find($id);
			}else if($parametros['editar'] == 'grupo'){
				$recurso = SysGrupoModulo::find($id);
			}
			$data = array("data"=>$recurso);
		}catch(\Exception $ex){
			$http_status = 500;
			$data = array('data'=>'Error al tratar de obtener los datos del recurso','code'=>'U01');
		}
		return Response::json($data,$http_status);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
		$respuesta = array();
		try{
			$parametros = Input::all();

			if($parametros['guardar'] == 'modulo'){
				$validacion = Validador::validar($parametros,$this->reglasModulo);
			}elseif ($parametros['guardar'] == 'grupo') {
				$validacion = Validador::validar($parametros,$this->reglasGrupo);
			}

			if($validacion === TRUE){
				if($parametros['guardar'] == 'modulo'){
					$recurso = new SysModulo;

					$recurso->idSysGrupoModulo 	= $parametros['grupo'];
					$recurso->idSysPermiso 		= $parametros['permiso'];
					$recurso->key 				= $parametros['key'];
					$recurso->nombre 			= $parametros['nombre'];
					$recurso->uri 				= $parametros['uri'];
					$recurso->icono 			= $parametros['icono'];

					if(isset($parametros['visible'])){
						$recurso->visible = 1;
					}else{
						$recurso->visible = 0;
					}
				}elseif($parametros['guardar'] == 'grupo') {
					$recurso = new SysGrupoModulo;

					$recurso->key 				= $parametros['grupo-key'];
					$recurso->nombre 			= $parametros['grupo-nombre'];
					$recurso->uri 				= $parametros['grupo-uri'];
					$recurso->icono 			= $parametros['grupo-icono'];

					if(isset($parametros['grupo-visible'])){
						$recurso->visible = 1;
					}else{
						$recurso->visible = 0;
					}
				}

				if($recurso){
					if($recurso->save()){
						if($parametros['guardar'] == 'grupo') {
							$recurso = SysGrupoModulo::all();
						}
						$respuesta['http_status'] = 200;
						$respuesta['data'] = array("data"=>$recurso);
					}else{
						$respuesta['http_status'] = 500;
						$respuesta['data'] = array("data"=>'Ocurrio un error al intentar guardar los datos del recurso','code'=>'U01');
					}
				}
			}else{
				$respuesta = $validacion;
			}
		}catch(\Exception $ex){
			$respuesta['http_status']=500;
			$respuesta['data']=array('data'=>'Error al tratar de obtener los datos del recurso','code'=>'U01','line'=>$ex->getLine(),'ex'=>$ex->getMessage());
		}
		return Response::json($respuesta['data'],$respuesta['http_status']);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
		$respuesta = array();
		try{
			$parametros = Input::all();

			if($parametros['guardar'] == 'modulo'){
				$validacion = Validador::validar($parametros,$this->reglasModulo);
			}elseif ($parametros['guardar'] == 'grupo') {
				$validacion = Validador::validar($parametros,$this->reglasGrupo);
			}

			if($validacion === TRUE){
				if($parametros['guardar'] == 'modulo'){
					$recurso = SysModulo::find($id);

					$recurso->idSysGrupoModulo 	= $parametros['grupo'];
					$recurso->idSysPermiso 		= $parametros['permiso'];
					$recurso->key 				= $parametros['key'];
					$recurso->nombre 			= $parametros['nombre'];
					$recurso->uri 				= $parametros['uri'];
					$recurso->icono 			= $parametros['icono'];
					$recurso->key 				= $parametros['key'];

					if(isset($parametros['visible'])){
						$recurso->visible = 1;
					}else{
						$recurso->visible = 0;
					}
				}elseif ($parametros['guardar'] == 'grupo') {
					$recurso = SysGrupoModulo::find($id);

					$recurso->key 				= $parametros['grupo-key'];
					$recurso->nombre 			= $parametros['grupo-nombre'];
					$recurso->uri 				= $parametros['grupo-uri'];
					$recurso->icono 			= $parametros['grupo-icono'];

					if(isset($parametros['grupo-visible'])){
						$recurso->visible = 1;
					}else{
						$recurso->visible = 0;
					}
				}

				if($recurso){
					if($recurso->save()){
						if($parametros['guardar'] == 'grupo') {
							$recurso = SysGrupoModulo::all();
						}
						$respuesta['http_status'] = 200;
						$respuesta['data'] = array("data"=>$recurso);
					}else{
						$respuesta['http_status'] = 500;
						$respuesta['data'] = array("data"=>'Ocurrio un error al intentar guardar los datos del recurso','code'=>'U01');
					}
				}
			}else{
				$respuesta = $validacion;
			}
		}catch(\Exception $ex){
			$respuesta['http_status']=500;
			$respuesta['data']=array('data'=>'Error al tratar de obtener los datos del recurso','code'=>'U01');
		}
		return Response::json($respuesta['data'],$respuesta['http_status']);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
		$http_status = 200;
		$data = array();

		try{
			$parametros = Input::all();

			if(isset($parametros['eliminar'])){
				if($parametros['eliminar'] == 'modulo'){
					$rows = SysModulo::where('id','=',$id)->delete();
				}elseif($parametros['eliminar'] == 'grupo'){
					$rows = DB::transaction(function()use($id){
						SysModulo::where('idSysGrupoModulo','=',$id)
									->update(array('idSysGrupoModulo'=>null));
						return SysGrupoModulo::where('id','=',$id)->delete();
					});
					$rows = SysGrupoModulo::all();
				}
			}else{
				$ids = $parametros['rows'];
				$rows = SysModulo::whereIn('id',$ids)->delete();
			}
			
			$data = array("data"=>"Se han eliminado los recursos.",'elementos'=>$rows);
		}catch(\Exception $ex){
			$http_status = 500;	
			$data = array('data' => "No se pueden borrar los registros",'code'=>'S03');
		}
		return Response::json($data,$http_status);
	}
}