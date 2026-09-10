class OrderSummaryModel {
  final int id;
  final String orderNo;
  final num totalAmount;
  final String status;
  final String paymentStatus;
  final String paymentMethod;
  final int itemsCount;
  final String createdAt;

  OrderSummaryModel({
    required this.id,
    required this.orderNo,
    required this.totalAmount,
    required this.status,
    required this.paymentStatus,
    required this.paymentMethod,
    required this.itemsCount,
    required this.createdAt,
  });

  factory OrderSummaryModel.fromJson(Map<String, dynamic> json) {
    return OrderSummaryModel(
      id: json['id'] ?? 0,
      orderNo: json['order_no'] ?? '',
      totalAmount: json['total_amount'] ?? 0,
      status: json['status'] ?? 'pending',
      paymentStatus: json['payment_status'] ?? 'unpaid',
      paymentMethod: json['payment_method'] ?? 'cod',
      itemsCount: json['items_count'] ?? 1,
      createdAt: json['created_at'] ?? '',
    );
  }
}

class OrderItemModel {
  final int productId;
  final String title;
  final String? variant;
  final num price;
  final int quantity;
  final num total;
  final String? image;

  OrderItemModel({
    required this.productId,
    required this.title,
    this.variant,
    required this.price,
    required this.quantity,
    required this.total,
    this.image,
  });

  factory OrderItemModel.fromJson(Map<String, dynamic> json) {
    return OrderItemModel(
      productId: json['product_id'] ?? 0,
      title: json['title'] ?? json['product_name'] ?? '',
      variant: json['variant'] ?? json['variant_name'],
      price: json['price'] ?? 0,
      quantity: json['quantity'] ?? 1,
      total: json['total'] ?? 0,
      image: json['image'] ?? json['thumbnail'],
    );
  }
}

class OrderStatusTimelineStep {
  final String status;
  final String label;
  final String? timestamp;
  final bool isCompleted;
  final String? note;

  OrderStatusTimelineStep({
    required this.status,
    required this.label,
    this.timestamp,
    required this.isCompleted,
    this.note,
  });

  factory OrderStatusTimelineStep.fromJson(Map<String, dynamic> json) {
    return OrderStatusTimelineStep(
      status: json['status'] ?? '',
      label: json['label'] ?? '',
      timestamp: json['timestamp'],
      isCompleted: json['is_completed'] ?? false,
      note: json['note'],
    );
  }
}

class OrderDetailModel {
  final String orderNo;
  final String customerName;
  final String customerPhone;
  final String deliveryAddress;
  final String? city;
  final num subtotal;
  final num shippingCost;
  final num discount;
  final num totalAmount;
  final String status;
  final String paymentStatus;
  final String paymentMethod;
  final String? courierName;
  final String? trackingCode;
  final String createdAt;
  final List<OrderItemModel> items;
  final List<OrderStatusTimelineStep> timeline;

  OrderDetailModel({
    required this.orderNo,
    required this.customerName,
    required this.customerPhone,
    required this.deliveryAddress,
    this.city,
    required this.subtotal,
    required this.shippingCost,
    required this.discount,
    required this.totalAmount,
    required this.status,
    required this.paymentStatus,
    required this.paymentMethod,
    this.courierName,
    this.trackingCode,
    required this.createdAt,
    required this.items,
    required this.timeline,
  });

  factory OrderDetailModel.fromJson(Map<String, dynamic> json) {
    var itemsList = <OrderItemModel>[];
    if (json['items'] != null && json['items'] is List) {
      itemsList = (json['items'] as List)
          .map((item) => OrderItemModel.fromJson(item))
          .toList();
    }

    var timelineList = <OrderStatusTimelineStep>[];
    if (json['status_timeline'] != null && json['status_timeline'] is List) {
      timelineList = (json['status_timeline'] as List)
          .map((step) => OrderStatusTimelineStep.fromJson(step))
          .toList();
    }

    return OrderDetailModel(
      orderNo: json['order_no'] ?? '',
      customerName: json['customer_name'] ?? json['shipping_name'] ?? '',
      customerPhone: json['customer_phone'] ?? json['shipping_phone'] ?? '',
      deliveryAddress: json['delivery_address'] ?? json['shipping_address'] ?? '',
      city: json['city'] ?? json['shipping_city'],
      subtotal: json['subtotal'] ?? 0,
      shippingCost: json['shipping_cost'] ?? 0,
      discount: json['discount'] ?? 0,
      totalAmount: json['total_amount'] ?? 0,
      status: json['status'] ?? 'pending',
      paymentStatus: json['payment_status'] ?? 'unpaid',
      paymentMethod: json['payment_method'] ?? 'cod',
      courierName: json['courier_name'],
      trackingCode: json['tracking_code'],
      createdAt: json['created_at'] ?? '',
      items: itemsList,
      timeline: timelineList,
    );
  }
}
