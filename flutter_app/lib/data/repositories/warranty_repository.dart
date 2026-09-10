import 'dart:io';
import '../../core/api/api_client.dart';
import '../../core/constants/api_endpoints.dart';
import '../models/warranty_model.dart';

class WarrantyRepository {
  final ApiClient _client = ApiClient();

  Future<List<WarrantyModel>> getCustomerWarranties() async {
    final response = await _client.get(ApiEndpoints.customerWarranties);
    final list = response['data'] as List? ?? [];
    return list.map((item) => WarrantyModel.fromJson(item)).toList();
  }

  Future<WarrantyVerifyResult> verifyWarranty(String serialNo) async {
    final response = await _client.get(
      ApiEndpoints.verifyWarranty,
      queryParameters: {'serial_no': serialNo},
    );
    return WarrantyVerifyResult.fromJson(response['data'] ?? response);
  }

  Future<Map<String, dynamic>> claimWarranty({
    required int warrantyId,
    required String issueDescription,
    File? proofImage,
  }) async {
    if (proofImage != null) {
      return await _client.postMultipart(
        ApiEndpoints.claimWarranty,
        data: {
          'warranty_id': warrantyId,
          'issue_description': issueDescription,
        },
        files: {'proof_image': proofImage},
      );
    } else {
      return await _client.post(
        ApiEndpoints.claimWarranty,
        data: {
          'warranty_id': warrantyId,
          'issue_description': issueDescription,
        },
      );
    }
  }
}
