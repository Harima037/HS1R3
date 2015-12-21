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
use BaseController, Input, Response, DB, Sentry, View;
use PDF, Proyecto, Jurisdiccion,Municipio,Region;

class ReporteEvaluacionController extends BaseController {

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id){
		$parametros = Input::all();
		$tipo_reporte = 'general';
		
		if(isset($parametros['tipo'])){
			$tipo_reporte = $parametros['tipo'];
		}

		if(isset($parametros['mes'])){
			$mes_actual = intval($parametros['mes']);
		}else{
			$mes_actual = Util::obtenerMesActual();
		}
		
		if($mes_actual == 0 || $mes_actual > 12){
			if(date('n') == 1){
				$mes_actual = 12;
			}else{
				$mes_actual = date('n') - 1;
			}
		}

		$recurso = Proyecto::with(array('liderProyecto','responsableInformacion','beneficiarios.tipoBeneficiario','datosProgramaPresupuestario','datosFuncion',
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
		},'componentes.actividades.desgloseConDatos.metasMes'=>function($query) use ($mes_actual){
			$query->where('mes','=',$mes_actual)->orderBy('mes','ASC');
		},'componentes.actividades.desgloseConDatos.metasMesAcumuladas'=>function($query) use ($mes_actual){
			$query->where('mes','<',$mes_actual)->orderBy('mes','ASC');
		},'analisisFuncional'=>function($query) use ($mes_actual){
			$query->where('mes','=',$mes_actual)->orderBy('mes','ASC');
		}))->find($id);
		$recurso = $recurso->toArray();
		
		if($recurso['idCobertura'] == 1){ //Cobertura Estado => Todos las Jurisdicciones
			$jurisdicciones = Jurisdiccion::all();
		}elseif($recurso['idCobertura'] == 2){ //Cobertura Municipio => La Jurisdiccion a la que pertenece el Municipio
			$jurisdicciones = Municipio::obtenerJurisdicciones($recurso['claveMunicipio'])->get();
		}elseif($recurso['idCobertura'] == 3){ //Cobertura Region => Las Jurisdicciones de los municipios pertencientes a la Region
			$jurisdicciones = Region::obtenerJurisdicciones($recurso['claveRegion'])->get();
		}
		$data['jurisdicciones'] = array('OC'=>'Oficina Central') + $jurisdicciones->lists('nombre','clave');
		$jurisdicciones = $jurisdicciones->toArray();
		unset($jurisdicciones);

		$nombreArchivo = ($recurso['idClasificacionProyecto']== 2) ? 'Rendición de Cuentas Inversión' : 'Rendición de Cuentas Institucional';
		$nombreArchivo.=' - '.$recurso['ClavePresupuestaria'];

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
		
		$data['componentes'] = array();
		$data['beneficiarios'] = array();
		$data['beneficiarios_avances'] = array();
		$data['avances_mes'] = array('componentes'=>array(),'actividades'=>array());
		$data['jurisdicciones_mes'] = array('componentes'=>array(),'actividades'=>array());
		$data['localidades_mes'] = array('componentes'=>array(),'actividades'=>array(),'localidades'=>array());
		$data['conteo_elementos'] = 0;
		$data['planes_mejora'] = array('componentes'=>array(),'actividades'=>array());

		foreach ($recurso['componentes'] as $componente) {
			if(isset($recurso['componentes'])){
				unset($recurso['componentes']);
			}
			$data['conteo_elementos']++;
			$datos_componente = array(
				'indicador' => $componente['indicador'],
				'id' => $componente['id'],
				'actividades' => array()
			);

			$total_avance_acumulado = 0;
			if(!isset($data['avances_mes']['componentes'][$componente['id']])){
				$data['avances_mes']['componentes'][$componente['id']] = array(
					'meta_programada' => 0.0,
					'avance_mes' => 0.0,
					'avance_acumulado' => 0.0,
					'analisis_resultados' => '',
					'justificacion_acumulada' => '',
					'plan_mejora' => 0
				);
			}
			foreach ($componente['registro_avance'] as $avance){
				if(isset($componente['registro_avance'])){
					unset($componente['registro_avance']);
				}
				$total_avance_acumulado += $avance['avanceMes'];
				if($avance['mes'] == $mes_actual){
					$data['avances_mes']['componentes'][$componente['id']]['avance_mes'] 				= $avance['avanceMes'];
					$data['avances_mes']['componentes'][$componente['id']]['analisis_resultados'] 		= $avance['analisisResultados'];
					$data['avances_mes']['componentes'][$componente['id']]['justificacion_acumulada'] 	= $avance['justificacionAcumulada'];
					$data['avances_mes']['componentes'][$componente['id']]['plan_mejora'] 				= $avance['planMejora'];
				}
			}
			$data['avances_mes']['componentes'][$componente['id']]['avance_acumulado'] = $total_avance_acumulado;

			foreach ($componente['plan_mejora'] as $plan_mejora) {
				$data['planes_mejora']['componentes'][$componente['id']] = array(
					'id' => $componente['id'],
					'accionMejora' => $plan_mejora['accionMejora'],
					'grupoTrabajo' => $plan_mejora['grupoTrabajo'],
					'fechaInicio' => $plan_mejora['fechaInicio'],
					'fechaTermino' => $plan_mejora['fechaTermino'],
					'fechaNotificacion' => $plan_mejora['fechaNotificacion'],
					'documentacionComprobatoria' => $plan_mejora['documentacionComprobatoria']
				);
			}
			
			$meta_mes_programada = 0;
			$metas_programada = array();
			$avance_acumulado = array();
			
			foreach ($componente['metas_mes'] as $meta_mes){
				if(!isset($metas_programada[$meta_mes['claveJurisdiccion']])){
					$metas_programada[$meta_mes['claveJurisdiccion']] = 0;
					$avance_acumulado[$meta_mes['claveJurisdiccion']] = 0;
				}

				$meta_mes_programada += $meta_mes['meta'];

				$metas_programada[$meta_mes['claveJurisdiccion']] += $meta_mes['meta'];
				$avance_acumulado[$meta_mes['claveJurisdiccion']] += $meta_mes['avance'];

				$data['jurisdicciones_mes']['componentes'][$componente['id']][$meta_mes['claveJurisdiccion']] = array(
					'meta_programada' => $metas_programada[$meta_mes['claveJurisdiccion']],
					'avance_mes' => ($meta_mes['mes'] == $mes_actual)?$meta_mes['avance']:0.0,
					'avance_acumulado' => $avance_acumulado[$meta_mes['claveJurisdiccion']]
				);
			}
			$data['avances_mes']['componentes'][$componente['id']]['meta_programada'] = $meta_mes_programada;
			
			foreach ($componente['desglose_con_datos'] as $desglose) {
				if(!isset($data['localidades_mes']['localidades'][$desglose['claveMunicipio'].'_'.$desglose['claveLocalidad']])){
					$data['localidades_mes']['localidades'][$desglose['claveMunicipio'].'_'.$desglose['claveLocalidad']] = array(
						'municipio' => $desglose['municipio'],
						'localidad' => $desglose['localidad']
					);
				}
				if(count($desglose['metas_mes'])){
					$metas_mes = $desglose['metas_mes'][0];
				}else{
					$metas_mes = array('avance'=>0,'meta'=>0);
				}
				$data['localidades_mes']['componentes'][$componente['id']][$desglose['claveJurisdiccion']][] = array(
					'clave_localidad'=> $desglose['claveMunicipio'].'_'.$desglose['claveLocalidad'],
					'meta_programada' => $metas_mes['meta'] + ((count($desglose['metas_mes_acumuladas']))?$desglose['metas_mes_acumuladas']['meta']:0),
					'avance_mes' => $metas_mes['avance'],
					'avance_acumulado' => $metas_mes['avance'] + ((count($desglose['metas_mes_acumuladas']))?$desglose['metas_mes_acumuladas']['avance']:0)
				);
			}

			foreach ($componente['actividades'] as $actividad) {
				if(isset($componente['actividades'])){
					unset($componente['actividades']);
				}
				
				$data['conteo_elementos']++;
				$datos_actividad = array(
					'indicador' => $actividad['indicador'],
					'id' => $actividad['id']
				);
				
				$total_avance_acumulado = 0;
				if(!isset($data['avances_mes']['actividades'][$actividad['id']])){
					$data['avances_mes']['actividades'][$actividad['id']] = array(
						'meta_programada' => 0.0,
						'avance_mes' => 0.0,
						'avance_acumulado' => 0.0,
						'analisis_resultados' => '',
						'justificacion_acumulada' => '',
						'plan_mejora' => 0
					);
				}
				foreach ($actividad['registro_avance'] as $avance){
					$total_avance_acumulado += $avance['avanceMes'];
					if($avance['mes'] == $mes_actual){
						$data['avances_mes']['actividades'][$actividad['id']]['avance_mes'] 				= $avance['avanceMes'];
						$data['avances_mes']['actividades'][$actividad['id']]['analisis_resultados'] 		= $avance['analisisResultados'];
						$data['avances_mes']['actividades'][$actividad['id']]['justificacion_acumulada'] 	= $avance['justificacionAcumulada'];
						$data['avances_mes']['actividades'][$actividad['id']]['plan_mejora'] 				= $avance['planMejora'];
					}
				}
				$data['avances_mes']['actividades'][$actividad['id']]['avance_acumulado'] = $total_avance_acumulado;

				foreach ($actividad['plan_mejora'] as $plan_mejora) {
					$data['planes_mejora']['actividades'][$actividad['id']] = array(
						'id' => $actividad['id'],
						'accionMejora' => $plan_mejora['accionMejora'],
						'grupoTrabajo' => $plan_mejora['grupoTrabajo'],
						'fechaInicio' => $plan_mejora['fechaInicio'],
						'fechaTermino' => $plan_mejora['fechaTermino'],
						'fechaNotificacion' => $plan_mejora['fechaNotificacion'],
						'documentacionComprobatoria' => $plan_mejora['documentacionComprobatoria']
					);
				}

				$meta_mes_programada = 0;
				$metas_programada = array();
				$avance_acumulado = array();

				foreach ($actividad['metas_mes'] as $meta_mes){
					if(!isset($metas_programada[$meta_mes['claveJurisdiccion']])){
						$metas_programada[$meta_mes['claveJurisdiccion']] = 0;
						$avance_acumulado[$meta_mes['claveJurisdiccion']] = 0;
					}

					$meta_mes_programada += $meta_mes['meta'];

					$metas_programada[$meta_mes['claveJurisdiccion']] += $meta_mes['meta'];
					$avance_acumulado[$meta_mes['claveJurisdiccion']] += $meta_mes['avance'];
					$data['jurisdicciones_mes']['actividades'][$actividad['id']][$meta_mes['claveJurisdiccion']] = array(
						'meta_programada' => $metas_programada[$meta_mes['claveJurisdiccion']],
						'avance_mes' => ($meta_mes['mes'] == $mes_actual)?$meta_mes['avance']:0.0,
						'avance_acumulado' => $avance_acumulado[$meta_mes['claveJurisdiccion']]
					);
				}
				$data['avances_mes']['actividades'][$actividad['id']]['meta_programada'] = $meta_mes_programada;
				//var_dump($actividad['desglose_con_datos']);die;
				foreach ($actividad['desglose_con_datos'] as $desglose) {
					if(!isset($data['localidades_mes']['localidades'][$desglose['claveMunicipio'].'_'.$desglose['claveLocalidad']])){
						$data['localidades_mes']['localidades'][$desglose['claveMunicipio'].'_'.$desglose['claveLocalidad']] = array(
							'municipio' => $desglose['municipio'],
							'localidad' => $desglose['localidad']
						);
					}
					if(count($desglose['metas_mes'])){
						$metas_mes = $desglose['metas_mes'][0];
					}else{
						$metas_mes = array('avance'=>0,'meta'=>0);
					}
					
					$data['localidades_mes']['actividades'][$actividad['id']][$desglose['claveJurisdiccion']][] = array(
						'clave_localidad' => $desglose['claveMunicipio'].'_'.$desglose['claveLocalidad'],
						'meta_programada' => $metas_mes['meta'] + ((count($desglose['metas_mes_acumuladas']))?$desglose['metas_mes_acumuladas']['meta']:0),
						'avance_mes' => $metas_mes['avance'],
						'avance_acumulado' => $metas_mes['avance'] + ((count($desglose['metas_mes_acumuladas']))?$desglose['metas_mes_acumuladas']['avance']:0)
					);
					
				}
				
				$datos_componente['actividades'][] = $datos_actividad;
			}
			$data['componentes'][] = $datos_componente;
		}
		unset($metas_programada);
		unset($avance_acumulado);
		unset($componente);
		unset($actividad);
		unset($desglose);
		unset($datos_actividad);
		unset($datos_componente);

		if(($mes_actual % 3) == 0){
			foreach ($recurso['beneficiarios'] as $beneficiario) {
				if(!isset($data['beneficiarios'][$beneficiario['idTipoBeneficiario']])){
					$data['beneficiarios'][$beneficiario['idTipoBeneficiario']] = array(
						'id' => $beneficiario['idTipoBeneficiario'],
						'tipoBeneficiario' => $beneficiario['tipo_beneficiario']['descripcion']
					);
				}

				$beneficiarios_acumulados = array();

				foreach ($beneficiario['registro_avance'] as $avance) {
					//if(!isset($data['beneficiarios_avances'][$avance->mes][$avance->idTipoBeneficiario])){
					if(!isset($data['beneficiarios_avances'][$avance['idTipoBeneficiario']])){
						//$data['beneficiarios_avances'][$avance['mes']][$avance['idTipoBeneficiario']] = array(
						$data['beneficiarios_avances'][$avance['idTipoBeneficiario']] = array(
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

					if(!isset($beneficiarios_acumulados[$avance['idTipoBeneficiario']])){
						$beneficiarios_acumulados[$avance['idTipoBeneficiario']] = array('f'=>0,'m'=>0);
					}

					$beneficiarios_acumulados[$avance['idTipoBeneficiario']][$avance['sexo']] += $avance['total'];

					//$data['beneficiarios_avances'][$avance->mes][$avance->idTipoBeneficiario][$avance->sexo] = array(
					$data['beneficiarios_avances'][$avance['idTipoBeneficiario']][$avance['sexo']] = array(
						'total' => $avance['total'],
						'urbana' => $avance['urbana'],
						'rural' => $avance['rural'],
						'mestiza' => $avance['mestiza'],
						'indigena' => $avance['indigena'],
						'inmigrante' => $avance['inmigrante'],
						'otros' => $avance['otros'],
						'muyAlta' => $avance['muyAlta'],
						'alta' => $avance['alta'],
						'media' => $avance['media'],
						'baja' => $avance['baja'],
						'muyBaja' => $avance['muyBaja'],
						'acumulado' => $beneficiarios_acumulados[$avance['idTipoBeneficiario']][$avance['sexo']]
					);
				}
			}
			unset($beneficiarios_acumulados);
		}else{
			unset($recurso['beneficiarios']);
		}
		
		$mes_actual = $data['mes_actual'];
		$i = $mes_actual;
		
		$datos['mes'] = $data['meses'][$mes_actual];
		$datos['proyecto'] = array(
			'ejercicio' => $recurso['ejercicio'],
			'nombreTecnico' => $recurso['nombreTecnico'],
			'ClavePresupuestaria' => $recurso['ClavePresupuestaria'],
			'liderProyecto' => $recurso['lider_proyecto']['nombre'],
			'cargoLiderProyecto' => $recurso['lider_proyecto']['cargo'],
			'responsableInformacion' => $recurso['responsable_informacion']['nombre'],
			'cargoResponsableInformacion' => $recurso['responsable_informacion']['cargo'],
			'fuenteInformacion' => $recurso['fuenteInformacion']
		);

		$datos['componentes'] = $data['componentes'];

		if(isset($data['avances_mes']['componentes'])){
			$datos['avances_mes']['componentes'] = $data['avances_mes']['componentes'];
		}else{
			$datos['avances_mes']['componentes'] = array();
		}
		
		if(isset($data['avances_mes']['actividades'])){
			$datos['avances_mes']['actividades'] = $data['avances_mes']['actividades'];
		}else{
			$datos['avances_mes']['actividades'] = array();
		}
		
		$datos['jurisdicciones_mes']['componentes'] = $data['jurisdicciones_mes']['componentes'];
		$datos['jurisdicciones_mes']['actividades'] = $data['jurisdicciones_mes']['actividades'];

		$datos['localidades_mes']['componentes'] = $data['localidades_mes']['componentes'];
		$datos['localidades_mes']['actividades'] = $data['localidades_mes']['actividades'];
		$datos['localidades_mes']['localidades'] = $data['localidades_mes']['localidades'];
		
		//var_dump($datos['localidades_mes']['localidades']); die;

		$datos['jurisdicciones'] = $data['jurisdicciones'];

		//
		$reporte_indicador = 'mes';
		if(($mes_actual % 3) == 0){
	    	$datos['beneficiarios'] = $data['beneficiarios'];
			$datos['beneficiarios_avances'] = $data['beneficiarios_avances'];
			$reporte_indicador = 'trimestre';

			$datos['proyecto']['programaPresupuestario'] = $recurso['datos_programa_presupuestario']['clave'] . ' ' . $recurso['datos_programa_presupuestario']['descripcion'];
			$datos['proyecto']['funcion'] = $recurso['datos_funcion']['clave'] . ' ' . $recurso['datos_funcion']['descripcion'];
			$datos['proyecto']['subFuncion'] = $recurso['datos_sub_funcion']['clave'] . ' ' . $recurso['datos_sub_funcion']['descripcion'];
			$datos['proyecto']['politicaPublica'] = $recurso['objetivo_ped_completo']['padre']['clave'] . ' ' . $recurso['objetivo_ped_completo']['padre']['descripcion'];
			$datos['proyecto']['tema'] = $recurso['objetivo_ped_completo']['padre']['padre']['clave'] . ' ' . $recurso['objetivo_ped_completo']['padre']['padre']['descripcion'];
			$datos['proyecto']['eje'] = $recurso['objetivo_ped_completo']['padre']['padre']['padre']['clave'] . ' ' . $recurso['objetivo_ped_completo']['padre']['padre']['padre']['descripcion'];

		   	$datos['fuentes_financiamiento'] = $recurso['fuentes_financiamiento'];

		   	$datos['mes'] = $data['meses'][$i];
		   	$datos['analisis_funcional'] = $recurso['analisis_funcional'][0];

		    $datos['planes_mejora'] = $data['planes_mejora'];
		}

		$dato_reporte = array('datos'=>$datos, 'reporte' => $reporte_indicador, 'tipo_reporte' => $tipo_reporte);
		
		unset($data);
		unset($datos);
		unset($reporte_indicador);
		unset($tipo_reporte);
		unset($recurso);
		/*
		$memoria = memory_get_usage();
		if ($memoria < 1024) 
            $memoria_dato = $memoria." B"; 
        elseif ($memoria < 1048576) 
            $memoria_dato = ($memoria/1024)." KB"; 
        else 
            $memoria_dato = ($memoria/1048576)." MB";
		var_dump($memoria_dato);die;
		*/
		
		$pdf = PDF::setPaper('LETTER')->setOrientation('landscape')->setWarnings(false)->loadView('rendicion-cuentas.pdf.reporte-seguimiento',$dato_reporte);
		
		$pdf->output();
		$dom_pdf = $pdf->getDomPDF();
		$canvas = $dom_pdf ->get_canvas();
		$w = $canvas->get_width();
  		$h = $canvas->get_height();
		$canvas->page_text(($w-75), ($h-16), "Página {PAGE_NUM} de {PAGE_COUNT}", null, 10, array(0, 0, 0));

		return $pdf->stream('reporte_seguimiento.pdf');
	}
}