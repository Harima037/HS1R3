<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ActividadDesgloseMetasMes extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "actividadDesgloseMetasMes";

	public function scopeAcumulado($query){
		$query->select('id','idActividadDesglose',DB::raw('sum(meta) AS meta'),DB::raw('sum(avance) AS avance'),DB::raw('max(mes) AS mes'))
				->groupBy('idActividadDesglose');
	}
}