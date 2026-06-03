@php $addons = $currentSchoolAddons ?? []; $has = function($key) use ($addons) { return empty($addons) || in_array($key, $addons); }; @endphp
{{-- Dashboard always --}}
<li><a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : '' }}">Dashboard</a></li>
@if($has('students'))
<li><a href="{{ safeRoute('admin.students.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.students.*') ? 'bg-blue-50 text-blue-700' : '' }}">Students</a></li>
<li><a href="{{ safeRoute('admin.students.create') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.students.create') ? 'bg-blue-50 text-blue-700' : '' }}">Add Student</a></li>
@endif
@if($has('teachers'))
<li><a href="{{ safeRoute('admin.teachers.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.teachers.*') ? 'bg-blue-50 text-blue-700' : '' }}">Teachers</a></li>
<li><a href="{{ safeRoute('admin.teachers.create') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.teachers.create') ? 'bg-blue-50 text-blue-700' : '' }}">Add Teacher</a></li>
@endif
@if($has('classes'))
<li><a href="{{ safeRoute('admin.classes.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.classes.*') ? 'bg-blue-50 text-blue-700' : '' }}">Classes</a></li>
<li><a href="{{ safeRoute('admin.classes.create') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.classes.create') ? 'bg-blue-50 text-blue-700' : '' }}">Add Class</a></li>
@endif
@if($has('subjects'))
<li><a href="{{ safeRoute('admin.subjects.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.subjects.*') ? 'bg-blue-50 text-blue-700' : '' }}">Subjects</a></li>
@endif
@if($has('grades'))
<li><a href="{{ safeRoute('admin.grades.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.grades.*') ? 'bg-blue-50 text-blue-700' : '' }}">Grade Approvals</a></li>
<li><a href="{{ safeRoute('admin.students.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.students.grades') || request()->routeIs('admin.students.show') ? 'bg-blue-50 text-blue-700' : '' }}">Student Grade Sheets</a></li>
<li><a href="{{ safeRoute('admin.transcripts.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.transcripts.*') ? 'bg-blue-50 text-blue-700' : '' }}">Transcripts</a></li>
@endif
@if($has('staff'))
<li><a href="{{ safeRoute('admin.staff.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.staff.index') ? 'bg-blue-50 text-blue-700' : '' }}">Staff</a></li>
<li><a href="{{ safeRoute('admin.staff.performance') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.staff.performance') ? 'bg-blue-50 text-blue-700' : '' }}">Staff Performance</a></li>
<li><a href="{{ safeRoute('admin.staff.schedules') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.staff.schedules') ? 'bg-blue-50 text-blue-700' : '' }}">Staff Schedules</a></li>
<li><a href="{{ safeRoute('admin.staff.payroll') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.staff.payroll') ? 'bg-blue-50 text-blue-700' : '' }}">Payroll</a></li>
@endif
@if($has('attendance'))
<li><a href="{{ safeRoute('admin.attendance.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.attendance.*') ? 'bg-blue-50 text-blue-700' : '' }}">Attendance</a></li>
<li><a href="{{ safeRoute('admin.attendance.students') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.attendance.students') ? 'bg-blue-50 text-blue-700' : '' }}">Student Attendance</a></li>
<li><a href="{{ safeRoute('admin.attendance.teachers') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.attendance.teachers') ? 'bg-blue-50 text-blue-700' : '' }}">Teacher Attendance</a></li>
@endif
@if($has('lesson_plans'))
<li><a href="{{ safeRoute('admin.lesson-plans.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.lesson-plans.*') ? 'bg-blue-50 text-blue-700' : '' }}">Lesson Plans</a></li>
<li><a href="{{ safeRoute('admin.lesson-plan-approvals.pending') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.lesson-plan-approvals.*') ? 'bg-blue-50 text-blue-700' : '' }}">Lesson Plan Approvals</a></li>
@endif
@if($has('users'))
<li><a href="{{ safeRoute('admin.users.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.users.index') ? 'bg-blue-50 text-blue-700' : '' }}">Users</a></li>
<li><a href="{{ safeRoute('admin.users.create') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.users.create') ? 'bg-blue-50 text-blue-700' : '' }}">Add User</a></li>
@endif
@if($has('notifications'))
<li><a href="{{ safeRoute('admin.notifications.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.notifications.index') ? 'bg-blue-50 text-blue-700' : '' }}">Notifications</a></li>
<li><a href="{{ safeRoute('admin.notifications.create') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.notifications.create') ? 'bg-blue-50 text-blue-700' : '' }}">Send Notification</a></li>
<li><a href="{{ safeRoute('admin.notifications.templates') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.notifications.templates') ? 'bg-blue-50 text-blue-700' : '' }}">Notification Templates</a></li>
@endif
@if($has('reports'))
<li><a href="{{ safeRoute('admin.reports.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.reports.*') ? 'bg-blue-50 text-blue-700' : '' }}">Reports</a></li>
<li><a href="{{ safeRoute('admin.reports.academic') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.reports.academic') ? 'bg-blue-50 text-blue-700' : '' }}">Academic Reports</a></li>
<li><a href="{{ safeRoute('admin.reports.financial') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.reports.financial') ? 'bg-blue-50 text-blue-700' : '' }}">Financial Reports</a></li>
@endif
@if($has('finance'))
<li><a href="{{ safeRoute('admin.fee-structures.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.fee-structures.*') ? 'bg-blue-50 text-blue-700' : '' }}">Fee Structures</a></li>
<li><a href="{{ safeRoute('admin.finance_officers.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.finance_officers.*') ? 'bg-blue-50 text-blue-700' : '' }}">Finance Officers</a></li>
<li><a href="{{ safeRoute('admin.fees.reports') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.fees.reports') ? 'bg-blue-50 text-blue-700' : '' }}">Fee Reports</a></li>
<li><a href="{{ safeRoute('finance.dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('finance.*') ? 'bg-blue-50 text-blue-700' : '' }}">Finance Dashboard</a></li>
<li><a href="{{ safeRoute('finance.payments.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('finance.payments.*') ? 'bg-blue-50 text-blue-700' : '' }}">Payments</a></li>
@endif
@if($has('transport'))
<li><a href="{{ safeRoute('admin.transport.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.transport.*') ? 'bg-blue-50 text-blue-700' : '' }}">Transport</a></li>
@endif
@if($has('hostel'))
<li><a href="{{ safeRoute('admin.hostel.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.hostel.*') ? 'bg-blue-50 text-blue-700' : '' }}">Hostel</a></li>
@endif
@if($has('schedules'))
<li><a href="{{ safeRoute('schedules.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('schedules.*') ? 'bg-blue-50 text-blue-700' : '' }}">Schedules</a></li>
@endif
@if($has('exams'))
<li><a href="{{ safeRoute('admin.exams.types.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.exams.types.*') ? 'bg-blue-50 text-blue-700' : '' }}">Exam Types</a></li>
@endif
@if($has('library'))
<li><a href="{{ safeRoute('library.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('library.*') ? 'bg-blue-50 text-blue-700' : '' }}">Library</a></li>
<li><a href="{{ safeRoute('library.books.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('library.books.*') ? 'bg-blue-50 text-blue-700' : '' }}">Library Books</a></li>
@endif
@if($has('signatures'))
<li><a href="{{ safeRoute('admin.settings.signature') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.settings.signature*') ? 'bg-blue-50 text-blue-700' : '' }}">Signatures</a></li>
<li><a href="{{ safeRoute('admin.e-signatures.signatures') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.e-signatures.*') ? 'bg-blue-50 text-blue-700' : '' }}">E-Signatures</a></li>
@endif
@if($has('inventory'))
<li><a href="{{ safeRoute('admin.inventory.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.inventory.*') ? 'bg-blue-50 text-blue-700' : '' }}">Inventory</a></li>
<li><a href="{{ safeRoute('admin.inventory.dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.inventory.dashboard') ? 'bg-blue-50 text-blue-700' : '' }}">Inventory Dashboard</a></li>
@endif
@if($has('visitation'))
<li><a href="{{ safeRoute('admin.visitor-management.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.visitor-management.*') ? 'bg-blue-50 text-blue-700' : '' }}">Visitation</a></li>
<li><a href="{{ safeRoute('admin.visitor-management.visitors.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50">Visitor Logs</a></li>
@endif
@if($has('checkin'))
<li><a href="{{ safeRoute('admin.attendance.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.attendance.*') ? 'bg-blue-50 text-blue-700' : '' }}">Check-in / Attendance</a></li>
@endif
@if($has('health_safety'))
<li><a href="{{ safeRoute('admin.health-safety.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.health-safety.*') ? 'bg-blue-50 text-blue-700' : '' }}">Health & Safety</a></li>
@endif
