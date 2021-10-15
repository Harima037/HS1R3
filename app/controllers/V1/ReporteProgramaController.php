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

            return $pdf->stream($nombreArchivo.'.pdf');
        }catch(\Exception $e){
            return Response::json($e,500);
        }
    }
}