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
use Excel,PDF, Proyecto, FIBAP, ComponenteMetaMes, ActividadMetaMes,DocumentoSoporte,OrigenFinanciamiento, Programa,ComponenteDesglose;

class ReporteProyectoController extends BaseController {

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		try{

		$parametros = explode('|',$id);
		$idProyecto = $parametros[0];

		if(isset($parametros[1])){
	 		$reporte = $parametros[1];
	 	}else{
	 		$reporte = 'caratula';
	 	}
		//$idProyecto = $id;

	 	if($reporte == 'caratula'){
	 		$recurso = Proyecto::contenidoReporte()->with('componentesCompletoDescripcion.metasMes','beneficiariosDescripcion')->find($idProyecto);
	 	}elseif($reporte == 'fibap'){
	 		$recurso = Proyecto::contenidoReporte()->with('beneficiariosDescripcion')->find($idProyecto);
	 	}elseif($reporte == 'cedula'){
	 		$recurso = Proyecto::contenidoReporte()->find($idProyecto);
	 	}
		//Datos para la hoja Programa Inversion
		//var_dump($recurso->toArray());die();
		if($reporte == 'caratula'){
			$componentesMetasJuris = array();
			$componentesMetasMes = array();
			$actividadesMetasJuris = array();
			$actividadesMetasMes = array();
			foreach ($recurso->componentesCompletoDescripcion as $componente) {
				$componentesMetasJuris[$componente->id] = array(
					'OC' => 0, '01' => 0, '02' => 0, '03' => 0, '04' => 0, '05' => 0, '06' => 0, '07' => 0, '08' => 0, '09' => 0,
					 '10' => 0, 'estatal' => 0
				);
				$componentesMetasMes[$componente->id] = array(
					1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0, 'estatal' => 0
				);
				foreach ($componente->metasMes as $metas_mes) {
					$componentesMetasJuris[$componente->id][$metas_mes->claveJurisdiccion] += $metas_mes->meta;
					$componentesMetasJuris[$componente->id]['estatal'] += $metas_mes->meta;
					$componentesMetasMes[$componente->id][$metas_mes->mes] += $metas_mes->meta;
					$componentesMetasMes[$componente->id]['estatal'] += $metas_mes->meta;
				}
				foreach ($componente->actividadesDescripcion as $actividad) {
					$actividadesMetasJuris[$actividad->id] = array(
						'OC' => 0, '01' => 0, '02' => 0, '03' => 0, '04' => 0, '05' => 0, '06' => 0, '07' => 0, '08' => 0, '09' => 0,
						 '10' => 0, 'estatal' => 0
					);
					$actividadesMetasMes[$actividad->id] = array(
						1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0, 'estatal' => 0
					);
					foreach ($actividad->metasMes as $metas_mes) {
						$actividadesMetasJuris[$actividad->id][$metas_mes->claveJurisdiccion] += $metas_mes->meta;
						$actividadesMetasJuris[$actividad->id]['estatal'] += $metas_mes->meta;
						$actividadesMetasMes[$actividad->id][$metas_mes->mes] += $metas_mes->meta;
						$actividadesMetasMes[$actividad->id]['estatal'] += $metas_mes->meta;
					}
				}
			}

			if($recurso->idClasificacionProyecto == 1){
				if($recurso->idPrograma){
					$programa = Programa::with('arbolProblemas','arbolObjetivos','indicadoresDescripcion')
										->contenidoDetalle()
										->find($recurso->idPrograma);
					$recurso['programaPresupuestarioAsignado'] = $programa;
				}else{
					$recurso['programaPresupuestarioAsignado'] = FALSE;
				}
			}else{
				$recurso['programaPresupuestarioAsignado'] = FALSE;
			}

			//Datos para la hoja Metas por Jurisdicción
		 	$recurso['componentesMetasJuris'] = $componentesMetasJuris;
		 	$recurso['actividadesMetasJuris'] = $actividadesMetasJuris;
		 	//Datos para la hoja Metas Por Mes
			$recurso['componentesMetasMes'] = $componentesMetasMes;
		 	$recurso['actividadesMetasMes'] = $actividadesMetasMes;
		}
	 	//Arreglamos el arreglo de beneficiarios
	 	if($reporte == 'caratula' || $reporte == 'fibap'){
	 		$beneficiarios = array();
		 	foreach ($recurso['beneficiariosDescripcion'] as $key => $beneficiario) {
		 		if(!isset($beneficiarios[$beneficiario->idTipoBeneficiario])){
					$beneficiarios[$beneficiario->idTipoBeneficiario] = array(
							'tipo' => $beneficiario->tipoBeneficiario,
							'total' => $beneficiario->total,
							'desglose' => array('f'=>array(),'m'=>array())
						);
		 		}else{
		 			$beneficiarios[$beneficiario->idTipoBeneficiario]['total'] += $beneficiario->total;
		 		}

		 		$beneficiarios[$beneficiario->idTipoBeneficiario]['desglose'][$beneficiario->sexo] = array(
					'total' 	 => $beneficiario->total,
					'urbana'	 => $beneficiario->urbana,
					'rural'		 => $beneficiario->rural,
					'mestiza' 	 => $beneficiario->mestiza,
					'indigena' 	 => $beneficiario->indigena,
					'inmigrante' => $beneficiario->inmigrante,
					'otros'		 => $beneficiario->otros,
					'muyAlta'	 => $beneficiario->muyAlta,
					'alta'		 => $beneficiario->alta,
					'media'		 => $beneficiario->media,
					'baja'		 => $beneficiario->baja,
					'muyBaja'	 => $beneficiario->muyBaja,
				);
		 	}
		 	$recurso['beneficiarios'] = $beneficiarios;
	 	}
	 	//Si es proyecto de inversión se obtiene el FIBAP relacionado
	 	if($reporte == 'fibap'){
 		 	if($recurso->idClasificacionProyecto == 2){
 		 		$fibap = FIBAP::contenidoCompleto()->where('idProyecto',$idProyecto)->first();
 		 		$fibap->distribucion_presupuesto_agrupado->load(array('ObjetoGasto'));
 		 		$documentos_seleccionados = $fibap->documentos->lists('id','id');
 	
 		 		$documentos = DocumentoSoporte::all();
 		 		$documentos_soporte = array();
 		 		foreach ($documentos as $documento){
 		 			$documentos_soporte[] = array(
 		 				'descripcion' => $documento->descripcion,
 		 				'seleccionado' => (isset($documentos_seleccionados[$documento->id]))?TRUE:FALSE
 		 			);
 		 		}
 		 		$fibap['documentos_soporte'] = $documentos_soporte;
 		 		$recurso['fibap'] = $fibap->toArray();
 		 	}
	 	}

	 	if($reporte == 'cedula'){
	 		if($recurso->idClasificacionProyecto == 2){
	 			$fibap = FIBAP::with('accionesCompletasDescripcion')->where('idProyecto','=',$idProyecto)->first();
	 			$origenes = OrigenFinanciamiento::all();
	 			
	 			$origenes_financiamiento = array();
	 			$origenes_total = array();
	 			$distribucion_partidas = array();
	 			$distribucion_partidas_totales = array();
	 			$distribucion_beneficiarios = array();
	 			$distribucion_localidad = array();
	 			$total_lineas_desglose = array();
	 			foreach ($fibap->accionesCompletasDescripcion as $accion) {
	 				$origenes_financiamiento[$accion->id] = array();
	 				$origenes_total[$accion->id] = 0;
	 				$indice = 1;
		 			foreach ($origenes as $origen) {
		 				$origenes_financiamiento[$accion->id][$origen->id] = array(
		 					'indice' => $indice,
		 					'descripcion' => $origen->descripcion,
		 					'monto' => 0
		 				);
		 				$indice++;
		 			}

		 			foreach ($accion->propuestasFinanciamiento as $propuesta) {
		 				$origenes_financiamiento[$accion->id][$propuesta->idOrigenFinanciamiento]['monto'] = $propuesta->cantidad;
		 				$origenes_total[$accion->id] += $propuesta->cantidad;
		 			}

	 				if(!isset($distribucion_partidas[$accion->id])){
	 					$distribucion_partidas[$accion->id] = array();
	 					$distribucion_partidas_totales[$accion->id] = array(
	 						'total'=>0, 
	 						'desglose'=>array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0)
	 					);
	 				}
	 				foreach ($accion->distribucionPresupustoPartidaDescripcion as $distribucion) {
		 				if(!isset($distribucion_partidas[$accion->id][$distribucion->idObjetoGasto])){
		 					$distribucion_partidas[$accion->id][$distribucion->idObjetoGasto] = array(
		 						'partida' => $distribucion->partida,
		 						'descripcion' => $distribucion->objetoGastoDescripcion,
		 						'total' => 0,
		 						'desglose' => array()
		 					);
		 				}
		 				$distribucion_partidas_totales[$accion->id]['total'] += $distribucion->cantidad;
		 				$distribucion_partidas_totales[$accion->id]['desglose'][$distribucion->mes] += $distribucion->cantidad;
		 				$distribucion_partidas[$accion->id][$distribucion->idObjetoGasto]['total'] += $distribucion->cantidad;
		 				$distribucion_partidas[$accion->id][$distribucion->idObjetoGasto]['desglose'][$distribucion->mes] = $distribucion->cantidad;
		 			}
		 			if(!isset($distribucion_localidad[$accion->id])){
		 				$distribucion_localidad[$accion->id] = array();
		 				$distribucion_beneficiarios[$accion->id] = array();
		 				$beneficiarios_acumulado = array();
		 			}

		 			$desglose_completo = ComponenteDesglose::listarDatos()
		 													->where('idComponente','=',$accion->idComponente)
		 													->with('metasMes','beneficiariosDescripcion')->get();
		 			//return Response::json($desglose_completo,500);

		 			//foreach ($accion->desglosePresupuestoCompleto as $desglose) {
		 			foreach ($desglose_completo as $desglose) {
		 				foreach ($desglose->beneficiariosDescripcion as $beneficiario) {
		 					if(!isset($beneficiarios_acumulado[$beneficiario->idTipoBeneficiario])){
		 						$beneficiarios_acumulado[$beneficiario->idTipoBeneficiario] = array(
		 							'descripcion' => $beneficiario->tipoBeneficiario,
		 							'totalF' => 0,
		 							'total' => 0
		 						);
		 					}
		 					$beneficiarios_acumulado[$beneficiario->idTipoBeneficiario]['totalF'] += $beneficiario->totalF;
		 					$beneficiarios_acumulado[$beneficiario->idTipoBeneficiario]['total'] += ($beneficiario->totalM + $beneficiario->totalF);
		 				}

		 				$metas = array( 1 => 0, 2 => 0, 3 => 0, 4 => 0 );
		 				foreach ($desglose->metasMes as $meta_mes) {
		 					$trimestre = ceil($meta_mes->mes / 3);
		 					$metas[$trimestre] += $meta_mes->meta;
		 				}
		 				$distribucion_localidad[$accion->id][] = array(
		 					'municipio' => $desglose->municipio,
		 					'localidad' => $desglose->claveLocalidad .' '. $desglose->localidad,
		 					'monto' => $desglose->presupuesto,
		 					'unidad' => $accion->unidadMedida,
		 					'cantidad' => $metas[1] + $metas[2] + $metas[3] + $metas[4] ,
		 					'metas' => $metas
		 				);
		 			}
		 			foreach ($beneficiarios_acumulado as $beneficiario) {
		 				$distribucion_beneficiarios[$accion->id][] = $beneficiario;
		 			}

		 			if(count($distribucion_localidad[$accion->id]) > count($distribucion_beneficiarios[$accion->id])){
		 				$total_lineas_desglose[$accion->id] = count($distribucion_localidad[$accion->id]);
		 			}else{
		 				$total_lineas_desglose[$accion->id] = count($distribucion_beneficiarios[$accion->id]);
		 			}
	 			}

	 			$fibap['total_lineas_desglose'] = $total_lineas_desglose;
	 			$fibap['origenes_financiamiento'] = $origenes_financiamiento;
				$fibap['origenes_total'] = $origenes_total;
				$fibap['distribucion_beneficiarios'] = $distribucion_beneficiarios;
	 			$fibap['distribucion_localidad'] = $distribucion_localidad;
	 			$fibap['distribucion_partidas'] = $distribucion_partidas;
	 			$fibap['distribucion_partidas_totales'] = $distribucion_partidas_totales;
	 			
	 			$recurso['fibap'] = $fibap;
	 		}
	 	}

		$data = array('data' => $recurso, 'reporte' => $reporte);

		//return Response::json($recurso,500);
		//var_dump($recurso->toArray());die();

		$nombreArchivo = ($recurso->idClasificacionProyecto== 2) ? 'Carátula Proyecto Inversión' : 'Carátula Proyecto Institucional';
		$nombreArchivo.=' - '.$recurso->ClavePresupuestaria;

		$pdf = PDF::loadView('expediente.pdf.caratula-completa',$data);
		if($reporte == 'caratula' || $reporte == 'cedula'){
			$pdf->setPaper('LEGAL')->setOrientation('landscape')->setWarnings(false);
		}elseif($reporte == 'fibap'){
			$pdf->setPaper('LETTER')->setOrientation('portrait')->setWarnings(false);
		}
		
		return $pdf->stream($nombreArchivo.'.pdf');


		}catch(\Exception $e){
			return Response::json($e,500);
		}
	}

}