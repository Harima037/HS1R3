<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class DesgloseMetasMes extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "desgloseMetasMes";

	public function scopeAcumulado($query){
		$query->select('id','idComponenteDesglose',DB::raw('sum(meta) AS meta'),DB::raw('sum(avance) AS avance'),DB::raw('max(mes) AS mes'))
				->groupBy('idComponenteDesglose');
	}
}