<table>
	<tr>
		<td rowspan="5" class="imagen izquierda">
			<img src="{{ URL::to('img/LogoFederal.png') }}" width="150">
		</td>
		<td height="20" class="titulo1" align="center">INSTITUTO DE SALUD</td>
		<td rowspan="5" class="imagen derecha">
			<img src="{{ URL::to('img/LogoInstitucional.png') }}" width="150">
		</td>
	</tr>
	<tr>
		<td height="19" class="titulo2" align="center">DIRECCIÓN DE PLANEACIÓN Y DESARROLLO</td>
	</tr>
	<tr>
		<td height="18" class="titulo3" align="center">SUBDIRECCIÓN DE PROGRAMACIÓN, ORGANIZACIÓN Y PRESUPUESTO</td>
	</tr>
	<tr>
		<td height="18" class="titulo3" align="center">DEPARTAMENTO DE EVALUACIÓN</td>
	</tr>
	<tr>
		<td height="18" class="titulo3" align="center">SEGUIMIENTO DE BENEFICIARIOS {{$proyecto['ejercicio']}}</td>
	</tr>
	<tr>
		<td height="18" colspan="3" align="right" class="negrita">Formato RC-5</td>
	</tr>
</table>

<table>
	<tr height="90" class="texto-medio texto">
		<td class="texto-centro">Nombre del proyecto: </td>
		<td class="negrita" colspan="4">{{ $proyecto['nombreTecnico'] }}</td>
		<td></td>
		<td class="texto-centro">Clave presupuestaria: </td>
		<td class="negrita" colspan="2">{{ $proyecto['ClavePresupuestaria'] }}</td>
		<td></td>
		<td></td>
		<td class="texto-derecha">Trimestre: </td>
		<td class="negrita">{{$mes['trimestre']}}</td>
		<td></td>
		<td></td>
	</tr>
</table>

<table>
	@foreach($beneficiarios as $beneficiario)
	<tr height="40"><td colspan="15">&nbsp;</td></tr>

	<tr class="tabla-datos" height="25">
		<td colspan="5" style="border:none;"></td>
		<td class="encabezado-tabla" colspan="4">POBLACIÓN</td>
		<td class="encabezado-tabla" colspan="5">MARGINACIÓN</td>
		<td rowspan="2" width="15" class="encabezado-tabla">POBLACIÓN ACUMULADA</td>
		<!--td class="sin-bordes"></td-->
	</tr>
	<tr class="tabla-datos" height="30">
		<td width="25" class="encabezado-tabla">TIPO</td>
		<td width="12" class="encabezado-tabla">GÉNERO</td>
		<td width="15" class="encabezado-tabla">TOTAL</td>
		<td width="15" class="encabezado-tabla">ZONA URBANA</td>
		<td width="15" class="encabezado-tabla">ZONA RURAL</td>
		<td width="15" class="encabezado-tabla">MESTIZA</td>
		<td width="15" class="encabezado-tabla">INDÍGENA</td>
		<td width="15" class="encabezado-tabla">INMIGRANTE</td>
		<td width="15" class="encabezado-tabla">OTROS</td>
		<td width="15" class="encabezado-tabla">MUY ALTA</td>
		<td width="15" class="encabezado-tabla">ALTA</td>
		<td width="15" class="encabezado-tabla">MEDIA</td>
		<td width="15" class="encabezado-tabla">BAJA</td>
		<td width="15" class="encabezado-tabla">MUY BAJA</td>
	</tr>

	<tr class="tabla-datos" height="40">
		<td class="texto-centro texto-medio" rowspan="2">{{$beneficiario['tipoBeneficiario']}}</td>
		<td class="texto-centro texto-medio negrita">Femenino</td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['f']['total'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['f']['urbana'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['f']['rural'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['f']['mestiza'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['f']['indigena'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['f']['inmigrante'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['f']['otros'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['f']['muyAlta'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['f']['alta'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['f']['media'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['f']['baja'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['f']['muyBaja'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['f']['acumulado'])}} </td>
	</tr>

	<tr class="tabla-datos" height="40">
		<td class="texto-centro texto-medio negrita">Masculino</td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['m']['total'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['m']['urbana'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['m']['rural'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['m']['mestiza'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['m']['indigena'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['m']['inmigrante'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['m']['otros'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['m']['muyAlta'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['m']['alta'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['m']['media'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['m']['baja'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['m']['muyBaja'])}} </td>
		<td class="texto-centro texto-medio"> {{number_format($beneficiarios_avances[$beneficiario['id']]['m']['acumulado'])}} </td>
	</tr>

	<tr class="tabla-datos" height="40">
		<td class="texto-centro texto-medio"></td>
		<td class="texto-centro texto-medio negrita">Total</td>
		<td class="texto-centro texto-medio">
			{{
		number_format($beneficiarios_avances[$beneficiario['id']]['f']['total'] + $beneficiarios_avances[$beneficiario['id']]['m']['total'])
			}}
		</td>
		<td class="texto-centro texto-medio">
			{{ 
		number_format($beneficiarios_avances[$beneficiario['id']]['f']['urbana'] + $beneficiarios_avances[$beneficiario['id']]['m']['urbana'])
			}}
		</td>
		<td class="texto-centro texto-medio">
			{{
		number_format($beneficiarios_avances[$beneficiario['id']]['f']['rural'] + $beneficiarios_avances[$beneficiario['id']]['m']['rural'])
			}}
		</td>
		<!--td width="0"></td-->
		<td class="texto-centro texto-medio">
			{{ 
		number_format($beneficiarios_avances[$beneficiario['id']]['f']['mestiza'] + $beneficiarios_avances[$beneficiario['id']]['m']['mestiza'])
			}}
		</td>
		<td class="texto-centro texto-medio">
			{{ 
		number_format($beneficiarios_avances[$beneficiario['id']]['f']['indigena'] + $beneficiarios_avances[$beneficiario['id']]['m']['indigena'])
			}}
		</td>
		<td class="texto-centro texto-medio">
			{{
		number_format($beneficiarios_avances[$beneficiario['id']]['f']['inmigrante'] + $beneficiarios_avances[$beneficiario['id']]['m']['inmigrante'])
			}}
		</td>
		<td class="texto-centro texto-medio">
			{{
		number_format($beneficiarios_avances[$beneficiario['id']]['f']['otros'] + $beneficiarios_avances[$beneficiario['id']]['m']['otros'])
			}}
		</td>
		<td class="texto-centro texto-medio">
			{{ 
		number_format($beneficiarios_avances[$beneficiario['id']]['f']['muyAlta'] + $beneficiarios_avances[$beneficiario['id']]['m']['muyAlta'])
			}}
		</td>
		<td class="texto-centro texto-medio">
			{{
		number_format($beneficiarios_avances[$beneficiario['id']]['f']['alta'] + $beneficiarios_avances[$beneficiario['id']]['m']['alta'])
			}}
		</td>
		<td class="texto-centro texto-medio">
			{{
		number_format($beneficiarios_avances[$beneficiario['id']]['f']['media'] + $beneficiarios_avances[$beneficiario['id']]['m']['media'])
			}}
		</td>
		<td class="texto-centro texto-medio">
			{{
		number_format($beneficiarios_avances[$beneficiario['id']]['f']['baja'] + $beneficiarios_avances[$beneficiario['id']]['m']['baja'])
			}}
		</td>
		<td class="texto-centro texto-medio">
			{{
		number_format($beneficiarios_avances[$beneficiario['id']]['f']['muyBaja'] + $beneficiarios_avances[$beneficiario['id']]['m']['muyBaja'])
			}}
		</td>
		<td class="texto-centro texto-medio">
			{{
		number_format($beneficiarios_avances[$beneficiario['id']]['f']['acumulado'] + $beneficiarios_avances[$beneficiario['id']]['m']['acumulado'])
			}}
		</td>
	</tr>

	<tr><td height="40" colspan="15">&nbsp;</td></tr>

	<tr class="negrita" height="20">
		<td></td>
		<td></td>
		<td></td>
		<td colspan="4" align="center">RESPONSABLE DE LA INFORMACIÓN</td>
		<td></td>
		<td colspan="5" align="center">LIDER DEL PROYECTO</td>
		<td></td>
		<td></td>
	</tr>
	<tr height="40">
		<td></td>
		<td></td>
		<td></td>
		<td colspan="4" height="40" class="linea-firma">&nbsp;</td>
		<td></td>
		<td colspan="5" class="linea-firma"></td>
		<td></td>
		<td></td>
	</tr>
	<tr class="negrita" height="20">
		<td></td>
		<td></td>
		<td></td>
		<td colspan="4" align="center">{{ $proyecto['responsableInformacion'] }}</td>
		<td></td>
		<td colspan="5" align="center">{{ $proyecto['liderProyecto'] }}</td>
		<td></td>
		<td></td>
	</tr>
	<tr class="negrita" height="20">
		<td></td>
		<td></td>
		<td></td>
		<td colspan="4" align="center">{{ $proyecto['cargoResponsableInformacion'] }}</td>
		<td></td>
		<td colspan="5" align="center">{{ $proyecto['cargoLiderProyecto'] }}</td>
		<td></td>
		<td></td>
	</tr>
	@endforeach

</table>