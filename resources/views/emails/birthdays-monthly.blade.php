<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            color: #1a1a1a;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th {
            background-color: #f8f9fa;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
            font-size: 14px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
            font-size: 14px;
        }

        .phones {
            font-size: 13px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
        }
    </style>
</head>

<body>
    <h1>🎂 Birthdays in {{ now()->format('F Y') }}</h1>

    @if($birthdays->count() > 0)
        <p>The following alumni have birthdays this month:</p>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone(s)</th>
                    <th>Email</th>
                    <th>Birth Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($birthdays->sortBy(fn($a) => $a->birth_date?->day) as $alumnus)
                    <tr>
                        <td>{{ $alumnus->name }}</td>
                        <td class="phones">
                            @if($alumnus->phones && count($alumnus->phones) > 0)
                                {{ implode(', ', $alumnus->phones) }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $alumnus->email ?? 'N/A' }}</td>
                        <td>{{ $alumnus->birth_date?->format('M j') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p>Mark your calendar for these upcoming celebrations!</p>
    @else
        <p>No birthdays this month.</p>
    @endif

    <div class="footer">
        Thanks,<br>
        {{ config('app.name') }}
    </div>
</body>

</html>