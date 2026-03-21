<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Boleta de Notas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #333; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2563eb; padding-bottom: 15px; }
        .header h1 { font-size: 16px; color: #1e40af; margin-bottom: 4px; }
        .header p { font-size: 10px; color: #666; }
        .info-grid { display: table; width: 100%; margin-bottom: 15px; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; width: 120px; font-weight: bold; padding: 3px 0; color: #555; }
        .info-value { display: table-cell; padding: 3px 0; }
        table.notas { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table.notas th, table.notas td { border: 1px solid #ddd; padding: 6px 8px; text-align: center; }
        table.notas th { background: #2563eb; color: #fff; font-size: 10px; text-transform: uppercase; }
        table.notas td.curso { text-align: left; font-weight: bold; }
        table.notas td.promedio { background: #f0f9ff; font-weight: bold; }
        .nota-alta { color: #16a34a; }
        .nota-media { color: #ca8a04; }
        .nota-baja { color: #dc2626; }
        .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
        .asistencia { margin-bottom: 15px; }
        .asistencia h3 { font-size: 12px; margin-bottom: 5px; }
        .asistencia-grid { display: table; width: 50%; }
        .asistencia-grid .row { display: table-row; }
        .asistencia-grid .label, .asistencia-grid .value { display: table-cell; padding: 2px 5px; }
        .asistencia-grid .value { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $colegio->nombre }}</h1>
        <p>{{ $colegio->direccion ?? '' }}</p>
        <p style="margin-top:8px; font-size:13px; font-weight:bold;">BOLETA DE NOTAS</p>
    </div>

    <div class="info-grid">
        <div class="info-row">
            <span class="info-label">Alumno:</span>
            <span class="info-value">{{ $alumno->user->apellidos }}, {{ $alumno->user->nombre }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Sección:</span>
            <span class="info-value">{{ $matricula->seccion->nombreCompleto() }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Periodo:</span>
            <span class="info-value">{{ $matricula->periodo->nombre }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha emisión:</span>
            <span class="info-value">{{ now()->format('d/m/Y') }}</span>
        </div>
    </div>

    <table class="notas">
        <thead>
            <tr>
                <th style="text-align:left;">Curso</th>
                @foreach($bimestres as $bimestre)
                <th>{{ $bimestre->nombre }}</th>
                @endforeach
                <th>Promedio</th>
            </tr>
        </thead>
        <tbody>
            @php $promedioGeneral = 0; $cursosConNota = 0; @endphp
            @foreach($cursos as $cursoSeccion)
            <tr>
                <td class="curso">{{ $cursoSeccion->curso->nombre }}</td>
                @php $total = 0; $count = 0; @endphp
                @foreach($bimestres as $bimestre)
                @php
                    $nota = $notas->where('curso_seccion_id', $cursoSeccion->id)->where('bimestre_id', $bimestre->id)->first();
                @endphp
                <td>
                    @if($nota)
                    <span class="{{ $nota->nota >= 14 ? 'nota-alta' : ($nota->nota >= 11 ? 'nota-media' : 'nota-baja') }}">
                        {{ $nota->nota }} ({{ $nota->nota_letra }})
                    </span>
                    @php $total += $nota->nota; $count++; @endphp
                    @else
                    —
                    @endif
                </td>
                @endforeach
                <td class="promedio">
                    @if($count > 0)
                    @php $prom = $total / $count; $promedioGeneral += $prom; $cursosConNota++; @endphp
                    {{ number_format($prom, 1) }}
                    @else
                    —
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td class="curso" colspan="{{ count($bimestres) + 1 }}" style="text-align:right;">PROMEDIO GENERAL</td>
                <td class="promedio" style="font-size:13px;">
                    @if($cursosConNota > 0)
                    {{ number_format($promedioGeneral / $cursosConNota, 1) }}
                    @else
                    —
                    @endif
                </td>
            </tr>
        </tfoot>
    </table>

    @if(isset($asistencia))
    <div class="asistencia">
        <h3>Resumen de Asistencia</h3>
        <div class="asistencia-grid">
            <div class="row"><span class="label">Presente:</span><span class="value">{{ $asistencia['presente'] ?? 0 }}</span></div>
            <div class="row"><span class="label">Faltas:</span><span class="value">{{ $asistencia['falta'] ?? 0 }}</span></div>
            <div class="row"><span class="label">Tardanzas:</span><span class="value">{{ $asistencia['tardanza'] ?? 0 }}</span></div>
            <div class="row"><span class="label">Justificadas:</span><span class="value">{{ $asistencia['justificada'] ?? 0 }}</span></div>
        </div>
    </div>
    @endif

    <div class="footer">
        Documento generado electrónicamente por {{ $colegio->nombre }} · {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
