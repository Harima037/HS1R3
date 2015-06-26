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

		//$paragraphStyle = array('align' => 'center');
		//$phpWord->addParagraphStyle('centrado', $paragraphStyle);
		$trimestres = array(1=>'PRIMER',2=>'SEGUNDO',3=>'TERCER',4=>'CUARTO');
		$trimestre = Util::obtenerTrimestre();

		$fontStyle = array('spaceAfter' => 60, 'size' => 12);

		$titulo = array('bold' => true);
		$texto = array('bold' => false);
		$centrado = array('align' => 'center');
		$justificado = array('align' => 'justify');

		$tableStyle = array('borderColor'=>'000000', 'borderSize'=>6);
		$phpWord->addTableStyle('TablaInfo', $tableStyle,$tableStyle);

		$phpWord->addTitleStyle(1, $titulo, $justificado);
		$phpWord->addTitleStyle(2, $titulo, $justificado);
		$phpWord->addTitleStyle(3, $titulo, $justificado);
		$phpWord->addTitleStyle(4, $titulo, $justificado);
	    // Every element you want to append to the word document is placed in a section.
	    // To create a basic section:
	    $section = $phpWord->addSection(array('orientation'=>'landscape','size'=>'letter'));

		// Add first page header
		
		//$header->firstPage();
		//$table = $header->addTable(array('width'=>100));
		//$table->addRow();

		//$cell = $table->addCell();
		//$textrun = $cell->addTextRun();
		//$textrun->addText(htmlspecialchars('LOGO1'));

		//$cell = $table->addCell();
		//$textrun = $cell->addTextRun();
	    $variables = SysConfiguracionVariable::obtenerVariables(array('clave-institucional','mision','vision'))->lists('valor','variable');

		$header = $section->addHeader();

		//$textrun = $header->addTextRun();
		//$textrun->addImage('img/LogoFederal.png', array('width' => 80, 'height' => 80, 'wrappingStyle' => 'square','positioning' => 'absolute','posHorizontalRel' => 'margin','posVerticalRel' => 'line'));
		//$header->addWatermark('img/LogoFederal.png', array('wrappingStyle' => 'square','positioning' => 'absolute','posHorizontalRel' => 'margin','posVerticalRel' => 'line'));

		//$textrun = $header->addTextRun();
		//$header->addImage('img/LogoInstitucional.png',array('wrappingStyle' => 'square','positioning' => 'absolute'));
		//$header->addWatermark(asset('img/LogoFederal.png'));
		//$header->addWatermark('img/LogoFederal.png');
		
		$header->addText(htmlspecialchars('GOBIERNO CONSTITUCIONAL DEL ESTADO DE CHIAPAS'),$titulo,$centrado);
		$header->addTextBreak(0);
		$header->addText(htmlspecialchars('SECRETARÍA DE SALUD'),$titulo,$centrado);
		$header->addTextBreak();
		$header->addText(htmlspecialchars('ANÁLISIS FUNCIONAL AL '.$trimestres[$trimestre].' TRIMESTRE DEL '.date('Y')),$titulo,$centrado);
		$header->addTextBreak();


		$footer = $section->addFooter();
		$footer->addPreserveText(htmlspecialchars('Pagina {PAGE} de {NUMPAGES}.'), null,array('align' => 'right'));

		$section->addText(htmlspecialchars('ÍNDICE'),$titulo,$centrado);
		$section->addTextBreak(2);
		$section->addTOC($fontStyle);
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

		$rows = Proyecto::reporteCuentaPublica(Util::obtenerMesActual(),date('Y'))->get();
		//var_dump($rows->toArray());die;
		//$queries = DB::getQueryLog();
		//var_dump(end($queries));die;
		
		$subfuncion_anterior = '';
		$clasificacion_anterior = 0;
		foreach ($rows as $elemento) {
			if($subfuncion_anterior != $elemento->subFuncionClave){
				$section->addPageBreak();
				$section->addTitle(htmlspecialchars('FUNCIÓN: ' . $elemento->funcionGasto),1);
				$section->addTextBreak();
				$section->addTitle(htmlspecialchars('SUBFUNCIÓN: ' . $elemento->subFuncionGasto),2);
				$section->addTextBreak();
				$subfuncion_anterior = $elemento->subFuncionClave;
				$clasificacion_anterior = 0;
			}

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

			$section->addTitle(htmlspecialchars('Proyecto: '.$elemento->nombreTecnico),4);
			$section->addTextBreak();

			$table = $section->addTable('TablaInfo');

			$row = $table->addRow();
			$row->addCell(2000)->addText('EJE');
			$row->addCell(2000)->addText('TEMA');
			$row->addCell(5065)->addText('POLÍTICA PÚBLICA');
			$row->addCell(5060)->addText('PROGRAMA PRESUPUESTARIO');

			$row = $table->addRow();
			$row->addCell(2000)->addText($elemento->ejeDescripcion);
			$row->addCell(2000)->addText($elemento->temaDescripcion);
			$row->addCell(5065)->addText($elemento->politicaPublicaDescripcion);
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
		}
		
		$section->addPageBreak();

		//$cell = $table->addCell();
		//$textrun = $cell->addTextRun();
		//$textrun->addText(htmlspecialchars('LOGO2'));
		/*$table->addCell(4500)->addImage(
		    'resources/PhpWord.png',
		    array('width' => 80, 'height' => 80, 'align' => 'right')
		);*/
		// Add header for all other pages
		//$subsequent = $section->addHeader();
		//$subsequent->addText(htmlspecialchars('Subsequent pages in Section 1 will Have this!'));
		//$subsequent->addImage('resources/_mars.jpg', array('width' => 80, 'height' => 80));
		// Add footer
		
		/*
		$section2 = $phpWord->addSection();
		$sec2Header = $section2->addHeader();
		$sec2Header->addText(htmlspecialchars('All pages in Section 2 will Have this!'));
		// Write some text
		$section2->addTextBreak();
		$section2->addText(htmlspecialchars('Some text...'));
		*/

	    /*
	    // After creating a section, you can append elements:
	    $section->addText('Hello world!');

	    // You can directly style your text by giving the addText function an array:
	    $section->addText('Hello world! I am formatted.',
	        array('name'=>'Tahoma', 'size'=>16, 'bold'=>true));

	    // If you often need the same style again you can create a user defined style
	    // to the word document and give the addText function the name of the style:
	    $phpWord->addFontStyle('myOwnStyle',
	        array('name'=>'Verdana', 'size'=>14, 'color'=>'1B2232'));
	    $section->addText('Hello world! I am formatted by a user defined style',
	        'myOwnStyle');

	    // You can also put the appended element to local object like this:
	    $fontStyle = new \PhpOffice\PhpWord\Style\Font();
	    $fontStyle->setBold(true);
	    $fontStyle->setName('Verdana');
	    $fontStyle->setSize(22);
	    $myTextElement = $section->addText('Hello World!');
	    $myTextElement->setFontStyle($fontStyle);*/
		header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
	    header("Content-Description: File Transfer");
		header('Content-Disposition: attachment; filename="CuentaPublica.docx"');
	    // Finally, write the document:
	        // The files will be in your public folder
	    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
	    $objWriter->save('php://output');
	}
}