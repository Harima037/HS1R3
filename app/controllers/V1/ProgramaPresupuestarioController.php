<?php

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, Hash, Exception,DateTime;
use Programa;

class ProgramaPresupuestarioController extends ProyectosController {
	private $reglasPrograma = array(
		'odm'						=> 'required',
		'modalidad'					=> 'required',
		'fecha-inicio'				=> 'required',
		'fecha-termino'				=> 'required',
		'resultados-esperados'		=> 'required',
		'enfoque-potencial'			=> 'required',
		'cuantificacion-potencial'	=> 'required',
		'enfoque-objetivo'			=> 'required',
		'cuantificacion-objetivo'	=> 'required',
		'justificacion-programa'	=> 'required',
		'programa-presupuestario'	=> 'required'
	);

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		//
		$http_status = 200;
		$data = array();

		$parametros = Input::all();

		if(isset($parametros['formatogrid'])){

			$rows = Programa::getModel();

			if(Sentry::getUser()->claveProgramaPresupuestario){
				$rows = $rows->where('claveProgramaPresupuestario','=',Sentry::getUser()->claveProgramaPresupuestario);
			}
			
			if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
			
			if(isset($parametros['buscar'])){				
				//$rows = $rows->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%');
				$total = $rows->count();
			}else{				
				$total = $rows->count();
			}
			
			$rows = $rows->select('programa.id','catalogoProgramasPresupuestales.clave','catalogoProgramasPresupuestales.descripcion','programa.fechaInicio',
						'programa.fechaTermino','programa.creadoPor','sentryUsers.username','programa.modificadoAl')
						->join('sentryUsers','sentryUsers.id','=','programa.actualizadoPor')
						->join('catalogoProgramasPresupuestales','catalogoProgramasPresupuestales.clave','=','programa.claveProgramaPresupuestario')
						//->join('catalogoEstatusProyectos','catalogoEstatusProyectos.id','=','proyectos.idEstatusProyecto')
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
		
		$rows = Proyecto::all();

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
	public function show($id){
		//
		$http_status = 200;
		$data = array();

		$parametros = Input::all();

		if($parametros){
			
		}else{
			
		}

		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$data["data"] = $recurso;
		}

		return Response::json($data,$http_status);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(){
		//
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');

		$parametros = Input::all();

		try{
			if($parametros['guardar'] == 'programa'){
				$validacion = Validador::validar(Input::all(), $this->reglasPrograma);
				if($validacion === TRUE){
					$recurso = new Programa;

					$recurso->claveProgramaPresupuestario = $parametros['programa-presupuestario'];
					$recurso->idOdm = $parametros['odm'];
					$recurso->idModalidad = $parametros['modalidad'];
					$recurso->ejercicio = date('Y');
					$recurso->fechaInicio = $parametros['fecha-inicio'];
					$recurso->fechaTermino = $parametros['fecha-termino'];
					$recurso->resultadosEsperados = $parametros['resultados-esperados'];
					$recurso->areaEnfoquePotencial = $parametros['enfoque-potencial'];
					$recurso->areaEnfoqueObjetivo = $parametros['enfoque-objetivo'];
					$recurso->cuantificacionEnfoquePotencial = $parametros['cuantificacion-potencial'];
					$recurso->cuantificacionEnfoqueObjetivo = $parametros['cuantificacion-objetivo'];
					$recurso->justificacionPrograma = $parametros['justificacion-programa'];

					if($recurso->save()){
						$respuesta['data'] = $recurso;
					}else{
						$respuesta['http_status'] = 500;
						$respuesta['data'] = array('data'=>'Error al intentar guardar el programa','code'=>'S01');
					}
				}else{
					//La Validación del Formulario encontro errores
					$respuesta['http_status'] = $validacion['http_status'];
					$respuesta['data'] = $validacion['data'];
				}
			}
		}catch(\Exception $ex){
			$respuesta['http_status'] = 500;
			if($respuesta['data']['data'] == ''){
				$respuesta['data']['data'] = 'Ocurrio un error al intentar almacenar los datos.';
			}
			$respuesta['data']['ex'] = $ex->getMessage();
			if(!isset($respuesta['data']['code'])){
				$respuesta['data']['code'] = 'S03';
			}
		}
		return Response::json($respuesta['data'],$respuesta['http_status']);
	}
}