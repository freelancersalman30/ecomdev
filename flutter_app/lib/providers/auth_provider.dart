import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/models/user_model.dart';
import '../data/repositories/auth_repository.dart';

final authRepositoryProvider = Provider<AuthRepository>((ref) => AuthRepository());

class AuthState {
  final bool isLoading;
  final bool isAuthenticated;
  final UserModel? user;
  final String? error;

  AuthState({
    this.isLoading = false,
    this.isAuthenticated = false,
    this.user,
    this.error,
  });

  AuthState copyWith({
    bool? isLoading,
    bool? isAuthenticated,
    UserModel? user,
    String? error,
  }) {
    return AuthState(
      isLoading: isLoading ?? this.isLoading,
      isAuthenticated: isAuthenticated ?? this.isAuthenticated,
      user: user ?? this.user,
      error: error,
    );
  }
}

class AuthNotifier extends StateNotifier<AuthState> {
  final AuthRepository _repo;

  AuthNotifier(this._repo) : super(AuthState(isLoading: true)) {
    checkInitialAuth();
  }

  Future<void> checkInitialAuth() async {
    try {
      final token = await _repo.getToken();
      if (token != null && token.isNotEmpty) {
        final cachedUser = await _repo.getCachedUser();
        state = AuthState(
          isLoading: false,
          isAuthenticated: true,
          user: cachedUser,
        );
        // Refresh in background
        _repo.getProfile().then((freshUser) {
          if (freshUser != null) {
            state = state.copyWith(user: freshUser);
          }
        }).catchError((_) {});
      } else {
        state = AuthState(isLoading: false, isAuthenticated: false);
      }
    } catch (_) {
      state = AuthState(isLoading: false, isAuthenticated: false);
    }
  }

  Future<bool> login(String login, String password) async {
    state = state.copyWith(isLoading: true, error: null);
    try {
      final response = await _repo.login(login: login, password: password);
      state = AuthState(
        isLoading: false,
        isAuthenticated: true,
        user: response.customer,
      );
      return true;
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
      return false;
    }
  }

  Future<bool> register({
    required String name,
    required String phone,
    String? email,
    required String password,
    String? address,
    String? city,
  }) async {
    state = state.copyWith(isLoading: true, error: null);
    try {
      final response = await _repo.register(
        name: name,
        phone: phone,
        email: email,
        password: password,
        address: address,
        city: city,
      );
      state = AuthState(
        isLoading: false,
        isAuthenticated: true,
        user: response.customer,
      );
      return true;
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
      return false;
    }
  }

  Future<void> logout() async {
    await _repo.logout();
    state = AuthState(isLoading: false, isAuthenticated: false, user: null);
  }

  Future<void> refreshProfile() async {
    final freshUser = await _repo.getProfile();
    if (freshUser != null) {
      state = state.copyWith(user: freshUser);
    }
  }
}

final authProvider = StateNotifierProvider<AuthNotifier, AuthState>((ref) {
  final repo = ref.watch(authRepositoryProvider);
  return AuthNotifier(repo);
});
