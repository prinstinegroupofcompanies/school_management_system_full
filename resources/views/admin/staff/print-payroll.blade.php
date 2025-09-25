<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll - {{ $payroll->staff->user->name ?? 'N/A' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .payroll-title {
            font-size: 18px;
            color: #666;
        }
        .payroll-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .staff-info, .payroll-details {
            width: 48%;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section h3 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        .salary-breakdown {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .earnings, .deductions {
            width: 48%;
        }
        .breakdown-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            padding: 2px 0;
        }
        .breakdown-total {
            border-top: 2px solid #333;
            margin-top: 10px;
            padding-top: 10px;
            font-weight: bold;
        }
        .net-pay {
            text-align: center;
            background: #f0f0f0;
            padding: 20px;
            border: 2px solid #333;
            margin: 30px 0;
        }
        .net-pay-amount {
            font-size: 24px;
            font-weight: bold;
            color: #2d5a2d;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">School Management System</div>
        <div class="payroll-title">PAYROLL STATEMENT</div>
    </div>

    <div class="payroll-info">
        <div class="staff-info">
            <div class="info-section">
                <h3>Staff Information</h3>
                <div class="info-row">
                    <span class="info-label">Name:</span>
                    <span class="info-value">{{ $payroll->staff->user->name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Employee ID:</span>
                    <span class="info-value">{{ $payroll->staff->employee_id ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Department:</span>
                    <span class="info-value">{{ $payroll->staff->department->name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Designation:</span>
                    <span class="info-value">{{ $payroll->staff->designation->name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <div class="payroll-details">
            <div class="info-section">
                <h3>Payroll Details</h3>
                <div class="info-row">
                    <span class="info-label">Payroll #:</span>
                    <span class="info-value">{{ $payroll->payroll_number ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Pay Date:</span>
                    <span class="info-value">{{ $payroll->pay_date ? $payroll->pay_date->format('M d, Y') : 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Pay Period:</span>
                    <span class="info-value">
                        {{ $payroll->pay_period_start ? $payroll->pay_period_start->format('M d') : 'N/A' }} - 
                        {{ $payroll->pay_period_end ? $payroll->pay_period_end->format('M d, Y') : 'N/A' }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">{{ ucfirst($payroll->status) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="salary-breakdown">
        <div class="earnings">
            <h3>Earnings</h3>
            <div class="breakdown-row">
                <span>Basic Salary</span>
                <span>${{ number_format($payroll->basic_salary, 2) }}</span>
            </div>
            @if($payroll->housing_allowance > 0)
            <div class="breakdown-row">
                <span>Housing Allowance</span>
                <span>${{ number_format($payroll->housing_allowance, 2) }}</span>
            </div>
            @endif
            @if($payroll->transport_allowance > 0)
            <div class="breakdown-row">
                <span>Transport Allowance</span>
                <span>${{ number_format($payroll->transport_allowance, 2) }}</span>
            </div>
            @endif
            @if($payroll->meal_allowance > 0)
            <div class="breakdown-row">
                <span>Meal Allowance</span>
                <span>${{ number_format($payroll->meal_allowance, 2) }}</span>
            </div>
            @endif
            @if($payroll->medical_allowance > 0)
            <div class="breakdown-row">
                <span>Medical Allowance</span>
                <span>${{ number_format($payroll->medical_allowance, 2) }}</span>
            </div>
            @endif
            @if($payroll->bonus > 0)
            <div class="breakdown-row">
                <span>Bonus</span>
                <span>${{ number_format($payroll->bonus, 2) }}</span>
            </div>
            @endif
            @if($payroll->overtime_hours > 0)
            <div class="breakdown-row">
                <span>Overtime ({{ $payroll->overtime_hours }}h × ${{ number_format($payroll->overtime_rate, 2) }})</span>
                <span>${{ number_format($payroll->overtime_hours * $payroll->overtime_rate, 2) }}</span>
            </div>
            @endif
            <div class="breakdown-row breakdown-total">
                <span>Gross Salary</span>
                <span>${{ number_format($payroll->gross_salary, 2) }}</span>
            </div>
        </div>

        <div class="deductions">
            <h3>Deductions</h3>
            @if($payroll->income_tax > 0)
            <div class="breakdown-row">
                <span>Income Tax</span>
                <span>${{ number_format($payroll->income_tax, 2) }}</span>
            </div>
            @endif
            @if($payroll->social_security > 0)
            <div class="breakdown-row">
                <span>Social Security</span>
                <span>${{ number_format($payroll->social_security, 2) }}</span>
            </div>
            @endif
            @if($payroll->pension_contribution > 0)
            <div class="breakdown-row">
                <span>Pension Contribution</span>
                <span>${{ number_format($payroll->pension_contribution, 2) }}</span>
            </div>
            @endif
            @if($payroll->health_insurance > 0)
            <div class="breakdown-row">
                <span>Health Insurance</span>
                <span>${{ number_format($payroll->health_insurance, 2) }}</span>
            </div>
            @endif
            @if($payroll->loan_deduction > 0)
            <div class="breakdown-row">
                <span>Loan Deduction</span>
                <span>${{ number_format($payroll->loan_deduction, 2) }}</span>
            </div>
            @endif
            @if($payroll->advance_deduction > 0)
            <div class="breakdown-row">
                <span>Advance Deduction</span>
                <span>${{ number_format($payroll->advance_deduction, 2) }}</span>
            </div>
            @endif
            <div class="breakdown-row breakdown-total">
                <span>Total Deductions</span>
                <span>${{ number_format($payroll->total_deductions, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="net-pay">
        <div>NET PAY</div>
        <div class="net-pay-amount">${{ number_format($payroll->net_salary, 2) }}</div>
    </div>

    @if($payroll->notes)
    <div class="info-section">
        <h3>Notes</h3>
        <p>{{ $payroll->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Generated on {{ now()->format('M d, Y \a\t H:i') }}</p>
        <p>This is a computer-generated document.</p>
    </div>

    <script>
        // Auto-print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
