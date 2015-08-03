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
use Excel, EvaluacionAnalisisFuncional, SysConfiguracionVariable, Proyecto;

class ReporteCuentaPublicaController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
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

		$trimestres = array(1=>'PRIMER',2=>'SEGUNDO',3=>'TERCER',4=>'CUARTO');
		$trimestre = Util::obtenerTrimestre();

		$titulo = array('bold' => true);
		$titulo_tabla = array('bold' => true, 'size'=>10);
		$texto = array('bold' => false);
		$centrado = array('align' => 'center');
		$justificado = array('align' => 'justify');

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
	    $sectionStyle = $section->getStyle();
		$sectionStyle->setMarginLeft(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(3));
		$sectionStyle->setMarginRight(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(2.19));
		$sectionStyle->setMarginTop(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(4.33));
		$sectionStyle->setMarginBottom(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(3));


	    $variables = SysConfiguracionVariable::obtenerVariables(array('clave-institucional','mision','vision'))->lists('valor','variable');

		$header = $section->addHeader();
		
		$table = $header->addTable('TablaEncabezado');
		$row = $table->addRow();
		$row->addCell(3000)->addImage('img/EscudoGobiernoChiapas.png');
		$cell = $row->addCell(8128);
		$cell->addText(htmlspecialchars('GOBIERNO CONSTITUCIONAL DEL ESTADO DE CHIAPAS'),$titulo,$centrado);
		$cell->addTextBreak(0);
		$cell->addText(htmlspecialchars('SECRETARÍA DE SALUD'),$titulo,$centrado);
		$cell->addTextBreak(0);
		$cell->addText(htmlspecialchars('ANÁLISIS FUNCIONAL AL '.$trimestres[$trimestre].' TRIMESTRE DEL '.date('Y')),$titulo,$centrado);
		$row->addCell(3000)->addImage('img/LogoInstitucional.png');
		
		$table = $header->addTable('TablaClave');
		$row = $table->addRow();
		$row->addCell(2250)->addText(htmlspecialchars($variables['clave-institucional']));

		$header->addTextBreak();

		$footer = $section->addFooter();
		$footer->addPreserveText(htmlspecialchars('Página {PAGE} de {NUMPAGES}.'), null,array('align' => 'right'));

		$section->addText(htmlspecialchars('ÍNDICE'),$titulo,$centrado);
		$section->addTextBreak(2);
		$section->addTOC();
		$section->addPageBreak();

		$section->addTextBreak(2);
		$section->addTitle(htmlspecialchars('MISIÓN'),1);
		$section->addTextBreak(1);
		$section->addText(htmlspecialchars($variables['mision']),$texto,$justificado);
		$section->addTextBreak(3);

		$section->addTitle(htmlspecialchars('VISIÓN'),1);
		$section->addTextBreak(1);
		$section->addText(htmlspecialchars($variables['vision']),$texto,$justificado);
		//$section->addPageBreak();

		$mes = Util::obtenerMesActual();
		if($mes == 0){
			$mes = date('n') - 1;
		}

		$rows = Proyecto::reporteCuentaPublica($mes,date('Y'))->get();
		//var_dump($rows->toArray());die;
		//$queries = DB::getQueryLog();
		//var_dump(end($queries));die;
		
		$subfuncion_anterior = '';
		$clasificacion_anterior = 0;
		$politica_programa_anterior = '';
		foreach ($rows as $elemento) {
			if($subfuncion_anterior != $elemento->subFuncionClave){
				$section->addPageBreak();
				$section->addTitle(htmlspecialchars('FUNCIÓN: ' . $elemento->funcionGasto),1);
				$section->addTextBreak();
				$section->addTitle(htmlspecialchars('SUBFUNCIÓN: ' . $elemento->subFuncionGasto),2);
				$section->addTextBreak();
				$subfuncion_anterior = $elemento->subFuncionClave;
				$clasificacion_anterior = 0;
				$politica_programa_anterior = '';
			}

			if($clasificacion_anterior != $elemento->idClasificacionProyecto){
				$section->addText(htmlspecialchars('OBJETIVOS Y PRINCIPALES COMENTARIOS DE LOS PROYECTOS INMERSOS EN ESTA SUBFUNCIÓN'),$titulo);
				$section->addTextBreak();
				if($elemento->idClasificacionProyecto == 1){
					$section->addTitle(htmlspecialchars('PROYECTOS INSTITUCIONALES:'),3);
				}else{
					$section->addTitle(htmlspecialchars('PROYECTOS DE INVERSIÓN:'),3);
				}
				$section->addTextBreak();
				$clasificacion_anterior = $elemento->idClasificacionProyecto;
				$politica_programa_anterior = '';
			}

			$politica_programa_actual = $elemento->politicaPublicaClave.'-'.$elemento->programaPresupuestario;

			if($politica_programa_anterior != $politica_programa_actual){
				$table = $section->addTable('TablaInfo');

				$row = $table->addRow();
				$row->addCell(3000)->addText('EJE',$titulo_tabla,$centrado);
				$row->addCell(3000)->addText('TEMA',$titulo_tabla,$centrado);
				$row->addCell(3065)->addText('POLÍTICA PÚBLICA',$titulo_tabla,$centrado);
				$row->addCell(5060)->addText('PROGRAMA PRESUPUESTARIO',$titulo_tabla,$centrado);

				$row = $table->addRow();
				$row->addCell(2500)->addText($elemento->ejeDescripcion);
				$row->addCell(2500)->addText($elemento->temaDescripcion);
				$row->addCell(4065)->addText($elemento->politicaPublicaDescripcion);
				$row->addCell(5060)->addText($elemento->programaPresupuestarioDescipcion);
				$section->addTextBreak();
				$politica_programa_anterior = $politica_programa_actual;
			}

			$section->addTitle(htmlspecialchars('Proyecto: '.rtrim($elemento->nombreTecnico,'.').'. ('.$elemento->unidadResponsableDescipcion.').'),4);
			$section->addTextBreak();

			if($elemento->totalMetas > 0){
				if($elemento->cuentaPublica){
					$section->addText(htmlspecialchars(trim($elemento->cuentaPublica)),$texto,$justificado);
				}else{
					$section->addText(htmlspecialchars('No presentaron avances.'),$texto,$justificado);
				}
			}else{
				if($elemento->cuentaPublica){
					$section->addText(htmlspecialchars(trim($elemento->cuentaPublica)),$texto,$justificado);
				}else{
					$section->addText(htmlspecialchars('No hay metas programadas para este trimestre.'),$texto,$justificado);
				}
			}
			$section->addTextBreak();
		}

		header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
	    header("Content-Description: File Transfer");
		header('Content-Disposition: attachment; filename="CuentaPublica.docx"');
	    
	    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
	    $objWriter->save('php://output');
	}
}