import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/models/order_model.dart';
import '../data/repositories/order_repository.dart';

final orderRepositoryProvider = Provider<OrderRepository>((ref) => OrderRepository());

final customerOrdersProvider = FutureProvider.autoDispose<List<OrderSummaryModel>>((ref) async {
  final repo = ref.watch(orderRepositoryProvider);
  return await repo.getCustomerOrders();
});

final orderDetailProvider = FutureProvider.autoDispose.family<OrderDetailModel, String>((ref, orderNo) async {
  final repo = ref.watch(orderRepositoryProvider);
  return await repo.getCustomerOrderDetail(orderNo);
});

class TrackOrderParams {
  final String orderNo;
  final String phone;

  TrackOrderParams({required this.orderNo, required this.phone});

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is TrackOrderParams &&
          runtimeType == other.runtimeType &&
          orderNo == other.orderNo &&
          phone == other.phone;

  @override
  int get hashCode => orderNo.hashCode ^ phone.hashCode;
}

final trackOrderProvider = FutureProvider.autoDispose.family<OrderDetailModel, TrackOrderParams>((ref, params) async {
  final repo = ref.watch(orderRepositoryProvider);
  return await repo.trackOrder(orderNo: params.orderNo, phone: params.phone);
});
