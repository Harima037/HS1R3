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
use BaseController, Input, Response, DB, Sentry, View, PDF;
use Excel, EvaluacionAnalisisFuncional, SysConfiguracionVariable, Proyecto;

class ReporteCedulaAvanceController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		try{
			$parametros = Input::all();

			if(!isset($parametros['mes'])){
				$mes = Util::obtenerMesActual();
				if($mes == 0){ $mes = date('n')-1; }
			}else{
				$mes = intval($parametros['mes']);
			}

			if(!isset($parametros['ejercicio'])){
				$ejercicio = date('Y');
			}else{
				$ejercicio = intval($parametros['ejercicio']);
			}

			if(isset($parametros['formatogrid'])){
				$rows = Proyecto::cedulasAvances($mes,$ejercicio);

				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }

				if(isset($parametros['buscar'])){		
					if($parametros['buscar']){
						$rows = $rows->where(function($query)use($parametros){
							$query->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%')
								->orWhere(DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'),'like','%'.$parametros['buscar'].'%');
						});
					}
				}
				
				$total = $rows->get();
				$total = count($total);
				
				//var_dump($total);die;
				//$queries = DB::getQueryLog();
				//var_dump(end($queries));die;

				$rows = $rows->skip(($parametros['pagina']-1)*10)->take(10)->get();

				$data = array('resultados'=>$total,'data'=>$rows);
				$http_status = 200;

				if($total<=0){
					$http_status = 404;
					$data = array('resultados'=>$total,"data"=>"No se encontraron variables",'code'=>'W00');
				}

				return Response::json($data,$http_status);
			}elseif(isset($parametros['resumen'])){
				$rows = Proyecto::reporteResumenAvances($mes,$ejercicio);
				
				if(isset($parametros['buscar'])){		
					if($parametros['buscar']){
						$rows = $rows->where(function($query)use($parametros){
							$query->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%')
								->orWhere(DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'),'like','%'.$parametros['buscar'].'%');
						});
					}
				}
				
				$rows = $rows->get()->toArray();

				foreach ($rows as $indice => $row) {
					$suma_porcentajes = 0;
					$total_porcentajes = count($row['componentes_metas_mes']) + count($row['actividades_metas_mes']);
					foreach ($row['componentes_metas_mes'] as $componente) {
						$suma_porcentajes = floatval($componente['porcentajeAcumulado']);
					}
					foreach ($row['actividades_metas_mes'] as $actividad) {
						$suma_porcentajes = floatval($actividad['porcentajeAcumulado']);
					}
					if($total_porcentajes > 0){
						$rows[$indice]['nivelCumplimientoFisico'] = number_format($suma_porcentajes / $total_porcentajes,2);
					}else{
						$rows[$indice]['nivelCumplimientoFisico'] = number_format(0,2);
					}

					$beneficiarios = NULL;
					if(count($row['registro_avance_beneficiarios']) > 1){
						if($row['evaluacion_mes']){
							if($row['evaluacion_mes']['indicadorResultadoBeneficiarios']){
								$beneficiarios = intval($row['evaluacion_mes']['indicadorResultadoBeneficiarios']);
							}else{
								$sum_avance = 0;
								foreach ($row['registro_avance_beneficiarios'] as $avance_benef) {
									$sum_avance += intval($avance_benef['avanceBeneficiario']);
								}
								if($sum_avance == 0){ $beneficiarios = 0; }
							}
						}else{
							$beneficiarios = 0;
						}
					}else{
						if(isset($row['registro_avance_beneficiarios'][0])){
							$beneficiarios = intval($row['registro_avance_beneficiarios'][0]['avanceBeneficiario']);
						}else{
							$beneficiarios = 0;
						}
					}

					if($beneficiarios !== NULL){
						$rows[$indice]['totalBeneficiarios'] = number_format($beneficiarios);
					}else{
						$rows[$indice]['totalBeneficiarios'] = 'Beneficiario no seleccionado';
					}
				}

				$datos = array('datos'=>$rows);
				$trimestre = Util::obtenerTrimestre($mes);
				$texto_trimestres = array(1=>'Primer',2=>'Segundo',3=>'Tercer',4=>'Cuarto');
				$datos['trimestre'] = $texto_trimestres[$trimestre];
				$datos['ejercicio'] = $ejercicio;

				//print_r($rows);die;

				$pdf = PDF::setPaper('LETTER')->setOrientation('landscape')->setWarnings(false)->loadView('reportes.pdf.reporte-resumen-avances',$datos);
				
				$pdf->output();
				$dom_pdf = $pdf->getDomPDF();
				$canvas = $dom_pdf->get_canvas();
				$w = $canvas->get_width();
		  		$h = $canvas->get_height();
				$canvas->page_text(($w-75), ($h-16), "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));
				

				return $pdf->stream('Resumen_Avances.pdf');
			}else{
				$rows = Proyecto::reporteCedulasAvances($mes,$ejercicio);

				if(isset($parametros['buscar'])){		
					if($parametros['buscar']){
						$rows = $rows->where(function($query)use($parametros){
							$query->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%')
								->orWhere(DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'),'like','%'.$parametros['buscar'].'%');
						});
					}
				}

				if(!isset($parametros['parte'])){
					$parte = 1;
				}else{
					$parte = intval($parametros['parte']);
				}

				//$rows = $rows->skip(($parte-1)*37)->take(37)->get()->toArray();
				$rows = $rows->get()->toArray();
				//var_dump($rows);die;
				//print_r($rows);die;

				//$queries = DB::getQueryLog();
				//var_dump($queries);die;

				$datos = array('datos'=>$rows);
				$datos['total_programado'] = 0;
				$datos['total_avance'] = 0;
				$datos['indices'] = array();
				$trimestre = Util::obtenerTrimestre($mes);
				$texto_trimestres = array(1=>'Primer',2=>'Segundo',3=>'Tercer',4=>'Cuarto');
				$datos['trimestre'] = $texto_trimestres[$trimestre];
				$datos['ejercicio'] = $ejercicio;
				
				$this->obtenerWord($datos);

				//return View::make('reportes.pdf.reporte-cedulas-avances')->with($datos);

				/*Excel::create('cedulasAvance', function($excel) use ( $datos ){
					$excel->sheet('Reporte', function($sheet) use ( $datos ){
						$sheet->loadView('reportes.excel.reporte-cedulas-avance',$datos);
					});
				})->export('xlsx');*/
	
				/*
				$pdf = PDF::setPaper('LETTER')->setOrientation('landscape')->setWarnings(false)->loadView('reportes.pdf.reporte-cedulas-avances',$datos);
				
				$pdf->output();
				$dom_pdf = $pdf->getDomPDF();
				$canvas = $dom_pdf->get_canvas();
				$w = $canvas->get_width();
		  		$h = $canvas->get_height();
				$canvas->page_text(($w-75), ($h-16), "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));
				
				return $pdf->stream('Cedulas_avances.pdf');
				*/
				
			}
		}catch(Exception $ex){
			return Response::json(array('data'=>'Ocurrio un error al generar el reporte.','message'=>$ex->getMessage(),'line'=>$ex->getLine()),500);
		}
	}

	function obtenerWord($datos){
		$phpWord = new \PhpOffice\PhpWord\PhpWord();

		$phpWord->setDefaultFontName('Arial');
		$phpWord->setDefaultFontSize(12);
		$phpWord->setDefaultParagraphStyle(
		    array(
		        'align'      => 'both',
		        'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0),
		        'spacing'    => 0,
		    )
		);

		$titulo = array('bold' => true);
		$texto = array('bold' => false);
		$centrado = array('align' => 'center');
		$justificado = array('align' => 'justify');
		$derecha = array('align' => 'right');

		$infoStyle = array('borderColor'=>'000000', 'borderSize'=>6);
		$claveStyle = array('borderColor'=>'000000','borderSize'=>6,'borderTopColor'=>'FFFFFF','cellMargin'=>200,'cellMarginTop'=>0);
		$headerStyle = array('borderColor'=>'FFFFFF','borderSize'=>0);

		$phpWord->addTableStyle('TablaInfo', $infoStyle);
		$phpWord->addTableStyle('TablaClave',$claveStyle);
		$phpWord->addTableStyle('TablaEncabezado',$headerStyle);

		$phpWord->addTitleStyle(1, $titulo, $justificado);
		$phpWord->addTitleStyle(2, $titulo, $justificado);
		$phpWord->addTitleStyle(3, $titulo, $justificado);
		$phpWord->addTitleStyle(4, $titulo, $justificado);
	    // Every element you want to append to the word document is placed in a section.
	    // To create a basic section:
	    $section = $phpWord->addSection(array('orientation'=>'landscape','size'=>'letter'));

		$header = $section->addHeader();
		$table = $header->addTable('TablaEncabezado');
		$row = $table->addRow();
		$row->addCell(3000)->addImage('img/EscudoGobiernoChiapas.png');
		$cell = $row->addCell(8128);
		$cell->addText(htmlspecialchars('INSTITUTO DE SALUD'),$titulo,$centrado);
		//$cell->addText(htmlspecialchars('ANÁLISIS FUNCIONAL AL '.$trimestres[$trimestre].' TRIMESTRE DEL '.date('Y')),$titulo,$centrado);
		$row->addCell(3000)->addImage('img/LogoInstitucional.png');
		$header->addTextBreak();

		$footer = $section->addFooter();
		$footer->addPreserveText(htmlspecialchars('Página {PAGE} de {NUMPAGES}.'), null,array('align' => 'right'));

		$section->addPageBreak();
		foreach ($datos['datos'] as $proyecto) {

			$table = $section->addTable('TablaEncabezado');

			$row = $table->addRow();
			$row->addCell(4000)->addText('Programa Presupuestario:',$titulo,$justificado);
			$row->addCell(10125)->addText(htmlspecialchars($proyecto['programaPresupuestarioDescipcion']),$texto,$justificado);

			$row = $table->addRow();
			if($proyecto['idClasificacionProyecto'] == 1){
				$tipo_proyecto = 'Institucional';
			}else{
				$tipo_proyecto = 'de Inversión';
			}
			$row->addCell(3900)->addText('Proyecto '.$tipo_proyecto.':',$titulo,$justificado);
			$row->addCell(10225)->addText(htmlspecialchars($proyecto['nombreTecnico']),$texto,$justificado);

			$row = $table->addRow();
			$row->addCell(3900)->addText('Clave Presupuestaria:',$titulo,$justificado);
			$row->addCell(10225)->addText(htmlspecialchars($proyecto['ClavePresupuestaria']),$texto,$justificado);

			$row = $table->addRow();
			$row->addCell(3900)->addText('Objetivo General:',$titulo,$justificado);
			$row->addCell(10225)->addText(htmlspecialchars($proyecto['finalidadProyecto']),$texto,$justificado);

			$section->addTextBreak();

			$table = $section->addTable('TablaInfo');
			$row = $table->addRow();
			$row->addCell(3500)->addText('Presupuesto Autorizado',$titulo,$centrado);
			$row->addCell(3500)->addText('Presupuesto Modificado',$titulo,$centrado);
			$row->addCell(3500)->addText('Presupuesto Ejercido',$titulo,$centrado);

			$row = $table->addRow();
			$row->addCell(3500)->addText(htmlspecialchars('$ '.$proyecto['presupuestoAprobado']),$texto,$derecha);
			$row->addCell(3500)->addText(htmlspecialchars('$ '.$proyecto['presupuestoModificado']),$texto,$derecha);
			$row->addCell(3500)->addText(htmlspecialchars('$ '.$proyecto['presupuestoEjercidoModificado']),$texto,$derecha);
			$section->addTextBreak();

			$table = $section->addTable('TablaInfo');

			$row = $table->addRow();
			$row->addCell(1025)->addText('Nivel',$titulo,$centrado);
			$row->addCell(4200)->addText('Indicador',$titulo,$centrado);
			$row->addCell(3000)->addText('Unidad/Medida',$titulo,$centrado);
			$row->addCell(2250)->addText('Meta',$titulo,$centrado);
			$row->addCell(2250)->addText('Avance',$titulo,$centrado);
			$row->addCell(1400)->addText('% Avance',$titulo,$centrado);

			foreach($proyecto['componentes'] AS $indice => $componente){
				$indices[$componente['id']]['indice'] = $indice+1;
				$indices[$componente['id']]['indiceActividad'] = 1;

				if($componente['avanceAcumulado']){
					$porcentaje = number_format(($componente['avanceAcumulado']*100)/$componente['metaAnual'],2);
				}else{
					$porcentaje = '0.00';
				}

				$row = $table->addRow();
				$row->addCell(1025)->addText('C '.$indice+1 ,$texto,$centrado);
				$row->addCell(4200)->addText(htmlspecialchars($componente['indicador']),$texto,$justificado);
				$row->addCell(3000)->addText(htmlspecialchars($componente['unidadMedida']),$texto,$centrado);
				$row->addCell(2250)->addText(number_format(floatval($componente['metaAnual']),2),$texto,$centrado);
				$row->addCell(2250)->addText(number_format(floatval($componente['avanceAcumulado']),2),$texto,$centrado);
				$row->addCell(1400)->addText($porcentaje,$texto,$centrado);
			}

			foreach($proyecto['actividades'] AS $indice => $actividad){
				$indice_componente = $indices[$actividad['idComponente']]['indice'];
				$indice_actividad = $indices[$actividad['idComponente']]['indiceActividad']++;

				if($actividad['avanceAcumulado']){
					$porcentaje = number_format(($actividad['avanceAcumulado']*100)/$actividad['metaAnual'],2);
				}else{
					$porcentaje = '0.00';
				}

				$row = $table->addRow();
				$row->addCell(1025)->addText('A '. $indice_componente . '.' . $indice_actividad ,$texto,$centrado);
				$row->addCell(4200)->addText(htmlspecialchars($actividad['indicador']),$texto,$justificado);
				$row->addCell(3000)->addText(htmlspecialchars($actividad['unidadMedida']),$texto,$centrado);
				$row->addCell(2250)->addText(number_format(floatval($actividad['metaAnual']),2),$texto,$centrado);
				$row->addCell(2250)->addText(number_format(floatval($actividad['avanceAcumulado']),2),$texto,$centrado);
				$row->addCell(1400)->addText($porcentaje,$texto,$centrado);
			}
			$section->addTextBreak();

			/*
			$table = $section->addTable('TablaInfo');

			$row = $table->addRow();
			$row->addCell(3000)->addText('Tipo de Beneficiario',$titulo,$centrado);
			$row->addCell(3000)->addText('Programado',$titulo,$centrado);
			$row->addCell(3065)->addText('Atendido',$titulo,$centrado);
			$row->addCell(5060)->addText('% Avance',$titulo,$centrado);

			$row = $table->addRow();
			$row->addCell(2500)->addText($elemento->ejeDescripcion);
			$row->addCell(2500)->addText($elemento->temaDescripcion);
			$row->addCell(4065)->addText($elemento->politicaPublicaDescripcion);
			$row->addCell(5060)->addText($elemento->programaPresupuestarioDescipcion);
			$section->addTextBreak();*/

			$section->addPageBreak();
			/*
			if($clasificacion_anterior != $elemento->idClasificacionProyecto){
				$section->addText(htmlspecialchars('OBJETIVOS Y PRINCIPALES COMENTARIOS DE LOS PROYECTOS INMERSOS EN ESTA SUBFUNCIÓN'),$titulo);
				$section->addTextBreak();
				if($elemento->idClasificacionProyecto == 1){
					$section->addTitle(htmlspecialchars('PROYECTOS INSTITUCIONALES'),3);
				}else{
					$section->addTitle(htmlspecialchars('PROYECTOS DE INVERSIÓN'),3);
				}
				$section->addTextBreak();
				$clasificacion_anterior = $elemento->idClasificacionProyecto;
			}

			$section->addTitle(htmlspecialchars('Proyecto: '.$elemento->nombreTecnico.' ('.$elemento->unidadResponsableDescipcion.')'),4);
			$section->addTextBreak();

			$table = $section->addTable('TablaInfo');

			$row = $table->addRow();
			$row->addCell(3000)->addText('EJE',$titulo,$centrado);
			$row->addCell(3000)->addText('TEMA',$titulo,$centrado);
			$row->addCell(3065)->addText('POLÍTICA PÚBLICA',$titulo,$centrado);
			$row->addCell(5060)->addText('PROGRAMA PRESUPUESTARIO',$titulo,$centrado);

			$row = $table->addRow();
			$row->addCell(2500)->addText($elemento->ejeDescripcion);
			$row->addCell(2500)->addText($elemento->temaDescripcion);
			$row->addCell(4065)->addText($elemento->politicaPublicaDescripcion);
			$row->addCell(5060)->addText($elemento->programaPresupuestarioDescipcion);
			$section->addTextBreak();

			if($elemento->totalMetas > 0){
				if($elemento->cuentaPublica){
					$section->addText(htmlspecialchars($elemento->cuentaPublica),$texto,$justificado);
				}else{
					$section->addText(htmlspecialchars('No presentaron avances.'),$texto,$justificado);
				}
			}else{
				if($elemento->cuentaPublica){
					$section->addText(htmlspecialchars($elemento->cuentaPublica),$texto,$justificado);
				}else{
					$section->addText(htmlspecialchars('No hay metas programadas para este trimestre.'),$texto,$justificado);
				}
			}
			$section->addTextBreak();
			*/
		}

		header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
	    header("Content-Description: File Transfer");
		header('Content-Disposition: attachment; filename="CedulasAvance.docx"');
	    
	    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
	    $objWriter->save('php://output');
	}
}
?>