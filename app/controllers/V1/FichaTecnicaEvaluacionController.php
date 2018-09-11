<?php
/* 
*	SIRE
*	Sistema de Integración Rendición de cuentas y Evaluación
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

use SSA\Utilerias\Validador;
use SSA\Utilerias\Util;
use BaseController, Input, Response, DB, Sentry, Hash, Exception,DateTime,Mail,Excel;
use UnidadResponsable,EvaluacionPlanMejora, Proyecto, FichaTecnicaEvaluacion;

class FichaTecnicaEvaluacionController extends BaseController {
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		$http_status = 200;
		$data = array();
		try{
			$parametros = Input::all();
			if(isset($parametros['formatogrid'])){
				if(isset($parametros['ejercicio'])){
					$ejercicio = $parametros['ejercicio'];
				}else{
					$ejercicio = Util::obtenerAnioCaptura();
				}

				if($parametros['trimestre'] == 1){
					$mes = 3;
				}else if ($parametros['trimestre'] == 2) {
					$mes = 6;
				}else if ($parametros['trimestre'] == 3) {
					$mes = 9;
				}else if ($parametros['trimestre'] == 4) {
					$mes = 12;
				}

				if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
				$skip = (($parametros['pagina']-1)*10);

				/*
				select concat(unidadResponsable, finalidad, funcion, subfuncion, subsubfuncion, programaSectorial, programaPresupuestario, origenAsignacion, actividadInstitucional, proyectoEstrategico, LPAD(numeroProyectoEstrategico,3,'0')) as clave, RAM.idProyecto, RAM.mes, P.nombreTecnico, RAM.planMejora
				FROM registroAvancesMetas RAM 
				JOIN proyectos P on P.id = RAM.idProyecto and ejercicio = 2018 
				WHERE RAM.planMejora = 1 and RAM.borradoAl is null and RAM.mes = 3 
				GROUP BY RAM.idProyecto
				ORDER BY P.unidadResponsable

				PC.indicador LIKE "%'.$parametros['buscar'].'%" or CA.indicador LIKE "%'.$parametros['buscar'].'%"
				*/

				$query = "select concat(unidadResponsable, finalidad, funcion, subfuncion, subsubfuncion, programaSectorial, programaPresupuestario, origenAsignacion, actividadInstitucional, proyectoEstrategico, LPAD(numeroProyectoEstrategico,3,'0')) as clave, RAM.idProyecto, RAM.mes, P.nombreTecnico, RAM.planMejora, FT.recomendacion ";
				$query .= "FROM registroAvancesMetas RAM ";

				$query .= "JOIN proyectos P on P.id = RAM.idProyecto and ejercicio = ? ";
				$query .= "LEFT JOIN fichaTecnicaEvaluacion FT on FT.idProyecto = P.id and FT.mes = ?";
				$query .= "WHERE RAM.planMejora = 1 and RAM.borradoAl is null and RAM.mes = ? ";

				if(isset($parametros['buscar'])){
					$query .= 'and (P.nombreTecnico LIKE "%'.$parametros['buscar'].'%" or concat(unidadResponsable, finalidad, funcion, subfuncion, subsubfuncion, programaSectorial, programaPresupuestario, origenAsignacion, actividadInstitucional, proyectoEstrategico, LPAD(numeroProyectoEstrategico,3,"0")) LIKE "%'.$parametros['buscar'].'%" ) ';
				}

				$query .= "GROUP BY RAM.idProyecto ";
				$query .= "ORDER BY P.unidadResponsable, P.id ";

				$query_params = [$ejercicio, $mes, $mes];

				if(!isset($parametros['excel'])){
					if($skip == 0){
						$query .= " LIMIT 10 ";
					}else{
						$query .= " LIMIT ".$skip.", 10 ";
					}
				}
				
				$rows = DB::select($query, $query_params);

				$query = "select count(distinct RAM.idProyecto) as conteo ";
				$query .= "FROM registroAvancesMetas RAM ";

				$query .= "JOIN proyectos P on P.id = RAM.idProyecto and ejercicio = ? ";
				$query .= "WHERE RAM.planMejora = 1 and RAM.borradoAl is null and RAM.mes = ? ";

				if(isset($parametros['buscar'])){
					$query .= 'and (P.nombreTecnico LIKE "%'.$parametros['buscar'].'%" or concat(unidadResponsable, finalidad, funcion, subfuncion, subsubfuncion, programaSectorial, programaPresupuestario, origenAsignacion, actividadInstitucional, proyectoEstrategico, LPAD(numeroProyectoEstrategico,3,"0")) LIKE "%'.$parametros['buscar'].'%" ) ';
				}

				//$query .= "GROUP BY RAM.idProyecto ";

				$query_params = [$ejercicio, $mes];
				
				$total = DB::select($query,$query_params);
				$total = $total[0]->conteo;
				
				$data = array('resultados'=>$total,'data'=>$rows);

				if($total<=0){
					$http_status = 404;
					$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
				}

				return Response::json($data,$http_status);

				//$queries = DB::getQueryLog();
				//var_dump(end($queries));die;
				/*
				$proximo_mes = $parametros['mes'];

				if($proximo_mes <= $mes){
					throw new Exception("El mes no puede ser anterior o igual, al último mes del trimestre", 1);
				}

				$unidades = UnidadResponsable::get()->lists('descripcion','clave');
				$avances_prox_mes = ['componentes'=>[],'actividades'=>[]];

				$query = 'select CMM.idComponente, sum(if(CMM.mes <= ? ,CMM.avance,0)) as avance, sum(CMM.meta) as meta, RAM.mes, RAM.analisisResultados ';
				$query .= 'from componenteMetasMes CMM ';
				$query .= 'left join registroAvancesMetas RAM on RAM.idNivel = CMM.idComponente and RAM.nivel = 1 and RAM.mes = ? and RAM.borradoAl is null ';
				$query .= 'where CMM.borradoAl is null '; //CMM.mes <= ? and
				$query .= 'group by CMM.idComponente ';

				$raw_avances_componentes = DB::select($query,[$proximo_mes,$proximo_mes]);
				
				foreach ($raw_avances_componentes as $avance) {
					$avances_prox_mes['componentes'][$avance->idComponente] = $avance;
				}

				$query = 'select AMM.idActividad, sum(if(AMM.mes <= ? ,AMM.avance,0)) as avance, sum(AMM.meta) as meta, RAM.mes, RAM.analisisResultados ';
				$query .= 'from actividadMetasMes AMM ';
				$query .= 'left join registroAvancesMetas RAM on RAM.idNivel = AMM.idActividad and RAM.nivel = 2 and RAM.mes = ? and RAM.borradoAl is null ';
				$query .= 'where AMM.borradoAl is null '; //AMM.mes <= ? and
				$query .= 'group by AMM.idActividad ';

				$raw_avances_actividades = DB::select($query,[$proximo_mes,$proximo_mes]);
				
				foreach ($raw_avances_actividades as $avance) {
					$avances_prox_mes['actividades'][$avance->idActividad] = $avance;
				}

				foreach ($rows as $row) {
					$row->areaResponsable = $unidades[$row->areaResponsable];

					if($row->nivel == 1){
						$tipo = 'componentes';
					}else{
						$tipo = 'actividades';
					}

					if(isset($avances_prox_mes[$tipo][$row->idNivel])){
						$row->avance = $avances_prox_mes[$tipo][$row->idNivel]->avance;
						$row->meta = $avances_prox_mes[$tipo][$row->idNivel]->meta;
						$row->analisisResultados = $avances_prox_mes[$tipo][$row->idNivel]->analisisResultados;
					}else{
						$row->avance = 0;
						$row->meta = 0;
						$row->analisisResultados = '';
					}

					$porcentaje = 0.00;

					if($row->avance > 0){
						if($row->meta > 0){
							$porcentaje = ($row->avance*100)/$row->meta;
						}
					}

					$row->porcentaje = $porcentaje;

				}

				if(isset($parametros['excel'])){
					Excel::create('SeguimientoPlanMejora', function($excel) use ($rows){
						$excel->sheet('Proyectos', function($sheet)  use ($rows){
					        $sheet->loadView('reportes.excel.seguimiento-plan-mejora', ['data'=>$rows]);

					        $sheet->setColumnFormat(array(
					        	'J3:J'.(count($rows)+3) => '# ##0.00%' 
					        ));
					    });
					    $excel->getActiveSheet()->getStyle('A1:I999')->getAlignment()->setWrapText(true);
						$excel->getActiveSheet()->freezePane('A3');
					})->download('xls');
				}else{

					$query = "select count(*) as conteo ";
					$query .= "FROM registroAvancesMetas RAM ";
					$query .= "LEFT JOIN proyectoComponentes PC on PC.id = RAM.idNivel and RAM.nivel = 1 ";
					$query .= "LEFT JOIN componenteActividades CA on CA.id = RAM.idNivel and RAM.nivel = 2 ";

					$query .= "JOIN proyectos P on P.id = RAM.idProyecto and ejercicio = ? ";
					$query .= "WHERE RAM.planMejora = 1 and RAM.borradoAl is null and RAM.mes = ? ";

					if(isset($parametros['buscar'])){
						$query .= 'and (P.nombreTecnico LIKE "%'.$parametros['buscar'].'%" or PC.indicador LIKE "%'.$parametros['buscar'].'%" or CA.indicador LIKE "%'.$parametros['buscar'].'%"  or concat(unidadResponsable, finalidad, funcion, subfuncion, subsubfuncion, programaSectorial, programaPresupuestario, origenAsignacion, actividadInstitucional, proyectoEstrategico, LPAD(numeroProyectoEstrategico,3,"0")) LIKE "%'.$parametros['buscar'].'%" ) ';
					}

					$query_params = [$ejercicio, $mes];
					
					$total = DB::select($query,$query_params);
					$total = $total[0]->conteo;
					
					$data = array('resultados'=>$total,'data'=>$rows);

					if($total<=0){
						$http_status = 404;
						$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
					}

					return Response::json($data,$http_status);
				}
				*/
			}
		}catch(Exception $ex){
			return Response::json(['data'=>$ex->getMessage(),'line'=>$ex->getLine()],500);
		}
	}

	public function show($id){
		try{
			$parametros = Input::all();

			if(isset($parametros['ejercicio'])){
				$ejercicio = $parametros['ejercicio'];
			}else{
				$ejercicio = Util::obtenerAnioCaptura();
			}

			if($parametros['trimestre'] == 1){
				$mes_actual = 3;
			}else if ($parametros['trimestre'] == 2) {
				$mes_actual = 6;
			}else if ($parametros['trimestre'] == 3) {
				$mes_actual = 9;
			}else if ($parametros['trimestre'] == 4) {
				$mes_actual = 12;
			}

			$recurso = Proyecto::with(array('liderProyecto','responsableInformacion','datosProgramaPresupuestario',
			'componentes.registroAvance'=>function($query) use ($mes_actual){
				$query->where('mes','=',$mes_actual)->orderBy('mes','ASC');
			},'componentes.metasMes'=>function($query){
				$query->orderBy('mes','ASC');
			},'componentes.actividades.registroAvance'=>function($query) use ($mes_actual){
				$query->where('mes','=',$mes_actual)->orderBy('mes','ASC');
			},'componentes.actividades.metasMes'=>function($query){
				$query->orderBy('mes','ASC');
			},'componentes.planMejora'=>function($query) use ($mes_actual){
				$query->where('mes','=',$mes_actual);
			},'componentes.actividades.planMejora'=>function($query) use ($mes_actual){
				$query->where('mes','=',$mes_actual);
			},'analisisFuncional'=>function($query) use ($mes_actual){
				$query->where('mes','=',$mes_actual);
			},'fichasTecnicasEvaluacion'=>function($query) use ($mes_actual){
				$query->where('mes','=',$mes_actual);
			}))->find($id);
			$recurso = $recurso->toArray();

			$proyectos_ep01 = Proyecto::fuentesFinanciamientoEP01($mes_actual,$ejercicio)->where('proyectos.id','=',$id)->get();

			$datos = array();

			$datos['proyecto'] = array(
				'id'=>$recurso['id'],
				'nombre'=>$recurso['nombreTecnico'],
				'programa'=>$recurso['datos_programa_presupuestario']['clave'] . ' - ' . $recurso['datos_programa_presupuestario']['descripcion'],
				'clave'=>$recurso['ClavePresupuestaria'],
				'finalidad'=>$recurso['analisis_funcional'][0]['finalidadProyecto'],
				'evaluacion'=>$recurso['analisis_funcional'][0]['analisisResultado'],
				'presupuesto_autorizado'=>0.00,
				'presupuesto_modificado'=>0.00,
				'presupuesto_devengado'=>0.00,
				'fuente_financiamiento'=>'',
				'componentes' => array(),
				'variable' => array('tipo'=>'NULL'),
				'promedio_alcanzado' => 0.00
			);

			if(count($recurso['fichas_tecnicas_evaluacion'])){
				$datos['proyecto']['variable'] = array(
					'tipo' => ($recurso['fichas_tecnicas_evaluacion'][0]['nivel'] == 1)?'C':'A',
					'id' => $recurso['fichas_tecnicas_evaluacion'][0]['idNivel'],
					'recomendacion' => $recurso['fichas_tecnicas_evaluacion'][0]['recomendacion'],
					'datos' => array()
				);
			}
	
			foreach($proyectos_ep01 as $proyecto_ff){
				$datos['proyecto']['fuente_financiamiento'] .= $proyecto_ff->clave . ' - ' . $proyecto_ff->descripcion . ', ';
				$datos['proyecto']['presupuesto_autorizado'] += $proyecto_ff->presupuestoAprobado;
				$datos['proyecto']['presupuesto_modificado'] += $proyecto_ff->presupuestoModificado;
				$datos['proyecto']['presupuesto_devengado'] += $proyecto_ff->presupuestoDevengado;
			}
	
			$total_elementos = 0;
			$nivel_componente = 1;
			$promedio_suma = 0;
			$promedio_total_elementos = 0;
			foreach($recurso['componentes'] as $componente){
				$total_elementos += 1;
				$nuevo_componente = array(
					'id' => $componente['id'],
					'nivel' => $nivel_componente,
					'indicador' => $componente['indicador'],
					'numerador' => $componente['numerador'],
					'denominador' => $componente['denominador'],
					'programado_numerador' => $componente['valorNumerador'],
					'programado_denominador' => $componente['valorDenominador'],
					'alcanzado' => 0.00,
					'programado' => 0.00,
					'porcentaje_meta' => 0.00,
					'porcentaje_anual' => 0.00,
					'tiene_plan_mejora' => (count($componente['plan_mejora']))?true:false,
					'auxiliar_fondo' => '',
					'auxiliar_color' => '',
					'plan_mejora' => $componente['plan_mejora'],
					'registro_avance' => $componente['registro_avance'],
					'actividades' => array()
				);
	
				foreach($componente['metas_mes'] as $meta){
					if($meta['mes'] <= $mes_actual){
						$nuevo_componente['alcanzado'] += $meta['avance'];
						$nuevo_componente['programado'] += $meta['meta'];
					}
				}
	
				if($nuevo_componente['programado'] > 0){
					$nuevo_componente['porcentaje_meta'] = ($nuevo_componente['alcanzado'] / $nuevo_componente['programado'])*100;
					$promedio_suma += $nuevo_componente['porcentaje_meta'];
					$promedio_total_elementos += 1;
				}else{
					$nuevo_componente['porcentaje_meta'] = $nuevo_componente['alcanzado']*100;
				}

				$nuevo_componente['porcentaje_anual'] = ($nuevo_componente['alcanzado'] / $nuevo_componente['programado_numerador'])*100;
	
				if($nuevo_componente['tiene_plan_mejora'] == '1'){
					if($nuevo_componente['porcentaje_meta'] < 50){
						$nuevo_componente['auxiliar_fondo'] = '#FF0000';
						$nuevo_componente['auxiliar_color'] = '#FFFFFF';
					}else if($nuevo_componente['porcentaje_meta'] < 90){
						$nuevo_componente['auxiliar_fondo'] = '#FFFF00';
						$nuevo_componente['auxiliar_color'] = '#000000';
					}else{
						$nuevo_componente['auxiliar_fondo'] = '#F79646';
						$nuevo_componente['auxiliar_color'] = '#000000';
					}
				}else{
					$nuevo_componente['auxiliar_fondo'] = '#00B050';
					$nuevo_componente['auxiliar_color'] = '#000000';
				}
				
				if($datos['proyecto']['variable']['tipo'] == 'C' && $datos['proyecto']['variable']['id'] == $nuevo_componente['id']){
					$datos['proyecto']['variable']['datos'] = $nuevo_componente;
				}

				$nivel_actividad = 1;
				foreach($componente['actividades'] as $actividad){
					$total_elementos += 1;
					$nueva_actividad = array(
						'id' => $actividad['id'],
						'nivel' => $nivel_componente . '.' . $nivel_actividad,
						'indicador' => $actividad['indicador'],
						'numerador' => $actividad['numerador'],
						'denominador' => $actividad['denominador'],
						'programado_numerador' => $actividad['valorNumerador'],
						'programado_denominador' => $actividad['valorDenominador'],
						'alcanzado' => 0.00,
						'programado' => 0.00,
						'porcentaje_meta' => 0.00,
						'porcentaje_anual' => 0.00,
						'tiene_plan_mejora' => (count($actividad['plan_mejora']))?true:false,
						'auxiliar_fondo' => '',
						'auxiliar_color' => '',
						'plan_mejora' => $actividad['plan_mejora'],
						'registro_avance' => $actividad['registro_avance']
					);
	
					foreach($actividad['metas_mes'] as $meta){
						if($meta['mes'] <= $mes_actual){
							$nueva_actividad['alcanzado'] += $meta['avance'];
							$nueva_actividad['programado'] += $meta['meta'];
						}
					}
	
					if($nueva_actividad['programado'] > 0){
						$nueva_actividad['porcentaje_meta'] = ($nueva_actividad['alcanzado'] / $nueva_actividad['programado'])*100;
						$promedio_suma += $nueva_actividad['porcentaje_meta'];
						$promedio_total_elementos += 1;
					}else{
						$nueva_actividad['porcentaje_meta'] = $nueva_actividad['alcanzado']*100;
					}

					$nueva_actividad['porcentaje_anual'] = ($nueva_actividad['alcanzado'] / $nueva_actividad['programado_numerador'])*100;

					if($nueva_actividad['tiene_plan_mejora'] == '1'){
						if($nueva_actividad['porcentaje_meta'] < 50){
							$nueva_actividad['auxiliar_fondo'] = '#FF0000';
							$nueva_actividad['auxiliar_color'] = '#FFFFFF';
						}else if($nueva_actividad['porcentaje_meta'] < 90){
							$nueva_actividad['auxiliar_fondo'] = '#FFFF00';
							$nueva_actividad['auxiliar_color'] = '#000000';
						}else{
							$nueva_actividad['auxiliar_fondo'] = '#F79646';
							$nueva_actividad['auxiliar_color'] = '#000000';
						}
					}else{
						$nueva_actividad['auxiliar_fondo'] = '#00B050';
						$nueva_actividad['auxiliar_color'] = '#000000';
					}

					if($datos['proyecto']['variable']['tipo'] == 'A' && $datos['proyecto']['variable']['id'] == $nueva_actividad['id']){
						$datos['proyecto']['variable']['datos'] = $nueva_actividad;
					}
	
					$nuevo_componente['actividades'][] = $nueva_actividad;
					$nivel_actividad += 1;
				}
	
				$datos['proyecto']['componentes'][] = $nuevo_componente;
				$nivel_componente += 1;
			}
	
			$datos['proyecto']['promedio_alcanzado'] = $promedio_suma / $promedio_total_elementos;

			return Response::json(array('datos'=>$datos),200);
		}catch(Exception $ex){
			return Response::json(['data'=>$ex->getMessage(),'line'=>$ex->getLine()],500);
		}
	}

	public function excel($id){
		$parametros = Input::all();

		if(isset($parametros['ejercicio'])){
			$ejercicio = $parametros['ejercicio'];
		}else{
			$ejercicio = Util::obtenerAnioCaptura();
		}

		if($parametros['trimestre'] == 1){
			$mes_actual = 3;
		}else if ($parametros['trimestre'] == 2) {
			$mes_actual = 6;
		}else if ($parametros['trimestre'] == 3) {
			$mes_actual = 9;
		}else if ($parametros['trimestre'] == 4) {
			$mes_actual = 12;
		}

		$recurso = Proyecto::with(array('liderProyecto','responsableInformacion','coordinadorGrupoEstrategico','datosProgramaPresupuestario',
		//'datosFuncion','datosSubFuncion','objetivoPedCompleto',
		/*'componentes.desgloseConDatos.metasMes'=>function($query) use ($mes_actual){
			$query->where('mes','=',$mes_actual)->orderBy('mes','ASC');
		},'componentes.desgloseConDatos.metasMesAcumuladas'=>function($query) use ($mes_actual){
			$query->where('mes','<',$mes_actual)->orderBy('mes','ASC');
		},'componentes.actividades.desgloseConDatos.metasMes'=>function($query) use ($mes_actual){
			$query->where('mes','=',$mes_actual)->orderBy('mes','ASC');
		},'componentes.actividades.desgloseConDatos.metasMesAcumuladas'=>function($query) use ($mes_actual){
			$query->where('mes','<',$mes_actual)->orderBy('mes','ASC');
		},*/
		'componentes.registroAvance'=>function($query) use ($mes_actual){
			$query->where('mes','=',$mes_actual)->orderBy('mes','ASC');
		},'componentes.metasMes'=>function($query){
			$query->orderBy('mes','ASC');
		},'componentes.actividades.registroAvance'=>function($query) use ($mes_actual){
			$query->where('mes','=',$mes_actual)->orderBy('mes','ASC');
		},'componentes.actividades.metasMes'=>function($query){
			$query->orderBy('mes','ASC');
		},'componentes.planMejora'=>function($query) use ($mes_actual){
			$query->where('mes','=',$mes_actual)->orderBy('mes','ASC');
		},'componentes.actividades.planMejora'=>function($query) use ($mes_actual){
			$query->where('mes','=',$mes_actual)->orderBy('mes','ASC');
		},'analisisFuncional'=>function($query) use ($mes_actual){
			$query->where('mes','=',$mes_actual)->orderBy('mes','ASC');
		},'fichasTecnicasEvaluacion'=>function($query) use ($mes_actual){
			$query->where('mes','=',$mes_actual);
		}))->find($id);
		$recurso = $recurso->toArray();

		$proyectos_ep01 = Proyecto::fuentesFinanciamientoEP01($mes_actual,$ejercicio)->where('proyectos.id','=',$id)->get();

		//return Response::json(['data'=>$presupuesto_proyecto],200);
		//return Response::json(['data'=>$recurso],200);

		$datos = array();

		$meses = array(
			1 => array('mes'=>'Enero',			'abrev'=>'ENE',	'trimestre'=>1, 'trimestre_letras'=>'1er'),
			2 => array('mes'=>'Febrero',		'abrev'=>'FEB',	'trimestre'=>1, 'trimestre_letras'=>'1er'),
			3 => array('mes'=>'Marzo',			'abrev'=>'MAR',	'trimestre'=>1, 'trimestre_letras'=>'1er'),
			4 => array('mes'=>'Abril',			'abrev'=>'ABR',	'trimestre'=>2, 'trimestre_letras'=>'2do'),
			5 => array('mes'=>'Mayo',			'abrev'=>'MAy',	'trimestre'=>2, 'trimestre_letras'=>'2do'),
			6 => array('mes'=>'Junio',			'abrev'=>'JUN',	'trimestre'=>2, 'trimestre_letras'=>'2do'),
			7 => array('mes'=>'Julio',			'abrev'=>'JUL',	'trimestre'=>3, 'trimestre_letras'=>'3er'),
			8 => array('mes'=>'Agosto',			'abrev'=>'AGO',	'trimestre'=>3, 'trimestre_letras'=>'3er'),
			9 => array('mes'=>'Septiembre',		'abrev'=>'SEP',	'trimestre'=>3, 'trimestre_letras'=>'3er'),
			10 => array('mes'=>'Octubre',		'abrev'=>'OCT',	'trimestre'=>4, 'trimestre_letras'=>'4to'),
			11 => array('mes'=>'Noviembre',		'abrev'=>'NOV',	'trimestre'=>4, 'trimestre_letras'=>'4to'),
			12 => array('mes'=>'Diciembre',		'abrev'=>'DIC',	'trimestre'=>4, 'trimestre_letras'=>'4to')
		);
		
		$datos['mes'] = $meses[intval($mes_actual)];
		$datos['proyecto'] = array(
			'lider_proyecto'=>$recurso['lider_proyecto'],
			'responsable_informacion'=>$recurso['responsable_informacion'],
			'coordinador_grupo_estrategico'=>$recurso['coordinador_grupo_estrategico'],
			'subcoordinador_grupo_estrategico'=>array('nombre'=>'Dr. Carlos Díaz Jiménez','cargo'=>'Subdirector de Planeación en Salud'),
			'nombre'=>$recurso['nombreTecnico'],
			'programa'=>$recurso['datos_programa_presupuestario']['clave'] . ' - ' . $recurso['datos_programa_presupuestario']['descripcion'],
			'clave'=>$recurso['ClavePresupuestaria'],
			'finalidad'=>$recurso['analisis_funcional'][0]['finalidadProyecto'],
			'evaluacion'=>$recurso['analisis_funcional'][0]['analisisResultado'],
			'presupuesto_autorizado'=>0.00,
			'presupuesto_modificado'=>0.00,
			'presupuesto_devengado'=>0.00,
			'fuente_financiamiento'=>'',
			'componentes' => array(),
			'variable' => array('id'=>null, 'tipo'=>null),
			'promedio_alcanzado' => 0.00,
			'promedio_auxiliar_fondo'=>'',
			'promedio_auxiliar_color'=>''
		);

		foreach($proyectos_ep01 as $proyecto_ff){
			$datos['proyecto']['fuente_financiamiento'] .= $proyecto_ff->clave . ' - ' . $proyecto_ff->descripcion . ', ';
			$datos['proyecto']['presupuesto_autorizado'] += $proyecto_ff->presupuestoAprobado;
			$datos['proyecto']['presupuesto_modificado'] += $proyecto_ff->presupuestoModificado;
			$datos['proyecto']['presupuesto_devengado'] += $proyecto_ff->presupuestoDevengado;
		}

		if(count($recurso['fichas_tecnicas_evaluacion'])){
			$datos['proyecto']['variable'] = array(
				'tipo' => ($recurso['fichas_tecnicas_evaluacion'][0]['nivel'] == 1)?'C':'A',
				'id' => $recurso['fichas_tecnicas_evaluacion'][0]['idNivel'],
				'recomendacion' => $recurso['fichas_tecnicas_evaluacion'][0]['recomendacion'],
				'datos' => array()
			);
		}

		$total_elementos = 0;
		$nivel_componente = 1;
		$auxiliar_formula = 27;
		$promedio_suma = 0;
		$promedio_total_elementos = 0;
		foreach($recurso['componentes'] as $componente){
			$total_elementos += 1;
			$nuevo_componente = array(
				'id' => $componente['id'],
				'nivel' => $nivel_componente,
				'indicador' => $componente['indicador'],
				'numerador' => $componente['numerador'],
				'denominador' => $componente['denominador'],
				'programado_numerador' => $componente['valorNumerador'],
				'programado_denominador' => $componente['valorDenominador'],
				'alcanzado' => 0.00,
				'programado' => 0.00,
				'porcentaje_meta' => 0.00,
				'tiene_plan_mejora' => (count($componente['plan_mejora']))?true:false,
				'auxiliar_formula' => $auxiliar_formula,
				'auxiliar_color' => '',
				'actividades' => array()
			);

			foreach($componente['metas_mes'] as $meta){
				if($meta['mes'] <= $mes_actual){
					$nuevo_componente['alcanzado'] += $meta['avance'];
					$nuevo_componente['programado'] += $meta['meta'];
				}
			}

			if($nuevo_componente['programado'] > 0){
				$nuevo_componente['porcentaje_meta'] = $nuevo_componente['alcanzado'] / $nuevo_componente['programado'];
				$promedio_suma += $nuevo_componente['porcentaje_meta'];
				$promedio_total_elementos += 1;
			}else{
				$nuevo_componente['porcentaje_meta'] = $nuevo_componente['alcanzado']*100;
			}

			if($nuevo_componente['tiene_plan_mejora'] == '1'){
				if($nuevo_componente['porcentaje_meta'] < 0.50){
					$nuevo_componente['auxiliar_fondo'] = '#FF0000';
					$nuevo_componente['auxiliar_color'] = '#FFFFFF';
				}else if($nuevo_componente['porcentaje_meta'] < 0.90){
					$nuevo_componente['auxiliar_fondo'] = '#FFFF00';
					$nuevo_componente['auxiliar_color'] = '#000000';
				}else{
					$nuevo_componente['auxiliar_fondo'] = '#F79646';
					$nuevo_componente['auxiliar_color'] = '#000000';
				}
			}else{
				$nuevo_componente['auxiliar_fondo'] = '#00B050';
				$nuevo_componente['auxiliar_color'] = '#000000';
			}

			if($datos['proyecto']['variable']['tipo'] == 'C' && $datos['proyecto']['variable']['id'] == $nuevo_componente['id']){
				$datos['proyecto']['variable']['datos'] = $nuevo_componente;
				$datos['proyecto']['variable']['datos']['plan_mejora'] = $componente['plan_mejora'][0];
				$datos['proyecto']['variable']['datos']['registro_avance'] = $componente['registro_avance'][0];
			}

			$nivel_actividad = 1;
			foreach($componente['actividades'] as $actividad){
				$auxiliar_formula += 2;
				$total_elementos += 1;
				$nueva_actividad = array(
					'id' => $actividad['id'],
					'nivel' => $nivel_componente . '.' . $nivel_actividad,
					'indicador' => $actividad['indicador'],
					'numerador' => $actividad['numerador'],
					'denominador' => $actividad['denominador'],
					'programado_numerador' => $actividad['valorNumerador'],
					'programado_denominador' => $actividad['valorDenominador'],
					'alcanzado' => 0.00,
					'programado' => 0.00,
					'porcentaje_meta' => 0.00,
					'tiene_plan_mejora' => (count($actividad['plan_mejora']))?true:false,
					'auxiliar_formula' => $auxiliar_formula,
					'auxiliar_color' => ''
				);

				foreach($actividad['metas_mes'] as $meta){
					if($meta['mes'] <= $mes_actual){
						$nueva_actividad['alcanzado'] += $meta['avance'];
						$nueva_actividad['programado'] += $meta['meta'];
					}
				}

				if($nueva_actividad['programado'] > 0){
					$nueva_actividad['porcentaje_meta'] = $nueva_actividad['alcanzado'] / $nueva_actividad['programado'];
					$promedio_suma += $nueva_actividad['porcentaje_meta'];
					$promedio_total_elementos += 1;
				}else{
					$nueva_actividad['porcentaje_meta'] = $nueva_actividad['alcanzado']*100;
				}

				if($nueva_actividad['tiene_plan_mejora'] == '1'){
					if($nueva_actividad['porcentaje_meta'] < 0.50){
						$nueva_actividad['auxiliar_fondo'] = '#FF0000';
						$nueva_actividad['auxiliar_color'] = '#FFFFFF';
					}else if($nueva_actividad['porcentaje_meta'] < 0.90){
						$nueva_actividad['auxiliar_fondo'] = '#FFFF00';
						$nueva_actividad['auxiliar_color'] = '#000000';
					}else{
						$nueva_actividad['auxiliar_fondo'] = '#F79646';
						$nueva_actividad['auxiliar_color'] = '#000000';
					}
				}else{
					$nueva_actividad['auxiliar_fondo'] = '#00B050';
					$nueva_actividad['auxiliar_color'] = '#000000';
				}

				if($datos['proyecto']['variable']['tipo'] == 'A' && $datos['proyecto']['variable']['id'] == $nueva_actividad['id']){
					$datos['proyecto']['variable']['datos'] = $nueva_actividad;
					$datos['proyecto']['variable']['datos']['plan_mejora'] = $actividad['plan_mejora'][0];
					$datos['proyecto']['variable']['datos']['registro_avance'] = $actividad['registro_avance'][0];
				}

				$nuevo_componente['actividades'][] = $nueva_actividad;
				$nivel_actividad += 1;
			}

			$datos['proyecto']['componentes'][] = $nuevo_componente;
			$nivel_componente += 1;
			$auxiliar_formula += 2;
		}

		$datos['proyecto']['promedio_alcanzado'] = $promedio_suma / $promedio_total_elementos;

		if($datos['proyecto']['promedio_alcanzado'] < 0.50){
			$datos['proyecto']['promedio_auxiliar_fondo'] = '#FF0000';
			$datos['proyecto']['promedio_auxiliar_color'] = '#FFFFFF';
		}else if($datos['proyecto']['promedio_alcanzado'] < 0.90){
			$datos['proyecto']['promedio_auxiliar_fondo'] = '#FFFF00';
			$datos['proyecto']['promedio_auxiliar_color'] = '#000000';
		}else if($datos['proyecto']['promedio_alcanzado'] > 1.10){ 
			$datos['proyecto']['promedio_auxiliar_fondo'] = '#F79646';
			$datos['proyecto']['promedio_auxiliar_color'] = '#000000';
		}else{
			$datos['proyecto']['promedio_auxiliar_fondo'] = '#00B050';
			$datos['proyecto']['promedio_auxiliar_color'] = '#000000';
		}
		

		$datos['auxiliar'] = array();
		$datos['auxiliar']['total_componentes'] = $total_elementos;
		//$datos['auxiliar']['total_variables'] = count($datos['proyecto']['variables']);

		Excel::create('fichaTecnicaDeEvaluacion', function($excel) use ( $datos ){
			$excel->sheet('ficha', function($sheet) use ( $datos ){
				$sheet->loadView('reportes.excel.ficha-tecnica-evaluacion',$datos);

				for($i = 7 ; $i <= 10 ; $i++){
					$sheet->getStyle('D'.$i.':R'.$i)->applyFromArray(array(
						'borders' => array(
							'bottom' => array(
								'style' => \PHPExcel_Style_Border::BORDER_THIN,
								'color' => array('argb' => '000000')
							)
						)
					));
				}

				$sheet->mergeCells('A15:G16');
				$sheet->mergeCells('A20:G21');

				$total_componentes = intval($datos['auxiliar']['total_componentes']);

				$row_1 = 27;
				$row_2 = 28;
				for ($i=0; $i < $total_componentes ; $i++) { 
					$sheet->mergeCells('B'.$row_1.':E'.$row_2);
					$row_1 += 2;
					$row_2 += 2;
				}

				$sheet->setColumnFormat(array(
					'C12' => '"$" #,##0.00_-',
					'H12' => '"$" #,##0.00_-',
					'L12' => '"$" #,##0.00_-',
					'H16' => '0.00%',
					'L22' => '0.00%',
					'R22' => '0.00%',
					'H22:K22' => '#,##0.00_-',
					'M22:Q22' => '#,##0.00_-',
					'O27'.':P'.((26)+($total_componentes*2)) => '#,##0.00_-',
					'Q27'.':R'.((26)+($total_componentes*2)) => '0.00%'
				));

				$sheet->getStyle('H16')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
				$sheet->getStyle('H16')->getFill()->getStartColor()->setARGB('FF00B050');

				/*
				$condicionBajoAvance = new \PHPExcel_Style_Conditional();
				$condicionBajoAvance->setConditionType(\PHPExcel_Style_Conditional::CONDITION_CELLIS)->setOperatorType(\PHPExcel_Style_Conditional::OPERATOR_LESSTHAN)->addCondition('.90');
				$condicionBajoAvance->getStyle()->getFont()->getColor()->setRGB('FF0000');
				$condicionBajoAvance->getStyle()->getFont()->setBold(true);
				//$condicionBajoAvance->getStyle()->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
				//$condicionBajoAvance->getStyle()->getFill()->getStartColor()->setARGB('FFFF0000');

				$condicionAltoAvance = new \PHPExcel_Style_Conditional();
				$condicionAltoAvance->setConditionType(\PHPExcel_Style_Conditional::CONDITION_CELLIS)->setOperatorType(\PHPExcel_Style_Conditional::OPERATOR_GREATERTHAN)->addCondition('1.10');
				$condicionAltoAvance->getStyle()->getFont()->getColor()->setRGB('FF0000');
				$condicionAltoAvance->getStyle()->getFont()->setBold(true);
				//$condicionAltoAvance->getStyle()->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
				//$condicionAltoAvance->getStyle()->getFill()->getStartColor()->setARGB('FFFF0000');

				$conditionalStyles = $sheet->getStyle('R27')->getConditionalStyles();
				array_push($conditionalStyles, $condicionBajoAvance);
				array_push($conditionalStyles, $condicionAltoAvance);
				$sheet->getStyle('R27:R'.(27+($total_componentes*2)))->setConditionalStyles($conditionalStyles);

				$sheet->getStyle('R27:R'.(27+($total_componentes*2)))->getFont()->getColor()->setRGB('00B050');
				$sheet->getStyle('R27:R'.(27+($total_componentes*2)))->getFont()->setBold(true);

				//$sheet->getStyle('H16')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
				//$sheet->getStyle('H16')->getFill()->getStartColor()->setARGB('FFFF0000');
				$conditionalStyles = $sheet->getStyle('H16')->getConditionalStyles();
				array_push($conditionalStyles, $condicionBajoAvance);
				array_push($conditionalStyles, $condicionAltoAvance);
				$sheet->getStyle('H16')->setConditionalStyles($conditionalStyles);
				*/

				//$sheet->getStyle('H16')->getFont()->getColor()->setRGB('00B050');
				//$sheet->getStyle('H16')->getFont()->setBold(true);
				/*
				$sheet->mergeCells('A1:I1');
				$sheet->mergeCells('A2:I2');
				$sheet->mergeCells('A3:I3');
				$sheet->mergeCells('A4:I4');
				$sheet->mergeCells('A5:I5');

				$sheet->getStyle('A6:I6')->applyFromArray(array(
					'borders' => array(
						'bottom' => array(
							'style' => \PHPExcel_Style_Border::BORDER_THICK,'color' => array('argb' => 'FF6600')
						)
					)
				));

				$sheet->mergeCells('A8:A10');
				$sheet->mergeCells('B8:D8');
				$sheet->mergeCells('B9:B10');
				$sheet->mergeCells('C9:C10');
				$sheet->mergeCells('D9:D10');
				$sheet->mergeCells('F8:I8');
				$sheet->mergeCells('F9:F10');
				$sheet->mergeCells('G9:G10');
				$sheet->mergeCells('H9:H10');
				$sheet->mergeCells('I9:I10');

				$sheet->cells('A1:I10',function($cells) { $cells->setAlignment('center'); });
				$sheet->getStyle('A1:I10')->getAlignment()->setWrapText(true);
				$sheet->getStyle('A8:I10')->applyFromArray(array(
					'fill' => array(
						'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb' => '00B550')
					),
					'font' => array(
						'size'      =>  9,
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
				$total = count($datos['datos']);
				$sheet->getStyle('A12:I'.($total+11))->getAlignment()->setWrapText(true);
				$sheet->setColumnFormat(array( 'B12:I'.($total+15) => '### ### ### ##0.00' ));

				$sheet->getStyle('D'.($total+15))->applyFromArray(array(
					'borders' => array(
						'bottom' => array(
							'style' => \PHPExcel_Style_Border::BORDER_DOUBLE,
							'color' => array('argb' => '000000')
						)
					)
				));

				$sheet->getStyle('I'.($total+15))->applyFromArray(array(
					'borders' => array(
						'bottom' => array(
							'style' => \PHPExcel_Style_Border::BORDER_DOUBLE,
							'color' => array('argb' => '000000')
						)
					)
				));

				$sheet->getStyle('A'.($total+16).':I'.($total+16).'')->applyFromArray(array(
					'borders' => array(
						'bottom' => array(
							'style' => \PHPExcel_Style_Border::BORDER_THIN,
							'color' => array('argb' => '000000')
						)
					)
				));

				$linea_firmas = $total+19;

				$sheet->getStyle('B'.($linea_firmas-1).':D'.($linea_firmas-1).'')->applyFromArray(array(
					'borders' => array(
						'bottom' => array(
							'style' => \PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '000000')
						)
					)
				));

				$sheet->getStyle('F'.($linea_firmas-1).':H'.($linea_firmas-1).'')->applyFromArray(array(
					'borders' => array(
						'bottom' => array(
							'style' => \PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => '000000')
						)
					)
				));

				$sheet->mergeCells('B'.$linea_firmas.':D'.$linea_firmas.'');
				$sheet->mergeCells('B'.($linea_firmas+1).':D'.($linea_firmas+1).'');
				$sheet->mergeCells('F'.$linea_firmas.':H'.$linea_firmas.'');
				$sheet->mergeCells('F'.($linea_firmas+1).':H'.($linea_firmas+1).'');
				$sheet->cells('A'.$linea_firmas.':I'.($linea_firmas+1),function($cells) { 
					$cells->setAlignment('center'); 
				});

				*/

				$imagen = $this->obtenerImagen('LogoInstitucional.png','A1');
				$imagen->setWorksheet($sheet);
				$imagen = $this->obtenerImagen('EscudoGobiernoChiapas.png','Q1',(-15));
				$imagen->setWorksheet($sheet);
			});
			$ultima_linea = $excel->getActiveSheet()->getHighestDataRow();
			$excel->getActiveSheet()->getStyle('A1:R'.$ultima_linea)->applyFromArray(
				array( 'font' => array( 'name'=> 'Arial' ) )
			);
			$excel->getActiveSheet()->getStyle('A1:R'.$ultima_linea)->getAlignment()->setWrapText(true);

			$excel->getActiveSheet()->getPageSetup()->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);
			$excel->getActiveSheet()->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

			$excel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);

			$excel->getActiveSheet()->getPageMargins()->setTop(0.3543307);
			$excel->getActiveSheet()->getPageMargins()->setRight(0);
			$excel->getActiveSheet()->getPageMargins()->setLeft(0.1968504);
			$excel->getActiveSheet()->getPageMargins()->setBottom(0.3543307);
			$excel->getActiveSheet()->getPageMargins()->setHeader(0.3149606);
			$excel->getActiveSheet()->getPageMargins()->setFooter(0.3149606);
		})->export('xlsx');
	}

	private function obtenerImagen($imagen,$celda,$offset = 10){
		$objDrawing = new \PHPExcel_Worksheet_Drawing();
		$objDrawing->setPath('./img/'.$imagen);// filesystem reference for the image file
		$objDrawing->setHeight(60);// sets the image height to 36px (overriding the actual image height); 
		$objDrawing->setWidth(120);// sets the image height to 36px (overriding the actual image height); 
		$objDrawing->setCoordinates($celda);// pins the top-left corner of the image to cell D24
		$objDrawing->setOffsetX($offset);// pins the top left corner of the image at an offset of 10 points horizontally to the right of the top-left corner of the cell
		return $objDrawing;
	}

	public function update($id){
		$respuesta['http_status'] = 200;
		$respuesta['data'] = array("data"=>'');
		
		try{
			$usuario = Sentry::getUser();

			$parametros = Input::all();

			if($parametros['trimestre'] == 1){
				$mes_actual = 3;
			}else if ($parametros['trimestre'] == 2) {
				$mes_actual = 6;
			}else if ($parametros['trimestre'] == 3) {
				$mes_actual = 9;
			}else if ($parametros['trimestre'] == 4) {
				$mes_actual = 12;
			}

			$ficha = FichaTecnicaEvaluacion::where('idProyecto','=',$id)->where('mes','=',$mes_actual)->first();

			if(!$ficha){
				$ficha = new FichaTecnicaEvaluacion();
			}
			
			$ficha->idProyecto = $id;
			$ficha->idNivel = $parametros['variable_id'];
			$ficha->nivel = ($parametros['tipo'] == 'C')?1:2;
			$ficha->idPlanMejora = $parametros['id_plan'];
			$ficha->mes = $mes_actual;
			$ficha->recomendacion = $parametros['recomendacion'];

			$ficha->save();
			
			$respuesta['data']['data'] = $parametros;
		}catch(Exception $e){
			$respuesta['http_status'] = 500;
			$respuesta['data'] = array("data"=>"Ocurrió un error en el servidor, intente de nuevo mas tarde o pongase en contacto con el administrador del sistema.",'ex'=>$e->getMessage(),'line'=>$e->getLine(),'code'=>'S02');
		}
		return Response::json($respuesta['data'],$respuesta['http_status']);
	}
}