<?php

class ControlArchivosController extends \BaseController {
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id){
        $archivo = ControlArchivos::find($id);

        if($archivo){

            $path = storage_path().$archivo->directorio.$archivo->nombre.'.'.$archivo->extension;

            if (!File::exists($path)) {
                $datos['sys_sistemas'] = SysGrupoModulo::all();
        		$datos['usuario'] = Sentry::getUser();
                
                return Response::view('errors.archivo_no_encontrado', array(
                    'usuario'=>$datos['usuario'],
                    'sys_activo'=>null,
                    'sys_sistemas'=>$datos['sys_sistemas'],
                    'sys_mod_activo'=>null), 403
                );
            }
            $finfo = finfo_open(FILEINFO_MIME_TYPE); 
            
            $file = File::get($path);
            $type = finfo_file($finfo, $path);

            $result = Response::make($file, 200);
            $result->header("Content-Type", $type);

            return $result;
        }
	}
}