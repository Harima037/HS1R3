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

use SSA\Utilerias\Validador,SSA\Utilerias\Util;
use Illuminate\Database\QueryException, \Exception;
use BaseController, Input, Response, DB, Sentry, IndicadorFASSA, RegistroAvanceIndicadorFASSA, IndicadorFASSAMeta,Directorio, IndicadorFASSAMetaComentarios;

class RevisionRendicionFassaController extends \BaseController {
	
	private $reglasComentario = array(
		'idproyecto' => 'required',		
		'idcampo' => 'required',
		'idavance' => 'required',
		'comentario' => 'required'
	);
	
	private $reglasComentario2 = array(
		'comentario' => 'required'
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
				
				$ejercicio_actual = Util::obtenerAnioCaptura();
				//$rows = IndicadorFASSA::getModel();
				$rows = IndicadorFASSAMeta::getModel();
				
				$usuario = Sentry::getUser();
				if($usuario->idDepartamento == 2){
					if($usuario->filtrarIndicadores){
						$rows = $rows->where('idUsuarioValidacionSeg','=',$usuario->id);
					}
				}else{
					$rows = $rows->where('idUsuarioRendCuenta','=',$usuario->id);
				}
				
				$rows = $rows->with('registroAvance');
				$rows = $rows->indicadoresEjercicio()->where('ejercicio','=',$ejercicio_actual);

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

				$rows = $rows->orderBy('id', 'desc')
							->skip(($parametros['pagina']-1)*10)->take(10)
							->get();
				//
				$data = array('resultados'=>$total,'data'=>$rows, 'mes_actual'=>Util::obtenerMesActual());
				$respuesta['data'] = $data;

				if($total<=0){
					$respuesta['http_status'] = 404;
					$respuesta['data'] = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
				}
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
			$recurso = IndicadorFASSAMeta::indicadorMetaDetalle()->with('comentario')->find($id);
			if($recurso){
				$mes_actual = intval(Util::obtenerMesActual());

				$recurso->load(array('registroAvance'=>function($query)use($mes_actual){
					return $query->where('mes','<=',$mes_actual);
				}));

				$recurso['mes_actual'] = $mes_actual;
				$data['data'] = $recurso;
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
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');

		$parametros = Input::all();

		$nuevoComentario = new IndicadorFASSAMetaComentarios;
		
		$nuevoComentario->idIndicadorFASSAMeta = $parametros['idproyecto'];
		$nuevoComentario->mes = intval(Util::obtenerMesActual());		
		$nuevoComentario->idCampo = $parametros['idcampo'];
		$nuevoComentario->idAvance = $parametros['idavance'];
		if($parametros['idavance']!='0')
			$nuevoComentario->mes = intval(Util::obtenerMesActual());
		else
			$nuevoComentario->mes = 13;
		$nuevoComentario->observacion = $parametros['comentario'];
		
		$Resultado = Validador::validar($parametros, $this->reglasComentario);
		
		if($Resultado === true)
		{
			$nuevoComentario->save();
			$respuesta['data']['data'] = $nuevoComentario;
		}
		else
		{
			$respuesta['http_status'] = 500;
			$respuesta = $Resultado;
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
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');
		
		$parametros = Input::all();
				
		if(isset($parametros['actualizarproyecto']))
		{
			//throw new Exception($parametros['actualizarproyecto'],1);
			
			if($parametros['actualizarproyecto']=="aprobar") //Poner estatus 4 (Aprobado)
			{
				if($parametros['tiporevision']=='meta')
				{
					$recurso = IndicadorFASSAMeta::find($id);
					if(is_null($recurso)){
						$respuesta['http_status'] = 404;
						$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
					}else{
						$recurso->idEstatus = 4;
						$recurso->save();
					}
				}
				else if($parametros['tiporevision']=='avance')
				{
					$recurso = RegistroAvanceIndicadorFASSA::find($parametros['idavance']);
					if(is_null($recurso)){
						$respuesta['http_status'] = 404;
						$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
					}else{
						$recurso->idEstatus = 4;
						$recurso->save();
					}
				}
			}
			else if($parametros['actualizarproyecto']=="regresar") //Poner estatus 3 (Regreso a corrección)
			{
				if($parametros['tiporevision']=='meta')
				{
					$recurso = IndicadorFASSAMeta::find($id);
					if(is_null($recurso)){
						$respuesta['http_status'] = 404;
						$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
					}else{
						$recurso->idEstatus = 3;
						$recurso->save();
					}
				}
				else if($parametros['tiporevision']=='avance')
				{
					$recurso = RegistroAvanceIndicadorFASSA::find($parametros['idavance']);
					if(is_null($recurso)){
						$respuesta['http_status'] = 404;
						$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
					}else{
						$recurso->idEstatus = 3;
						$recurso->save();
					}
				}
			}
			else if($parametros['actualizarproyecto']=="firmar") //Poner estatus 5 (Enviar a firma)
			{
				if($parametros['tiporevision']=='meta')
				{
					$recurso = IndicadorFASSAMeta::find($id);
					if(is_null($recurso)){
						$respuesta['http_status'] = 404;
						$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
					}else{
						$recurso->idEstatus = 5;
						$recurso->save();
					}
				}
				else if($parametros['tiporevision']=='avance')
				{
					$recurso = RegistroAvanceIndicadorFASSA::find($parametros['idavance']);
					if(is_null($recurso)){
						$respuesta['http_status'] = 404;
						$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
					}else{
						$recurso->idEstatus = 5;
						$recurso->save();
					}
				}
			}
			
		}
		else
		{
			$recurso = IndicadorFASSAMetaComentarios::find($id);
			if(is_null($recurso)){
				$respuesta['http_status'] = 404;
				$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
			}else{
				
				$recurso->observacion = $parametros['comentario'];
			
				$Resultado = Validador::validar($parametros, $this->reglasComentario2);
							
				if($Resultado===true)
					$recurso->save();
				else
				{
					$respuesta['http_status'] = 500;
					$respuesta = $Resultado;
				}
			}
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
			$recurso = IndicadorFASSAMetaComentarios::where('id','=',$id)->delete();
			
			if(is_null($recurso)){
				$respuesta['http_status'] = 404;
				$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
			}
		}catch(Exception $ex){
			$http_status = 500;	
			$data = array('data' => "No se puede eliminar el comentario",'ex'=>$ex->getMessage(),'code'=>'S03');	
		}

		return Response::json($data,$http_status);
	}


}//Fin de la CLASE