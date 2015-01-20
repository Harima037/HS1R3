<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ComponenteDesgloce extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "componenteDesgloce";

	public function metasMes(){
		$this->hasMany('DesgloceMetasMes','idComponenteDesgloce');
	}
}