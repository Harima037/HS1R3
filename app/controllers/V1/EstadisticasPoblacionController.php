<?php

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry,Exception;
use Hash, File, ControlArchivos;

class EstadisticasPoblacionController extends \BaseController {

    private $reglas = array(
        'titulo' => 'required',
        'archivo-poblacion' => 'required'
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
			$archivos = ControlArchivos::where('claveGrupo','EST-POB')->get();

			if(count($archivos) == 0){
				$data = array("data"=>[]);
			}else{
				$data = array("data"=>$archivos->toArray());
			}
		}catch(\Exception $e){
			$http_status = 404;
			$data = array("data"=>"",'ex'=>$e->getMessage(),'line'=>$e->getLine(),'code'=>'S02');
		}

		return Response::json($data,$http_status);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(){
		$respuesta = array();
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');

		$parametros = Input::all();

        try{
			
            if (Input::hasFile('archivo-poblacion')){
				if(isset($_FILES['archivo-poblacion'])) {
					if($_FILES['archivo-poblacion']['size'] > 5242880) { //5 MB (size is also in bytes)
						$respuesta['data'] = array('data' => "Error, el archivo no puede ser mayor a 5MB",'code'=>'S03');
						throw new Exception("EXEP-MSG-PER", 1);
					}
				}
                $finfo = finfo_open(FILEINFO_MIME_TYPE); 
                $archivoConDatos = Input::file('archivo-poblacion');
    
                $type = finfo_file($finfo, $archivoConDatos); 
    
                $idusuario = Sentry::getUser()->id;
                $fechahora = date("d").date("m").date("Y").date("H").date("i").date("s");
    
                $nombreArchivo = 'EST-POB-'.$idusuario.$fechahora;
                
                $destinationPath = storage_path().'/estadistica-poblacion/';
                $upload_success = Input::file('archivo-poblacion')->move($destinationPath, $nombreArchivo.".pdf");
                
                if($type == "application/pdf"){
                    $resultado = Validador::validar($parametros, $this->reglas);		
					if($resultado === true){
                        $recurso = new ControlArchivos();
                        $recurso->titulo = $parametros['titulo'];
                        $recurso->nombre = $nombreArchivo;
                        $recurso->directorio = '/estadistica-poblacion/';
                        $recurso->claveGrupo = 'EST-POB';
                        $recurso->extension = 'PDF';
                        $recurso->save();

                        $respuesta['data'] = $recurso;
                    }else{
                        $respuesta = $resultado;
                    }
                }else{
                    $respuesta['http_status'] = 404;
                    $respuesta['data'] = array("data"=>"Formato de archivo incorrecto.",'code'=>'U06');
                }
            }

		}catch(Exception $ex){
			$respuesta['http_status'] = 500;
			if($ex->getMessage() != 'EXEP-MSG-PER'){
				$respuesta['data'] = array('data' => "Error al subir los archivos",'msg' => $ex->getMessage(),'code'=>'S03');
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
	public function destroy($id){
		//
		$http_status = 200;
		$data = array();

		try{
			$archivo = ControlArchivos::find($id);

			$path = storage_path().$archivo->directorio.$archivo->nombre.'.'.$archivo->extension;
			File::delete($path);

			if($archivo->delete()){
				$data = array("data"=>"Se ha eliminado el archivo seleccionado.");
			}else{
				$http_status = 404;
				$data = array('data' => "No se puede eliminar el archivo seleccionado.",'code'=>'S03');
			}	
		}catch(Exception $ex){
			$http_status = 500;	
			$data = array('data' => "Error al elminar el archivo seleccionado",$ex->getMessage(),'code'=>'S03');	
		}

		return Response::json($data,$http_status);
		
	}

}