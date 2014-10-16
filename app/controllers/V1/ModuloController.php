<?php

namespace V1;

use iWARE\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry;
use Modulo, Hash;

class ModuloController extends \BaseController {
	private $reglas = array(
			'catalogo' => 'required',
			'nombre' => 'required|alpha_dash'
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

		try{
			$parametros = Input::all();
			if(isset($parametros['formatogrid'])){
				
				$rows = Modulo::getModel();

				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
				
				if(isset($parametros['buscar'])){				
					$rows = $rows->where('nombre','like','%'.$parametros['buscar'].'%')
							->where('datos','like','%'.$parametros['buscar'].'%');
					$total = $rows->count();
				}else{				
					$total = $rows->count();						
				}

				$rows = $rows->select('modulo.id','catalogo.descripcion','modulo.nombre','modulo.datos','modulo.creadoAl','modulo.modificadoAl')
									->leftjoin('catalogo','catalogo.id','=','modulo.idCatalogo')
									->orderBy('id', 'desc')
									->skip(($parametros['pagina']-1)*10)->take(10)
									->get();

				$data = array('resultados'=>$total,'data'=>$rows);

				if($total<=0){
					$http_status = 404;
					$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
				}
				
				return Response::json($data,$http_status);
			}	

			$rows = Modulo::all();

			if(count($rows) == 0){
				$http_status = 404;
				$data = array("data"=>"No hay datos",'code'=>'W00');
			}else{
				$data = array("data"=>$rows->toArray());
			}
		}catch(\Exception $e){
			$http_status = 404;
			$data = array("data"=>"",'code'=>'S02');
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

		$recurso = Modulo::find($id);

		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$data = array("data"=>$recurso->toArray());
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
		
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');

		$recurso = new Modulo;
		$recurso->nombre = Input::get('nombre');
		$recurso->datos = Input::get('datos');
		$recurso->idCatalogo = Input::get('catalogo');

		$respuesta = Validador::guardar(Input::all(), $this->reglas, $recurso);		
		
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

		$recurso = Modulo::find($id);

		if(is_null($recurso)){
			$respuesta['http_status'] = 404;
			$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$recurso->nombre	= Input::get('nombre');
			$recurso->datos 	= Input::get('datos');
			$recurso->idCatalogo = Input::get('catalogo');

			$respuesta = Validador::guardar(Input::all(), $this->reglas, $recurso);
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
			$ids = Input::get('rows');
			
			$rows = Modulo::wherein('id', $ids)->delete();

			if($rows>0){
				$data = array("data"=>"Se han eliminado los recursos.");
			}else{
				$http_status = 404;
				$data = array('data' => "No se pueden eliminar los recursos.",'code'=>'U06');
			}
		}catch(Exception $ex){
			$http_status = 500;	
			$data = array('data' => "No se pueden borrar los registros",'code'=>'S01');	
		}

		return Response::json($data,$http_status);
	}

}