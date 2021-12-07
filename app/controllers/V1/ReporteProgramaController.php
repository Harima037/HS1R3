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
*	@author 			Donaldo Ríos, Mario Alberto Cabrera Alfaro
*	@package 			poa
*	@version 			1.0
*	@comment 			
*/

namespace V1;

use SSA\Utilerias\Util;
use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, View, DateTime;
use Excel,PDF, Programa;

class ReporteProgramaController extends BaseController {

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id){
        try{
            $programa = Programa::with('arbolProblemas','arbolObjetivos','indicadoresDescripcion')->contenidoDetalle()->find($id);
            //return Response::json($programa,500);
            $nombreArchivo = 'Carátula Programa Presupuestario';
            $nombreArchivo.=' - '.$programa->claveProgramaPresupuestario;

            $data = array('data' => ['programaPresupuestarioAsignado' => $programa]);

            //return Response::json($data,500);

            $pdf = PDF::loadView('expediente.pdf.programa-presupuestario',$data);

            $pdf->setPaper('LEGAL')->setOrientation('landscape')->setWarnings(false);

            $font = \Font_Metrics::get_font("helvetica", "bold");

            $pdf->output();
            $dom_pdf = $pdf->getDomPDF();
            $canvas = $dom_pdf->get_canvas();

            $width = $canvas->get_width();
            $heigth = $canvas->get_height();
            $color = array(0.565, 0.565, 0.565);
            $date = new DateTime();
            $timestamp = $date->getTimestamp();

            $canvas->page_text(10, $heigth-20, "CARATULA DEL PROGRAMA: ".$programa->programaPresupuestarioDescripcion, $font, 8, array(0, 0, 0));
            $canvas->page_text($width-75, $heigth-20, "Página {PAGE_NUM} de {PAGE_COUNT}", $font, 8, array(0, 0, 0));
            $canvas->page_text(($width/2)-50,$heigth-20,"SSA".$timestamp, $font, 8, $color);

    		return $pdf->stream($nombreArchivo.'.pdf');
            //return $pdf->stream($nombreArchivo.'.pdf');
        }catch(\Exception $e){
            return Response::json($e,500);
        }
    }
}