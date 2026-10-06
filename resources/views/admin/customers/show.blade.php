@extends('layouts.admin')

@section('title', 'Customer CRM: ' . $customer->name)
@section('page-title', 'Customer Profile: ' . $customer->name)

@section('content')
<div class="space-y-6">

    <!-- Customer Overview Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.customers.index') }}" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center font-black text-xl">
                {{ substr($customer->name, 0, 1) }}
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $customer->name }}</h2>
                <div class="text-xs text-slate-500 font-mono">{{ $customer->phone }} | {{ $customer->email ?? 'No email' }}</div>
                <div class="text-xs text-slate-400 mt-0.5">{{ $customer->address }}, {{ $customer->city }}</div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="text-right">
                <span class="text-xs text-slate-400">Total Lifetime Spend:</span>
                <div class="text-2xl font-black text-emerald-500 code-font">৳{{ number_format($customer->total_spent, 2) }}</div>
            </div>
            <div class="text-right">
                <span class="text-xs text-slate-400">Loyalty Points:</span>
                <div class="text-xl font-bold text-amber-500 code-font">★ {{ $customer->loyalty_points }}</div>
            </div>
            <button type="button" onclick="openEditModal(@js($customer))" title="Edit Customer" class="px-3.5 py-2 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-600 dark:bg-sky-950/40 dark:hover:bg-sky-950/80 dark:text-sky-400 font-bold text-xs transition flex items-center gap-1.5">
                <i data-lucide="pencil" class="w-4 h-4"></i>
                <span>Edit</span>
            </button>
            <form method="POST" action="{{ route('admin.customers.destroy', $customer->id) }}" onsubmit="return confirm('Are you sure you want to delete customer {{ addslashes($customer->name) }}? All order history will be safely preserved.');">
                @csrf
                @method('DELETE')
                <button type="submit" title="Delete Customer" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/40 dark:hover:bg-rose-950/80 dark:text-rose-400 font-bold text-xs transition flex items-center gap-1.5">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    <span>Delete</span>
                </button>
            </form>
        </div>
    </div>

    <!-- 3 Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <span class="text-xs text-slate-400">Delivery Success Rate</span>
            <div class="text-xl font-black {{ $customer->delivery_success_rate < 50 ? 'text-rose-500' : 'text-emerald-500' }} code-font mt-1">
                {{ $customer->delivery_success_rate }}%
            </div>
        </div>

        <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <span class="text-xs text-slate-400">Total Orders Placed</span>
            <div class="text-xl font-black text-slate-900 dark:text-white code-font mt-1">
                {{ $customer->total_orders_count }} Orders
            </div>
        </div>

        <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <span class="text-xs text-slate-400">Fraud Flag Status</span>
            <div class="text-xl font-black {{ $customer->is_flagged_fraud ? 'text-rose-500' : 'text-emerald-500' }} mt-1">
                {{ $customer->is_flagged_fraud ? 'Flagged Buyer' : 'Clean / Safe Buyer' }}
            </div>
        </div>
    </div>

    @if($customer->notes || $customer->fraud_reason)
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @if($customer->fraud_reason)
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50">
            <span class="text-xs font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider">Fraud / Risk Reason</span>
            <p class="text-xs text-rose-700 dark:text-rose-300 mt-1">{{ $customer->fraud_reason }}</p>
        </div>
        @endif
        @if($customer->notes)
        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Admin Notes</span>
            <p class="text-xs text-slate-700 dark:text-slate-300 mt-1">{{ $customer->notes }}</p>
        </div>
        @endif
    </div>
    @endif

    <!-- Orders History Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 font-bold text-sm text-slate-900 dark:text-white">
            Customer Orders History
        </div>
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-bold uppercase">
                <tr>
                    <th class="p-3">Order No</th>
                    <th class="p-3">Date</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Grand Total</th>
                    <th class="p-3">Payment</th>
                    <th class="p-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($customer->orders as $order)
                <tr>
                    <td class="p-3 font-mono font-bold text-slate-900 dark:text-white">{{ $order->order_no }}</td>
                    <td class="p-3 text-slate-400">{{ $order->created_at->format('d M Y') }}</td>
                    <td class="p-3">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 dark:bg-slate-800">
                            {{ str_replace('_', ' ', $order->order_status) }}
                        </span>
                    </td>
                    <td class="p-3 font-bold code-font text-slate-900 dark:text-white">৳{{ number_format($order->grand_total, 2) }}</td>
                    <td class="p-3 text-slate-500">{{ strtoupper($order->payment_method) }}</td>
                    <td class="p-3 text-right">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-emerald-500 hover:underline">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-6 text-center text-slate-400">No orders placed yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- EDIT CUSTOMER MODAL -->
    <div id="editCustomerModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm" style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-xl w-full p-6 border border-slate-200 dark:border-slate-800 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="pencil" class="w-4 h-4 text-sky-500"></i>
                    <span>Edit Customer: <span id="editModalCustomerName" class="text-emerald-500">{{ $customer->name }}</span></span>
                </h3>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-white text-lg font-bold">&times;</button>
            </div>
            
            <form id="editCustomerForm" method="POST" action="{{ route('admin.customers.update', $customer->id) }}" class="space-y-3 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Full Name *</label>
                        <input type="text" name="name" id="editName" value="{{ old('name', $customer->name) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Phone Number *</label>
                        <input type="text" name="phone" id="editPhone" value="{{ old('phone', $customer->phone) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-mono outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Email Address</label>
                        <input type="email" name="email" id="editEmail" value="{{ old('email', $customer->email) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Update Password (Leave blank to keep)</label>
                        <input type="password" name="password" placeholder="New password" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-500 mb-1">Address</label>
                        <input type="text" name="address" id="editAddress" value="{{ old('address', $customer->address) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">City / District</label>
                        <input type="text" name="city" id="editCity" value="{{ old('city', $customer->city) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Postal Code</label>
                        <input type="text" name="postal_code" id="editPostalCode" value="{{ old('postal_code', $customer->postal_code) }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-500 mb-1">Loyalty Points</label>
                        <input type="number" name="loyalty_points" id="editLoyaltyPoints" value="{{ old('loyalty_points', $customer->loyalty_points) }}" min="0" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs code-font outline-none">
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/50 space-y-2">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="editFraudCheck" name="is_flagged_fraud" value="1" {{ $customer->is_flagged_fraud ? 'checked' : '' }} onchange="document.getElementById('editFraudReasonBox').style.display = this.checked ? 'block' : 'none'" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                        <label for="editFraudCheck" class="text-xs font-semibold text-slate-700 dark:text-slate-300 cursor-pointer">
                            Flag this customer for High Fraud Risk
                        </label>
                    </div>
                    <div id="editFraudReasonBox" style="{{ $customer->is_flagged_fraud ? '' : 'display: none;' }}">
                        <input type="text" name="fraud_reason" id="editFraudReason" value="{{ old('fraud_reason', $customer->fraud_reason) }}" placeholder="Reason for fraud flag..." class="w-full px-3 py-2 rounded-xl border border-rose-200 dark:border-rose-900/60 bg-white dark:bg-slate-800 text-xs text-rose-600 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-500 mb-1">Admin Notes</label>
                    <textarea name="notes" id="editNotes" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs outline-none">{{ old('notes', $customer->notes) }}</textarea>
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
    function openEditModal(cust) {
        if (cust) {
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
        }
        document.getElementById('editCustomerModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editCustomerModal').style.display = 'none';
    }
</script>
@endpush
@endsection
