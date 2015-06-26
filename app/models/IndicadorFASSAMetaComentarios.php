<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class IndicadorFASSAMetaComentarios extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "indicadorFASSAMetaComentarios";
}