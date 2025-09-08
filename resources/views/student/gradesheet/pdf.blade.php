<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Gradesheet</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
    </head>
<body>
    <div class="header">
        <h2>Gradesheet - {{ $student->user->name }} ({{ $year }})</h2>
        <div>Student ID: {{ $student->student_id }}</div>
        <div>Class: {{ $student->class->name ?? '' }}</div>
        <div>Period: {{ ucfirst($period) }}</div>
    </div>
    <table>
        <thead>
        <tr>
            <th>Subject</th>
            <th>Sem 1 Avg</th>
            <th>Sem 2 Avg</th>
            <th>Year Avg</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($grades as $g)
            <tr>
                <td>{{ $g->subject->name ?? '' }}</td>
                <td>{{ $g->sem1_avg ?? '-' }}</td>
                <td>{{ $g->sem2_avg ?? '-' }}</td>
                <td>{{ $g->year_avg ?? '-' }}</td>
                <td style="text-transform: capitalize;">{{ $g->status }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>


