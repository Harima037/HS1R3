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
use Excel, EvaluacionAnalisisFuncional, SysConfiguracionVariable;

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

		$titulo = array('bold' => true);
		$texto = array('bold' => false);
		$centrado = array('align' => 'center');
		$justificado = array('align' => 'justify');

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
		$header->addText(htmlspecialchars('GOBIERNO CONSTITUCIONAL DEL ESTADO DE CHIAPAS'),$titulo,$centrado);
		$header->addTextBreak(0);
		$header->addText(htmlspecialchars('SECRETARÍA DE SALUD'),$titulo,$centrado);
		$header->addTextBreak();
		$header->addText(htmlspecialchars('ANÁLISIS FUNCIONAL AL '.$trimestres[$trimestre].' TRIMESTRE DEL '.date('Y')),$titulo,$centrado);
		
		$footer = $section->addFooter();
		$footer->addPreserveText(htmlspecialchars('Pagina {PAGE} de {NUMPAGES}.'), null,array('align' => 'right'));

		$section->addText(htmlspecialchars('Indice'),$titulo);
		$section->addPageBreak();

		$section->addTextBreak(2);
		$section->addText(htmlspecialchars('MISIÓN'),$titulo);
		$section->addTextBreak(1);
		$section->addText(htmlspecialchars($variables['mision']),$texto,$justificado);
		$section->addTextBreak(3);

		$section->addText(htmlspecialchars('VISIÓN'),$titulo);
		$section->addTextBreak(1);
		$section->addText(htmlspecialchars($variables['vision']),$texto,$justificado);
		$section->addPageBreak();

		$rows = EvaluacionAnalisisFuncional::cuentaPublica(Util::obtenerMesActual(),date('Y'));
		$rows = $rows->orderBy('id', 'desc')->get();
		
		foreach ($rows as $row) {
			if($row->cuentaPublica){
				$section->addTextBreak();
				$section->addText(htmlspecialchars($row->cuentaPublica),$texto,$justificado);
				$lineStyle = array('weight' => 1, 'width' => 100, 'height' => 0, 'color' => 635552);
				$section->addLine($lineStyle);
			}
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
		
		// Write some text
		$section->addTextBreak();
		$section->addText(htmlspecialchars('Some text...'));
		// Create a second page
		$section->addPageBreak();
		// Write some text
		$section->addTextBreak();
		$section->addText(htmlspecialchars('Some text...'));
		// Create a third page
		$section->addPageBreak();
		// Write some text
		$section->addTextBreak();
		$section->addText(htmlspecialchars('Some text...'));
		// New portrait section
		$section2 = $phpWord->addSection();
		$sec2Header = $section2->addHeader();
		$sec2Header->addText(htmlspecialchars('All pages in Section 2 will Have this!'));
		// Write some text
		$section2->addTextBreak();
		$section2->addText(htmlspecialchars('Some text...'));


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

	    header("Content-Description: File Transfer");
		header('Content-Disposition: attachment; filename="archivo.docx"');
	    // Finally, write the document:
	        // The files will be in your public folder
	    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
	    $objWriter->save('php://output');
	}
}