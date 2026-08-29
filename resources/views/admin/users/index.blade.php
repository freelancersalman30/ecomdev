@extends('layouts.admin')

@section('title', 'Admin Users & RBAC')
@section('page-title', 'User Accounts & Roles')

@section('content')
<div class="space-y-6">

    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Admin Staff Directory</h2>
            <p class="text-xs text-slate-500">Manage administrator accounts, managers, and POS operators with Spatie RBAC</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.roles.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs transition">
                Role & Permission Matrix
            </a>
            <button onclick="document.getElementById('addUserModal').style.display = 'flex'" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>+ Create Admin User</span>
            </button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase">
                <tr>
                    <th class="p-3.5">Name</th>
                    <th class="p-3.5">Email Address</th>
                    <th class="p-3.5">Assigned Role</th>
                    <th class="p-3.5">Created At</th>
                    <th class="p-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($users as $user)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                    <td class="p-3.5">
                        <div class="font-bold text-slate-900 dark:text-white text-xs">{{ $user->name }}</div>
                    </td>
                    <td class="p-3.5 font-mono text-slate-600 dark:text-slate-400">{{ $user->email }}</td>
                    <td class="p-3.5">
                        @foreach($user->roles as $role)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                            {{ $role->name }}
                        </span>
                        @endforeach
                    </td>
                    <td class="p-3.5 text-slate-400">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="p-3.5 text-right">
                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this staff account?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-500 hover:underline">Delete</button>
                        </form>
                        @else
                        <span class="text-slate-400 italic">Current Session</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- CREATE USER MODAL -->
    <div id="addUserModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm" style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Create New Staff User</h3>
                <button onclick="document.getElementById('addUserModal').style.display = 'none'" class="text-slate-400">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Full Name *</label>
                    <input type="text" name="name" required placeholder="Staff name" class="w-full px-3 py-2 rounded-xl border text-xs bg-white dark:bg-slate-800 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Email Address *</label>
                    <input type="email" name="email" required placeholder="staff@dreamerspcb.com" class="w-full px-3 py-2 rounded-xl border text-xs bg-white dark:bg-slate-800 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Password *</label>
                    <input type="password" name="password" required placeholder="******" class="w-full px-3 py-2 rounded-xl border text-xs bg-white dark:bg-slate-800 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Assign Role *</label>
                    <select name="role" required class="w-full px-3 py-2 rounded-xl border text-xs bg-white dark:bg-slate-800 outline-none">
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('addUserModal').style.display = 'none'" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-xs">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 text-white font-bold text-xs">Create Staff User</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
