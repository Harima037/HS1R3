<?php
/* 
*	SIRE
*
*	PHP version 5.5.3
*
* 	Área de Informática, Dirección de Planeación y Desarrollo.
*
*	@copyright			Copyright 2014, Instituto de Salud.
*	@author 			Mario Cabrera
*	@package 			sire
*	@version 			1.0 
*	@comment 			
*/

namespace V1;

use SSA\Utilerias\Validador;
use Illuminate\Database\QueryException, \Exception;
use BaseController, Input, Response, DB, Sentry, IndicadorFASSA,Directorio;

class IndicadorFassaController extends \BaseController {

	private $reglas = array(
			'nivel-indicador'			=> 'required',
			'indicador'					=> 'required',
			'tipo-formula'				=> 'required',
			'formula'					=> 'required',
			'fuente-informacion'		=> 'required',
			'numerador'					=> 'required',
			'denominador'				=> 'required',
			'unidad-responsable'		=> 'required',
			'responsable-informacion'	=> 'required'
		);

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		$respuesta = array('http_status'=>200,'data'=>'');

		$parametros = Input::all();
		
		try{
			if(isset($parametros['formatogrid'])){
				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
			
				$rows = IndicadorFASSA::getModel();

				if(isset($parametros['buscar'])){
					if($parametros['buscar']){
						$rows = $rows->where(function($query) use ($parametros){
									$query->where('indicador','like','%'.$parametros['buscar'].'%')
										->orWhere('fuenteInformacion','like','%'.$parametros['buscar'].'%');
								});
					}
					$total = $rows->count();
				}else{
					$total = $rows->count();
				}

				$rows = $rows->select('indicadorFASSA.id','indicadorFASSA.indicador','indicadorFASSA.claveNivel','sentryUsers.username','indicadorFASSA.modificadoAl')
							->orderBy('id', 'desc')
							->leftjoin('sentryUsers','indicadorFASSA.actualizadoPor','=','sentryUsers.id')
							->skip(($parametros['pagina']-1)*10)->take(10)
							->get();
				//
				$data = array('resultados'=>$total,'data'=>$rows);

				if($total<=0){
					$respuesta['http_status'] = 404;
					$respuesta['data'] = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
				}
				$respuesta['data'] = $data;
			}elseif(isset($parametros['cargar-responsables'])){
				$responsables = Directorio::responsablesActivos($parametros['unidad-responsable'])->get();
				$respuesta['data'] = array('data'=>$responsables);
			}else{
				$rows = IndicadorFASSA::all();
				if(count($rows) == 0){
					$respuesta['http_status'] = 404;
					$respuesta['data'] = array("data"=>"No hay datos",'code'=>'W00');
				}else{
					$respuesta['data'] = array("data"=>$rows);
				}
			}
		}catch(\Exception $e){
			$respuesta['http_status'] = 500;
			$respuesta['data'] = array("data"=>$e->getMessage(),'code'=>'S01');
		}
		return Response::json($respuesta['data'],$respuesta['http_status']);
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
			$recurso = IndicadorFASSA::find($id);
			if($recurso){
				$responsables = Directorio::responsablesActivos($recurso->claveUnidadResponsable)->get();
				$recurso['responsables'] = $responsables;
				$data = array("data"=>$recurso);
			}else{
				$http_status = 404;
				$data = array("data"=>"No se ha podido encontrar el recurso solicitado.",'code'=>'S01');
			}
		}catch(\Exception $e){
			$http_status = 500;
			$data = array("data"=>'Error al obtener los datos','code'=>'S03','ex'=>$e->getMessage());
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
		$respuesta = Validador::validar(Input::all(), $this->reglas);
		
		if($respuesta === true){
			try{
				$respuesta = array();
				$parametros = Input::all();

				$recurso = new IndicadorFASSA;

				$recurso->claveNivel 				= $parametros['nivel-indicador'];
				$recurso->indicador 				= $parametros['indicador'];
				$recurso->claveTipoFormula 			= $parametros['tipo-formula'];
				$recurso->formula 					= $parametros['formula'];
				$recurso->fuenteInformacion 		= $parametros['fuente-informacion'];
				$recurso->numerador 				= $parametros['numerador'];
				$recurso->denominador 				= $parametros['denominador'];
				$recurso->claveUnidadResponsable 	= $parametros['unidad-responsable'];
				$recurso->idResponsableInformacion	= $parametros['responsable-informacion'];

				$numerador = $parametros['numerador'];
				$denominador = $parametros['denominador'];
				if($parametros['tipo-formula'] == 'T'){
					$porcentaje = floatval(($numerador * 100000)/$denominador);
				}else{
					$porcentaje = floatval(($numerador * 100)/$denominador);
				}
				$recurso->porcentaje = $porcentaje;

				$titular = Directorio::titularesActivos(array($parametros['unidad-responsable']))->first();
				$recurso->idLiderPrograma = $titular->id;

				if($recurso->save()){
					$respuesta['http_status'] = 200;
					$respuesta['data'] = array("data"=>$recurso->toArray());
				}else{
					$respuesta['http_status'] = 500;
					$respuesta['data'] = array("data"=>'Ocurrio un error al intentar guardar la informacion','code'=>'S01');
				}
			}catch(\Exception $e){
				$respuesta['http_status'] = 500;
				$respuesta['data'] = array("data"=>$e->getMessage(),'code'=>'S03');
			}
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
			$valid_result = Validador::validar(Input::all(), $this->reglas);
			if($valid_result === true){
				$parametros = Input::all();

				$recurso = IndicadorFASSA::find($id);

				if($parametros['unidad-responsable'] != $recurso->claveUnidadResponsable){
					$titular = Directorio::titularesActivos(array($parametros['unidad-responsable']))->first();
					$recurso->idLiderPrograma = $titular->id;
				}

				$recurso->claveNivel 				= $parametros['nivel-indicador'];
				$recurso->indicador 				= $parametros['indicador'];
				$recurso->claveTipoFormula 			= $parametros['tipo-formula'];
				$recurso->formula 					= $parametros['formula'];
				$recurso->fuenteInformacion 		= $parametros['fuente-informacion'];
				$recurso->numerador 				= $parametros['numerador'];
				$recurso->denominador 				= $parametros['denominador'];
				$recurso->claveUnidadResponsable 	= $parametros['unidad-responsable'];
				$recurso->idResponsableInformacion	= $parametros['responsable-informacion'];

				$numerador = $parametros['numerador'];
				$denominador = $parametros['denominador'];
				if($parametros['tipo-formula'] == 'T'){
					$porcentaje = floatval(($numerador * 100000)/$denominador);
				}else{
					$porcentaje = floatval(($numerador * 100)/$denominador);
				}
				$recurso->porcentaje = $porcentaje;

				if($recurso->save()){
					$respuesta['http_status'] = 200;
					$respuesta['data'] = array("data"=>$recurso->toArray());
				}else{
					$respuesta['http_status'] = 500;
					$respuesta['data'] = array("data"=>'No se pudieron guardar los cambios.','code'=>'S03');
				}
			}else{
				$respuesta['http_status'] = $valid_result['http_status'];
				$respuesta['data'] = $valid_result['data'];
			}
		}catch(\Exception $e){
			$respuesta['http_status'] = 500;
			$respuesta['data'] = array("data"=>$e->getMessage(),'code'=>'S03');
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
			
			$rows = IndicadorFASSA::whereIn('id',$ids)->delete();
			
			if($rows>0){
				$data = array("data"=>"Se han eliminado los recursos.");
			}else{
				$http_status = 500;
				$data = array('data' => "No se pueden eliminar los recursos.",'code'=>'S03');
			}
		}catch(\Exception $ex){
			$http_status = 500;	
			$data = array('data' => "No se pueden borrar los registros",'code'=>'S03');	
		}

		return Response::json($data,$http_status);
	}

}