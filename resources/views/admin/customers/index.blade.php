@extends('layouts.admin')

@section('title', 'Customer CRM Directory')
@section('page-title', 'Customer CRM & Loyalty Records')

@section('content')
<div class="space-y-6">

    <!-- Header & Search Toolbar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Customer Directory</h2>
            <p class="text-xs text-slate-500">Track customer spending, delivery success rate scoring, and loyalty rewards</p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="flex items-center gap-2 w-full sm:w-80">
                <div class="relative flex-1">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search }}" 
                        placeholder="Search name, phone, email, city..." 
                        class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-800 text-white text-xs font-semibold hover:bg-slate-700 transition">
                    Search
                </button>
            </form>

            <button type="button" onclick="openCreateModal()" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-1.5 whitespace-nowrap">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>+ Add Customer</span>
            </button>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 text-xs uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3.5">Customer & Phone</th>
                        <th class="px-4 py-3.5">City & Address</th>
                        <th class="px-4 py-3.5">Orders Placed</th>
                        <th class="px-4 py-3.5">Delivery Rate</th>
                        <th class="px-4 py-3.5">Total Spent</th>
                        <th class="px-4 py-3.5">Loyalty Points</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($customers as $cust)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                        <td class="px-4 py-3.5">
                            <div class="font-bold text-xs text-slate-900 dark:text-white">
                                <a href="{{ route('admin.customers.show', $cust->id) }}" class="hover:text-emerald-500">
                                    {{ $cust->name }}
                                </a>
                            </div>
                            <div class="text-[11px] text-slate-500 font-mono">{{ $cust->phone }}</div>
                            @if($cust->email)
                            <div class="text-[10px] text-slate-400">{{ $cust->email }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-xs">
                            <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $cust->city ?? 'Dhaka' }}</div>
                            <div class="text-[10px] text-slate-400 truncate max-w-[160px]">{{ $cust->address ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3.5 font-bold code-font text-xs text-slate-900 dark:text-white">
                            {{ $cust->total_orders_count }}
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold code-font {{ $cust->delivery_success_rate < 50 ? 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' }}">
                                {{ $cust->delivery_success_rate }}%
                            </span>
                        </td>
                        <td class="px-4 py-3.5 font-bold code-font text-xs text-slate-900 dark:text-white">
                            ৳{{ number_format($cust->total_spent, 2) }}
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="px-2 py-0.5 rounded-md text-xs font-bold bg-amber-500/10 text-amber-500 code-font">
                                ★ {{ $cust->loyalty_points }} pts
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            @if($cust->is_flagged_fraud)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-rose-500/10 text-rose-500">
                                Fraud Alert
                            </span>
                            @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-500">
                                Verified
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.customers.show', $cust->id) }}" title="View Customer Profile" class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-emerald-500 hover:bg-slate-100 dark:hover:bg-slate-800 inline-flex transition">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <button type="button" 
                                        onclick="openEditModal(@js($cust))" 
                                        title="Edit Customer" 
                                        class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-sky-500 hover:bg-slate-100 dark:hover:bg-slate-800 inline-flex transition">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.customers.destroy', $cust->id) }}" onsubmit="return confirm('Are you sure you want to delete customer {{ addslashes($cust->name) }}? All order history will be safely preserved.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete Customer" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 inline-flex transition">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-400 text-xs">
                            No customers found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $customers->links() }}
        </div>
    </div>

    <!-- CREATE CUSTOMER MODAL -->
    <div id="createCustomerModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm" style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-xl w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="user-plus" class="w-4 h-4 text-emerald-500"></i>
                    <span>Create New Customer</span>
                </h3>
                <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-white text-lg font-bold">&times;</button>
            </div>
            
            <form method="POST" action="{{ route('admin.customers.store') }}" class="space-y-3 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Full Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Tanvir Ahmed" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Phone Number *</label>
                        <input type="text" name="phone" required placeholder="017XXXXXXXX" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Email Address</label>
                        <input type="email" name="email" placeholder="customer@example.com" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Login Password (Optional)</label>
                        <input type="password" name="password" placeholder="Min 6 characters" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-500 mb-1">Address</label>
                        <input type="text" name="address" placeholder="House #, Road #, Area..." class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">City / District</label>
                        <input type="text" name="city" value="Dhaka" placeholder="Dhaka" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Postal Code</label>
                        <input type="text" name="postal_code" placeholder="1205" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Initial Loyalty Points</label>
                        <input type="number" name="loyalty_points" value="0" min="0" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs code-font outline-none">
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/50 space-y-2">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="createFraudCheck" name="is_flagged_fraud" value="1" onchange="document.getElementById('createFraudReasonBox').style.display = this.checked ? 'block' : 'none'" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                        <label for="createFraudCheck" class="text-xs font-semibold text-slate-700 dark:text-slate-300 cursor-pointer">
                            Flag this customer for High Fraud Risk
                        </label>
                    </div>
                    <div id="createFraudReasonBox" style="display: none;">
                        <input type="text" name="fraud_reason" placeholder="Reason for fraud flag..." class="w-full px-3 py-2 rounded-xl border border-rose-200 dark:border-rose-900/60 bg-white dark:bg-slate-800 text-xs text-rose-600 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-500 mb-1">Admin Notes</label>
                    <textarea name="notes" rows="2" placeholder="Special customer remarks or VIP status..." class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="closeCreateModal()" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md">
                        Save Customer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT CUSTOMER MODAL -->
    <div id="editCustomerModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm" style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-xl w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="pencil" class="w-4 h-4 text-sky-500"></i>
                    <span>Edit Customer: <span id="editModalCustomerName" class="text-emerald-500"></span></span>
                </h3>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-white text-lg font-bold">&times;</button>
            </div>
            
            <form id="editCustomerForm" method="POST" action="" class="space-y-3 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Full Name *</label>
                        <input type="text" name="name" id="editName" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Phone Number *</label>
                        <input type="text" name="phone" id="editPhone" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Email Address</label>
                        <input type="email" name="email" id="editEmail" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Update Password (Leave blank to keep)</label>
                        <input type="password" name="password" placeholder="New password" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-500 mb-1">Address</label>
                        <input type="text" name="address" id="editAddress" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">City / District</label>
                        <input type="text" name="city" id="editCity" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Postal Code</label>
                        <input type="text" name="postal_code" id="editPostalCode" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Loyalty Points</label>
                        <input type="number" name="loyalty_points" id="editLoyaltyPoints" min="0" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs code-font outline-none">
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/50 space-y-2">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="editFraudCheck" name="is_flagged_fraud" value="1" onchange="document.getElementById('editFraudReasonBox').style.display = this.checked ? 'block' : 'none'" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                        <label for="editFraudCheck" class="text-xs font-semibold text-slate-700 dark:text-slate-300 cursor-pointer">
                            Flag this customer for High Fraud Risk
                        </label>
                    </div>
                    <div id="editFraudReasonBox" style="display: none;">
                        <input type="text" name="fraud_reason" id="editFraudReason" placeholder="Reason for fraud flag..." class="w-full px-3 py-2 rounded-xl border border-rose-200 dark:border-rose-900/60 bg-white dark:bg-slate-800 text-xs text-rose-600 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-500 mb-1">Admin Notes</label>
                    <textarea name="notes" id="editNotes" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold shadow-md">
                        Update Customer
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('createCustomerModal').style.display = 'flex';
    }

    function closeCreateModal() {
        document.getElementById('createCustomerModal').style.display = 'none';
    }

    function openEditModal(cust) {
        const form = document.getElementById('editCustomerForm');
        form.action = "{{ url('admin/customers') }}/" + cust.id;
        document.getElementById('editModalCustomerName').innerText = cust.name;
        document.getElementById('editName').value = cust.name || '';
        document.getElementById('editPhone').value = cust.phone || '';
        document.getElementById('editEmail').value = cust.email || '';
        document.getElementById('editAddress').value = cust.address || '';
        document.getElementById('editCity').value = cust.city || 'Dhaka';
        document.getElementById('editPostalCode').value = cust.postal_code || '';
        document.getElementById('editLoyaltyPoints').value = cust.loyalty_points || 0;
        document.getElementById('editNotes').value = cust.notes || '';
        
        const isFraud = Boolean(cust.is_flagged_fraud);
        document.getElementById('editFraudCheck').checked = isFraud;
        document.getElementById('editFraudReason').value = cust.fraud_reason || '';
        document.getElementById('editFraudReasonBox').style.display = isFraud ? 'block' : 'none';

        document.getElementById('editCustomerModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editCustomerModal').style.display = 'none';
    }
</script>
@endpush
@endsection

