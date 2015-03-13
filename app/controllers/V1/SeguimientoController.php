<?php

namespace V1;

use SSA\Utilerias\Validador;
use SSA\Utilerias\Util;
use BaseController, Input, Response, DB, Sentry, Hash, Exception,DateTime;
use Proyecto,Componente,Actividad,Beneficiario,RegistroAvanceMetas,ComponenteMetaMes,ActividadMetaMes,RegistroAvanceBeneficiario;

class SeguimientoController extends BaseController {
	private $reglasBeneficiarios = array(
		'id-beneficiario'			=> 'required',
		'altaf' 					=> 'sometimes|required|integer|min:0',
		'altam' 					=> 'sometimes|required|integer|min:0',
		'bajaf' 					=> 'sometimes|required|integer|min:0',
		'bajam' 					=> 'sometimes|required|integer|min:0',
		'indigenaf'					=> 'sometimes|required|integer|min:0',
		'indigenam'					=> 'sometimes|required|integer|min:0',
		'inmigrantef' 				=> 'sometimes|required|integer|min:0',
		'inmigrantem' 				=> 'sometimes|required|integer|min:0',
		'mediaf' 					=> 'sometimes|required|integer|min:0',
		'mediam' 					=> 'sometimes|required|integer|min:0',
		'mestizaf' 					=> 'sometimes|required|integer|min:0',
		'mestizam'					=> 'sometimes|required|integer|min:0',
		'muyaltaf' 					=> 'sometimes|required|integer|min:0',
		'muyaltam' 					=> 'sometimes|required|integer|min:0',
		'muybajaf' 					=> 'sometimes|required|integer|min:0',
		'muybajam' 					=> 'sometimes|required|integer|min:0',
		'otrosf' 					=> 'sometimes|required|integer|min:0',
		'otrosm' 					=> 'sometimes|required|integer|min:0',
		'ruralf' 					=> 'sometimes|required|integer|min:0',
		'ruralm' 					=> 'sometimes|required|integer|min:0',
		'urbanaf' 					=> 'sometimes|required|integer|min:0',
		'urbanam' 					=> 'sometimes|required|integer|min:0'
	);

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		$http_status = 200;
		$data = array();

		$parametros = Input::all();
		if(isset($parametros['formatogrid'])){

			if(isset($parametros['grid'])){
				if($parametros['grid'] == 'rendicion-acciones'){
					$rows = Proyecto::with('componentes.actividades.registroAvance')->find($parametros['idProyecto']);
					$rows->componentes->load('registroAvance');
					$total = count($rows);
				}elseif($parametros['grid'] == 'rendicion-beneficiarios'){
					$rows = Beneficiario::with(array('registroAvance'=>function($query){
						$query->select('id','idProyectoBeneficiario','idTipoBeneficiario','sexo',DB::raw('sum(total) AS total'))
								->groupBy('idTipoBeneficiario','sexo');
					},'tipoBeneficiario'))->where('idProyecto','=',$parametros['idProyecto'])->get();
					$total = count($rows);
				}
			}else{
				$rows = Proyecto::getModel();
				$rows = $rows->where('idEstatusProyecto','=',5)
							->where('idClasificacionProyecto','=',$parametros['clasificacionProyecto'])
							->where('unidadResponsable','=',Sentry::getUser()->claveUnidad);
							//->where('idClasificacionProyecto','=',$)
				//$rows = $rows->with('registroAvance');
				$rows = $rows->with(array('registroAvance'=>function($query){
					$query->select('id','idProyecto','mes',DB::raw('sum(avanceMes) as avanceMes'),DB::raw('sum(planMejora) as planMejora'),DB::raw('count(idNivel) as registros'))->groupBy('idProyecto','mes');
				}));
				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
				
				if(isset($parametros['buscar'])){				
					$rows = $rows->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%');
					$total = $rows->count();
				}else{				
					$total = $rows->count();						
				}
				
				$rows = $rows->select('proyectos.id',DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0")) as clavePresup'),
				'nombreTecnico','catalogoClasificacionProyectos.descripcion AS clasificacionProyecto','proyectos.idEstatusProyecto',
					'catalogoEstatusProyectos.descripcion AS estatusProyecto','sentryUsers.username','proyectos.modificadoAl')
									->join('sentryUsers','sentryUsers.id','=','proyectos.creadoPor')
									->join('catalogoClasificacionProyectos','catalogoClasificacionProyectos.id','=','proyectos.idClasificacionProyecto')
									->join('catalogoEstatusProyectos','catalogoEstatusProyectos.id','=','proyectos.idEstatusProyecto')
									->orderBy('id', 'desc')
									->skip(($parametros['pagina']-1)*10)->take(10)
									->get();
			}
			
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

		if(isset($parametros['mostrar'])){
			if($parametros['mostrar'] == 'datos-proyecto-avance'){
				$recurso = Proyecto::with('datosFuncion','datosSubFuncion','datosProgramaPresupuestario','componentes.metasMesAgrupado'
					,'componentes.registroAvance','componentes.actividades.metasMesAgrupado','componentes.actividades.registroAvance')->find($id);
			}elseif($parametros['mostrar'] == 'datos-componente-avance'){
				$mes_actual = Util::obtenerMesActual();
				//Se obtienen las metas por mes del mes actual y las metas por mes totales agrupadas por jurisdicción
				$recurso = Componente::with(array('metasMesJurisdiccion'=>function($query) use ($mes_actual){
					$query->where('mes','<=',$mes_actual);
				},'registroAvance'=>function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				},'metasMes' => function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				},'unidadMedida'))->find($id);
			}elseif($parametros['mostrar'] == 'datos-actividad-avance'){
				$mes_actual = Util::obtenerMesActual();
				//Se obtienen las metas por mes del mes actual y las metas por mes totales agrupadas por jurisdicción
				$recurso = Actividad::with(array('metasMesJurisdiccion'=>function($query) use ($mes_actual){
					$query->where('mes','<=',$mes_actual);
				},'registroAvance'=>function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				},'metasMes' => function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				},'unidadMedida'))->find($id);
			}elseif($parametros['mostrar'] == 'datos-beneficiarios-avance'){
				$mes_actual = Util::obtenerMesActual();
				$recurso['acumulado'] = RegistroAvanceBeneficiario::where('idProyecto','=',$parametros['id-proyecto'])
														->where('idTipoBeneficiario','=',$id)
														->where('mes','<',$mes_actual)->groupBy('idTipoBeneficiario','sexo')
														->select('idTipoBeneficiario','sexo',DB::raw('sum(total) AS total'))->get();
				$recurso['beneficiario'] = Beneficiario::with(array('tipoBeneficiario','registroAvance'=>function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				}))->where('idProyecto','=',$parametros['id-proyecto'])->where('idTipoBeneficiario','=',$id)->get();
			}
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
			if($parametros['guardar'] == 'avance-metas'){
				$respuesta = $this->guardarAvance($parametros);
				if($respuesta['http_status'] != 200){
					throw new Exception("Error al procesar los datos", 1);
				}
			}elseif($parametros['guardar'] == 'avance-beneficiarios'){
				$respuesta = $this->guardarAvanceBeneficiario($parametros);
			}
		}catch(\Exception $ex){
			$respuesta['http_status'] = 500;	
			if($respuesta['data']['data'] == ''){
				$respuesta['data']['data'] = 'Ocurrio un error al intentar almacenar los datos';
			}
			if(strpos($ex->getMessage(), '{"field":') !== FALSE){
				$respuesta['data']['code'] = 'U00';
				$respuesta['data']['data'] = $ex->getMessage();
			}else{
				$respuesta['data']['ex'] = $ex->getMessage();
			}
			$respuesta['data']['line'] = $ex->getLine();
			if(!isset($respuesta['data']['code'])){
				$respuesta['data']['code'] = 'S03';
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
	public function update($id){
		//
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');
		try{
			$parametros = Input::all();

			if($parametros['guardar'] == 'avance-metas'){
				$respuesta = $this->guardarAvance($parametros,$id);
				if($respuesta['http_status'] != 200){
					throw new Exception("Error al procesar los datos", 1);
				}
			}elseif($parametros['guardar'] == 'avance-beneficiarios'){
				$respuesta = $this->guardarAvanceBeneficiario($parametros,TRUE);
			}
		}catch(\Exception $ex){
			$respuesta['http_status'] = 500;	
			if($respuesta['data']['data'] == ''){
				$respuesta['data']['data'] = 'Ocurrio un error al intentar almacenar los datos';
			}
			if(strpos($ex->getMessage(), '{"field":') !== FALSE){
				$respuesta['data']['code'] = 'U00';
				$respuesta['data']['data'] = $ex->getMessage();
			}else{
				$respuesta['data']['ex'] = $ex->getMessage();
			}
			$respuesta['data']['line'] = $ex->getLine();
			if(!isset($respuesta['data']['code'])){
				$respuesta['data']['code'] = 'S03';
			}
		}
		return Response::json($respuesta['data'],$respuesta['http_status']);
	}

	public function guardarAvanceBeneficiario($parametros, $es_editar = FALSE){
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');

		$validacion = Validador::validar(Input::all(), $this->reglasBeneficiarios);

		if($validacion === TRUE){
			$mes_actual = Util::obtenerMesActual();
			$recurso = Beneficiario::with(array('registroAvance'=>function($query) use ($mes_actual){
							$query->where('mes','=',$mes_actual);
						}))->where('idProyecto','=',$parametros['id-proyecto'])
						->where('idTipoBeneficiario','=',$parametros['id-beneficiario'])
						->get();

			$sexos_registrados = $recurso->lists('sexo');
			foreach ($sexos_registrados as $sexo) {
				$suma_zona		= $parametros['urbana'.$sexo] + $parametros['rural'.$sexo];
				$suma_poblacion	= $parametros['mestiza'.$sexo] + $parametros['indigena'.$sexo] + $parametros['inmigrante'.$sexo] + $parametros['otros'.$sexo];
				$suma_marginacion	= $parametros['muyalta'.$sexo] + $parametros['alta'.$sexo] + $parametros['media'.$sexo] + $parametros['baja'.$sexo] + $parametros['muybaja'.$sexo];

				if(($suma_zona != $suma_poblacion) || ($suma_poblacion != $suma_marginacion) || ($suma_marginacion != $suma_zona)){
					$respuesta['data'] = array('data'=>array(json_encode(array('field'=>'errorbeneficiarios','error'=>'Los totales capturados no corresponden entre si.'))),'code'=>'U00');
					$respuesta['http_status'] = 500;
					return $respuesta;
				}
			}
			//
			//
			$respuesta['data'] = DB::transaction(function() use ($recurso,$es_editar,$mes_actual,$parametros){
				$advertencia = '';
				foreach ($recurso as $beneficiario) {
					$sexo = $beneficiario->sexo;
					if($es_editar){
						$avance = $beneficiario->registroAvance[0];
					}else{
						$avance = new RegistroAvanceBeneficiario;
					}
					$avance->idProyecto 		= $beneficiario->idProyecto;
					$avance->idTipoBeneficiario	= $beneficiario->idTipoBeneficiario;
					$avance->sexo 				= $sexo;
					$avance->mes 				= $mes_actual;
					$avance->total 				= $parametros['urbana'.$sexo] + $parametros['rural'.$sexo];

					$avance->urbana 			= $parametros['urbana'.$sexo];
					$avance->rural 				= $parametros['rural'.$sexo];

					$avance->mestiza 			= $parametros['mestiza'.$sexo];
					$avance->indigena 			= $parametros['indigena'.$sexo];
					$avance->inmigrante 		= $parametros['inmigrante'.$sexo];
					$avance->otros 				= $parametros['otros'.$sexo];

					$avance->muyAlta 			= $parametros['muyalta'.$sexo];
					$avance->alta 				= $parametros['alta'.$sexo];
					$avance->media 				= $parametros['media'.$sexo];
					$avance->baja 				= $parametros['baja'.$sexo];
					$avance->muyBaja 			= $parametros['muybaja'.$sexo];

					$beneficiario->registroAvance()->save($avance);
				}
				$total_beneficiarios = $recurso->lists('total','sexo');
				$beneficiarios_acumulados = RegistroAvanceBeneficiario::where('idProyecto','=',$parametros['id-proyecto'])
															->where('idTipoBeneficiario','=',$parametros['id-beneficiario'])
															->where('mes','<=',$mes_actual)->groupBy('idTipoBeneficiario','sexo')
															->select('idTipoBeneficiario','sexo',DB::raw('sum(total) AS total'))->get();
				foreach ($beneficiarios_acumulados as $acumulado) {
					if($acumulado->total > $total_beneficiarios[$acumulado->sexo]){
						$advertencia = 'Los datos del avance de beneficiarios han sido guardados, sin embargo algunos totales capturados son mayores a los programados en el proyecto';
					}
				}
				return array('advertencia'=>$advertencia);
			});
		}else{
			$respuesta['http_status'] = $validacion['http_status'];
			$respuesta['data'] = $validacion['data'];
		}
		return $respuesta;
	}

	public function guardarAvance($parametros,$id = NULL){
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');
		$es_editar = FALSE;

		$mes_actual = Util::obtenerMesActual();

		if($id){
			$registro_avance = RegistroAvanceMetas::find($id);
			$es_editar = TRUE;
		}else{
			$registro_avance = new RegistroAvanceMetas;
		}

		//Se obtienen las metas por mes del mes actual y las metas por mes totales agrupadas por jurisdicción
		if($parametros['nivel'] == 'componente'){
			$accion_metas = Componente::with(array('metasMesJurisdiccion'=>function($query) use ($mes_actual){
				$query->where('mes','<=',$mes_actual);
			},'metasMes' => function($query) use ($mes_actual){
				$query->where('mes','=',$mes_actual);
			}))->find($parametros['id-accion']);
			$registro_avance->nivel = 1;
		}else{
			$accion_metas = Actividad::with(array('metasMesJurisdiccion'=>function($query) use ($mes_actual){
				$query->where('mes','<=',$mes_actual);
			},'metasMes' => function($query) use ($mes_actual){
				$query->where('mes','=',$mes_actual);
			}))->find($parametros['id-accion']);
			$registro_avance->nivel = 2;
		}

		$registro_avance->idProyecto = $accion_metas->idProyecto;
		$registro_avance->idNivel = $parametros['id-accion'];
		$registro_avance->mes = $mes_actual;

		$conteo_alto_bajo_avance = 0;
		$faltan_campos = array();
		
		$metas_acumuladas = $accion_metas->metasMesJurisdiccion->lists('meta','claveJurisdiccion');
		$avances_acumulados = $accion_metas->metasMesJurisdiccion->lists('avance','claveJurisdiccion');
		
		$guardar_metas = array();
		$total_avance = 0;
		foreach ($accion_metas->metasMes as $metas) {
			if($parametros['avance'][$metas->claveJurisdiccion] == ''){
				$faltan_campos[] = json_encode(array('field'=>'avance_'.$metas->claveJurisdiccion,'error'=>'Este campo es requerido'));
			}else{
				$meta_acumulada = $metas_acumuladas[$metas->claveJurisdiccion];
				$avance_acumulado = $avances_acumulados[$metas->claveJurisdiccion];

				if($metas->avance){
					$avance_acumulado -= $metas->avance;
				}

				$avance_acumulado += $parametros['avance'][$metas->claveJurisdiccion];

				if($meta_acumulada > 0 && $metas->meta > 0){
					$porcentaje_avance = (( $avance_acumulado  / $meta_acumulada ) * 100);
					if($porcentaje_avance < 90 || $porcentaje_avance > 110){
						$conteo_alto_bajo_avance++;
					}
				}elseif($meta_acumulada == 0 && $parametros['avance'][$metas->claveJurisdiccion] > 0){
					$conteo_alto_bajo_avance++;
				}

				$total_avance += $parametros['avance'][$metas->claveJurisdiccion];
				$metas->avance = $parametros['avance'][$metas->claveJurisdiccion];
				$guardar_metas[] = $metas;

				if($metas->meta == 0 && $metas->avance > 0){
					$conteo_alto_bajo_avance++;
				}
			}
		}
		
		//Si las metas capturadas no fueron puestas en la programación entonces ahi que agregarlas ya que no estan en la tabla
		$jurisdicciones_capturadas = $accion_metas->metasMes->lists('claveJurisdiccion');
		$jurisdicciones_formulario = array_keys($parametros['avance']);
		$metas_nuevas = array_diff($jurisdicciones_formulario, $jurisdicciones_capturadas);
		foreach ($metas_nuevas as $jurisdiccion) {
			if($parametros['avance'][$jurisdiccion] > 0){
				if($parametros['nivel'] == 'componente'){
					$meta = new ComponenteMetaMes;
					//$meta->idComponente = $parametros['id-accion'];
				}else{
					$meta = new ActividadMetaMes;
					//$meta->idActividad = $parametros['id-accion'];
				}
				$meta->claveJurisdiccion = $jurisdiccion;
				$meta->mes = $mes_actual;
				$meta->meta = 0;
				$meta->avance = $parametros['avance'][$jurisdiccion];
				$meta->idProyecto = $accion_metas->idProyecto;
				$guardar_metas[] = $meta;
				$conteo_alto_bajo_avance++;
				$total_avance += $parametros['avance'][$jurisdiccion];
			}
		}
		
		if(trim($parametros['analisis-resultados']) == ''){
			$faltan_campos[] = json_encode(array('field'=>'analisis-resultados','error'=>'Este campo es requerido.'));
		}else{
			$registro_avance->analisisResultados = $parametros['analisis-resultados'];
		}

		if($conteo_alto_bajo_avance){
			if(trim($parametros['justificacion-acumulada']) == ''){
				$faltan_campos[] = json_encode(array('field'=>'justificacion-acumulada','error'=>'Este campo es requerido.'));
			}else{
				$registro_avance->justificacionAcumulada = $parametros['justificacion-acumulada'];
			}
			$registro_avance->planMejora = 1;
		}else{
			$registro_avance->justificacionAcumulada = 'El avance se encuentra dentro de los parametros establecidos';
			$registro_avance->planMejora = 0;
		}

		if(count($faltan_campos)){
			$respuesta['http_status'] = 500;
			$respuesta['data']['code'] = 'U00';
			$respuesta['data']['data'] = $faltan_campos;
			return $respuesta;
			//throw new Exception("Error en la captura", 1);
		}

		$registro_avance->avanceMes = $total_avance;
		
		$respuesta['data'] = DB::transaction(function() use ($registro_avance, $guardar_metas, $accion_metas){
			if($registro_avance->save()){
				$accion_metas->metasMes()->saveMany($guardar_metas);
				return array('data'=>$registro_avance);
			}else{
				//No se pudieron guardar los datos del proyecto
				$respuesta['data']['code'] = 'S01';
				throw new Exception("Error al guardar los datos de la FIBAP: Error en el guardado de la ficha", 1);
			}
		});

		return $respuesta;
	}
}