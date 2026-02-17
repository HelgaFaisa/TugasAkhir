// lib/features/kepulangan/data/services/pengajuan_kepulangan_service.dart

import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../../../../core/config/app_config.dart';
import '../models/pengajuan_kepulangan_model.dart';

class PengajuanKepulanganService {
  /// Get token dari SharedPreferences
  Future<String?> _getToken() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      return prefs.getString('token');
    } catch (e) {
      print('🔴 Error getting token: $e');
      return null;
    }
  }

  /// Headers dengan Authorization
  Future<Map<String, String>> _headers() async {
    final token = await _getToken();
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }

  /// POST: Submit pengajuan kepulangan baru
  /// Endpoint: /api/v1/kepulangan/pengajuan
  Future<Map<String, dynamic>> submitPengajuan({
    required String tanggalPulang,
    required String tanggalKembali,
    required String alasan,
  }) async {
    try {
      final url = Uri.parse('${AppConfig.baseUrl}/kepulangan/pengajuan');

      print('🔵 POST PENGAJUAN URL: $url');

      final body = json.encode({
        'tanggal_pulang': tanggalPulang,
        'tanggal_kembali': tanggalKembali,
        'alasan': alasan,
      });

      print('🔵 Request Body: $body');

      final response = await http
          .post(url, headers: await _headers(), body: body)
          .timeout(const Duration(seconds: 15));

      print('🔵 Response Status: ${response.statusCode}');
      print('🔵 Response Body: ${response.body}');

      if (response.statusCode == 200 || response.statusCode == 201) {
        final jsonResponse = json.decode(response.body);
        return jsonResponse;
      } else {
        final errorData = json.decode(response.body);
        return {
          'success': false,
          'message': errorData['message'] ?? 'Gagal submit pengajuan',
        };
      }
    } catch (e) {
      print('🔴 Submit Pengajuan Error: $e');
      return {
        'success': false,
        'message': 'Error: ${e.toString()}',
      };
    }
  }

  /// GET: List pengajuan kepulangan santri
  /// Endpoint: /api/v1/kepulangan/pengajuan
  Future<Map<String, dynamic>> getListPengajuan({int page = 1}) async {
    try {
      final url = Uri.parse(
        '${AppConfig.baseUrl}/kepulangan/pengajuan?page=$page',
      );

      print('🔵 GET PENGAJUAN URL: $url');

      final response = await http
          .get(url, headers: await _headers())
          .timeout(const Duration(seconds: 15));

      print('🔵 Response Status: ${response.statusCode}');

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);

        if (jsonResponse['success'] == true) {
          final data = jsonResponse['data'];

          // Parse list pengajuan
          final List<PengajuanKepulanganModel> pengajuanList =
              (data['pengajuan'] as List)
                  .map((item) => PengajuanKepulanganModel.fromJson(item))
                  .toList();

          return {
            'success': true,
            'pengajuan': pengajuanList,
            'pagination': data['pagination'],
          };
        } else {
          return {
            'success': false,
            'message': jsonResponse['message'] ?? 'Gagal get data',
          };
        }
      } else {
        return {
          'success': false,
          'message': 'Server error: ${response.statusCode}',
        };
      }
    } catch (e) {
      print('🔴 Get Pengajuan Error: $e');
      return {
        'success': false,
        'message': 'Error: ${e.toString()}',
      };
    }
  }

  /// POST: Preview durasi & validasi kuota (sebelum submit)
  /// Endpoint: /api/v1/kepulangan/pengajuan/preview
  Future<Map<String, dynamic>> previewPengajuan({
    required String tanggalPulang,
    required String tanggalKembali,
  }) async {
    try {
      final url = Uri.parse('${AppConfig.baseUrl}/kepulangan/pengajuan/preview');

      final body = json.encode({
        'tanggal_pulang': tanggalPulang,
        'tanggal_kembali': tanggalKembali,
      });

      final response = await http
          .post(url, headers: await _headers(), body: body)
          .timeout(const Duration(seconds: 10));

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        return {'success': false};
      }
    } catch (e) {
      print('🔴 Preview Error: $e');
      return {'success': false};
    }
  }
}