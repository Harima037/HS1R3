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
use BaseController, Input, Response, DB, Sentry, Hash, Exception,DateTime,Mail;
use Proyecto,Componente,Actividad,Beneficiario,RegistroAvanceMetas,ComponenteMetaMes,ActividadMetaMes,RegistroAvanceBeneficiario,EvaluacionAnalisisFuncional,EvaluacionProyectoMes,
	EvaluacionPlanMejora,ComponenteDesglose,DesgloseMetasMes,Directorio,SysConfiguracionVariable;

class ReporteSeguimientoMetasController extends BaseController {
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		$http_status = 200;
		$data = array();

		$parametros = Input::all();
		if(isset($parametros['formatogrid'])){
			//$mes_actual = Util::obtenerMesActual();

			if(isset($parametros['ejercicio'])){
				$ejercicio = $parametros['ejercicio'];
			}else{
				$ejercicio = Util::obtenerAnioCaptura();
			}

			$rows = Proyecto::getModel();
			$rows = $rows->where('idEstatusProyecto','=',5)
						->where('idClasificacionProyecto','=',$parametros['clasificacionProyecto'])
						->where('ejercicio',$ejercicio);
			
			$usuario = Sentry::getUser();
			
			if($usuario->idDepartamento == 2){
				if($usuario->filtrarProyectos){
					$rows = $rows->where('idUsuarioValidacionSeg','=',$usuario->id);
				}
			}else{
				$rows = $rows->where('idUsuarioRendCuenta','=',$usuario->id);
			}
			
			if($usuario->claveUnidad){
				$unidades = explode('|',$usuario->claveUnidad);
				$rows = $rows->whereIn('unidadResponsable',$unidades);
			}
			
			if($parametros['pagina']==0){ $parametros['pagina'] = 1; }
			
			if(isset($parametros['buscar'])){				
				$rows = $rows->where(function($query)use($parametros){
					$query->where('proyectos.nombreTecnico','like','%'.$parametros['buscar'].'%')
						->orWhere(DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0"))'),'like','%'.$parametros['buscar'].'%');
				});
				$total = $rows->count();
			}else{				
				$total = $rows->count();						
			}
			
			$rows = $rows->select('proyectos.id',DB::raw('concat(unidadResponsable,finalidad,funcion,subfuncion,subsubfuncion,programaSectorial,programaPresupuestario,programaEspecial,actividadInstitucional,proyectoEstrategico,LPAD(numeroProyectoEstrategico,3,"0")) as clavePresup'),
				'nombreTecnico',
				DB::raw('concat_ws(" ",sentryUsers.nombres,sentryUsers.apellidoPaterno,sentryUsers.apellidoMaterno) AS nombreRevisor'))
				->leftjoin('sentryUsers','sentryUsers.id','=','proyectos.idUsuarioValidacionSeg')
				->orderBy('id', 'desc')
				->skip(($parametros['pagina']-1)*10)->take(10)
				->get();

			$data = array('resultados'=>$total,'data'=>$rows);

			if($total<=0){
				$http_status = 404;
				$data = array('resultados'=>$total,"data"=>"No hay datos",'code'=>'W00');
			}

			return Response::json($data,$http_status);
		}
		
		$rows = Proyecto::all();

		if(count($rows) == 0){
			$http_status = 404;
			$data = array("data"=>"No hay datos",'code'=>'W00');
		}else{
			$data = array("data"=>$rows->toArray());
		}

		return Response::json($data,$http_status);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id){
		//
		$http_status = 200;
		$data = array();
		
		$recurso = Proyecto::with(array('datosFuncion','datosSubFuncion','datosProgramaPresupuestario',
			'evaluacionMeses'=>function($query){
				$query->whereIn('idEstatus',array(4,5));
			})
		)->find($id);

		if(is_null($recurso)){
			$http_status = 404;
			$data = array("data"=>"No existe el recurso que quiere solicitar.",'code'=>'U06');
		}else{
			$data["data"] = $recurso;
		}

		return Response::json($data,$http_status);
	}
}