<?php

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, Hash, Exception,DateTime;
use Estrategia, Directorio, EstrategiaComentario;

class RevisionEstrategiaInstitucionalController extends BaseController {
		
	private $reglasComentario = array(
			'idestrategia' => 'required',
			'idcampo' => 'required',
			'comentario' => 'required'
		);

	public function index(){
		//
		$http_status = 200;
		$data = array();

		$parametros = Input::all();

		if(isset($parametros['formatogrid'])){

           
				$rows = Estrategia::getModel()->wherein('estrategia.idEstatus',array(2, 4));

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
				
				$rows = $rows->select('estrategia.id','catalogoProgramasPresupuestales.clave','catalogoProgramasPresupuestales.descripcion AS estrategia', 'estrategia.descripcionIndicador as descripcion',
							'estrategia.idEstatus','catalogoEstatusProyectos.descripcion as estatus','sentryUsers.username','estrategia.modificadoAl')
							->join('sentryUsers','sentryUsers.id','=','estrategia.actualizadoPor')
							->join('catalogoProgramasPresupuestales','catalogoProgramasPresupuestales.clave','=','estrategia.claveProgramaPresupuestario')
							->join('catalogoEstatusProyectos','catalogoEstatusProyectos.id','=','estrategia.idEstatus')
							//->where('programa.idEstatus','<',5)
							
							->orderBy('id', 'desc')
							->skip(($parametros['pagina']-1)*10)->take(10)
							->get();
			

			$data = array('resultados'=>$total,'data'=>$rows);

			if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
			}

		}else{
			$rows = Programa::all();

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
	public function show($id)
	{
		$http_status = 200;
		$data = array();

		$parametros = Input::all();
		$recurso = null;

		if($parametros){
			if($parametros['mostrar'] == 'editar-estrategia'){
				$recurso = Estrategia::find($id)->load("programaPresupuestario", "formula", "frecuencia", "unidadResponsable", "programaSectorial", "odm", "ped", "tipoIndicador", "dimension", "unidadMedida", "comentario", "responsable");
				
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
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(){
		//
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');

		$parametros = Input::all();

		$nuevoComentario = new EstrategiaComentario;
		
		$nuevoComentario->idEstrategia = $parametros['idestrategia'];
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
		
		if(isset($parametros['actualizarestrategia']))
		{
			if($parametros['actualizarestrategia']=="aprobar") //Poner estatus 4 (Aprobado)
			{
				$recurso = Estrategia::find($id);
				if(is_null($recurso)){
					$respuesta['http_status'] = 404;
					$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
				}else{
					$recurso->idEstatus = 4;
					$recurso->save();
				}
			}
			else if($parametros['actualizarestrategia']=="regresar") //Poner estatus 3 (Regreso a corrección)
			{
				$recurso = Estrategia::find($id);
				if(is_null($recurso)){
					$respuesta['http_status'] = 404;
					$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
				}else{
					$recurso->idEstatus = 3;
					$recurso->save();
				}
			}
			else if($parametros['actualizarestrategia']=="firmar") //Poner estatus 5 (Enviar a firma)
			{
				$recurso = Estrategia::find($id);
				if(is_null($recurso)){
					$respuesta['http_status'] = 404;
					$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
				}else{
					$recurso->idEstatus = 5;
					$recurso->save();
				}
			}
			
		}
		else
		{
			$recurso = EstrategiaComentario::find($id);
			if(is_null($recurso)){
				$respuesta['http_status'] = 404;
				$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
			}else{
				$recurso->idEstrategia = $parametros['idestrategia'];
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
		$http_status = 200;
		$data = array();

		try{
			$recurso = EstrategiaComentario::where('id','=',$id)->delete();
			
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