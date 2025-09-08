@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Add Student</h1>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('admin.students.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Student ID (optional)</label>
            <input type="text" name="student_id" class="form-control" value="{{ old('student_id') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Class</label>
            <select name="class_id" class="form-select" required>
                <option value="">Select class</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}" @if(old('class_id')==$class->id) selected @endif>{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Semester</label>
                <select name="semester" class="form-select">
                    <option value="">Default/All</option>
                    @foreach ($semesters as $sem)
                        <option value="{{ $sem }}" @if(old('semester')==$sem) selected @endif>{{ $sem }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Year</label>
                <input type="number" name="year" class="form-control" min="2000" max="2100" value="{{ old('year', $currentYear) }}" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-control" value="{{ old('address') }}">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Create Student</button>
    </form>
</div>
@endsection


