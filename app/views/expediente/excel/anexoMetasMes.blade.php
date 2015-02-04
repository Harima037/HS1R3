<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
	<table>
		<tr><td colspan="15"></td></tr>
		<tr>
			<td colspan="15" align="center">GOBIERNO DEL ESTADO DE CHIAPAS</td>
		</tr>
		<tr>
			<td colspan="15" align="center">SECRETARÍA DE SALUD</td>
		</tr>
		<tr>
			<td colspan="15" align="center">INSTITUTO DE SALUD</td>
		</tr>
		<tr>
			<td colspan="15" align="center">DIRECCIÓN DE PLANEACIÓN Y DESARROLLO</td>
		</tr>
		<tr>
			<td colspan="15" align="center">DEPARTAMENTO DE INTEGRACION PROGRAMATICA PRESUPUESTAL</td>
		</tr>
		<tr><td colspan="15" align="center">DISTRIBUCIÓN DE METAS POR JURISDICCIÓN 2013</td></tr>
		<tr>
			<td colspan="15">NOMBRE DEL PROYECTO: {{ $data['nombreTecnico'] }}</td>
		</tr>
		<tr><td colspan="15"></td></tr>
		<tr>
			<td colspan="15">CLAVE PRESUPUESTAL: {{ $data['ClavePresupuestaria'] }}</td>
		</tr>
		<tr><td colspan="15"></td></tr>
		<tr>
			<td style="background-color:#FFFF00;border:1px solid #000"></td>
			<td style="background-color:#FFFF00;border:1px solid #000">DESCRIPCIÓN DEL INDICADOR</td>
			<td style="background-color:#FFFF00;border:1px solid #000">ENERO</td>
			<td style="background-color:#FFFF00;border:1px solid #000">FEBRERO</td>
			<td style="background-color:#FFFF00;border:1px solid #000">MARZO</td>
			<td style="background-color:#FFFF00;border:1px solid #000">ABRIL</td>
			<td style="background-color:#FFFF00;border:1px solid #000">MAYO</td>
			<td style="background-color:#FFFF00;border:1px solid #000">JUNIO</td>
			<td style="background-color:#FFFF00;border:1px solid #000">JULIO</td>
			<td style="background-color:#FFFF00;border:1px solid #000">AGOSTO</td>
			<td style="background-color:#FFFF00;border:1px solid #000">SEPTIEMBRE</td>
			<td style="background-color:#FFFF00;border:1px solid #000">OCTUBRE</td>
			<td style="background-color:#FFFF00;border:1px solid #000">NOVIEMBRE</td>
			<td style="background-color:#FFFF00;border:1px solid #000">DICIEMBRE</td>
			<td style="background-color:#FFFF00;border:1px solid #000">TOTAL</td>
		</tr>
		@foreach($data['componentes'] as $componente)
		<tr>
			<td style="border:1px solid #000">{{ $componente->objetivo }}</td>
			<td style="border:1px solid #000">{{ $componente->indicador }}</td>
			<?php
				$suma = 0;
			?>
			<td style="border:1px solid #000">
				@foreach($data['componentesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='ENE' && $jurisdiccion->idComponente==$componente->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['componentesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='FEB' && $jurisdiccion->idComponente==$componente->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['componentesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='MAR' && $jurisdiccion->idComponente==$componente->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['componentesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='ABR' && $jurisdiccion->idComponente==$componente->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['componentesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='MAY' && $jurisdiccion->idComponente==$componente->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['componentesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='JUN' && $jurisdiccion->idComponente==$componente->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['componentesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='JUL' && $jurisdiccion->idComponente==$componente->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['componentesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='AGO' && $jurisdiccion->idComponente==$componente->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['componentesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='SEP' && $jurisdiccion->idComponente==$componente->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['componentesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='OCT' && $jurisdiccion->idComponente==$componente->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['componentesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='NOV' && $jurisdiccion->idComponente==$componente->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['componentesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='DIC' && $jurisdiccion->idComponente==$componente->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			
			<td style="border:1px solid #000">{{$suma}}</td>
		</tr>
		@endforeach
		@foreach($componente->actividades as $actividad)
		<tr>
			<td style="border:1px solid #000">{{ $actividad->objetivo }}</td>
			<td style="border:1px solid #000">{{ $actividad->indicador }}</td>
			<?php
				$suma = 0;
			?>
			<td style="border:1px solid #000">
				@foreach($data['actividadesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='ENE' && $jurisdiccion->idActividad==$actividad->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['actividadesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='FEB' && $jurisdiccion->idActividad==$actividad->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['actividadesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='MAR' && $jurisdiccion->idActividad==$actividad->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['actividadesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='ABR' && $jurisdiccion->idActividad==$actividad->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['actividadesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='MAY' && $jurisdiccion->idActividad==$actividad->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['actividadesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='JUN' && $jurisdiccion->idActividad==$actividad->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['actividadesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='JUL' && $jurisdiccion->idActividad==$actividad->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['actividadesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='AGO' && $jurisdiccion->idActividad==$actividad->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['actividadesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='SEP' && $jurisdiccion->idActividad==$actividad->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['actividadesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='OCT' && $jurisdiccion->idActividad==$actividad->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['actividadesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='NOV' && $jurisdiccion->idActividad==$actividad->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['actividadesMetasMes'] as $jurisdiccion)
					@if($jurisdiccion->mes=='DIC' && $jurisdiccion->idActividad==$actividad->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			
			<td style="border:1px solid #000">{{$suma}}</td>
		</tr>
		@endforeach
		<tr>
			<td colspan="15"></td>
		</tr>
		<tr>
			<td colspan="15">FUENTE DE INFORMACIÓN:</td>
		</tr>
		<tr>
			<td colspan="15"></td>
		</tr>
		<tr>
			<td colspan="5"></td>
			<td colspan="5"></td>
			<td colspan="5"></td>
		</tr>

		<tr>
			<td colspan="5" align="center">RESPONSABLE DEL PROGRAMA</td>
			<td colspan="5"></td>
			<td colspan="5" align="center">DIRECTOR</td>
		</tr>
		<tr></tr>
		<tr>
			<td colspan="5"></td>
			<td colspan="5"></td>
			<td colspan="5"></td>
		</tr>
	</table>
</body>
</html>