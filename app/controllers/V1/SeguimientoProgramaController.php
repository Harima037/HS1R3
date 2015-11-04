<?php

namespace V1;

use SSA\Utilerias\Validador;
use SSA\Utilerias\Util;
use BaseController, Input, Response, DB, Sentry, Hash, Exception,DateTime;
use Programa, ProgramaIndicador, RegistroAvancePrograma, EvaluacionProgramaTrimestre, RegistroAvanceComentario, Directorio; 

class SeguimientoProgramaController extends BaseController {
	private $reglasAvance = array(
		'analisis-resultados'	=> 'required',
		'avance-trimestre'		=> 'required'
	);
	
	private $reglasComentario = array(
			'idregistroavance' => 'required',
			'idcampo' => 'required',
			'comentario' => 'required'
		);

	private $reglasDatosInformacion = array(
		'fuente-informacion'		=> 'required',
		'responsable-informacion'	=> 'required'
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
				if($parametros['grid'] == 'rendicion-indicadores'){
					$rows = ProgramaIndicador::with('registroAvance')->where('idPrograma','=',$parametros['idPrograma'])->get();
					//$rows->componentes->load('registroAvance');
					$total = count($rows);
				}
			}else{
				$mes_del_trimestre = Util::obtenerMesTrimestre();
				if($mes_del_trimestre == 3){
					$trimestre_actual = Util::obtenerTrimestre();
				}else{
					$trimestre_actual = 0;
				}

				$rows = Programa::getModel();
				$rows = $rows->where('programa.idEstatus','=',5);
				//$rows = $rows->wherein('Eval.idEstatus',array(2,4));
				
				$usuario = Sentry::getUser();
				if($usuario->idDepartamento == 2){
					if($usuario->filtrarProgramas){
						$rows = $rows->where('idUsuarioValidacionSeg','=',$usuario->id);
					}
				}else{
					$rows = $rows->where('idUsuarioRendCuenta','=',$usuario->id);
				}
				
				$rows = $rows->with(array('registroAvance'=>function($query){
					$query->select('id','idPrograma','trimestre',DB::raw('sum(justificacion) AS justificacion'),
									DB::raw('count(idIndicador) AS registros'))->groupBy('idPrograma','trimestre');
				}/*,'evaluacionTrimestre'=>function($query) use ($trimestre_actual){
					$query->where('trimestre','=',$trimestre_actual);
				}*/));

				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
				
				
				$rows = $rows->leftjoin('evaluacionProgramaTrimestre AS Eval', function($join) use($trimestre_actual)
										{
											$join->on('programa.id', '=', 'Eval.idPrograma')
											->where('Eval.trimestre', '=', $trimestre_actual);
										})
							->leftjoin('programaIndicador','programaIndicador.idPrograma','=','programa.id');
				$total = $rows->count();
				
				
				$rows = $rows->select('programa.id','programaPresupuestario.descripcion AS programa','programaPresupuestario.clave','Eval.idEstatus',
									DB::raw('count(programaIndicador.trim1) AS trim1'),DB::raw('count(programaIndicador.trim2) AS trim2'),
									DB::raw('count(programaIndicador.trim3) AS trim3'),DB::raw('count(programaIndicador.trim4) AS trim4'))
									->join('catalogoProgramasPresupuestales AS programaPresupuestario','programaPresupuestario.clave','=','programa.claveProgramaPresupuestario')
									->orderBy('id', 'desc')
									->groupBy('programa.id')
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
			if($parametros['mostrar'] == 'datos-programa-presupuestario'){
				$recurso = Programa::join('catalogoProgramasPresupuestales AS programaPresupuestario','programaPresupuestario.clave','=','programa.claveProgramaPresupuestario')
					->join('catalogoUnidadesResponsables AS unidadResponsable','unidadResponsable.clave','=','programa.claveUnidadResponsable')
					->select('programa.*','programaPresupuestario.descripcion AS programaPresupuestario','unidadResponsable.descripcion AS unidadResponsable')
					->find($id);
			}elseif($parametros['mostrar'] == 'datos-programa-avance'){
				$mes_del_trimestre = Util::obtenerMesTrimestre();
				if($mes_del_trimestre == 3){
					$trimestre_actual = Util::obtenerTrimestre();
				}else{
					$trimestre_actual = 0;
				}
				$recurso = Programa::with(array('indicadores.registroAvance',
					'evaluacionTrimestre'=>function($query) use ($trimestre_actual){
						$query->where('trimestre','=',$trimestre_actual);
					}))
					->join('catalogoProgramasPresupuestales AS programaPresupuestario','programaPresupuestario.clave','=','programa.claveProgramaPresupuestario')
					->join('catalogoUnidadesResponsables AS unidadResponsable','unidadResponsable.clave','=','programa.claveUnidadResponsable')
					->select('programa.*','programaPresupuestario.descripcion AS programaPresupuestario','unidadResponsable.descripcion AS unidadResponsable')
					->find($id);
				$recurso['responsables'] = Directorio::responsablesActivos($recurso->claveUnidadResponsable)->get();
			}elseif($parametros['mostrar'] == 'datos-metas-avance'){
				$trimestre_actual = Util::obtenerTrimestre();
				$recurso = ProgramaIndicador::with(array('registroAvance'=>function($query) use ($trimestre_actual){
					$query->where('trimestre','<=',$trimestre_actual);
				},'registroAvance.comentarios'))->join('catalogoUnidadesMedida AS unidadMedida','unidadMedida.id','=','programaIndicador.idUnidadMedida')
				->select('programaIndicador.*','unidadMedida.descripcion AS unidadMedida')
				->find($id);
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

		$nuevoComentario = new RegistroAvanceComentario;
		
		$nuevoComentario->idRegistroAvance = $parametros['idregistroavance'];
		$nuevoComentario->idCampo = $parametros['idcampo'];
		$nuevoComentario->comentario = $parametros['comentario'];
		
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
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');
		
		$parametros = Input::all();
		
		if(isset($parametros['actualizarprograma']))
		{
			$trimestre_actual = Util::obtenerTrimestre();
			$avanceProgramaTrimestral = RegistroAvancePrograma::where('idPrograma','=',$id)->where('trimestre','=',$trimestre_actual)->get();
			$RegistrosAvances = array();				
			foreach($avanceProgramaTrimestral as $fila)
				$RegistrosAvances[] = $fila['id'];
			if($parametros['actualizarprograma']=="aprobar") //Poner estatus 4 (Aprobado)
			{
				$validar = RegistroAvanceComentario::wherein('idRegistroAvance', $RegistrosAvances)->count();				
				if($validar)
				{
					$respuesta['http_status'] = 500;
					$respuesta['data'] = array("data"=>"Existen comentarios, debe eliminarlos primero para aprobar el avance.",'code'=>'U06');
				}
				else
				{
					$recurso = EvaluacionProgramaTrimestre::where('idPrograma','=',$id)->where('trimestre','=',$trimestre_actual)->first();
					if(is_null($recurso)){
						$respuesta['http_status'] = 404;
						$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
					}else{
						$recurso->idEstatus = 4;
						$recurso->save();
					}
				}
			}
			else if($parametros['actualizarprograma']=="regresar") //Poner estatus 3 (Regreso a corrección)
			{
				$validar = RegistroAvanceComentario::wherein('idRegistroAvance', $RegistrosAvances)->count();				
				if($validar)
				{
					$recurso = EvaluacionProgramaTrimestre::where('idPrograma','=',$id)->where('trimestre','=',$trimestre_actual)->first();
					if(is_null($recurso)){
						$respuesta['http_status'] = 404;
						$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
					}else{
						$recurso->idEstatus = 3;
						$recurso->save();
					}
				}
				else
				{
					$respuesta['http_status'] = 500;
					$respuesta['data'] = array("data"=>"No existen comentarios, debe escribir al menos uno para regresar a corrección el avance.",'code'=>'U06');				
				}
			}
			else if($parametros['actualizarprograma']=="firmar") //Poner estatus 5 (Enviar a firma)
			{
				$validar = RegistroAvanceComentario::wherein('idRegistroAvance', $RegistrosAvances)->count();				
				if($validar)
				{
					$respuesta['http_status'] = 500;
					$respuesta['data'] = array("data"=>"Existen comentarios, debe eliminarlos primero para poner el estatus de firma en el avance.",'code'=>'U06');
				}
				else
				{
					$recurso = EvaluacionProgramaTrimestre::where('idPrograma','=',$id)->where('trimestre','=',$trimestre_actual)->first();
					if(is_null($recurso)){
						$respuesta['http_status'] = 404;
						$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
					}else{
						$recurso->idEstatus = 5;
						$recurso->save();
					}
				}
			}
			else if($parametros['actualizarprograma'] == 'datos-informacion')
			{
				$validado = Validador::validar($parametros,$this->reglasDatosInformacion);
				if($validado === TRUE){
					$recurso = Programa::find($id);

					$recurso->fuenteInformacion = $parametros['fuente-informacion'];
					$recurso->idResponsable = $parametros['responsable-informacion'];
					if($recurso->save()){
						$respuesta['http_status'] = 200;
						$respuesta['data'] = array('data'=>$recurso);
					}else{
						$respuesta['http_status'] = 500;
						$respuesta['data'] = array('data'=>'Ocurrio un error al intentar guardar los datos.','code'=>'S01');
					}
				}else{
					$respuesta = $validado;
				}
			}
		}
		else
		{
			$recurso = RegistroAvanceComentario::find($id);
			if(is_null($recurso)){
				$respuesta['http_status'] = 404;
				$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
			}else{				
				$recurso->comentario = $parametros['comentario'];
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
		$http_status = 200;
		$data = array();

		try{
			$recurso = RegistroAvanceComentario::where('id','=',$id)->delete();
			
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