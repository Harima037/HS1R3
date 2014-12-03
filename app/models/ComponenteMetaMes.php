<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ComponenteMetaMes extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "componenteMetasMes";
}