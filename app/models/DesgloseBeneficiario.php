<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class DesgloseBeneficiario extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "desgloseBeneficiarios";
}