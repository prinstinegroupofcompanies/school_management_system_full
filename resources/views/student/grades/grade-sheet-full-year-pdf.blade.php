<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>End of Year Grade Sheet {{ $year }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2563eb; padding-bottom: 15px; }
        .school-name { font-size: 18px; font-weight: bold; color: #2563eb; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f3f4f6; }
        .summary { margin-top: 20px; padding: 15px; background: #f8fafc; border: 1px solid #e2e8f0; }
        .promotion { font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">{{ $school->name ?? config('app.name') }}</div>
        <div style="font-size: 14px; color: #666;">{{ $school->address ?? '' }}</div>
        <div style="margin-top: 10px; font-size: 16px; font-weight: bold;">End of Year Grade Sheet - Academic Year {{ $year }}</div>
    </div>

    <div>
        <strong>Student:</strong> {{ $student->user->name ?? 'N/A' }} &nbsp;|&nbsp;
        <strong>Class:</strong> {{ $student->classRoom->name ?? 'N/A' }} &nbsp;|&nbsp;
        <strong>Yearly Average:</strong> {{ number_format($stats['yearly_average'], 1) }}% &nbsp;|&nbsp;
        <strong>Promotion (70%+):</strong> {{ $stats['eligible_for_promotion'] ? 'Eligible' : 'Not eligible' }}
    </div>

    @foreach($bySemester as $sem => $semGrades)
        <h4 style="margin-top: 20px;">{{ $sem == 1 ? 'Semester 1 (Term 1)' : ($sem == 2 ? 'Semester 2 (Term 2)' : "Period {$sem}") }}</h4>
        <table>
            <thead><tr><th>Subject</th><th>Average</th></tr></thead>
            <tbody>
                @foreach($semGrades as $g)
                    <tr><td>{{ $g->subject->name ?? 'N/A' }}</td><td>{{ $g->year_avg ? number_format($g->year_avg, 1) : '-' }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <div class="summary">
        <div><strong>Final Yearly Average (all terms):</strong> {{ number_format($stats['yearly_average'], 1) }}%</div>
        <div class="promotion">Promotion status: {{ $stats['eligible_for_promotion'] ? 'Eligible for promotion (70% or above).' : 'Not eligible for promotion (below 70%).' }}</div>
    </div>
</body>
</html>
