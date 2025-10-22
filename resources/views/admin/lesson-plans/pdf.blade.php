<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lesson Plan - {{ $lessonPlan->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #3B82F6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1E40AF;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #6B7280;
            margin: 5px 0 0 0;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .info-row {
            display: table-row;
        }
        .info-cell {
            display: table-cell;
            padding: 8px 0;
            border-bottom: 1px solid #E5E7EB;
        }
        .info-label {
            font-weight: bold;
            width: 30%;
            color: #374151;
        }
        .info-value {
            width: 70%;
            color: #6B7280;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h3 {
            color: #1E40AF;
            border-bottom: 1px solid #3B82F6;
            padding-bottom: 5px;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .section p {
            margin: 0;
            white-space: pre-line;
            color: #374151;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-draft { background-color: #F3F4F6; color: #374151; }
        .status-submitted { background-color: #DBEAFE; color: #1E40AF; }
        .status-first-level-approved { background-color: #FEF3C7; color: #92400E; }
        .status-second-level-approved { background-color: #D1FAE5; color: #065F46; }
        .status-rejected { background-color: #FEE2E2; color: #991B1B; }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
            text-align: center;
            color: #6B7280;
            font-size: 12px;
        }
        .attachments {
            background-color: #F9FAFB;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #3B82F6;
        }
        .attachments h4 {
            margin: 0 0 10px 0;
            color: #1E40AF;
            font-size: 14px;
        }
        .attachment-list {
            margin: 0;
            padding-left: 20px;
        }
        .attachment-list li {
            margin-bottom: 5px;
            color: #6B7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $lessonPlan->title }}</h1>
        <p>Lesson Plan Document</p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <div class="info-grid">
        <div class="info-row">
            <div class="info-cell info-label">Teacher:</div>
            <div class="info-cell info-value">{{ $lessonPlan->teacher->user->name ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-cell info-label">Subject:</div>
            <div class="info-cell info-value">{{ $lessonPlan->subject->name ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-cell info-label">Class:</div>
            <div class="info-cell info-value">{{ $lessonPlan->class->name ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-cell info-label">Lesson Date:</div>
            <div class="info-cell info-value">{{ $lessonPlan->lesson_date ? $lessonPlan->lesson_date->format('F j, Y') : 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-cell info-label">Time:</div>
            <div class="info-cell info-value">{{ $lessonPlan->start_time }} - {{ $lessonPlan->end_time }}</div>
        </div>
        <div class="info-row">
            <div class="info-cell info-label">Duration:</div>
            <div class="info-cell info-value">{{ $lessonPlan->duration_minutes ?? 'N/A' }} minutes</div>
        </div>
        <div class="info-row">
            <div class="info-cell info-label">Status:</div>
            <div class="info-cell info-value">
                <span class="status-badge status-{{ $lessonPlan->status }}">
                    {{ ucfirst(str_replace('_', ' ', $lessonPlan->status)) }}
                </span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-cell info-label">Created:</div>
            <div class="info-cell info-value">{{ $lessonPlan->created_at->format('F j, Y \a\t g:i A') }}</div>
        </div>
    </div>

    <div class="section">
        <h3>Description</h3>
        <p>{{ $lessonPlan->description ?? 'No description provided.' }}</p>
    </div>

    <div class="section">
        <h3>Learning Objectives</h3>
        <p>{{ $lessonPlan->objectives ?? 'No objectives provided.' }}</p>
    </div>

    <div class="section">
        <h3>Materials Needed</h3>
        <p>{{ $lessonPlan->materials_needed ?? 'No materials specified.' }}</p>
    </div>

    <div class="section">
        <h3>Activities</h3>
        <p>{{ $lessonPlan->activities ?? 'No activities specified.' }}</p>
    </div>

    <div class="section">
        <h3>Assessment</h3>
        <p>{{ $lessonPlan->assessment ?? 'No assessment specified.' }}</p>
    </div>

    @if($lessonPlan->homework)
    <div class="section">
        <h3>Homework</h3>
        <p>{{ $lessonPlan->homework }}</p>
    </div>
    @endif

    @if($lessonPlan->notes)
    <div class="section">
        <h3>Additional Notes</h3>
        <p>{{ $lessonPlan->notes }}</p>
    </div>
    @endif

    @if($lessonPlan->attachments && count($lessonPlan->attachments) > 0)
    <div class="attachments">
        <h4>Attachments</h4>
        <ul class="attachment-list">
            @foreach($lessonPlan->attachments as $attachment)
            <li>{{ $attachment['name'] ?? 'Unknown file' }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="footer">
        <p>This lesson plan was generated from the School Management System</p>
        <p>Lesson Plan ID: {{ $lessonPlan->id }} | Document Version: 1.0</p>
    </div>
</body>
</html>
