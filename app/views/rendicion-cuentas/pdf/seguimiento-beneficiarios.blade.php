<div class="header">
	<table>
		<tr>
			<td rowspan="5" class="imagen izquierda">
				<img src="{{ public_path().'/img/LogoFederal.png' }}" width="125">
			</td>
			<td class="titulo1" align="center">INSTITUTO DE SALUD</td>
			<td rowspan="5" class="imagen derecha">
				<img src="{{ public_path().'/img/LogoInstitucional.png' }}" width="125">
			</td>
		</tr>
		<tr><td class="titulo2" align="center">DIRECCIÓN DE PLANEACIÓN Y DESARROLLO</td></tr>
		<tr><td class="titulo3" align="center">SUBDIRECCIÓN DE PLANEACIÓN EN SALUD</td></tr>
		<tr><td class="titulo3" align="center">DEPARTAMENTO DE EVALUACIÓN</td></tr>
		<tr><td class="titulo3" align="center">SEGUIMIENTO DE BENEFICIARIOS {{$proyecto['ejercicio']}}</td></tr>
		<tr><td colspan="3" align="right" class="negrita">Formato RC-5</td></tr>
	</table>
	<table>
		<tr height="90" class="texto-medio texto">
			<td width="100" class="texto-centro">Nombre del proyecto: </td>
			<td class="negrita" colspan="4">{{ $proyecto['nombreTecnico'] }}</td>
			<td width="105" class="texto-centro">Clave presupuestaria: </td>
			<td width="90" class="negrita" colspan="2">{{ $proyecto['ClavePresupuestaria'] }}</td>
			<td width="80" class="texto-derecha">Trimestre: </td>
			<td width="20" class="negrita">{{$mes['trimestre']}}</td>
		</tr>
		<tr height="40"><td colspan="6">&nbsp;</td></tr>
	</table>
</div>

<table>
<thead>
	<tr class="tabla-datos">
		<td rowspan="2" class="encabezado-tabla" style="font-size:7;">GRUPO</td>
		<td rowspan="2" class="encabezado-tabla" style="font-size:7;">TIPO</td>
		<td rowspan="2" class="encabezado-tabla" style="font-size:7;">GÉNERO</td>
		<td rowspan="2" class="encabezado-tabla" style="font-size:7;">TOTAL</td>
		<td rowspan="2" class="encabezado-tabla" style="font-size:7;">ZONA <br> URBANA</td>
		<td rowspan="2" class="encabezado-tabla" style="font-size:7;">ZONA <br> RURAL</td>
		<td class="encabezado-tabla" style="font-size:7;" colspan="2">POBLACIÓN</td>
		<td class="encabezado-tabla" style="font-size:7;" colspan="5">MARGINACIÓN</td>
		<td rowspan="2" class="encabezado-tabla" style="font-size:7;">POBLACIÓN<br>ACUMULADA</td>
	</tr>
	<tr class="tabla-datos">
		<td class="encabezado-tabla" style="font-size:7;">MESTIZA</td>
		<td class="encabezado-tabla" style="font-size:7;">INDÍGENA</td>
		<td class="encabezado-tabla" style="font-size:7;">MUY ALTA</td>
		<td class="encabezado-tabla" style="font-size:7;">ALTA</td>
		<td class="encabezado-tabla" style="font-size:7;">MEDIA</td>
		<td class="encabezado-tabla" style="font-size:7;">BAJA</td>
		<td class="encabezado-tabla" style="font-size:7;">MUY BAJA</td>
	</tr>
</thead>
<tbody>
@foreach($beneficiarios as $beneficiario)
	<tr class="tabla-datos" height="40">
		<td class="texto-centro texto-medio">{{$beneficiario['grupo']}}</td>
		<td class="texto-centro texto-medio">{{$beneficiario['tipoBeneficiario']}}</td>
		<td class="texto-centro texto-medio negrita">
			{{($beneficiario['sexo'] == 'f')?'Femenino':'Masculino'}}
		</td>
		<td class="texto-centro texto-medio">
			{{number_format($beneficiarios_avances[$beneficiario['id']][$beneficiario['sexo']]['total'])}} 
		</td>
		<td class="texto-centro texto-medio">
			{{number_format($beneficiarios_avances[$beneficiario['id']][$beneficiario['sexo']]['urbana'])}} 
		</td>
		<td class="texto-centro texto-medio">
			{{number_format($beneficiarios_avances[$beneficiario['id']][$beneficiario['sexo']]['rural'])}} 
		</td>
		<td class="texto-centro texto-medio">
			{{number_format($beneficiarios_avances[$beneficiario['id']][$beneficiario['sexo']]['mestiza'])}} 
		</td>
		<td class="texto-centro texto-medio">
			{{number_format($beneficiarios_avances[$beneficiario['id']][$beneficiario['sexo']]['indigena'])}} 
		</td>
		<td class="texto-centro texto-medio">
			{{number_format($beneficiarios_avances[$beneficiario['id']][$beneficiario['sexo']]['muyAlta'])}} 
		</td>
		<td class="texto-centro texto-medio">
			{{number_format($beneficiarios_avances[$beneficiario['id']][$beneficiario['sexo']]['alta'])}} 
		</td>
		<td class="texto-centro texto-medio">
			{{number_format($beneficiarios_avances[$beneficiario['id']][$beneficiario['sexo']]['media'])}} 
		</td>
		<td class="texto-centro texto-medio">
			{{number_format($beneficiarios_avances[$beneficiario['id']][$beneficiario['sexo']]['baja'])}} 
		</td>
		<td class="texto-centro texto-medio">
			{{number_format($beneficiarios_avances[$beneficiario['id']][$beneficiario['sexo']]['muyBaja'])}} 
		</td>
		<td class="texto-centro texto-medio">
			{{number_format($beneficiarios_avances[$beneficiario['id']][$beneficiario['sexo']]['acumulado'])}} 
		</td>
	</tr>
	</tr>
@endforeach
</tbody>
</table>
<table>
	<tr>
		<td colspan="5">&nbsp;</td>
	</tr>
	<tr class="negrita" height="20">
		<td width="10%"></td>
		<td align="center">RESPONSABLE DE LA INFORMACIÓN</td>
		<td width="10%"></td>
		<td align="center">LIDER DEL PROYECTO</td>
		<td width="10%"></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td height="20" class="linea-firma"></td>
		<td>&nbsp;</td>
		<td class="linea-firma"></td>
		<td>&nbsp;</td>
	</tr>
	<tr class="negrita" height="20">
		<td></td>
		<td align="center">{{ $proyecto['responsableInformacion'] }}</td>
		<td></td>
		<td align="center">{{ $proyecto['liderProyecto'] }}</td>
		<td></td>
	</tr>
	<tr class="negrita" height="20">
		<td></td>
		<td align="center">{{ $proyecto['cargoResponsableInformacion'] }}</td>
		<td></td>
		<td align="center">{{ $proyecto['cargoLiderProyecto'] }}</td>
		<td></td>
	</tr>
	<tr>
		<td colspan="5">&nbsp;</td>
	</tr>
</table>

