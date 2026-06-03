{{-- Super Admin Sidebar: system-wide, multi-school --}}
<li><a href="{{ route('super_admin.dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-purple-50 {{ request()->routeIs('super_admin.dashboard') ? 'bg-purple-50 text-purple-700' : '' }}">Dashboard</a></li>
<li><a href="{{ route('super_admin.schools.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-purple-50 {{ request()->routeIs('super_admin.schools.*') ? 'bg-purple-50 text-purple-700' : '' }}">Schools</a></li>
<li><a href="{{ route('super_admin.schools.create') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-purple-50 {{ request()->routeIs('super_admin.schools.create') ? 'bg-purple-50 text-purple-700' : '' }}">Add School</a></li>
