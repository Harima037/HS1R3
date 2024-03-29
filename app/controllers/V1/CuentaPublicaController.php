<?php

namespace V1;

use SSA\Utilerias\Validador, SSA\Utilerias\Util;
use BaseController, Input, Response, DB, Sentry, Exception;
use Hash, File, EvaluacionAnalisisFuncional, SysConfiguracionVariable;

class CuentaPublicaController extends \BaseController {
	private $reglas = array(
		'cuenta-publica' => 'required'
	);

	private $reglasDatosInstitucionales = array(
		'clave-institucional'	=> 'required',
		'mision'				=> 'required',
		'vision'				=> 'required'
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

				if(isset($parametros['mes'])){
					$mes = $parametros['mes'];
				}else{
					$mes = Util::obtenerMesActual();
					if($mes == 0){ $mes = date('n') - 1; }
				}
				
				if(isset($parametros['ejercicio'])){
					$ejercicio = $parametros['ejercicio'];
				}else{
					$ejercicio = date('Y');
				}

				$rows = EvaluacionAnalisisFuncional::cuentaPublica($mes,$ejercicio);

				$usuario = Sentry::getUser();
				
				if($usuario->filtrarProyectos){
					$rows = $rows->where('idUsuarioValidacionSeg','=',$usuario->id);
				}

				if($usuario->claveUnidad){
					$unidades = explode('|',$usuario->claveUnidad);
					$rows = $rows->whereIn('unidadResponsable',$unidades);
				}

				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
				
				if(isset($parametros['buscar'])){				
					$rows = $rows->where(function($query)use($parametros){
							$query->where(DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,origenAsignacion,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'),'like','%'.$parametros['buscar'].'%')
								->orWhere('nombreTecnico','like','%'.$parametros['buscar'].'%');
					});
					$total = $rows->count();
				}else{				
					$total = $rows->count();						
				}

				$rows = $rows->orderBy('id', 'desc')
							->skip(($parametros['pagina']-1)*10)->take(10)
							->get();
				//
				$data = array('resultados'=>$total,'data'=>$rows);

				if($total<=0){
					$http_status = 404;
					$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
				}
				
				return Response::json($data,$http_status);
			}	

			$rows = EvaluacionAnalisisFuncional::all();

			if(count($rows) == 0){
				$http_status = 404;
				$data = array("data"=>"No hay datos",'code'=>'W00');
			}else{
				$data = array("data"=>$rows->toArray());
			}
		}catch(\Exception $e){
			$http_status = 404;
			$data = array("data"=>"",'ex'=>$e->getMessage(),'line'=>$e->getLine(),'code'=>'S02');
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

		//$recurso = EvaluacionAnalisisFuncional::find($id);
		$recurso = EvaluacionAnalisisFuncional::select('evaluacionAnalisisFuncional.id','evaluacionAnalisisFuncional.idProyecto','evaluacionAnalisisFuncional.mes','evaluacionAnalisisFuncional.analisisResultado','evaluacionAnalisisFuncional.beneficiarios','evaluacionAnalisisFuncional.justificacionGlobal','evaluacionAnalisisFuncional.cuentaPublica','proyectos.finalidadProyecto')->leftjoin('proyectos','proyectos.id','=','evaluacionAnalisisFuncional.idProyecto')->find($id);
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
		$respuesta = Validador::validar(Input::all(), $this->reglasDatosInstitucionales);
		
		if($respuesta === true){
			try{
				$respuesta = array();
				$parametros = Input::all();

				$variables = SysConfiguracionVariable::obtenerVariables(array('clave-institucional','mision','vision'));

				foreach ($variables as $variable) {
					$valor = $parametros[$variable->variable];
					if($valor){
						$variable->valor = $valor;
					}else{
						$variable->valor = null;
					}
				}

				DB::transaction(function()use($variables){
					foreach ($variables as $variable) {
						$variable->save();
					}
				});
				$respuesta['http_status'] = 200;
				$respuesta['data'] = array('data'=>$variables);
			}catch(Exception $e){
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
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');	

		try{
			$parametros = Input::all();

			$resultado = Validador::validar($parametros, $this->reglas);
			if($resultado===true){
				$recurso = EvaluacionAnalisisFuncional::find($id);
				
				if(is_null($recurso)){
					$respuesta['http_status'] = 404;
					$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
				}else{
					$recurso->cuentaPublica = $parametros['cuenta-publica'];
					
					if(!$recurso->save()){
						throw new Exception("Ocurrio un error al intentar guardar los datos del recurso.", 1);
					}
				}
			}else{
				$respuesta = $resultado;
			}
		}catch(Exception $e){
			$respuesta['http_status'] = 500;
			$respuesta['data'] = array("data"=>"Ocurrio un error en el servidor al momento de realizar la operación.",'ex'=>$e->getMessage(),'line'=>$e->getLine(),'code'=>'S02');
		}
		return Response::json($respuesta['data'],$respuesta['http_status']);
	}
}