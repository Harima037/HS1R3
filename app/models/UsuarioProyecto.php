<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class UsuarioProyecto extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "usuariosProyectos";
}