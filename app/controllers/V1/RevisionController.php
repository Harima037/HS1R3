<?php

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, Hash, Exception;
use Proyecto, Componente, Actividad, Beneficiario, FIBAP, ComponenteMetaMes, ActividadMetaMes, Region, Municipio, Jurisdiccion, 
	FibapDatosProyecto, Titular, ComponenteDesglose, ProyectoComentario;

class RevisionController extends BaseController {
	private $reglasProyecto = array(
		'funciongasto'				=> 'required',
		'clasificacionproyecto'		=> 'required',
		'nombretecnico'				=> 'sometimes|required',
		'ejercicio'					=> 'required',
		'tipoproyecto'				=> 'required',
		'cobertura'					=> 'sometimes|required',
		'municipio'					=> 'sometimes|required_if:cobertura,2|digits_between:1,3',
		'region'					=> 'sometimes|required_if:cobertura,3|alpha',
		'tipoaccion'				=> 'required',
		'unidadresponsable'			=> 'required|digits:2',
		'programasectorial'			=> 'required|alpha_num|size:1',
		'programapresupuestario'	=> 'sometimes|required|alpha_num|size:3',
		'programaespecial'			=> 'required|alpha_num|size:3',
		'actividadinstitucional'	=> 'required|alpha_num|size:3',
		'proyectoestrategico'		=> 'required|alpha_num|size:1',
		'vinculacionped'			=> 'sometimes|required',
		'tipobeneficiario'			=> 'sometimes|required',
		'totalbeneficiariosf'		=> 'sometimes|required|integer|min:0',
		'totalbeneficiariosm'		=> 'sometimes|required|integer|min:0',
		'altaf' 					=> 'required|integer|min:0',
		'altam' 					=> 'required|integer|min:0',
		'bajaf' 					=> 'required|integer|min:0',
		'bajam' 					=> 'required|integer|min:0',
		'indigenaf'					=> 'required|integer|min:0',
		'indigenam'					=> 'required|integer|min:0',
		'inmigrantef' 				=> 'required|integer|min:0',
		'inmigrantem' 				=> 'required|integer|min:0',
		'mediaf' 					=> 'required|integer|min:0',
		'mediam' 					=> 'required|integer|min:0',
		'mestizaf' 					=> 'required|integer|min:0',
		'mestizam'					=> 'required|integer|min:0',
		'muyaltaf' 					=> 'required|integer|min:0',
		'muyaltam' 					=> 'required|integer|min:0',
		'muybajaf' 					=> 'required|integer|min:0',
		'muybajam' 					=> 'required|integer|min:0',
		'otrosf' 					=> 'required|integer|min:0',
		'otrosm' 					=> 'required|integer|min:0',
		'ruralf' 					=> 'required|integer|min:0',
		'ruralm' 					=> 'required|integer|min:0',
		'urbanaf' 					=> 'required|integer|min:0',
		'urbanam' 					=> 'required|integer|min:0'
	);

	private $reglasComponente = array(
		'denominador-ind-componente' 	=> 'required',
		'descripcion-ind-componente' 	=> 'required',
		'descripcion-obj-componente' 	=> 'required',
		'dimension-componente' 			=> 'required',
		'formula-componente' 			=> 'required',
		'frecuencia-componente' 		=> 'required',
		'interpretacion-componente' 	=> 'required',
		'meta-componente' 				=> 'required|numeric|min:0',
		'numerador-componente' 			=> 'required|numeric|min:1',
		'numerador-ind-componente' 		=> 'required',
		'supuestos-componente' 			=> 'required',
		'tipo-ind-componente' 			=> 'required',
		'anio-base-componente' 			=> 'integer|min:0',
		'denominador-componente' 		=> 'required_if:formula-componente,1,2,3,4,5,6|numeric|min:0',
		'linea-base-componente' 		=> 'numeric|min:0',
		'trim1-componente' 				=> 'numeric',
		'trim2-componente' 				=> 'numeric',
		'trim3-componente' 				=> 'numeric',
		'trim4-componente' 				=> 'numeric',
		'unidad-medida-componente' 		=> 'required',
		'verificacion-componente' 		=> 'required'
	);

	private $reglasActividad = array(
		'denominador-ind-actividad' 	=> 'required',
		'descripcion-ind-actividad' 	=> 'required',
		'descripcion-obj-actividad' 	=> 'required',
		'dimension-actividad' 			=> 'required',
		'formula-actividad' 			=> 'required',
		'frecuencia-actividad' 			=> 'required',
		'interpretacion-actividad' 		=> 'required',
		'meta-actividad' 				=> 'required|numeric|min:0',
		'numerador-actividad' 			=> 'required|numeric|min:1',
		'numerador-ind-actividad' 		=> 'required',
		'supuestos-actividad' 			=> 'required',
		'tipo-ind-actividad' 			=> 'required',
		'anio-base-actividad' 			=> 'integer|min:0',
		'denominador-actividad' 		=> 'required_if:formula-actividad,1,2,3,4,5,6|numeric|min:0',
		'linea-base-actividad' 			=> 'numeric|min:0',
		'trim1-actividad' 				=> 'numeric',
		'trim2-actividad' 				=> 'numeric',
		'trim3-actividad' 				=> 'numeric',
		'trim4-actividad' 				=> 'numeric',
		'unidad-medida-actividad' 		=> 'required',
		'verificacion-actividad' 		=> 'required'
	);
	
	private $reglasComentario = array(
			'idproyecto' => 'required',
			'idcampo' => 'required',
			'comentario' => 'required'
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

			$rows = Proyecto::getModel();
			//$rows = $rows->where('unidadResponsable','=',Sentry::getUser()->claveUnidad);
			$rows = $rows->where('idEstatusProyecto','=','2');
			
			if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
			
			if(isset($parametros['buscar'])){				
				$rows = $rows->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%');
				$total = $rows->count();
			}else{				
				$total = $rows->count();						
			}
			
			$rows = $rows->select('proyectos.id',DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0")) as clavePresup'),
				'nombreTecnico','catalogoClasificacionProyectos.descripcion AS clasificacionProyecto',
				'catalogoEstatusProyectos.descripcion AS estatusProyecto','sentryUsers.username','proyectos.modificadoAl')
								->join('sentryUsers','sentryUsers.id','=','proyectos.creadoPor')
								->join('catalogoClasificacionProyectos','catalogoClasificacionProyectos.id','=','proyectos.idClasificacionProyecto')
								->join('catalogoEstatusProyectos','catalogoEstatusProyectos.id','=','proyectos.idEstatusProyecto')
								->orderBy('id', 'desc')
								->skip(($parametros['pagina']-1)*10)->take(10)
								->get();
			$proyectos = array();
			foreach ($rows as $row) {
				# code...
				$proyectos[] = array(
						'id' 					=> $row->id,
						'clavePresup' 			=> $row->clavePresup,
						'nombreTecnico' 		=> $row->nombreTecnico,
						'clasificacionProyecto'	=> $row->clasificacionProyecto,
						'estatusProyecto'		=> $row->estatusProyecto,
						'username'				=> $row->username,
						'modificadoAl'			=> date_format($row->modificadoAl,'d/m/Y')
					);
			}
			$data = array('resultados'=>$total,'data'=>$proyectos);

			if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
			}
			
			return Response::json($data,$http_status);
		}elseif(isset($parametros['proyectos_inversion'])){
			$rows = Proyecto::getModel();
			$rows = $rows->select('proyectos.id',DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0")) as clavePresup'),
				'nombreTecnico','catalogoClasificacionProyectos.descripcion AS clasificacionProyecto',
				'catalogoEstatusProyectos.descripcion AS estatusProyecto','sentryUsers.username')
								->join('sentryUsers','sentryUsers.id','=','proyectos.creadoPor')
								->join('catalogoClasificacionProyectos','catalogoClasificacionProyectos.id','=','proyectos.idClasificacionProyecto')
								->join('catalogoEstatusProyectos','catalogoEstatusProyectos.id','=','proyectos.idEstatusProyecto')
								->leftjoin('fibap','proyectos.id','=','fibap.idProyecto')
								->orderBy('proyectos.id','desc')
								->where('proyectos.idClasificacionProyecto','=',DB::raw('2'))
								->where('unidadResponsable','=',Sentry::getUser()->claveUnidad)
								->whereNull('fibap.id')
								->get();
			$data = array('data'=>$rows);
			
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
	public function show($id)
	{
		//
		$http_status = 200;
		$data = array();

		$parametros = Input::all();

		if($parametros){
			if($parametros['ver'] == 'componente'){
				$recurso = Componente::with('actividades.unidadMedida','metasMes')->find($id);
			}elseif ($parametros['ver'] == 'lista-desglose') {

				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
				$pagina = $parametros['pagina'];
				$rows = ComponenteDesglose::listarDatos()->where('idComponente','=',$id);

				if(isset($parametros['buscar'])){				
					$rows = $rows->where(function($query) use ($parametros){
									$query->where('jurisdiccion.nombre','like','%'.$parametros['buscar'].'%')
										->orWhere('municipio.nombre','like','%'.$parametros['buscar'].'%')
										->orWhere('localidad.nombre','like','%'.$parametros['buscar'].'%');
								});
					$total = $rows->count();

					$queries = DB::getQueryLog();
					$data['query'] = print_r(end($queries),true);
				}else{				
					$total = $rows->count();						
				}
				$data['total'] = $total;
				$recurso = $rows->orderBy('id', 'desc')
							->skip(($parametros['pagina']-1)*10)->take(10)
							->get();

			}elseif($parametros['ver'] == 'actividad'){

				$recurso = Actividad::with('metasMes')->find($id);

			}elseif($parametros['ver'] == 'proyecto'){

				$recurso = Proyecto::contenidoCompleto()->find($id);
				//$comentarios = ProyectoComentario::where('idProyecto', '=', $id)->get();								
				if($recurso){
					
					if($recurso->idClasificacionProyecto == 2){
						$recurso->load('fibap');
						if($recurso->fibap){
							$recurso->fibap->load('documentos','propuestasFinanciamiento','antecedentesFinancieros','distribucionPresupuestoAgrupado');
							$recurso->fibap->distribucionPresupuestoAgrupado->load('objetoGasto');
						}
					}
					$recurso->componentes->load(array('actividades','formula','dimension','frecuencia','tipoIndicador','unidadMedida','entregable','entregableTipo','entregableAccion','desgloseCompleto'));
					foreach ($recurso->componentes as $key => $componente) {
						$recurso->componentes[$key]->actividades->load(array('formula','dimension','frecuencia','tipoIndicador','unidadMedida'));
					}
				}

			}elseif($parametros['ver'] == 'datos-fibap'){
				$recurso = FibapDatosProyecto::where('idFibap','=',$id)->get();
				$recurso = $recurso[0];
			}
		}else{
			$recurso = Proyecto::contenidoCompleto()->find($id);
			$recurso->componentes->load('unidadMedida');
			$recurso->load('comentarios');
			if($recurso->idCobertura == 1){ //Cobertura Estado => Todos las Jurisdicciones
				$jurisdicciones = Jurisdiccion::all();
			}elseif($recurso->idCobertura == 2){ //Cobertura Municipio => La Jurisdiccion a la que pertenece el Municipio
				$jurisdicciones = Municipio::obtenerJurisdicciones($recurso->claveMunicipio)->get();
			}elseif($recurso->idCobertura == 3){ //Cobertura Region => Las Jurisdicciones de los municipios pertencientes a la Region
				$jurisdicciones = Region::obtenerJurisdicciones($recurso->claveRegion)->get();
			}
		}

		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$recurso = $recurso->toArray();
			if(!$parametros){
				$recurso['jurisdicciones'] = array('OC'=>'O.C.') + $jurisdicciones->lists('clave','clave');
			}
			$data["data"] = $recurso;
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
					
		$nuevoComentario = new ProyectoComentario;
		
		$nuevoComentario->idProyecto = $parametros['idproyecto'];
		$nuevoComentario->idCampo = $parametros['idcampo'];
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

		$recurso = ProyectoComentario::find($id);
		
		
		if(is_null($recurso)){
			$respuesta['http_status'] = 404;
			$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$recurso->idProyecto = $parametros['idproyecto'];
			$recurso->idCampo = $parametros['idcampo'];
			$recurso->observacion = $parametros['comentario'];
			
			$Resultado = Validador::validar($parametros, $this->reglasComentario);
						
			if($Resultado===true)
				$recurso->save();
			else
			{
				$respuesta['http_status'] = 500;
				$respuesta = $Resultado;
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
			$recurso = ProyectoComentario::where('id','=',$id)->delete();
			
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

}