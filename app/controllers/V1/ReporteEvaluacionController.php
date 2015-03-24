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
		$recurso = Proyecto::with(array('componentes.registroAvance'=>function($query) use ($mes_actual){
			$query->where('mes','<=',$mes_actual)->orderBy('mes','ASC');
		},'componentes.metasMes'=>function($query) use ($mes_actual){
			$query->where('mes','<=',$mes_actual)->orderBy('mes','ASC');
		},'componentes.actividades.registroAvance'=>function($query) use ($mes_actual){
			$query->where('mes','<=',$mes_actual)->orderBy('mes','ASC');
		},'componentes.actividades.metasMes'=>function($query) use ($mes_actual){
			$query->where('mes','<=',$mes_actual)->orderBy('mes','ASC');
		}),'liderProyecto')->find($id);

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
			1 => array('mes'=>'Enero',			'abrev'=>'ENE',	'trimestre'=>1),
			2 => array('mes'=>'Febrero',		'abrev'=>'FEB',	'trimestre'=>1),
			3 => array('mes'=>'Marzo',			'abrev'=>'MAR',	'trimestre'=>1),
			4 => array('mes'=>'Abril',			'abrev'=>'ABR',	'trimestre'=>2),
			5 => array('mes'=>'Mayo',			'abrev'=>'MAy',	'trimestre'=>2),
			6 => array('mes'=>'Junio',			'abrev'=>'JUN',	'trimestre'=>2),
			7 => array('mes'=>'Julio',			'abrev'=>'JUL',	'trimestre'=>3),
			8 => array('mes'=>'Agosto',			'abrev'=>'AGO',	'trimestre'=>3),
			9 => array('mes'=>'Septiembre',		'abrev'=>'SEP',	'trimestre'=>3),
			10 => array('mes'=>'Octubre',		'abrev'=>'OCT',	'trimestre'=>4),
			11 => array('mes'=>'Noviembre',		'abrev'=>'NOV',	'trimestre'=>4),
			12 => array('mes'=>'Dicembre',		'abrev'=>'DIC',	'trimestre'=>4)
		);
		$data['recurso'] = $recurso;
		$data['componentes'] = array();
		$data['avances_mes'] = array('componentes'=>array(),'actividades'=>array());
		$data['jurisdicciones_mes'] = array('componentes'=>array(),'actividades'=>array());
		$data['conteo_elementos'] = 0;
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
				$data['avances_mes']['componentes'][$avance->mes][$componente->id] = array(
					'meta_programada' => 0.0,
					'avance_mes' => $avance->avanceMes,
					'avance_acumulado' => $avance_acumulado,
					'analisis_resultados' => $avance->analisisResultados,
					'justificacion_acumulada' => $avance->justificacionAcumulada,
					'plan_mejora' => $avance->planMejora
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

				if(isset($data['avances_mes']['componentes'][$meta_mes->mes][$componente->id])){
					$data['avances_mes']['componentes'][$meta_mes->mes][$componente->id]['meta_programada'] = $meta_mes_programada;
				}else{
					$data['avances_mes']['componentes'][$meta_mes->mes][$componente->id] = array(
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
				$data['jurisdicciones_mes']['componentes'][$meta_mes->mes][$componente->id][$meta_mes->claveJurisdiccion] = array(
					'meta_programada' => $metas_programada[$meta_mes->claveJurisdiccion],
					'avance_mes' => $meta_mes->avance,
					'avance_acumulado' => $avance_acumulado[$meta_mes->claveJurisdiccion]
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
					$data['avances_mes']['actividades'][$avance->mes][$actividad->id] = array(
						'meta_programada' => 0,
						'avance_mes' => $avance->avanceMes,
						'avance_acumulado' => $avance_acumulado,
						'analisis_resultados' => $avance->analisisResultados,
						'justificacion_acumulada' => $avance->justificacionAcumulada,
						'plan_mejora' => $avance->planMejora
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

					if(isset($data['avances_mes']['actividades'][$meta_mes->mes][$actividad->id])){
						$data['avances_mes']['actividades'][$meta_mes->mes][$actividad->id]['meta_programada'] = $meta_mes_programada;
					}else{
						$data['avances_mes']['actividades'][$meta_mes->mes][$actividad->id] = array(
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
					$data['jurisdicciones_mes']['actividades'][$meta_mes->mes][$actividad->id][$meta_mes->claveJurisdiccion] = array(
						'meta_programada' => $metas_programada[$meta_mes->claveJurisdiccion],
						'avance_mes' => $meta_mes->avance,
						'avance_acumulado' => $avance_acumulado[$meta_mes->claveJurisdiccion]
					);
				}
				$datos_componente['actividades'][] = $datos_actividad;
			}
			$data['componentes'][] = $datos_componente;
		}
		//var_dump($data);die();

		Excel::create($nombreArchivo, function($excel) use ($data){

			$mes_actual = $data['mes_actual'];
			for ($i=1; $i <= $mes_actual ; $i++) {

				$datos['mes'] = $data['meses'][$i];
				$datos['proyecto'] = array(
					'ejercicio' => $data['recurso']->ejercicio,
					'nombreTecnico' => $data['recurso']->nombreTecnico,
					'ClavePresupuestaria' => $data['recurso']->ClavePresupuestaria,
					'liderProyecto' => $data['recurso']->liderProyecto->nombre
				);
				$datos['componentes'] = $data['componentes'];

				if(isset($data['avances_mes']['componentes'][$i])){
					$datos['avances_mes']['componentes'] = $data['avances_mes']['componentes'][$i];
				}else{
					$datos['avances_mes']['componentes'] = array();
				}
				
				if(isset($data['avances_mes']['actividades'][$i])){
					$datos['avances_mes']['actividades'] = $data['avances_mes']['actividades'][$i];
				}else{
					$datos['avances_mes']['actividades'] = array();
				}
				
				$datos['jurisdicciones_mes']['componentes'] = $data['jurisdicciones_mes']['componentes'][$i];
				$datos['jurisdicciones_mes']['actividades'] = $data['jurisdicciones_mes']['actividades'][$i];

				$datos['jurisdicciones'] = $data['jurisdicciones'];

				$excel->sheet('SM '.$datos['mes']['abrev'], function($sheet)  use ($datos){
			        $sheet->loadView('rendicion-cuentas.excel.seguimiento-metas-mes', $datos);
			    });

			    $excel->getActiveSheet()->getStyle('A7:J7')->getAlignment()->setWrapText(true); 
			    $excel->getActiveSheet()->getStyle('A10:J10')->getAlignment()->setWrapText(true); 

			    $elementos = $data['conteo_elementos'];
			    $excel->getActiveSheet()->getStyle('A11:J'.(11 + $elementos))->getAlignment()->setWrapText(true); 

			    $numero_fila = 10 + $elementos + 8;
			    $excel->getActiveSheet()->getStyle('A'.$numero_fila.':J'.$numero_fila)->getAlignment()->setWrapText(true); 

			    $datos_beneficiarios['proyecto'] = $datos['proyecto'];
			    $datos_beneficiarios['mes'] = $data['meses'][$i];
			    if(($i % 3) == 0){
			    	$excel->sheet('SB'.$datos['mes']['trimestre'].'TRIM', function($sheet)  use ($datos_beneficiarios){
				        $sheet->loadView('rendicion-cuentas.excel.seguimiento-beneficiarios', $datos_beneficiarios);
				    });
				    $excel->getActiveSheet()->getStyle('A9:O9')->getAlignment()->setWrapText(true); 
			    }
			}
		})->export('xls');

	}

}