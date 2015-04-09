<?php

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, Hash, Exception,DateTime;
use Programa, ProgramaArbolProblema, ProgramaArbolObjetivo, ProgramaIndicador;

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

	private $reglasProblemaObjetivo = array(
		'descripcion-problema'	=> 'sometimes|required',
		'descripcion-objetivo'	=> 'sometimes|required'
	);

	private $reglasCausaEfecto = array(
		'causa'		=> 'required',
		'efecto' 	=> 'required'
	);

	private $reglasMedioFin = array(
		'medio'	=> 'required',
		'fin' 	=> 'required'
	);

	private $reglasIndicador = array(
		'tipo-indicador'			=> 'required',
		'ambito-programa'			=> 'required',
		'denominador-ind-programa' 	=> 'required',
		'descripcion-ind-programa' 	=> 'required',
		'descripcion-obj-programa' 	=> 'required',
		'dimension-programa' 		=> 'required',
		'formula-programa' 			=> 'required',
		'frecuencia-programa' 		=> 'required',
		'interpretacion-programa' 	=> 'required',
		'numerador-ind-programa' 	=> 'required',
		'supuestos-programa' 		=> 'required',
		'tipo-ind-programa' 		=> 'required',
		'anio-base-programa' 		=> 'integer|min:0',
		'linea-base-programa' 		=> 'numeric|min:0',
		'unidad-medida-programa' 	=> 'required',
		'verificacion-programa' 	=> 'required',
		'trim1-programa' 			=> 'numeric',
		'trim2-programa' 			=> 'numeric',
		'trim3-programa' 			=> 'numeric',
		'trim4-programa' 			=> 'numeric',
		'denominador-programa' 		=> 'required_if:formula-programa,1,2,3,4,5,6|numeric|min:0',
		'numerador-programa' 		=> 'required|numeric|min:1',
		'meta-programa' 			=> 'required|numeric|min:0'
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

			if(isset($parametros['listar'])){
				$id = $parametros['id-programa'];
				if($parametros['listar'] == 'problemas'){
					$rows = ProgramaArbolProblema::select('id','causa','efecto')->where('idPrograma','=',$id)->get();
				}elseif($parametros['listar'] == 'objetivos'){
					$rows = ProgramaArbolObjetivo::select('id','medio','fin')->where('idPrograma','=',$id)->get();
				}elseif($parametros['listar'] == 'indicadores') {
					$rows = ProgramaIndicador::select('programaIndicador.id','claveTipoIndicador','descripcionIndicador as indicador','unidadMedida.descripcion')
											->join('catalogoUnidadesMedida AS unidadMedida','unidadMedida.id','=','programaIndicador.idUnidadMedida')
											->where('idPrograma','=',$id)->get();
				}
				$total = count($rows);
			}else{
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
				
				$rows = $rows->select('programa.id','catalogoProgramasPresupuestales.clave','catalogoProgramasPresupuestales.descripcion AS programa','programa.fechaInicio',
							'programa.fechaTermino','programa.idEstatus','catalogoEstatusProyectos.descripcion as estatus','sentryUsers.username','programa.modificadoAl')
							->join('sentryUsers','sentryUsers.id','=','programa.actualizadoPor')
							->join('catalogoProgramasPresupuestales','catalogoProgramasPresupuestales.clave','=','programa.claveProgramaPresupuestario')
							->join('catalogoEstatusProyectos','catalogoEstatusProyectos.id','=','programa.idEstatus')
							->where('programa.idEstatus','<',5)
							->orderBy('id', 'desc')
							->skip(($parametros['pagina']-1)*10)->take(10)
							->get();
			}

			$data = array('resultados'=>$total,'data'=>$rows);

			if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
			}

		}else{
			$rows = Proyecto::all();

			if(count($rows) == 0){
				$http_status = 404;
				$data = array("data"=>"No hay datos",'code'=>'W00');
			}else{
				$data = array("data"=>$rows->toArray());
			}
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
			if($parametros['mostrar'] == 'editar-programa'){
				$recurso = Programa::find($id);
			}elseif($parametros['mostrar'] == 'editar-causa-efecto'){
				$recurso = ProgramaArbolProblema::find($id);
			}elseif($parametros['mostrar'] == 'editar-medio-fin'){
				$recurso = ProgramaArbolObjetivo::find($id);
			}elseif($parametros['mostrar'] == 'editar-indicador'){
				$recurso = ProgramaIndicador::find($id);
			}
		}else{
			$recurso = Programa::find($id);
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
					$recurso->claveUnidadResponsable = $parametros['unidad-responsable'];
					$recurso->idOdm = $parametros['odm'];
					$recurso->idModalidad = $parametros['modalidad'];
					$recurso->idEstatus = 1;
					$recurso->ejercicio = $parametros['ejercicio'];
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
			}elseif($parametros['guardar'] == 'causa-efecto'){
				$validacion = Validador::validar(Input::all(), $this->reglasCausaEfecto);
				if($validacion === TRUE){
					$programa = Programa::find($parametros['id-programa']);

					$recurso = new ProgramaArbolProblema;

					$recurso->causa = $parametros['causa'];
					$recurso->efecto = $parametros['efecto'];

					if($programa->arbolProblema()->save($recurso)){
						$respuesta['data'] = $recurso;
					}else{
						$respuesta['http_status'] = 500;
						$respuesta['data'] = array('data'=>'Error al intentar guardar los datos','code'=>'S01');
					}
				}else{
					//La Validación del Formulario encontro errores
					$respuesta['http_status'] = $validacion['http_status'];
					$respuesta['data'] = $validacion['data'];
				}
			}elseif($parametros['guardar'] == 'medio-fin'){
				$validacion = Validador::validar(Input::all(), $this->reglasMedioFin);
				if($validacion === TRUE){
					$programa = Programa::find($parametros['id-programa']);

					$recurso = new ProgramaArbolObjetivo;

					$recurso->medio = $parametros['medio'];
					$recurso->fin = $parametros['fin'];

					if($programa->arbolObjetivo()->save($recurso)){
						$respuesta['data'] = $recurso;
					}else{
						$respuesta['http_status'] = 500;
						$respuesta['data'] = array('data'=>'Error al intentar guardar los datos','code'=>'S01');
					}
				}else{
					//La Validación del Formulario encontro errores
					$respuesta['http_status'] = $validacion['http_status'];
					$respuesta['data'] = $validacion['data'];
				}
			}elseif($parametros['guardar'] == 'indicador'){
				$validacion = Validador::validar(Input::all(), $this->reglasIndicador);
				if($validacion === TRUE){
					$tipo_indicador = $parametros['tipo-indicador'];

					$programa = Programa::with(array('indicadores'=>function($query) use ($tipo_indicador){
						$query->where('claveTipoIndicador','=',$tipo_indicador);
					}))->find($parametros['id-programa']);

					if(count($programa->indicadores)){
						$respuesta['data']['data'] = 'Ya fue capturado un indicador de este tipo';
						throw new Exception("Se encontraron indicadores capturados", 1);
					}

					$recurso = new ProgramaIndicador;

					$recurso->claveTipoIndicador	= $parametros['tipo-indicador'];
					$recurso->claveAmbito			= $parametros['ambito-programa'];

					$recurso->descripcionObjetivo 	= $parametros['descripcion-obj-programa'];
					$recurso->mediosVerificacion 	= $parametros['verificacion-programa'];
					$recurso->supuestos 			= $parametros['supuestos-programa'];
					$recurso->descripcionIndicador 	= $parametros['descripcion-ind-programa'];
					$recurso->numerador 			= $parametros['numerador-ind-programa'];
					$recurso->denominador 			= $parametros['denominador-ind-programa'];
					$recurso->interpretacion 		= $parametros['interpretacion-programa'];

					$recurso->idFormula				= ($parametros['formula-programa'])?$parametros['formula-programa']:NULL;
					$recurso->idDimensionIndicador 	= ($parametros['dimension-programa'])?$parametros['dimension-programa']:NULL;
					$recurso->idFrecuenciaIndicador = ($parametros['frecuencia-programa'])?$parametros['frecuencia-programa']:NULL;
					$recurso->idTipoIndicador 		= ($parametros['tipo-ind-programa'])?$parametros['tipo-ind-programa']:NULL;
					
					$recurso->idUnidadMedida 		= ($parametros['unidad-medida-programa'])?$parametros['unidad-medida-programa']:NULL;
					$recurso->metaIndicador 		= $parametros['meta-programa'];
					$recurso->trim1 				= ($parametros['trim1-programa'])?$parametros['trim1-programa']:NULL;
					$recurso->trim2 				= ($parametros['trim2-programa'])?$parametros['trim2-programa']:NULL;
					$recurso->trim3 				= ($parametros['trim3-programa'])?$parametros['trim3-programa']:NULL;
					$recurso->trim4 				= ($parametros['trim4-programa'])?$parametros['trim4-programa']:NULL;

					$numerador = floatval($recurso->trim1) + floatval($recurso->trim2) + floatval($recurso->trim3) + floatval($recurso->trim4);
					$recurso->valorNumerador 		= $numerador;

					if($recurso->idFormula == 7){
						$recurso->valorDenominador 	= NULL;
					}else{
						$recurso->valorDenominador 	= $parametros['denominador-programa'];
					}
					$recurso->lineaBase 			= ($parametros['linea-base-programa'])?$parametros['linea-base-programa']:NULL;
					$recurso->anioBase 				= ($parametros['anio-base-programa'])?$parametros['anio-base-programa']:NULL;

					if($programa->indicadores()->save($recurso)){
						$respuesta['data'] = $recurso;
					}else{
						$respuesta['http_status'] = 500;
						$respuesta['data'] = array('data'=>'Error al intentar guardar los datos','code'=>'S01');
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
		try{
			if($parametros['guardar'] == 'programa'){
				$validacion = Validador::validar(Input::all(), $this->reglasPrograma);
				if($validacion === TRUE){
					$recurso = Programa::find($id);

					$recurso->claveProgramaPresupuestario = $parametros['programa-presupuestario'];
					$recurso->claveUnidadResponsable = $parametros['unidad-responsable'];
					$recurso->ejercicio = $parametros['ejercicio'];
					$recurso->idOdm = $parametros['odm'];
					$recurso->idModalidad = $parametros['modalidad'];
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
			}elseif ($parametros['guardar'] == 'programa-problema-objetivo') {
				$validacion = Validador::validar(Input::all(), $this->reglasProblemaObjetivo);
				if($validacion === TRUE){
					$recurso = Programa::find($id);

					if(isset($parametros['descripcion-problema'])){
						$recurso->arbolProblema = $parametros['descripcion-problema'];
					}
					
					if(isset($parametros['descripcion-objetivo'])){
						$recurso->arbolObjetivo = $parametros['descripcion-objetivo'];
					}

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
			}elseif($parametros['guardar'] == 'causa-efecto'){
				$validacion = Validador::validar(Input::all(), $this->reglasCausaEfecto);
				if($validacion === TRUE){
					$recurso = ProgramaArbolProblema::find($id);

					$recurso->causa = $parametros['causa'];
					$recurso->efecto = $parametros['efecto'];

					if($recurso->save()){
						$respuesta['data'] = $recurso;
					}else{
						$respuesta['http_status'] = 500;
						$respuesta['data'] = array('data'=>'Error al intentar guardar los datos','code'=>'S01');
					}
				}else{
					//La Validación del Formulario encontro errores
					$respuesta['http_status'] = $validacion['http_status'];
					$respuesta['data'] = $validacion['data'];
				}
			}elseif($parametros['guardar'] == 'medio-fin'){
				$validacion = Validador::validar(Input::all(), $this->reglasMedioFin);
				if($validacion === TRUE){
					$recurso = ProgramaArbolObjetivo::find($id);

					$recurso->medio = $parametros['medio'];
					$recurso->fin = $parametros['fin'];

					if($recurso->save()){
						$respuesta['data'] = $recurso;
					}else{
						$respuesta['http_status'] = 500;
						$respuesta['data'] = array('data'=>'Error al intentar guardar los datos','code'=>'S01');
					}
				}else{
					//La Validación del Formulario encontro errores
					$respuesta['http_status'] = $validacion['http_status'];
					$respuesta['data'] = $validacion['data'];
				}
			}elseif($parametros['guardar'] == 'indicador'){
				$validacion = Validador::validar(Input::all(), $this->reglasIndicador);
				if($validacion === TRUE){

					$recurso = ProgramaIndicador::find($id);

					$tipo_indicador = $parametros['tipo-indicador'];

					if($tipo_indicador != $recurso->claveTipoIndicador){
						$programa = Programa::with(array('indicadores'=>function($query) use ($tipo_indicador){
							$query->where('claveTipoIndicador','=',$tipo_indicador);
						}))->find($parametros['id-programa']);

						if(count($programa->indicadores)){
							$respuesta['data']['data'] = 'Ya fue capturado un indicador de este tipo';
							throw new Exception("Se encontraron indicadores capturados", 1);
						}

						$recurso->claveTipoIndicador	= $parametros['tipo-indicador'];
					}

					$recurso->claveAmbito			= $parametros['ambito-programa'];

					$recurso->descripcionObjetivo 	= $parametros['descripcion-obj-programa'];
					$recurso->mediosVerificacion 	= $parametros['verificacion-programa'];
					$recurso->supuestos 			= $parametros['supuestos-programa'];
					$recurso->descripcionIndicador 	= $parametros['descripcion-ind-programa'];
					$recurso->numerador 			= $parametros['numerador-ind-programa'];
					$recurso->denominador 			= $parametros['denominador-ind-programa'];
					$recurso->interpretacion 		= $parametros['interpretacion-programa'];

					$recurso->idFormula				= ($parametros['formula-programa'])?$parametros['formula-programa']:NULL;
					$recurso->idDimensionIndicador 	= ($parametros['dimension-programa'])?$parametros['dimension-programa']:NULL;
					$recurso->idFrecuenciaIndicador = ($parametros['frecuencia-programa'])?$parametros['frecuencia-programa']:NULL;
					$recurso->idTipoIndicador 		= ($parametros['tipo-ind-programa'])?$parametros['tipo-ind-programa']:NULL;
					
					$recurso->idUnidadMedida 		= ($parametros['unidad-medida-programa'])?$parametros['unidad-medida-programa']:NULL;
					$recurso->metaIndicador 		= $parametros['meta-programa'];
					$recurso->trim1 				= ($parametros['trim1-programa'])?$parametros['trim1-programa']:NULL;
					$recurso->trim2 				= ($parametros['trim2-programa'])?$parametros['trim2-programa']:NULL;
					$recurso->trim3 				= ($parametros['trim3-programa'])?$parametros['trim3-programa']:NULL;
					$recurso->trim4 				= ($parametros['trim4-programa'])?$parametros['trim4-programa']:NULL;

					$numerador = floatval($recurso->trim1) + floatval($recurso->trim2) + floatval($recurso->trim3) + floatval($recurso->trim4);
					$recurso->valorNumerador 		= $numerador;

					if($recurso->idFormula == 7){
						$recurso->valorDenominador 	= NULL;
					}else{
						$recurso->valorDenominador 	= $parametros['denominador-programa'];
					}
					$recurso->lineaBase 			= ($parametros['linea-base-programa'])?$parametros['linea-base-programa']:NULL;
					$recurso->anioBase 				= ($parametros['anio-base-programa'])?$parametros['anio-base-programa']:NULL;

					if($recurso->save()){
						$respuesta['data'] = $recurso;
					}else{
						$respuesta['http_status'] = 500;
						$respuesta['data'] = array('data'=>'Error al intentar guardar los datos','code'=>'S01');
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
		$data = array('data'=>'');
		try{
			$parametros = Input::all();
			$ids = $parametros['rows'];
			if(isset($parametros['eliminar'])){
				if($parametros['eliminar'] == 'causa-efecto'){
					$rows = ProgramaArbolProblema::whereIn('id',$ids)->delete();
				}elseif($parametros['eliminar'] == 'medio-fin'){
					$rows = ProgramaArbolObjetivo::whereIn('id',$ids)->delete();
				}elseif($parametros['eliminar'] == 'indicador') {
					$rows = ProgramaIndicador::whereIn('id',$ids)->delete();
				}
			}else{
				$rows = DB::transaction(function() use ($ids){
					ProgramaArbolProblema::whereIn('idPrograma',$ids)->delete();
					ProgramaArbolObjetivo::whereIn('idPrograma',$ids)->delete();
					ProgramaIndicador::whereIn('idPrograma',$ids)->delete();
					return Programa::whereIn('id',$ids)->delete();
				});
			}

			if($rows>0){
				$data = array("data"=>"Se han eliminado los recursos.");
			}else{
				$http_status = 500;
				$data = array('data' => "No se pueden eliminar los recursos.",'code'=>'S03');
			}
		}catch(Exception $ex){
			$http_status = 500;	
			$data['code'] = 'S03';
			$data['ex'] = $ex->getMessage();
			if($data['data'] == ''){
				$data['data'] = "No se pueden borrar los registros";
			}
		}
		return Response::json($data,$http_status);
	}
}