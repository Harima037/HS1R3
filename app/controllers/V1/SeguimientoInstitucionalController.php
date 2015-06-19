<?php

namespace V1;

use SSA\Utilerias\Validador;
use SSA\Utilerias\Util;
use BaseController, Input, Response, DB, Sentry, Hash, Exception,DateTime,Mail;
use Proyecto,Componente,Actividad,Beneficiario,RegistroAvanceMetas,ComponenteMetaMes,ActividadMetaMes,RegistroAvanceBeneficiario,EvaluacionAnalisisFuncional,EvaluacionPlanMejora,EvaluacionComentario,EvaluacionProyectoMes,ComponenteDesglose;

class SeguimientoInstitucionalController extends BaseController {
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

	private $reglasAnalisisFuncional = array(
		'analisis-resultado'	=> 'required',
		'beneficiarios'			=> 'required',
		'justificacion-global'	=> 'required'
	);

	private $reglasPlanMejora = array(
		'accion-mejora'					=> 'required',
		'grupo-trabajo'					=> 'required',
		'documentacion-comprobatoria'	=> 'required',
		'fecha-inicio'					=> 'required|date',
		'fecha-termino'					=> 'required|date',
		'fecha-notificacion'			=> 'required|date'
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
	public function index(){
		$http_status = 200;
		$data = array();

		$parametros = Input::all();
		if(isset($parametros['formatogrid'])){

			if(isset($parametros['grid'])){
				if($parametros['grid'] == 'rendicion-acciones'){
					$rows = Proyecto::with('componentes.actividades.registroAvance','responsableInformacion')->find($parametros['idProyecto']);
					$rows->componentes->load('registroAvance');
					$total = count($rows);
				}elseif($parametros['grid'] == 'rendicion-beneficiarios'){
					$rows = Beneficiario::with(array('comentarios','registroAvance'=>function($query){
						$query->select('id','idProyectoBeneficiario','idTipoBeneficiario','sexo',DB::raw('sum(total) AS total'))
								->groupBy('idTipoBeneficiario','sexo');
					},'tipoBeneficiario'))->where('idProyecto','=',$parametros['idProyecto'])->get();
					$total = count($rows);
				}
			}else{
				$mes_actual = Util::obtenerMesActual();
				$rows = Proyecto::getModel();
				$rows = $rows->where('idEstatusProyecto','=',5)
							->where('idClasificacionProyecto','=',$parametros['clasificacionProyecto']);
							//->where('unidadResponsable','=',Sentry::getUser()->claveUnidad);
							//->where('idClasificacionProyecto','=',$)
				//$rows = $rows->with('registroAvance');
				$rows = $rows->with(array('registroAvance'=>function($query){
					$query->select('id','idProyecto','mes',DB::raw('sum(avanceMes) as avanceMes'),DB::raw('sum(planMejora) as planMejora'),DB::raw('count(idNivel) as registros'))->groupBy('idProyecto','mes');
				}));

				$rows = $rows->wherein('evaluacionProyectoMes.idEstatus', array(2, 4));

				$rows = $rows->join('evaluacionProyectoMes', function($join) use($mes_actual)
									{
											$join->on('proyectos.id', '=', 'evaluacionProyectoMes.idProyecto')
											->where('evaluacionProyectoMes.mes', '=', $mes_actual)
											->where('evaluacionProyectoMes.anio', '=', date('Y'));
									});

				$usuario = Sentry::getUser();
				if($usuario->proyectosAsignados){
					if($usuario->proyectosAsignados->proyectos){
						$proyectos = explode('|',$usuario->proyectosAsignados->proyectos);
						$rows = $rows->whereIn('proyectos.id',$proyectos);
					}
				}

				if($usuario->claveUnidad){
					$unidades = explode('|',$usuario->claveUnidad);
					$rows = $rows->whereIn('unidadResponsable',$unidades);
				}
				$rows = $rows->with(array('registroAvance'=>function($query){
					$query->select('id','idProyecto','mes',DB::raw('sum(avanceMes) as avanceMes'),DB::raw('sum(planMejora) as planMejora'),DB::raw('count(idNivel) as registros'))->groupBy('idProyecto','mes');
				},'evaluacionMeses'=>function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
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
		
		$mes_actual = Util::obtenerMesActual();

		if(isset($parametros['mostrar'])){
			if($parametros['mostrar'] == 'datos-proyecto-avance'){
				$recurso = Proyecto::with(array('datosFuncion','datosSubFuncion','datosProgramaPresupuestario','componentes.metasMesAgrupado'
					,'componentes.registroAvance','componentes.actividades.metasMesAgrupado','componentes.actividades.registroAvance','evaluacionMeses'=>function($query) use ($mes_actual){
						$query->where('mes','=',$mes_actual);
						}))->find($id);
			}elseif ($parametros['mostrar'] == 'datos-municipio-avance') {
				//$id = idComponente y $parametros['clave-municipio'] y $parametros['nivel'] = 'componente'
				
				if($parametros['nivel'] == 'componente'){
					$recurso = ComponenteDesglose::listarDatos()->where('claveMunicipio','=',$parametros['clave-municipio'])
													->where('idComponente','=',$id);
				}
				$recurso = $recurso->with(array('metasMes'=>function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				},'metasMesAcumuladas'=>function($query) use ($mes_actual){
					$query->where('mes','<=',$mes_actual);
				}))->get();
			}elseif($parametros['mostrar'] == 'datos-metas-avance'){
				if($parametros['nivel'] == 'componente'){
					$recurso = Componente::getModel();
				}else{
					$recurso = Actividad::getModel();
				}
				//Se obtienen las metas por mes del mes actual y las metas por mes totales agrupadas por jurisdicción
				$recurso = $recurso->with(array('metasMesJurisdiccion'=>function($query) use ($mes_actual){
					$query->where('mes','<=',$mes_actual);
				},'registroAvance'=>function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				},'metasMes' => function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				},'planMejora'=>function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				},'unidadMedida','comentarios'))->find($id);

				if($parametros['nivel'] == 'componente'){
					$recurso->load('desgloseMunicipios');
					//$queries = DB::getQueryLog();
					//throw new Exception(print_r(end($queries),true), 1);
				}
			}elseif($parametros['mostrar'] == 'datos-metas-avance'){
				if($parametros['nivel'] == 'componente'){
					$recurso = Componente::getModel()->with(array('comentarios'));
				}else{
					$recurso = Actividad::getModel()->with(array('comentarios'));
				}
				//Se obtienen las metas por mes del mes actual y las metas por mes totales agrupadas por jurisdicción
				$recurso = $recurso->with(array('metasMesJurisdiccion'=>function($query) use ($mes_actual){
					$query->where('mes','<=',$mes_actual);
				},'registroAvance'=>function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				},'metasMes' => function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				},'planMejora'=>function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				},'unidadMedida'))->find($id);
				
				if($parametros['nivel'] == 'componente'){
					$recurso->load('desgloseMunicipios');
					//$queries = DB::getQueryLog();
					//throw new Exception(print_r(end($queries),true), 1);
				}
				
			}elseif($parametros['mostrar'] == 'datos-beneficiarios-avance'){
				$mes_actual = Util::obtenerMesActual();
				$recurso['acumulado'] = RegistroAvanceBeneficiario::where('idProyecto','=',$parametros['id-proyecto'])
														->where('idTipoBeneficiario','=',$id)
														->where('mes','<',$mes_actual)->groupBy('idTipoBeneficiario','sexo')
														->select('idTipoBeneficiario','sexo',DB::raw('sum(total) AS total'))->get();
				$recurso['beneficiario'] = Beneficiario::with(array('tipoBeneficiario','comentarios','registroAvance'=>function($query) use ($mes_actual){
					$query->where('mes','=',$mes_actual);
				}))->where('idProyecto','=',$parametros['id-proyecto'])->where('idTipoBeneficiario','=',$id)->get();
			}elseif ($parametros['mostrar'] == 'analisis-funcional') {
				$recurso = EvaluacionAnalisisFuncional::with('comentarios')->find($id);
			}elseif ($parametros['mostrar'] == 'comentarios-proyecto-mes') {
				$recurso = EvaluacionComentario::where('idProyecto','=',$id)
											->where('mes','=',$parametros['mes'])
											->where('tipoElemento','=','4')->get();
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

		$nuevoComentario = new EvaluacionComentario;
		
		$nuevoComentario->idProyecto = $parametros['idproyecto'];
		$nuevoComentario->mes = Util::obtenerMesActual();		
		$nuevoComentario->idCampo = $parametros['idcampo'];
		$nuevoComentario->tipoElemento = $parametros['tipocomentario'];
		$nuevoComentario->idElemento = $parametros['idelemento'];
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
	public function update($id){
		//
		
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');
		
		$parametros = Input::all();
		$mes_actual = Util::obtenerMesActual();

		if(isset($parametros['actualizarproyecto']))
		{
			//throw new Exception($parametros['actualizarproyecto'],1);
			$estatus = 0;
			if($parametros['actualizarproyecto']=="aprobar") //Poner estatus 4 (Aprobado)
			{
				$validar = DB::table('evaluacionComentarios')
                    ->where('idProyecto', '=', $id)
					->where('mes','=',$mes_actual)
					//->where('anio','=',date("Y"))
					->whereNull('borradoAl')
					->select('evaluacionComentarios.id')->get();
							
				
				if(count($validar)>0) //Existen comentarios, no se puede aprobar
				{
					$respuesta['http_status'] = 500;
					$respuesta['data'] = array("data"=>"Debe eliminar todos los comentarios para poder aprobar el avance.",'code'=>'U06');
				}
				else
				{
					/*$recurso = EvaluacionProyectoMes::where('idProyecto','=',$id)
								->where('mes','=',$mes_actual)
								->where('anio','=',date("Y"))
								->update(array('idEstatus' => '4'));*/
					$estatus = 4;
				}
			}
			else if($parametros['actualizarproyecto']=="regresar") //Poner estatus 3 (Regreso a corrección)
			{
				$validar = DB::table('evaluacionComentarios')
                    ->where('idProyecto', '=', $id)
					->where('mes','=',$mes_actual)
					->whereNull('borradoAl')
					->select('evaluacionComentarios.id')->get();
							
				
				if(count($validar)>0) //Existen comentarios, se puede enviar a corregir
				{
					/*$recurso = EvaluacionProyectoMes::where('idProyecto','=',$id)
								->where('mes','=',$mes_actual)
								->where('anio','=',date("Y"))
								->update(array('idEstatus' => '3'));*/
					$estatus = 3;
				}
				else
				{
					$respuesta['http_status'] = 500;
					$respuesta['data'] = array("data"=>"Debe escribir al menos un comentario, para poder regresar el proyecto a corrección.",'code'=>'U06');
				}
			}
			else if($parametros['actualizarproyecto']=="firmar") //Poner estatus 5 (Enviar a firma)
			{
				$validar = DB::table('evaluacionComentarios')
                    ->where('idProyecto', '=', $id)
					->where('mes','=',$mes_actual)
					->whereNull('borradoAl')
					->select('evaluacionComentarios.id')->get();
				
				if(count($validar)>0) //Existen comentarios, no se puede aprobar
				{
					$respuesta['http_status'] = 500;
					$respuesta['data'] = array("data"=>"Debe eliminar todos los comentarios para poder firmar el avance.",'code'=>'U06');
				}
				else
				{
					/*$recurso = EvaluacionProyectoMes::where('idProyecto','=',$id)
								->where('mes','=',$mes_actual)
								->where('anio','=',date("Y"))
								->update(array('idEstatus' => '5'));*/
					$estatus = 5;
				}
			}
			//Enviar correo
			if($estatus > 0 && $respuesta['http_status'] == 200){
				$recurso = EvaluacionProyectoMes::where('idProyecto','=',$id)
								->where('mes','=',$mes_actual)
								//->where('anio','=',date("Y"))
								->update(array('idEstatus' => $estatus));
				if($recurso){
					$usuario = Sentry::getUserProvider()->createModel();
					$usuario = $usuario->where('idDepartamento','=',3)
										->join('usuariosProyectos','usuariosProyectos.idSentryUser','=','sentryUsers.id')
										->where(function($query)use($id){
											$query = $query->where('usuariosProyectos.proyectos','like',$id.'|%')
															->orWhere('usuariosProyectos.proyectos','like','%|'.$id.'|%')
															->orWhere('usuariosProyectos.proyectos','like','%|'.$id)
															->orWhere('usuariosProyectos.proyectos','like',$id);
										})
										->select('sentryUsers.id','sentryUsers.nombres','sentryUsers.email')
										->first();
					if($usuario){
						$avance_mes =  EvaluacionProyectoMes::where('idProyecto','=',$id)
														->where('mes','=',$mes_actual)->first();
						$proyecto = Proyecto::find($id);

						$data['usuario'] = $usuario;
						$data['proyecto'] = $proyecto;
						$data['mes_captura'] = Util::obtenerDescripcionMes($mes_actual);
						$data['estatus'] = $avance_mes->idEstatus;

						if($avance_mes->idEstatus == 3){
							$estatus_label = 'Con errores';
						}elseif($avance_mes->idEstatus == 4){
							$estatus_label = 'Registrado';
						}else{
							$estatus_label = 'Firmado';
						}

						Mail::send('emails.rendicion-cuentas.proyecto-revision-respuesta', $data, function($message) use ($usuario,$estatus_label){
							$message->to($usuario->email,$usuario->nombres)->subject('SIRE:: Avance de Metas revisado ('.$estatus_label.')');
						});
						$respuesta['notas'] = 'Con correo enviado';
					}else{
						$respuesta['notas'] = 'Sin correo enviado';
					}
				}else{
					$respuesta['http_status'] = 500;
					$respuesta['data'] = array("data"=>"Ocurrio un error al intentar validar el proyecto.",'code'=>'S01');
				}
			}
		}
		else
		{
			$recurso = EvaluacionComentario::find($id);
			if(is_null($recurso)){
				$respuesta['http_status'] = 404;
				$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
			}else{
						
				$recurso->idProyecto = $parametros['idproyecto'];
				$recurso->mes = Util::obtenerMesActual();		
				$recurso->idCampo = $parametros['idcampo'];
				$recurso->tipoElemento = $parametros['tipocomentario'];
				$recurso->idElemento = $parametros['idelemento'];
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
	
	public function destroy($id)
	{
		//
		$http_status = 200;
		$data = array();

		try{
			$recurso = EvaluacionComentario::where('id','=',$id)->delete();
			
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