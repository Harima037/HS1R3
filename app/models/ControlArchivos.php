<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ControlArchivos extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "controlArchivos";
}