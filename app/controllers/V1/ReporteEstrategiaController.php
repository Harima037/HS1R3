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
use BaseController, Input, Response, DB, Sentry, View;
use Excel, PDF, Estrategia;

class ReporteEstrategiaController extends BaseController {

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id){
        try{
            $estrategia = Estrategia::with('metasAnios')->contenidoReporte()->find($id);
            //return Response::json($estrategia,500);
            $nombreArchivo = 'Carátula Estrategia Institucional';
            $nombreArchivo.=' - '.$estrategia->claveUnidadResponsable;

            $data = array('data' => $estrategia);

            //return Response::json($data,500);

            $pdf = PDF::loadView('expediente.pdf.estrategia-institucional',$data);

            $pdf->setPaper('LEGAL')->setOrientation('landscape')->setWarnings(false);

            return $pdf->stream($nombreArchivo.'.pdf');
        }catch(\Exception $e){
            return Response::json($e,500);
        }
    }
}