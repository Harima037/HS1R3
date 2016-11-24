<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class OrigenAsignacion extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoOrigenesAsignacion";
}