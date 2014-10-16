<?php

namespace V1;

use iWARE\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry;
use Catalogo, Hash;

class CatalogoController extends \BaseController {
	private $reglas = array(
			'descripcion' => 'required'
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
			
			$rows = Catalogo::getModel();

			if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
			
			if(isset($parametros['buscar'])){				
				$rows = $rows->where('catalogo.descripcion','like','%'.$parametros['buscar'].'%');
				$total = $rows->count();
			}else{				
				$total = $rows->count();						
			}

			$rows = $rows->select('id','descripcion','creadoAl','modificadoAl')
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

		$rows = Catalogo::all();

		if(count($rows) == 0){
			$http_status = 404;
			$data = array("data"=>"No hay datos",'code'=>'W00');
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

		$recurso = Catalogo::find($id);

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

		$recurso = new Catalogo;
		$recurso->descripcion = Input::get('descripcion');

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

		$recurso = Catalogo::find($id);

		if(is_null($recurso)){
			$respuesta['http_status'] = 404;
			$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$recurso->descripcion	= Input::get('descripcion');

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
			
			$rows = Catalogo::wherein('id', $ids)->delete();

			if($rows>0){
				$data = array("data"=>"Se han eliminado los recursos.");
			}else{
				$http_status = 404;
				$data = array('data' => "No se pueden eliminar los recursos.",'code'=>'S03');
			}	
		}catch(Exception $ex){
			$http_status = 500;	
			$data = array('data' => "No se pueden borrar los registros",'code'=>'S03');	
		}

		return Response::json($data,$http_status);
	}

}