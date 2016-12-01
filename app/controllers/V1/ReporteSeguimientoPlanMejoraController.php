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
use UnidadResponsable;

class ReporteSeguimientoPlanMejoraController extends BaseController {
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

				$query = "select RAM.nivel, RAM.idNivel, RAM.mes, IF(RAM.nivel=1,PC.indicador,CA.indicador) as indicador, RAM.analisisResultados as actividades, P.unidadResponsable as areaResponsable, EPM.grupoTrabajo, EPM.fechaNotificacion, EPM.accionMejora ";
				$query .= "FROM registroAvancesMetas RAM ";
				$query .= "JOIN evaluacionPlanMejora EPM on EPM.idNivel = RAM.idNivel and EPM.nivel = RAM.nivel and EPM.mes = RAM.mes and EPM.borradoAl is null ";
				$query .= "LEFT JOIN proyectoComponentes PC on PC.id = RAM.idNivel and RAM.nivel = 1 ";
				$query .= "LEFT JOIN componenteActividades CA on CA.id = RAM.idNivel and RAM.nivel = 2 ";

				$query .= "JOIN proyectos P on P.id = EPM.idProyecto and ejercicio = ? ";
				$query .= "WHERE RAM.planMejora = 1 and RAM.borradoAl is null and RAM.mes = ? ";
				$query .= "ORDER BY P.unidadResponsable ";

				$query_params = [$ejercicio, $mes];

				if(!isset($parametros['excel'])){
					if($skip == 0){
						$query .= " LIMIT 10 ";
					}else{
						$query .= " LIMIT ".$skip.", 10 ";
					}
				}
				
				$rows = DB::select($query, $query_params);

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

					        /*$sheet->setColumnFormat(array( 
					        	'C5:D999' => '### ### ### ##0.00', 
					        	'E5:E999' => '# ##0.00%' 
					        ));*/
					    });
					    $excel->getActiveSheet()->getStyle('A1:I999')->getAlignment()->setWrapText(true);

					    /*$excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(false);
						$excel->getActiveSheet()->getColumnDimension('A')->setWidth("40");
						$excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(false);
						$excel->getActiveSheet()->getColumnDimension('B')->setWidth("40");*/

						$excel->getActiveSheet()->freezePane('A3');
					})->download('xls');
				}else{
					$total = DB::select('select count(*) as conteo from registroAvancesMetas where planMejora = 1 and borradoAl is null and mes = ?',[$mes]);
					$total = $total[0]->conteo;
					
					$data = array('resultados'=>$total,'data'=>$rows);

					if($total<=0){
						$http_status = 404;
						$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
					}

					return Response::json($data,$http_status);
				}
			}
		}catch(Exception $ex){
			return Response::json(['data'=>$ex->getMessage(),'line'=>$ex->getLine()],500);
		}
	}
}