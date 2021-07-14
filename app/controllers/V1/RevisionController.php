<?php

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, Hash, Exception;
use Proyecto, Componente, Actividad, Beneficiario, FIBAP, ComponenteMetaMes, ActividadMetaMes, Region, Municipio, Jurisdiccion, Programa, 
	FibapDatosProyecto, Titular, ComponenteDesglose, ActividadDesglose, ProyectoComentario, Accion, DistribucionPresupuesto, ProyectoFinanciamiento;

class RevisionController extends BaseController {
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
			if(isset($parametros['estatusProyecto']) && $parametros['estatusProyecto']){
				$rows = $rows->wherein('idEstatusProyecto',array($parametros['estatusProyecto']));
			}else{
				$rows = $rows->wherein('idEstatusProyecto',array(2, 3, 4, 5));
			}
			
			
			if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
			
			$usuario = Sentry::getUser();
			/*
			if($usuario->proyectosAsignados){
				if($usuario->proyectosAsignados->proyectos){
					$proyectos = explode('|',$usuario->proyectosAsignados->proyectos);
					$rows = $rows->whereIn('proyectos.id',$proyectos);
				}
			}
			*/
			if($usuario->claveUnidad){
				$unidades = explode('|',$usuario->claveUnidad);
				$rows = $rows->whereIn('unidadResponsable',$unidades);
			}
			
			if(isset($parametros['buscar'])){				
				$rows = $rows->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%');
				$total = $rows->count();
			}else{				
				$total = $rows->count();						
			}
			
			$rows = $rows->select('proyectos.id',DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,origenAsignacion,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0")) as clavePresup'),
				'nombreTecnico','catalogoClasificacionProyectos.descripcion AS clasificacionProyecto','catalogoUnidadesResponsables.descripcion AS unidadResponsable',
				'catalogoEstatusProyectos.descripcion AS estatusProyecto','proyectos.idEstatusProyecto','sentryUsers.username','proyectos.modificadoAl')
								->join('sentryUsers','sentryUsers.id','=','proyectos.creadoPor')
								->join('catalogoClasificacionProyectos','catalogoClasificacionProyectos.id','=','proyectos.idClasificacionProyecto')
								->join('catalogoEstatusProyectos','catalogoEstatusProyectos.id','=','proyectos.idEstatusProyecto')
								->join('catalogoUnidadesResponsables','catalogoUnidadesResponsables.clave','=','proyectos.unidadResponsable')
								->orderBy('proyectos.modificadoAl','desc')
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
						'unidadResponsable'		=> $row->unidadResponsable,
						'estatusProyecto'		=> $row->estatusProyecto,
						'idEstatusProyecto'		=> $row->idEstatusProyecto,
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
			if(Sentry::getUser()->claveUnidad){
				$unidades = explode('|',Sentry::getUser()->claveUnidad);
			}
			$rows = Proyecto::getModel();
			$rows = $rows->select('proyectos.id',DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,origenAsignacion,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0")) as clavePresup'),
				'nombreTecnico','catalogoClasificacionProyectos.descripcion AS clasificacionProyecto',
				'catalogoEstatusProyectos.descripcion AS estatusProyecto','sentryUsers.username')
								->join('sentryUsers','sentryUsers.id','=','proyectos.creadoPor')
								->join('catalogoClasificacionProyectos','catalogoClasificacionProyectos.id','=','proyectos.idClasificacionProyecto')
								->join('catalogoEstatusProyectos','catalogoEstatusProyectos.id','=','proyectos.idEstatusProyecto')
								->leftjoin('fibap','proyectos.id','=','fibap.idProyecto')
								->orderBy('proyectos.actualizadoAl','desc')
								->wherein('proyectos.idClasificacionProyecto',array(2, 4))
								->whereIn('unidadResponsable',$unidades)
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
			//throw new Exception(json_encode($parametros),1);
			if($parametros['ver'] == 'componente'){
				$recurso = Componente::with('actividades.unidadMedida','metasMes')->find($id);
			}elseif($parametros['ver'] == 'financiamiento'){
				$recurso = ProyectoFinanciamiento::with('subFuentesFinanciamiento')->find($id);
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
			}elseif($parametros['ver'] == 'detalles-presupuesto'){

				if($parametros['tipo-accion'] == 'componente'){
					$desglose = ComponenteDesglose::with('metasMes','beneficiarios.tipoBeneficiario')->find($id);
				}else{
					$desglose = ActividadDesglose::with('metasMes','beneficiarios.tipoBeneficiario')->find($id);
				}
				
				$recurso = Accion::with('partidas')->find($desglose->idAccion);
				
				$calendarizado = DistribucionPresupuesto::where('idAccion','=',$desglose->idAccion)
													->whereIn('idObjetoGasto',$recurso->partidas->lists('id'))
													->where('claveJurisdiccion','=',$desglose->claveJurisdiccion);
				if($desglose->claveJurisdiccion != 'OC'){
					$calendarizado = $calendarizado->where('claveMunicipio','=',$desglose->claveMunicipio)
												->where('claveLocalidad','=',$desglose->claveLocalidad);
				}
				$calendarizado = $calendarizado->get();
				$recurso['desglose'] = $desglose;
				$recurso['calendarizado'] = $calendarizado;
				// 
			}
			
		}else{
			$recurso = Proyecto::contenidoCompleto()->find($id);
			$recurso->componentes->load('comportamientoAccion','tipoValorMeta','unidadMedida','dimension','tipoIndicador','metasMes','formula','frecuencia','actividades','entregable','entregableTipo','entregableAccion','accion.partidas','accion.propuestasFinanciamiento.origen','accion.distribucionPresupuesto','accion.desglosePresupuestoComponente');
			$recurso->load('fuentesFinanciamiento.fondoFinanciamiento','fuentesFinanciamiento.fuenteFinanciamiento','fuentesFinanciamiento.subFuentesFinanciamiento');
			$recurso->beneficiarios->load('tipoCaptura');
			$recurso->load('comentarios');

			$recurso->datos_programa_presupuestario_indicadores = Programa::where('claveProgramaPresupuestario','=',$recurso->programaPresupuestario)->where('idEstatus','=',5)->with('indicadoresDescripcion')->first();
			
			if($recurso->idClasificacionProyecto == 2){
				$recurso->load('fibap');
				if($recurso->fibap)
				{
					$recurso->fibap->load('documentos','propuestasFinanciamiento','antecedentesFinancieros','distribucionPresupuestoAgrupado');
					$recurso->fibap->distribucionPresupuestoAgrupado->load('objetoGasto');
				}
			}
			
			/*$recurso->componentes->load(array('actividades','formula','dimension','frecuencia','tipoIndicador','unidadMedida','entregable','entregableTipo','entregableAccion','desgloseCompleto'));
			foreach ($recurso->componentes as $key => $componente) {
				$recurso->componentes[$key]->actividades->load(array('formula','dimension','frecuencia','tipoIndicador','unidadMedida'));
			}*/
			
			/*$recurso->componentes->load(array('actividades','formula','dimension','frecuencia','tipoIndicador','unidadMedida','entregable','entregableTipo','entregableAccion','desgloseCompleto'));*/
			foreach ($recurso->componentes as $key => $componente) {
				$recurso->componentes[$key]->actividades->load(array('formula','dimension','frecuencia','tipoIndicador','comportamientoAccion','tipoValorMeta','unidadMedida','metasMes','accion.partidas','accion.propuestasFinanciamiento.origen','accion.distribucionPresupuesto','accion.desglosePresupuestoActividad'));
			}
			
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
				$recurso['jurisdicciones'] = array('OC'=>'OC') + $jurisdicciones->lists('clave','clave');
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
		$nuevoComentario->tipoComentario = $parametros['tipocomentario'];
		
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
				$recurso = Proyecto::find($id);
				if(is_null($recurso)){
					$respuesta['http_status'] = 404;
					$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
				}else{
					$recurso->idEstatusProyecto = 4;
					$recurso->save();
				}
			}
			else if($parametros['actualizarproyecto']=="regresar") //Poner estatus 3 (Regreso a corrección)
			{
				$recurso = Proyecto::find($id);
				if(is_null($recurso)){
					$respuesta['http_status'] = 404;
					$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
				}else{
					$recurso->idEstatusProyecto = 3;
					$recurso->save();
				}
			}
			else if($parametros['actualizarproyecto']=="firmar") //Poner estatus 5 (Enviar a firma)
			{
				$recurso = Proyecto::find($id);
				if(is_null($recurso)){
					$respuesta['http_status'] = 404;
					$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
				}else{
					$recurso->idEstatusProyecto = 5;
					$recurso->save();
				}
			}
			
		}
		else
		{
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