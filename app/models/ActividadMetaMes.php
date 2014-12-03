<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ActividadMetaMes extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "actividadMetasMes";
}