class ApiException implements Exception {
  final String message;
  final int? statusCode;
  final Map<String, dynamic>? errors;

  ApiException({
    required this.message,
    this.statusCode,
    this.errors,
  });

  @override
  String toString() => message;

  String get firstValidationError {
    if (errors != null && errors!.isNotEmpty) {
      final firstKey = errors!.keys.first;
      final val = errors![firstKey];
      if (val is List && val.isNotEmpty) {
        return val.first.toString();
      }
      return val.toString();
    }
    return message;
  }
}
