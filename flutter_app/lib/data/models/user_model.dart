class UserModel {
  final int id;
  final String name;
  final String phone;
  final String? email;
  final String? avatar;
  final String? address;
  final String? city;
  final int loyaltyPoints;
  final num totalSpent;
  final int totalOrders;

  UserModel({
    required this.id,
    required this.name,
    required this.phone,
    this.email,
    this.avatar,
    this.address,
    this.city,
    required this.loyaltyPoints,
    required this.totalSpent,
    required this.totalOrders,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      phone: json['phone'] ?? '',
      email: json['email'],
      avatar: json['avatar'],
      address: json['address'],
      city: json['city'],
      loyaltyPoints: json['loyalty_points'] ?? 0,
      totalSpent: json['total_spent'] ?? 0,
      totalOrders: json['total_orders'] ?? 0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'phone': phone,
      'email': email,
      'avatar': avatar,
      'address': address,
      'city': city,
      'loyalty_points': loyaltyPoints,
      'total_spent': totalSpent,
      'total_orders': totalOrders,
    };
  }
}

class AuthResponse {
  final bool success;
  final String message;
  final String token;
  final UserModel customer;

  AuthResponse({
    required this.success,
    required this.message,
    required this.token,
    required this.customer,
  });

  factory AuthResponse.fromJson(Map<String, dynamic> json) {
    return AuthResponse(
      success: json['success'] ?? false,
      message: json['message'] ?? '',
      token: json['token'] ?? '',
      customer: UserModel.fromJson(json['customer'] ?? {}),
    );
  }
}
