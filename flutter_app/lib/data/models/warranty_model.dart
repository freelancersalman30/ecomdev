class WarrantyModel {
  final int id;
  final String orderNo;
  final String productTitle;
  final String? serialNo;
  final String warrantyPeriod;
  final String purchaseDate;
  final String expiryDate;
  final String status;
  final bool canClaim;

  WarrantyModel({
    required this.id,
    required this.orderNo,
    required this.productTitle,
    this.serialNo,
    required this.warrantyPeriod,
    required this.purchaseDate,
    required this.expiryDate,
    required this.status,
    required this.canClaim,
  });

  factory WarrantyModel.fromJson(Map<String, dynamic> json) {
    return WarrantyModel(
      id: json['id'] ?? 0,
      orderNo: json['order_no'] ?? '',
      productTitle: json['product_title'] ?? json['product_name'] ?? '',
      serialNo: json['serial_no'],
      warrantyPeriod: json['warranty_period'] ?? '',
      purchaseDate: json['purchase_date'] ?? '',
      expiryDate: json['expiry_date'] ?? '',
      status: json['status'] ?? 'active',
      canClaim: json['can_claim'] ?? false,
    );
  }
}

class WarrantyVerifyResult {
  final bool isValid;
  final String serialNo;
  final String? productName;
  final String? orderNo;
  final String? purchaseDate;
  final String? expiryDate;
  final int? daysLeft;
  final String status;
  final String message;

  WarrantyVerifyResult({
    required this.isValid,
    required this.serialNo,
    this.productName,
    this.orderNo,
    this.purchaseDate,
    this.expiryDate,
    this.daysLeft,
    required this.status,
    required this.message,
  });

  factory WarrantyVerifyResult.fromJson(Map<String, dynamic> json) {
    return WarrantyVerifyResult(
      isValid: json['is_valid'] ?? false,
      serialNo: json['serial_no'] ?? '',
      productName: json['product_name'],
      orderNo: json['order_no'],
      purchaseDate: json['purchase_date'],
      expiryDate: json['expiry_date'],
      daysLeft: json['days_left'],
      status: json['status'] ?? 'unknown',
      message: json['message'] ?? '',
    );
  }
}
