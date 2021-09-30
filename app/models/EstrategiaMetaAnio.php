<?php

class EstrategiaMetaAnio extends BaseModel{
	protected $table = "estrategiaMetasAnios";
	protected $guarded = array('id','idEstrategia','anio','numerador','denominador','metaIndicador');
}

?>