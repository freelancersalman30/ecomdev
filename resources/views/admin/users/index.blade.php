@extends('layouts.admin')

@section('title', 'Admin Users & RBAC')
@section('page-title', 'Admin Staff & User Management')

@section('content')
<div class="space-y-6">

    {{-- Top Action Header --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="shield-check" class="w-5 h-5 text-emerald-500"></i>
                <span>Admin Staff Directory & RBAC</span>
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Manage administrators, branch managers, and POS operators with Spatie role-based access control.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.roles.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-xs transition flex items-center gap-1.5">
                <i data-lucide="key-round" class="w-3.5 h-3.5"></i>
                <span>Role Matrix</span>
            </a>
            <button onclick="openCreateModal()" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>+ Create Staff User</span>
            </button>
        </div>
    </div>

    {{-- Quick Overview Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $stats['total'] }}</div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Staff</div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold">
                <i data-lucide="shield" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $stats['admins'] }}</div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Super Admins</div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold">
                <i data-lucide="briefcase" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $stats['managers'] }}</div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Managers</div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                <i data-lucide="user-check" class="w-5 h-5"></i>
            </div>
            <div>
                <div class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $stats['others'] }}</div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Operators / Staff</div>
            </div>
        </div>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search staff by name or email..." class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white outline-none focus:border-emerald-500">
            </div>
            <div class="sm:w-48">
                <select name="role" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 outline-none focus:border-emerald-500">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'role']))
                <a href="{{ route('admin.users.index') }}" class="px-3.5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-600 dark:text-slate-300 font-semibold text-xs transition">
                    Clear
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Users Data Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4">Staff User</th>
                        <th class="py-3.5 px-4">Email Address</th>
                        <th class="py-3.5 px-4">Assigned Role</th>
                        <th class="py-3.5 px-4">Created Date</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                        {{-- Name with Avatar --}}
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-emerald-500 to-teal-400 text-slate-950 font-bold text-xs flex items-center justify-center shadow-sm">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white text-xs flex items-center gap-1.5">
                                        <span>{{ $user->name }}</span>
                                        @if($user->id === auth()->id())
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-500/20">YOU</span>
                                        @endif
                                    </div>
                                    <span class="text-[10px] text-slate-400">ID #{{ $user->id }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- Email --}}
                        <td class="py-3.5 px-4 font-mono text-slate-600 dark:text-slate-300">
                            {{ $user->email }}
                        </td>

                        {{-- Roles --}}
                        <td class="py-3.5 px-4">
                            @forelse($user->roles as $role)
                                @php
                                    $roleColor = match(strtolower($role->name)) {
                                        'admin', 'super-admin' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border-purple-200 dark:border-purple-800',
                                        'manager' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300 border-sky-200 dark:border-sky-800',
                                        default => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $roleColor }}">
                                    {{ $role->name }}
                                </span>
                            @empty
                                <span class="text-slate-400 italic text-[11px]">No Role Assigned</span>
                            @endforelse
                        </td>

                        {{-- Created At --}}
                        <td class="py-3.5 px-4 text-slate-400">
                            <div>{{ $user->created_at->format('d M Y') }}</div>
                            <div class="text-[10px] text-slate-400">{{ $user->created_at->diffForHumans() }}</div>
                        </td>

                        {{-- Actions --}}
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                {{-- Edit Button --}}
                                <button type="button" 
                                    onclick="openEditModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}', '{{ optional($user->roles->first())->name ?? '' }}')"
                                    class="p-1.5 rounded-lg bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-600 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 transition"
                                    title="Edit Staff User">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </button>

                                {{-- Delete Button --}}
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete staff account \'{{ addslashes($user->name) }}\'? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 transition" title="Delete Staff Account">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                @else
                                <span class="p-1.5 text-slate-300 dark:text-slate-700 cursor-not-allowed" title="Cannot delete active session">
                                    <i data-lucide="lock" class="w-4 h-4"></i>
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-400">
                            <i data-lucide="user-x" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                            <p class="text-xs">No admin staff accounts found matching your criteria.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    {{-- CREATE USER MODAL --}}
    <div id="addUserModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm" style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="user-plus" class="w-4 h-4 text-emerald-500"></i>
                    <span>Create New Staff User</span>
                </h3>
                <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Full Name *</label>
                    <input type="text" name="name" required placeholder="Staff name (e.g. John Doe)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Email Address *</label>
                    <input type="email" name="email" required placeholder="staff@dreamerspcb.com" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Initial Password *</label>
                    <input type="password" name="password" required placeholder="Minimum 6 characters" minlength="6" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Assign Spatie RBAC Role *</label>
                    <select name="role" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 outline-none focus:border-emerald-500">
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeCreateModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-600 dark:text-slate-300 font-semibold text-xs transition">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition">Create Staff Account</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT USER MODAL --}}
    <div id="editUserModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm" style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="user-cog" class="w-4 h-4 text-emerald-500"></i>
                    <span>Edit Staff Profile & Role</span>
                </h3>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <form id="editUserForm" method="POST" action="" class="space-y-3.5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Full Name *</label>
                    <input type="text" name="name" id="edit_name" required placeholder="Staff name" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Email Address *</label>
                    <input type="email" name="email" id="edit_email" required placeholder="staff@dreamerspcb.com" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">
                        <span>Reset Password</span>
                        <span class="text-[10px] text-slate-400 font-normal">(Leave blank to keep existing password)</span>
                    </label>
                    <input type="password" name="password" id="edit_password" placeholder="Enter new password (optional)" minlength="6" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Assigned Role *</label>
                    <select name="role" id="edit_role" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 outline-none focus:border-emerald-500">
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-600 dark:text-slate-300 font-semibold text-xs transition">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function openCreateModal() {
        const modal = document.getElementById('addUserModal');
        if (modal) modal.style.display = 'flex';
    }

    function closeCreateModal() {
        const modal = document.getElementById('addUserModal');
        if (modal) modal.style.display = 'none';
    }

    function openEditModal(id, name, email, role) {
        const modal = document.getElementById('editUserModal');
        const form = document.getElementById('editUserForm');
        const nameInput = document.getElementById('edit_name');
        const emailInput = document.getElementById('edit_email');
        const roleSelect = document.getElementById('edit_role');
        const passwordInput = document.getElementById('edit_password');

        if (form) {
            form.action = `/admin/users/${id}`;
        }
        if (nameInput) nameInput.value = name;
        if (emailInput) emailInput.value = email;
        if (roleSelect && role) roleSelect.value = role;
        if (passwordInput) passwordInput.value = '';

        if (modal) modal.style.display = 'flex';
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function closeEditModal() {
        const modal = document.getElementById('editUserModal');
        if (modal) modal.style.display = 'none';
    }

    // Close modals on clicking backdrop
    window.addEventListener('click', function(e) {
        const addModal = document.getElementById('addUserModal');
        const editModal = document.getElementById('editUserModal');
        if (e.target === addModal) closeCreateModal();
        if (e.target === editModal) closeEditModal();
    });
</script>
@endsection
