<?php
use SSA\Utilerias\Util;

class ReporteSeguimientoMetasController extends BaseController {
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function indexInstitucional(){
		$datos = array(
			'meses' => array(
					array('clave'=>1,	'mes'=>'Enero',			'abrev'=>'ENE'),
					array('clave'=>2,	'mes'=>'Febrero',		'abrev'=>'FEB'),
					array('clave'=>3,	'mes'=>'Marzo',			'abrev'=>'MAR'),
					array('clave'=>4,	'mes'=>'Abril',			'abrev'=>'ABR'),
					array('clave'=>5,	'mes'=>'Mayo',			'abrev'=>'MAY'),
					array('clave'=>6,	'mes'=>'Junio',			'abrev'=>'JUN'),
					array('clave'=>7,	'mes'=>'Julio',			'abrev'=>'JUL'),
					array('clave'=>8,	'mes'=>'Agosto',		'abrev'=>'AGO'),
					array('clave'=>9,	'mes'=>'Septiembre',	'abrev'=>'SEP'),
					array('clave'=>10,	'mes'=>'Octubre',		'abrev'=>'OCT'),
					array('clave'=>11,	'mes'=>'Noviembre',		'abrev'=>'NOV'),
					array('clave'=>12,	'mes'=>'Dicembre',		'abrev'=>'DIC')
				)
		);
		$datos['anio_captura'] = Util::obtenerAnioCaptura();
		$datos['mes_avance'] = Util::obtenerMesActual();
		$datos['superusuario']=0;
		////// procesar si el usuario es admin 
			$usr_tmp=Sentry::getUser();
			    // Find the user using the user id
	    $user = Sentry::findUserByID($usr_tmp->id);

	    // Get the user groups
	    $groups = $user->getGroups();
	    for($i=0;$i<count($groups);$i++){
			$datos_permisos = json_decode($groups[$i],true);
			if(isset($datos_permisos['permissions']['REPORTES.REPSEGINST.S']) && $datos_permisos['permissions']['REPORTES.REPSEGINST.S'] == 1){
				$datos['superusuario'] = 1; 
				break;
			}
	    	/*if ($groups[$i]->id==1||$groups[$i]->id==3||$groups[$i]->id==7||$groups[$i]->id==18||$groups[$i]->id==20){
	    		$datos['superusuario']=1; 
	    	}*/
	    }
		$permiso_usuario = json_decode($usr_tmp,true);
		if(isset($permiso_usuario['permissions']['REPORTES.REPSEGINST.S']) && isset($permiso_usuario['permissions']['REPORTES.REPSEGINST.S']) == -1){
			$datos['superusuario'] = 0; 
		}else if(isset($permiso_usuario['permissions']['REPORTES.REPSEGINST.S']) && isset($permiso_usuario['permissions']['REPORTES.REPSEGINST.S']) == 1){
			$datos['superusuario'] = 1; 
		}
	   
		if ($usr_tmp->id<3){		/// si es root o admin
			$datos['superusuario']=1;
		}

		/*$permiso_usuario=json_decode($usr_tmp,true);		
		$permiso_superusuario= $permiso_usuario['permissions']['superuser'];
		
		if($permiso_superusuario==1){
			$datos['superusuario']=1;	
		}
		*/
		
		/////////////////////

		$mes_actual = Util::obtenerMesActual();
		if($mes_actual == 0){
			$mes_actual = date('n')-1;
			if($mes_actual == 0){
				$mes_actual = 12;
			}
		}
		$datos['mes_actual'] = $mes_actual;
		return parent::loadIndex('REPORTES','REPSEGINST',$datos);
	}
	
	public function indexInversion(){
		$datos = array(
			'meses' => array(
					array('clave'=>1,	'mes'=>'Enero',			'abrev'=>'ENE'),
					array('clave'=>2,	'mes'=>'Febrero',		'abrev'=>'FEB'),
					array('clave'=>3,	'mes'=>'Marzo',			'abrev'=>'MAR'),
					array('clave'=>4,	'mes'=>'Abril',			'abrev'=>'ABR'),
					array('clave'=>5,	'mes'=>'Mayo',			'abrev'=>'MAY'),
					array('clave'=>6,	'mes'=>'Junio',			'abrev'=>'JUN'),
					array('clave'=>7,	'mes'=>'Julio',			'abrev'=>'JUL'),
					array('clave'=>8,	'mes'=>'Agosto',		'abrev'=>'AGO'),
					array('clave'=>9,	'mes'=>'Septiembre',	'abrev'=>'SEP'),
					array('clave'=>10,	'mes'=>'Octubre',		'abrev'=>'OCT'),
					array('clave'=>11,	'mes'=>'Noviembre',		'abrev'=>'NOV'),
					array('clave'=>12,	'mes'=>'Dicembre',		'abrev'=>'DIC')
				)
		);
		$datos['anio_captura'] = Util::obtenerAnioCaptura();
		$datos['mes_avance'] = Util::obtenerMesActual();
		$mes_actual = Util::obtenerMesActual();

			$datos['superusuario']=0;
		////// procesar si el usuario es admin 
			$usr_tmp=Sentry::getUser();
			    // Find the user using the user id
	    $user = Sentry::findUserByID($usr_tmp->id);

	    // Get the user groups
	    $groups = $user->getGroups();
	    for($i=0;$i<count($groups);$i++){
			$datos_permisos = json_decode($groups[$i],true);
			if(isset($datos_permisos['permissions']['REPORTES.REPSEGINV.S']) && $datos_permisos['permissions']['REPORTES.REPSEGINV.S'] == 1){
				$datos['superusuario'] = 1; 
				break;
			}
	    	/*if ($groups[$i]->id==1||$groups[$i]->id==3||$groups[$i]->id==7||$groups[$i]->id==18||$groups[$i]->id==20){
	    		$datos['superusuario']=1;
	    	}*/
	    }

	   
		
		if ($usr_tmp->id<3){		/// si es root o admin
			$datos['superusuario']=1;
		}
		/////////////////////////////////////////////////////////////////////////////

		if($mes_actual == 0){
			$mes_actual = date('n')-1;
			if($mes_actual == 0){
				$mes_actual = 12;
			}
		}
		$datos['mes_actual'] = $mes_actual;
		return parent::loadIndex('REPORTES','REPSEGINV',$datos);
	}
}