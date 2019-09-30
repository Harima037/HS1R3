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
use BaseController, Input, Response, DB, Sentry, View;
use Excel, EvaluacionAnalisisFuncional, SysConfiguracionVariable, Proyecto, SysGrupoModulo;

class ReporteCuentaPublicaController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		if(!Sentry::hasAccess('REVISION.CUENTPUB.R')){
			$datos['usuario'] = Sentry::getUser();
			$datos['sys_sistemas'] = SysGrupoModulo::all();
			return Response::view('errors.403', array(
				'usuario'=>$datos['usuario'],
				'sys_activo'=>null,
				'sys_sistemas'=>$datos['sys_sistemas'],
				'sys_mod_activo'=>null), 403
			);
		}

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
		$parametros = Input::all();
		if(isset($parametros['mes'])){
			$mes = $parametros['mes'];
		}else{
			$mes = Util::obtenerMesActual();
			if($mes == 0){ $mes = date('n') - 1; }
		}
		
		if(isset($parametros['ejercicio'])){
			$ejercicio = $parametros['ejercicio'];
		}else{
			$ejercicio = date('Y');
		}

		$trimestres = array(1=>'PRIMER',2=>'SEGUNDO',3=>'TERCER',4=>'CUARTO');
		$trimestre = Util::obtenerTrimestre($mes);

		$titulo = array('bold' => true);
		$titulo_tabla = array('bold' => true, 'size'=>10);
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
	    $sectionStyle = $section->getStyle();
		$sectionStyle->setMarginLeft(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(3));
		$sectionStyle->setMarginRight(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(2.19));
		$sectionStyle->setMarginTop(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(4.33));
		$sectionStyle->setMarginBottom(\PhpOffice\PhpWord\Shared\Converter::cmToTwip(3));


	    $variables = SysConfiguracionVariable::obtenerVariables(array('clave-institucional','mision','vision'))->lists('valor','variable');

		$header = $section->addHeader();
		
		$table = $header->addTable('TablaEncabezado');
		$row = $table->addRow();
		//$row->addCell(3000)->addImage('img/LogoFederal.png');
		$row->addCell(3000)->addImage('img/LogoInstitucional.png',array('width' => 125,'align'=>'left'));
		$cell = $row->addCell(8128);
		$cell->addText(htmlspecialchars('GOBIERNO CONSTITUCIONAL DEL ESTADO DE CHIAPAS'),$titulo,$centrado);
		$cell->addTextBreak(0);
		$cell->addText(htmlspecialchars('INSTITUTO DE SALUD'),$titulo,$centrado);
		$cell->addTextBreak(0);
		$cell->addText(htmlspecialchars('ANÁLISIS FUNCIONAL AL '.$trimestres[$trimestre].' TRIMESTRE DEL '.$ejercicio),$titulo,$centrado);
		//$row->addCell(3000)->addImage('img/LogoInstitucional.png');
		//$row->addCell(3000)->addImage('img/LogoFederal.png',array('width' => 125,'align'=>'right'));
		
		$table = $header->addTable('TablaClave');
		$row = $table->addRow();
		$row->addCell(2250)->addText(htmlspecialchars($variables['clave-institucional']));

		$header->addTextBreak();

		$footer = $section->addFooter();
		$footer->addPreserveText(htmlspecialchars('Página {PAGE} de {NUMPAGES}.'), null,array('align' => 'right'));

		$section->addText(htmlspecialchars('ÍNDICE'),$titulo,$centrado);
		$section->addTextBreak(2);
		$section->addTOC();
		$section->addPageBreak();

		$section->addTextBreak(2);
		$section->addTitle(htmlspecialchars('MISIÓN'),1);
		$section->addTextBreak(1);
		$section->addText(htmlspecialchars($variables['mision']),$texto,$justificado);
		$section->addTextBreak(3);

		$section->addTitle(htmlspecialchars('VISIÓN'),1);
		$section->addTextBreak(1);
		$section->addText(htmlspecialchars($variables['vision']),$texto,$justificado);
		//$section->addPageBreak();

		$rows = Proyecto::reporteCuentaPublica($mes,$ejercicio);

		$rows = $rows->with(array(
		'componentesMetasMes'=>function($query){
			$query->select('componenteMetasMes.id','componenteMetasMes.idProyecto',DB::raw('min(componenteMetasMes.mes) AS mes'))
				->whereNull('componenteMetasMes.borradoAl')
				->groupBy('componenteMetasMes.idProyecto');
		},
		'actividadesMetasMes'=>function($query){
			$query->select('actividadMetasMes.id','actividadMetasMes.idProyecto',DB::raw('min(actividadMetasMes.mes) AS mes'))
				->whereNull('actividadMetasMes.borradoAl')
				->groupBy('actividadMetasMes.idProyecto');
		}
		))->get();
		//return Response::json($rows,200);
		//var_dump($rows->toArray());die;
		//$queries = DB::getQueryLog();
		//var_dump(end($queries));die;
		
		$subfuncion_anterior = '';
		$clasificacion_anterior = 0;
		$politica_programa_anterior = '';
		foreach ($rows as $elemento) {
			if($subfuncion_anterior != $elemento->subFuncionClave){
				$section->addPageBreak();
				$section->addTitle(htmlspecialchars('FUNCIÓN: ' . $elemento->funcionGasto),1);
				$section->addTextBreak();
				$section->addTitle(htmlspecialchars('SUBFUNCIÓN: ' . $elemento->subFuncionGasto),2);
				$section->addTextBreak();
				$subfuncion_anterior = $elemento->subFuncionClave;
				$clasificacion_anterior = 0;
				$politica_programa_anterior = '';
			}

			if($clasificacion_anterior != $elemento->idClasificacionProyecto){
				$section->addText(htmlspecialchars('OBJETIVOS Y PRINCIPALES COMENTARIOS DE LOS PROYECTOS INMERSOS EN ESTA SUBFUNCIÓN'),$titulo);
				$section->addTextBreak();
				if($elemento->idClasificacionProyecto == 1){
					$section->addTitle(htmlspecialchars('PROYECTOS INSTITUCIONALES:'),3);
				}else{
					$section->addTitle(htmlspecialchars('PROYECTOS DE INVERSIÓN:'),3);
				}
				$section->addTextBreak();
				$clasificacion_anterior = $elemento->idClasificacionProyecto;
				$politica_programa_anterior = '';
			}

			$politica_programa_actual = $elemento->politicaPublicaClave.'-'.$elemento->programaPresupuestario;

			if($politica_programa_anterior != $politica_programa_actual){
				$table = $section->addTable('TablaInfo');

				$estilo_tabla = array(
					//'bgcolor'=>'#621132'
					'bgcolor'=>'#AFAFAF'
				);

				$row = $table->addRow();
				$row->addCell(3000,$estilo_tabla)->addText('EJE',$titulo_tabla,$centrado);
				$row->addCell(3000,$estilo_tabla)->addText('TEMA',$titulo_tabla,$centrado);
				$row->addCell(3065,$estilo_tabla)->addText('POLÍTICA PÚBLICA',$titulo_tabla,$centrado);
				$row->addCell(5060,$estilo_tabla)->addText('PROGRAMA PRESUPUESTARIO',$titulo_tabla,$centrado);

				$row = $table->addRow();
				$row->addCell(2500)->addText($elemento->ejeDescripcion);
				$row->addCell(2500)->addText($elemento->temaDescripcion);
				$row->addCell(4065)->addText($elemento->politicaPublicaDescripcion);
				$row->addCell(5060)->addText($elemento->programaPresupuestarioDescipcion);
				$section->addTextBreak();
				$politica_programa_anterior = $politica_programa_actual;
			}

			$section->addTitle(htmlspecialchars('Proyecto: '.$elemento->nombreTecnico.'. ('.$elemento->unidadResponsableDescipcion.').'),4);
			$section->addTextBreak();

			if($elemento->totalMetas > 0){
				if($elemento->cuentaPublica){
					$section->addText(htmlspecialchars(trim($elemento->cuentaPublica)),$texto,$justificado);
				}else{
					$section->addText(htmlspecialchars('No presentaron avances.'),$texto,$justificado);
				}
			}else{
				if($elemento->cuentaPublica){
					$section->addText(htmlspecialchars(trim($elemento->cuentaPublica)),$texto,$justificado);
				}else{
					$mes_programado = 0;
					if(isset($elemento->componentesMetasMes[0])){
						$mes_componente = $elemento->componentesMetasMes[0]->mes;
					}else{
						$mes_componente = 99;
					}
					if(isset($elemento->actividadesMetasMes[0])){
						$mes_actividad = $elemento->actividadesMetasMes[0]->mes;
					}else{
						$mes_actividad = 99;
					}
					if($mes_componente < $mes_actividad){
						$mes_programado = $mes_componente;
					}else{
						$mes_programado = $mes_actividad;
					}

					$trimestre_programado = Util::obtenerTrimestre($mes_programado);

					if($trimestre_programado == 1 || $trimestre_programado == 3){
						$trimestre_programado = $trimestre_programado.'er';
					}elseif($trimestre_programado == 2){
						$trimestre_programado = '2do';
					}elseif($trimestre_programado == 4){
						$trimestre_programado = '4to';
					}
					$section->addText(htmlspecialchars('Las acciones de este proyecto se encuentran programadas al '.$trimestre_programado.' trimestre.'),$texto,$justificado);
				}
			}
			$section->addTextBreak();
		}

		header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
	    header("Content-Description: File Transfer");
		header('Content-Disposition: attachment; filename="CuentaPublica.docx"');
	    
	    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
	    $objWriter->save('php://output');
	}
}