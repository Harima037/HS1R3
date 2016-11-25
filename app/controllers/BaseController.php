<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}
	
	public function loadIndex($sys_sis_llave,$sys_mod_llave = NULL,$datos_extra = array()){
		$datos = array();
		$datos['sys_sistemas'] = SysGrupoModulo::all();
		$datos['sys_activo'] = SysGrupoModulo::findByKey($sys_sis_llave);
		$uri = $datos['sys_activo']->uri;
		$permission = $sys_sis_llave;
		
		if($sys_mod_llave === NULL){
			$datos['sys_mod_activo'] = null;
		}else{
			$datos['sys_mod_activo'] = SysModulo::findByKey($sys_mod_llave);
			$uri .= '.' . $datos['sys_mod_activo']->uri;
			$permission .=  '.' . $sys_mod_llave;
		}
		$datos['usuario'] = Sentry::getUser();

		if(Sentry::hasAccess($permission.'.R')){
			if(count($datos_extra) > 0){
				foreach ($datos_extra as $key => $value) {
					$datos[$key] = $value;
				}
			}

			return View::make($uri)->with($datos);
		}else{
			return Response::view('errors.403', array('usuario'=>$datos['usuario'],'sys_activo'=>null,'sys_sistemas'=>$datos['sys_sistemas'],'sys_mod_activo'=>null), 403);
		}

	}

}