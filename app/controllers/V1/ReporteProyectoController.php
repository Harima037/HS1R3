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
*	@author 			Donaldo Ríos
*	@package 			poa
*	@version 			1.0
*	@comment 			
*/

namespace V1;

use SSA\Utilerias\Util;
use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, View;
use Excel,PDF, Proyecto, FIBAP, ComponenteMetaMes, ActividadMetaMes,DocumentoSoporte;

class ReporteProyectoController extends BaseController {

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$parametros = explode('|',$id);
		$idProyecto = $parametros[0];
		//$idProyecto = $id;

		//return Response::json(Proyecto::contenidoReporte()->find($idProyecto),500);

		$recurso = Proyecto::contenidoReporte()->find($idProyecto);
		//Datos para la hoja Programa Inversion
		//var_dump($recurso->toArray());die();
		
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

		//Datos para la hoja Metas por Jurisdicción
	 	$recurso['componentesMetasJuris'] = $componentesMetasJuris;
	 	$recurso['actividadesMetasJuris'] = $actividadesMetasJuris;

	 	//Datos para la hoja Metas Por Mes
		$recurso['componentesMetasMes'] = $componentesMetasMes;
	 	$recurso['actividadesMetasMes'] = $actividadesMetasMes;

	 	//Arreglamos el arreglo de beneficiarios
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

	 	//Si es proyecto de inversión se obtiene el FIBAP relacionado
	 	$fibap = null;
	 	if($recurso->idClasificacionProyecto == 2){

	 		$fibap = FIBAP::contenidoCompleto()->where('idProyecto',$idProyecto)->first();
	 		$fibap->distribucion_presupuesto_agrupado->load(array('ObjetoGasto'));

	 		$documentos = DocumentoSoporte::all();
	 		$documentos_soporte = array();
	 		foreach ($documentos as $documento) {
	 			$documentos_soporte[$documento->id] = array(
	 				'descripcion' => $documento->descripcion,
	 				'seleccionado' => FALSE
	 			);
	 		}

	 		foreach ($fibap->documentos as $documento) {
	 			$documentos_soporte[$documento->id]['seleccionado'] = TRUE;
	 		}

	 		$fibap['documentos_soporte'] = $documentos_soporte;

	 		$recurso['fibap'] = $fibap->toArray();
	 	}
	 	
	 	if(isset($parametros[1])){
	 		$reporte = $parametros[1];
	 	}else{
	 		$reporte = 'caratula';
	 	}

		$data = array('data' => $recurso, 'reporte' => $reporte);

		//return Response::json($recurso,500);
		//var_dump($recurso->toArray());die();

		//return View::make('expediente.excel.fibap')->with($data);

		$nombreArchivo = ($recurso->idClasificacionProyecto== 2) ? 'Carátula Proyecto Inversión' : 'Carátula Proyecto Institucional';
		$nombreArchivo.=' - '.$recurso->ClavePresupuestaria;

		//$pdf = PDF::loadView('expediente.excel.caratula',$data)->setPaper('LEGAL')->setOrientation('landscape')->setWarnings(false);
		$pdf = PDF::loadView('expediente.excel.caratula-completa',$data);

		if($reporte == 'caratula'){
			$pdf->setPaper('LEGAL')->setOrientation('landscape')->setWarnings(false);
		}elseif($reporte == 'fibap'){
			$pdf->setPaper('LETTER')->setOrientation('portrait')->setWarnings(false);
		}
		
		return $pdf->stream($nombreArchivo.'pdf');

		//return $pdf->download($nombreArchivo.'.pdf');
		/*
		Excel::create('archivo_prueba',function($excel) use ($data){
			$excel->sheet('Programa Institucional',function($sheet){
				$sheet->loadView('expediente.excel.caratula');
			});
		})->export('xls');
		return;
		Excel::create($nombreArchivo, function($excel) use ($data){

			if($data['data']->idClasificacionProyecto == 1){
				$excel->sheet('Programa Presupuestario', function($sheet)  use ($data){

			        $sheet->loadView('expediente.excel.programaPresupuestario', $data);
			        $sheet->setWidth('A', 26);
			        $sheet->setWidth('B', 35);
			        $sheet->setWidth('C', 23);
			        $sheet->setWidth('D', 26);
			        $sheet->setWidth('E', 19);
			        $sheet->setWidth('F', 16);
			        $sheet->setWidth('G', 13);
			        $sheet->cell('A10', function($cells) {
			        	$cells->setBorder('thin', 'thin', 'none', 'thin');
					});
					$sheet->cell('A11:A15', function($cells) {
			        	$cells->setBorder('none', 'none', 'none', 'thin');
					});
					$sheet->cell('E18', function($cells) {
			        	$cells->setBorder('none', 'none', 'thin', 'none');
					});
					$sheet->setBorder('B11:G11', 'thin');
					$sheet->cell('A16:D16', function($cells) {
			        	$cells->setBorder('thin', 'thin', 'none', 'thin');
					});
					$sheet->setBorder('A17:D18', 'thin');
					$sheet->setBorder('E12:F12', 'thin');
					$sheet->setBorder('A20:G20', 'thin');
					$sheet->setBorder('A22:C22', 'thin');
					$sheet->cell('A24', function($cells) {
			        	$cells->setBorder('thin', 'thin', 'none', 'thin');
					});
					$sheet->setBorder('A25:G25', 'thin');
					$sheet->setBorder('A26:B31', 'thin');
					$sheet->setBorder('D26:G31', 'thin');
					$sheet->setBorder('A34:G34', 'thin');
					$sheet->setBorder('A35:B40', 'thin');
					$sheet->setBorder('D35:G40', 'thin');
					$sheet->setBorder('A43:D46', 'thin');
					$sheet->setBorder('A49:G49', 'thin');
					$sheet->setBorder('A51:H51', 'thin');
					$sheet->setBorder('B53:H56', 'thin');
					$sheet->cell('A53', function($cells) {
			        	$cells->setBorder('thin', 'thin', 'none', 'thin');
					});
					$sheet->cell('A54:A55', function($cells) {
			        	$cells->setBorder('none', 'thin', 'none', 'thin');
					});
					$sheet->cell('A56', function($cells) {
			        	$cells->setBorder('none', 'thin', 'thin', 'thin');
					});
					$sheet->setBorder('A58:H58', 'thin');
					$sheet->cell('A60', function($cells) {
			        	$cells->setBorder('thin', 'thin', 'none', 'thin');
					});
					$sheet->setBorder('B61:D61', 'thin');
					$sheet->cell('A61:A64', function($cells) {
			        	$cells->setBorder('none', 'thin', 'none', 'thin');
					});
					$sheet->cell('E61', function($cells) {
			        	$cells->setBorder('thin', 'thin', 'none', 'thin');
					});
					$sheet->cell('A65', function($cells) {
			        	$cells->setBorder('none', 'none', 'thin', 'thin');
					});
					$sheet->cell('E65', function($cells) {
			        	$cells->setBorder('none', 'none', 'thin', 'none');
					});
					$sheet->setBorder('B65:D65', 'thin');
					$sheet->setBorder('A67:E67', 'thin');
					$sheet->setBorder('A71:H71', 'thin');
					$sheet->setBorder('B73:H76', 'thin');
					$sheet->cell('A73', function($cells) {
			        	$cells->setBorder('thin', 'thin', 'none', 'thin');
					});
					$sheet->cell('A74:A75', function($cells) {
			        	$cells->setBorder('none', 'thin', 'none', 'thin');
					});
					$sheet->cell('A76', function($cells) {
			        	$cells->setBorder('none', 'thin', 'thin', 'thin');
					});
					$sheet->setBorder('A78:H78', 'thin');
					$sheet->cell('A80', function($cells) {
			        	$cells->setBorder('thin', 'thin', 'none', 'thin');
					});
					$sheet->setBorder('B81:D81', 'thin');
					$sheet->cell('E81', function($cells) {
			        	$cells->setBorder('thin', 'thin', 'none', 'thin');
					});
					$sheet->cell('A81:A84', function($cells) {
			        	$cells->setBorder('none', 'thin', 'none', 'thin');
					});
					$sheet->setBorder('B85:D85', 'thin');
					$sheet->cell('A85', function($cells) {
			        	$cells->setBorder('none', 'thin', 'thin', 'thin');
					});
					$sheet->cell('E85', function($cells) {
			        	$cells->setBorder('none', 'none', 'thin', 'none');
					});
					$sheet->setBorder('A87:E87', 'thin');

			    });
			}

			$nombreHoja = ($data['data']->idClasificacionProyecto == 2) ? 'Programa Inversión' : 'Programa Institucional';

			$excel->sheet($nombreHoja, function($sheet)  use ($data){

		        $sheet->loadView('expediente.excel.programa', $data);
		        $sheet->setWidth('A', 30);
		        $sheet->setSize('A2', 30, 20);
		        $sheet->cell('E31', function($cell) {
		        	$cell->setBackground('#CCCCCC');
		        	$cell->setAlignment('center');
				});
				$sheet->cell('B30', function($cell) {
		        	$cell->setValignment('middle');
				});
				$sheet->cell('A10:B10', function($cells) {
		        	$cells->setBorder('thin', 'none', 'none', 'thin');
				});
				$sheet->cell('A19:B19', function($cells) {
		        	$cells->setBorder('none', 'none', 'thin', 'thin');
				});
				$sheet->cell('A11:A18', function($cells) {
		        	$cells->setBorder('none', 'none', 'none', 'thin');
				});

				$sheet->cell('A24:B24', function($cells) {
		        	$cells->setBorder('thin', 'none', 'none', 'thin');
				});
				$sheet->cell('A27:B27', function($cells) {
		        	$cells->setBorder('none', 'none', 'thin', 'thin');
				});
				$sheet->cell('A25:A26', function($cells) {
		        	$cells->setBorder('none', 'none', 'none', 'thin');
				});
				$sheet->setBorder('A8:O8', 'thin');
				$sheet->setBorder('C10:O17', 'thin');
				$sheet->setBorder('C18:H18', 'thin');
				$sheet->setBorder('C19:O19', 'thin');
				$sheet->setBorder('B21:K21', 'thin');
				$sheet->setBorder('B22:H22', 'thin');
				$sheet->setBorder('C24:K27', 'thin');
				$sheet->setBorder('A29:O31', 'thin');
				$sheet->setBorder('A32:O'.(31+(count($data['data']['beneficiarios'])*2)), 'thin');
				$fila=33 + (count($data['data']['beneficiarios'])*2);
				//Bordes para componentes
				foreach ($data['data']->componentes as $componente) {
					$sheet->setBorder('A'.$fila.':O'.$fila, 'thin');
					$sheet->setBorder('A'.($fila+1).':N'.($fila+1), 'thin');
					$sheet->setBorder('A'.($fila+2).':O'.($fila+4), 'thin');
					$sheet->setBorder('A'.($fila+6).':I'.($fila+6), 'thin');
					$sheet->setBorder('B'.($fila+7).':I'.($fila+10), 'thin');
					$sheet->setBorder('K'.($fila+7).':O'.($fila+10), 'thin');
					$sheet->setBorder('A'.($fila+11).':O'.($fila+13), 'thin');
					$fila+=15;
					//Bordes para actividades
					foreach ($componente->actividades as $actividad) {
						$sheet->setBorder('A'.($fila).':O'.($fila+3), 'thin');
						$sheet->setBorder('A'.($fila+5).':I'.($fila+9), 'thin');
						//$sheet->setBorder('B'.($fila+7).':I'.($fila+10), 'thin');
						$sheet->setBorder('K'.($fila+6).':O'.($fila+9), 'thin');
						$sheet->setBorder('A'.($fila+10).':O'.($fila+13), 'thin');
						$fila+=15;
					}
					//Bordes para el desglose del componente
					if($data['data']->idClasificacionProyecto == 2){
						foreach ($componente->desglose_completo as $desglose) {
							$sheet->setBorder('A'.($fila).':O'.($fila), 'thin');
							$sheet->setBorder('B'.($fila+1).':N'.($fila+2), 'thin');
							$sheet->setBorder('A'.($fila+4).':C'.($fila+4), 'thin');
							$sheet->setBorder('A'.($fila+5).':O'.($fila+5), 'thin');
							$fila+=7;
						}
					}
				}

		    });

				$excel->sheet('Anexo por Jurisdicción', function($sheet)  use ($data){

		        $sheet->loadView('expediente.excel.anexoPorJurisdiccion', $data);
		        $sheet->setWidth('A', 30);
		        $sheet->setWidth('B', 17);

		    });
			
			$excel->sheet('Metas x mes', function($sheet)  use ($data){

		        $sheet->loadView('expediente.excel.anexoMetasMes', $data);
		        $sheet->setWidth('A', 30);
		        $sheet->setWidth('B', 25);

		    });

		    if($data['data']->idClasificacionProyecto == 2){
		    	$excel->sheet('FIBAP', function($sheet)  use ($data){

			        $sheet->setStyle(array(
					    'font' => array(
					        'name'      =>  'Calibri',
					        'size'      =>  9
					    )
					));
			        $sheet->loadView('expediente.excel.fibap', $data);
			        $sheet->setWidth('A', 13);
			        $sheet->setWidth('B', 17);
			        $sheet->setWidth('C', 5);
			        $sheet->setWidth('D', 5);
			        $sheet->setWidth('E', 10);
			        $sheet->setWidth('F', 6);
			        $sheet->setWidth('G', 11);
			        $sheet->setWidth('H', 5);
			        $sheet->setWidth('I', 5);
			        $sheet->setWidth('J', 7);
			        $sheet->setWidth('K', 9);
			        $sheet->setWidth('L', 10);
			        $sheet->setWidth('M', 5);
			        $sheet->setWidth('N', 3);
			        $sheet->setWidth('O', 10);
			        $sheet->setWidth('P', 10);
			        $sheet->setWidth('Q', 10);
			        $sheet->setWidth('R', 10);

			        $sheet->setBorder('A6:R6', 'thin');
			        $sheet->setBorder('A8:R11', 'thin');
			        $sheet->setBorder('A15:R17', 'thin');
			        $sheet->setBorder('A21:E21', 'thin');
			        $sheet->setBorder('K21:R21', 'thin');
			        $sheet->setBorder('C24:C27', 'thin');
			        $sheet->setBorder('H24:H27', 'thin');
			        $sheet->setBorder('M25:M27', 'thin');
			        $sheet->setBorder('O24:R26', 'thin');

			        $fila = 32;

			        foreach($data['data']['fibap']['antecedentes_financieros'] as $antecedente){
				        $sheet->setBorder('A'.($fila), 'thin');
				        $sheet->setBorder('C'.($fila).':E'.($fila), 'thin');
				        $sheet->setBorder('G'.($fila), 'thin');
				        $sheet->setBorder('I'.($fila), 'thin');
				        $sheet->setBorder('L'.($fila).':R'.($fila), 'thin');
				        $fila++;
			    	}
			    	$fila+=2;
			    	$sheet->setBorder('A'.($fila).':I'.($fila+1), 'thin');
			    	$sheet->setBorder('L'.($fila).':R'.($fila+1), 'thin');
			    	$fila+=3;
			    	$sheet->setBorder('A'.($fila).':R'.($fila+1), 'thin');
			    	$fila+=3;
			    	$sheet->setBorder('A'.($fila).':R'.($fila), 'thin');
			    	$fila+=3;
			    	$sheet->setBorder('C'.($fila).':E'.($fila+1), 'thin');
			    	$sheet->setBorder('H'.($fila).':K'.($fila+2), 'thin');
			    	$sheet->setBorder('O'.($fila).':Q'.($fila+2), 'thin');
			    	$fila+=7;
			    	foreach($data['data']['fibap']['distribucion_presupuesto_agrupado'] as $distribucion){
			    		$sheet->setBorder('A'.($fila).':E'.($fila), 'thin');
			    		$sheet->setBorder('G'.($fila).':O'.($fila), 'thin');
			    		$sheet->setBorder('Q'.($fila), 'thin');
			    		$fila++;
			    	}
			    });
		    }
		})->export('xls');
		*/
	}

}