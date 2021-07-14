<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class TipoValorMeta extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoTiposValorMeta";
}