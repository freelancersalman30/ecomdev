import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/models/warranty_model.dart';
import '../data/repositories/warranty_repository.dart';

final warrantyRepositoryProvider = Provider<WarrantyRepository>((ref) => WarrantyRepository());

final customerWarrantiesProvider = FutureProvider.autoDispose<List<WarrantyModel>>((ref) async {
  final repo = ref.watch(warrantyRepositoryProvider);
  return await repo.getCustomerWarranties();
});

final verifyWarrantyProvider = FutureProvider.autoDispose.family<WarrantyVerifyResult, String>((ref, serialNo) async {
  final repo = ref.watch(warrantyRepositoryProvider);
  return await repo.verifyWarranty(serialNo);
});
