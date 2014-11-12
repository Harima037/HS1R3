<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ClasificacionProyecto extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoClasificacionProyectos";
}