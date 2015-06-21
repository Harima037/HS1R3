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
use Excel, EvaluacionAnalisisFuncional; 

class ReporteCuentaPublicaController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		$phpWord = new \PhpOffice\PhpWord\PhpWord();

	    // Every element you want to append to the word document is placed in a section.
	    // To create a basic section:
	    $section = $phpWord->addSection();
	    
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
	    $myTextElement->setFontStyle($fontStyle);

	    header("Content-Description: File Transfer");
		header('Content-Disposition: attachment; filename="archivo.docx"');
	    // Finally, write the document:
	        // The files will be in your public folder
	    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
	    $objWriter->save('php://output');
	}
}