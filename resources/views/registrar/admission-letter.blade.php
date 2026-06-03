<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admission Letter - {{ $student->admission_no }}</title>
    <style>
        @page { margin: 20mm; }
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 100px;
            max-height: 100px;
            margin-bottom: 10px;
        }
        .school-name {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .school-address {
            font-size: 10pt;
            color: #666;
        }
        .letter-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            margin: 30px 0;
            text-decoration: underline;
        }
        .student-info {
            margin: 20px 0;
        }
        .info-row {
            margin: 8px 0;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .content {
            margin: 30px 0;
            text-align: justify;
        }
        .signature-section {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 250px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
        .qr-code img {
            max-width: 150px;
            height: auto;
        }
        .footer {
            margin-top: 40px;
            font-size: 10pt;
            color: #666;
            text-align: center;
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

    <div class="letter-title">ADMISSION LETTER</div>

    <div class="content">
        <p><strong>Date:</strong> {{ $generatedDate }}</p>
        <br>
        <p>This is to certify that <strong>{{ $student->user->name }}</strong> has been admitted to <strong>{{ $schoolName }}</strong> 
        for the academic year <strong>{{ $student->academic_year }}</strong>.</p>
        
        <div class="student-info">
            <div class="info-row">
                <span class="info-label">Admission Number:</span>
                <span>{{ $student->admission_no }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Student ID:</span>
                <span>{{ $student->student_id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Class:</span>
                <span>{{ $student->classRoom->name ?? 'N/A' }}</span>
            </div>
            @if($student->section)
            <div class="info-row">
                <span class="info-label">Section:</span>
                <span>{{ $student->section->name }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Date of Birth:</span>
                <span>{{ $student->date_of_birth ? $student->date_of_birth->format('F d, Y') : 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Gender:</span>
                <span>{{ ucfirst($student->gender) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Admission Date:</span>
                <span>{{ $student->admission_date ? $student->admission_date->format('F d, Y') : 'N/A' }}</span>
            </div>
        </div>

        <p style="margin-top: 20px;">The student is hereby granted admission and is expected to comply with all school rules and regulations.</p>

        @if(isset($username) && $username)
        <div style="margin-top: 30px; padding: 15px; background-color: #f5f5f5; border: 1px solid #ddd; border-radius: 5px;">
            <h4 style="margin-bottom: 15px; color: #333;">Login Credentials</h4>
            <div class="info-row">
                <span class="info-label">Username:</span>
                <span style="font-family: monospace; font-weight: bold; color: #007bff;">{{ $username }}</span>
            </div>
            @if(isset($rawPassword) && $rawPassword)
            <div class="info-row">
                <span class="info-label">Password:</span>
                <span style="font-family: monospace; font-weight: bold; color: #dc3545;">{{ $rawPassword }}</span>
            </div>
            @endif
            <p style="margin-top: 15px; font-size: 11pt; color: #666;">
                <strong>Important:</strong> Please change your password after your first login for security purposes.
            </p>
            <p style="margin-top: 10px; font-size: 11pt;">
                Access your dashboard at: <a href="{{ url('/') }}">{{ url('/') }}</a>
            </p>
        </div>
        @endif
    </div>

    @if($qrCode)
    <div class="qr-code">
        <p><strong>Student QR Code:</strong></p>
        <img src="data:image/png;base64,{{ $qrCode }}" alt="Student QR Code">
    </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                @if($signaturePath && file_exists($signaturePath))
                    <img src="{{ $signaturePath }}" alt="Registrar Signature" style="max-width: 150px; max-height: 60px;">
                @else
                    <br><br>
                @endif
                <strong>{{ $generatedBy }}</strong><br>
                Registrar
            </div>
        </div>
    </div>

    @if(isset($schoolDescription) && $schoolDescription)
    <div style="margin-top: 40px; padding: 20px; background-color: #f9f9f9; border-top: 2px solid #333;">
        <h4 style="margin-bottom: 10px;">About {{ config('school.name', 'the School') }}</h4>
        <p style="text-align: justify; line-height: 1.8; font-size: 11pt;">{{ $schoolDescription }}</p>
    </div>
    @endif

    <div class="footer">
        <p>This is a computer-generated document. Generated on {{ $generatedDate }}</p>
    </div>
</body>
</html>

