@extends('layouts.admin')

@section('title', 'Spatie RBAC Roles & Permissions')
@section('page-title', 'Roles & Permissions Matrix')

@section('content')
<div class="space-y-6">

    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Role-Based Access Control (RBAC)</h2>
            <p class="text-xs text-slate-500">Configure permission sets for Super Admin, Store Managers, POS Cashiers, and Inventory Clerks</p>
        </div>

        <button onclick="document.getElementById('addRoleModal').style.display = 'flex'" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
            <i data-lucide="shield-plus" class="w-4 h-4"></i>
            <span>+ Create Custom Role</span>
        </button>
    </div>

    <!-- Roles Matrix -->
    <div class="space-y-6">
        @foreach($roles as $role)
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center font-bold">
                        <i data-lucide="shield" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-900 dark:text-white">{{ $role->name }}</h3>
                        <p class="text-[11px] text-slate-400 font-mono">{{ $role->permissions->count() }} active permissions</p>
                    </div>
                </div>
            </div>

            <!-- Permissions Checkbox Form -->
            <form method="POST" action="{{ route('admin.roles.permissions.update', $role->id) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 py-2">
                    @foreach($permissions as $group => $perms)
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/40 space-y-2">
                        <div class="font-bold text-slate-700 dark:text-slate-300 text-[11px] uppercase tracking-wider">{{ $group }}</div>
                        <div class="space-y-1.5">
                            @foreach($perms as $perm)
                            <label class="flex items-center gap-2 cursor-pointer text-xs">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" {{ $role->hasPermissionTo($perm->name) ? 'checked' : '' }} class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="text-slate-600 dark:text-slate-400 font-mono text-[11px]">{{ explode('.', $perm->name)[1] ?? $perm->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="flex justify-end pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition">
                        Save Permissions for {{ $role->name }}
                    </button>
                </div>
            </form>
        </div>
        @endforeach
    </div>

    <!-- CREATE ROLE MODAL -->
    <div id="addRoleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm" style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Create New Role</h3>
                <button onclick="document.getElementById('addRoleModal').style.display = 'none'" class="text-slate-400">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Role Title *</label>
                    <input type="text" name="name" required placeholder="e.g. Sales Associate" class="w-full px-3 py-2 rounded-xl border text-xs bg-white dark:bg-slate-800 outline-none">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('addRoleModal').style.display = 'none'" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-xs">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 text-white font-bold text-xs">Create Role</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
