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
use Excel, Proyecto, FIBAP, ComponenteMetaMes, ActividadMetaMes,Jurisdiccion,Municipio,Region;

class ReporteEvaluacionController extends BaseController {

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id){
		$mes_actual = Util::obtenerMesActual();
		$recurso = Proyecto::with(array('liderProyecto','beneficiarios.tipoBeneficiario','datosProgramaPresupuestario','datosFuncion',
			'datosSubFuncion','objetivoPedCompleto','fuentesFinanciamiento.fuenteFinanciamiento','fuentesFinanciamiento.subFuentesFinanciamiento',
		'componentes.registroAvance'=>function($query) use ($mes_actual){
			$query->where('mes','<=',$mes_actual)->orderBy('mes','ASC');
		},'componentes.metasMes'=>function($query) use ($mes_actual){
			$query->where('mes','<=',$mes_actual)->orderBy('mes','ASC');
		},'componentes.actividades.registroAvance'=>function($query) use ($mes_actual){
			$query->where('mes','<=',$mes_actual)->orderBy('mes','ASC');
		},'componentes.actividades.metasMes'=>function($query) use ($mes_actual){
			$query->where('mes','<=',$mes_actual)->orderBy('mes','ASC');
		},'beneficiarios.registroAvance'=>function($query) use ($mes_actual){
			$query->where('mes','<=',$mes_actual)->orderBy('mes','ASC');
		},'componentes.planMejora'=>function($query) use ($mes_actual){
			$query->where('mes','=',$mes_actual)->orderBy('mes','ASC');
		},'componentes.actividades.planMejora'=>function($query) use ($mes_actual){
			$query->where('mes','=',$mes_actual)->orderBy('mes','ASC');
		},'componentes.desgloseConDatos.metasMes'=>function($query) use ($mes_actual){
			$query->where('mes','=',$mes_actual)->orderBy('mes','ASC');
		},'componentes.desgloseConDatos.metasMesAcumuladas'=>function($query) use ($mes_actual){
			$query->where('mes','<',$mes_actual)->orderBy('mes','ASC');
		},'analisisFuncional'=>function($query) use ($mes_actual){
			$query->where('mes','=',$mes_actual)->orderBy('mes','ASC');
		}))->find($id);

		if($recurso->idCobertura == 1){ //Cobertura Estado => Todos las Jurisdicciones
			$jurisdicciones = Jurisdiccion::all();
		}elseif($recurso->idCobertura == 2){ //Cobertura Municipio => La Jurisdiccion a la que pertenece el Municipio
			$jurisdicciones = Municipio::obtenerJurisdicciones($recurso->claveMunicipio)->get();
		}elseif($recurso->idCobertura == 3){ //Cobertura Region => Las Jurisdicciones de los municipios pertencientes a la Region
			$jurisdicciones = Region::obtenerJurisdicciones($recurso->claveRegion)->get();
		}
		$data['jurisdicciones'] = array('OC'=>'Oficina Central') + $jurisdicciones->lists('nombre','clave');

		$nombreArchivo = ($recurso->idClasificacionProyecto== 2) ? 'Rendición de Cuentas Inversión' : 'Rendición de Cuentas Institucional';
		$nombreArchivo.=' - '.$recurso->ClavePresupuestaria;

		$data['mes_actual'] = $mes_actual;
		$data['meses'] = array(
			1 => array('mes'=>'Enero',			'abrev'=>'ENE',	'trimestre'=>1, 'trimestre_letras'=>'PRIMER'),
			2 => array('mes'=>'Febrero',		'abrev'=>'FEB',	'trimestre'=>1, 'trimestre_letras'=>'PRIMER'),
			3 => array('mes'=>'Marzo',			'abrev'=>'MAR',	'trimestre'=>1, 'trimestre_letras'=>'PRIMER'),
			4 => array('mes'=>'Abril',			'abrev'=>'ABR',	'trimestre'=>2, 'trimestre_letras'=>'SEGUNDO'),
			5 => array('mes'=>'Mayo',			'abrev'=>'MAy',	'trimestre'=>2, 'trimestre_letras'=>'SEGUNDO'),
			6 => array('mes'=>'Junio',			'abrev'=>'JUN',	'trimestre'=>2, 'trimestre_letras'=>'SEGUNDO'),
			7 => array('mes'=>'Julio',			'abrev'=>'JUL',	'trimestre'=>3, 'trimestre_letras'=>'TERCER'),
			8 => array('mes'=>'Agosto',			'abrev'=>'AGO',	'trimestre'=>3, 'trimestre_letras'=>'TERCER'),
			9 => array('mes'=>'Septiembre',		'abrev'=>'SEP',	'trimestre'=>3, 'trimestre_letras'=>'TERCER'),
			10 => array('mes'=>'Octubre',		'abrev'=>'OCT',	'trimestre'=>4, 'trimestre_letras'=>'CUARTO'),
			11 => array('mes'=>'Noviembre',		'abrev'=>'NOV',	'trimestre'=>4, 'trimestre_letras'=>'CUARTO'),
			12 => array('mes'=>'Dicembre',		'abrev'=>'DIC',	'trimestre'=>4, 'trimestre_letras'=>'CUARTO')
		);
		$data['recurso'] = $recurso;
		$data['componentes'] = array();
		$data['beneficiarios'] = array();
		$data['beneficiarios_avances'] = array();
		$data['avances_mes'] = array('componentes'=>array(),'actividades'=>array());
		$data['jurisdicciones_mes'] = array('componentes'=>array(),'actividades'=>array());
		$data['localidades_mes'] = array('componentes'=>array(),'actividades'=>array());
		$data['conteo_elementos'] = 0;
		$data['planes_mejora'] = array('componentes'=>array(),'actividades'=>array());
		foreach ($recurso->componentes as $componente) {
			$data['conteo_elementos']++;
			$datos_componente = array(
				'indicador' => $componente->indicador,
				'id' => $componente->id,
				'actividades' => array()
			);

			$avance_acumulado = 0;
			foreach ($componente->registroAvance as $avance){
				$avance_acumulado += $avance->avanceMes;
				//$data['avances_mes']['componentes'][$avance->mes][$componente->id] = array(
				$data['avances_mes']['componentes'][$componente->id] = array(
					'meta_programada' => 0.0,
					'avance_mes' => $avance->avanceMes,
					'avance_acumulado' => $avance_acumulado,
					'analisis_resultados' => $avance->analisisResultados,
					'justificacion_acumulada' => $avance->justificacionAcumulada,
					'plan_mejora' => $avance->planMejora
				);
			}

			foreach ($componente->planMejora as $plan_mejora) {
				$data['planes_mejora']['componentes'][$componente->id] = array(
					'id' => $componente->id,
					'accionMejora' => $plan_mejora->accionMejora,
					'grupoTrabajo' => $plan_mejora->grupoTrabajo,
					'fechaInicio' => $plan_mejora->fechaInicio,
					'fechaTermino' => $plan_mejora->fechaTermino,
					'fechaNotificacion' => $plan_mejora->fechaNotificacion,
					'documentacionComprobatoria' => $plan_mejora->documentacionComprobatoria
				);
			}
			
			$meta_mes_programada = 0;
			$metas_programada = array();
			$avance_acumulado = array();
			
			foreach ($componente->metasMes as $meta_mes){
				if(!isset($metas_programada[$meta_mes->claveJurisdiccion])){
					$metas_programada[$meta_mes->claveJurisdiccion] = 0;
					$avance_acumulado[$meta_mes->claveJurisdiccion] = 0;
				}

				$meta_mes_programada += $meta_mes->meta;

				//if(isset($data['avances_mes']['componentes'][$meta_mes->mes][$componente->id])){
				if(isset($data['avances_mes']['componentes'][$componente->id])){
					//$data['avances_mes']['componentes'][$meta_mes->mes][$componente->id]['meta_programada'] = $meta_mes_programada;
					$data['avances_mes']['componentes'][$componente->id]['meta_programada'] = $meta_mes_programada;
				}else{
					//$data['avances_mes']['componentes'][$meta_mes->mes][$componente->id] = array(
					$data['avances_mes']['componentes'][$componente->id] = array(
						'meta_programada' => $meta_mes_programada,
						'avance_mes' => 0,
						'avance_acumulado' => 0,
						'analisis_resultados' => '',
						'justificacion_acumulada' => '',
						'plan_mejora' => 0
					);
				}

				$metas_programada[$meta_mes->claveJurisdiccion] += $meta_mes->meta;
				$avance_acumulado[$meta_mes->claveJurisdiccion] += $meta_mes->avance;
				//$data['jurisdicciones_mes']['componentes'][$meta_mes->mes][$componente->id][$meta_mes->claveJurisdiccion] = array(
				$data['jurisdicciones_mes']['componentes'][$componente->id][$meta_mes->claveJurisdiccion] = array(
					'meta_programada' => $metas_programada[$meta_mes->claveJurisdiccion],
					'avance_mes' => $meta_mes->avance,
					'avance_acumulado' => $avance_acumulado[$meta_mes->claveJurisdiccion]
				);
			}

			foreach ($componente->desgloseConDatos as $desglose) {
				$data['localidades_mes']['componentes'][$componente->id][$desglose->claveJurisdiccion][] = array(
					'municipio' => $desglose->municipio,
					'localidad' => $desglose->localidad,
					'meta_programada' => $desglose->metasMes[0]->meta + ((count($desglose->metasMesAcumuladas))?$desglose->metasMesAcumuladas[0]->meta:0),
					'avance_mes' => $desglose->metasMes[0]->avance,
					'avance_acumulado' => $desglose->metasMes[0]->avance + ((count($desglose->metasMesAcumuladas))?$desglose->metasMesAcumuladas[0]->avance:0)
				);
			}

			foreach ($componente->actividades as $actividad) {
				$data['conteo_elementos']++;
				$datos_actividad = array(
					'indicador' => $actividad->indicador,
					'id' => $actividad->id
				);

				$avance_acumulado = 0;
				foreach ($actividad->registroAvance as $avance){
					$avance_acumulado += $avance->avanceMes;
					//$data['avances_mes']['actividades'][$avance->mes][$actividad->id] = array(
					$data['avances_mes']['actividades'][$actividad->id] = array(
						'meta_programada' => 0,
						'avance_mes' => $avance->avanceMes,
						'avance_acumulado' => $avance_acumulado,
						'analisis_resultados' => $avance->analisisResultados,
						'justificacion_acumulada' => $avance->justificacionAcumulada,
						'plan_mejora' => $avance->planMejora
					);
				}

				foreach ($actividad->planMejora as $plan_mejora) {
					$data['planes_mejora']['actividades'][$actividad->id] = array(
						'id' => $actividad->id,
						'accionMejora' => $plan_mejora->accionMejora,
						'grupoTrabajo' => $plan_mejora->grupoTrabajo,
						'fechaInicio' => $plan_mejora->fechaInicio,
						'fechaTermino' => $plan_mejora->fechaTermino,
						'fechaNotificacion' => $plan_mejora->fechaNotificacion,
						'documentacionComprobatoria' => $plan_mejora->documentacionComprobatoria
					);
				}

				$meta_mes_programada = 0;
				$metas_programada = array();
				$avance_acumulado = array();

				foreach ($actividad->metasMes as $meta_mes){
					if(!isset($metas_programada[$meta_mes->claveJurisdiccion])){
						$metas_programada[$meta_mes->claveJurisdiccion] = 0;
						$avance_acumulado[$meta_mes->claveJurisdiccion] = 0;
					}

					$meta_mes_programada += $meta_mes->meta;

					//if(isset($data['avances_mes']['actividades'][$meta_mes->mes][$actividad->id])){
					if(isset($data['avances_mes']['actividades'][$actividad->id])){
						//$data['avances_mes']['actividades'][$meta_mes->mes][$actividad->id]['meta_programada'] = $meta_mes_programada;
						$data['avances_mes']['actividades'][$actividad->id]['meta_programada'] = $meta_mes_programada;
					}else{
						//$data['avances_mes']['actividades'][$meta_mes->mes][$actividad->id] = array(
						$data['avances_mes']['actividades'][$actividad->id] = array(
							'meta_programada' => $meta_mes_programada,
							'avance_mes' => 0,
							'avance_acumulado' => 0,
							'analisis_resultados' => '',
							'justificacion_acumulada' => '',
							'plan_mejora' => 0
						);
					}

					$metas_programada[$meta_mes->claveJurisdiccion] += $meta_mes->meta;
					$avance_acumulado[$meta_mes->claveJurisdiccion] += $meta_mes->avance;
					//$data['jurisdicciones_mes']['actividades'][$meta_mes->mes][$actividad->id][$meta_mes->claveJurisdiccion] = array(
					$data['jurisdicciones_mes']['actividades'][$actividad->id][$meta_mes->claveJurisdiccion] = array(
						'meta_programada' => $metas_programada[$meta_mes->claveJurisdiccion],
						'avance_mes' => $meta_mes->avance,
						'avance_acumulado' => $avance_acumulado[$meta_mes->claveJurisdiccion]
					);
				}
				$datos_componente['actividades'][] = $datos_actividad;
			}
			$data['componentes'][] = $datos_componente;
		}
		
		foreach ($recurso->beneficiarios as $beneficiario) {
			if(!isset($data['beneficiarios'][$beneficiario->idTipoBeneficiario])){
				$data['beneficiarios'][$beneficiario->idTipoBeneficiario] = array(
					'id' => $beneficiario->idTipoBeneficiario,
					'tipoBeneficiario' => $beneficiario->tipoBeneficiario->descripcion
				);
			}

			$beneficiarios_acumulados = array();

			foreach ($beneficiario->registroAvance as $avance) {
				//if(!isset($data['beneficiarios_avances'][$avance->mes][$avance->idTipoBeneficiario])){
				if(!isset($data['beneficiarios_avances'][$avance->idTipoBeneficiario])){
					//$data['beneficiarios_avances'][$avance->mes][$avance->idTipoBeneficiario] = array(
					$data['beneficiarios_avances'][$avance->idTipoBeneficiario] = array(
						'f' => array(
							'total' => 0,'urbana' => 0,'rural' => 0,'mestiza' => 0,'indigena' => 0,
							'inmigrante' => 0,'otros' => 0,'muyAlta' => 0,'alta' => 0,'media' => 0,
							'baja' => 0,'muyBaja' => 0,'acumulado' => 0
						), 
						'm' => array(
							'total' => 0,'urbana' => 0,'rural' => 0,'mestiza' => 0,'indigena' => 0,
							'inmigrante' => 0,'otros' => 0,'muyAlta' => 0,'alta' => 0,'media' => 0,
							'baja' => 0,'muyBaja' => 0,'acumulado' => 0
						)
					);
				}

				if(!isset($beneficiarios_acumulados[$avance->idTipoBeneficiario])){
					$beneficiarios_acumulados[$avance->idTipoBeneficiario] = array('f'=>0,'m'=>0);
				}

				$beneficiarios_acumulados[$avance->idTipoBeneficiario][$avance->sexo] += $avance->total;

				//$data['beneficiarios_avances'][$avance->mes][$avance->idTipoBeneficiario][$avance->sexo] = array(
				$data['beneficiarios_avances'][$avance->idTipoBeneficiario][$avance->sexo] = array(
					'total' => $avance->total,
					'urbana' => $avance->urbana,
					'rural' => $avance->rural,
					'mestiza' => $avance->mestiza,
					'indigena' => $avance->indigena,
					'inmigrante' => $avance->inmigrante,
					'otros' => $avance->otros,
					'muyAlta' => $avance->muyAlta,
					'alta' => $avance->alta,
					'media' => $avance->media,
					'baja' => $avance->baja,
					'muyBaja' => $avance->muyBaja,
					'acumulado' => $beneficiarios_acumulados[$avance->idTipoBeneficiario][$avance->sexo]
				);
			}
		}
		//print_r($recurso->objetivoPedCompleto->toArray());die();

		Excel::create($nombreArchivo, function($excel) use ($data){

			$mes_actual = $data['mes_actual'];
			$i = $mes_actual;
			//for ($i=1; $i <= $mes_actual ; $i++) {

				//$datos['mes'] = $data['meses'][$i];
				$datos['mes'] = $data['meses'][$mes_actual];
				$datos['proyecto'] = array(
					'ejercicio' => $data['recurso']->ejercicio,
					'nombreTecnico' => $data['recurso']->nombreTecnico,
					'ClavePresupuestaria' => $data['recurso']->ClavePresupuestaria,
					'liderProyecto' => $data['recurso']->liderProyecto->nombre,
					'cargoLiderProyecto' => $data['recurso']->liderProyecto->cargo,
					'fuenteInformacion' => $data['recurso']->fuenteInformacion
				);

				$datos['componentes'] = $data['componentes'];

				//if(isset($data['avances_mes']['componentes'][$i])){
				if(isset($data['avances_mes']['componentes'])){
					//$datos['avances_mes']['componentes'] = $data['avances_mes']['componentes'][$i];
					$datos['avances_mes']['componentes'] = $data['avances_mes']['componentes'];
				}else{
					$datos['avances_mes']['componentes'] = array();
				}
				
				//if(isset($data['avances_mes']['actividades'][$i])){
				if(isset($data['avances_mes']['actividades'])){
					//$datos['avances_mes']['actividades'] = $data['avances_mes']['actividades'][$i];
					$datos['avances_mes']['actividades'] = $data['avances_mes']['actividades'];
				}else{
					$datos['avances_mes']['actividades'] = array();
				}
				
				//$datos['jurisdicciones_mes']['componentes'] = $data['jurisdicciones_mes']['componentes'][$i];
				$datos['jurisdicciones_mes']['componentes'] = $data['jurisdicciones_mes']['componentes'];
				//$datos['jurisdicciones_mes']['actividades'] = $data['jurisdicciones_mes']['actividades'][$i];
				$datos['jurisdicciones_mes']['actividades'] = $data['jurisdicciones_mes']['actividades'];

				$datos['localidades_mes']['componentes'] = $data['localidades_mes']['componentes'];
				$datos['localidades_mes']['actividades'] = $data['localidades_mes']['actividades'];

				//var_dump($datos['localidades_mes']['componentes']); die;

				$datos['jurisdicciones'] = $data['jurisdicciones'];

				$excel->sheet('SM '.$datos['mes']['abrev'], function($sheet)  use ($datos){
			        $sheet->loadView('rendicion-cuentas.excel.seguimiento-metas-mes', $datos);
			        $imagen = $this->obtenerImagen('LogoFederal.png','A1');
					$imagen->setWorksheet($sheet);
			        $imagen = $this->obtenerImagen('LogoInstitucional.png','J1');
					$imagen->setWorksheet($sheet);
			    });

			    $excel->getActiveSheet()->getStyle('A7:J7')->getAlignment()->setWrapText(true); 
			    $excel->getActiveSheet()->getStyle('A10:J10')->getAlignment()->setWrapText(true); 

			    $elementos = $data['conteo_elementos'];
			    $excel->getActiveSheet()->getStyle('A11:J'.(11 + $elementos))->getAlignment()->setWrapText(true); 

			    $numero_fila = 10 + $elementos + 8;
			    $excel->getActiveSheet()->getStyle('A'.$numero_fila.':J'.$numero_fila)->getAlignment()->setWrapText(true); 

			    if(($i % 3) == 0){
			    	$datos_beneficiarios['proyecto'] = $datos['proyecto'];
			    	//$datos_beneficiarios['mes'] = $data['meses'][$i];
			    	$datos_beneficiarios['mes'] = $data['meses'][$mes_actual];
			    	$datos_beneficiarios['beneficiarios'] = $data['beneficiarios'];
					//$datos_beneficiarios['beneficiarios_avances'] = $data['beneficiarios_avances'][$i];
					$datos_beneficiarios['beneficiarios_avances'] = $data['beneficiarios_avances'];

			    	$excel->sheet('SB'.$datos['mes']['trimestre'].'TRIM', function($sheet)  use ($datos_beneficiarios){
				        $sheet->loadView('rendicion-cuentas.excel.seguimiento-beneficiarios', $datos_beneficiarios);
				        $imagen = $this->obtenerImagen('LogoFederal.png','A1');
						$imagen->setWorksheet($sheet);
				        $imagen = $this->obtenerImagen('LogoInstitucional.png','N1');
						$imagen->setWorksheet($sheet);
				        $sheet->getStyle('A7:O7')->getAlignment()->setWrapText(true);
				    });

				    $total_beneficiarios = count($datos_beneficiarios['beneficiarios']);
				    $row = 9;

				   	for($i = 0 ; $i < $total_beneficiarios ; $i++){
				   		$excel->getActiveSheet()->getStyle('F'.$row.':O'.$row)->getAlignment()->setWrapText(true);
				    	$excel->getActiveSheet()->getStyle('A'.($row+1).':N'.($row+1))->getAlignment()->setWrapText(true);
				    	$row += 11;
				    	//throw new \Exception($row, 1);
				   	}

				   	$datos['proyecto']['programaPresupuestario'] = $data['recurso']->datosProgramaPresupuestario->clave . ' ' . $data['recurso']->datosProgramaPresupuestario->descripcion;
					$datos['proyecto']['funcion'] = $data['recurso']->datosFuncion->clave . ' ' . $data['recurso']->datosFuncion->descripcion;
					$datos['proyecto']['subFuncion'] = $data['recurso']->datosSubFuncion->clave . ' ' . $data['recurso']->datosSubFuncion->descripcion;
					$datos['proyecto']['politicaPublica'] = $data['recurso']->objetivoPedCompleto->padre->clave . ' ' . $data['recurso']->objetivoPedCompleto->padre->descripcion;
					$datos['proyecto']['tema'] = $data['recurso']->objetivoPedCompleto->padre->padre->clave . ' ' . $data['recurso']->objetivoPedCompleto->padre->padre->descripcion;
					$datos['proyecto']['eje'] = $data['recurso']->objetivoPedCompleto->padre->padre->padre->clave . ' ' . $data['recurso']->objetivoPedCompleto->padre->padre->padre->descripcion;

				   	$datos_analisis['proyecto'] = $datos['proyecto'];
				   	$datos_analisis['fuentes_financiamiento'] = $data['recurso']->fuentesFinanciamiento;
				   	$datos_analisis['mes'] = $data['meses'][$i];
				   	$datos_analisis['analisis_funcional'] = $data['recurso']->analisisFuncional[0];
				   	$excel->sheet('CP'.$datos['mes']['trimestre'].'TRIM', function($sheet)  use ($datos_analisis){
				        $sheet->loadView('rendicion-cuentas.excel.analisis-funcional', $datos_analisis);

				        $imagen = $this->obtenerImagen('LogoFederal.png','A1');
				        $imagen->setWorksheet($sheet);
						
				        $imagen = $this->obtenerImagen('LogoInstitucional.png','E1',60);
						$imagen->setWorksheet($sheet);
				    });
				    $excel->getActiveSheet()->getStyle('A8:D8')->getAlignment()->setWrapText(true);
				    $excel->getActiveSheet()->getStyle('A11:E11')->getAlignment()->setWrapText(true);
				    $excel->getActiveSheet()->getStyle('A10:D10')->getAlignment()->setWrapText(true);

				    $datos_plan['componentes'] = $data['componentes'];
				    $datos_plan['proyecto'] = $datos['proyecto'];
				    $datos_plan['planes_mejora'] = $data['planes_mejora'];
				    $excel->sheet('PAM'.$datos['mes']['trimestre'].'TRIM', function($sheet)  use ($datos_plan,$elementos){
				        $sheet->loadView('rendicion-cuentas.excel.plan-mejora', $datos_plan);
				        $sheet->getStyle('F9')->getAlignment()->setWrapText(true);
				        $sheet->getStyle('A7:M7')->getAlignment()->setWrapText(true);
				        $sheet->getStyle('A10:M10')->getAlignment()->setWrapText(true);
				        $sheet->getStyle('A11:M'.(11 + $elementos))->getAlignment()->setWrapText(true); 
				        $imagen = $this->obtenerImagen('LogoFederal.png','A1');
						$imagen->setWorksheet($sheet);
				        $imagen = $this->obtenerImagen('LogoInstitucional.png','L1',60);
						$imagen->setWorksheet($sheet);
				    });
				    //$excel->getActiveSheet()->getStyle('A9:M9')->getAlignment()->setWrapText(true);
			    }
			//}
		})->download('xls');
	}

	private function obtenerImagen($imagen,$celda,$offset = 10){
		$objDrawing = new \PHPExcel_Worksheet_Drawing();
		$objDrawing->setPath('./img/'.$imagen);// filesystem reference for the image file
		$objDrawing->setHeight(100);// sets the image height to 36px (overriding the actual image height); 
		$objDrawing->setWidth(200);// sets the image height to 36px (overriding the actual image height); 
		$objDrawing->setCoordinates($celda);// pins the top-left corner of the image to cell D24
		$objDrawing->setOffsetX($offset);// pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
		return $objDrawing;
	}
}