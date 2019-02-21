<?php
/* 
*	SIRE
*	Sistema de Integración, Rendición de cuentas y Evaluación
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
use Excel, Estrategia;

class ReporteSeguimientoEstrategiaController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		//
		/*$parametros = Input::all();
		$datos = array();

		$mes_actual = Util::obtenerMesActual();
		$ejercicio = Util::obtenerAnioCaptura();

		$datos['ejercicio'] = $ejercicio;

		$rows = Estrategia::getModel();
		$rows = $rows->where('idEstatus','=',5);
					//->where('idClasificacionProyecto','=',$parametros['clasificacion-proyecto']);
		//

		if($mes_actual == 0){
			$mes_actual = date('n') - 1;
		}
		
		$rows = $rows->leftjoin('evaluacionProyectoMes', function($join) use($mes_actual){
							$join->on('proyectos.id', '=', 'evaluacionProyectoMes.idProyecto')
							->where('evaluacionProyectoMes.mes', '=', $mes_actual);
						});

		$usuario = Sentry::getUser();
		
		if($usuario->filtrarProyectos){
			$rows = $rows->where('idUsuarioValidacionSeg','=',$usuario->id);
		}

		if($usuario->claveUnidad){
			$unidades = explode('|',$usuario->claveUnidad);
			$rows = $rows->whereIn('unidadResponsable',$unidades);
		}

		$rows = $rows->with(array('componentesMetasMes'=>function($query)use($mes_actual){
							$query->select('id','idProyecto',DB::raw('sum(meta) AS totalMeta'))
							->where('mes','=',$mes_actual)
							->groupBy('idProyecto','mes');
						},'actividadesMetasMes'=>function($query)use($mes_actual){
							$query->select('id','idProyecto',DB::raw('sum(meta) AS totalMeta'))
							->where('mes','=',$mes_actual)
							->groupBy('idProyecto','mes');
						}));

		$rows = $rows->select('proyectos.id','nombreTecnico','catalogoClasificacionProyectos.descripcion AS clasificacionProyecto',
			DB::raw('CONCAT_WS(" ",datosRevisor.nombres,datosRevisor.apellidoPaterno,datosRevisor.apellidoMaterno) AS nombreRevisor'),
			DB::raw('CONCAT_WS(" ",datosEnlace.nombres,datosEnlace.apellidoPaterno,datosEnlace.apellidoMaterno) AS nombreEnlace'),
				'catalogoUnidadesResponsables.descripcion AS descripcionUnidadResponsable',
				'catalogoEstatusProyectos.descripcion AS estatusAvance',
				'unidadResponsable','finalidad','funcion','subFuncion','subSubFuncion','programaSectorial',
				'programaPresupuestario','origenAsignacion','actividadInstitucional','proyectoEstrategico',
				'numeroProyectoEstrategico')
				->leftjoin('sentryUsers AS datosRevisor','datosRevisor.id','=','proyectos.idUsuarioValidacionSeg')
				->leftjoin('sentryUsers AS datosEnlace','datosEnlace.id','=','proyectos.idUsuarioRendCuenta')
				->join('catalogoClasificacionProyectos','catalogoClasificacionProyectos.id','=','proyectos.idClasificacionProyecto')
				->join('catalogoUnidadesResponsables','catalogoUnidadesResponsables.clave','=','proyectos.unidadResponsable')
				->leftjoin('catalogoEstatusProyectos','catalogoEstatusProyectos.id','=','evaluacionProyectoMes.idEstatus')
				->orderBy('unidadResponsable','asc')
				->orderBy('nombreTecnico','asc')
				->get();

		
		$datos['datos'] = $rows;
		//var_dump($datos);die;
		
		
		Excel::create('reporte-seguimiento', function($excel) use ($datos){
			$excel->sheet('Seguimiento', function($sheet)  use ($datos){
		        $sheet->loadView('revision.excel.reporte-seguimiento', $datos);
		    });
		    //$excel->getActiveSheet()->getStyle('A2:I'.(count($datos['datos'])+1))->getAlignment()->setWrapText(true);
		    for($col = 'A'; $col !== 'G'; $col++) {
			    $excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
			}
		})->download('xls');*/
	}
}