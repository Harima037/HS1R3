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
use Excel, CargaDatosEP01;

class ReporteEP20Controller extends BaseController {

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
				//$rows = Proyecto::cedulasAvances($mes,$ejercicio);

				$rows  = CargaDatosEP01::where('cargaDatosEP01.mes','=',$mes)
										->where('cargaDatosEP01.CP','=',$ejercicio)
										->join('catalogoFuncionesGasto AS funcionesGasto','funcionesGasto.clave','=',DB::raw('concat_ws(".",cargaDatosEP01.FI,cargaDatosEP01.FU,cargaDatosEP01.SF,cargaDatosEP01.SSF)'))
										->groupBy('cargaDatosEP01.FI')
										->groupBy('cargaDatosEP01.FU')
										->groupBy('cargaDatosEP01.SF')
										->groupBy('cargaDatosEP01.SSF');

				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }

				if(isset($parametros['buscar'])){		
					if($parametros['buscar']){
						$rows = $rows->where('funcionesGasto.descripcion','like','%'.$parametros['buscar'].'%');
					}
				}
				
				$total = count($rows->select('funcionesGasto.clave')->get());
				
				//var_dump($total);die;
				//$queries = DB::getQueryLog();
				//var_dump(end($queries));die;

				$rows = $rows->select('funcionesGasto.descripcion AS concepto',
								DB::raw('sum(cargaDatosEP01.presupuestoAprobado) AS presupuestoAprobado'),
								DB::raw('sum(cargaDatosEP01.modificacionNeta) AS modificacionNeta'),
								DB::raw('sum(cargaDatosEP01.presupuestoModificado) AS presupuestoModificado'))
							->skip(($parametros['pagina']-1)*10)->take(10)->get();

				$data = array('resultados'=>$total,'data'=>$rows);
				$http_status = 200;

				if($total<=0){
					$http_status = 404;
					$data = array('resultados'=>$total,"data"=>"No se encontraron variables",'code'=>'W00');
				}

				return Response::json($data,$http_status);
			}else{

				$rows  = CargaDatosEP01::where('cargaDatosEP01.mes','=',$mes)
						->where('cargaDatosEP01.CP','=',$ejercicio)
						->join('catalogoFuncionesGasto AS funcionesGasto','funcionesGasto.clave','=',DB::raw('concat_ws(".",cargaDatosEP01.FI,cargaDatosEP01.FU,cargaDatosEP01.SF,cargaDatosEP01.SSF)'))
						->groupBy('cargaDatosEP01.FI')
						->groupBy('cargaDatosEP01.FU')
						->groupBy('cargaDatosEP01.SF')
						->groupBy('cargaDatosEP01.SSF')
						->select('funcionesGasto.descripcion AS concepto',
								DB::raw('sum(cargaDatosEP01.presupuestoAprobado) AS presupuestoAprobado'),
								DB::raw('sum(cargaDatosEP01.presupuestoModificado) AS presupuestoModificado'),
								DB::raw('sum(cargaDatosEP01.presupuestoDevengadoModificado) AS presupuestoDevengadoModificado'),
								DB::raw('sum(cargaDatosEP01.presupuestoComprometidoModificado) AS presupuestoComprometidoModificado'),
								DB::raw('sum(cargaDatosEP01.presupuestoPorLiberar) AS presupuestoPorLiberar'),
								DB::raw('sum(cargaDatosEP01.disponiblePresupuestarioModificado) AS disponiblePresupuestarioModificado')
						);

				if(isset($parametros['buscar'])){		
					if($parametros['buscar']){
						$rows = $rows->where('funcionesGasto.descripcion','like','%'.$parametros['buscar'].'%');
					}
				}

				$rows = $rows->get();
				//var_dump($rows);die;
				//print_r($rows);die;

				//$queries = DB::getQueryLog();
				//var_dump($queries);die;

				$datos = array('datos'=>$rows);
				$datos['fecha_trimestre'] = '30 de Junio del 2015';

				//return Response::json($rows,200);

				Excel::create('ep20', function($excel) use ( $datos ){
					$excel->sheet('EP 20', function($sheet) use ( $datos ){
						$sheet->loadView('reportes.excel.estado-programatico-funcional',$datos);

						$sheet->mergeCells('A1:J1');
						$sheet->mergeCells('A2:J2');
						$sheet->mergeCells('A3:J3');
						$sheet->mergeCells('A4:J4');
						$sheet->mergeCells('A5:J5');

						$sheet->mergeCells('A8:A10');
						$sheet->mergeCells('B8:D8');
						$sheet->mergeCells('B9:B10');
						$sheet->mergeCells('C9:C10');
						$sheet->mergeCells('D9:D10');
						$sheet->mergeCells('F8:J8');
						$sheet->mergeCells('F9:F10');
						$sheet->mergeCells('G9:G10');
						$sheet->mergeCells('H9:H10');
						$sheet->mergeCells('I9:I10');
						$sheet->mergeCells('J9:J10');

						$sheet->cells('A1:J10',function($cells) { $cells->setAlignment('center'); });
						$sheet->getStyle('A1:J10')->getAlignment()->setWrapText(true);
						$sheet->getStyle('A8:J10')->applyFromArray(array(
						    'fill' => array(
						        'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
						        'color' => array('rgb' => '28A659')
						    ),
						    'font' => array(
						        'size'      =>  8,
						        'bold'      =>  true,
						        'color'		=> array('rgb'=>'000000')
						    ),
						    'borders' => array(
						    	'allborders' => array(
						    		'style' => \PHPExcel_Style_Border::BORDER_THIN,
	            					'color' => array('argb' => 'FFFFFF')
						    	)
						    )
						));
						$total = count($datos['datos']);
						$sheet->getStyle('A12:J'.($total+11))->getAlignment()->setWrapText(true);
						$sheet->setColumnFormat(array( 'B12:J'.($total+15) => '### ### ### ##0.00' ));
					});
				})->export('xlsx');
			}
		}catch(Exception $ex){
			return Response::json(array('data'=>'Ocurrio un error al generar el reporte.','message'=>$ex->getMessage(),'line'=>$ex->getLine()),500);
		}
	}

	function obtenerWord($datos){
		$phpWord = new \PhpOffice\PhpWord\PhpWord();

		$phpWord->setDefaultFontName('Arial');
		$phpWord->setDefaultFontSize(12);
		$phpWord->setDefaultParagraphStyle(
		    array(
		        'align'      => 'both',
		        'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0),
		        'spacing'    => 0,
		    )
		);

		$titulo = array('bold' => true);
		$texto = array('bold' => false);
		$centrado = array('align' => 'center');
		$justificado = array('align' => 'justify');

		$infoStyle = array('borderColor'=>'000000', 'borderSize'=>6);
		$claveStyle = array('borderColor'=>'000000','borderSize'=>6,'borderTopColor'=>'FFFFFF','cellMargin'=>200,'cellMarginTop'=>0);
		$headerStyle = array('borderColor'=>'FFFFFF','borderSize'=>0);

		$phpWord->addTableStyle('TablaInfo', $infoStyle);
		$phpWord->addTableStyle('TablaClave',$claveStyle);
		$phpWord->addTableStyle('TablaEncabezado',$headerStyle);

		$phpWord->addTitleStyle(1, $titulo, $justificado);
		$phpWord->addTitleStyle(2, $titulo, $justificado);
		$phpWord->addTitleStyle(3, $titulo, $justificado);
		$phpWord->addTitleStyle(4, $titulo, $justificado);
	    // Every element you want to append to the word document is placed in a section.
	    // To create a basic section:
	    $section = $phpWord->addSection(array('orientation'=>'landscape','size'=>'letter'));

		$header = $section->addHeader();
		$table = $header->addTable('TablaEncabezado');
		$row = $table->addRow();
		$row->addCell(3000)->addImage('img/EscudoGobiernoChiapas.png');
		$cell = $row->addCell(8128);
		$cell->addText(htmlspecialchars('GOBIERNO CONSTITUCIONAL DEL ESTADO DE CHIAPAS'),$titulo,$centrado);
		$cell->addTextBreak(0);
		$cell->addText(htmlspecialchars('SECRETARÍA DE SALUD'),$titulo,$centrado);
		$cell->addTextBreak();
		//$cell->addText(htmlspecialchars('ANÁLISIS FUNCIONAL AL '.$trimestres[$trimestre].' TRIMESTRE DEL '.date('Y')),$titulo,$centrado);
		$row->addCell(3000)->addImage('img/LogoInstitucional.png');
		$header->addTextBreak();

		$footer = $section->addFooter();
		$footer->addPreserveText(htmlspecialchars('Página {PAGE} de {NUMPAGES}.'), null,array('align' => 'right'));

		$section->addPageBreak();
		foreach ($datos['datos'] as $proyecto) {
			
			$section->addText(htmlspecialchars($proyecto['programaPresupuestarioDescipcion']));
			$section->addTextBreak();
			$section->addText(htmlspecialchars($proyecto['idClasificacionProyecto']));
			$section->addTextBreak();
			$section->addText(htmlspecialchars($proyecto['nombreTecnico']));
			$section->addTextBreak();
			$section->addText(htmlspecialchars($proyecto['ClavePresupuestaria']));
			$section->addTextBreak();
			$section->addText(htmlspecialchars($proyecto['finalidadProyecto']));
			$section->addTextBreak();

			$section->addPageBreak();
			/*
			if($clasificacion_anterior != $elemento->idClasificacionProyecto){
				$section->addText(htmlspecialchars('OBJETIVOS Y PRINCIPALES COMENTARIOS DE LOS PROYECTOS INMERSOS EN ESTA SUBFUNCIÓN'),$titulo);
				$section->addTextBreak();
				if($elemento->idClasificacionProyecto == 1){
					$section->addTitle(htmlspecialchars('PROYECTOS INSTITUCIONALES'),3);
				}else{
					$section->addTitle(htmlspecialchars('PROYECTOS DE INVERSIÓN'),3);
				}
				$section->addTextBreak();
				$clasificacion_anterior = $elemento->idClasificacionProyecto;
			}

			$section->addTitle(htmlspecialchars('Proyecto: '.$elemento->nombreTecnico.' ('.$elemento->unidadResponsableDescipcion.')'),4);
			$section->addTextBreak();

			$table = $section->addTable('TablaInfo');

			$row = $table->addRow();
			$row->addCell(3000)->addText('EJE',$titulo,$centrado);
			$row->addCell(3000)->addText('TEMA',$titulo,$centrado);
			$row->addCell(3065)->addText('POLÍTICA PÚBLICA',$titulo,$centrado);
			$row->addCell(5060)->addText('PROGRAMA PRESUPUESTARIO',$titulo,$centrado);

			$row = $table->addRow();
			$row->addCell(2500)->addText($elemento->ejeDescripcion);
			$row->addCell(2500)->addText($elemento->temaDescripcion);
			$row->addCell(4065)->addText($elemento->politicaPublicaDescripcion);
			$row->addCell(5060)->addText($elemento->programaPresupuestarioDescipcion);
			$section->addTextBreak();

			if($elemento->totalMetas > 0){
				if($elemento->cuentaPublica){
					$section->addText(htmlspecialchars($elemento->cuentaPublica),$texto,$justificado);
				}else{
					$section->addText(htmlspecialchars('No presentaron avances.'),$texto,$justificado);
				}
			}else{
				if($elemento->cuentaPublica){
					$section->addText(htmlspecialchars($elemento->cuentaPublica),$texto,$justificado);
				}else{
					$section->addText(htmlspecialchars('No hay metas programadas para este trimestre.'),$texto,$justificado);
				}
			}
			$section->addTextBreak();
			*/
		}

		header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
	    header("Content-Description: File Transfer");
		header('Content-Disposition: attachment; filename="CedulasAvance.docx"');
	    
	    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
	    $objWriter->save('php://output');
	}
}
?>