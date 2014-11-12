<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class TipoAccion extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoTiposAcciones";
}