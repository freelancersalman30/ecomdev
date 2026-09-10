import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/constants/app_colors.dart';
import '../../../data/models/warranty_model.dart';
import '../../../providers/auth_provider.dart';
import '../../../providers/warranty_provider.dart';
import '../../widgets/custom_button.dart';
import '../../widgets/custom_text_field.dart';
import '../auth/login_screen.dart';
import 'warranty_verify_scanner_screen.dart';

class WarrantyScreen extends ConsumerWidget {
  const WarrantyScreen({super.key});

  void _openClaimDialog(BuildContext context, WidgetRef ref, WarrantyModel warranty) {
    final descController = TextEditingController();
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text('Claim Warranty for ${warranty.productTitle}'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Serial: ${warranty.serialNo ?? "N/A"}', style: const TextStyle(fontSize: 12, color: AppColors.textMuted)),
            const SizedBox(height: 12),
            CustomTextField(
              controller: descController,
              label: 'Issue Description *',
              hintText: 'Describe the problem in detail...',
              maxLines: 3,
            ),
          ],
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Cancel')),
          ElevatedButton(
            onPressed: () async {
              if (descController.text.trim().isEmpty) return;
              Navigator.pop(ctx);
              try {
                final repo = ref.read(warrantyRepositoryProvider);
                await repo.claimWarranty(
                  warrantyId: warranty.id,
                  issueDescription: descController.text.trim(),
                );
                if (context.mounted) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                      content: Text('Warranty claim submitted successfully!'),
                      backgroundColor: AppColors.success,
                    ),
                  );
                  ref.invalidate(customerWarrantiesProvider);
                }
              } catch (e) {
                if (context.mounted) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(content: Text('Claim failed: ${e.toString()}'), backgroundColor: AppColors.error),
                  );
                }
              }
            },
            child: const Text('Submit Claim'),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final authState = ref.watch(authProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Product Warranties'),
        actions: [
          IconButton(
            icon: const Icon(Icons.qr_code_scanner_rounded),
            tooltip: 'Scan Serial Barcode',
            onPressed: () {
              Navigator.of(context).push(
                MaterialPageRoute(builder: (_) => const WarrantyVerifyScannerScreen()),
              );
            },
          ),
        ],
      ),
      body: !authState.isAuthenticated
          ? Center(
              child: Padding(
                padding: const EdgeInsets.all(24.0),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(Icons.verified_user_outlined, size: 64, color: AppColors.textMuted),
                    const SizedBox(height: 16),
                    const Text(
                      'Sign In to View Warranties',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 8),
                    const Text(
                      'Access all your registered product warranties and submit claims easily.',
                      textAlign: TextAlign.center,
                      style: TextStyle(fontSize: 13, color: AppColors.textSecondary),
                    ),
                    const SizedBox(height: 24),
                    CustomButton(
                      text: 'Sign In',
                      onPressed: () {
                        Navigator.of(context).push(MaterialPageRoute(builder: (_) => const LoginScreen()));
                      },
                    ),
                    const SizedBox(height: 12),
                    CustomButton(
                      text: 'Quick Barcode Verification',
                      isOutlined: true,
                      onPressed: () {
                        Navigator.of(context).push(MaterialPageRoute(builder: (_) => const WarrantyVerifyScannerScreen()));
                      },
                    ),
                  ],
                ),
              ),
            )
          : ref.watch(customerWarrantiesProvider).when(
                data: (warranties) {
                  if (warranties.isEmpty) {
                    return Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const Icon(Icons.shield_outlined, size: 64, color: AppColors.textMuted),
                          const SizedBox(height: 12),
                          const Text('No registered warranties found', style: TextStyle(fontWeight: FontWeight.bold)),
                          const SizedBox(height: 16),
                          CustomButton(
                            text: 'Scan Product Barcode',
                            icon: Icons.qr_code_scanner,
                            onPressed: () {
                              Navigator.of(context).push(
                                MaterialPageRoute(builder: (_) => const WarrantyVerifyScannerScreen()),
                              );
                            },
                          ),
                        ],
                      ),
                    );
                  }

                  return RefreshIndicator(
                    onRefresh: () async {
                      ref.invalidate(customerWarrantiesProvider);
                    },
                    child: ListView.separated(
                      padding: const EdgeInsets.all(16),
                      itemCount: warranties.length,
                      separatorBuilder: (_, __) => const SizedBox(height: 12),
                      itemBuilder: (context, index) {
                        final w = warranties[index];
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
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Expanded(
                                    child: Text(
                                      w.productTitle,
                                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                                    ),
                                  ),
                                  Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                    decoration: BoxDecoration(
                                      color: w.status == 'active'
                                          ? AppColors.success.withOpacity(0.15)
                                          : AppColors.error.withOpacity(0.15),
                                      borderRadius: BorderRadius.circular(6),
                                    ),
                                    child: Text(
                                      w.status.toUpperCase(),
                                      style: TextStyle(
                                        color: w.status == 'active' ? AppColors.success : AppColors.error,
                                        fontWeight: FontWeight.bold,
                                        fontSize: 10,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                              const SizedBox(height: 6),
                              if (w.serialNo != null)
                                Text('Serial No: ${w.serialNo}', style: const TextStyle(fontSize: 12, color: AppColors.textSecondary)),
                              Text('Period: ${w.warrantyPeriod} • Expires: ${w.expiryDate}', style: const TextStyle(fontSize: 12, color: AppColors.textSecondary)),
                              if (w.canClaim) ...[
                                const SizedBox(height: 12),
                                CustomButton(
                                  text: 'Claim Warranty',
                                  icon: Icons.assignment_turned_in_outlined,
                                  height: 40,
                                  onPressed: () => _openClaimDialog(context, ref, w),
                                ),
                              ],
                            ],
                          ),
                        );
                      },
                    ),
                  );
                },
                loading: () => const Center(child: CircularProgressIndicator(color: AppColors.primary)),
                error: (err, _) => Center(child: Text('Error: ${err.toString()}')),
              ),
    );
  }
}
