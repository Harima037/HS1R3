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
			<td style="background-color:#FFFF00;border:1px solid #000" colspan="2">DESCRIPCIÓN DEL INDICADOR</td>
			<td style="background-color:#FFFF00;border:1px solid #000">OC</td>
			<td style="background-color:#FFFF00;border:1px solid #000">I</td>
			<td style="background-color:#FFFF00;border:1px solid #000">II</td>
			<td style="background-color:#FFFF00;border:1px solid #000">III</td>
			<td style="background-color:#FFFF00;border:1px solid #000">IV</td>
			<td style="background-color:#FFFF00;border:1px solid #000">V</td>
			<td style="background-color:#FFFF00;border:1px solid #000">VI</td>
			<td style="background-color:#FFFF00;border:1px solid #000">VII</td>
			<td style="background-color:#FFFF00;border:1px solid #000">VIII</td>
			<td style="background-color:#FFFF00;border:1px solid #000">IX</td>
			<td style="background-color:#FFFF00;border:1px solid #000">X</td>
			<td style="background-color:#FFFF00;border:1px solid #000">ESTATAL</td>
		</tr>
		@foreach($data['componentes'] as $componente)
		<tr>
			<td style="border:1px solid #000">{{ $componente->objetivo }}</td>
			<td colspan="2" style="border:1px solid #000">{{ $componente->indicador }}</td>
			<?php
				$suma = 0;
			?>
			<td style="border:1px solid #000">
				@foreach($data['componentesMetasJuris'] as $jurisdiccion)
					@if($jurisdiccion->claveJurisdiccion=='OC' && $jurisdiccion->idComponente==$componente->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['componentesMetasJuris'] as $jurisdiccion)
					@if($jurisdiccion->claveJurisdiccion=='01' && $jurisdiccion->idComponente==$componente->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['componentesMetasJuris'] as $jurisdiccion)
					@if($jurisdiccion->claveJurisdiccion=='02' && $jurisdiccion->idComponente==$componente->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['componentesMetasJuris'] as $jurisdiccion)
					@if($jurisdiccion->claveJurisdiccion=='03' && $jurisdiccion->idComponente==$componente->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['componentesMetasJuris'] as $jurisdiccion)
					@if($jurisdiccion->claveJurisdiccion=='04' && $jurisdiccion->idComponente==$componente->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['componentesMetasJuris'] as $jurisdiccion)
					@if($jurisdiccion->claveJurisdiccion=='05' && $jurisdiccion->idComponente==$componente->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['componentesMetasJuris'] as $jurisdiccion)
					@if($jurisdiccion->claveJurisdiccion=='06' && $jurisdiccion->idComponente==$componente->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['componentesMetasJuris'] as $jurisdiccion)
					@if($jurisdiccion->claveJurisdiccion=='07' && $jurisdiccion->idComponente==$componente->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['componentesMetasJuris'] as $jurisdiccion)
					@if($jurisdiccion->claveJurisdiccion=='08' && $jurisdiccion->idComponente==$componente->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['componentesMetasJuris'] as $jurisdiccion)
					@if($jurisdiccion->claveJurisdiccion=='09' && $jurisdiccion->idComponente==$componente->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['componentesMetasJuris'] as $jurisdiccion)
					@if($jurisdiccion->claveJurisdiccion=='10' && $jurisdiccion->idComponente==$componente->id)
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
			<td style="border:1px solid #000" colspan="2">{{ $actividad->indicador }}</td>
			<?php
				$suma = 0;
			?>
			<td style="border:1px solid #000">
				@foreach($data['actividadesMetasJuris'] as $jurisdiccion)
					@if($jurisdiccion->claveJurisdiccion=='OC' && $jurisdiccion->idActividad==$actividad->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['actividadesMetasJuris'] as $jurisdiccion)
					@if($jurisdiccion->claveJurisdiccion=='01' && $jurisdiccion->idActividad==$actividad->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['actividadesMetasJuris'] as $jurisdiccion)
					@if($jurisdiccion->claveJurisdiccion=='02' && $jurisdiccion->idActividad==$actividad->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['actividadesMetasJuris'] as $jurisdiccion)
					@if($jurisdiccion->claveJurisdiccion=='03' && $jurisdiccion->idActividad==$actividad->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['actividadesMetasJuris'] as $jurisdiccion)
					@if($jurisdiccion->claveJurisdiccion=='04' && $jurisdiccion->idActividad==$actividad->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['actividadesMetasJuris'] as $jurisdiccion)
					@if($jurisdiccion->claveJurisdiccion=='05' && $jurisdiccion->idActividad==$actividad->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['actividadesMetasJuris'] as $jurisdiccion)
					@if($jurisdiccion->claveJurisdiccion=='06' && $jurisdiccion->idActividad==$actividad->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['actividadesMetasJuris'] as $jurisdiccion)
					@if($jurisdiccion->claveJurisdiccion=='07' && $jurisdiccion->idActividad==$actividad->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['actividadesMetasJuris'] as $jurisdiccion)
					@if($jurisdiccion->claveJurisdiccion=='08' && $jurisdiccion->idActividad==$actividad->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['actividadesMetasJuris'] as $jurisdiccion)
					@if($jurisdiccion->claveJurisdiccion=='09' && $jurisdiccion->idActividad==$actividad->id)
						{{$jurisdiccion->sumeta}}
						<?php $suma += $jurisdiccion->sumeta; ?>
					@endif
				@endforeach
			</td>
			<td style="border:1px solid #000">
				@foreach($data['actividadesMetasJuris'] as $jurisdiccion)
					@if($jurisdiccion->claveJurisdiccion=='10' && $jurisdiccion->idActividad==$actividad->id)
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