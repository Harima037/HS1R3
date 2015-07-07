<?php

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry,Exception;
use Hash, File, BitacoraCargaEPRegion, CargaDatosEPRegion;

class EPRegionalizadoController extends \BaseController {
	private $reglas = array(
			'mes' => 'required',
			'ejercicio' => 'sometimes|required'
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
				
				$rows = BitacoraCargaEPRegion::getModel();

				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
				
				if(isset($parametros['buscar'])){				
					$rows = $rows->where('bitacoraCargaEPRegion.ejercicio','like','%'.$parametros['buscar'].'%')
							->where('bitacoraCargaEPRegion.mes','like','%'.$parametros['buscar'].'%');
					$total = $rows->count();
				}else{				
					$total = $rows->count();						
				}

				$rows = $rows->select('bitacoraCargaEPRegion.id','ejercicio','mes','totalRegistros','totalImporte',
										'usuario.username','bitacoraCargaEPRegion.modificadoAl')
									->leftjoin('sentryUsers AS usuario','usuario.id','=','bitacoraCargaEPRegion.actualizadoPor')
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

			$rows = BitacoraCargaEPRegion::all();

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

		$recurso = BitacoraCargaEPRegion::find($id);

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
	public function store() //POST
	{
		$respuesta = array();
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');	
		$lineasConErrorEnCampos = "";
		$lineasConErrorEjercicio = "";
		$errorNumeroCampos = 0;
		$parametros = Input::all();

		if (Input::hasFile('datoscsv')){			
			$finfo = finfo_open(FILEINFO_MIME_TYPE); 
			$archivoConDatos = Input::file('datoscsv');
			$type = finfo_file($finfo, $archivoConDatos); 
			$idusuario = Sentry::getUser()->id;
			$fechahora = date("d").date("m").date("Y").date("H").date("i").date("s");
			$nombreArchivo = 'ARCHIVO'.$idusuario.$fechahora;
			$idInsertado ='';
			$numeroRegistros = '';
			
			if($type=="text/plain") //Si el Mime coincide con CSV
			{
				$row = 1;
				if (($handle = fopen($archivoConDatos, "r")) !== FALSE) {
					$ejercicio = $parametros['ejercicio'];
					while (($data2 = fgetcsv($handle, 1000, ",")) !== FALSE) {
						if($row > 1){
							if(count($data2) < 20){ //Número de columnas de cada línea, para validar si todos los campos se tienen
								$lineasConErrorEnCampos = $lineasConErrorEnCampos . $row . ", ";
								$errorNumeroCampos = 1;
							}
							if($data2[16] != $ejercicio){
								$lineasConErrorEjercicio = $lineasConErrorEjercicio . $row . ", ";
								$errorNumeroCampos = 1;
							}
						}
						$row++;
				    }

					if($errorNumeroCampos == 1){
						$respuesta['http_status'] = 404;
						$errores = '';
						if($lineasConErrorEnCampos != ""){
							$errores .= "Error en los datos, las lineas ".$lineasConErrorEnCampos."no están completas";
						}
						if($lineasConErrorEjercicio != ""){
							$errores .= "Error en los datos, las lineas ".$lineasConErrorEjercicio."no corresponden al ejercicio proporcionado";
						}
						$respuesta['data'] = array("data"=>$errores,'code'=>'U06');
					}else{
						$recurso = new BitacoraCargaEPRegion;
						$recurso->mes = $parametros['mes'];
						$recurso->ejercicio = $parametros['ejercicio'];
						
						$validarEjercicioMes = BitacoraCargaEPRegion::getModel();
						$validarEjercicioMes = $validarEjercicioMes->where('mes','=',$parametros['mes'])
														 ->where('ejercicio','=',$parametros['ejercicio'])->count();
						//
						if($validarEjercicioMes){
							$respuesta['http_status'] = 404;
							$respuesta['data'] = array("data"=>"Ya se cuenta con la carga de datos del mes y ejercicio especificados.",'code'=>'U06');
						}else{
							$resultado = Validador::validar($parametros, $this->reglas);		
							if($resultado === true){
								$destinationPath = storage_path().'/archivoscsv/';

								$upload_success = Input::file('datoscsv')->move($destinationPath, $nombreArchivo.".csv");
								$csv = $destinationPath . $nombreArchivo.".csv";

								try {
	 								DB::connection()->getPdo()->beginTransaction();
									$recurso->save();
									$idInsertado = $recurso->id;
									$query = sprintf("
										LOAD DATA local INFILE '%s' 
										INTO TABLE cargaDatosEPRegion 
										FIELDS TERMINATED BY ',' 
										OPTIONALLY ENCLOSED BY '\"' 
										ESCAPED BY '\"' 
										LINES TERMINATED BY '\\n' 
										IGNORE 1 LINES 
										(`UR`,`FI`,`FU`,`SF`,`SSF`,`PS`,`PP`,`PE`,`AI`,`PT`,`MPIO`,`OG`,`STG`,`FF`,`SFF`,`DG`,`CP`,
										`DM`,`clave`,`importe`) 
										set idBitacoraCargaEPRegion='%s', mes='%s'
										", addslashes($csv), $idInsertado, $parametros['mes']);
									DB::connection()->getpdo()->exec($query);

									$conteoTotales = CargaDatosEPRegion::getModel();
									$conteoTotales = $conteoTotales->select(
										DB::raw('COUNT(id) AS registros'),
										DB::raw('SUM(importe) AS totalImporte')
									);
									$conteoTotales = $conteoTotales->where('idBitacoraCargaEPRegion','=',$idInsertado)->first();
									
									$recurso->totalRegistros = $conteoTotales->registros;
									$recurso->totalImporte = $conteoTotales->totalImporte;

									$recurso->save();

									DB::connection()->getPdo()->commit();

									$respuesta['data'] = array('data'=>$recurso);
								}catch (\PDOException $e){
									$respuesta['http_status'] = 404;
									$respuesta['data'] = array("data"=>"Ha ocurrido un error, no se pudieron cargar los datos. Verfique su conexión a Internet.",'code'=>'U06');
								    DB::connection()->getPdo()->rollBack();
								    throw $e;
								}catch(Exception $e){
									$respuesta['http_status'] = 500;
									$respuesta['data'] = array("data"=>"",'ex'=>$e->getMessage(),'line'=>$e->getLine(),'code'=>'S02');
								}

								File::delete($csv);
							}else{
								$respuesta = $resultado;
							}
						}
					}
				}
				fclose($handle);
			}
			else
			{
				$respuesta['http_status'] = 404;
				$respuesta['data'] = array("data"=>"Formato de archivo incorrecto.",'code'=>'U06');
			}
		}
		else
		{
			$respuesta['http_status'] = 404;
			$respuesta['data'] = array("data"=>"No se encontró el archivo.",'code'=>'U06');
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
			$recurso = BitacoraCargaEPRegion::find($id);
			$parametros = Input::all();

			if(is_null($recurso)){
				$respuesta['http_status'] = 404;
				$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
			}else{
				$validarEjercicioMes = BitacoraCargaEPRegion::getModel();
				$validarEjercicioMes = $validarEjercicioMes->where('mes','=',$parametros['mes'])
												 ->where('ejercicio','=',$recurso->ejercicio)
												 ->where('id','<>',$recurso->id)
												 ->count();
				//
				if($validarEjercicioMes){
					$respuesta['http_status'] = 404;
					$respuesta['data'] = array("data"=>"Ya ha asignado el mes previamente a otra carga de datos.",'code'=>'U06');
				}else{
					$recurso->mes	= $parametros['mes'];
					
					$resultado = Validador::validar($parametros, $this->reglas);
				
					if($resultado===true){
						DB::transaction(function()use($recurso){
							if($recurso->save()){
								CargaDatosEPRegion::where('idBitacoraCargaEPRegion','=',$recurso->id)
												->update(array('mes'=>$recurso->mes));
							}else{
								throw new Exception("No se pudieron actualizar los datos de la carga", 1);
							}
						});
					}else{
						$respuesta = $resultado;
					}
				}
			}
		}catch(Exception $e){
			$respuesta['http_status'] = 500;
			$respuesta['data'] = array("data"=>"Ocurrio un error en el servidor al momento de realizar la operación.",'ex'=>$e->getMessage(),'line'=>$e->getLine(),'code'=>'S02');
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
			$ids = Input::get('rows');

			$rows = DB::transaction(function()use($ids){
				CargaDatosEPRegion::whereIn('idBitacoraCargaEPRegion',$ids)->delete();
				$rows = BitacoraCargaEPRegion::whereIn('id', $ids)->delete();
				return $rows;
			});

			if($rows>0){
				$data = array("data"=>"Se han eliminado los registros.");
			}else{
				$http_status = 404;
				$data = array('data' => "No se pueden eliminar los registros.",'code'=>'S03');
			}	
		}catch(Exception $ex){
			$http_status = 500;	
			$data = array('data' => "No se pueden borrar los registros",$ex->getMessage(),'code'=>'S03');	
		}

		return Response::json($data,$http_status);
		
	}

}