<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Export des Aliments</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport des Aliments</h1>
        <p>Généré le: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                @if(in_array('name', $columns))<th>Nom</th>@endif
                @if(in_array('description', $columns))<th>Description</th>@endif
                @if(in_array('calories', $columns))<th>Calories (kcal)</th>@endif
                @if(in_array('protein', $columns))<th>Protéines (g)</th>@endif
                @if(in_array('carbs', $columns))<th>Glucides (g)</th>@endif
                @if(in_array('fat', $columns))<th>Lipides (g)</th>@endif
                @if(in_array('is_approved', $columns))<th>Statut</th>@endif
                @if(in_array('created_at', $columns))<th>Date d'ajout</th>@endif
            </tr>
        </thead>
        <tbody>
            @forelse($foods as $food)
                <tr>
                    @if(in_array('name', $columns))<td>{{ $food['Nom'] ?? '' }}</td>@endif
                    @if(in_array('description', $columns))<td>{{ $food['Description'] ?? '' }}</td>@endif
                    @if(in_array('calories', $columns))<td>{{ $food['Calories (kcal)'] ?? '' }}</td>@endif
                    @if(in_array('protein', $columns))<td>{{ $food['Protéines (g)'] ?? '' }}</td>@endif
                    @if(in_array('carbs', $columns))<td>{{ $food['Glucides (g)'] ?? '' }}</td>@endif
                    @if(in_array('fat', $columns))<td>{{ $food['Lipides (g)'] ?? '' }}</td>@endif
                    @if(in_array('is_approved', $columns))<td>{{ $food['Statut'] ?? '' }}</td>@endif
                    @if(in_array('created_at', $columns))<td>{{ $food["Date d'ajout"] ?? '' }}</td>@endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) }}" style="text-align: center;">Aucune donnée disponible</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Généré par SmartHealth Tracker - {{ config('app.name') }} - Page {PAGENO} sur {nb}
    </div>
</body>
</html>
