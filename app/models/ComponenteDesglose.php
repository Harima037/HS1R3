<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ComponenteDesglose extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "componenteDesglose";

	public function metasMes(){
		return $this->hasMany('DesgloseMetasMes','idComponenteDesglose');
	}

	public function municipio(){
    	return $this->belongsTo('Municipio','claveMunicipio','clave');
    }

    public function localidad(){
    	return $this->belongsTo('Localidad','claveLocalidad','clave');
    }
}