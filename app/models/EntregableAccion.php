<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class EntregableAccion extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoEntregablesAcciones";
}