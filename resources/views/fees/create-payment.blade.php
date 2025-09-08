@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl mx-auto bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Record Payment</h1>
            <a href="{{ route('fees.payments.index') }}" class="px-3 py-2 bg-gray-600 text-white rounded">Back</a>
        </div>
        <form method="POST" action="{{ route('fees.payments.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Name of Student</label>
                <select name="student_id" id="student_id" class="mt-1 block w-full border-gray-300 rounded-md" required>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" data-class="{{ $student->class->name ?? '' }}">{{ $student->user->name ?? 'N/A' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Student Class</label>
                <input type="text" id="student_class" class="mt-1 block w-full border-gray-300 rounded-md bg-gray-50" readonly>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Amount Paid</label>
                <input type="number" name="amount" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Payment Date</label>
                <input type="date" name="payment_date" class="mt-1 block w-full border-gray-300 rounded-md" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                <select name="payment_method" class="mt-1 block w-full border-gray-300 rounded-md" required>
                    <option value="cash">Cash</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="mobile_money">Mobile Money</option>
                    <option value="cheque">Cheque</option>
                    <option value="card">Card</option>
                </select>
            </div>
            <div class="pt-2">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Save Payment</button>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
  const sel = document.getElementById('student_id');
  const out = document.getElementById('student_class');
  function update(){ const o = sel.options[sel.selectedIndex]; out.value = o ? (o.getAttribute('data-class')||'') : ''; }
  sel.addEventListener('change', update); update();
});
</script>
@endsection


