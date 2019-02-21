<?php 
/* 
*	POA
*	Programa Operativo Anual
*
*	PHP version 5.5.3
*
* 	Área de Informática, Dirección de Planeación y Desarrollo.
*
*	@copyright			Copyright 2015, Instituto de Salud.
*	@author 			Mario Alberto Cabrera Alfaro
*	@package 			poa
*	@version 			1.0
*	@comment 			
*/


namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, Hash, Exception,DateTime;
use Estrategia, ProgramaArbolProblema, ProgramaArbolObjetivo, ProgramaIndicador,Titular,Directorio,Proyecto;

class EstrategiaInstitucionalController extends \BaseController {

	private $reglasEstrategia = array(
		'unidad-responsable'		=> 'required',
		'programa-presupuestario'	=> 'required',
		'programa-sectorial'		=> 'required',
		'ejercicio'					=> 'required',
		'tipo-ind'			=> 'required',
		'anio-base' 		=> 'integer|min:0',
		'linea-base' 		=> 'numeric|min:0',
		'unidad-medida' 	=> 'required',
		'trim1' 			=> 'numeric',
		'trim2' 			=> 'numeric',
		'trim3' 			=> 'numeric',
		'trim4' 			=> 'numeric',
		'numerador' 		=> 'required|numeric|min:1',
	);


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$http_status = 200;
		$data = array();

		$parametros = Input::all();

		/*if(isset($parametros['formatogrid'])){

			if(isset($parametros['listar'])){
				$id = $parametros['id-programa'];
				if($parametros['listar'] == 'problemas'){
					$rows = ProgramaArbolProblema::select('id','causa','efecto')->where('idPrograma','=',$id)->get();
				}elseif($parametros['listar'] == 'objetivos'){
					$rows = ProgramaArbolObjetivo::select('id','medio','fin')->where('idPrograma','=',$id)->get();
				}elseif($parametros['listar'] == 'indicadores'){
					$rows = ProgramaIndicador::select('programaIndicador.id','claveTipoIndicador','descripcionIndicador as indicador','unidadMedida.descripcion')
											->join('catalogoUnidadesMedida AS unidadMedida','unidadMedida.id','=','programaIndicador.idUnidadMedida')
											->where('idPrograma','=',$id)->get();
				}elseif($parametros['listar'] == 'proyectos'){
					$rows = Proyecto::select('proyectos.*','sentryUsers.username','catalogoCoberturas.descripcion AS coberturaDescripcion')
									->leftjoin('sentryUsers','sentryUsers.id','=','proyectos.actualizadoPor')
									->leftjoin('catalogoCoberturas','catalogoCoberturas.id','=','proyectos.idCobertura')
									->where('proyectos.idPrograma','=',$id)->get();
				}elseif($parametros['listar'] == 'buscar-proyecto'){
					$programa = Programa::find($id);
					$rows = Proyecto::select('proyectos.*','sentryUsers.username','catalogoCoberturas.descripcion AS coberturaDescripcion')
									->leftjoin('sentryUsers','sentryUsers.id','=','proyectos.actualizadoPor')
									->leftjoin('catalogoCoberturas','catalogoCoberturas.id','=','proyectos.idCobertura')
									->where('proyectos.ejercicio','=',$programa->ejercicio)
									->where('proyectos.programaPresupuestario','=',$programa->claveProgramaPresupuestario)
									->where('proyectos.idClasificacionProyecto','=',1)
									->whereNull('proyectos.idPrograma')
									->get();
				}
				$total = count($rows);
			}else{
				$rows = Programa::getModel();

				if(Sentry::getUser()->claveProgramaPresupuestario){
					$rows = $rows->where('claveProgramaPresupuestario','=',Sentry::getUser()->claveProgramaPresupuestario);
				}
				
				if(Sentry::getUser()->claveUnidad){
					$unidades = explode('|',Sentry::getUser()->claveUnidad);
					$rows = $rows->whereIn('claveUnidadResponsable',$unidades);
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
							//->where('programa.idEstatus','<',5)
							->orderBy('id', 'desc')
							->skip(($parametros['pagina']-1)*10)->take(10)
							->get();
			}

			$data = array('resultados'=>$total,'data'=>$rows);

			if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
			}

		}elseif(isset($parametros['typeahead'])){
			$rows = Programa::getModel();

			if(isset($parametros['buscar'])){				
				$rows = $rows->where(function($query)use($parametros){
						$query->where('programaPresupuestal.descripcion','like','%'.$parametros['buscar'].'%')
							->orWhere('programa.claveProgramaPresupuestario','like','%'.$parametros['buscar'].'%');
					});
			}

			if(isset($parametros['unidades'])){
				$unidades = explode(',',$parametros['unidades']);
				$rows = $rows->whereIn('programa.claveUnidadResponsable',$unidades);
			}
			$rows = $rows->where('programa.idEstatus','=',5);

			if(isset($parametros['departamento'])){
				if(isset($parametros['usuario'])){
					$id_usuario = $parametros['usuario'];
				}else{
					$id_usuario = 0;
				}
				
				if($parametros['departamento'] == 2){
					$rows = $rows->where(function($query)use($id_usuario){
						$query->whereNull('programa.idUsuarioValidacionSeg')
							->orWhere('programa.idUsuarioValidacionSeg','=',$id_usuario);
					});
				}else{
					$rows = $rows->where(function($query)use($id_usuario){
						$query->whereNull('programa.idUsuarioRendCuenta')
							->orWhere('programa.idUsuarioRendCuenta','=',$id_usuario);
					});
				}
			}
			//var_dump($proyectos_asignados);die;
			//throw new Exception("Error:: " + print_r($proyectos_asignados,true), 1);
			
			$rows = $rows->contenidoSuggester()->get();

			if(count($rows)<=0){
	          $data = array('resultados'=>0,"data"=>array());
	        }else{
	          $data = array('resultados'=>count($rows),'data'=>$rows);
	        }
		}else{*/
			$rows = Estrategia::all()->load("programaPresupuestario", "Estatus", "Usuario");

			if(count($rows)<=0){
				$data = array('resultados'=>0,"data"=>array());
			  }else{
				$data = array('resultados'=>count($rows),'data'=>$rows);
			  }
		//}
		return Response::json($data,$http_status);
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');

		$parametros = Input::all();
		//return Response::json($parametros,500);
		try{

			if($parametros['guardar'] != 'estrategia'){
				if(isset($parametros['id-estrategia'])){
					$id_estrategia = $parametros['id-estrategia'];
				}else{
					$id_estrategia = $id;
				}

				$estrategia = Estrategia::find($id_estrategia);

				if($estrategia->idEstatus != 1 && $estrategia->idEstatus != 3){
					switch ($programa->idEstatus) {
						case 2:
							$respuesta['data']['data'] = 'El programa se encuentra en proceso de revisión, por tanto no es posible editarlo';
							break;
						case 4:
							$respuesta['data']['data'] = 'El programa se encuentra registrado, por tanto no es posible editarlo';
							break;
						case 5:
							$respuesta['data']['data'] = 'El programa ya fue firmado, por tanto no es posible editarlo';
							break;
						default:
							$respuesta['data']['data'] = 'El estatus del programa es desconocido';
							break;
					}
					throw new Exception("El programa se encuentra en un estatus en el que no esta disponible para edición", 1);
				}
			}
			
			if($parametros['guardar'] == 'estrategia'){
				$validacion = Validador::validar(Input::all(), $this->reglasEstrategia);
				if($validacion === TRUE){
					$estrategia = Estrategia::where('ejercicio','=',$parametros['ejercicio'])
										->where('claveProgramaPresupuestario','=',$parametros['programa-presupuestario'])->get();

					if(count($estrategia)){
						$respuesta['data']['data'] = 'Esta estrategia institucional ya se encentra capturado';
						throw new Exception("Estrategia Institucional encontrado", 1);
					}

					$recurso = new Estrategia;

					$recurso->claveUnidadResponsable = $parametros['unidad-responsable'];
					$recurso->claveProgramaSectorial = $parametros['programa-sectorial'];
					$recurso->claveProgramaPresupuestario = $parametros['programa-presupuestario'];
					$recurso->ejercicio = $parametros['ejercicio'];
					$recurso->idEstatus = 1;

					$recurso->idObjetivoPED = ($parametros['vinculacion-ped'])?$parametros['vinculacion-ped']:NULL;
					$recurso->idOdm = ($parametros['odm'])?$parametros['odm']:NULL;
					$recurso->mision = $parametros['mision'];
					$recurso->vision = $parametros['vision'];
					$recurso->descripcionIndicador = $parametros['descripcion-indicador'];
					$recurso->numerador = $parametros['numerador'];
					$recurso->denominador = $parametros['denominador'];
					$recurso->interpretacion = $parametros['interpretacion'];
					
					$recurso->idTipoIndicador = $parametros['tipo-ind'];
					$recurso->idDimensionIndicador = $parametros['dimension'];
					$recurso->idUnidadMedida = $parametros['unidad-medida'];
					$recurso->metaIndicador = $parametros['meta'];
					$recurso->lineaBase = $parametros['linea-base'];
					$recurso->anioBase = $parametros['anio-base'];
					$recurso->idFormula = $parametros['formula'];
					$recurso->idFrecuenciaIndicador = $parametros['frecuencia'];
					$recurso->trim1 = $parametros['trim1'];
					$recurso->trim2 = $parametros['trim2'];
					$recurso->trim3 = $parametros['trim3'];
					$recurso->trim4 = $parametros['trim4'];
					$recurso->valorNumerador = $parametros['valorNumerador'];
					$recurso->valorDenominador = $parametros['valorDenominador'];
					$recurso->fuenteInformacion = $parametros['fuente-informacion'];
					$recurso->idResponsable = $parametros['responsable'];

					if($recurso->save()){
						//$recurso['responsables'] = Directorio::responsablesActivos($recurso->claveUnidadResponsable)->get();
						$respuesta['data'] = array('data'=>$recurso);
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


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$http_status = 200;
		$data = array();

		$parametros = Input::all();
		$recurso = null;

		if($parametros){
			if($parametros['mostrar'] == 'editar-estrategia'){
				$recurso = Estrategia::with('comentario')->find($id);
				//$recurso = Estrategia::find($id);
				if($recurso){
					$recurso['responsables'] = Directorio::responsablesActivos($recurso->claveUnidadResponsable)->get();
				}
			}else{
				$recurso = Estrategia::find($id);
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
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');

		$parametros = Input::all();
		
		try{

			//if($parametros['guardar'] != 'estrategia'){
				if(isset($parametros['id-estrategia'])){
					$id_estrategia = $parametros['id-estrategia'];
				}else{
					$id_estrategia = $id;
				}

				$estrategia = Estrategia::find($id_estrategia);

				if($estrategia->idEstatus != 1 && $estrategia->idEstatus != 3){
					switch ($estrategia->idEstatus) {
						case 2:
							$respuesta['data']['data'] = 'El programa se encuentra en proceso de revisión, por tanto no es posible editarlo';
							break;
						case 4:
							$respuesta['data']['data'] = 'El programa se encuentra registrado, por tanto no es posible editarlo';
							break;
						case 5:
							$respuesta['data']['data'] = 'El programa ya fue firmado, por tanto no es posible editarlo';
							break;
						default:
							$respuesta['data']['data'] = 'El estatus del programa es desconocido';
							break;
					}
					throw new Exception("El programa se encuentra en un estatus en el que no esta disponible para edición", 1);
				}
			//}
			
			if($parametros['guardar'] == 'validar-estrategia'){
				$recurso = Estrategia::find($id);

				$recurso->idEstatus = 2;
				if($recurso->save()){
					$respuesta['data']['data'] = 'El Programa Presupuestario fue enviado a revisión';
				}else{
					$respuesta['http_status'] = 500;
					$respuesta['data']['code'] = 'S01';
					$respuesta['data']['data'] = 'No fue posible enviar el programa a revisión';
				}
				
			}else if($parametros['guardar'] == 'estrategia'){
				$validacion = Validador::validar(Input::all(), $this->reglasEstrategia);
				if($validacion === TRUE){
					/*$estrategia = Estrategia::where('ejercicio','=',$parametros['ejercicio'])
										->where('claveProgramaPresupuestario','=',$parametros['programa-presupuestario'])->get();

					if(count($estrategia)){
						$respuesta['data']['data'] = 'Esta estrategia institucional ya se encentra capturado';
						throw new Exception("Estrategia Institucional encontrado", 1);
					}*/

					//$recurso = new Estrategia;
					$recurso = Estrategia::find($id);

					$recurso->claveUnidadResponsable = $parametros['unidad-responsable'];
					$recurso->claveProgramaSectorial = $parametros['programa-sectorial'];
					$recurso->claveProgramaPresupuestario = $parametros['programa-presupuestario'];
					$recurso->ejercicio = $parametros['ejercicio'];
					$recurso->idEstatus = 1;

					$recurso->idObjetivoPED = ($parametros['vinculacion-ped'])?$parametros['vinculacion-ped']:NULL;
					$recurso->idOdm = ($parametros['odm'])?$parametros['odm']:NULL;
					$recurso->mision = $parametros['mision'];
					$recurso->vision = $parametros['vision'];
					$recurso->descripcionIndicador = $parametros['descripcion-indicador'];
					$recurso->numerador = $parametros['numerador'];
					$recurso->denominador = $parametros['denominador'];
					$recurso->interpretacion = $parametros['interpretacion'];
					
					$recurso->idTipoIndicador = $parametros['tipo-ind'];
					$recurso->idDimensionIndicador = $parametros['dimension'];
					$recurso->idUnidadMedida = $parametros['unidad-medida'];
					$recurso->metaIndicador = $parametros['meta'];
					$recurso->lineaBase = $parametros['linea-base'];
					$recurso->anioBase = $parametros['anio-base'];
					$recurso->idFormula = $parametros['formula'];
					$recurso->idFrecuenciaIndicador = $parametros['frecuencia'];
					$recurso->trim1 = $parametros['trim1'];
					$recurso->trim2 = $parametros['trim2'];
					$recurso->trim3 = $parametros['trim3'];
					$recurso->trim4 = $parametros['trim4'];
					$recurso->valorNumerador = $parametros['valorNumerador'];
					$recurso->valorDenominador = $parametros['valorDenominador'];
					$recurso->fuenteInformacion = $parametros['fuente-informacion'];
					$recurso->idResponsable = $parametros['responsable'];

					if($recurso->update()){
						//$recurso['responsables'] = Directorio::responsablesActivos($recurso->claveUnidadResponsable)->get();
						$respuesta['data'] = array('data'=>$recurso);
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


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
