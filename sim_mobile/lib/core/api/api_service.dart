// lib/core/api/api_service.dart

import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/app_config.dart';

class ApiService {
  // Singleton pattern (ringan, 1 instance saja)
  static final ApiService _instance = ApiService._internal();
  factory ApiService() => _instance;
  ApiService._internal();

  /// GET TOKEN dari SharedPreferences
  Future<String?> _getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('token');
  }

  /// HEADERS dengan Authorization
  Future<Map<String, String>> _headers({bool needsAuth = false}) async {
    final headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };

    if (needsAuth) {
      final token = await _getToken();
      if (token != null) {
        headers['Authorization'] = 'Bearer $token';
      }
    }

    return headers;
  }

  /// LOGIN SANTRI
  /// Return: {success, token, user, santri}
  Future<Map<String, dynamic>> login({
    required String idSantri,
    required String password,
  }) async {
    try {
      final response = await http
          .post(
            Uri.parse('${AppConfig.baseUrl}/login'),
            headers: await _headers(),
            body: json.encode({
              'id_santri': idSantri,
              'password': password,
            }),
          )
          .timeout(AppConfig.timeout);

      final data = json.decode(response.body);

      if (response.statusCode == 200) {
        // Simpan token ke SharedPreferences
        if (data['token'] != null) {
          final prefs = await SharedPreferences.getInstance();
          await prefs.setString('token', data['token']);
          await prefs.setString('user_data', json.encode(data['user']));
          if (data['santri'] != null) {
            await prefs.setString('santri_data', json.encode(data['santri']));
          }
        }
        return data;
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Login gagal',
        };
      }
    } catch (e) {
      return {
        'success': false,
        'message': 'Koneksi error: ${e.toString()}',
      };
    }
  }

  /// LOGOUT
  Future<Map<String, dynamic>> logout() async {
    try {
      final response = await http
          .post(
            Uri.parse('${AppConfig.baseUrl}/logout'),
            headers: await _headers(needsAuth: true),
          )
          .timeout(AppConfig.timeout);

      // Hapus semua data lokal
      final prefs = await SharedPreferences.getInstance();
      await prefs.clear();

      return json.decode(response.body);
    } catch (e) {
      // Tetap clear local data meskipun API error
      final prefs = await SharedPreferences.getInstance();
      await prefs.clear();

      return {
        'success': false,
        'message': 'Logout error: ${e.toString()}',
      };
    }
  }

  /// GET PROFILE
  Future<Map<String, dynamic>> getProfile() async {
    try {
      final response = await http
          .get(
            Uri.parse('${AppConfig.baseUrl}/profile'),
            headers: await _headers(needsAuth: true),
          )
          .timeout(AppConfig.timeout);

      return json.decode(response.body);
    } catch (e) {
      return {
        'success': false,
        'message': 'Gagal mengambil profil: ${e.toString()}',
      };
    }
  }

  /// CHECK TOKEN VALIDITY
  Future<bool> isTokenValid() async {
    try {
      final response = await http
          .get(
            Uri.parse('${AppConfig.baseUrl}/profile'),
            headers: await _headers(needsAuth: true),
          )
          .timeout(AppConfig.timeout);

      return response.statusCode == 200;
    } catch (e) {
      return false;
    }
  }
}