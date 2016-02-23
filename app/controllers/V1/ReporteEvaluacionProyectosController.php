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
use Excel, EvaluacionAnalisisFuncional, SysConfiguracionVariable, Proyecto, Jurisdiccion,CargaDatosEP01;
//use elliottb\phpgraphlib;
require_once base_path('vendor/elliottb/phpgraphlib/phpgraphlib.php');
//use \PHPGraphLib;

class ReporteEvaluacionProyectosController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		try{
			$parametros = Input::all();

			//return Response::json(array('data'=>$image_data),200);

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

			$rows = Proyecto::reporteEvaluacionProyectos($ejercicio)
							->where('idEstatusProyecto',5)
							->with([
								'componentes.metasMes' => function($componentesMetasMes) use ($mes){
									$componentesMetasMes->where('mes','<=',$mes);
								},
								'componentes.actividades.metasMes' => function($actividadesMetasMes) use ($mes){
									$actividadesMetasMes->where('mes','<=',$mes);
								},
								'analisisFuncional' => function($analisisFuncional) use ($mes){
									$analisisFuncional->where('mes',$mes);
								},
								'conteoPlanesMejora' => function($planesMejora) use ($mes){
									$planesMejora->where('mes','<=',$mes);
								}
							])
							->get();
			//
			$presupuestos_raw = CargaDatosEP01::reporteProyectosEP01($mes,$ejercicio)->get()->toArray();

			$presupuestos = [];
			foreach ($presupuestos_raw as $proyecto) {
				if(!isset($presupuestos[$proyecto['clavePresupuestaria']])){
					$presupuestos[$proyecto['clavePresupuestaria']] = [
						'presupuestoAprobado' => 0.00,
						'presupuestoDevengadoModificado' => 0.00,
						'presupuestoEjercidoModificado' => 0.00,
						'presupuestoModificado' => 0.00,
						'fuentesFinanciamiento' => [],
						'subFuentesFinanciamiento' => []
					];
				}

				$presupuestos[$proyecto['clavePresupuestaria']]['presupuestoAprobado'] += $proyecto['presupuestoAprobado'];
				$presupuestos[$proyecto['clavePresupuestaria']]['presupuestoDevengadoModificado'] += $proyecto['presupuestoDevengadoModificado'];
				$presupuestos[$proyecto['clavePresupuestaria']]['presupuestoEjercidoModificado'] += $proyecto['presupuestoEjercidoModificado'];
				$presupuestos[$proyecto['clavePresupuestaria']]['presupuestoModificado'] += $proyecto['presupuestoModificado'];
				$presupuestos[$proyecto['clavePresupuestaria']]['fuentesFinanciamiento'][$proyecto['claveFuenteFinanciamiento']] = $proyecto['fuenteFinanciamiento'];
				$presupuestos[$proyecto['clavePresupuestaria']]['subFuentesFinanciamiento'][$proyecto['claveSubFuenteFinanciamiento']] = $proyecto['subFuenteFinanciamiento'];
			}

			$catalogos['jurisdicciones'] = Jurisdiccion::get()->lists('nombre','clave');

			$datos = $rows->toArray();
			//return Response::json(array('datos'=>$datos),200);
			$this->obtenerWord($datos,$presupuestos,$catalogos);
		}catch(Exception $ex){
			return Response::json(array('data'=>'Ocurrio un error al generar el reporte.','message'=>$ex->getMessage(),'line'=>$ex->getLine()),500);
		}
	}

	function obtenerWord($datos,$presupuestos,$catalogos){
		try{
			$phpWord = new \PhpOffice\PhpWord\PhpWord();

			$phpWord->setDefaultFontName('Arial');
			$phpWord->setDefaultFontSize(10);
			$phpWord->setDefaultParagraphStyle(
			    array(
			        'align'      => 'both',
			        'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0),
			        'spacing'    => 0,
			    )
			);
		/***                <<<<<<<<<<<<<<<<<<<<  Definición de Estilos   >>>>>>>>>>>>>>>>>>>                   ***/
			$titulo_pag = array('bold' => true,'size'=>20);
			$titulo = array('bold' => true);
			$titulo_tabla = array('bold' => true, 'size'=>7, 'color'=>'FFFFFF');
			$titulo_row_span =  array('vMerge'=>'restart', 'valign'=>'center', 'bgColor'=>'28A659');
			$titulo_row_continue =  array('vMerge'=>'continue', 'valign'=>'center', 'bgColor'=>'28A659');
			$titulo_col_span_4 =  array('gridSpan'=>4, 'valign'=>'center', 'bgColor'=>'28A659');
			$titulo_cell = array('valign'=>'center','bgColor'=>'068437');
			$cell_middle_1 = array('valign'=>'center');
			$cell_middle_2 = array('valign'=>'center','bgColor'=>'DDDDDD');

			$texto = array('bold' => false);
			$texto_tabla = array('bold' => false, 'size'=>7);
			$centrado = array('align' => 'center');
			$justificado = array('align' => 'justify');
			$derecha = array('align' => 'right');
			$izquierda = array('align' => 'left');

			$infoStyle = array('borderColor'=>'000000', 'borderSize'=>6);
			$headerStyle = array('borderColor'=>'FFFFFF','borderSize'=>0);

			$phpWord->addTableStyle('TablaInfo', $infoStyle);
			$phpWord->addTableStyle('TablaEncabezado',$headerStyle);
			$phpWord->addTitleStyle(3, ['bold'=>true], ['align'=>'center']);
		/***                <<<<<<<<<<<<<<<<<<<<  Definición de Estilos   >>>>>>>>>>>>>>>>>>>                   ***/
		    
		    $section = $phpWord->addSection(array('orientation'=>'portrait','size'=>'letter'));

		    $sectionStyle = $section->getStyle();
			$sectionStyle->setMarginLeft(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(0.75));
			$sectionStyle->setMarginRight(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(0.5));
			$sectionStyle->setMarginTop(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(0.1));
			$sectionStyle->setMarginBottom(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(0.5));

		/***                <<<<<<<<<<<<<<<<<<<<  Encabezado   >>>>>>>>>>>>>>>>>>>                   ***/
			$header = $section->addHeader();
			$table = $header->addTable('TablaEncabezado');
			$row = $table->addRow();
			$row->addCell(3000)->addImage('img/LogoFederal.png',array('width' => 90,'height' => 30,'align'=>'left'));
			$cell = $row->addCell(5128)->addImage('img/EscudoGobiernoChiapas.png',array('width' => 100,'height' => 30,'align'=>'center'));
			$row->addCell(3000)->addImage('img/LogoInstitucional.png',array('width' => 80,'height' => 30,'align'=>'right'));
		/***                <<<<<<<<<<<<<<<<<<<<  Encabezado   >>>>>>>>>>>>>>>>>>>                   ***/

		/***                <<<<<<<<<<<<<<<<<<<<  Pie de Página   >>>>>>>>>>>>>>>>>>>                   ***/
			$footer = $section->addFooter();
			$footer->addPreserveText(htmlspecialchars('{PAGE}'), null,array('align' => 'center'));
		/***                <<<<<<<<<<<<<<<<<<<<  Pie de Página   >>>>>>>>>>>>>>>>>>>                   ***/

			$lista_imagenes = [];
			foreach ($datos as $index => $proyecto) {
				$section->addTitle(htmlspecialchars('4.' . ($index+1) . ' ' . $proyecto['nombreTecnico']),3);
				$section->addText(htmlspecialchars('('.$proyecto['ClavePresupuestaria'].')'),$titulo,$centrado);

				$section->addTextBreak(1);
			/***                <<<<<<<<<<<<<<<<<<<<  Información General  >>>>>>>>>>>>>>>>>>>                   ***/
				$textrun = $section->addTextRun();
				$textrun->addText('Área líder del proyecto: ', array('bold' => true));
				$textrun->addText($proyecto['unidadResponsableDescripcion']);
				//$section->addTextBreak();

				$textrun = $section->addTextRun();
				$textrun->addText('Tipo de proyecto estratégico: ', array('bold' => true));
				$textrun->addText(($proyecto['idClasificacionProyecto'])?'Institucional':'Inversión');
				//$section->addTextBreak();

				$textrun = $section->addTextRun();
				$textrun->addText('Tipo de proyecto: ', array('bold' => true));
				$textrun->addText($proyecto['tipoProyectoDescripcion']);
				//$section->addTextBreak();

				$textrun = $section->addTextRun();
				$textrun->addText('Cobertura: ', array('bold' => true));
				$textrun->addText($proyecto['coberturaDescripcion']);
				//$section->addTextBreak();

				$textrun = $section->addTextRun();
				$textrun->addText('Afectación presupuestaria: ', array('bold' => true));
				$textrun->addText('');
				//$section->addTextBreak();

				if(isset($presupuestos[$proyecto['ClavePresupuestaria']])){
					$textrun = $section->addTextRun();
					$textrun->addText('Fuente de financiamiento: ', array('bold' => true));
					$textrun->addText(implode(', ', $presupuestos[$proyecto['ClavePresupuestaria']]['fuentesFinanciamiento']));
					//$section->addTextBreak();

					$textrun = $section->addTextRun();
					$textrun->addText('Subfuente de financiamiento: ', array('bold' => true));
					$textrun->addText(implode(', ', $presupuestos[$proyecto['ClavePresupuestaria']]['subFuentesFinanciamiento']));
					//$section->addTextBreak();

					$textrun = $section->addTextRun();
					$textrun->addText('Presupuesto autorizado: ', array('bold' => true));
					$textrun->addText('$ ' . number_format($presupuestos[$proyecto['ClavePresupuestaria']]['presupuestoAprobado'],2));
					$section->addTextBreak(1);
				}else{
					$textrun = $section->addTextRun();
					$textrun->addText('Fuente de financiamiento: ', array('bold' => true));
					$textrun->addText('Información no encontrada en el EP01',array('color'=>'#FF0000','bold'=>true));
					//$section->addTextBreak();

					$textrun = $section->addTextRun();
					$textrun->addText('Subfuente de financiamiento: ', array('bold' => true));
					$textrun->addText('Información no encontrada en el EP01',array('color'=>'#FF0000','bold'=>true));
					//$section->addTextBreak();

					$textrun = $section->addTextRun();
					$textrun->addText('Presupuesto autorizado: ', array('bold' => true));
					$textrun->addText('Información no encontrada en el EP01',array('color'=>'#FF0000','bold'=>true));
					$section->addTextBreak(1);
				}
			/***                <<<<<<<<<<<<<<<<<<<<  Información General  >>>>>>>>>>>>>>>>>>>                   ***/

				$section->addText(htmlspecialchars('RESULTADO DE INDICADORES'),$titulo);
				$section->addTextBreak(1);

				$table = $section->addTable('TablaInfo');

				$row = $table->addRow();
				$row->addCell(1251,$titulo_row_span)->addText('Nivel',$titulo_tabla,$centrado);
				$row->addCell(1552,$titulo_row_span)->addText('Descripción',$titulo_tabla,$centrado);
				$row->addCell(1251,$titulo_row_span)->addText('Cantidad Programada',$titulo_tabla,$centrado);
				$row->addCell(5004,$titulo_col_span_4)->addText('Avances trimestrales',$titulo_tabla,$centrado);
				$row->addCell(1251,$titulo_row_span)->addText('Cantidad alcanzada',$titulo_tabla,$centrado);
				$row->addCell(950,$titulo_row_span)->addText('Porcentaje alcanzado',$titulo_tabla,$centrado);

				$row = $table->addRow();
				$row->addCell(1251,$titulo_row_continue);
				$row->addCell(1552,$titulo_row_continue);
				$row->addCell(1251,$titulo_row_continue);
				$row->addCell(1251,$titulo_cell)->addText('1',$titulo_tabla,$centrado);
				$row->addCell(1251,$titulo_cell)->addText('2',$titulo_tabla,$centrado);
				$row->addCell(1251,$titulo_cell)->addText('3',$titulo_tabla,$centrado);
				$row->addCell(1251,$titulo_cell)->addText('4',$titulo_tabla,$centrado);
				$row->addCell(1251,$titulo_row_continue);
				$row->addCell(950,$titulo_row_continue);

				$con_grafica = false;
				$proyecto_jurisdicciones = [];
				
				$row_class = null;
				$row_counter = 0;
				foreach ($proyecto['componentes'] as $componente) {
					$row_counter++;
					if(($row_counter%2) == 0){
						$row_class = $cell_middle_2;
					}else{
						$row_class = $cell_middle_1;
					}
					$trimestres = [1 => 0.00 , 2 => 0.00 , 3 => 0.00 , 4 => 0.00];
					$total_avance = 0.00;
					foreach ($componente['metas_mes'] as $meta) {
						$trimestre = ceil($meta['mes'] / 3);
						$trimestres[$trimestre] += $meta['avance'];
						$total_avance += $meta['avance'];
						if($meta['claveJurisdiccion'] == 'OC'){ $meta['claveJurisdiccion'] = '01'; }
						if($meta['avance'] > 0 || $meta['meta'] > 0){
							if(!isset($proyecto_jurisdicciones[$meta['claveJurisdiccion']])){
								$proyecto_jurisdicciones[$meta['claveJurisdiccion']] = [
									'jurisdiccion' 	=> $meta['claveJurisdiccion'] . ' ' . $catalogos['jurisdicciones'][$meta['claveJurisdiccion']],
									'programado' 	=> 0.00,
									'avance' 		=> 0.00
								];
							}
							$proyecto_jurisdicciones[$meta['claveJurisdiccion']]['programado'] += $meta['meta'];
							$proyecto_jurisdicciones[$meta['claveJurisdiccion']]['avance'] += $meta['avance'];	
						}
					}

					$row = $table->addRow();
					$row->addCell(1251,$titulo_cell)->addText('COMPONENTE',$titulo_tabla,$centrado);
					$row->addCell(1552,$row_class)->addText($componente['indicador'],$texto_tabla,$izquierda);
					$row->addCell(1251,$row_class)->addText(number_format($componente['valorNumerador'],2),$texto_tabla,$centrado);
					$row->addCell(1251,$row_class)->addText(number_format($trimestres[1],2),$texto_tabla,$centrado);
					$row->addCell(1251,$row_class)->addText(number_format($trimestres[2],2),$texto_tabla,$centrado);
					$row->addCell(1251,$row_class)->addText(number_format($trimestres[3],2),$texto_tabla,$centrado);
					$row->addCell(1251,$row_class)->addText(number_format($trimestres[4],2),$texto_tabla,$centrado);
					$row->addCell(1251,$row_class)->addText(number_format($total_avance,2),$texto_tabla,$centrado);
					$row->addCell(950,$row_class)->addText(number_format(round(($total_avance*100)/$componente['valorNumerador'],2),2).'%',$texto_tabla,$centrado);

					foreach ($componente['actividades'] as $actividad) {
						$row_counter++;
						if(($row_counter%2) == 0){
							$row_class = $cell_middle_2;
						}else{
							$row_class = $cell_middle_1;
						}

						$trimestres = [1 => 0.00 , 2 => 0.00 , 3 => 0.00 , 4 => 0.00];
						$total_avance = 0.00;
						foreach ($actividad['metas_mes'] as $meta) {
							$trimestre = ceil($meta['mes'] / 3);
							$trimestres[$trimestre] += $meta['avance'];
							$total_avance += $meta['avance'];
							if($meta['claveJurisdiccion'] == 'OC'){ $meta['claveJurisdiccion'] = '01'; }
							if($meta['avance'] > 0 || $meta['meta'] > 0){
								if(!isset($proyecto_jurisdicciones[$meta['claveJurisdiccion']])){
									$proyecto_jurisdicciones[$meta['claveJurisdiccion']] = [
										'jurisdiccion' 	=> $meta['claveJurisdiccion'] . ' ' . $catalogos['jurisdicciones'][$meta['claveJurisdiccion']],
										'programado' 	=> 0.00,
										'avance' 		=> 0.00
									];
								}
								$proyecto_jurisdicciones[$meta['claveJurisdiccion']]['programado'] += $meta['meta'];
								$proyecto_jurisdicciones[$meta['claveJurisdiccion']]['avance'] += $meta['avance'];
							}
						}

						$row = $table->addRow();
						$row->addCell(1251,$titulo_cell)->addText('ACTIVIDAD',$titulo_tabla,$centrado);
						$row->addCell(1552,$row_class)->addText($actividad['indicador'],$texto_tabla,$izquierda);
						$row->addCell(1251,$row_class)->addText(number_format($actividad['valorNumerador'],2),$texto_tabla,$centrado);
						$row->addCell(1251,$row_class)->addText(number_format($trimestres[1],2),$texto_tabla,$centrado);
						$row->addCell(1251,$row_class)->addText(number_format($trimestres[2],2),$texto_tabla,$centrado);
						$row->addCell(1251,$row_class)->addText(number_format($trimestres[3],2),$texto_tabla,$centrado);
						$row->addCell(1251,$row_class)->addText(number_format($trimestres[4],2),$texto_tabla,$centrado);
						$row->addCell(1251,$row_class)->addText(number_format($total_avance,2),$texto_tabla,$centrado);
						$row->addCell(950,$row_class)->addText(number_format(round(($total_avance*100)/$actividad['valorNumerador'],2),2).'%',$texto_tabla,$centrado);
					}
				}

				$section->addTextBreak(2);
				$section->addText(htmlspecialchars('AVANCE JURISDICCIONAL'),$titulo);
				$section->addTextBreak(1);

				$avance_comparar = false;
				$suma_porcentajes = 0.00;
				$jurisdicciones = [];
				$jurisdicciones_porcentajes = [];

				foreach ($proyecto_jurisdicciones as $indice => $jurisdiccion) {
					if($jurisdiccion['programado'] == 0){
						$porcentaje_avance = round(($jurisdiccion['avance']*100),2);
					}else{
						$porcentaje_avance = round(($jurisdiccion['avance']*100)/$jurisdiccion['programado'],2);
					}
					$suma_porcentajes += $porcentaje_avance;
					if($avance_comparar === false){
						$avance_comparar = $porcentaje_avance;
					}else if($avance_comparar != $porcentaje_avance){
						$con_grafica = true;
					}
					$jurisdicciones[$indice] = $jurisdiccion['jurisdiccion'];
					$jurisdicciones_porcentajes[$indice] = $porcentaje_avance ;
				}

				if(count($proyecto_jurisdicciones) == 10 && $con_grafica){
					$section->addText(htmlspecialchars('El proyecto se implementó en las 10 Jurisdicciones Sanitarias del Estado, observándose los siguientes resultados:'),$texto);
				}else if(count($proyecto_jurisdicciones) == 10 && !$con_grafica && $avance_comparar == 100.00){
					$section->addText(htmlspecialchars('El proyecto se implementó en todas Jurisdicciones Sanitarias del Estado, observándose el cumplimiento de las metas programadas.'),$texto);
				}else if(count($proyecto_jurisdicciones) == 10 && !$con_grafica && $avance_comparar != 100.00){
					$section->addText(htmlspecialchars('El proyecto se implementó en todas Jurisdicciones Sanitarias del Estado, observándose un avance del '.$avance_comparar.'% en relación a las metas programadas.'),$texto);
				}else if(count($proyecto_jurisdicciones) < 10 && count($proyecto_jurisdicciones) > 1 && $con_grafica){
					$section->addText(htmlspecialchars('Las Jurisdicciones Sanitarias en las que se implementó el proyecto fueron: '.implode(', ',$jurisdicciones).'; observándose los siguientes resultados:'),$texto);
				}else if(count($proyecto_jurisdicciones) < 10 && count($proyecto_jurisdicciones) > 1 && !$con_grafica && $avance_comparar == 100.00){
					$section->addText(htmlspecialchars('El proyecto se implementó en las Jurisdicciones Sanitarias: '.implode(', ',$jurisdicciones).'; observándose el cumplimiento de las metas programadas.'),$texto);
				}else if(count($proyecto_jurisdicciones) < 10 && count($proyecto_jurisdicciones) > 1 && !$con_grafica && $avance_comparar != 100.00){
					$section->addText(htmlspecialchars('El proyecto se implementó en las Jurisdicciones Sanitarias: '.implode(', ',$jurisdicciones).'; observándose un avance del '.$avance_comparar.'% en relación a las metas programadas.'),$texto);
				}else if(count($proyecto_jurisdicciones) == 1 && $avance_comparar == 100.00){
					$section->addText(htmlspecialchars('El proyecto se implementó en la Jurisdicción Sanitaria '.implode(', ',$jurisdicciones).', observándose el cumplimiento de las metas programadas.'),$texto);
					$con_grafica = false;
				}else{
					$section->addText(htmlspecialchars('El proyecto se implementó en la Jurisdicción Sanitaria '.implode(', ',$jurisdicciones).', observándose un avance del '.$avance_comparar.'% en relación a las metas programadas.'),$texto);
					$con_grafica = false;
				}

				//0
				$section->addTextBreak();

				if($con_grafica){

					$graph = new \PHPGraphLib(650,200);
					
					$graph->addData($jurisdicciones_porcentajes);
					$graph->setBars(false);
					$graph->setLine(true);
					$graph->setGrid(false);
					$graph->setDataPoints(true);
					$graph->setDataPointColor('navy');
					$graph->setDataValues(true);
					$graph->setDataValueColor('navy');
					$graph->setGoalLine(110,'red');
					$graph->setGoalLine(100,'green');
					$graph->setGoalLine(90,'red');
					$graph->setXValuesHorizontal(true);

					ob_start();
						$graph->createGraph();
						$image_data = ob_get_contents();
					ob_end_clean();

					$chart_file_path = storage_path().'/archivoscsv/chart-avance-jurisdiccion-'.$proyecto['id'].'.png';

					file_put_contents($chart_file_path, $image_data);

					$lista_imagenes[] = $chart_file_path;
					
					$section->addText(htmlspecialchars('AVANCE JURISDICCIONAL (%)'),$titulo,$centrado);

					$table = $section->addTable();
					$row = $table->addRow();
					$row->addCell(11259)->addImage($chart_file_path,array('align'=>'center'));
				    //$section->addTextBreak(1);
				}
				$section->addTextBreak(1);
				$section->addText(htmlspecialchars('AVANCE FÍSICO-FINANCIERO'),$titulo);
				$section->addTextBreak(1);

				$promedio_avance = round($suma_porcentajes/count($jurisdicciones_porcentajes),2);

				$textrun = $section->addTextRun();
				$textrun->addText('Promedio de avance físico alcanzado: ', array('bold' => true));
				$textrun->addText($promedio_avance . ' %');
				//$section->addTextBreak();
				$avance_logrado = 0;

				if(isset($presupuestos[$proyecto['ClavePresupuestaria']])){
					$textrun = $section->addTextRun();
					$textrun->addText('Presupuesto ejercido: ', array('bold' => true));
					$textrun->addText('$ ' . number_format($presupuestos[$proyecto['ClavePresupuestaria']]['presupuestoEjercidoModificado'],2));

					if($presupuestos[$proyecto['ClavePresupuestaria']]['presupuestoModificado']>0){
						$avance_logrado = round(($presupuestos[$proyecto['ClavePresupuestaria']]['presupuestoEjercidoModificado']*100)/$presupuestos[$proyecto['ClavePresupuestaria']]['presupuestoModificado'],2);
					}else{
						$avance_logrado = 0;
					}
					$textrun = $section->addTextRun();
					$textrun->addText('Avance financiero logrado: ', array('bold' => true));
					$textrun->addText($avance_logrado . ' %');
				}else{
					$textrun = $section->addTextRun();
					$textrun->addText('Presupuesto ejercido: ', array('bold' => true));
					$textrun->addText('Información no encontrada en el EP01',array('color'=>'#FF0000','bold'=>true));

					$textrun = $section->addTextRun();
					$textrun->addText('Avance financiero logrado: ', array('bold' => true));
					$textrun->addText('Información no encontrada en el EP01',array('color'=>'#FF0000','bold'=>true));
				}
				
				$section->addTextBreak();

				$graph = new \PHPGraphLib(650,150);
				$data = array("Avance fisico" => $promedio_avance, "Avance financiero" => $avance_logrado);
				$graph->addData($data);
				$graph->setBarColor('navy');
				$graph->setGrid(false);
				$graph->setDataValues(true);
				$graph->setDataValueColor('navy');
				$graph->setDataFormat('percent');
				$graph->setXValuesHorizontal(true);

				ob_start();
					$graph->createGraph();
					$image_data = ob_get_contents();
				ob_end_clean();

				$chart_file_path = storage_path().'/archivoscsv/chart-fisico-finan-'.$proyecto['id'].'.png';

				file_put_contents($chart_file_path, $image_data);

				$lista_imagenes[] = $chart_file_path;
				
				$section->addText(htmlspecialchars('AVANCE FÍSICO - FINANCIERO'),$titulo,$centrado);

				$table = $section->addTable();
				$row = $table->addRow();
				$row->addCell(11259)->addImage($chart_file_path,array('align'=>'center'));
				
				$section->addTextBreak(1);
				
				$section->addText(htmlspecialchars('OBSERVACIONES:'),$titulo);
				if(isset($proyecto['analisis_funcional'][0])){
					$section->addText(htmlspecialchars($proyecto['analisis_funcional'][0]['justificacionGlobal']));
				}else{
					$textrun->addText('Información no encontrada en la base de datos',array('color'=>'#FF0000','bold'=>true));
				}
				

				$section->addTextBreak(1);
				$total_meses_planes = count($proyecto['conteo_planes_mejora']);
				if($total_meses_planes > 0){
					$trimestres = '';
					foreach ($proyecto['conteo_planes_mejora'] as $index => $plan_mejora) {
						$trimestre = ceil($plan_mejora['mes'] / 3);
						switch ($trimestre) {
							case 1: $trimestre = 'primer'; break;
							case 2: $trimestre = 'segundo'; break;
							case 3: $trimestre = 'tercer'; break;
							case 4: $trimestre = 'cuarto'; break;
							default: $trimestre = 'error'; break;
						}
						if($index > 0 && ($index+1) < $total_meses_planes){
							$trimestres .= ', ' . $trimestre;
						}else if($total_meses_planes > 1 && ($index+1) == $total_meses_planes){
							$trimestres .= ' y ' . $trimestre;
						}else{
							$trimestres .= $trimestre;
						}
					}
					$section->addText(htmlspecialchars('Para lograr los resultados obtenidos, en este proyecto se implementó un Plan de Acción de Mejora, durante el '.$trimestres . ' trimestre.'));
				}
				
				$section->addPageBreak();
			}
			
			header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
		    header("Content-Description: File Transfer");
			header('Content-Disposition: attachment; filename="EvaluacionProyectos.docx"');
		    
		    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord,'Word2007');
		    $objWriter->save('php://output');

		    foreach ($lista_imagenes as $imagen) { unlink($imagen); }
		}catch(Exception $ex){
			if(isset($lista_imagenes)){
				foreach ($lista_imagenes as $imagen) { unlink($imagen); }
			}
			var_dump('expression');
			return Response::json(array('data'=>'Ocurrio un error al generar el reporte.','message'=>$ex->getMessage(),'line'=>$ex->getLine()),500);
		}
	}
}
?>