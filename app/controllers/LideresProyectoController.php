<?php

class LideresProyectoController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		
		$areas_raw = Area::orderBy('idAreaDepende','ASC')->get();
		$areas = array();
		$conteo = 0;
		
		foreach ($areas_raw as $i => $area) {
			$area = $area->toArray();
			if($area['idAreaDepende'] != null){
				foreach($areas as $j => $area_depende){
					if($area_depende['id'] == $area['idAreaDepende']){
						$area['nivelArbol'] = $area_depende['nivelArbol'] + 1;
						array_splice($areas,$j+1,0,array($area));
						break;
					}
				}
			}else{
				$area['nivelArbol'] = 0;
				$areas[] = $area;
			}
		}
		
		$catalogos = array(
			'areas' => $areas
		);

		return parent::loadIndex('ADMIN','CATRESPO',$catalogos);
		
	}

}