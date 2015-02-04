<?php

namespace V1;

use SSA\Utilerias\Util;
use SSA\Utilerias\Validador;
use BaseController, Input, Response, DB, Sentry;
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
		$recurso->componentes->load(array('actividades','formula','dimension','frecuencia','tipoIndicador','unidadMedida','entregable'));
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
	 	
	 	if($recurso->idClasificacionProyecto == 2){
		 	//Datos para la hoja Metas Por Mes
		 	$componentesMetasMes = ComponenteMetaMes::select('idComponente', 'mes', DB::raw('sum(meta) as sumeta'))
													->where('idProyecto',$idProyecto)->groupBy('mes','idComponente')->get();

			$actividadesMetasMes = ActividadMetaMes::select('idActividad', 'mes', DB::raw('sum(meta) as sumeta'))
													->where('idProyecto',$idProyecto)->groupBy('mes','idActividad')->get();

			$recurso['componentesMetasMes'] = $componentesMetasMes;
		 	$recurso['actividadesMetasMes'] = $actividadesMetasMes;
		}

		$data = array("data"=> $recurso);

		$nombreArchivo = ($recurso->idClasificacionProyecto== 2) ? 'Carátula Proyecto Inversión' : 'Carátula Proyecto Institucional';
		$nombreArchivo.=' - '.$recurso->ClavePresupuestaria;

		Excel::create($nombreArchivo, function($excel) use ($data){

			$excel->sheet('Programa Inversión', function($sheet)  use ($data){

		        $sheet->loadView('expediente.excel.programaInversion', $data);
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
				foreach ($data['data']->componentes as $componente) {
					$sheet->setBorder('A'.$fila.':O'.$fila, 'thin');
					$sheet->setBorder('A'.($fila+1).':N'.($fila+1), 'thin');
					$sheet->setBorder('A'.($fila+2).':O'.($fila+4), 'thin');
					$sheet->setBorder('A'.($fila+6).':I'.($fila+6), 'thin');
					$sheet->setBorder('B'.($fila+7).':I'.($fila+10), 'thin');
					$sheet->setBorder('K'.($fila+7).':O'.($fila+10), 'thin');
					$sheet->setBorder('A'.($fila+11).':O'.($fila+13), 'thin');
					$fila+=15;
					foreach ($componente->actividades as $actividad) {
						$sheet->setBorder('A'.($fila).':O'.($fila+3), 'thin');
						$sheet->setBorder('A'.($fila+5).':I'.($fila+9), 'thin');
						//$sheet->setBorder('B'.($fila+7).':I'.($fila+10), 'thin');
						$sheet->setBorder('K'.($fila+6).':O'.($fila+9), 'thin');
						$sheet->setBorder('A'.($fila+10).':O'.($fila+13), 'thin');
						$fila+=15;
					}
				}

		    });

			$excel->sheet('Anexo por Jurisdicción', function($sheet)  use ($data){

		        $sheet->loadView('expediente.excel.anexoPorJurisdiccion', $data);
		        $sheet->setWidth('A', 30);

		    });

			if($data['data']->idClasificacionProyecto == 2){
				$excel->sheet('Metas x mes', function($sheet)  use ($data){

			        $sheet->loadView('expediente.excel.anexoMetasMes', $data);
			        $sheet->setWidth('A', 30);

			    });
			}
			
		})->export('xls');

	}

}