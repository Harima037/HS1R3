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

use SSA\Utilerias\Util;
use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, View;
use Excel,PDF, Programa, ProgramaIndicador, RegistroAvancePrograma, EvaluacionProgramaTrimestre; 

class ReporteEvaluacionProgramaController extends BaseController {

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id){
		$nombreArchivo = 'ProgramaPresupuestario';
		$parametros =Input::all();
		 
		if(isset($parametros['trimestre'])){
			$trimestre_actual = $parametros['trimestre'];
		}else{
			$trimestre_actual = Util::obtenerTrimestre();
		}
		
		$recurso = Programa::with(array('indicadores.registroAvance'=>function($query) use ($trimestre_actual){
			$query->where('trimestre','<=',$trimestre_actual);
		}))->select('programa.*','programaPresupuestal.descripcion as programaPresupuestario','titular.nombre as liderPrograma',
					'titular.cargo as cargoLiderPrograma','responsable.nombre as responsableInformacion',
					'responsable.cargo as cargoResponsableInformacion')
		->leftjoin('catalogoProgramasPresupuestales AS programaPresupuestal','programaPresupuestal.clave','=','programa.claveProgramaPresupuestario')
		->leftjoin('vistaDirectorio as titular','titular.id','=','programa.idLiderPrograma')
		->leftjoin('vistaDirectorio as responsable','responsable.id','=','programa.idResponsable')
		->find($id);


		$datos['trimestre'] = $trimestre_actual;
		$datos['programa'] = array(
			'ejercicio' => $recurso->ejercicio,
			'nombre' => $recurso->programaPresupuestario,
			'fuenteInformacion' => $recurso->fuenteInformacion,
			'liderPrograma' => $recurso->liderPrograma,
			'cargoLiderPrograma' => $recurso->cargoLiderPrograma,
			'responsableInformacion' => $recurso->responsableInformacion,
			'cargoResponsableInformacion' => $recurso->cargoResponsableInformacion
		);

		$datos['indicadores'] = array();

		foreach ($recurso->indicadores as $indicador) {
			$metas = array(
				1 => $indicador->trim1,
				2 => $indicador->trim2,
				3 => $indicador->trim3,
				4 => $indicador->trim4
			);

			$datos_indicador = array(
				'nivel' => '',
				'indicador' => $indicador->descripcionIndicador,
				'meta_original' => 0,
				'avance_trimestre' => 0,
				'avance_acumulado' => 0,
				'analisis_resultados' => '',
				'justificacion_acumulada' => ''
			);

			if($indicador->claveTipoIndicador == 'F'){
				$datos_indicador['nivel'] = 'Fin';
			}else{
				$datos_indicador['nivel'] = 'Proposito';
			}

			foreach ($indicador->registroAvance as $registro_avance) {
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
		}

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
		
		$pdf = PDF::setPaper('LETTER')
					->setOrientation('landscape')
					->setWarnings(false)
					->loadView('rendicion-cuentas.pdf.programa-metas-trimestre',$datos);
		return $pdf->stream($nombreArchivo.'.pdf');
	}
}