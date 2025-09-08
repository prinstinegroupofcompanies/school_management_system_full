<?php

namespace Tests\Feature\Finance;

use App\Models\ClassRoom;
use App\Models\FeeItem;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_generate_invoice_pdf_view(): void
    {
        $this->withoutExceptionHandling();
        $finance = User::factory()->create(['user_type' => 'finance']);
        $class = ClassRoom::factory()->create();
        $studentUser = User::factory()->create(['user_type' => 'student']);
        $student = Student::factory()->create(['user_id' => $studentUser->id, 'class_id' => $class->id]);

        FeeItem::create([
            'item_name' => 'Tuition',
            'quantity' => 1,
            'price_per_unit' => 100,
            'total' => 100,
            'class_id' => $class->id,
            'is_active' => true,
        ]);

        $this->actingAs($finance)
            ->get(route('finance.invoices.create'))
            ->assertStatus(200);
    }
}


