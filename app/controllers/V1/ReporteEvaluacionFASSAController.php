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
use PDF,IndicadorFASSAMeta;

class ReporteEvaluacionFASSAController extends BaseController {

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id){
		
		$recurso = IndicadorFASSAMeta::indicadorMetaDetalle()->find($id);
		if($recurso){
			$parametros = Input::all();
			
			if(isset($parametros['mes'])){
				$mes_actual = $parametros['mes'];
			}else{
				$mes_actual = Util::obtenerMesActual();
				if($mes_actual == 0){
					$mes_actual = date('n')-1;
					if($mes_actual == 0){
						$mes_actual = 12;
					}
				}
			}
			$trimestre = ceil($mes_actual/3);

			$recurso->load(array(
				'registroAvance'=>function($query)use($mes_actual){
					return $query->where('mes','=',$mes_actual);
				},
				'metasTrimestre'=>function($query)use($trimestre){
					return $query->where('trimestre','=',$trimestre);
				}
			));
			
			if($recurso->idEstatus < 4){
				return Response::json(array('data'=>'El recurso no se encuentra disponible para impresión'),500);
			}
			if(count($recurso->registroAvance) == 0){
				return Response::json(array('data'=>'No se encontraron registros de seguimiento'),404);
			}
			if($recurso->registroAvance[0]->idEstatus < 4){
				return Response::json(array('data'=>'El recurso no se encuentra disponible para impresión'),500);
			}
			
			$niveles 			= array('F'=>'Fin','P'=>'Propósito','C'=>'Componente','A'=>'Actividad');
			$tipos_formulas 	= array('P'=>'Porcentaje','T'=>'Tasa');
			
			$indicador['ejercicio'] = $recurso->ejercicio;
			$indicador['nivel'] = $niveles[$recurso->claveNivel];
			$indicador['indicador'] = $recurso->indicador;
			$indicador['tipoFormula'] = $tipos_formulas[$recurso->claveTipoFormula];
			$indicador['formula'] = $recurso->formula;
			$indicador['fuenteInformacion'] = $recurso->fuenteInformacion;	
			
			$indicador['metaNumerador'] = number_format($recurso->metasTrimestre[0]->numerador,2);
			$indicador['metaDenominador'] = number_format($recurso->metasTrimestre[0]->denominador,2);
			$indicador['metaPorcentaje'] = number_format($recurso->metasTrimestre[0]->porcentaje,2);
			
			$indicador['responsableInformacion'] = $recurso->nombreResponsableInformacion;
			$indicador['liderPrograma'] = $recurso->nombreLiderPrograma;
			$indicador['cargoResponsableInformacion'] = $recurso->cargoResponsableInformacion;
			$indicador['cargoLiderPrograma'] = $recurso->cargoLiderPrograma;
			
			$indicador['avanceNumerador'] = number_format($recurso->registroAvance[0]->numerador,2);
			$indicador['avanceDenominador'] = number_format($recurso->registroAvance[0]->denominador,2);
			$indicador['avancePorcentaje'] = number_format($recurso->registroAvance[0]->porcentaje,2);
			$indicador['desempenio'] = number_format(($recurso->registroAvance[0]->porcentaje / $recurso->metasTrimestre[0]->porcentaje)*100,2);
			$indicador['justificacion'] = $recurso->registroAvance[0]->justificacionAcumulada;
			
			$datos['indicador'] = $indicador;
			
			$pdf = PDF::setPaper('LETTER')->setOrientation('landscape')->setWarnings(false)->loadView('rendicion-cuentas.pdf.indicador-desempenio-fassa',$datos);
			return $pdf->stream('desempeñoFASSA.pdf');
		}else{
			return Response::json(array('data'=>'Recurso no encontrado'),404);
		}
	}
}