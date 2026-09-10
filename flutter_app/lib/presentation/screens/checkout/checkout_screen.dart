import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/utils/currency_formatter.dart';
import '../../../data/models/shipping_method_model.dart';
import '../../../providers/auth_provider.dart';
import '../../../providers/cart_provider.dart';
import '../../widgets/custom_button.dart';
import '../../widgets/custom_text_field.dart';
import 'order_success_screen.dart';

class CheckoutScreen extends ConsumerStatefulWidget {
  const CheckoutScreen({super.key});

  @override
  ConsumerState<CheckoutScreen> createState() => _CheckoutScreenState();
}

class _CheckoutScreenState extends ConsumerState<CheckoutScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _phoneController = TextEditingController();
  final _addressController = TextEditingController();
  final _cityController = TextEditingController();
  final _notesController = TextEditingController();

  String _paymentMethod = 'cod'; // cod, online
  bool _isSubmitting = false;

  @override
  void initState() {
    super.initState();
    final user = ref.read(authProvider).user;
    if (user != null) {
      _nameController.text = user.name;
      _phoneController.text = user.phone;
      _addressController.text = user.address ?? '';
      _cityController.text = user.city ?? '';
    }
  }

  @override
  void dispose() {
    _nameController.dispose();
    _phoneController.dispose();
    _addressController.dispose();
    _cityController.dispose();
    _notesController.dispose();
    super.dispose();
  }

  Future<void> _handlePlaceOrder() async {
    if (!_formKey.currentState!.validate()) return;

    final cartState = ref.read(cartProvider);
    if (cartState.items.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Your cart is empty.')),
      );
      return;
    }

    if (cartState.selectedShipping == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please select a delivery method.')),
      );
      return;
    }

    setState(() => _isSubmitting = true);

    try {
      final repo = ref.read(cartRepositoryProvider);
      final response = await repo.placeOrder(
        name: _nameController.text.trim(),
        phone: _phoneController.text.trim(),
        address: _addressController.text.trim(),
        city: _cityController.text.trim().isNotEmpty ? _cityController.text.trim() : null,
        deliveryMethodId: cartState.selectedShipping!.id,
        paymentMethod: _paymentMethod,
        couponCode: cartState.coupon?.code,
        orderNotes: _notesController.text.trim().isNotEmpty ? _notesController.text.trim() : null,
        items: cartState.items,
      );

      final orderNo = response['data']?['order_no'] ?? response['order_no'] ?? 'N/A';
      final total = response['data']?['total_amount'] ?? cartState.grandTotal;

      await ref.read(cartProvider.notifier).clearCart();

      if (mounted) {
        setState(() => _isSubmitting = false);
        Navigator.of(context).pushReplacement(
          MaterialPageRoute(
            builder: (_) => OrderSuccessScreen(
              orderNo: orderNo.toString(),
              phone: _phoneController.text.trim(),
              totalAmount: total,
            ),
          ),
        );
      }
    } catch (e) {
      if (mounted) {
        setState(() => _isSubmitting = false);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Failed to place order: ${e.toString()}'),
            backgroundColor: AppColors.error,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final cartState = ref.watch(cartProvider);
    final shippingAsync = ref.watch(shippingMethodsProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Checkout'),
      ),
      bottomNavigationBar: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: Colors.white,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.06),
              blurRadius: 10,
              offset: const Offset(0, -4),
            ),
          ],
        ),
        child: SafeArea(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text('Total Payable', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                  Text(
                    CurrencyFormatter.format(cartState.grandTotal),
                    style: const TextStyle(fontSize: 19, fontWeight: FontWeight.w800, color: AppColors.primaryDark),
                  ),
                ],
              ),
              const SizedBox(height: 14),
              CustomButton(
                text: 'Confirm & Place Order',
                icon: Icons.check_circle_outline_rounded,
                isLoading: _isSubmitting,
                onPressed: _handlePlaceOrder,
              ),
            ],
          ),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Delivery Details Card
              _buildSectionCard(
                title: '1. Shipping Information',
                icon: Icons.location_on_outlined,
                child: Column(
                  children: [
                    CustomTextField(
                      controller: _nameController,
                      label: 'Recipient Name *',
                      hintText: 'e.g. John Doe',
                      prefixIcon: Icons.person_outline,
                      validator: (val) => val == null || val.trim().isEmpty ? 'Name is required' : null,
                    ),
                    const SizedBox(height: 12),
                    CustomTextField(
                      controller: _phoneController,
                      label: 'Contact Phone Number *',
                      hintText: 'e.g. 01700000000',
                      prefixIcon: Icons.phone_outlined,
                      keyboardType: TextInputType.phone,
                      validator: (val) => val == null || val.trim().isEmpty ? 'Phone is required' : null,
                    ),
                    const SizedBox(height: 12),
                    CustomTextField(
                      controller: _addressController,
                      label: 'Delivery Address *',
                      hintText: 'House/Street/Area details',
                      prefixIcon: Icons.home_outlined,
                      maxLines: 2,
                      validator: (val) => val == null || val.trim().isEmpty ? 'Address is required' : null,
                    ),
                    const SizedBox(height: 12),
                    CustomTextField(
                      controller: _cityController,
                      label: 'City / District',
                      hintText: 'e.g. Dhaka, Chittagong',
                      prefixIcon: Icons.location_city_outlined,
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 16),

              // Shipping Method Selector
              _buildSectionCard(
                title: '2. Delivery Method',
                icon: Icons.local_shipping_outlined,
                child: shippingAsync.when(
                  data: (methods) {
                    if (methods.isEmpty) {
                      return const Text('Standard delivery will be applied.');
                    }

                    // Auto select first if none selected
                    if (cartState.selectedShipping == null && methods.isNotEmpty) {
                      WidgetsBinding.instance.addPostFrameCallback((_) {
                        ref.read(cartProvider.notifier).setShippingMethod(methods.first);
                      });
                    }

                    return Column(
                      children: methods.map((method) {
                        final isSelected = cartState.selectedShipping?.id == method.id;
                        return Container(
                          margin: const EdgeInsets.only(bottom: 8),
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(
                              color: isSelected ? AppColors.primary : AppColors.border,
                              width: isSelected ? 1.5 : 1,
                            ),
                            color: isSelected ? AppColors.primaryLight.withOpacity(0.2) : Colors.white,
                          ),
                          child: RadioListTile<int>(
                            value: method.id,
                            groupValue: cartState.selectedShipping?.id,
                            activeColor: AppColors.primary,
                            title: Text(
                              method.name,
                              style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13),
                            ),
                            subtitle: method.estimatedDelivery != null
                                ? Text(method.estimatedDelivery!, style: const TextStyle(fontSize: 11))
                                : null,
                            secondary: Text(
                              CurrencyFormatter.format(method.cost),
                              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: AppColors.primaryDark),
                            ),
                            onChanged: (_) => ref.read(cartProvider.notifier).setShippingMethod(method),
                          ),
                        );
                      }).toList(),
                    );
                  },
                  loading: () => const Center(child: Padding(padding: EdgeInsets.all(12), child: CircularProgressIndicator())),
                  error: (_, __) => const Text('Could not load shipping methods'),
                ),
              ),

              const SizedBox(height: 16),

              // Payment Method
              _buildSectionCard(
                title: '3. Payment Method',
                icon: Icons.payment_outlined,
                child: Column(
                  children: [
                    Container(
                      margin: const EdgeInsets.only(bottom: 8),
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(
                          color: _paymentMethod == 'cod' ? AppColors.primary : AppColors.border,
                          width: _paymentMethod == 'cod' ? 1.5 : 1,
                        ),
                        color: _paymentMethod == 'cod' ? AppColors.primaryLight.withOpacity(0.2) : Colors.white,
                      ),
                      child: RadioListTile<String>(
                        value: 'cod',
                        groupValue: _paymentMethod,
                        activeColor: AppColors.primary,
                        title: const Text('Cash on Delivery (COD)', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
                        subtitle: const Text('Pay with cash upon receiving your order', style: TextStyle(fontSize: 11)),
                        secondary: const Icon(Icons.money_rounded, color: AppColors.primary),
                        onChanged: (val) => setState(() => _paymentMethod = val!),
                      ),
                    ),
                    Container(
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(
                          color: _paymentMethod == 'online' ? AppColors.primary : AppColors.border,
                          width: _paymentMethod == 'online' ? 1.5 : 1,
                        ),
                        color: _paymentMethod == 'online' ? AppColors.primaryLight.withOpacity(0.2) : Colors.white,
                      ),
                      child: RadioListTile<String>(
                        value: 'online',
                        groupValue: _paymentMethod,
                        activeColor: AppColors.primary,
                        title: const Text('Online Payment / Cards / bKash / Nagad', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
                        subtitle: const Text('Instant secure digital payment', style: TextStyle(fontSize: 11)),
                        secondary: const Icon(Icons.credit_card_rounded, color: AppColors.primary),
                        onChanged: (val) => setState(() => _paymentMethod = val!),
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 16),

              // Order Notes
              _buildSectionCard(
                title: '4. Special Instructions (Optional)',
                icon: Icons.note_alt_outlined,
                child: CustomTextField(
                  controller: _notesController,
                  label: 'Order Notes',
                  hintText: 'e.g. Leave package with security guard',
                  maxLines: 2,
                ),
              ),

              const SizedBox(height: 24),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildSectionCard({
    required String title,
    required IconData icon,
    required Widget child,
  }) {
    return Container(
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
            children: [
              Icon(icon, size: 20, color: AppColors.primary),
              const SizedBox(width: 8),
              Text(
                title,
                style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w700, color: AppColors.textPrimary),
              ),
            ],
          ),
          const Padding(
            padding: EdgeInsets.symmetric(vertical: 12),
            child: Divider(),
          ),
          child,
        ],
      ),
    );
  }
}
