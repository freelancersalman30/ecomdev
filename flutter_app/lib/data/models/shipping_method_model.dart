class CouponResultModel {
  final bool valid;
  final String code;
  final String discountType; // percentage, fixed
  final num discountValue;
  final num calculatedDiscount;
  final String message;

  CouponResultModel({
    required this.valid,
    required this.code,
    required this.discountType,
    required this.discountValue,
    required this.calculatedDiscount,
    required this.message,
  });

  factory CouponResultModel.fromJson(Map<String, dynamic> json) {
    return CouponResultModel(
      valid: json['valid'] ?? false,
      code: json['code'] ?? '',
      discountType: json['discount_type'] ?? 'fixed',
      discountValue: json['discount_value'] ?? 0,
      calculatedDiscount: json['calculated_discount'] ?? 0,
      message: json['message'] ?? '',
    );
  }
}

class ShippingMethodModel {
  final int id;
  final String name;
  final num cost;
  final String? estimatedDelivery;
  final String? zone;

  ShippingMethodModel({
    required this.id,
    required this.name,
    required this.cost,
    this.estimatedDelivery,
    this.zone,
  });

  factory ShippingMethodModel.fromJson(Map<String, dynamic> json) {
    return ShippingMethodModel(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      cost: json['cost'] ?? 0,
      estimatedDelivery: json['estimated_delivery'] ?? json['delivery_time'],
      zone: json['zone'],
    );
  }
}
