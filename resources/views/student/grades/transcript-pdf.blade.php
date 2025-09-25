<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Transcript - {{ $student->user->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .school-address {
            font-size: 14px;
            color: #666;
        }
        .transcript-title {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin: 30px 0;
        }
        .student-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .info-section {
            flex: 1;
        }
        .info-item {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        .summary {
            background: #f8f9fa;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 5px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }
        .summary-item {
            text-align: center;
        }
        .summary-value {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
        }
        .summary-label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .grades-table th,
        .grades-table td {
            border: 1px solid #333;
            padding: 12px;
            text-align: left;
        }
        .grades-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        .grades-table td:first-child {
            font-weight: bold;
        }
        .grade-score {
            text-align: center;
            font-weight: bold;
        }
        .grade-letter {
            text-align: center;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 4px;
            color: white;
        }
        .grade-a-plus { background-color: #059669; }
        .grade-a { background-color: #2563eb; }
        .grade-b-plus { background-color: #d97706; }
        .grade-b { background-color: #dc2626; }
        .grade-c-plus { background-color: #dc2626; }
        .grade-c { background-color: #991b1b; }
        .grade-d { background-color: #7f1d1d; }
        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-section {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
        }
        .legend {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .legend-title {
            font-weight: bold;
            margin-bottom: 15px;
        }
        .legend-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .legend-grade {
            padding: 2px 6px;
            border-radius: 3px;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="school-name">School Name</div>
        <div class="school-address">School Address, City, Country</div>
        <div class="school-address">Phone: (123) 456-7890 | Email: info@school.com</div>
    </div>

    <!-- Transcript Title -->
    <div class="transcript-title">Academic Transcript</div>

    <!-- Student Information -->
    <div class="student-info">
        <div class="info-section">
            <div class="info-item">
                <span class="info-label">Student Name:</span>
                {{ $student->user->name }}
            </div>
            <div class="info-item">
                <span class="info-label">Student ID:</span>
                {{ $student->admission_number ?? 'N/A' }}
            </div>
            <div class="info-item">
                <span class="info-label">Class:</span>
                {{ $student->classRoom->name ?? 'N/A' }}
            </div>
        </div>
        <div class="info-section">
            <div class="info-item">
                <span class="info-label">Academic Year:</span>
                {{ date('Y') }}
            </div>
            <div class="info-item">
                <span class="info-label">Date Issued:</span>
                {{ now()->format('F d, Y') }}
            </div>
            <div class="info-item">
                <span class="info-label">Status:</span>
                Active Student
            </div>
        </div>
    </div>

    <!-- Academic Performance Summary -->
    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ $stats['total_subjects'] }}</div>
                <div class="summary-label">Subjects</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $stats['average_score'] ? number_format($stats['average_score'], 2) : 'N/A' }}</div>
                <div class="summary-label">Overall Average</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">{{ $stats['total_credits'] }}</div>
                <div class="summary-label">Total Credits</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">
                    @if($stats['average_score'])
                        @php
                            $letterGrade = '';
                            if ($stats['average_score'] >= 90) $letterGrade = 'A+';
                            elseif ($stats['average_score'] >= 80) $letterGrade = 'A';
                            elseif ($stats['average_score'] >= 70) $letterGrade = 'B+';
                            elseif ($stats['average_score'] >= 60) $letterGrade = 'B';
                            elseif ($stats['average_score'] >= 50) $letterGrade = 'C+';
                            elseif ($stats['average_score'] >= 40) $letterGrade = 'C';
                            else $letterGrade = 'D';
                        @endphp
                        {{ $letterGrade }}
                    @else
                        N/A
                    @endif
                </div>
                <div class="summary-label">Overall Grade</div>
            </div>
        </div>
    </div>

    <!-- Subject Grades Table -->
    @if($grades->count() > 0)
        <table class="grades-table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Semester 1</th>
                    <th>Semester 2</th>
                    <th>Year Average</th>
                    <th>Final Grade</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grades as $subjectName => $subjectGrades)
                    @php
                        $latestGrade = $subjectGrades->first();
                    @endphp
                    <tr>
                        <td>{{ $subjectName }}</td>
                        <td class="grade-score">
                            {{ $latestGrade->sem1_avg ? number_format($latestGrade->sem1_avg, 1) : '-' }}
                        </td>
                        <td class="grade-score">
                            {{ $latestGrade->sem2_avg ? number_format($latestGrade->sem2_avg, 1) : '-' }}
                        </td>
                        <td class="grade-score">
                            {{ $latestGrade->year_avg ? number_format($latestGrade->year_avg, 1) : '-' }}
                        </td>
                        <td class="grade-score">
                            @if($latestGrade->year_avg)
                                @php
                                    $letterGrade = '';
                                    $gradeClass = '';
                                    if ($latestGrade->year_avg >= 90) {
                                        $letterGrade = 'A+';
                                        $gradeClass = 'grade-a-plus';
                                    } elseif ($latestGrade->year_avg >= 80) {
                                        $letterGrade = 'A';
                                        $gradeClass = 'grade-a';
                                    } elseif ($latestGrade->year_avg >= 70) {
                                        $letterGrade = 'B+';
                                        $gradeClass = 'grade-b-plus';
                                    } elseif ($latestGrade->year_avg >= 60) {
                                        $letterGrade = 'B';
                                        $gradeClass = 'grade-b';
                                    } elseif ($latestGrade->year_avg >= 50) {
                                        $letterGrade = 'C+';
                                        $gradeClass = 'grade-c-plus';
                                    } elseif ($latestGrade->year_avg >= 40) {
                                        $letterGrade = 'C';
                                        $gradeClass = 'grade-c';
                                    } else {
                                        $letterGrade = 'D';
                                        $gradeClass = 'grade-d';
                                    }
                                @endphp
                                <span class="grade-letter {{ $gradeClass }}">{{ $letterGrade }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p>No grades available for transcript generation.</p>
        </div>
    @endif

    <!-- Grade Legend -->
    <div class="legend">
        <div class="legend-title">Grade Scale</div>
        <div class="legend-grid">
            <div class="legend-item">
                <span class="legend-grade grade-a-plus">A+</span>
                <span>90-100</span>
            </div>
            <div class="legend-item">
                <span class="legend-grade grade-a">A</span>
                <span>80-89</span>
            </div>
            <div class="legend-item">
                <span class="legend-grade grade-b-plus">B+</span>
                <span>70-79</span>
            </div>
            <div class="legend-item">
                <span class="legend-grade grade-b">B</span>
                <span>60-69</span>
            </div>
            <div class="legend-item">
                <span class="legend-grade grade-c-plus">C+</span>
                <span>50-59</span>
            </div>
            <div class="legend-item">
                <span class="legend-grade grade-c">C</span>
                <span>40-49</span>
            </div>
            <div class="legend-item">
                <span class="legend-grade grade-d">D</span>
                <span>0-39</span>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="signature-section">
            <div style="height: 60px; border-bottom: 1px solid #333; margin-bottom: 10px;"></div>
            <div>Academic Advisor</div>
        </div>
        <div class="signature-section">
            <div style="height: 60px; border-bottom: 1px solid #333; margin-bottom: 10px;"></div>
            <div>Registrar</div>
        </div>
    </div>
</body>
</html>
