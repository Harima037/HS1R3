<?php
/* 
*	SIRE
*	Sistema de Integración, Rendición de cuentas y Evaluación
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

use SSA\Utilerias\Util;
use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, View;
use Estrategia, PDF;

class ReporteSeguimientoEstrategiaController extends BaseController {

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
			$rows = Estrategia::getModel();

			if(isset($parametros['ejercicio'])){
				$ejercicio = $parametros['ejercicio'];
			}else{
				$ejercicio = Util::obtenerAnioCaptura();
			}

			$rows = $rows->where('estrategia.idEstatus','=',5)->where('ejercicio',$ejercicio);
			
			$usuario = Sentry::getUser();
			if($usuario->idDepartamento == 2){
				if($usuario->filtrarProgramas){
					$rows = $rows->where('idUsuarioValidacionSeg','=',$usuario->id);
				}
			}else{
				$rows = $rows->where('idUsuarioRendCuenta','=',$usuario->id);
			}
			
			$rows = $rows->with(array('registroAvance'=>function($query){
				$query->select('id','idEstrategia','trimestre',DB::raw('sum(justificacion) AS justificacion'),
								DB::raw('count(idEstrategia) AS registros'))->groupBy('idEstrategia','trimestre');
			},'evaluacionTrimestre'=>function($query){
				$query->whereIn('idEstatus',array(4,5));
			}));

			if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
			
			$total = $rows->count();
			
			
			$rows = $rows->select('estrategia.id','estrategia.descripcionIndicador as descripcion')
					->orderBy('id', 'desc')
					->groupBy('estrategia.id')
					->skip(($parametros['pagina']-1)*10)->take(10)
					->get();

			$data = array('resultados'=>$total,'data'=>$rows);

			if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
			}

			return Response::json($data,$http_status);
		}
		
		$rows = Estrategia::all();

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
		$nombreArchivo = 'EstrategiaInstitucional';
		$parametros =Input::all();
		 
		if(isset($parametros['trimestre'])){
			$trimestre_actual = $parametros['trimestre'];
		}else{
			$trimestre_actual = Util::obtenerTrimestre();
		}
		
		$recurso = Estrategia::with(array('registroAvance'=>function($query) use ($trimestre_actual){
			$query->where('trimestre','<=',$trimestre_actual);
		}))->select('estrategia.*','titular.nombre as liderPrograma', //'programaPresupuestal.descripcion as programaPresupuestario',
					'titular.cargo as cargoLiderPrograma','responsable.nombre as responsableInformacion',
					'responsable.cargo as cargoResponsableInformacion')
		//->leftjoin('catalogoProgramasPresupuestales AS programaPresupuestal','programaPresupuestal.clave','=','estrategia.claveProgramaPresupuestario')
		->leftjoin('vistaDirectorio as titular','titular.id','=','estrategia.idLiderPrograma')
		->leftjoin('vistaDirectorio as responsable','responsable.id','=','estrategia.idResponsable')
		->find($id);

		
		$datos['trimestre'] = $trimestre_actual;
		$datos['estrategia'] = array(
			'ejercicio' => $recurso->ejercicio,
			'nombre' => $recurso->descripcionIndicador,
			'fuenteInformacion' => $recurso->fuenteInformacion,
			'liderPrograma' => $recurso->liderPrograma,
			'cargoLiderPrograma' => $recurso->cargoLiderPrograma,
			'responsableInformacion' => $recurso->responsableInformacion,
			'cargoResponsableInformacion' => $recurso->cargoResponsableInformacion
		);

	
		$datos['indicadores'] = array();

		//foreach ($recurso->indicadores as $indicador) {
			$metas = array(
				1 => $recurso->trim1,
				2 => $recurso->trim2,
				3 => $recurso->trim3,
				4 => $recurso->trim4
			);

			$datos_indicador = array(
				'indicador' => $recurso->descripcionIndicador,
				'meta_original' => 0,
				'avance_trimestre' => 0,
				'avance_acumulado' => 0,
				'analisis_resultados' => '',
				'justificacion_acumulada' => ''
			);

			

			foreach ($recurso->registroAvance as $registro_avance) {
				if($registro_avance->trimestre == $trimestre_actual){
					$datos_indicador['avance_trimestre'] = $registro_avance->avance;
					$datos_indicador['analisis_resultados'] = $registro_avance->analisisResultados;
					$datos_indicador['justificacion_acumulada'] = $registro_avance->justificacionAcumulada;
				}
				$datos_indicador['avance_acumulado'] += $registro_avance->avance;
			}

			for ($i=1; $i <= 4 ; $i++) { 
				if(count($metas)>=$i){
				$datos_indicador['meta_original'] += $metas[$i];
				$datos_indicador['meta_trimestral']=$metas[$trimestre_actual];
				}
			}

			$datos['indicadores'][] = $datos_indicador;
		//}

		switch ($trimestre_actual) {
			case 1:
				$datos['trimestre_lbl'] = 'Primero';
				break;
			case 2:
				$datos['trimestre_lbl'] = 'Segundo';
				break;
			case 3:
				$datos['trimestre_lbl'] = 'Tercero';
				break;
			case 4:
				$datos['trimestre_lbl'] = 'Cuarto';
				break;
		}
		//return $datos;
		$pdf = PDF::setPaper('LETTER')
					->setOrientation('landscape')
					->setWarnings(false)
					->loadView('rendicion-cuentas.pdf.estrategia-metas-trimestre',$datos);
		return $pdf->stream($nombreArchivo.'.pdf');
	}
}