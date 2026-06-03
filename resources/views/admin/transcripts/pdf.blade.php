<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transcript - {{ $transcript->student->user->name ?? 'Student' }} - {{ $transcript->academic_year }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 24px; border-bottom: 2px solid #2563eb; padding-bottom: 16px; }
        .school { font-size: 18px; font-weight: bold; color: #2563eb; }
        .title { font-size: 16px; margin-top: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; font-size: 12px; }
        th { background: #f3f4f6; font-weight: bold; }
        .term-title { margin-top: 20px; font-weight: bold; font-size: 14px; }
        .meta { margin-bottom: 20px; }
        .meta p { margin: 4px 0; }
    </style>
</head>
<body>
    <div class="header">
        <div class="school">{{ $school?->name ?? 'School' }}</div>
        <div class="title">Academic Transcript — {{ $transcript->academic_year }}</div>
    </div>

    <div class="meta">
        <p><strong>Student:</strong> {{ $transcript->student->user->name ?? '—' }}</p>
        <p><strong>Class:</strong> {{ $transcript->student->classRoom->name ?? '—' }}</p>
        <p><strong>CGPA:</strong> {{ number_format($transcript->cgpa ?? 0, 2) }}</p>
        <p><strong>Generated:</strong> {{ $transcript->generated_at?->format('F d, Y') ?? '—' }}</p>
    </div>

    @if(!empty($transcript->terms_data) && is_array($transcript->terms_data))
        @foreach($transcript->terms_data as $termBlock)
            <div class="term-title">{{ $termBlock['term'] ?? 'Term' }} — GPA: {{ number_format($termBlock['gpa'] ?? 0, 2) }}</div>
            <table>
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Mid-term</th>
                        <th>Final</th>
                        <th>Total</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($termBlock['subjects'] ?? [] as $row)
                        <tr>
                            <td>{{ $row['subject'] ?? '—' }}</td>
                            <td>{{ $row['mid_term'] ?? '—' }}</td>
                            <td>{{ $row['final'] ?? '—' }}</td>
                            <td>{{ $row['total'] ?? '—' }}</td>
                            <td>{{ $row['grade'] ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @endif

    @if($transcript->remarks)
        <p><strong>Remarks:</strong> {{ $transcript->remarks }}</p>
    @endif
</body>
</html>
