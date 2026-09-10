import '../../core/api/api_client.dart';
import '../../core/constants/api_endpoints.dart';
import '../models/order_model.dart';

class OrderRepository {
  final ApiClient _client = ApiClient();

  Future<List<OrderSummaryModel>> getCustomerOrders({int page = 1}) async {
    final response = await _client.get(
      ApiEndpoints.customerOrders,
      queryParameters: {'page': page},
    );
    final list = response['data'] as List? ?? [];
    return list.map((item) => OrderSummaryModel.fromJson(item)).toList();
  }

  Future<OrderDetailModel> getCustomerOrderDetail(String orderNo) async {
    final response = await _client.get(ApiEndpoints.customerOrderDetail(orderNo));
    return OrderDetailModel.fromJson(response['data'] ?? response);
  }

  Future<OrderDetailModel> trackOrder({
    required String orderNo,
    required String phone,
  }) async {
    final response = await _client.get(
      ApiEndpoints.trackOrder,
      queryParameters: {
        'order_no': orderNo,
        'phone': phone,
      },
    );
    return OrderDetailModel.fromJson(response['data'] ?? response);
  }
}
