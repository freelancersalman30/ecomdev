import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/utils/currency_formatter.dart';
import '../../../data/models/order_model.dart';
import '../../../providers/order_provider.dart';
import '../../widgets/custom_button.dart';
import '../../widgets/custom_text_field.dart';

class OrderTrackingScreen extends ConsumerStatefulWidget {
  final String? orderNo;
  final String? phone;

  const OrderTrackingScreen({
    super.key,
    this.orderNo,
    this.phone,
  });

  @override
  ConsumerState<OrderTrackingScreen> createState() => _OrderTrackingScreenState();
}

class _OrderTrackingScreenState extends ConsumerState<OrderTrackingScreen> {
  final _orderNoController = TextEditingController();
  final _phoneController = TextEditingController();
  TrackOrderParams? _searchParams;

  @override
  void initState() {
    super.initState();
    if (widget.orderNo != null && widget.phone != null) {
      _orderNoController.text = widget.orderNo!;
      _phoneController.text = widget.phone!;
      _searchParams = TrackOrderParams(orderNo: widget.orderNo!, phone: widget.phone!);
    }
  }

  @override
  void dispose() {
    _orderNoController.dispose();
    _phoneController.dispose();
    super.dispose();
  }

  void _searchOrder() {
    final orderNo = _orderNoController.text.trim();
    final phone = _phoneController.text.trim();

    if (orderNo.isEmpty || phone.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please enter both Order ID and Phone Number')),
      );
      return;
    }

    setState(() {
      _searchParams = TrackOrderParams(orderNo: orderNo, phone: phone);
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Track Order'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Search Card
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: AppColors.border),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Enter Order Details',
                    style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700),
                  ),
                  const SizedBox(height: 12),
                  CustomTextField(
                    controller: _orderNoController,
                    label: 'Order ID / No',
                    hintText: 'e.g. ORD-2026-0001',
                    prefixIcon: Icons.tag_rounded,
                  ),
                  const SizedBox(height: 12),
                  CustomTextField(
                    controller: _phoneController,
                    label: 'Contact Phone Number',
                    hintText: 'e.g. 01700000000',
                    prefixIcon: Icons.phone_outlined,
                    keyboardType: TextInputType.phone,
                  ),
                  const SizedBox(height: 16),
                  CustomButton(
                    text: 'Find Order Status',
                    icon: Icons.search_rounded,
                    onPressed: _searchOrder,
                  ),
                ],
              ),
            ),

            const SizedBox(height: 20),

            if (_searchParams != null) ...[
              ref.watch(trackOrderProvider(_searchParams!)).when(
                    data: (order) => _buildOrderTimeline(order),
                    loading: () => const Center(
                      child: Padding(
                        padding: EdgeInsets.all(32),
                        child: CircularProgressIndicator(color: AppColors.primary),
                      ),
                    ),
                    error: (err, _) => Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: AppColors.error.withOpacity(0.05),
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(color: AppColors.error.withOpacity(0.3)),
                      ),
                      child: Row(
                        children: [
                          const Icon(Icons.error_outline, color: AppColors.error),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Text(
                              'Could not find order: ${err.toString()}',
                              style: const TextStyle(color: AppColors.error, fontSize: 13),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
            ],
          ],
        ),
      ),
    );
  }

  Widget _buildOrderTimeline(OrderDetailModel order) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // Status Overview Header
        Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: AppColors.border),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    'Order #${order.orderNo}',
                    style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: AppColors.primaryDark),
                  ),
                  _buildStatusChip(order.status),
                ],
              ),
              const SizedBox(height: 8),
              Text(
                'Placed on: ${order.createdAt}',
                style: const TextStyle(fontSize: 12, color: AppColors.textSecondary),
              ),
              if (order.courierName != null) ...[
                const SizedBox(height: 4),
                Text(
                  'Courier: ${order.courierName} ${order.trackingCode != null ? "(${order.trackingCode})" : ""}',
                  style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: AppColors.textPrimary),
                ),
              ],
            ],
          ),
        ),

        const SizedBox(height: 16),

        // Timeline Step by Step
        Container(
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: AppColors.border),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'Delivery Progress',
                style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700),
              ),
              const SizedBox(height: 16),
              if (order.timeline.isEmpty)
                const Text('No status history available yet.', style: TextStyle(fontSize: 13, color: AppColors.textSecondary))
              else
                ...order.timeline.asMap().entries.map((entry) {
                  final index = entry.key;
                  final step = entry.value;
                  final isLast = index == order.timeline.length - 1;

                  return Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Column(
                        children: [
                          Container(
                            width: 22,
                            height: 22,
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              color: step.isCompleted ? AppColors.success : AppColors.border,
                            ),
                            child: Icon(
                              step.isCompleted ? Icons.check : Icons.circle,
                              size: 14,
                              color: Colors.white,
                            ),
                          ),
                          if (!isLast)
                            Container(
                              width: 2,
                              height: 36,
                              color: step.isCompleted ? AppColors.success : AppColors.border,
                            ),
                        ],
                      ),
                      const SizedBox(width: 14),
                      Expanded(
                        child: Padding(
                          padding: const EdgeInsets.only(bottom: 16),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                step.label,
                                style: TextStyle(
                                  fontSize: 14,
                                  fontWeight: step.isCompleted ? FontWeight.bold : FontWeight.w500,
                                  color: step.isCompleted ? AppColors.textPrimary : AppColors.textMuted,
                                ),
                              ),
                              if (step.timestamp != null) ...[
                                const SizedBox(height: 2),
                                Text(
                                  step.timestamp!,
                                  style: const TextStyle(fontSize: 11, color: AppColors.textSecondary),
                                ),
                              ],
                              if (step.note != null && step.note!.isNotEmpty) ...[
                                const SizedBox(height: 2),
                                Text(
                                  step.note!,
                                  style: const TextStyle(fontSize: 11, color: AppColors.textMuted),
                                ),
                              ],
                            ],
                          ),
                        ),
                      ),
                    ],
                  );
                }).toList(),
            ],
          ),
        ),

        const SizedBox(height: 16),

        // Items Summary Card
        Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: AppColors.border),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text('Items Ordered', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
              const SizedBox(height: 12),
              ...order.items.map((item) => Padding(
                    padding: const EdgeInsets.only(bottom: 8.0),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Expanded(
                          child: Text(
                            '${item.quantity}x ${item.title} ${item.variant != null ? "(${item.variant})" : ""}',
                            style: const TextStyle(fontSize: 13),
                          ),
                        ),
                        Text(
                          CurrencyFormatter.format(item.total),
                          style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13),
                        ),
                      ],
                    ),
                  )),
              const Divider(),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text('Total Amount Paid / Due', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                  Text(
                    CurrencyFormatter.format(order.totalAmount),
                    style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 16, color: AppColors.primaryDark),
                  ),
                ],
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildStatusChip(String status) {
    Color bg = AppColors.primaryLight;
    Color text = AppColors.primaryDark;

    switch (status.toLowerCase()) {
      case 'delivered':
        bg = AppColors.success.withOpacity(0.15);
        text = AppColors.success;
        break;
      case 'cancelled':
        bg = AppColors.error.withOpacity(0.15);
        text = AppColors.error;
        break;
      case 'processing':
      case 'in_transit':
        bg = AppColors.warning.withOpacity(0.15);
        text = AppColors.warning;
        break;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: BorderRadius.circular(8),
      ),
      child: Text(
        status.toUpperCase(),
        style: TextStyle(color: text, fontWeight: FontWeight.bold, fontSize: 11),
      ),
    );
  }
}
