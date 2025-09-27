<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Sheet - Period {{ $semester }} - {{ $year }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            background: white;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
        }
        
        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        
        .school-address {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .document-title {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin-top: 15px;
        }
        
        .student-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
        }
        
        .info-left, .info-right {
            flex: 1;
        }
        
        .info-item {
            display: flex;
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            width: 120px;
            color: #374151;
        }
        
        .info-value {
            color: #1f2937;
        }
        
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .grades-table th {
            background: #2563eb;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        
        .grades-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .grades-table tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .grades-table tr:hover {
            background: #f3f4f6;
        }
        
        .grade-excellent {
            color: #059669;
            font-weight: bold;
        }
        
        .grade-good {
            color: #2563eb;
            font-weight: bold;
        }
        
        .grade-average {
            color: #d97706;
            font-weight: bold;
        }
        
        .grade-poor {
            color: #dc2626;
            font-weight: bold;
        }
        
        .statistics {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
        }
        
        .stat-label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
        }
        
        .signature-left {
            flex: 1;
        }
        
        .signature-right {
            flex: 1;
            text-align: center;
        }
        
        .signature-box {
            width: 200px;
            height: 80px;
            border: 1px solid #d1d5db;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f9fafb;
        }
        
        .signature-image {
            max-width: 180px;
            max-height: 70px;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        
        .no-grades {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            
            .header {
                page-break-inside: avoid;
            }
            
            .grades-table {
                page-break-inside: avoid;
            }
            
            .signature-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="school-name">
            {{ $school->name ?? 'Liberia School Management System' }}
        </div>
        <div class="school-address">
            {{ $school->address ?? 'Monrovia, Liberia' }}
        </div>
        <div class="document-title">
            OFFICIAL GRADE SHEET - PERIOD {{ $semester }} - {{ $year }}
        </div>
    </div>

    <!-- Student Information -->
    <div class="student-info">
        <div class="info-left">
            <div class="info-item">
                <span class="info-label">Student Name:</span>
                <span class="info-value">{{ $student->user->name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Student ID:</span>
                <span class="info-value">{{ $student->student_id }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Class:</span>
                <span class="info-value">{{ $student->classRoom->name ?? 'N/A' }}</span>
            </div>
        </div>
        <div class="info-right">
            <div class="info-item">
                <span class="info-label">Period:</span>
                <span class="info-value">{{ $semester }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Academic Year:</span>
                <span class="info-value">{{ $year }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Date Generated:</span>
                <span class="info-value">{{ date('F d, Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Grades Table -->
    @if($grades->count() > 0)
        <table class="grades-table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Teacher</th>
                    <th>Period Average</th>
                    <th>Grade</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grades as $grade)
                    <tr>
                        <td>{{ $grade->subject->name ?? 'N/A' }}</td>
                        <td>{{ $grade->teacher->user->name ?? 'N/A' }}</td>
                        <td>{{ number_format($grade->year_avg, 2) }}%</td>
                        <td class="
                            @if($grade->year_avg >= 90) grade-excellent
                            @elseif($grade->year_avg >= 80) grade-good
                            @elseif($grade->year_avg >= 70) grade-average
                            @else grade-poor
                            @endif
                        ">
                            @if($grade->year_avg >= 90) A
                            @elseif($grade->year_avg >= 80) B
                            @elseif($grade->year_avg >= 70) C
                            @elseif($grade->year_avg >= 60) D
                            @else F
                            @endif
                        </td>
                        <td>
                            <span style="color: #059669; font-weight: bold;">{{ ucfirst($grade->status) }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-grades">
            <h3>No grades available for this period</h3>
            <p>Grades will appear here once teachers submit them for Period {{ $semester }} - {{ $year }}.</p>
        </div>
    @endif

    <!-- Statistics -->
    @if($grades->count() > 0)
        <div class="statistics">
            <div class="stat-item">
                <div class="stat-value">{{ $stats['total_subjects'] }}</div>
                <div class="stat-label">Total Subjects</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ number_format($stats['average_score'], 1) }}%</div>
                <div class="stat-label">Average Score</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ number_format($stats['highest_score'], 1) }}%</div>
                <div class="stat-label">Highest Score</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $stats['passed_subjects'] }}</div>
                <div class="stat-label">Passed Subjects</div>
            </div>
        </div>
    @endif

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-left">
            <div style="margin-bottom: 20px;">
                <strong>Remarks:</strong>
            </div>
            <div style="border-bottom: 1px solid #d1d5db; padding-bottom: 10px; margin-bottom: 20px;">
                <!-- Empty line for remarks -->
            </div>
            <div style="font-size: 14px;">
                <strong>Authorized Signature</strong>
            </div>
        </div>
        
        <div class="signature-right">
            <div class="signature-box">
                @if($adminSignature)
                    <img src="{{ public_path('storage/' . $adminSignature) }}" alt="Authorized Signature" class="signature-image">
                @else
                    <span style="color: #9ca3af;">Signature</span>
                @endif
            </div>
            <div style="font-size: 14px;">
                <div style="border-top: 1px solid #d1d5db; padding-top: 5px; width: 200px; margin: 0 auto;"></div>
                <span style="font-size: 12px;">Authorized Signature</span>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This is an official document generated by Liberia School Management System</p>
        <p>Generated on {{ date('F d, Y \a\t g:i A') }}</p>
    </div>
</body>
</html>
