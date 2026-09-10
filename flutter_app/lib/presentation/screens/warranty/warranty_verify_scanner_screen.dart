import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:mobile_scanner/mobile_scanner.dart';
import '../../../core/constants/app_colors.dart';
import '../../../data/models/warranty_model.dart';
import '../../../providers/warranty_provider.dart';
import '../../widgets/custom_button.dart';
import '../../widgets/custom_text_field.dart';

class WarrantyVerifyScannerScreen extends ConsumerStatefulWidget {
  const WarrantyVerifyScannerScreen({super.key});

  @override
  ConsumerState<WarrantyVerifyScannerScreen> createState() => _WarrantyVerifyScannerScreenState();
}

class _WarrantyVerifyScannerScreenState extends ConsumerState<WarrantyVerifyScannerScreen> {
  final _serialController = TextEditingController();
  final MobileScannerController _cameraController = MobileScannerController();
  String? _searchedSerial;
  bool _isCameraActive = true;

  @override
  void dispose() {
    _serialController.dispose();
    _cameraController.dispose();
    super.dispose();
  }

  void _onBarcodeDetected(BarcodeCapture capture) {
    if (!_isCameraActive) return;
    final List<Barcode> barcodes = capture.barcodes;
    for (final barcode in barcodes) {
      if (barcode.rawValue != null && barcode.rawValue!.isNotEmpty) {
        setState(() {
          _isCameraActive = false;
          _serialController.text = barcode.rawValue!;
          _searchedSerial = barcode.rawValue!;
        });
        break;
      }
    }
  }

  void _verifyManual() {
    final serial = _serialController.text.trim();
    if (serial.isEmpty) return;
    setState(() {
      _searchedSerial = serial;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Warranty Scanner'),
        actions: [
          IconButton(
            icon: Icon(_isCameraActive ? Icons.flash_on : Icons.camera_alt),
            onPressed: () {
              setState(() {
                _isCameraActive = !_isCameraActive;
              });
            },
          ),
        ],
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            // Scanner View
            if (_isCameraActive)
              Container(
                height: 240,
                width: double.infinity,
                color: Colors.black,
                child: Stack(
                  alignment: Alignment.center,
                  children: [
                    MobileScanner(
                      controller: _cameraController,
                      onDetect: _onBarcodeDetected,
                    ),
                    Container(
                      width: 200,
                      height: 120,
                      decoration: BoxDecoration(
                        border: Border.all(color: AppColors.primary, width: 2),
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                    const Positioned(
                      bottom: 12,
                      child: Text(
                        'Align barcode / QR within frame',
                        style: TextStyle(color: Colors.white70, fontSize: 12),
                      ),
                    ),
                  ],
                ),
              ),

            Padding(
              padding: const EdgeInsets.all(16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Manual input
                  Row(
                    children: [
                      Expanded(
                        child: CustomTextField(
                          controller: _serialController,
                          label: 'Serial Number / Warranty Code',
                          hintText: 'e.g. SN-88392183',
                          prefixIcon: Icons.qr_code_2_rounded,
                        ),
                      ),
                      const SizedBox(width: 8),
                      Padding(
                        padding: const EdgeInsets.only(top: 24),
                        child: ElevatedButton(
                          onPressed: _verifyManual,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: AppColors.primary,
                            minimumSize: const Size(80, 50),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                          ),
                          child: const Text('Check', style: TextStyle(color: Colors.white)),
                        ),
                      ),
                    ],
                  ),

                  const SizedBox(height: 24),

                  // Result Card
                  if (_searchedSerial != null) ...[
                    ref.watch(verifyWarrantyProvider(_searchedSerial!)).when(
                          data: (result) => _buildResultCard(result),
                          loading: () => const Center(
                            child: Padding(
                              padding: EdgeInsets.all(24),
                              child: CircularProgressIndicator(color: AppColors.primary),
                            ),
                          ),
                          error: (err, _) => Container(
                            padding: const EdgeInsets.all(16),
                            decoration: BoxDecoration(
                              color: AppColors.error.withOpacity(0.08),
                              borderRadius: BorderRadius.circular(16),
                              border: Border.all(color: AppColors.error.withOpacity(0.3)),
                            ),
                            child: Row(
                              children: [
                                const Icon(Icons.error_outline, color: AppColors.error),
                                const SizedBox(width: 12),
                                Expanded(child: Text('Verification error: ${err.toString()}')),
                              ],
                            ),
                          ),
                        ),
                  ],
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildResultCard(WarrantyVerifyResult result) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(
          color: result.isValid ? AppColors.success : AppColors.error,
          width: 1.5,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(
                result.isValid ? Icons.verified_rounded : Icons.cancel_rounded,
                color: result.isValid ? AppColors.success : AppColors.error,
                size: 28,
              ),
              const SizedBox(width: 10),
              Text(
                result.isValid ? 'Warranty Valid' : 'Warranty Invalid / Expired',
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                  color: result.isValid ? AppColors.success : AppColors.error,
                ),
              ),
            ],
          ),
          const SizedBox(height: 14),
          if (result.productName != null) ...[
            Text('Product: ${result.productName}', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
            const SizedBox(height: 4),
          ],
          Text('Serial Number: ${result.serialNo}', style: const TextStyle(fontSize: 13, color: AppColors.textSecondary)),
          if (result.purchaseDate != null) ...[
            const SizedBox(height: 4),
            Text('Purchase Date: ${result.purchaseDate}', style: const TextStyle(fontSize: 13, color: AppColors.textSecondary)),
          ],
          if (result.expiryDate != null) ...[
            const SizedBox(height: 4),
            Text('Expiry Date: ${result.expiryDate}', style: const TextStyle(fontSize: 13, color: AppColors.textSecondary)),
          ],
          if (result.daysLeft != null) ...[
            const SizedBox(height: 8),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              decoration: BoxDecoration(
                color: AppColors.primaryLight,
                borderRadius: BorderRadius.circular(6),
              ),
              child: Text(
                '${result.daysLeft} days remaining',
                style: const TextStyle(fontWeight: FontWeight.bold, color: AppColors.primaryDark, fontSize: 12),
              ),
            ),
          ],
        ],
      ),
    );
  }
}
