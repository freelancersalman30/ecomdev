import 'package:intl/intl.dart';
import '../constants/app_constants.dart';

class CurrencyFormatter {
  static final NumberFormat _formatter = NumberFormat("#,##0.00", "en_US");

  static String format(num? amount, {bool includeSymbol = true}) {
    if (amount == null) return includeSymbol ? '${AppConstants.currencySymbol}0.00' : '0.00';
    final formatted = _formatter.format(amount);
    return includeSymbol ? '${AppConstants.currencySymbol}$formatted' : formatted;
  }
}
