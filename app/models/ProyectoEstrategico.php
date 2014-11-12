<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ProyectoEstrategico extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoProyectosEstrategicos";
}