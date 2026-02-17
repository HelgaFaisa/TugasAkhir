// lib/features/kepulangan/data/services/kepulangan_service.dart

import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import '../../../../core/config/app_config.dart';
import '../models/kepulangan_model.dart';
import 'package:flutter/foundation.dart';


class KepulanganService {
  /// Get token dari SharedPreferences
  Future<String?> _getToken() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      return prefs.getString('token');
    } catch (e) {
      if (kDebugMode) {
        print('Error getting token: $e');
      }
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

  /// GET LIST KEPULANGAN
  /// Parameter:
  /// - page: halaman (default 1)
  /// - status: filter status (optional)
  /// - tahun: filter tahun (optional)
  Future<Map<String, dynamic>> getListKepulangan({
    int page = 1,
    String? status,
    String? tahun,
  }) async {
    try {
      // Build query params
      final queryParams = <String, String>{'page': page.toString()};
      if (status != null && status.isNotEmpty) {
        queryParams['status'] = status;
      }
      if (tahun != null && tahun.isNotEmpty) {
        queryParams['tahun'] = tahun;
      }

      final url = Uri.parse(
        '${AppConfig.baseUrl}/kepulangan',
      ).replace(queryParameters: queryParams);

      print('🔵 GET KEPULANGAN URL: $url');

      final response = await http
          .get(url, headers: await _headers())
          .timeout(const Duration(seconds: 15));

      if (kDebugMode) {
        print('🔵 Kepulangan Response Status: ${response.statusCode}');
      }
      print('🔵 Kepulangan Response Body: ${response.body}');

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);

        if (jsonResponse['success'] == true) {
          final data = jsonResponse['data'];

          // Parse list kepulangan
          final List<KepulanganModel> kepulanganList =
              (data['kepulangan'] as List)
                  .map((item) => KepulanganModel.fromJson(item))
                  .toList();

          // Parse kuota info
          final KuotaInfo kuotaInfo = KuotaInfo.fromJson(data['kuota']);

          // Parse pagination
          final KepulanganPaginationModel pagination =
              KepulanganPaginationModel.fromJson(data['pagination']);

          return {
            'success': true,
            'kepulangan': kepulanganList,
            'kuota': kuotaInfo,
            'pagination': pagination,
          };
        } else {
          return {
            'success': false,
            'message': jsonResponse['message'] ?? 'Gagal mengambil data',
          };
        }
      } else {
        return {
          'success': false,
          'message': 'Server error: ${response.statusCode}',
        };
      }
    } catch (e) {
      print('🔴 Kepulangan Error: $e');
      return {'success': false, 'message': 'Error: ${e.toString()}'};
    }
  }

  /// GET DETAIL KEPULANGAN
  Future<Map<String, dynamic>> getDetailKepulangan(String idKepulangan) async {
    try {
      final url = Uri.parse('${AppConfig.baseUrl}/kepulangan/$idKepulangan');

      print('🔵 GET DETAIL KEPULANGAN URL: $url');

      final response = await http
          .get(url, headers: await _headers())
          .timeout(const Duration(seconds: 15));

      print('🔵 Detail Kepulangan Response Status: ${response.statusCode}');
      print('🔵 Detail Kepulangan Response Body: ${response.body}');

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);

        if (jsonResponse['success'] == true) {
          final data = jsonResponse['data'];

          // Parse kepulangan
          final KepulanganModel kepulangan = KepulanganModel.fromJson(
            data['kepulangan'],
          );

          // Parse kuota info
          final KuotaInfo kuotaInfo = KuotaInfo.fromJson(data['kuota']);

          // Parse santri info
          final santriInfo = data['kepulangan']['santri'];

          return {
            'success': true,
            'kepulangan': kepulangan,
            'kuota': kuotaInfo,
            'santri': santriInfo,
          };
        } else {
          return {
            'success': false,
            'message': jsonResponse['message'] ?? 'Gagal mengambil detail',
          };
        }
      } else if (response.statusCode == 404) {
        return {'success': false, 'message': 'Data kepulangan tidak ditemukan'};
      } else {
        return {
          'success': false,
          'message': 'Server error: ${response.statusCode}',
        };
      }
    } catch (e) {
      print('🔴 Detail Kepulangan Error: $e');
      return {'success': false, 'message': 'Error: ${e.toString()}'};
    }
  }

  /// GET KUOTA INFO
  Future<Map<String, dynamic>> getKuotaInfo() async {
    try {
      final url = Uri.parse('${AppConfig.baseUrl}/kepulangan/kuota');

      print('🔵 GET KUOTA INFO URL: $url');

      final response = await http
          .get(url, headers: await _headers())
          .timeout(const Duration(seconds: 15));

      print('🔵 Kuota Info Response Status: ${response.statusCode}');
      print('🔵 Kuota Info Response Body: ${response.body}');

      if (response.statusCode == 200) {
        final jsonResponse = json.decode(response.body);

        if (jsonResponse['success'] == true) {
          final data = jsonResponse['data'];

          // Parse kuota info
          final KuotaInfo kuotaInfo = KuotaInfo.fromJson(data);

          // Parse detail izin (optional, untuk detail breakdown)
          final List<dynamic>? detailIzin = data['detail_izin'];

          return {
            'success': true,
            'kuota': kuotaInfo,
            'detail_izin': detailIzin,
          };
        } else {
          return {
            'success': false,
            'message': jsonResponse['message'] ?? 'Gagal mengambil kuota',
          };
        }
      } else {
        return {
          'success': false,
          'message': 'Server error: ${response.statusCode}',
        };
      }
    } catch (e) {
      print('🔴 Kuota Info Error: $e');
      return {'success': false, 'message': 'Error: ${e.toString()}'};
    }
  }
}
