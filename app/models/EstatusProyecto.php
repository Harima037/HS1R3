<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class EstatusProyecto extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoEstatusProyectos";
}