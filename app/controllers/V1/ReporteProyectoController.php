<?php

namespace V1;

use SSA\Utilerias\Util;
use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry, View;
use Excel, Proyecto, ComponenteMetaMes, ActividadMetaMes;

class ReporteProyectoController extends BaseController {

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		
		$idProyecto = $id;
		
		$recurso = Proyecto::contenidoCompleto()->find($idProyecto);
		
		//Datos para la hoja Programa Inversion
		$recurso->componentes->load(array('actividades','formula','dimension','frecuencia','tipoIndicador','unidadMedida','entregable','entregableTipo','entregableAccion','desgloseCompleto'));
					foreach ($recurso->componentes as $key => $componente) {
						$recurso->componentes[$key]->actividades->load(array('formula','dimension','frecuencia','tipoIndicador','unidadMedida'));
					}
		//Datos para la hoja Metas por Jurisdicción
		$componentesMetasJuris = ComponenteMetaMes::select('idComponente', 'claveJurisdiccion', DB::raw('sum(meta) as sumeta'))
												->where('idProyecto',$idProyecto)->groupBy('claveJurisdiccion','idComponente')->get();

		$actividadesMetasJuris = ActividadMetaMes::select('idActividad', 'claveJurisdiccion', DB::raw('sum(meta) as sumeta'))
												->where('idProyecto',$idProyecto)->groupBy('claveJurisdiccion','idActividad')->get();

	 	$recurso['componentesMetasJuris'] = $componentesMetasJuris;
	 	$recurso['actividadesMetasJuris'] = $actividadesMetasJuris;
	 	
	 	//if($recurso->idClasificacionProyecto == 2){
		 	//Datos para la hoja Metas Por Mes
		 	$componentesMetasMes = ComponenteMetaMes::select('idComponente', 'mes', DB::raw('sum(meta) as sumeta'))
													->where('idProyecto',$idProyecto)->groupBy('mes','idComponente')->get();

			$actividadesMetasMes = ActividadMetaMes::select('idActividad', 'mes', DB::raw('sum(meta) as sumeta'))
													->where('idProyecto',$idProyecto)->groupBy('mes','idActividad')->get();

			$recurso['componentesMetasMes'] = $componentesMetasMes;
		 	$recurso['actividadesMetasMes'] = $actividadesMetasMes;
		//}

		$data = array("data"=> $recurso);

		//var_dump($recurso->toArray());die();
		//return View::make('expediente.excel.programa')->with($data);die();

		$nombreArchivo = ($recurso->idClasificacionProyecto== 2) ? 'Carátula Proyecto Inversión' : 'Carátula Proyecto Institucional';
		$nombreArchivo.=' - '.$recurso->ClavePresupuestaria;

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
				$sheet->setBorder('A29:O33', 'thin');
				$fila=35;
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

		})->export('xls');

	}

}