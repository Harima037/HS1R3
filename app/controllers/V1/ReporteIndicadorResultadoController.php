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
use BaseController, Input, Response, Sentry, View, Exception;
use Excel, EvaluacionAnalisisFuncional, SysConfiguracionVariable, Proyecto, SysGrupoModulo;

class ReporteIndicadorResultadoController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		if(!Sentry::hasAccess('REPORTES.INDIRESULT.R')){
			$datos['usuario'] = Sentry::getUser();
			$datos['sys_sistemas'] = SysGrupoModulo::all();
			return Response::view('errors.403', array(
				'usuario'=>$datos['usuario'],
				'sys_activo'=>null,
				'sys_sistemas'=>$datos['sys_sistemas'],
				'sys_mod_activo'=>null), 403
			);
		}

		try{
			$parametros = Input::all();
			$datos = array();
			
			if(!isset($parametros['mes'])){
				$mes = Util::obtenerMesActual();
				if($mes == 0){ 
					if(date('n') == 1){
						$mes = 12;
					}else{
						$mes = date('n')-1;
					}
				}
			}else{
				$mes = intval($parametros['mes']);
			}

			$trimestre = Util::obtenerTrimestre($mes);
			$texto_trimestres = array(1=>'PRIMER',2=>'SEGUNDO',3=>'TERCER',4=>'CUARTO');
			$datos['trimestre'] = $texto_trimestres[$trimestre];

			if(!isset($parametros['ejercicio'])){
				$datos['ejercicio'] = Util::obtenerAnioCaptura();
			}else{
				$datos['ejercicio'] = intval($parametros['ejercicio']);
			}

			$rows = Proyecto::reporteIndicadoresResultados($mes,$datos['ejercicio'])->get();
			$rows_fuente_finan = Proyecto::fuentesFinanciamientoEP01($mes,$datos['ejercicio'])->get();

			$fuentesFinan = array();
			foreach ($rows_fuente_finan as $fuente) {
				if(!isset($fuentesFinan[$fuente->idProyecto])){
					$fuentesFinan[$fuente->idProyecto] = array(
						'NombreTecnico' => $fuente->nombreTecnico,
						'totalPresupuestoAprobado'=>0,
						'totalPresupuestoModificado'=>0,
						'totalPresupuestoDevengado'=>0,
						//'fuentesFinanciamiento' => array()
						'fuentesFinanciamiento' => array(
							0 => array(
								'descripcion' 			=> 0,
								'clave' 				=> 0,
								'presupuestoAprobado' 	=> 0,
								'presupuestoModificado' => 0,
								'presupuestoDevengado' 	=> 0
							)
						)
					);
				}
				
				/*$fuentesFinan[$fuente->idProyecto]['fuentesFinanciamiento'][] = array(
					'descripcion' 			=> $fuente->descripcion,
					'clave' 				=> $fuente->clave,
					'presupuestoAprobado' 	=> $fuente->presupuestoAprobado,
					'presupuestoModificado' => $fuente->presupuestoModificado,
					'presupuestoDevengado' 	=> $fuente->presupuestoDevengado
				);*/

				$fuentesFinan[$fuente->idProyecto]['fuentesFinanciamiento'][0]['descripcion'] 			+= $fuente->descripcion;
				$fuentesFinan[$fuente->idProyecto]['fuentesFinanciamiento'][0]['clave'] 				+= $fuente->clave;
				$fuentesFinan[$fuente->idProyecto]['fuentesFinanciamiento'][0]['presupuestoAprobado'] 	+= $fuente->presupuestoAprobado;
				$fuentesFinan[$fuente->idProyecto]['fuentesFinanciamiento'][0]['presupuestoModificado'] += $fuente->presupuestoModificado;
				$fuentesFinan[$fuente->idProyecto]['fuentesFinanciamiento'][0]['presupuestoDevengado'] 	+= $fuente->presupuestoDevengado;

				$fuentesFinan[$fuente->idProyecto]['totalPresupuestoAprobado']		+= $fuente->presupuestoAprobado;
				$fuentesFinan[$fuente->idProyecto]['totalPresupuestoModificado']	+= $fuente->presupuestoModificado;
				$fuentesFinan[$fuente->idProyecto]['totalPresupuestoDevengado']		+= $fuente->presupuestoDevengado;
			}
			
			//$queries = DB::getQueryLog();
			//var_dump(end($queries));die;
			//return Response::json($fuentesFinan,200);
			//$rows = $rows->toArray();
			$hojas = array(
				'2.3.1.1'=>null,
				//'2.3.1.1_justificaciones'=>null,
				'2.3.4.1'=>null,
				//'2.3.4.1_justificaciones'=>null,
				'2.3.5.1'=>null,
				//'2.3.5.1_justificaciones'=>null,
				'2.3.2.1'=>null,
				'2.3.3.1'=>null,
				'4.4.1.1'=>null,
			);
			foreach ($rows as $row) {
				if(!$hojas[$row->subFuncionClave]){
					$hojas[$row->subFuncionClave] = array(
						'titulo' => $row->subFuncionDescripcion,
						'total_presup_aprobado' => 0,
						'total_presup_modificado' => 0,
						'total_presup_devengado' => 0,
						'conteo_items' => 0,
						'justificaciones'=>array(),
						'clase' => array()
					);
				}

				
				/*
				if(count($hojas) == 0){
					$hojas[] = array(
							'titulo'=>'Proyectos',
							'total_presup_aprobado' => 0,
							'total_presup_modificado' => 0,
							'total_presup_devengado' => 0,
							'conteo_items' => 0,
							'justificaciones'=>array(),
							'clase' => array()
						);
				}
				*/

				if(!isset($hojas[$row->subFuncionClave]['clase'][$row->idClasificacionProyecto])){
					$hojas[$row->subFuncionClave]['clase'][$row->idClasificacionProyecto] = array('proyectos'=>array());
					$hojas[$row->subFuncionClave]['conteo_items']++;
				}
				/*
				if(!isset($hojas[0]['clase'][$row->idClasificacionProyecto])){
					$hojas[0]['clase'][$row->idClasificacionProyecto] = array('proyectos'=>array());
					$hojas[0]['conteo_items']++;
				}
				*/

				$clave_fuentes = '';
				$titulo_fuentes = '';
				//---------------------------------------------------------------------------vvvvvvvvvvvvvvv
				$fuentes_del_proyecto = $fuentesFinan[$row->id];
				$total_fuentes = count($fuentes_del_proyecto['fuentesFinanciamiento']) - 1;
				$row->fuentesFinanciamiento = $fuentes_del_proyecto['fuentesFinanciamiento'];
				foreach ($fuentes_del_proyecto['fuentesFinanciamiento'] as $key => $fuente) {
					/*if($key == 0){
						$titulo_fuentes = $fuente['descripcion'];
					}elseif($key < $total_fuentes){
						$titulo_fuentes .= ', ' . $fuente['descripcion'];
					}else{
						$ultimo = ' y ';
						if(strtolower(substr($fuente['descripcion'],0,1)) == 'i'){
							$ultimo = ' e ';
						}elseif(strtolower(substr($fuente['descripcion'],0,1)) == 'h'){
							if(strtolower(substr($fuente['descripcion'],1,1)) == 'i'){
								$ultimo = ' e ';
							}
						}
						$titulo_fuentes .= $ultimo . $fuente['descripcion'];
					}*/
					//$clave_fuentes .= $fuente['clave'] .'_';
					$row->totalPresupuestoAprobado += $fuente['presupuestoAprobado'];
					$row->totalPresupuestoModificado += $fuente['presupuestoModificado'];
					$row->totalPresupuestoDevengado += $fuente['presupuestoDevengado'];
				}
				//---------------------------------------------------------------------------^^^^^^^^^^^^^^^

				/*if(!isset($hojas[$row->subFuncionClave]['clase'][$row->idClasificacionProyecto]['fuentes'][$clave_fuentes])){
					$hojas[$row->subFuncionClave]['clase'][$row->idClasificacionProyecto]['fuentes'][$clave_fuentes] = array(
						'titulo' => $titulo_fuentes,
						'proyectos'=>array()
					);
					//$hojas[$row->subFuncionClave]['conteo_items']++;
				}*/

				$hojas[$row->subFuncionClave]['clase'][$row->idClasificacionProyecto]['proyectos'][] = $row;
				$hojas[$row->subFuncionClave]['conteo_items']++;
				/*
				$hojas[0]['clase'][$row->idClasificacionProyecto]['proyectos'][] = $row;
				$hojas[0]['conteo_items']++;
				*/

				$row->desfaseActividades = count($row->componentes);

				$total_acciones = count($row->componentes) + count($row->actividades);
				$total_fuentes = count($row->fuentesFinanciamiento);

				//$hojas[$row->subFuncionClave]['justificaciones'] += $row->componentes->lists('justificacionAcumulada','identificador');
				//$hojas[$row->subFuncionClave]['justificaciones'] += $row->actividades->lists('justificacionAcumulada','identificador');

				if($total_acciones > $total_fuentes){
					$row->totalItems = $total_acciones;
				}else{
					$row->totalItems = $total_fuentes;
				}

				$hojas[$row->subFuncionClave]['conteo_items'] += $row->totalItems;
				$hojas[$row->subFuncionClave]['total_presup_aprobado'] += $row->totalPresupuestoAprobado;
				$hojas[$row->subFuncionClave]['total_presup_modificado'] += $row->totalPresupuestoModificado;
				$hojas[$row->subFuncionClave]['total_presup_devengado'] += $row->totalPresupuestoDevengado;

				/*
				$hojas[0]['conteo_items'] += $row->totalItems;
				$hojas[0]['total_presup_aprobado'] += $row->totalPresupuestoAprobado;
				$hojas[0]['total_presup_modificado'] += $row->totalPresupuestoModificado;
				$hojas[0]['total_presup_devengado'] += $row->totalPresupuestoDevengado;
				*/
			}
			
			//return Response::json($hojas,200);

			$datos['nombres_subfuncion'] = [
				'2.3.1.1'=>'SERVICIO A LA COMUNIDAD',
				'2.3.1.1_justificaciones'=>'COMUNIDAD JUSTIFICACIÓN',
				'2.3.4.1'=>'RECTORÍA DEL SISTEMA',
				'2.3.4.1_justificaciones'=>'RECTORÍA JUSTIFICACIÓN',
				'2.3.5.1'=>'PROTECCIÓN SOCIAL EN SALUD',
				'2.3.5.1_justificaciones'=>'PROTECCIÓN SOCIAL JUSTIFICACIÓN',
				'2.3.2.1'=>'SERVICIO A LA PERSONA',
				'2.3.2.1_justificaciones'=>'PERSONA JUSTIFICACIÓN',
				'2.3.3.1'=>'GENERACION RECURSOS SALUD',
				'2.3.3.1_justificaciones'=>'RECURSOS SALUD JUSTIFICACIÓN',
				'4.4.1.1'=>'ADEUDOS EJERCICIOS ANTERIORES',
				'4.4.1.1_justificaciones'=>'ADEUDOS FISCALES JUSTIFICACIÓN',
			];

			$datos['hojas'] = $hojas;

			$usuario = Sentry::getUser();
			$file_name = 'indicadores-resultados-'.$usuario->id;
			//return Response::json(array('data'=>$hojas),200);
			Excel::create($file_name, function($excel) use ( $datos ){
				$datos_hoja = array();
				$datos_hoja['ejercicio'] = $datos['ejercicio'];
				$datos_hoja['trimestre'] = $datos['trimestre'];

				//$justificaciones = array();
				
				foreach ($datos['hojas'] as $clave => $hoja) {
					//$excel->sheet('Proyectos', function($sheet) use ( $datos_hoja, $hoja ){
					$justificaciones = array();

					if(!$hoja){
						continue;
					}

					foreach ($hoja['clase'] as $clasificacion) {
						foreach($clasificacion['proyectos'] as $proyecto){
							foreach($proyecto->componentes as $componente){
								$justificaciones[] = array('clave'=>$proyecto->proyectoEstrategico.str_pad($proyecto->numeroProyectoEstrategico, 3,'0',STR_PAD_LEFT), 'nombre'=>rtrim($proyecto->nombreTecnico,'.'), 'justificacion'=>$componente->justificacionAcumulada);
							}
							foreach($proyecto->actividades as $actividad){
								$justificaciones[] = array('clave'=>$proyecto->proyectoEstrategico.str_pad($proyecto->numeroProyectoEstrategico, 3,'0',STR_PAD_LEFT), 'nombre'=>rtrim($proyecto->nombreTecnico,'.'), 'justificacion'=>$actividad->justificacionAcumulada);
							}
						}
					}

					$excel->sheet($datos['nombres_subfuncion'][$clave], function($sheet) use ( $datos_hoja, $hoja ){
						$sheet->setStyle(array(
						    'font' => array(
						        'name'      =>  'Arial',
						        'size'      =>  10
						    )
						));

						$datos_hoja['hoja'] = $hoja;

						$sheet->loadView('reportes.excel.indicadores-resultados',$datos_hoja);
						
				    	$sheet->mergeCells('A2:Q2');
						$sheet->mergeCells('A4:Q4');
						$sheet->cells('A2:Q4',function($cells){ $cells->setAlignment('center'); });
						$sheet->mergeCells('A9:A10');
						$sheet->mergeCells('B9:B10');
						$sheet->mergeCells('D9:E10');
						$sheet->mergeCells('F9:I9');
						$sheet->mergeCells('J9:J10');
						$sheet->mergeCells('K9:K10');
						$sheet->mergeCells('L9:L10');
						$sheet->mergeCells('M9:Q9');
						$sheet->mergeCells('A11:Q11');
						$sheet->cells('A9:Q12',function($cells) { $cells->setAlignment('center'); });
						$sheet->getStyle('A9:Q12')->getAlignment()->setWrapText(true);
						$sheet->getStyle('A9:Q10')->applyFromArray(array(
						    'fill' => array(
						        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
								//'color' => array('rgb' => '621132')
								'color' => array('rgb' => 'AFAFAF')
						    ),
						    'font' => array(
						        'size'      =>  8,
						        'bold'      =>  true,
								//'color'		=> array('rgb'=>'FFFFFF')
								'color'		=> array('rgb'=>'000000')
						    ),
						    'borders' => array(
						    	'allborders' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
	            					'color' => array('argb' => 'FFFFFF')
						    	)
						    )
						));
						$sheet->getStyle('A9:I10')->applyFromArray(array(
							'fill'=>array(
								'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
								'color' => array('rgb' => 'AFAFAF')
							)
						));
						$sheet->getStyle('M9:Q10')->applyFromArray(array(
							'fill'=>array(
								'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
								'color' => array('rgb' => 'AFAFAF')
							)
						));
						$sheet->getStyle('A11:Q11')->applyFromArray(array(
							'fill' => array(
						        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
								//'color' => array('rgb' => 'B09A5B')
								'color' => array('rgb' => 'AFAFAF')
						    ),
						    'font' => array(
						        'size'      =>  12,
								'bold'      =>  true,
								//'color'		=> array('rgb'=>'FFFFFF')
								'color'		=> array('rgb'=>'000000')
						    ),
						    'borders' => array(
						    	'top' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
	            					'color' => array('argb' => 'FFFFFF')
						    	)
						    )
						));
						$sheet->getStyle('A12:Q12')->applyFromArray(array(
						    'fill' => array(
						        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
						        'color' => array('rgb' => 'DDDDDD')
						    ),
						    'font' => array(
						        'size'      =>  12,
						        'bold'      =>  true,
						        'color'		=> array('rgb'=>'000000')
						    ),
						    'borders' => array(
						    	'top' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
	            					'color' => array('argb' => 'DDDDDD')
						    	)
						    	/*,'bottom' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
	            					'color' => array('argb' => '621132')
						    	)*/
						    )
						));
						$total = $hoja['conteo_items'] + 13;
						for ($i='A'; $i < 'Q' ; $i++) { 
							if($i != 'D'){
								$sheet->getStyle($i.'14:'.$i.$total)->applyFromArray(array(
								    'font' => array( 'size' => 10),
								    'borders' => array(
								    	'right' => array(
								    		'style' => \PHPExcel_Style_Border::BORDER_THICK,
			            					'color' => array('argb' => '002060')
								    	)
								    )
								));
							}
						}
						//$sheet->mergeCells('B14:B'.$total);
						$sheet->cell('B14',function($cell){
							$cell->setAlignment('center');
							//$cell->setValignment('center');
						});

						$sheet->getStyle('A14:Q'.$total)->getAlignment()->setWrapText(true);
						$sheet->setColumnFormat(array(
							'F14:H'.$total => '### ### ### ##0.00', //Aqui
							'J12:L12' => '### ### ### ##0.00',
							'I12:I'.$total => '### ### ### ##0.00',
							'J14:L'.$total => '### ### ### ##0.00',
							//'K12:N'.$total => '### ### ### ##0.00',
						    'O14:Q'.$total => '### ### ### ##0'
						));

						$imagen = $this->obtenerImagen('LogoInstitucional.png','A1');
						$imagen->setWorksheet($sheet);
						//$imagen = $this->obtenerImagen('LogoFederal.png','P1');
						//$imagen->setWorksheet($sheet);
				    });
					
					$ultima_linea = $excel->getActiveSheet()->getHighestDataRow();
					$rows_sumar = explode(',',$excel->getActiveSheet()->getCell('A'.$ultima_linea)->getValue());

					$suma_l = '';
					$suma_m = '';
					$suma_n = '';
					foreach ($rows_sumar as $indice => $valor) {
						$suma_l .= 'J'.$valor.',';
						$suma_m .= 'K'.$valor.',';
						$suma_n .= 'L'.$valor.',';
					}
					
					$excel->getActiveSheet()->setCellValue('B14','3');
					$excel->getActiveSheet()->setCellValue('A'.$ultima_linea,null);
					$excel->getActiveSheet()->setCellValue('J12','=SUM('.rtrim($suma_l,',').')');
					$excel->getActiveSheet()->setCellValue('K12','=SUM('.rtrim($suma_m,',').')');
					$excel->getActiveSheet()->setCellValue('L12','=SUM('.rtrim($suma_n,',').')');

					$excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1,10);

					$excel->getActiveSheet()->getHeaderFooter()->setOddHeader('&RPágina &P / &N');
					$excel->getActiveSheet()->getHeaderFooter()->setEvenHeader('&RPágina &P / &N');

					$excel->getActiveSheet()->getPageSetup()->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
					$excel->getActiveSheet()->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

					$excel->getActiveSheet()->getPageSetup()->setFitToPage(true);
					$excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
					$excel->getActiveSheet()->getPageSetup()->setFitToHeight(0);

					$excel->getActiveSheet()->getPageMargins()->setTop(0.3543307);
					$excel->getActiveSheet()->getPageMargins()->setBottom(0.3543307);

					$excel->getActiveSheet()->getPageMargins()->setRight(0.1968504);
					$excel->getActiveSheet()->getPageMargins()->setLeft(0.2755906);
					
					$excel->getActiveSheet()->getPageMargins()->setHeader(0.9055118);
					$excel->getActiveSheet()->getPageMargins()->setFooter(0.5118110);

					$excel->sheet($datos['nombres_subfuncion'][$clave.'_justificaciones'], function($sheet) use ( $justificaciones ){
						//$objPHPExcel->getActiveSheet()->fromArray($testArray, NULL, 'A1');
						$sheet->fromArray($justificaciones, NULL, 'A1');
					});
				}

				$excel->setActiveSheetIndex(0);
				//print_r($justificaciones);
				//throw new Exception("Error Processing Request Spageete somebodur", 1);
			//})->download('xlsx');
			})->store('xlsx', storage_path());

			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header("Content-Description: File Transfer");
			header('Content-Disposition: attachment; filename="IndicadoresResultados.xlsx"');

			$temp_file = storage_path($file_name.'.xlsx');
			
			readfile($temp_file);
			unlink($temp_file);
		}catch(Exception $ex){
			return Response::json(array('data'=>'Ocurrio un error al generar el reporte.','message'=>$ex->getMessage(),'line'=>$ex->getLine()),500);
		}
	}

	public function reporteGeneral(){
		if(!Sentry::hasAccess('REPORTES.INDIRESULT.R')){
			$datos['usuario'] = Sentry::getUser();
			$datos['sys_sistemas'] = SysGrupoModulo::all();
			return Response::view('errors.403', array(
				'usuario'=>$datos['usuario'],
				'sys_activo'=>null,
				'sys_sistemas'=>$datos['sys_sistemas'],
				'sys_mod_activo'=>null), 403
			);
		}

		try{
			$parametros = Input::all();
			$datos = array();
			
			if(!isset($parametros['mes'])){
				$mes = Util::obtenerMesActual();
				if($mes == 0){ 
					if(date('n') == 1){
						$mes = 12;
					}else{
						$mes = date('n')-1;
					}
				}
			}else{
				$mes = intval($parametros['mes']);
			}

			$trimestre = Util::obtenerTrimestre($mes);
			$texto_trimestres = array(1=>'PRIMER',2=>'SEGUNDO',3=>'TERCER',4=>'CUARTO');
			$datos['trimestre'] = $texto_trimestres[$trimestre];

			if(!isset($parametros['ejercicio'])){
				$datos['ejercicio'] = Util::obtenerAnioCaptura();
			}else{
				$datos['ejercicio'] = intval($parametros['ejercicio']);
			}

			$rows = Proyecto::reporteIndicadoresResultados($mes,$datos['ejercicio'])->get();
			$rows_fuente_finan = Proyecto::fuentesFinanciamientoEP01($mes,$datos['ejercicio'])->get();
			
			/*$queries = \DB::getQueryLog();
			var_dump(end($queries));die;*/
			
			$fuentesFinan = array();
			$fuentesDesglose = array();
			//return Response::json(array('data'=>$rows_fuente_finan),200);
			foreach ($rows_fuente_finan as $fuente) {
				if(!isset($fuentesFinan[$fuente->idProyecto])){
					$fuentesFinan[$fuente->idProyecto] = array(
						'NombreTecnico' => $fuente->nombreTecnico,
						'totalPresupuestoAprobado'=>0,
						'totalPresupuestoModificado'=>0,
						'totalPresupuestoDevengado'=>0,
						//'fuentesFinanciamiento' => array()
						'fuentesFinanciamiento' => array(
							0 => array(
								'descripcion' 			=> 0,
								'clave' 				=> 0,
								'presupuestoAprobado' 	=> 0,
								'presupuestoModificado' => 0,
								'presupuestoDevengado' 	=> 0
							)
						)
					);
				}
				$fuentesDesglose[$fuente->idProyecto][] = $fuente;
				
				$fuentesFinan[$fuente->idProyecto]['fuentesFinanciamiento'][0]['descripcion'] 			+= $fuente->descripcion;
				$fuentesFinan[$fuente->idProyecto]['fuentesFinanciamiento'][0]['clave'] 				+= $fuente->clave;
				$fuentesFinan[$fuente->idProyecto]['fuentesFinanciamiento'][0]['presupuestoAprobado'] 	+= $fuente->presupuestoAprobado;
				$fuentesFinan[$fuente->idProyecto]['fuentesFinanciamiento'][0]['presupuestoModificado'] += $fuente->presupuestoModificado;
				$fuentesFinan[$fuente->idProyecto]['fuentesFinanciamiento'][0]['presupuestoDevengado'] 	+= $fuente->presupuestoDevengado;

				$fuentesFinan[$fuente->idProyecto]['totalPresupuestoAprobado']		+= $fuente->presupuestoAprobado;
				$fuentesFinan[$fuente->idProyecto]['totalPresupuestoModificado']	+= $fuente->presupuestoModificado;
				$fuentesFinan[$fuente->idProyecto]['totalPresupuestoDevengado']		+= $fuente->presupuestoDevengado;
			}
			//return Response::json(array('data'=>$fuentesDesglose),200);
				
			
			
			//$queries = DB::getQueryLog();
			//var_dump(end($queries));die;
			//return Response::json($fuentesFinan,200);
			//$rows = $rows->toArray();
			$hojas = array();
			foreach ($rows as $row) {
				if(!isset($hojas[$row->subFuncionClave])){
					$hojas[$row->subFuncionClave] = array(
						'titulo' => $row->subFuncionDescripcion,
						'total_presup_aprobado' => 0,
						'total_presup_modificado' => 0,
						'total_presup_devengado' => 0,
						'conteo_items' => 0,
						'justificaciones'=>array(),
						'clase' => array()
					);
				}
				
				if(!isset($hojas[$row->subFuncionClave]['clase'][$row->idClasificacionProyecto])){
					$hojas[$row->subFuncionClave]['clase'][$row->idClasificacionProyecto] = array('proyectos'=>array());
					$hojas[$row->subFuncionClave]['conteo_items']++;
				}
				
				$row->fuentes_financiamiento = $fuentesDesglose[$row->id];
				$clave_fuentes = '';
				$titulo_fuentes = '';
				//---------------------------------------------------------------------------vvvvvvvvvvvvvvv
				//return Response::json(array('data'=>$fuentesFinan, "id"=>$row->id),200);
				$fuentes_del_proyecto = $fuentesFinan[$row->id];
				$total_fuentes = count($fuentes_del_proyecto['fuentesFinanciamiento']) - 1;
				$row->fuentesFinanciamiento = $fuentes_del_proyecto['fuentesFinanciamiento'];
				foreach ($fuentes_del_proyecto['fuentesFinanciamiento'] as $key => $fuente) {
					
					$row->totalPresupuestoAprobado += $fuente['presupuestoAprobado'];
					$row->totalPresupuestoModificado += $fuente['presupuestoModificado'];
					$row->totalPresupuestoDevengado += $fuente['presupuestoDevengado'];
				}
				
				$hojas[$row->subFuncionClave]['clase'][$row->idClasificacionProyecto]['proyectos'][] = $row;
				$hojas[$row->subFuncionClave]['conteo_items']++;
				
				$row->desfaseActividades = count($row->componentes);

				$total_acciones = count($row->componentes) + count($row->actividades);
				$total_fuentes = count($row->fuentes_financiamiento);

				if($total_acciones > $total_fuentes){
					$row->totalItems = $total_acciones;
				}else{
					$row->totalItems = $total_fuentes;
				}

				$hojas[$row->subFuncionClave]['conteo_items'] += $row->totalItems;
				$hojas[$row->subFuncionClave]['total_presup_aprobado'] += $row->totalPresupuestoAprobado;
				$hojas[$row->subFuncionClave]['total_presup_modificado'] += $row->totalPresupuestoModificado;
				$hojas[$row->subFuncionClave]['total_presup_devengado'] += $row->totalPresupuestoDevengado;
			}
			
			$datos['nombres_subfuncion'] = [
				'2.3.1.1'=>'Prest. serv. salud comunidad',
				'2.3.2.1'=>'Prest. serv. salud persona',
				'2.3.3.1'=>'Generación recursos salud',
				'2.3.4.1'=>'Rectoría sistema salud',
				'2.3.5.1'=>'Protección social en salud',
				'4.4.1.1'=>'Adeudos ejercicios anteriores',
			];

			//array_pop($hojas);
			//array_pop($hojas);

			$datos['hojas'] = $hojas;
			//return Response::json(array('data'=>$hojas),200);
			$usuario = Sentry::getUser();
			$file_name = 'indicadores-resultados-trimestral-'.$usuario->id;

			Excel::create($file_name, function($excel) use ( $datos ){
				$datos_hoja = array();
				$datos_hoja['ejercicio'] = $datos['ejercicio'];
				$datos_hoja['trimestre'] = $datos['trimestre'];

				$justificaciones = array();
				
				foreach ($datos['hojas'] as $clave => $hoja) {
					//$excel->sheet('Proyectos', function($sheet) use ( $datos_hoja, $hoja ){

					foreach ($hoja['clase'] as $clasificacion) {
						foreach($clasificacion['proyectos'] as $proyecto){
							foreach($proyecto->componentes as $componente){
								$justificaciones[] = array('clave'=>$proyecto->proyectoEstrategico.str_pad($proyecto->numeroProyectoEstrategico, 3,'0',STR_PAD_LEFT), 'nombre'=>rtrim($proyecto->nombreTecnico,'.'), 'justificacion'=>$componente->justificacionAcumulada);
							}
							foreach($proyecto->actividades as $actividad){
								$justificaciones[] = array('clave'=>$proyecto->proyectoEstrategico.str_pad($proyecto->numeroProyectoEstrategico, 3,'0',STR_PAD_LEFT), 'nombre'=>rtrim($proyecto->nombreTecnico,'.'), 'justificacion'=>$actividad->justificacionAcumulada);
							}
						}
					}

					$excel->sheet($datos['nombres_subfuncion'][$clave], function($sheet) use ( $datos_hoja, $hoja, $justificaciones ){
						$sheet->setStyle(array(
						    'font' => array(
						        'name'      =>  'Arial',
						        'size'      =>  10
						    )
						));

						$datos_hoja['hoja'] = $hoja;

						$sheet->loadView('reportes.excel.indicadores-resultados-trimestral',$datos_hoja);
						
				    	$sheet->mergeCells('A2:Q2');
						$sheet->mergeCells('A4:Q4');
						$sheet->cells('A2:Q4',function($cells){ $cells->setAlignment('center'); });
						$sheet->mergeCells('A9:A10');
						$sheet->mergeCells('B9:B10');
						$sheet->mergeCells('D9:E10');
						$sheet->mergeCells('F9:F10');
						$sheet->mergeCells('G9:G10');
						
						$sheet->mergeCells('H9:O9');
						$sheet->mergeCells('P9:P10');
						$sheet->mergeCells('Q9:Q10');
						$sheet->mergeCells('R9:R10');
						$sheet->mergeCells('S9:S10');
						
						$sheet->mergeCells('T9:X9');
						$sheet->mergeCells('Y9:Y10');
						$sheet->mergeCells('A11:Y11');
						/*$sheet->mergeCells('T9:T9');
						*/
						
						$sheet->cells('A9:Y12',function($cells) { $cells->setAlignment('center'); });
						$sheet->getStyle('A9:Y12')->getAlignment()->setWrapText(true);
						$sheet->getStyle('A9:Y10')->applyFromArray(array(
						    'fill' => array(
						        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
						        'color' => array('rgb' => '621132')
						    ),
						    'font' => array(
						        'size'      =>  8,
						        'bold'      =>  true,
						        'color'		=> array('rgb'=>'FFFFFF')
						    ),
						    'borders' => array(
						    	'allborders' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
	            					'color' => array('argb' => 'FFFFFF')
						    	)
						    )
						));
						$sheet->getStyle('A9:I10')->applyFromArray(array(
							'fill'=>array(
								'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
								'color' => array('rgb' => '621132')
							)
						));
						$sheet->getStyle('M9:Q10')->applyFromArray(array(
							'fill'=>array(
								'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
								'color' => array('rgb' => '621132')
							)
						));
						$sheet->getStyle('A11:Q11')->applyFromArray(array(
							'fill' => array(
						        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
						        'color' => array('rgb' => 'B09A5B')
						    ),
						    'font' => array(
						        'size'      =>  13,
								'bold'      =>  true,
								'color'		=> array('rgb'=>'FFFFFF')
						    ),
						    'borders' => array(
						    	'top' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
	            					'color' => array('argb' => 'FFFFFF')
						    	)
						    )
						));
						$sheet->getStyle('A12:Y12')->applyFromArray(array(
						    'fill' => array(
						        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
						        'color' => array('rgb' => 'DDDDDD')
						    ),
						    'font' => array(
						        'size'      =>  12,
						        'bold'      =>  true,
						        'color'		=> array('rgb'=>'000000')
						    ),
						    'borders' => array(
						    	'top' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
	            					'color' => array('argb' => 'DDDDDD')
						    	),
						    	'bottom' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
	            					'color' => array('argb' => '621132')
						    	)
						    )
						));
						$total = $hoja['conteo_items'] + 13;
						for ($i='A'; $i < 'Y' ; $i++) { 
							if($i != 'D'){
								$sheet->getStyle($i.'14:'.$i.$total)->applyFromArray(array(
								    'font' => array( 'size' => 10),
								    'borders' => array(
								    	'right' => array(
								    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
			            					'color' => array('argb' => '002060')
								    	)
								    )
								));
							}
						}
						$sheet->mergeCells('D14:D'.$total);
						$sheet->cell('D14',function($cell){
							$cell->setAlignment('center');
							$cell->setValignment('center');
						});

						$sheet->getStyle('A14:Q'.$total)->getAlignment()->setWrapText(true);
						$sheet->setColumnFormat(array(
							'H14:K'.$total => '### ### ### ##0.00', //Aqui
							'M12:N'.$total => '### ### ### ##0.00',
							'Q12:S'.$total => '### ### ### ##0.00',
							'V14:X'.$total => '### ### ### ##0.00',
							//'K12:N'.$total => '### ### ### ##0.00',
						    //'O14:Q'.$total => '### ### ### ##0'
						));

						/*$sheet->getStyle('L14:L'.$total)->getNumberFormat()->applyFromArray( 
							array( 
								'code' => \PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
							)
						);
						$sheet->getStyle('K14:K'.$total)->getNumberFormat()->applyFromArray( 
							array( 
								'code' => \PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
							)
						);*/

						$sheet->setColumnFormat(array(
							'K14:L'.$total => '0.00%'
						));
						$sheet->setColumnFormat(array(
							'N14:O'.$total => '0.00%'
						));

						$imagen = $this->obtenerImagen('LogoInstitucional.png','A1');
						$imagen->setWorksheet($sheet);
						//$imagen = $this->obtenerImagen('LogoFederal.png','P1');
						//$imagen->setWorksheet($sheet);
				    });
					
					$ultima_linea = $excel->getActiveSheet()->getHighestDataRow();
					$rows_sumar = explode(',',$excel->getActiveSheet()->getCell('A'.$ultima_linea)->getValue());

					$suma_l = '';
					$suma_m = '';
					$suma_n = '';
					foreach ($rows_sumar as $indice => $valor) {
						$suma_l .= 'Q'.$valor.',';
						$suma_m .= 'R'.$valor.',';
						$suma_n .= 'S'.$valor.',';
					}
					
					//$excel->getActiveSheet()->setCellValue('D14','3');
					$excel->getActiveSheet()->setCellValue('C'.$ultima_linea,null);
					$excel->getActiveSheet()->setCellValue('Q12','=SUM('.rtrim($suma_l,',').')');
					$excel->getActiveSheet()->setCellValue('R12','=SUM('.rtrim($suma_m,',').')');
					$excel->getActiveSheet()->setCellValue('S12','=SUM('.rtrim($suma_n,',').')');

					$excel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1,10);

					$excel->getActiveSheet()->getHeaderFooter()->setOddHeader('&RPágina &P / &N');
					$excel->getActiveSheet()->getHeaderFooter()->setEvenHeader('&RPágina &P / &N');

					$excel->getActiveSheet()->getPageSetup()->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
					$excel->getActiveSheet()->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

					$excel->getActiveSheet()->getPageSetup()->setFitToPage(true);
					$excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
					$excel->getActiveSheet()->getPageSetup()->setFitToHeight(0);

					$excel->getActiveSheet()->getPageMargins()->setTop(0.3543307);
					$excel->getActiveSheet()->getPageMargins()->setBottom(0.3543307);

					$excel->getActiveSheet()->getPageMargins()->setRight(0.1968504);
					$excel->getActiveSheet()->getPageMargins()->setLeft(0.2755906);
					
					$excel->getActiveSheet()->getPageMargins()->setHeader(0.9055118);
					$excel->getActiveSheet()->getPageMargins()->setFooter(0.5118110);
				}

				//print_r($justificaciones);
				//throw new Exception("Error Processing Request Spageete somebodur", 1);
				
				$excel->sheet('1.3 JUSTIFICACIONES', function($sheet) use ( $justificaciones ){
					//$objPHPExcel->getActiveSheet()->fromArray($testArray, NULL, 'A1');
					$sheet->fromArray($justificaciones, NULL, 'A1');
				});
			//})->download('xlsx');
			})->store('xlsx', storage_path());

			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header("Content-Description: File Transfer");
			header('Content-Disposition: attachment; filename="IndicadoresResultadosTrimestral.xlsx"');

			$temp_file = storage_path($file_name.'.xlsx');
			
			readfile($temp_file);
			unlink($temp_file);

		}catch(Exception $ex){
			return Response::json(array('data'=>'Ocurrio un error al generar el reporte.','message'=>$ex->getMessage(),'line'=>$ex->getLine()),500);
		}
	}

	private function obtenerImagen($imagen,$celda,$offset = 10){
		$objDrawing = new \PHPExcel_Worksheet_Drawing();
		$objDrawing->setPath('./img/'.$imagen);// filesystem reference for the image file
		$objDrawing->setHeight(90);// sets the image height to 36px (overriding the actual image height); 
		$objDrawing->setWidth(180);// sets the image height to 36px (overriding the actual image height); 
		$objDrawing->setCoordinates($celda);// pins the top-left corner of the image to cell D24
		$objDrawing->setOffsetX($offset);// pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
		return $objDrawing;
	}
}
?>