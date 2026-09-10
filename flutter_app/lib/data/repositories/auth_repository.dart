import 'dart:convert';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../../core/api/api_client.dart';
import '../../core/constants/api_endpoints.dart';
import '../../core/constants/app_constants.dart';
import '../models/user_model.dart';

class AuthRepository {
  final ApiClient _client = ApiClient();
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  Future<AuthResponse> login({
    required String login,
    required String password,
  }) async {
    final response = await _client.post(
      ApiEndpoints.login,
      data: {
        'login': login,
        'password': password,
      },
    );

    final authResponse = AuthResponse.fromJson(response);
    await _storage.write(key: AppConstants.tokenKey, value: authResponse.token);
    await _storage.write(
      key: AppConstants.customerDataKey,
      value: jsonEncode(authResponse.customer.toJson()),
    );

    return authResponse;
  }

  Future<AuthResponse> register({
    required String name,
    required String phone,
    String? email,
    required String password,
    String? address,
    String? city,
  }) async {
    final response = await _client.post(
      ApiEndpoints.register,
      data: {
        'name': name,
        'phone': phone,
        'email': email,
        'password': password,
        'address': address,
        'city': city,
      },
    );

    final authResponse = AuthResponse.fromJson(response);
    await _storage.write(key: AppConstants.tokenKey, value: authResponse.token);
    await _storage.write(
      key: AppConstants.customerDataKey,
      value: jsonEncode(authResponse.customer.toJson()),
    );

    return authResponse;
  }

  Future<UserModel?> getProfile() async {
    final response = await _client.get(ApiEndpoints.profile);
    if (response['success'] == true && response['customer'] != null) {
      final user = UserModel.fromJson(response['customer']);
      await _storage.write(
        key: AppConstants.customerDataKey,
        value: jsonEncode(user.toJson()),
      );
      return user;
    }
    return null;
  }

  Future<UserModel> updateProfile({
    required String name,
    String? email,
    String? address,
    String? city,
  }) async {
    final response = await _client.put(
      ApiEndpoints.profile,
      data: {
        'name': name,
        'email': email,
        'address': address,
        'city': city,
      },
    );

    final user = UserModel.fromJson(response['customer']);
    await _storage.write(
      key: AppConstants.customerDataKey,
      value: jsonEncode(user.toJson()),
    );
    return user;
  }

  Future<void> logout() async {
    try {
      await _client.post(ApiEndpoints.logout);
    } catch (_) {}
    await _storage.delete(key: AppConstants.tokenKey);
    await _storage.delete(key: AppConstants.customerDataKey);
  }

  Future<String?> getToken() async {
    return await _storage.read(key: AppConstants.tokenKey);
  }

  Future<UserModel?> getCachedUser() async {
    final raw = await _storage.read(key: AppConstants.customerDataKey);
    if (raw != null) {
      return UserModel.fromJson(jsonDecode(raw));
    }
    return null;
  }
}
