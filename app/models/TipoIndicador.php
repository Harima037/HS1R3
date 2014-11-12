<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class TipoIndicador extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoTiposIndicadores";
}