<form wire:submit.prevent="createUser" class="space-y-4">
    <input type="text" wire:model="name" placeholder="Full Name" required class="w-full border rounded p-2">
    <input type="email" wire:model="email" placeholder="Email" required class="w-full border rounded p-2">
    <input type="text" wire:model="username" placeholder="Username (auto)" readonly class="w-full border rounded p-2">

    <h4 class="font-semibold">Select Roles</h4>
    <div class="grid grid-cols-2 gap-2">
        @foreach(\Spatie\Permission\Models\Role::all() as $role)
            <label class="flex items-center space-x-2"><input type="checkbox" wire:model="roles" value="{{ $role->name }}"> <span>{{ $role->name }}</span></label>
        @endforeach
    </div>

    <h4 class="font-semibold">Permissions (Optional Override)</h4>
    <div class="grid grid-cols-3 gap-2 max-h-48 overflow-auto border p-2 rounded">
        @foreach(\Spatie\Permission\Models\Permission::all() as $perm)
            <label class="flex items-center space-x-2"><input type="checkbox" wire:model="permissions" value="{{ $perm->name }}"> <span>{{ $perm->name }}</span></label>
        @endforeach
    </div>

    <button type="submit" class="btn btn-primary bg-blue-600 text-white px-4 py-2 rounded">Create User</button>
</form>

