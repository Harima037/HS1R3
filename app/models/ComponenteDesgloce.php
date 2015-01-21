<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ComponenteDesgloce extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "componenteDesgloce";

	public function metasMes(){
		return $this->hasMany('DesgloceMetasMes','idComponenteDesgloce');
	}

	public function municipio(){
    	return $this->belongsTo('Municipio','claveMunicipio','clave');
    }

    public function localidad(){
    	return $this->belongsTo('Localidad','claveLocalidad','clave');
    }
}