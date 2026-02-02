<!DOCTYPE html>
<html>

<head>
    <title>Accesos a documentos</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        h1 {
            text-align: center;
            margin-bottom: 5px;
        }

        .report-meta {
            text-align: center;
            font-size: 10px;
            margin-bottom: 15px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #2c3e50;
            color: #fff;
            font-size: 11px;
            text-transform: uppercase;
            padding: 8px;
        }

        td {
            padding: 7px;
            border-bottom: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background-color: #f7f9fb;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 9px;
            text-align: center;
            color: #999;
        }
    </style>
</head>

<body>

    <h1>Accesos a documentos</h1>
    <div class="report-meta">
        Generado: {{ $generated_at }}
    </div>

    <table>
        <tr>
            <th>#</th>
            <th>Usuario</th>
            <th>Email</th>
            <th>Documento</th>
            <th>Estado</th>
        </tr>
        @foreach ($data as $i => $acc)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $acc->user->name }}</td>
                <td>{{ $acc->user->email }}</td>
                <td>{{ $acc->documento->name }}</td>
                <td>{{ strtoupper($acc->estado) }}</td>
            </tr>
        @endforeach
    </table>

    <div class="footer">
        Sistema de Normas ISO - Reporte automático
    </div>

</body>

</html>
