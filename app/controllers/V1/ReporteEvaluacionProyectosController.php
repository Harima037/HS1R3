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
use Excel, EvaluacionAnalisisFuncional, SysConfiguracionVariable, Proyecto, Jurisdiccion;

class ReporteEvaluacionProyectosController extends BaseController {

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

			$rows = Proyecto::reporteEvaluacionProyectos($mes,$ejercicio)
							->where('ejercicio',$ejercicio)
							->where('idEstatusProyecto',5)
							->with('componentes.metasMes','componentes.actividades.metasMes')
							->get();

			$catalogos['jurisdicciones'] = Jurisdiccion::get()->lists('nombre','clave');

			$datos = $rows->toArray();
			//return Response::json(array('datos'=>$datos,'catalogos'=>$catalogos),200);
			$this->obtenerWord($datos,$catalogos);
		}catch(Exception $ex){
			return Response::json(array('data'=>'Ocurrio un error al generar el reporte.','message'=>$ex->getMessage(),'line'=>$ex->getLine()),500);
		}
	}

	function obtenerWord($datos,$catalogos){
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

			$textrun = $section->addTextRun();
			$textrun->addText('Fuente de financiamiento: ', array('bold' => true));
			$textrun->addText('sacar del EP01');
			//$section->addTextBreak();

			$textrun = $section->addTextRun();
			$textrun->addText('Subfuente de financiamiento: ', array('bold' => true));
			$textrun->addText('sacar del EP01');
			//$section->addTextBreak();

			$textrun = $section->addTextRun();
			$textrun->addText('Presupuesto autorizado: ', array('bold' => true));
			$textrun->addText('sacar del EP01, numero y letra');
			$section->addTextBreak(1);
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
				$section->addText(htmlspecialchars('AVANCE JURISDICCIONAL (%)'),$titulo);
			    $chart = $section->addChart('line', array_keys($jurisdicciones), $jurisdicciones_porcentajes);
			    $chart->getStyle()->setWidth(\PhpOffice\PhpWord\Shared\Converter::inchToEmu(2.5))->setHeight(\PhpOffice\PhpWord\Shared\Converter::inchToEmu(2));
			    $section->addTextBreak();
			}
			
			$section->addTextBreak(2);
			$section->addText(htmlspecialchars('AVANCE FÍSICO-FINANCIERO'),$titulo);
			$section->addTextBreak(1);

			$textrun = $section->addTextRun();
			$textrun->addText('Promedio de avance físico alcanzado: ', array('bold' => true));
			$textrun->addText('calcular');
			//$section->addTextBreak();

			$textrun = $section->addTextRun();
			$textrun->addText('Presupuesto ejercido: ', array('bold' => true));
			$textrun->addText('sacar del EP01');
			//$section->addTextBreak();

			$textrun = $section->addTextRun();
			$textrun->addText('Avance financiero logrado: ', array('bold' => true));
			$textrun->addText('ejercido vs modificado');
			$section->addTextBreak(1);

			$section->addPageBreak();
		}

	/*
		$section->addTitle(htmlspecialchars('2D charts'), 1);
		$section = $phpWord->addSection(array('colsNum' => 2, 'breakType' => 'continuous'));
		$chartTypes = array('pie', 'doughnut', 'bar', 'column', 'line', 'area', 'scatter', 'radar');
		$twoSeries = array('bar', 'column', 'line', 'area', 'scatter', 'radar');
		$threeSeries = array('bar', 'line');
		$categories = array('A', 'B', 'C', 'D', 'E');
		$series1 = array(1, 3, 2, 5, 4);
		$series2 = array(3, 1, 7, 2, 6);
		$series3 = array(8, 3, 2, 5, 4);
		foreach ($chartTypes as $chartType) {
		    $section->addTitle(ucfirst($chartType), 2);
		    $chart = $section->addChart($chartType, $categories, $series1);
		    $chart->getStyle()->setWidth(\PhpOffice\PhpWord\Shared\Converter::inchToEmu(4.5))->setHeight(\PhpOffice\PhpWord\Shared\Converter::inchToEmu(4));
		    if (in_array($chartType, $twoSeries)) {
		        $chart->addSeries($categories, $series2);
		    }
		    if (in_array($chartType, $threeSeries)) {
		        $chart->addSeries($categories, $series3);
		    }
		    $section->addTextBreak();
		}
		// 3D charts
		$section = $phpWord->addSection(array('breakType' => 'continuous'));
		$section->addTitle(htmlspecialchars('3D charts'), 1);
		$section = $phpWord->addSection(array('colsNum' => 2, 'breakType' => 'continuous'));
		$chartTypes = array('pie', 'bar', 'column', 'line', 'area');
		$multiSeries = array('bar', 'column', 'line', 'area');
		$style = array('width' => \PhpOffice\PhpWord\Shared\Converter::cmToEmu(5), 'height' => \PhpOffice\PhpWord\Shared\Converter::cmToEmu(4), '3d' => true);
		foreach ($chartTypes as $chartType) {
		    $section->addTitle(ucfirst($chartType), 2);
		    $chart = $section->addChart($chartType, $categories, $series1, $style);
		    if (in_array($chartType, $multiSeries)) {
		        $chart->addSeries($categories, $series2);
		        $chart->addSeries($categories, $series3);
		    }
		    $section->addTextBreak();
		}
	*/

	/*
		$section->addTextBreak(10);
		$section->addText(htmlspecialchars('Cédulas de Avances Físico-financieros al '.$datos['trimestre'].' Trimestre del '.$datos['ejercicio']),array('bold' => true,'size'=>28),$centrado);

		
		foreach ($datos['datos'] as $proyecto) {
			$section->addPageBreak();

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
				$row->addCell(1025)->addText('C '.($indice+1) ,$texto,$centrado);
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

			$table = $section->addTable('TablaEncabezado');
			$row = $table->addRow();
			$row->addCell(2500,$headerStyle);
			$row->addCell(9900,$infoStyle)->addText('Beneficiarios',$titulo,$centrado);
			$row->addCell(2500,$headerStyle);

			$table = $section->addTable('TablaEncabezado');
			$row = $table->addRow();
			$row->addCell(2500,$headerStyle);
			$row->addCell(4000,$infoStyle)->addText('Tipo de Beneficiario',$titulo,$centrado);
			$row->addCell(2250,$infoStyle)->addText('Programado',$titulo,$centrado);
			$row->addCell(2250,$infoStyle)->addText('Atendido',$titulo,$centrado);
			$row->addCell(1400,$infoStyle)->addText('% Avance',$titulo,$centrado);
			$row->addCell(2500,$headerStyle);

			$total_programado = 0;
			$total_avance = 0;
			foreach($proyecto['beneficiarios_descripcion'] AS $beneficiario){
				$total_programado += $beneficiario['programadoTotal'];
				$total_avance += $beneficiario['avanceTotal'];
				$row = $table->addRow();
				$row->addCell(2500,$headerStyle);
				$row->addCell(4000,$infoStyle)->addText(htmlspecialchars($beneficiario['tipoBeneficiario']),$texto,$centrado);
				$row->addCell(2250,$infoStyle)->addText(number_format($beneficiario['programadoTotal']),$texto,$centrado);
				$row->addCell(2250,$infoStyle)->addText(number_format($beneficiario['avanceTotal']),$texto,$centrado);
				$row->addCell(1400,$infoStyle)->addText(number_format(($beneficiario['avanceTotal']/$beneficiario['programadoTotal'])*100,2),$texto,$centrado);
				$row->addCell(2500,$headerStyle);
			}

			$row = $table->addRow();
			$row->addCell(2500,$headerStyle);
			$row->addCell(4000,$infoStyle)->addText('Total',$titulo,$centrado);
			$row->addCell(2250,$infoStyle)->addText(number_format($total_programado),$titulo,$centrado);
			$row->addCell(2250,$infoStyle)->addText(number_format($total_avance),$titulo,$centrado);
			$row->addCell(1400,$infoStyle)->addText(number_format(($total_avance/$total_programado)*100,2),$titulo,$centrado);
			$row->addCell(2500,$headerStyle);
		}

	*/
		header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
	    header("Content-Description: File Transfer");
		header('Content-Disposition: attachment; filename="EvaluacionProyectos.docx"');
	    
	    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
	    $objWriter->save('php://output');
	}
}
?>