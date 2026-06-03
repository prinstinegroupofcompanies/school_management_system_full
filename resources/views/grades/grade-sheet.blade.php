<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Grade Sheet - {{ $student->admission_no }}</title>
    <style>
        @page { margin: 15mm; }
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .logo {
            max-width: 80px;
            max-height: 80px;
            margin-bottom: 5px;
        }
        .school-name {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .school-address {
            font-size: 9pt;
            color: #666;
        }
        .title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 20px 0;
            text-decoration: underline;
        }
        .student-info {
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
            padding: 10px;
            background-color: #f5f5f5;
        }
        .info-column {
            flex: 1;
        }
        .info-row {
            margin: 5px 0;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }
        .photo-placeholder {
            width: 80px;
            height: 100px;
            border: 1px solid #000;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9pt;
            color: #666;
        }
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .grades-table th,
        .grades-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        .grades-table th {
            background-color: #e0e0e0;
            font-weight: bold;
        }
        .grades-table td.subject {
            text-align: left;
            font-weight: bold;
        }
        .summary {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-size: 11pt;
        }
        .summary-label {
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-around;
        }
        .signature-box {
            width: 200px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 40px;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($schoolLogoPath && file_exists($schoolLogoPath))
            <img src="{{ $schoolLogoPath }}" alt="School Logo" class="logo">
        @endif
        <div class="school-name">{{ $schoolName }}</div>
        <div class="school-address">{{ $schoolAddress }}</div>
    </div>

    <div class="title">STUDENT GRADE SHEET</div>

    <div class="student-info">
        <div class="info-column">
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span>{{ $student->user->name ?? ($student->first_name . ' ' . $student->last_name) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Class:</span>
                <span>{{ $student->classRoom->name ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Admission No:</span>
                <span>{{ $student->admission_no ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Student ID:</span>
                <span>{{ $student->student_id ?? 'N/A' }}</span>
            </div>
        </div>
        <div class="info-column">
            <div class="info-row">
                <span class="info-label">Term:</span>
                <span>{{ $term }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Year:</span>
                <span>{{ $year }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date:</span>
                <span>{{ $generatedDate }}</span>
            </div>
            <div class="photo-placeholder">
                Photo
            </div>
        </div>
    </div>

    <table class="grades-table">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Mid-Term</th>
                <th>Final</th>
                <th>Total</th>
                <th>Grade</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
            @forelse($grades as $grade)
            <tr>
                <td class="subject">{{ $grade['subject'] }}</td>
                <td>{{ $grade['mid_term'] }}</td>
                <td>{{ $grade['final'] }}</td>
                <td>{{ $grade['total'] }}</td>
                <td>{{ $grade['grade'] }}</td>
                <td>{{ $grade['remark'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px;">
                    No grades found for this term and year.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-row">
            <span class="summary-label">Average Score:</span>
            <span>{{ $averageScore }}%</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">GPA:</span>
            <span>{{ $gpa }}</span>
        </div>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                Class Teacher
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                Principal
            </div>
        </div>
    </div>

    <div class="footer">
        <p>This is a computer-generated document. Generated on {{ $generatedDate }}</p>
    </div>
</body>
</html>

