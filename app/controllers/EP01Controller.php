<?php
use SSA\Utilerias\Util;

class EP01Controller extends BaseController {
	
	public function index(){
		return parent::loadIndex('CARGAR','REP-EP01');
	}
}