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
use BaseController, Input, Response, DB, Sentry, View, PDF;
use Excel, EvaluacionAnalisisFuncional, SysConfiguracionVariable, Proyecto, ProyectosVariacionGastoRazones;

class ReporteVariacionesGastoController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		try{
			$parametros = Input::all();

			if(!isset($parametros['mes'])){
				$mes = Util::obtenerMesActual();
				if($mes == 0){ $mes = date('n')-1; }
			}else{
				$mes = intval($parametros['mes']);
			}

			if(!isset($parametros['ejercicio'])){
				$ejercicio = date('Y');
			}else{
				$ejercicio = intval($parametros['ejercicio']);
			}

			if(isset($parametros['formatogrid'])){
				$rows = Proyecto::variacionesGasto($mes,$ejercicio);

				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }

				if(isset($parametros['buscar'])){		
					if($parametros['buscar']){
						$rows = $rows->where(function($query)use($parametros){
							$query->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%')
								->orWhere(DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'),'like','%'.$parametros['buscar'].'%');
						});
					}
				}
				
				$total = $rows->get();
				$total = count($total);
				
				//var_dump($total);die;
				//$queries = DB::getQueryLog();
				//var_dump(end($queries));die;

				$rows = $rows->skip(($parametros['pagina']-1)*10)->take(10)->get();

				$data = array('resultados'=>$total,'data'=>$rows);
				$http_status = 200;

				if($total<=0){
					$http_status = 404;
					$data = array('resultados'=>$total,"data"=>"No se encontraron variables",'code'=>'W00');
				}

				return Response::json($data,$http_status);
			}else{
				$rows = Proyecto::reporteVariacionesGasto($mes,$ejercicio);

				if(isset($parametros['buscar'])){		
					if($parametros['buscar']){
						$rows = $rows->where(function($query)use($parametros){
							$query->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%')
								->orWhere(DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'),'like','%'.$parametros['buscar'].'%');
						});
					}
				}

				$rows = $rows->get();
				
				$datos = array();
				$cuantos = 0;
				
				$datos['fila'] = array();
				$arreglo = array();	
				$totalModificado = 0;
				$totalAprobado = 0;
				$totalDevengado = 0;
								
				foreach($rows as $row)
				{
					$data_row = array(					
						'nombre'=>$row['nombreTecnico'],
						'modificado' => $row['presupuestoModificado']/1000000,
						'aprobado' => $row['presupuestoAprobado']/1000000,
						'devengado' => $row['presupuestoDevengado']/1000000,
						'variacion' => ($row['presupuestoAprobado']-$row['presupuestoModificado'])/1000000,
						'variacion2' => ($row['presupuestoDevengado']-$row['presupuestoModificado'])/1000000,
						'razonesAprobado' => $row['razonesAprobado'],
						'razonesDevengado' => $row['razonesDevengado']						
					);
					
					$totalModificado += $row['presupuestoModificado']/1000000;
					$totalAprobado += $row['presupuestoAprobado']/1000000;
					$totalDevengado += $row['presupuestoDevengado']/1000000;
					
					$arreglo[] = $data_row;
					$cuantos++;
				}
				
				$datos['cuantos'] = $cuantos;
				$datos['fila'] = $arreglo;				
				$datos['totalModificado'] = $totalModificado;
				$datos['totalAprobado'] = $totalAprobado;
				$datos['totalDevengado'] = $totalDevengado;
				$datos['totalVariacion'] = $totalAprobado-$totalModificado;
				$datos['totalVariacion2'] = $totalDevengado-$totalModificado;
				$datos['porcentajeVariacion'] = $totalAprobado/$totalModificado*100-100;
				$datos['porcentajeVariacion2'] = $totalDevengado/$totalModificado*100-100;
				
				Excel::create('variaciones-gasto', function($excel) use ( $datos ){
				
				$excel->sheet('MODIFICADO-APROBADO', function($sheet) use ( $datos ){
					$sheet->setStyle(array(
					    'font' => array(
					        'name'      =>  'Arial',
					        'size'      =>  9
					    )
					));

					$sheet->loadView('reportes.excel.variaciones-gasto',$datos);
					$imagen = $this->obtenerImagen('EscudoGobiernoChiapas.png','A1');
					$imagen->setWorksheet($sheet);
					$imagen = $this->obtenerImagen('LogoInstitucional.png','F1');
					$imagen->setWorksheet($sheet);
					
					$sheet->mergeCells('B5:E5');
					$sheet->mergeCells('B7:E7');					
					$sheet->mergeCells('B8:B9');
					$sheet->mergeCells('C8:C9');
					$sheet->mergeCells('D8:E8');
					$sheet->mergeCells('A11:F11');
					$sheet->mergeCells('D12:E12');
					$sheet->mergeCells('A13:F13');
					$sheet->cells('B5:E10',function($cells){ $cells->setAlignment('center'); });
					$sheet->cells('A11:F12',function($cells){ $cells->setAlignment('center'); });

					$total = 14 + $datos['cuantos'];
					$i = 14;
																
					$sheet->getStyle('A14:B'.$total)->getAlignment()->setWrapText(true);
					$sheet->getStyle('F14:F'.$total)->getAlignment()->setWrapText(true);
					
					for($i=14;$i<=$total;$i++)
						$sheet->mergeCells('D'.$i.':E'.$i);
						
					$sheet->cells('B14:E'.$total,function($cells){ $cells->setAlignment('center'); });

					$sheet->setColumnFormat(array(
						'B10:E10' => '### ### ### ##0.00'
					));
					
										
					$sheet->setColumnFormat(array(
						'B14:E'.$total => '### ### ### ##0.00'
					));
					
					$sheet->getStyle('B5:E5')->applyFromArray(array(
					    'font' => array(
					        'size'      =>  11,
					        'bold'      =>  true,
					        'color'		=> array('rgb'=>'000000')
					    )
					));
					$sheet->getStyle('A13:F13')->applyFromArray(array(
					    'font' => array(
					        'size'      =>  10,
					        'bold'      =>  true,
					        'color'		=> array('rgb'=>'000000')
					    )
					));
					$sheet->getStyle('B7:E7')->applyFromArray(array(
					    'font' => array(
					        'size'      =>  11,
					        'bold'      =>  true,
					        'color'		=> array('rgb'=>'FFFFFF')
					    ),
						'fill' => array(
					        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
					        'color' => array('rgb' => '28A659')
					    ),
						'borders' => array(
					    	'allborders' => array(
					    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
	            				'color' => array('rgb' => '000000')
					    	)
					    )
					));
					$sheet->getStyle('B8:E10')->applyFromArray(array(
					    'font' => array(
					        'size'      =>  11,
					        'bold'      =>  true,
					        'color'		=> array('rgb'=>'000000')
					    ),						
						'borders' => array(
					    	'allborders' => array(
					    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
	            				'color' => array('rgb' => '000000')
					    	)
					    )
					));
					$sheet->getStyle('B8:E9')->applyFromArray(array(
					    'fill' => array(
					        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
					        'color' => array('rgb' => 'DDDDDD')
					    )
					));
					$sheet->getStyle('A11:F11')->applyFromArray(array(
					    'font' => array(
					        'size'      =>  11,
					        'bold'      =>  true,
					        'color'		=> array('rgb'=>'000000')
					    ),						
						'borders' => array(
					    	'allborders' => array(
					    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
	            				'color' => array('rgb' => '000000')
					    	)
					    )
					));
					$sheet->getStyle('A12:F12')->applyFromArray(array(
					    'font' => array(
					        'size'      =>  11,
					        'bold'      =>  true,
					        'color'		=> array('rgb'=>'FFFFFF')
					    ),
						'fill' => array(
					        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
					        'color' => array('rgb' => '28A659')
					    ),
						'borders' => array(
					    	'allborders' => array(
					    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
	            				'color' => array('rgb' => '000000')
					    	)
					    )
					));
					
					$sheet->getStyle('A13:F'.$total)->applyFromArray(array(
					    'font' => array(
					        'size'      =>  10
					    ),
						'borders' => array(
						  	'top' => array(
								'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
	            				'color' => array('argb' => '28A659')
						    ),
						    'bottom' => array(
								'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
								'color' => array('argb' => '28A659')
					    	),
							'left' => array(
								'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
								'color' => array('argb' => '28A659')
					    	),
							'right' => array(
								'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
								'color' => array('argb' => '28A659')
					    	),
							
						 )
					));
			    });
				$excel->sheet('MODIFICADO-DEVENGADO', function($sheet) use ( $datos ){
					$sheet->setStyle(array(
					    'font' => array(
					        'name'      =>  'Arial',
					        'size'      =>  9
					    )
					));

					$sheet->loadView('reportes.excel.variaciones-gasto2',$datos);
					$imagen = $this->obtenerImagen('EscudoGobiernoChiapas.png','A1');
					$imagen->setWorksheet($sheet);
					$imagen = $this->obtenerImagen('LogoInstitucional.png','F1');
					$imagen->setWorksheet($sheet);
					
					$sheet->mergeCells('B5:E5');
					$sheet->mergeCells('B7:E7');					
					$sheet->mergeCells('B8:B9');
					$sheet->mergeCells('C8:C9');
					$sheet->mergeCells('D8:E8');
					$sheet->mergeCells('A11:F11');
					$sheet->mergeCells('D12:E12');
					$sheet->mergeCells('A13:F13');
					$sheet->cells('B5:E10',function($cells){ $cells->setAlignment('center'); });
					$sheet->cells('A11:F12',function($cells){ $cells->setAlignment('center'); });

					$total = 14 + $datos['cuantos'];
					$i = 14;
																
					$sheet->getStyle('A14:B'.$total)->getAlignment()->setWrapText(true);
					$sheet->getStyle('F14:F'.$total)->getAlignment()->setWrapText(true);
					
					for($i=14;$i<=$total;$i++)
						$sheet->mergeCells('D'.$i.':E'.$i);
						
					$sheet->cells('B14:E'.$total,function($cells){ $cells->setAlignment('center'); });

					$sheet->setColumnFormat(array(
						'B10:E10' => '### ### ### ##0.00'
					));
					
										
					$sheet->setColumnFormat(array(
						'B14:E'.$total => '### ### ### ##0.00'
					));
					
					$sheet->getStyle('B5:E5')->applyFromArray(array(
					    'font' => array(
					        'size'      =>  11,
					        'bold'      =>  true,
					        'color'		=> array('rgb'=>'000000')
					    )
					));
					$sheet->getStyle('A13:F13')->applyFromArray(array(
					    'font' => array(
					        'size'      =>  10,
					        'bold'      =>  true,
					        'color'		=> array('rgb'=>'000000')
					    )
					));
					$sheet->getStyle('B7:E7')->applyFromArray(array(
					    'font' => array(
					        'size'      =>  11,
					        'bold'      =>  true,
					        'color'		=> array('rgb'=>'FFFFFF')
					    ),
						'fill' => array(
					        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
					        'color' => array('rgb' => '28A659')
					    ),
						'borders' => array(
					    	'allborders' => array(
					    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
	            				'color' => array('rgb' => '000000')
					    	)
					    )
					));
					$sheet->getStyle('B8:E10')->applyFromArray(array(
					    'font' => array(
					        'size'      =>  11,
					        'bold'      =>  true,
					        'color'		=> array('rgb'=>'000000')
					    ),						
						'borders' => array(
					    	'allborders' => array(
					    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
	            				'color' => array('rgb' => '000000')
					    	)
					    )
					));
					$sheet->getStyle('B8:E9')->applyFromArray(array(
					    'fill' => array(
					        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
					        'color' => array('rgb' => 'DDDDDD')
					    )
					));
					$sheet->getStyle('A11:F11')->applyFromArray(array(
					    'font' => array(
					        'size'      =>  11,
					        'bold'      =>  true,
					        'color'		=> array('rgb'=>'000000')
					    ),						
						'borders' => array(
					    	'allborders' => array(
					    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
	            				'color' => array('rgb' => '000000')
					    	)
					    )
					));
					$sheet->getStyle('A12:F12')->applyFromArray(array(
					    'font' => array(
					        'size'      =>  11,
					        'bold'      =>  true,
					        'color'		=> array('rgb'=>'FFFFFF')
					    ),
						'fill' => array(
					        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
					        'color' => array('rgb' => '28A659')
					    ),
						'borders' => array(
					    	'allborders' => array(
					    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
	            				'color' => array('rgb' => '000000')
					    	)
					    )
					));
					
					$sheet->getStyle('A13:F'.$total)->applyFromArray(array(
					    'font' => array(
					        'size'      =>  10
					    ),
						'borders' => array(
						  	'top' => array(
								'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
	            				'color' => array('argb' => '28A659')
						    ),
						    'bottom' => array(
								'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
								'color' => array('argb' => '28A659')
					    	),
							'left' => array(
								'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
								'color' => array('argb' => '28A659')
					    	),
							'right' => array(
								'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
								'color' => array('argb' => '28A659')
					    	),
							
						 )
					));
			    });

			})->download('xlsx');

				/*//var_dump($rows->toArray());die;*/
			}
		}catch(Exception $ex){
			return Response::json(array('data'=>'Ocurrio un error al generar el reporte.','message'=>$ex->getMessage(),'line'=>$ex->getLine()),500);
		}
	}
	//Funcion para editar
	public function show($id){
		//
		$http_status = 200;
		$data = array();
		$parametros = Input::all();
		
		$recurso = ProyectosVariacionGastoRazones::ultimaRazon($id)->get();
		
		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$recurso = $recurso->toArray();			
			$data["data"] = $recurso;
		}
		return Response::json($data,$http_status);
	}
	
	
	
	
	public function update($id)
	{
		//
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');
		
		$parametros = Input::all();
		
		//var_dump($parametros);die;
		
		if(isset($parametros["mes-razones"]))
		{
			$Busqueda = ProyectosVariacionGastoRazones::hallaRazonPorMes($id,$parametros["mes-razones"])->get();
			$idabuscar = 0;
			
			foreach($Busqueda as $row)
				$idabuscar = $row->id;
			
			$recurso = ProyectosVariacionGastoRazones::find($idabuscar);
			
			if(is_null($recurso)){//Insertar
				$recurso = new ProyectosVariacionGastoRazones;
				$recurso->idProyecto = $id;
				$recurso->mes = $parametros["mes-razones"];
			}
			//Si no es nulo, solamente actualizar las razones
			$recurso->razonesAprobado = $parametros["razones"];
			$recurso->razonesDevengado = $parametros["razones2"];
			$recurso->save();
			$respuesta['data']['data'] = $recurso;		
		}
		else
		{
			$respuesta['http_status'] = 500;
		}
		
		return Response::json($respuesta['data'],$respuesta['http_status']);
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