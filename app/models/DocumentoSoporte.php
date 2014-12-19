<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class DocumentoSoporte extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoDocumentosSoporte";
}