<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class TipoProyecto extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoTiposProyectos";
}