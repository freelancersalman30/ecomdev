import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/models/home_feed_model.dart';
import '../data/repositories/catalog_repository.dart';

final catalogRepositoryProvider = Provider<CatalogRepository>((ref) => CatalogRepository());

final homeFeedProvider = FutureProvider.autoDispose<HomeFeedModel>((ref) async {
  final repo = ref.watch(catalogRepositoryProvider);
  return await repo.getHomeFeed();
});
