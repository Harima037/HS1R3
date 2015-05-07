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
use Excel, Programa, ProgramaIndicador, RegistroAvancePrograma, EvaluacionProgramaTrimestre; 

class ReporteEvaluacionProgramaController extends BaseController {

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id){
		$nombreArchivo = 'ProgramaPresupuestario';
		$trimestre_actual = Util::obtenerTrimestre();

		$recurso = Programa::with(array('indicadores.registroAvance'=>function($query) use ($trimestre_actual){
			$query->where('trimestre','<=',$trimestre_actual);
		}))->select('programa.*','programaPresupuestal.descripcion as programaPresupuestario','titulares.nombre as liderPrograma',
					'titulares.cargo as cargoLiderPrograma')
		->join('catalogoProgramasPresupuestales AS programaPresupuestal','programaPresupuestal.clave','=','programa.claveProgramaPresupuestario')
		->join('titulares','titulares.id','=','programa.idLiderPrograma')
		->find($id);

		Excel::create($nombreArchivo, function($excel) use ($recurso, $trimestre_actual){

			$datos['trimestre'] = $trimestre_actual;

			$datos['programa'] = array(
				'ejercicio' => $recurso->ejercicio,
				'nombre' => $recurso->programaPresupuestario,
				'liderPrograma' => $recurso->liderPrograma,
				'cargoLiderPrograma' => $recurso->cargoLiderPrograma
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

				for ($i=1; $i <= $trimestre_actual ; $i++) { 
					$datos_indicador['meta_original'] += $metas[$i];
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
			
			$excel->sheet('PP', function($sheet)  use ($datos){
		        $sheet->loadView('rendicion-cuentas.excel.programa-metas-trimestre', $datos);
		        $imagen = $this->obtenerImagen('LogoFederal.png','A1');
				$imagen->setWorksheet($sheet);
		        $imagen = $this->obtenerImagen('LogoInstitucional.png','H1');
				$imagen->setWorksheet($sheet);
		    });
		    $excel->getActiveSheet()->getStyle('A7:H7')->getAlignment()->setWrapText(true);
			$excel->getActiveSheet()->getStyle('A9:H9')->getAlignment()->setWrapText(true);

			$excel->getActiveSheet()->getStyle('A10:H11')->getAlignment()->setWrapText(true);
		})->download('xls');
	}

	private function obtenerImagen($imagen,$celda,$offset = 10){
		$objDrawing = new \PHPExcel_Worksheet_Drawing();
		$objDrawing->setPath('./img/'.$imagen);// filesystem reference for the image file
		$objDrawing->setHeight(100);// sets the image height to 36px (overriding the actual image height); 
		$objDrawing->setWidth(200);// sets the image height to 36px (overriding the actual image height); 
		$objDrawing->setCoordinates($celda);// pins the top-left corner of the image to cell D24
		$objDrawing->setOffsetX($offset);// pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
		return $objDrawing;
	}
}