<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class TipoBeneficiario extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoTiposBeneficiarios";
}