<?php

namespace V1;

use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, Hash, Exception,DateTime;
use Programa, ProgramaArbolProblema, ProgramaArbolObjetivo, ProgramaIndicador, Proyecto, ProgramaComentario;

class RevisionProgramaController extends BaseController {
		
	private $reglasComentario = array(
			'idprograma' => 'required',
			'idcampo' => 'required',
			'comentario' => 'required'
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
							//->where('programa.idEstatus','<',5)
							->wherein('programa.idEstatus',array(2, 4, 5))
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
	public function show($id){
		//
		$http_status = 200;
		$data = array();

		$parametros = Input::all();

		if($parametros){
			if($parametros['mostrar'] == 'editar-programa'){
				$recurso = Programa::contenidoDetalle()->with('comentario')->find($id);
			}elseif($parametros['mostrar'] == 'editar-causa-efecto'){
				$recurso = ProgramaArbolProblema::find($id);
			}elseif($parametros['mostrar'] == 'editar-medio-fin'){
				$recurso = ProgramaArbolObjetivo::find($id);
			}elseif($parametros['mostrar'] == 'editar-indicador'){
				$recurso = ProgramaIndicador::contenidoDetalle()->find($id);				

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

		$nuevoComentario = new ProgramaComentario;
		
		$nuevoComentario->idPrograma = $parametros['idprograma'];
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
		
		if(isset($parametros['actualizarprograma']))
		{
			if($parametros['actualizarprograma']=="aprobar") //Poner estatus 4 (Aprobado)
			{
				$recurso = Programa::find($id);
				if(is_null($recurso)){
					$respuesta['http_status'] = 404;
					$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
				}else{
					$recurso->idEstatus = 4;
					$recurso->save();
				}
			}
			else if($parametros['actualizarprograma']=="regresar") //Poner estatus 3 (Regreso a corrección)
			{
				$recurso = Programa::find($id);
				if(is_null($recurso)){
					$respuesta['http_status'] = 404;
					$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
				}else{
					$recurso->idEstatus = 3;
					$recurso->save();
				}
			}
			else if($parametros['actualizarprograma']=="firmar") //Poner estatus 5 (Enviar a firma)
			{
				$recurso = Programa::find($id);
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
			$recurso = ProgramaComentario::find($id);
			if(is_null($recurso)){
				$respuesta['http_status'] = 404;
				$respuesta['data'] = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
			}else{
				$recurso->idPrograma = $parametros['idprograma'];
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
			$recurso = ProgramaComentario::where('id','=',$id)->delete();
			
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