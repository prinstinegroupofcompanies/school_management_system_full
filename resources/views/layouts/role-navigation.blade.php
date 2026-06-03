@php
$hasRoleBased = auth()->check() && (auth()->user()->roles()->count() > 0);
if (!function_exists('safeRoute')) {
    function safeRoute($name, $parameters = [], $fallback = '#') {
        try {
            if (Route::has($name)) {
                return route($name, $parameters);
            }
        } catch (\Exception $e) {
            \Log::warning("Navigation route error: {$name}", ['error' => $e->getMessage()]);
        }
        $fallbackRoutes = [
            'admin.students.index' => '/admin/students', 'admin.students.create' => '/admin/students/create',
            'admin.teachers.index' => '/admin/teachers', 'admin.teachers.create' => '/admin/teachers/create',
            'admin.classes.index' => '/admin/classes', 'admin.classes.create' => '/admin/classes/create',
            'admin.subjects.index' => '/admin/subjects', 'admin.subjects.create' => '/admin/subjects/create',
            'admin.grades.index' => '/admin/grades', 'admin.grades.analytics' => '/admin/grades/analytics',
            'admin.transcripts.index' => '/admin/transcripts', 'admin.transcripts.create' => '/admin/transcripts/create',
            'admin.staff.index' => '/admin/staff', 'admin.staff.performance' => '/admin/staff/performance',
            'admin.staff.schedules' => '/admin/staff/schedules', 'admin.staff.payroll' => '/admin/staff/payroll',
            'admin.attendance.index' => '/admin/attendance', 'admin.attendance.students' => '/admin/attendance/students',
            'admin.attendance.teachers' => '/admin/attendance/teachers',
            'admin.users.index' => '/admin/users', 'admin.users.create' => '/admin/users/create',
            'admin.notifications.index' => '/admin/notifications', 'admin.notifications.create' => '/admin/notifications/create',
            'admin.notifications.templates' => '/admin/notifications/templates',
            'admin.reports.index' => '/admin/reports', 'admin.reports.academic' => '/admin/reports/academic',
            'admin.reports.financial' => '/admin/reports/financial',
            'admin.lesson-plans.index' => '/admin/lesson-plans', 'admin.lesson-plans.dashboard' => '/admin/lesson-plans',
            'admin.lesson-plan-approvals.pending' => '/admin/lesson-plans/approvals/pending',
            'admin.finance_officers.index' => '/admin/finance-officers', 'admin.fees.reports' => '/admin/fees/reports',
            'admin.fee-structures.index' => '/admin/fee-structures',
            'admin.transport.index' => '/admin/transport', 'admin.hostel.index' => '/admin/hostel',
            'finance.dashboard' => '/finance/dashboard', 'payments.index' => '/finance/payments', 'finance.payments.index' => '/finance/payments',
            'schedules.index' => '/schedules', 'admin.exams.types.index' => '/admin/exams/types',
            'library.index' => '/library', 'library.books.index' => '/library/books',
            'admin.settings.signature' => '/admin/settings/signature', 'admin.e-signatures.signatures' => '/admin/e-signatures/signatures',
            'admin.inventory.index' => '/admin/inventory', 'admin.inventory.dashboard' => '/admin/inventory/dashboard',
            'admin.visitor-management.index' => '/admin/visitor-management', 'admin.visitor-management.visitors.index' => '/admin/visitor-management/visitors',
            'admin.health-safety.index' => '/admin/health-safety', 'admin.health-safety.dashboard' => '/admin/health-safety/dashboard',
        ];
        return $fallbackRoutes[$name] ?? $fallback;
    }
}
@endphp

@if($hasRoleBased)
<nav class="flex-1 px-6 py-6 space-y-3">
    <ul class="space-y-2">
        @role('super_admin')
            @include('sidebar.super_admin')
        @endrole

        @role('admin')
            @include('sidebar.admin')
        @endrole

        @role('registrar')
            @include('sidebar.registrar')
        @endrole

        @role('parent')
            @include('sidebar.parent')
        @endrole

        @role('librarian')
            @include('sidebar.librarian')
        @endrole

        @role('conductor_driver')
            @include('sidebar.driver')
        @endrole

        @role('teacher')
            @include('sidebar.teacher')
        @endrole
    </ul>
</nav>
@else
    @include('layouts.navigation')
@endif


