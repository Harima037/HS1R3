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
use BaseController, Input, Response, DB, Sentry, View, PDF, Exception;
use Excel, EvaluacionAnalisisFuncional, SysConfiguracionVariable, Proyecto, Jurisdiccion, UnidadResponsable,CargaDatosEP01;
//use elliottb\phpgraphlib;
require_once base_path('vendor/elliottb/phpgraphlib/phpgraphlib.php');
require_once base_path('vendor/elliottb/phpgraphlib/phpgraphlib_pie.php');
//use \PHPGraphLib;

class ReporteEvaluacionProyectosController extends BaseController {

	protected $lista_imagenes = [];
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		try{
			$parametros = Input::all();

			//return Response::json(array('data'=>$image_data),200);

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

			$rows = Proyecto::reporteEvaluacionProyectos($ejercicio)
							->where('idEstatusProyecto',5)
							->with([
								'componentes.metasMes' => function($componentesMetasMes) use ($mes){
									$componentesMetasMes->where('mes','<=',$mes);
								},
								'componentes.actividades.metasMes' => function($actividadesMetasMes) use ($mes){
									$actividadesMetasMes->where('mes','<=',$mes);
								},
								'analisisFuncional' => function($analisisFuncional) use ($mes){
									$analisisFuncional->where('mes',$mes);
								},
								'conteoPlanesMejora' => function($planesMejora) use ($mes){
									$planesMejora->where('mes','<=',$mes);
								}
							])
							->get();
			//
			$presupuestos_raw = CargaDatosEP01::reporteProyectosEP01($mes,$ejercicio)->get()->toArray();

			$presupuesto_fuente_financiamiento = ['total'=>0.0,'fuentes'=>[]]; //<--- Calcular al obtener las fuentes de financiamiento
			$presupuestos = [];
			//return Response::json(array('datos'=>$presupuestos_raw),200);
			foreach ($presupuestos_raw as $proyecto) {
				if(!isset($presupuestos[$proyecto['clavePresupuestaria']])){
					$presupuestos[$proyecto['clavePresupuestaria']] = [
						'presupuestoAprobado' => 0.00,
						'presupuestoDevengadoModificado' => 0.00,
						'presupuestoEjercidoModificado' => 0.00,
						'presupuestoModificado' => 0.00,
						'fuentesFinanciamiento' => [],
						'subFuentesFinanciamiento' => [],
						'tiposRecursos' => [],
						'afectacionPresupuestaria'=>[]
					];
				}

				$presupuestos[$proyecto['clavePresupuestaria']]['presupuestoAprobado'] += $proyecto['presupuestoAprobado'];
				$presupuestos[$proyecto['clavePresupuestaria']]['presupuestoDevengadoModificado'] += $proyecto['presupuestoDevengadoModificado'];
				$presupuestos[$proyecto['clavePresupuestaria']]['presupuestoEjercidoModificado'] += $proyecto['presupuestoEjercidoModificado'];
				$presupuestos[$proyecto['clavePresupuestaria']]['presupuestoModificado'] += $proyecto['presupuestoModificado'];
				$presupuestos[$proyecto['clavePresupuestaria']]['fuentesFinanciamiento'][$proyecto['claveFuenteFinanciamiento']] = $proyecto['fuenteFinanciamiento'];
				$presupuestos[$proyecto['clavePresupuestaria']]['subFuentesFinanciamiento'][$proyecto['claveSubFuenteFinanciamiento']] = $proyecto['subFuenteFinanciamiento'];
				if(!isset($presupuestos[$proyecto['clavePresupuestaria']]['tiposRecursos'][$proyecto['claveTipoRecurso']])){
					$presupuestos[$proyecto['clavePresupuestaria']]['tiposRecursos'][$proyecto['claveTipoRecurso']] = $proyecto['tipoRecurso'];
				}
				if($proyecto['claveTipoRecurso'] == '1' || $proyecto['claveTipoRecurso'] == '4' || $proyecto['claveTipoRecurso'] == '6'){
					$presupuestos[$proyecto['clavePresupuestaria']]['afectacionPresupuestaria']['EST'] = 'Estatal';
				}else{
					$presupuestos[$proyecto['clavePresupuestaria']]['afectacionPresupuestaria']['FED'] = 'Federal';
				}
				

				$presupuesto_fuente_financiamiento['total'] += $proyecto['presupuestoAprobado'];
				if(!isset($presupuesto_fuente_financiamiento['fuentes'][$proyecto['claveFuenteFinanciamiento']])){
					$presupuesto_fuente_financiamiento['fuentes'][$proyecto['claveFuenteFinanciamiento']] = [
						'fuenteFinanciamiento' => str_replace(['á','é','í','ó','ú','Á','É','Í','Ó','Ú'],['a','e','i','o','u','A','E','I','O','U'], $proyecto['fuenteFinanciamiento']),
						'monto' => 0.00,
						'porcentaje' => 0.00
					];
				}
				$presupuesto_fuente_financiamiento['fuentes'][$proyecto['claveFuenteFinanciamiento']]['monto'] += $proyecto['presupuestoAprobado'];
			}

			$conversionCapitalizar = function($cadena){
				return mb_convert_case($cadena, MB_CASE_TITLE, 'UTF-8');
			};

			$catalogos['jurisdicciones'] = array_map($conversionCapitalizar, Jurisdiccion::get()->lists('nombre','clave'));
			$catalogos['unidades_responsables'] = UnidadResponsable::get()->lists('abreviatura','clave');

			$datos = $rows->toArray();
			//return Response::json(array('datos'=>$datos),200);
			$this->obtenerWord($datos,$presupuestos,$presupuesto_fuente_financiamiento,$catalogos);
		}catch(Exception $ex){
			if(count($this->lista_imagenes) > 0){
				foreach ($this->lista_imagenes as $imagen) { unlink($imagen); }
			}
			return Response::json(array('data'=>'Ocurrio un error al generar el reporte.','message'=>$ex->getMessage(),'line'=>$ex->getLine()),500);
		}
	}

	function obtenerWord($datos,$presupuestos,$presupuesto_fuente_financiamiento,$catalogos){
		$phpWord = new \PhpOffice\PhpWord\PhpWord();

		$phpWord->setDefaultFontName('Arial');
		$phpWord->setDefaultFontSize(10);
		$phpWord->setDefaultParagraphStyle(
		    array(
		        'align'      => 'both',
		        'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0),
		        'spacing'    => 0,
		    )
		);

	/***                <<<<<<<<<<<<<<<<<<<<  Definición de Estilos   >>>>>>>>>>>>>>>>>>>                   ***/
		$titulo_pag = array('bold' => true,'size'=>20);
		$titulo = array('bold' => true);
		$titulo_tabla = array('bold' => true, 'size'=>7, 'color'=>'FFFFFF');
		$titulo_tabla_style = array('valign'=>'center', 'bgColor'=>'28A659');
		$titulo_row_span =  array('vMerge'=>'restart', 'valign'=>'center', 'bgColor'=>'28A659');
		$titulo_row_continue =  array('vMerge'=>'continue', 'valign'=>'center', 'bgColor'=>'28A659');
		$titulo_col_span_4 =  array('gridSpan'=>4, 'valign'=>'center', 'bgColor'=>'28A659');
		$titulo_cell = array('valign'=>'center','bgColor'=>'068437');
		$cell_middle_1 = array('valign'=>'center');
		$cell_middle_2 = array('valign'=>'center','bgColor'=>'DDDDDD');

		$texto = array('bold' => false);
		$texto_tabla = array('bold' => false, 'size'=>7);
		$centrado = array('align' => 'center');
		$justificado = array('align' => 'justify');
		$derecha = array('align' => 'right');
		$izquierda = array('align' => 'left');

		$infoStyle = array('borderColor'=>'000000', 'borderSize'=>6);
		$infoHeader = array('borderTopColor'=>'000000', 'borderTopSize'=>6,'borderBottomColor'=>'000000', 'borderBottomSize'=>6);
		$headerStyle = array('borderColor'=>'FFFFFF','borderSize'=>0);

		$phpWord->addTableStyle('TablaInfo', $infoStyle);
		$phpWord->addTableStyle('TablaInfoHeader', $infoHeader);
		$phpWord->addTableStyle('TablaEncabezado',$headerStyle);
		$phpWord->addTitleStyle(3, ['bold'=>true], ['align'=>'center']);

		$section = $phpWord->addSection(array('orientation'=>'portrait','size'=>'letter'));
		$section_resumen = $phpWord->addSection(array('orientation'=>'portrait','size'=>'letter'));
	/***                <<<<<<<<<<<<<<<<<<<<  Definición de Estilos   >>>>>>>>>>>>>>>>>>>                   ***/
	    $sectionStyle = $section_resumen->getStyle();
		$sectionStyle->setMarginLeft(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(0.75));
		$sectionStyle->setMarginRight(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(0.5));
		$sectionStyle->setMarginTop(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(0.1));
		$sectionStyle->setMarginBottom(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(0.5));

	    $sectionStyle = $section->getStyle();
		$sectionStyle->setMarginLeft(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(0.75));
		$sectionStyle->setMarginRight(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(0.5));
		$sectionStyle->setMarginTop(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(0.1));
		$sectionStyle->setMarginBottom(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(0.5));
	/***                <<<<<<<<<<<<<<<<<<<<  Definición de Estilos   >>>>>>>>>>>>>>>>>>>                   ***/

	/***                <<<<<<<<<<<<<<<<<<<<  Encabezado   >>>>>>>>>>>>>>>>>>>                   ***/
		$header = $section_resumen->addHeader();
		$table = $header->addTable('TablaEncabezado');
		$row = $table->addRow();
		$row->addCell(3000)->addImage('img/LogoFederal.png',array('width' => 90,'height' => 30,'align'=>'left'));
		$cell = $row->addCell(5128)->addImage('img/EscudoGobiernoChiapas.png',array('width' => 100,'height' => 30,'align'=>'center'));
		$row->addCell(3000)->addImage('img/LogoInstitucional.png',array('width' => 80,'height' => 30,'align'=>'right'));

		$header = $section->addHeader();
		$table = $header->addTable('TablaEncabezado');
		$row = $table->addRow();
		$row->addCell(3000)->addImage('img/LogoFederal.png',array('width' => 90,'height' => 30,'align'=>'left'));
		$cell = $row->addCell(5128)->addImage('img/EscudoGobiernoChiapas.png',array('width' => 100,'height' => 30,'align'=>'center'));
		$row->addCell(3000)->addImage('img/LogoInstitucional.png',array('width' => 80,'height' => 30,'align'=>'right'));
	/***                <<<<<<<<<<<<<<<<<<<<  Encabezado   >>>>>>>>>>>>>>>>>>>                   ***/

	/***                <<<<<<<<<<<<<<<<<<<<  Pie de Página   >>>>>>>>>>>>>>>>>>>                   ***/
		$footer = $section_resumen->addFooter();
		$footer->addPreserveText(htmlspecialchars('{PAGE}'), null,array('align' => 'center'));		

		$footer = $section->addFooter();
		$footer->addPreserveText(htmlspecialchars('{PAGE}'), null,array('align' => 'center'));
	/***                <<<<<<<<<<<<<<<<<<<<  Pie de Página   >>>>>>>>>>>>>>>>>>>                   ***/

		$this->lista_imagenes = [];

	/***             <<<<<<<<<<<<<<<<<<<<  Objetos para Análisis General  >>>>>>>>>>>>>>>>>>>                 ***/
		$proyectos_unidad_responsable = [];
		$proyectos_tipo_proyecto = ['INVERSION'=>0,'INSTITUCIONAL'=>0];

		$proyectos_tipo_recurso = [];
		$proyectos_fuente_financiamiento = ['Mezcla de recursos'=>0];
		$proyectos_subfuente_financiamiento = [];
		
		$cumplimiento_indicadores = ['Satisfactorio'=>0,'No Satisfactorio'=>0];
		$cumplimiento_indicadores_no_satisfactorio = ['Alto avance'=>0,'Bajo avance'=>0];
		$cumplimiento_unidad_responsable = []; //'01'=>0,'02'=>0,...
		$cumplimiento_proyecto = ['Satisfactorio'=>0,'No Satisfactorio'=>0];
		$cumplimiento_proyecto_no_satisfactorio = ['Alto avance'=>0,'Bajo avance'=>0];
		$causas_bajo_avance = []; // [['causa'=>'','no_proyecto'=>0],['causa'=>'','no_proyecto'=>0],...];
		$causas_alto_avance = []; // [['causa'=>'','no_proyecto'=>0],['causa'=>'','no_proyecto'=>0],...];
		$causas_sin_avance = []; // [['causa'=>'','no_proyecto'=>0],['causa'=>'','no_proyecto'=>0],...];
	/***             <<<<<<<<<<<<<<<<<<<<  Objetos para Análisis General  >>>>>>>>>>>>>>>>>>>                 ***/

		foreach ($datos as $index => $proyecto) {

		/***   Datos pertenecientes al análisis general  ***/
			if(!isset($proyectos_unidad_responsable[$proyecto['unidadResponsableDescripcion']])){
				$proyectos_unidad_responsable[$proyecto['unidadResponsableDescripcion']] = 0;
			}
			$proyectos_unidad_responsable[$proyecto['unidadResponsableDescripcion']] += 1;
			if($proyecto['idClasificacionProyecto'] == 1){
				$proyectos_tipo_proyecto['INSTITUCIONAL'] += 1;
			}else{
				$proyectos_tipo_proyecto['INVERSION'] += 1;
			}
		/***   Datos pertenecientes al análisis general  ***/

			$section->addTitle(htmlspecialchars('4.' . ($index+1) . ' ' . $proyecto['nombreTecnico']),3);
			$section->addText(htmlspecialchars('('.$proyecto['ClavePresupuestaria'].')'),$titulo,$centrado);

			$section->addTextBreak(1);
		/***                <<<<<<<<<<<<<<<<<<<<  Información General  >>>>>>>>>>>>>>>>>>>                   ***/
			$textrun = $section->addTextRun();
			$textrun->addText('Área líder del proyecto: ', array('bold' => true));
			$textrun->addText($proyecto['unidadResponsableDescripcion']);
			//$section->addTextBreak();

			$textrun = $section->addTextRun();
			$textrun->addText('Tipo de proyecto estratégico: ', array('bold' => true));
			$textrun->addText(($proyecto['idClasificacionProyecto'])?'Institucional':'Inversión');
			//$section->addTextBreak();

			$textrun = $section->addTextRun();
			$textrun->addText('Tipo de proyecto: ', array('bold' => true));
			$textrun->addText($proyecto['tipoProyectoDescripcion']);
			//$section->addTextBreak();

			$textrun = $section->addTextRun();
			$textrun->addText('Cobertura: ', array('bold' => true));
			$textrun->addText($proyecto['coberturaDescripcion']);
			//$section->addTextBreak();

			if(isset($presupuestos[$proyecto['ClavePresupuestaria']])){
				$textrun = $section->addTextRun();
				$textrun->addText('Afectación presupuestaria: ', array('bold' => true));
				$textrun->addText(implode(' y ', $presupuestos[$proyecto['ClavePresupuestaria']]['afectacionPresupuestaria']));
				//$section->addTextBreak();

				$textrun = $section->addTextRun();
				$textrun->addText('Fuente de financiamiento: ', array('bold' => true));
				$textrun->addText(implode(', ', $presupuestos[$proyecto['ClavePresupuestaria']]['fuentesFinanciamiento']));
				//$section->addTextBreak();

				$textrun = $section->addTextRun();
				$textrun->addText('Subfuente de financiamiento: ', array('bold' => true));
				$textrun->addText(implode(', ', $presupuestos[$proyecto['ClavePresupuestaria']]['subFuentesFinanciamiento']));
				//$section->addTextBreak();

				$cantidad_letras = ucfirst(mb_strtolower(ltrim(Util::transformarCantidadLetras($presupuestos[$proyecto['ClavePresupuestaria']]['presupuestoAprobado'])),'UTF-8'));

				$textrun = $section->addTextRun();
				$textrun->addText('Presupuesto autorizado: ', array('bold' => true));
				$textrun->addText('$ ' . number_format($presupuestos[$proyecto['ClavePresupuestaria']]['presupuestoAprobado'],2) . ' ('.$cantidad_letras.' M.N.)');
				$section->addTextBreak(1);

				$tipo_recurso = implode(' / ', $presupuestos[$proyecto['ClavePresupuestaria']]['tiposRecursos']);
				if(!isset($proyectos_tipo_recurso[$tipo_recurso])){
					$proyectos_tipo_recurso[$tipo_recurso] = 1;
				}else{
					$proyectos_tipo_recurso[$tipo_recurso] += 1;
				}

				if(count($presupuestos[$proyecto['ClavePresupuestaria']]['fuentesFinanciamiento']) > 1){
					$proyectos_fuente_financiamiento['Mezcla de recursos'] += 1;
				}else{
					$fuente = str_replace(['á','é','í','ó','ú','Á','É','Í','Ó','Ú'],['a','e','i','o','u','A','E','I','O','U'],implode('', $presupuestos[$proyecto['ClavePresupuestaria']]['fuentesFinanciamiento']));
					if(isset($proyectos_fuente_financiamiento[$fuente])){
						$proyectos_fuente_financiamiento[$fuente] += 1;
					}else{
						$proyectos_fuente_financiamiento[$fuente] = 1;
					}
				}
				
				$sub_fuente = str_replace(['á','é','í','ó','ú','Á','É','Í','Ó','Ú'],['a','e','i','o','u','A','E','I','O','U'],implode(' / ', $presupuestos[$proyecto['ClavePresupuestaria']]['subFuentesFinanciamiento']));
				if(isset($proyectos_subfuente_financiamiento[$sub_fuente])){
					$proyectos_subfuente_financiamiento[$sub_fuente] += 1;
				}else{
					$proyectos_subfuente_financiamiento[$sub_fuente] = 1;
				}
			}else{
				$textrun = $section->addTextRun();
				$textrun->addText('Afectación presupuestaria: ', array('bold' => true));
				$textrun->addText('Información no encontrada en el EP01',array('color'=>'#FF0000','bold'=>true));
				//$section->addTextBreak();

				$textrun = $section->addTextRun();
				$textrun->addText('Fuente de financiamiento: ', array('bold' => true));
				$textrun->addText('Información no encontrada en el EP01',array('color'=>'#FF0000','bold'=>true));
				//$section->addTextBreak();

				$textrun = $section->addTextRun();
				$textrun->addText('Subfuente de financiamiento: ', array('bold' => true));
				$textrun->addText('Información no encontrada en el EP01',array('color'=>'#FF0000','bold'=>true));
				//$section->addTextBreak();

				$textrun = $section->addTextRun();
				$textrun->addText('Presupuesto autorizado: ', array('bold' => true));
				$textrun->addText('Información no encontrada en el EP01',array('color'=>'#FF0000','bold'=>true));
				$section->addTextBreak(1);
			}
		/***                <<<<<<<<<<<<<<<<<<<<  Información General  >>>>>>>>>>>>>>>>>>>                   ***/

			$section->addText(htmlspecialchars('RESULTADO DE INDICADORES'),$titulo);
			$section->addTextBreak(1);

			$table = $section->addTable('TablaInfo');

			$row = $table->addRow();
			$row->addCell(1251,$titulo_row_span)->addText('Nivel',$titulo_tabla,$centrado);
			$row->addCell(1552,$titulo_row_span)->addText('Descripción',$titulo_tabla,$centrado);
			$row->addCell(1251,$titulo_row_span)->addText('Cantidad Programada',$titulo_tabla,$centrado);
			$row->addCell(5004,$titulo_col_span_4)->addText('Avances trimestrales',$titulo_tabla,$centrado);
			$row->addCell(1251,$titulo_row_span)->addText('Cantidad alcanzada',$titulo_tabla,$centrado);
			$row->addCell(950,$titulo_row_span)->addText('Porcentaje alcanzado',$titulo_tabla,$centrado);

			$row = $table->addRow();
			$row->addCell(1251,$titulo_row_continue);
			$row->addCell(1552,$titulo_row_continue);
			$row->addCell(1251,$titulo_row_continue);
			$row->addCell(1251,$titulo_cell)->addText('1',$titulo_tabla,$centrado);
			$row->addCell(1251,$titulo_cell)->addText('2',$titulo_tabla,$centrado);
			$row->addCell(1251,$titulo_cell)->addText('3',$titulo_tabla,$centrado);
			$row->addCell(1251,$titulo_cell)->addText('4',$titulo_tabla,$centrado);
			$row->addCell(1251,$titulo_row_continue);
			$row->addCell(950,$titulo_row_continue);

			$con_grafica = false;
			$proyecto_jurisdicciones = [];
			
			$row_class = null;
			$row_counter = 0;
			foreach ($proyecto['componentes'] as $componente) {
				$row_counter++;
				if(($row_counter%2) == 0){
					$row_class = $cell_middle_2;
				}else{
					$row_class = $cell_middle_1;
				}
				$trimestres = [1 => 0.00 , 2 => 0.00 , 3 => 0.00 , 4 => 0.00];
				$total_avance = 0.00;
				foreach ($componente['metas_mes'] as $meta) {
					$trimestre = ceil($meta['mes'] / 3);
					$trimestres[$trimestre] += $meta['avance'];
					$total_avance += $meta['avance'];
					if($meta['claveJurisdiccion'] == 'OC'){ $meta['claveJurisdiccion'] = '01'; }
					if($meta['avance'] > 0 || $meta['meta'] > 0){
						if(!isset($proyecto_jurisdicciones[$meta['claveJurisdiccion']])){
							$proyecto_jurisdicciones[$meta['claveJurisdiccion']] = [
								'jurisdiccion' 	=> $meta['claveJurisdiccion'] . ' ' . $catalogos['jurisdicciones'][$meta['claveJurisdiccion']],
								'programado' 	=> 0.00,
								'avance' 		=> 0.00
							];
						}
						$proyecto_jurisdicciones[$meta['claveJurisdiccion']]['programado'] += $meta['meta'];
						$proyecto_jurisdicciones[$meta['claveJurisdiccion']]['avance'] += $meta['avance'];	
					}
				}

				$porcentaje_avance = round(($total_avance*100)/$componente['valorNumerador'],2);

				$row = $table->addRow();
				$row->addCell(1251,$titulo_cell)->addText('COMPONENTE',$titulo_tabla,$centrado);
				$row->addCell(1552,$row_class)->addText($componente['indicador'],$texto_tabla,$izquierda);
				$row->addCell(1251,$row_class)->addText(number_format($componente['valorNumerador'],2),$texto_tabla,$centrado);
				$row->addCell(1251,$row_class)->addText(number_format($trimestres[1],2),$texto_tabla,$centrado);
				$row->addCell(1251,$row_class)->addText(number_format($trimestres[2],2),$texto_tabla,$centrado);
				$row->addCell(1251,$row_class)->addText(number_format($trimestres[3],2),$texto_tabla,$centrado);
				$row->addCell(1251,$row_class)->addText(number_format($trimestres[4],2),$texto_tabla,$centrado);
				$row->addCell(1251,$row_class)->addText(number_format($total_avance,2),$texto_tabla,$centrado);
				$row->addCell(950,$row_class)->addText(number_format($porcentaje_avance,2).'%',$texto_tabla,$centrado);

				if($porcentaje_avance <= 110 && $porcentaje_avance >= 90){
					$cumplimiento_indicadores['Satisfactorio'] += 1;
				}elseif ($porcentaje_avance > 110) {
					$cumplimiento_indicadores['No Satisfactorio'] += 1;
					$cumplimiento_indicadores_no_satisfactorio['Alto avance'] += 1;
				}elseif ($porcentaje_avance < 90) {
					$cumplimiento_indicadores['No Satisfactorio'] += 1;
					$cumplimiento_indicadores_no_satisfactorio['Bajo avance'] += 1;
				}

				foreach ($componente['actividades'] as $actividad) {
					$row_counter++;
					if(($row_counter%2) == 0){
						$row_class = $cell_middle_2;
					}else{
						$row_class = $cell_middle_1;
					}

					$trimestres = [1 => 0.00 , 2 => 0.00 , 3 => 0.00 , 4 => 0.00];
					$total_avance = 0.00;
					foreach ($actividad['metas_mes'] as $meta) {
						$trimestre = ceil($meta['mes'] / 3);
						$trimestres[$trimestre] += $meta['avance'];
						$total_avance += $meta['avance'];
						if($meta['claveJurisdiccion'] == 'OC'){ $meta['claveJurisdiccion'] = '01'; }
						if($meta['avance'] > 0 || $meta['meta'] > 0){
							if(!isset($proyecto_jurisdicciones[$meta['claveJurisdiccion']])){
								$proyecto_jurisdicciones[$meta['claveJurisdiccion']] = [
									'jurisdiccion' 	=> $meta['claveJurisdiccion'] . ' ' . $catalogos['jurisdicciones'][$meta['claveJurisdiccion']],
									'programado' 	=> 0.00,
									'avance' 		=> 0.00
								];
							}
							$proyecto_jurisdicciones[$meta['claveJurisdiccion']]['programado'] += $meta['meta'];
							$proyecto_jurisdicciones[$meta['claveJurisdiccion']]['avance'] += $meta['avance'];
						}
					}

					$row = $table->addRow();
					$row->addCell(1251,$titulo_cell)->addText('ACTIVIDAD',$titulo_tabla,$centrado);
					$row->addCell(1552,$row_class)->addText($actividad['indicador'],$texto_tabla,$izquierda);
					$row->addCell(1251,$row_class)->addText(number_format($actividad['valorNumerador'],2),$texto_tabla,$centrado);
					$row->addCell(1251,$row_class)->addText(number_format($trimestres[1],2),$texto_tabla,$centrado);
					$row->addCell(1251,$row_class)->addText(number_format($trimestres[2],2),$texto_tabla,$centrado);
					$row->addCell(1251,$row_class)->addText(number_format($trimestres[3],2),$texto_tabla,$centrado);
					$row->addCell(1251,$row_class)->addText(number_format($trimestres[4],2),$texto_tabla,$centrado);
					$row->addCell(1251,$row_class)->addText(number_format($total_avance,2),$texto_tabla,$centrado);
					$row->addCell(950,$row_class)->addText(number_format(round(($total_avance*100)/$actividad['valorNumerador'],2),2).'%',$texto_tabla,$centrado);
				}
			}

			$section->addTextBreak(2);
			$section->addText(htmlspecialchars('AVANCE JURISDICCIONAL'),$titulo);
			$section->addTextBreak(1);

			$avance_comparar = false;
			$suma_porcentajes = 0.00;
			$jurisdicciones = [];
			$jurisdicciones_porcentajes = [];

			foreach ($proyecto_jurisdicciones as $indice => $jurisdiccion) {
				if($jurisdiccion['programado'] == 0){
					$porcentaje_avance = round(($jurisdiccion['avance']*100),2);
				}else{
					$porcentaje_avance = round(($jurisdiccion['avance']*100)/$jurisdiccion['programado'],2);
				}
				$suma_porcentajes += $porcentaje_avance;
				if($avance_comparar === false){
					$avance_comparar = $porcentaje_avance;
				}else if($avance_comparar != $porcentaje_avance){
					$con_grafica = true;
				}
				$jurisdicciones[$indice] = $jurisdiccion['jurisdiccion'];
				$jurisdicciones_porcentajes[$indice] = number_format($porcentaje_avance,2);
			}
			ksort($jurisdicciones);
			ksort($jurisdicciones_porcentajes);

			if(count($proyecto_jurisdicciones) == 10 && $con_grafica){
				$section->addText(htmlspecialchars('El proyecto se implementó en las 10 Jurisdicciones Sanitarias del Estado, observándose los siguientes resultados:'),$texto);
			}else if(count($proyecto_jurisdicciones) == 10 && !$con_grafica && $avance_comparar == 100.00){
				$section->addText(htmlspecialchars('El proyecto se implementó en todas Jurisdicciones Sanitarias del Estado, observándose el cumplimiento de las metas programadas.'),$texto);
			}else if(count($proyecto_jurisdicciones) == 10 && !$con_grafica && $avance_comparar != 100.00){
				$section->addText(htmlspecialchars('El proyecto se implementó en todas Jurisdicciones Sanitarias del Estado, observándose un avance del '.number_format($avance_comparar,2).'% en relación a las metas programadas.'),$texto);
			}else if(count($proyecto_jurisdicciones) < 10 && count($proyecto_jurisdicciones) > 1 && $con_grafica){
				$section->addText(htmlspecialchars('Las Jurisdicciones Sanitarias en las que se implementó el proyecto fueron: '.implode(', ',$jurisdicciones).'; observándose los siguientes resultados:'),$texto);
			}else if(count($proyecto_jurisdicciones) < 10 && count($proyecto_jurisdicciones) > 1 && !$con_grafica && $avance_comparar == 100.00){
				$section->addText(htmlspecialchars('El proyecto se implementó en las Jurisdicciones Sanitarias: '.implode(', ',$jurisdicciones).'; observándose el cumplimiento de las metas programadas.'),$texto);
			}else if(count($proyecto_jurisdicciones) < 10 && count($proyecto_jurisdicciones) > 1 && !$con_grafica && $avance_comparar != 100.00){
				$section->addText(htmlspecialchars('El proyecto se implementó en las Jurisdicciones Sanitarias: '.implode(', ',$jurisdicciones).'; observándose un avance del '.number_format($avance_comparar,2).'% en relación a las metas programadas.'),$texto);
			}else if(count($proyecto_jurisdicciones) == 1 && $avance_comparar == 100.00){
				$section->addText(htmlspecialchars('El proyecto se implementó en la Jurisdicción Sanitaria '.implode(', ',$jurisdicciones).', observándose el cumplimiento de las metas programadas.'),$texto);
				$con_grafica = false;
			}else{
				$section->addText(htmlspecialchars('El proyecto se implementó en la Jurisdicción Sanitaria '.implode(', ',$jurisdicciones).', observándose un avance del '.number_format($avance_comparar,2).'% en relación a las metas programadas.'),$texto);
				$con_grafica = false;
			}

			$section->addTextBreak();

			if($con_grafica){

				$graph = new \PHPGraphLib(650,200);
				
				$graph->addData($jurisdicciones_porcentajes);
				$graph->setBars(false);
				$graph->setLine(true);
				$graph->setGrid(false);
				$graph->setDataPoints(true);
				$graph->setDataPointColor('navy');
				$graph->setDataValues(true);
				$graph->setDataValueColor('navy');
				$graph->setGoalLine(110,'red');
				$graph->setGoalLine(100,'green');
				$graph->setGoalLine(90,'red');
				$graph->setXValuesHorizontal(true);

				ob_start();
					$graph->createGraph();
					$image_data = ob_get_contents();
				ob_end_clean();

				$chart_file_path = storage_path().'/archivoscsv/chart-avance-jurisdiccion-'.$proyecto['id'].'.png';

				file_put_contents($chart_file_path, $image_data);

				$this->lista_imagenes[] = $chart_file_path;
				
				$section->addText(htmlspecialchars('AVANCE JURISDICCIONAL (%)'),$titulo,$centrado);

				$table = $section->addTable();
				$row = $table->addRow();
				$row->addCell(11259)->addImage($chart_file_path,array('align'=>'center'));
			    //$section->addTextBreak(1);
			}
			$section->addTextBreak(1);
			$section->addText(htmlspecialchars('AVANCE FÍSICO-FINANCIERO'),$titulo);
			$section->addTextBreak(1);

			$promedio_avance = round($suma_porcentajes/count($jurisdicciones_porcentajes),2);

			$textrun = $section->addTextRun();
			$textrun->addText('Promedio de avance físico alcanzado: ', array('bold' => true));
			$textrun->addText(number_format($promedio_avance,2) . ' %');
			//$section->addTextBreak();
			$avance_logrado = 0;

			if(!isset($cumplimiento_unidad_responsable[$proyecto['unidadResponsable']])){
				$cumplimiento_unidad_responsable[$proyecto['unidadResponsable']] = [
					'Logrado' => 0,
					'Total' => 0,
					'UnidadResponsable' => $proyecto['unidadResponsableDescripcion']
				];
			}

			if(isset($proyecto['analisis_funcional'][0])){
				$analisis_funcional = $proyecto['analisis_funcional'][0]['justificacionGlobal'];
			}else{
				$analisis_funcional = 'Información no encontrada en la base de datos';
			}

			$cumplimiento_unidad_responsable[$proyecto['unidadResponsable']]['Total'] += 1;
			if($promedio_avance <= 110 && $promedio_avance >= 90){
				$cumplimiento_proyecto['Satisfactorio'] += 1;
				$cumplimiento_unidad_responsable[$proyecto['unidadResponsable']]['Logrado'] += 1;
			}elseif ($promedio_avance > 110) {
				$cumplimiento_proyecto['No Satisfactorio'] += 1;
				$cumplimiento_proyecto_no_satisfactorio['Alto avance'] += 1;
				$causas_alto_avance[] = $analisis_funcional;
			}else{
				$cumplimiento_proyecto['No Satisfactorio'] += 1;
				$cumplimiento_proyecto_no_satisfactorio['Bajo avance'] += 1;
				if ($promedio_avance == 0) {
					$promedio_avance = '0.00';
					$causas_sin_avance[] = $analisis_funcional;
				}else{
					$causas_bajo_avance[] = $analisis_funcional;
				}
			}
			
			if(isset($presupuestos[$proyecto['ClavePresupuestaria']])){
				$textrun = $section->addTextRun();
				$textrun->addText('Presupuesto ejercido: ', array('bold' => true));
				$cantidad_letras = ucfirst(mb_strtolower(ltrim(Util::transformarCantidadLetras($presupuestos[$proyecto['ClavePresupuestaria']]['presupuestoEjercidoModificado'])),'UTF-8'));
				$textrun->addText('$ ' . number_format($presupuestos[$proyecto['ClavePresupuestaria']]['presupuestoEjercidoModificado'],2) . ' ('.$cantidad_letras.' M.N.)');

				if($presupuestos[$proyecto['ClavePresupuestaria']]['presupuestoModificado']>0){
					$avance_logrado = round(($presupuestos[$proyecto['ClavePresupuestaria']]['presupuestoEjercidoModificado']*100)/$presupuestos[$proyecto['ClavePresupuestaria']]['presupuestoModificado'],2);
				}else{
					$avance_logrado = 0;
				}
				$textrun = $section->addTextRun();
				$textrun->addText('Avance financiero logrado: ', array('bold' => true));
				$textrun->addText(number_format($avance_logrado,2) . ' %');
			}else{
				$textrun = $section->addTextRun();
				$textrun->addText('Presupuesto ejercido: ', array('bold' => true));
				$textrun->addText('Información no encontrada en el EP01',array('color'=>'#FF0000','bold'=>true));

				$textrun = $section->addTextRun();
				$textrun->addText('Avance financiero logrado: ', array('bold' => true));
				$textrun->addText('Información no encontrada en el EP01',array('color'=>'#FF0000','bold'=>true));
			}
			
			$section->addTextBreak();

			$graph = new \PHPGraphLib(650,150);
			if($avance_logrado == 0){ $avance_logrado = '0.00';}
			$data = array("Avance fisico" => $promedio_avance, "Avance financiero" => $avance_logrado);
			$graph->addData($data);
			$graph->setBarColor('navy');
			$graph->setGrid(false);
			$graph->setDataValues(true);
			$graph->setDataValueColor('navy');
			$graph->setDataFormat('percent');
			$graph->setXValuesHorizontal(true);

			ob_start();
				$graph->createGraph();
				$image_data = ob_get_contents();
			ob_end_clean();

			$chart_file_path = storage_path().'/archivoscsv/chart-fisico-finan-'.$proyecto['id'].'.png';

			file_put_contents($chart_file_path, $image_data);

			$this->lista_imagenes[] = $chart_file_path;
			
			$section->addText(htmlspecialchars('AVANCE FÍSICO - FINANCIERO'),$titulo,$centrado);

			$table = $section->addTable();
			$row = $table->addRow();
			$row->addCell(11259)->addImage($chart_file_path,array('align'=>'center'));
			
			$section->addTextBreak(1);
			
			$section->addText(htmlspecialchars('OBSERVACIONES:'),$titulo);
			if(isset($proyecto['analisis_funcional'][0])){
				$section->addText(htmlspecialchars($proyecto['analisis_funcional'][0]['justificacionGlobal']));
			}else{
				$section->addText('Información no encontrada en la base de datos',array('bold'=>true),array('color'=>'#FF0000'));
			}

			$section->addTextBreak(1);
			$total_meses_planes = count($proyecto['conteo_planes_mejora']);
			if($total_meses_planes > 0){
				$trimestres = '';
				foreach ($proyecto['conteo_planes_mejora'] as $index => $plan_mejora) {
					$trimestre = ceil($plan_mejora['mes'] / 3);
					switch ($trimestre) {
						case 1: $trimestre = 'primer'; break;
						case 2: $trimestre = 'segundo'; break;
						case 3: $trimestre = 'tercer'; break;
						case 4: $trimestre = 'cuarto'; break;
						default: $trimestre = 'error'; break;
					}
					if($index > 0 && ($index+1) < $total_meses_planes){
						$trimestres .= ', ' . $trimestre;
					}else if($total_meses_planes > 1 && ($index+1) == $total_meses_planes){
						$trimestres .= ' y ' . $trimestre;
					}else{
						$trimestres .= $trimestre;
					}
				}
				$section->addText(htmlspecialchars('Para lograr los resultados obtenidos, en este proyecto se implementó un Plan de Acción de Mejora, durante el '.$trimestres . ' trimestre.'));
			}
			
			$section->addPageBreak();
		}

		$texto_proyectos = '';
		$proyectos_unidades_resumen = [];
		foreach ($proyectos_unidad_responsable as $unidad => $no_proyectos) {
			$texto_proyectos .= $unidad . ' presentó ' . $no_proyectos . ', ';
			$nueva_unidad = str_replace(['Dirección','á','é','í','ó','ú'],['D.','a','e','i','o','u'],$unidad);
			$proyectos_unidades_resumen[$nueva_unidad] = $no_proyectos;
		}

		$section_resumen->addText(htmlspecialchars('ANÁLISIS GENERAL'),$titulo,$centrado);
		$section_resumen->addTextBreak(1);
		$section_resumen->addText(htmlspecialchars('Al mes de mes de año, encontramos que se han gestionado '.count($datos).' proyectos que fueron autorizados y liberados por la Secretaría de Hacienda. De los proyectos autorizados, '.$texto_proyectos),$texto,$justificado);
		$section_resumen->addTextBreak(1);

		$graph = new \PHPGraphLibPie(800, 400);
		$graph->addData($proyectos_unidades_resumen);
		$graph->setLabelTextColor('50,50,50');
		$graph->setLegendTextColor('50,50,50');
		ob_start();
			$graph->createGraph();
			$image_data = ob_get_contents();
		ob_end_clean();
		$chart_file_path = storage_path().'/archivoscsv/chart-analisis-general-proyectos-UR.png';
		file_put_contents($chart_file_path, $image_data);
		$this->lista_imagenes[] = $chart_file_path;
		$section_resumen->addText(htmlspecialchars('PROYECTOS POR ÁREA RESPONSABLE'),$titulo,$centrado);
		$table = $section_resumen->addTable();
		$row = $table->addRow();
		$row->addCell(11259)->addImage($chart_file_path,array('align'=>'center','width'=>'650'));
		$section_resumen->addTextBreak(1);

		$section_resumen->addText(htmlspecialchars('Con la finalidad de mejorar los procesos y servicios otorgados a la población, se tramitaron y autorizaron '.$proyectos_tipo_proyecto['INSTITUCIONAL'].' proyectos de tipo institucional y '.$proyectos_tipo_proyecto['INVERSION'].' proyectos de inversión, que además permitieron fortalecer la infraestructura del instituto.'),$texto,$justificado);
		$section_resumen->addTextBreak(1);

		$graph = new \PHPGraphLibPie(400, 100);
		$graph->addData($proyectos_tipo_proyecto);
		$graph->setLabelTextColor('50,50,50');
		$graph->setLegendTextColor('50,50,50');
		ob_start();
			$graph->createGraph();
			$image_data = ob_get_contents();
		ob_end_clean();
		$chart_file_path = storage_path().'/archivoscsv/chart-analisis-general-proyectos-TIPOS.png';
		file_put_contents($chart_file_path, $image_data);
		$this->lista_imagenes[] = $chart_file_path;
		$section_resumen->addText(htmlspecialchars('TIPO DE PROYECTO'),$titulo,$centrado);
		$table = $section_resumen->addTable();
		$row = $table->addRow();
		$row->addCell(11259)->addImage($chart_file_path,array('align'=>'center'));
		$section_resumen->addTextBreak(1);

		$texto_proyectos = '';
		$proyectos_tipo_recurso_resumen = [];
		foreach ($proyectos_tipo_recurso as $tipo_recurso => $total_proyectos) {
			$texto_proyectos .= $total_proyectos . ' proyectos fueron financiados con ' . $tipo_recurso . ', ';
			$nuevo_tipo_recurso = str_replace('RECURSOS ','',$tipo_recurso);
			$proyectos_tipo_recurso_resumen[$nuevo_tipo_recurso] = $total_proyectos;
		}
		$section_resumen->addText(htmlspecialchars('De los '.count($datos).' proyectos, '.$texto_proyectos),$texto,$justificado);
		$section_resumen->addTextBreak(1);

		$graph = new \PHPGraphLibPie(400, 100);
		$graph->addData($proyectos_tipo_recurso_resumen);
		$graph->setLabelTextColor('50,50,50');
		$graph->setLegendTextColor('50,50,50');
		ob_start();
			$graph->createGraph();
			$image_data = ob_get_contents();
		ob_end_clean();
		$chart_file_path = storage_path().'/archivoscsv/chart-analisis-general-proyectos-TIPORECURSO.png';
		file_put_contents($chart_file_path, $image_data);
		$this->lista_imagenes[] = $chart_file_path;
		$section_resumen->addText(htmlspecialchars('TIPO DE RECURSO'),$titulo,$centrado);
		$table = $section_resumen->addTable();
		$row = $table->addRow();
		$row->addCell(11259)->addImage($chart_file_path,array('align'=>'center'));
		$section_resumen->addTextBreak(1);

		$texto_proyectos = '';
		foreach ($proyectos_fuente_financiamiento as $fuente => $total_proyectos) {
			$texto_proyectos .= $total_proyectos . ' proyectos fueron financiados através de ' . $fuente . ', ';
		}
		$section_resumen->addText(htmlspecialchars('En este sentido '.$texto_proyectos),$texto,$justificado);
		$section_resumen->addTextBreak(1);

		$graph = new \PHPGraphLib(600,600);
		$graph->addData($proyectos_fuente_financiamiento);
		$graph->setGradient('red', 'maroon');
		$graph->setupXAxis(50);
		$graph->setDataValues(true);
		$graph->setDataValueColor('navy');
		ob_start();
			$graph->createGraph();
			$image_data = ob_get_contents();
		ob_end_clean();
		$chart_file_path = storage_path().'/archivoscsv/chart-analisis-general-proyectos-FUENTESFINAN.png';
		file_put_contents($chart_file_path, $image_data);
		$this->lista_imagenes[] = $chart_file_path;
		$section_resumen->addText(htmlspecialchars('FUENTE DE FINANCIAMIENTO'),$titulo,$centrado);
		$table = $section_resumen->addTable();
		$row = $table->addRow();
		$row->addCell(11259)->addImage($chart_file_path,array('align'=>'center'));
		$section_resumen->addTextBreak(1);

		$section_resumen->addText(htmlspecialchars('Detallado con el importa autorizado, a continuación enlistamos las fuentes y montos autorizados:'),$texto,$justificado);
		$section_resumen->addTextBreak(1);

		$table = $section_resumen->addTable('TablaInfo');
		$row = $table->addRow();
		$row->addCell(8000,$titulo_tabla_style)->addText('Fuente de Financiamiento',$titulo_tabla,$centrado);
		$row->addCell(2309,$titulo_tabla_style)->addText('Monto autorizado',$titulo_tabla,$centrado);
		$row->addCell(950,$titulo_tabla_style)->addText('Porcentaje alcanzado',$titulo_tabla,$centrado);

		$line_alt = 0;
		foreach ($presupuesto_fuente_financiamiento['fuentes'] as $clave_fuente => $datos) {
			$line_alt++;
			if(($line_alt%2) == 0){
				$row_class = $cell_middle_2;
			}else{
				$row_class = $cell_middle_1;
			}
			$porcentaje_presupuesto = ($datos['monto']*100)/$presupuesto_fuente_financiamiento['total'];
			$row = $table->addRow();
			$row->addCell(8000,$row_class)->addText(htmlspecialchars($datos['fuenteFinanciamiento']),$texto_tabla,$justificado);
			$row->addCell(2309,$row_class)->addText(htmlspecialchars(number_format($datos['monto'],2)),$texto_tabla,$centrado);
			$row->addCell(950,$row_class)->addText(htmlspecialchars(number_format($porcentaje_presupuesto,2).' %'),$texto_tabla,$centrado);
		}

		$row = $table->addRow();
		$row->addCell(8000,$titulo_tabla_style)->addText('Total',$titulo_tabla,$izquierda);
		$row->addCell(2309,$titulo_tabla_style)->addText(htmlspecialchars(number_format($presupuesto_fuente_financiamiento['total'],2)),$titulo_tabla,$centrado);
		$row->addCell(950,$titulo_tabla_style)->addText('100.00 %',$titulo_tabla,$centrado);
		$section_resumen->addTextBreak(1);

		//proyectos_subfuente_financiamiento
		$texto_proyectos = '';
		foreach ($proyectos_subfuente_financiamiento as $fuente => $total_proyectos) {
			$texto_proyectos .= $total_proyectos . ' proyectos fueron tramitados mediante ' . $fuente . ', ';
		}
		$section_resumen->addText(htmlspecialchars('De acuerdo a la subfuente de financiamiento, '.$texto_proyectos),$texto,$justificado);
		$section_resumen->addTextBreak(1);

		$graph = new \PHPGraphLib(600,800);
		$graph->addData($proyectos_subfuente_financiamiento);
		$graph->setGradient('red', 'maroon');
		$graph->setupXAxis(50);
		$graph->setDataValues(true);
		$graph->setDataValueColor('navy');
		ob_start();
			$graph->createGraph();
			$image_data = ob_get_contents();
		ob_end_clean();
		$chart_file_path = storage_path().'/archivoscsv/chart-analisis-general-proyectos-SUBFUENTESFINAN.png';
		file_put_contents($chart_file_path, $image_data);
		$this->lista_imagenes[] = $chart_file_path;
		$section_resumen->addText(htmlspecialchars('SUBFUENTE DE FINANCIAMIENTO'),$titulo,$centrado);
		$table = $section_resumen->addTable();
		$row = $table->addRow();
		$row->addCell(11259)->addImage($chart_file_path,array('align'=>'center'));
		$section_resumen->addTextBreak(1);

		$section_resumen->addText(htmlspecialchars('De acuerdo al artículo 58 de las Normas Presupuestarias para la Administración Pública del Estado de Chiapas 2014, los proyectos se elaboraron con base al Presupuesto basado en Resultados (PbR) en el cual, se incorporaron elementos como son: objetivos, indicadores y beneficiarios, entre otros; lo cual, permite impulsar acciones de monitoreo para efectos de verificar los resultados obtenidos de la aplicación de los recursos públicos y el grado de cumplimiento de las metas. De esta manera, se podrán verificar si las actividades a realizar generaron los productos y servicios que la sociedad demanda y si éstas, impactan en el mejoramiento de su entorno, bienestar individual y colectivo de la gente.'),$texto,$justificado);
		$section_resumen->addTextBreak(1);

		$total_indicadores = $cumplimiento_indicadores['Satisfactorio'] + $cumplimiento_indicadores['No Satisfactorio'];
		$section_resumen->addText(htmlspecialchars('De los '.$total_indicadores.' indicadores programados, en '.$cumplimiento_indicadores['Satisfactorio'].' se cumplió satisfactoriamente las metas establecidas, ------; '.$cumplimiento_indicadores_no_satisfactorio['Bajo avance'].' quedaron por debajo de lo esperado y '.$cumplimiento_indicadores_no_satisfactorio['Alto avance'].' rebasaron lo proyectado.'),$texto,$justificado);
		$section_resumen->addTextBreak(1);

		$graph = new \PHPGraphLibPie(300, 100);
		$graph->addData($cumplimiento_indicadores);
		$graph->setLabelTextColor('50,50,50');
		$graph->setLegendTextColor('50,50,50');
		ob_start();
			$graph->createGraph();
			$image_data = ob_get_contents();
		ob_end_clean();
		$chart_file_path = storage_path().'/archivoscsv/chart-analisis-general-proyectos-CUMPIND1.png';
		file_put_contents($chart_file_path, $image_data);
		$this->lista_imagenes[] = $chart_file_path;

		$graph = new \PHPGraphLibPie(300, 100);
		$graph->addData($cumplimiento_indicadores_no_satisfactorio);
		$graph->setLabelTextColor('50,50,50');
		$graph->setLegendTextColor('50,50,50');
		ob_start();
			$graph->createGraph();
			$image_data = ob_get_contents();
		ob_end_clean();
		$chart_file_path2 = storage_path().'/archivoscsv/chart-analisis-general-proyectos-CUMPIND2.png';
		file_put_contents($chart_file_path2, $image_data);
		$this->lista_imagenes[] = $chart_file_path2;

		$section_resumen->addText(htmlspecialchars('CUMPLIMIENTO POR INDICADORES'),$titulo,$centrado);
		$table = $section_resumen->addTable();
		$row = $table->addRow();
		$row->addCell(5634)->addImage($chart_file_path,array('align'=>'center'));
		$row->addCell(5625)->addImage($chart_file_path2,array('align'=>'center'));
		$section_resumen->addTextBreak(1);

		$cumplimiento_area = [];
		$texto_proyectos = '';
		foreach ($cumplimiento_unidad_responsable as $clave => $value) {
			$porcentaje_area = round(($value['Logrado']*100)/$value['Total'],2);
			$cumplimiento_area[$catalogos['unidades_responsables'][$clave]] = $porcentaje_area;
			$texto_proyectos .= $value['UnidadResponsable'] . ' alcanzó el ' . number_format($porcentaje_area,2) . '% de efectividad, ';
		}

		$section_resumen->addText(htmlspecialchars('En cuanto al cumplimiento por área Líder del Proyecto, tenemos que, '.$texto_proyectos.' .'),$texto,$justificado);
		$section_resumen->addTextBreak(1);

		$graph = new \PHPGraphLib(650,200);
		$graph->addData($cumplimiento_area);
		$graph->setBars(false);
		$graph->setLine(true);
		$graph->setDataPoints(true);
		$graph->setDataPointColor('navy');
		$graph->setDataValues(true);
		$graph->setDataValueColor('blue');
		$graph->setDataFormat('percent');
		$graph->setXValuesHorizontal(true);
		ob_start();
			$graph->createGraph();
			$image_data = ob_get_contents();
		ob_end_clean();
		$chart_file_path = storage_path().'/archivoscsv/chart-analisis-general-proyectos-CUMPAREA.png';
		file_put_contents($chart_file_path, $image_data);
		$this->lista_imagenes[] = $chart_file_path;
		$section_resumen->addText(htmlspecialchars('CUMPLIMIENTO POR DIRECCIÓN'),$titulo,$centrado);
		$table = $section_resumen->addTable();
		$row = $table->addRow();
		$row->addCell(11259)->addImage($chart_file_path,array('align'=>'center'));
		$section_resumen->addTextBreak(1);

		$total_proyectos = $cumplimiento_proyecto['Satisfactorio'] + $cumplimiento_proyecto['No Satisfactorio'];
		$section_resumen->addText(htmlspecialchars('Así tenemos que, de los '.$total_proyectos.' proyectos, '.$cumplimiento_proyecto['Satisfactorio'].' lograron un avance satisfactorio,sin embargo, '.$cumplimiento_proyecto_no_satisfactorio['Bajo avance'].' tuvieron un bajo avance y '.$cumplimiento_proyecto_no_satisfactorio['Alto avance'].' rebasaron lo programado.'),$texto,$justificado);
		$section_resumen->addTextBreak(1);

		$graph = new \PHPGraphLibPie(300, 100);
		$graph->addData($cumplimiento_proyecto);
		$graph->setLabelTextColor('50,50,50');
		$graph->setLegendTextColor('50,50,50');
		ob_start();
			$graph->createGraph();
			$image_data = ob_get_contents();
		ob_end_clean();
		$chart_file_path = storage_path().'/archivoscsv/chart-analisis-general-proyectos-CUMPPROY1.png';
		file_put_contents($chart_file_path, $image_data);
		$this->lista_imagenes[] = $chart_file_path;

		$graph = new \PHPGraphLibPie(300, 100);
		$graph->addData($cumplimiento_proyecto_no_satisfactorio);
		$graph->setLabelTextColor('50,50,50');
		$graph->setLegendTextColor('50,50,50');
		ob_start();
			$graph->createGraph();
			$image_data = ob_get_contents();
		ob_end_clean();
		$chart_file_path2 = storage_path().'/archivoscsv/chart-analisis-general-proyectos-CUMPPROY2.png';
		file_put_contents($chart_file_path2, $image_data);
		$this->lista_imagenes[] = $chart_file_path2;

		$section_resumen->addText(htmlspecialchars('CUMPLIMIENTO POR PROYECTO'),$titulo,$centrado);
		$table = $section_resumen->addTable();
		$row = $table->addRow();
		$row->addCell(5634)->addImage($chart_file_path,array('align'=>'center'));
		$row->addCell(5625)->addImage($chart_file_path2,array('align'=>'center'));
		$section_resumen->addTextBreak(1);

		$section_resumen->addText(htmlspecialchars('Identificándose como las principales causas del incumplimiento de los proyectos, las siguientes:'),$texto,$justificado);
		$section_resumen->addTextBreak(1);

		$table = $section_resumen->addTable('TablaInfo');
		$row = $table->addRow();
		$row->addCell(10000,$titulo_tabla_style)->addText('CAUSAS DE LA FALTA DE AVANCE',$titulo_tabla,$centrado);
		$row->addCell(1259,$titulo_tabla_style)->addText('NO. PROYECTOS',$titulo_tabla,$centrado);
		$line_alt = 0;
		foreach ($causas_sin_avance as $causa) {
			$line_alt++;
			if(($line_alt%2) == 0){ $row_class = $cell_middle_2; }
			else{ $row_class = $cell_middle_1; }
			$row = $table->addRow();
			$row->addCell(10000,$row_class)->addText(htmlspecialchars($causa),$texto_tabla,$justificado);
			$row->addCell(1259,$row_class)->addText(htmlspecialchars(1),$texto_tabla,$centrado);
		}
		$section_resumen->addTextBreak(1);

		$table = $section_resumen->addTable('TablaInfo');
		$row = $table->addRow();
		$row->addCell(10000,$titulo_tabla_style)->addText('CAUSAS DEL BAJO AVANCE',$titulo_tabla,$centrado);
		$row->addCell(1259,$titulo_tabla_style)->addText('NO. PROYECTOS',$titulo_tabla,$centrado);
		$line_alt = 0;
		foreach ($causas_bajo_avance as $causa) {
			$line_alt++;
			if(($line_alt%2) == 0){ $row_class = $cell_middle_2; }
			else{ $row_class = $cell_middle_1; }
			$row = $table->addRow();
			$row->addCell(10000,$row_class)->addText(htmlspecialchars($causa),$texto_tabla,$justificado);
			$row->addCell(1259,$row_class)->addText(htmlspecialchars(1),$texto_tabla,$centrado);
		}
		$section_resumen->addTextBreak(1);

		$table = $section_resumen->addTable('TablaInfo');
		$row = $table->addRow();
		$row->addCell(10000,$titulo_tabla_style)->addText('CAUSAS DEL ALTO AVANCE',$titulo_tabla,$centrado);
		$row->addCell(1259,$titulo_tabla_style)->addText('NO. PROYECTOS',$titulo_tabla,$centrado);
		$line_alt = 0;
		foreach ($causas_alto_avance as $causa) {
			$line_alt++;
			if(($line_alt%2) == 0){ $row_class = $cell_middle_2; }
			else{ $row_class = $cell_middle_1; }
			$row = $table->addRow();
			$row->addCell(10000,$row_class)->addText(htmlspecialchars($causa),$texto_tabla,$justificado);
			$row->addCell(1259,$row_class)->addText(htmlspecialchars(1),$texto_tabla,$centrado);
		}
		$section_resumen->addTextBreak(1);

		header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
	    header("Content-Description: File Transfer");
		header('Content-Disposition: attachment; filename="EvaluacionProyectos.docx"');
	    
	    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord,'Word2007');
	    $objWriter->save('php://output');

	    foreach ($this->lista_imagenes as $imagen) { unlink($imagen); }
	}
}
?>