<?php

namespace Tests\Feature\Finance;

use App\Models\ClassRoom;
use App\Models\PaymentRecord;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_finance_can_approve_payment(): void
    {
        $finance = User::factory()->create(['user_type' => 'finance']);
        $class = ClassRoom::factory()->create();
        $studentUser = User::factory()->create(['user_type' => 'student']);
        $student = Student::factory()->create(['user_id' => $studentUser->id, 'class_id' => $class->id]);
        $fee = StudentFee::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'semester' => 'Semester 1',
            'year' => 2025,
            'total_amount' => 200,
            'paid_amount' => 0,
            'balance' => 200,
        ]);
        $payment = PaymentRecord::create([
            'student_id' => $student->id,
            'fee_id' => $fee->id,
            'amount' => 50,
            'payment_method' => 'Bank',
            'transaction_reference' => 'TX123',
            'status' => 'pending',
        ]);

        $this->actingAs($finance)
            ->post(route('finance.payments.approve', $payment))
            ->assertRedirect();

        $payment->refresh();
        $fee->refresh();
        $this->assertEquals('approved', $payment->status);
        $this->assertEquals(50.00, (float) $fee->paid_amount);
        $this->assertEquals(150.00, (float) $fee->balance);
    }
}


