<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class DestinoGasto extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoDestinoGasto";
}