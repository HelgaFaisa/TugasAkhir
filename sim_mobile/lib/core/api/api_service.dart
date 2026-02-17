// lib/core/api/api_service.dart

import 'dart:convert';
import 'dart:async';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/app_config.dart';
import 'package:flutter/foundation.dart';

class ApiService {
  // Singleton pattern (ringan, 1 instance saja)
  static final ApiService _instance = ApiService._internal();
  factory ApiService() => _instance;
  ApiService._internal();

  /// GET TOKEN dari SharedPreferences
  Future<String?> _getToken() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      return prefs.getString('token');
    } catch (e) {
      if (kDebugMode) {
        if (kDebugMode) {
      }
        print('Error getting token: $e');
      }
      return null;
    }
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

  /// LOGIN WALI SANTRI
  /// Username: username akun wali
  /// Password: password (biasanya NIS)
  /// Return: {success, token, user, santri}
  Future<Map<String, dynamic>> login({
    required String username,
    required String password,
  }) async {
    try {
      final url = Uri.parse('${AppConfig.baseUrl}/login');
      if (kDebugMode) {
        print('🔵 Login URL: $url');
      } // Debug

      final response = await http
          .post(
            url,
            headers: await _headers(),
            body: json.encode({
              'id_santri':
                  username, // Backend masih pakai 'id_santri' sebagai key
              'password': password,
            }),
          )
          .timeout(
            AppConfig.timeout,
            onTimeout: () {
              throw TimeoutException(
                'Koneksi timeout. Periksa koneksi internet atau server Laravel tidak berjalan.',
              );
            },
          );

      if (kDebugMode) {
        print('🔵 Response status: ${response.statusCode}');
      } // Debug
      if (kDebugMode) {
        print('🔵 Response body: ${response.body}');
      } // Debug

      if (response.statusCode == 200) {
        final data = json.decode(response.body);

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
        final data = json.decode(response.body);
        return {'success': false, 'message': data['message'] ?? 'Login gagal'};
      }
    } on TimeoutException catch (e) {
      return {'success': false, 'message': e.message ?? 'Koneksi timeout'};
    } on SocketException catch (_) {
      return {
        'success': false,
        'message':
            'Tidak dapat terhubung ke server. Pastikan Laravel sudah berjalan.',
      };
    } catch (e) {
      if (kDebugMode) {
        print('🔴 Login error: $e');
      } // Debug
      return {'success': false, 'message': 'Error: ${e.toString()}'};
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
          .timeout(
            AppConfig.timeout,
            onTimeout: () {
              throw TimeoutException('Timeout saat logout');
            },
          );

      // Hapus semua data lokal
      final prefs = await SharedPreferences.getInstance();
      await prefs.clear();

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        return {
          'success':
              true, // Tetap return success karena local data sudah terhapus
          'message': 'Logout berhasil (offline)',
        };
      }
    } catch (e) {
      // Tetap clear local data meskipun API error
      final prefs = await SharedPreferences.getInstance();
      await prefs.clear();

      return {'success': true, 'message': 'Logout berhasil (offline)'};
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
          .timeout(
            AppConfig.timeout,
            onTimeout: () {
              throw TimeoutException('Timeout saat mengambil profil');
            },
          );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);

        // Update cache
        if (data['success'] == true && data['data'] != null) {
          final prefs = await SharedPreferences.getInstance();
          await prefs.setString('santri_data', json.encode(data['data']));
        }

        return data;
      } else {
        return {'success': false, 'message': 'Gagal mengambil profil'};
      }
    } catch (e) {
      return {'success': false, 'message': 'Error: ${e.toString()}'};
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
          .timeout(
            const Duration(seconds: 10),
          ); // Timeout lebih pendek untuk validasi

      return response.statusCode == 200;
    } catch (e) {
      if (kDebugMode) {
        print('🔴 Token validation error: $e');
      }
      return false;
    }
  }

  /// GET USER ROLE from local storage
  Future<String?> getUserRole() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final userJson = prefs.getString('user_data');
      if (userJson != null) {
        final userData = json.decode(userJson);
        return userData['role'];
      }
      return null;
    } catch (e) {
      if (kDebugMode) {
        print('🔴 Get user role error: $e');
      }
      return null;
    }
  }

  /// GET SALDO UANG SAKU
  Future<Map<String, dynamic>> getSaldoUangSaku({
    String? tanggalDari,
    String? tanggalSampai,
  }) async {
    try {
      // Build URL dengan query parameters
      var url = '${AppConfig.baseUrl}/uang-saku/saldo';
      final queryParams = <String, String>{};

      if (tanggalDari != null) queryParams['tanggal_dari'] = tanggalDari;
      if (tanggalSampai != null) queryParams['tanggal_sampai'] = tanggalSampai;

      if (queryParams.isNotEmpty) {
        url +=
            '?${queryParams.entries.map((e) => '${e.key}=${e.value}').join('&')}';
      }

      final response = await http
          .get(Uri.parse(url), headers: await _headers(needsAuth: true))
          .timeout(
            AppConfig.timeout,
            onTimeout: () {
              throw TimeoutException('Timeout saat mengambil saldo');
            },
          );

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        return {'success': false, 'message': 'Gagal mengambil saldo'};
      }
    } on SocketException {
      return {
        'success': false,
        'message': 'Koneksi gagal, periksa internet Anda',
      };
    } catch (e) {
      return {'success': false, 'message': 'Error: ${e.toString()}'};
    }
  }

  /// GET RIWAYAT UANG SAKU
  Future<Map<String, dynamic>> getRiwayatUangSaku({
    int page = 1,
    String? jenisTrans = 'semua',
    String? tanggalDari,
    String? tanggalSampai,
  }) async {
    try {
      // Build query parameters
      final queryParams = <String, String>{'page': page.toString()};

      if (jenisTrans != null && jenisTrans != 'semua') {
        queryParams['jenis_transaksi'] = jenisTrans;
      }
      if (tanggalDari != null) queryParams['tanggal_dari'] = tanggalDari;
      if (tanggalSampai != null) queryParams['tanggal_sampai'] = tanggalSampai;

      final url =
          '${AppConfig.baseUrl}/uang-saku?${queryParams.entries.map((e) => '${e.key}=${e.value}').join('&')}';

      final response = await http
          .get(Uri.parse(url), headers: await _headers(needsAuth: true))
          .timeout(
            AppConfig.timeout,
            onTimeout: () {
              throw TimeoutException('Timeout saat mengambil riwayat');
            },
          );

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        return {'success': false, 'message': 'Gagal mengambil riwayat'};
      }
    } on SocketException {
      return {
        'success': false,
        'message': 'Koneksi gagal, periksa internet Anda',
      };
    } catch (e) {
      return {'success': false, 'message': 'Error: ${e.toString()}'};
    }
  }

  /// GET LIST BERITA
  Future<Map<String, dynamic>> getBerita({int page = 1}) async {
    try {
      final url = Uri.parse('${AppConfig.baseUrl}/berita?page=$page');
      if (kDebugMode) {
        print('🔵 GET BERITA URL: $url');
      } // Debug

      final response = await http
          .get(url, headers: await _headers(needsAuth: true))
          .timeout(
            AppConfig.timeout,
            onTimeout: () {
              throw TimeoutException('Timeout saat mengambil berita');
            },
          );

      if (kDebugMode) {
        print('🔵 Berita Response Status: ${response.statusCode}');
      } // Debug
      if (kDebugMode) {
        print('🔵 Berita Response Body: ${response.body}');
      } // Debug

      if (response.statusCode == 200) {
        final result = json.decode(response.body);
        if (kDebugMode) {
          print(
          '✅ Berita berhasil dimuat: ${result['data']?.length ?? 0} item',
        );
        } // Debug

        // Debug gambar URLs
        if (result['data'] != null) {
          for (var berita in result['data']) {
            if (berita['gambar_url'] != null) {
              if (kDebugMode) {
                print('🖼️ Gambar URL: ${berita['gambar_url']}');
              }
            }
          }
        }

        return result;
      } else {
        if (kDebugMode) {
          print('🔴 Berita error: ${response.statusCode}');
        } // Debug
        return {'success': false, 'message': 'Gagal mengambil berita'};
      }
    } on SocketException {
      if (kDebugMode) {
        print('🔴 Berita SocketException');
      } // Debug
      return {
        'success': false,
        'message': 'Koneksi gagal, periksa internet Anda',
      };
    } catch (e) {
      if (kDebugMode) {
        print('🔴 Berita Error: $e');
      } // Debug
      return {'success': false, 'message': 'Error: ${e.toString()}'};
    }
  }

  /// GET DETAIL BERITA
  Future<Map<String, dynamic>> getDetailBerita(String idBerita) async {
    try {
      final url = Uri.parse('${AppConfig.baseUrl}/berita/$idBerita');
      if (kDebugMode) {
        print('🔵 GET DETAIL BERITA URL: $url');
      } // Debug

      final response = await http
          .get(url, headers: await _headers(needsAuth: true))
          .timeout(
            AppConfig.timeout,
            onTimeout: () {
              throw TimeoutException('Timeout saat mengambil detail berita');
            },
          );

      if (kDebugMode) {
        print(
        '🔵 Detail Berita Response Status: ${response.statusCode}',
      );
      } // Debug
      if (kDebugMode) {
        print('🔵 Detail Berita Response Body: ${response.body}');
      } // Debug

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        if (kDebugMode) {
          print('🔴 Detail Berita error: ${response.statusCode}');
        } // Debug
        return {'success': false, 'message': 'Gagal mengambil detail berita'};
      }
    } on SocketException {
      if (kDebugMode) {
        print('🔴 Detail Berita SocketException');
      } // Debug
      return {
        'success': false,
        'message': 'Koneksi gagal, periksa internet Anda',
      };
    } catch (e) {
      if (kDebugMode) {
        print('🔴 Detail Berita Error: $e');
      } // Debug
      return {'success': false, 'message': 'Error: ${e.toString()}'};
    }
  }

  /// GET RIWAYAT KESEHATAN
  Future<Map<String, dynamic>> getRiwayatKesehatan({
    int page = 1,
    String? status,
  }) async {
    try {
      final queryParams = <String, String>{'page': page.toString()};
      if (status != null && status != 'semua') {
        queryParams['status'] = status;
      }

      final url =
          queryParams.isEmpty
              ? '${AppConfig.baseUrl}/kesehatan'
              : '${AppConfig.baseUrl}/kesehatan?${queryParams.entries.map((e) => '${e.key}=${e.value}').join('&')}';

      final response = await http
          .get(Uri.parse(url), headers: await _headers(needsAuth: true))
          .timeout(
            AppConfig.timeout,
            onTimeout: () {
              throw TimeoutException(
                'Timeout saat mengambil riwayat kesehatan',
              );
            },
          );

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        return {
          'success': false,
          'message': 'Gagal mengambil riwayat kesehatan',
        };
      }
    } on SocketException {
      return {
        'success': false,
        'message': 'Koneksi gagal, periksa internet Anda',
      };
    } catch (e) {
      return {'success': false, 'message': 'Error: ${e.toString()}'};
    }
  }

  /// GET DETAIL KESEHATAN
  Future<Map<String, dynamic>> getDetailKesehatan(String idKesehatan) async {
    try {
      final response = await http
          .get(
            Uri.parse('${AppConfig.baseUrl}/kesehatan/$idKesehatan'),
            headers: await _headers(needsAuth: true),
          )
          .timeout(
            AppConfig.timeout,
            onTimeout: () {
              throw TimeoutException('Timeout saat mengambil detail kesehatan');
            },
          );

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        return {
          'success': false,
          'message': 'Gagal mengambil detail kesehatan',
        };
      }
    } on SocketException {
      return {
        'success': false,
        'message': 'Koneksi gagal, periksa internet Anda',
      };
    } catch (e) {
      return {'success': false, 'message': 'Error: ${e.toString()}'};
    }
  }

  /// GET STATISTIK KESEHATAN
  Future<Map<String, dynamic>> getStatistikKesehatan() async {
    try {
      final response = await http
          .get(
            Uri.parse('${AppConfig.baseUrl}/kesehatan/statistik'),
            headers: await _headers(needsAuth: true),
          )
          .timeout(
            AppConfig.timeout,
            onTimeout: () {
              throw TimeoutException('Timeout');
            },
          );

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        return {'success': false};
      }
    } catch (e) {
      return {'success': false};
    }
  }

  /// GET STATUS SPP BULAN INI
  Future<Map<String, dynamic>> getStatusSppBulanIni() async {
    try {
      final response = await http
          .get(
            Uri.parse('${AppConfig.baseUrl}/spp/status-bulan-ini'),
            headers: await _headers(needsAuth: true),
          )
          .timeout(AppConfig.timeout);

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        return {'success': false};
      }
    } catch (e) {
      return {'success': false};
    }
  }

  /// GET TUNGGAKAN SPP
  Future<Map<String, dynamic>> getTunggakanSpp() async {
    try {
      final response = await http
          .get(
            Uri.parse('${AppConfig.baseUrl}/spp/tunggakan'),
            headers: await _headers(needsAuth: true),
          )
          .timeout(AppConfig.timeout);

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        return {'success': false};
      }
    } catch (e) {
      return {'success': false};
    }
  }

  /// GET RIWAYAT SPP
  Future<Map<String, dynamic>> getRiwayatSpp({
    int page = 1,
    String? status,
  }) async {
    try {
      final queryParams = <String, String>{'page': page.toString()};
      if (status != null && status != 'semua') {
        queryParams['status'] = status;
      }

      final url =
          '${AppConfig.baseUrl}/spp/riwayat?${queryParams.entries.map((e) => '${e.key}=${e.value}').join('&')}';

      final response = await http
          .get(Uri.parse(url), headers: await _headers(needsAuth: true))
          .timeout(AppConfig.timeout);

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        return {'success': false, 'message': 'Gagal mengambil riwayat SPP'};
      }
    } on SocketException {
      return {
        'success': false,
        'message': 'Koneksi gagal, periksa internet Anda',
      };
    } catch (e) {
      return {'success': false, 'message': 'Error: ${e.toString()}'};
    }
  }

  /// GET STATISTIK SPP
  Future<Map<String, dynamic>> getStatistikSpp() async {
    try {
      final response = await http
          .get(
            Uri.parse('${AppConfig.baseUrl}/spp/statistik'),
            headers: await _headers(needsAuth: true),
          )
          .timeout(AppConfig.timeout);

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        return {'success': false};
      }
    } catch (e) {
      return {'success': false};
    }
  }

  /// GET LIST KEPULANGAN
  Future<Map<String, dynamic>> getKepulangan({
    int page = 1,
    String? status,
    String? tahun,
  }) async {
    try {
      // Build query params
      final queryParams = <String, String>{'page': page.toString()};
      if (status != null && status.isNotEmpty) queryParams['status'] = status;
      if (tahun != null && tahun.isNotEmpty) queryParams['tahun'] = tahun;

      final url = Uri.parse(
        '${AppConfig.baseUrl}/kepulangan',
      ).replace(queryParameters: queryParams);

      final response = await http
          .get(url, headers: await _headers(needsAuth: true))
          .timeout(AppConfig.timeout);

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        return {'success': false};
      }
    } catch (e) {
      return {'success': false, 'message': 'Error: ${e.toString()}'};
    }
  }

  /// GET DETAIL KEPULANGAN
  Future<Map<String, dynamic>> getDetailKepulangan(String idKepulangan) async {
    try {
      final url = Uri.parse('${AppConfig.baseUrl}/kepulangan/$idKepulangan');

      final response = await http
          .get(url, headers: await _headers(needsAuth: true))
          .timeout(AppConfig.timeout);

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        return {'success': false};
      }
    } catch (e) {
      return {'success': false, 'message': 'Error: ${e.toString()}'};
    }
  }

  /// GET KUOTA KEPULANGAN
  Future<Map<String, dynamic>> getKuotaKepulangan() async {
    try {
      final url = Uri.parse('${AppConfig.baseUrl}/kepulangan/kuota');

      final response = await http
          .get(url, headers: await _headers(needsAuth: true))
          .timeout(AppConfig.timeout);

      if (response.statusCode == 200) {
        return json.decode(response.body);
      } else {
        return {'success': false};
      }
    } catch (e) {
      return {'success': false, 'message': 'Error: ${e.toString()}'};
    }
  }

  // Tambahkan di akhir class ApiService, sebelum closing bracket

/// ==========================================
/// PELANGGARAN API METHODS
/// ==========================================

/// GET KLASIFIKASI PELANGGARAN (Public)
Future<Map<String, dynamic>> getKlasifikasiPelanggaran() async {
  try {
    final response = await http
        .get(
          Uri.parse('${AppConfig.baseUrl}/pelanggaran/klasifikasi'),
          headers: await _headers(needsAuth: true),
        )
        .timeout(AppConfig.timeout);

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      return {'success': false, 'message': 'Gagal mengambil klasifikasi'};
    }
  } on SocketException {
    return {
      'success': false,
      'message': 'Koneksi gagal, periksa internet Anda',
    };
  } catch (e) {
    return {'success': false, 'message': 'Error: ${e.toString()}'};
  }
}

/// GET KATEGORI PELANGGARAN (Public)
Future<Map<String, dynamic>> getKategoriPelanggaran({
  String? idKlasifikasi,
}) async {
  try {
    var url = '${AppConfig.baseUrl}/pelanggaran/kategori';
    if (idKlasifikasi != null && idKlasifikasi.isNotEmpty) {
      url += '?id_klasifikasi=$idKlasifikasi';
    }

    final response = await http
        .get(
          Uri.parse(url),
          headers: await _headers(needsAuth: true),
        )
        .timeout(AppConfig.timeout);

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      return {'success': false, 'message': 'Gagal mengambil kategori'};
    }
  } on SocketException {
    return {
      'success': false,
      'message': 'Koneksi gagal, periksa internet Anda',
    };
  } catch (e) {
    return {'success': false, 'message': 'Error: ${e.toString()}'};
  }
}

/// GET PEMBINAAN & SANKSI (Public)
Future<Map<String, dynamic>> getPembinaanSanksi() async {
  try {
    final response = await http
        .get(
          Uri.parse('${AppConfig.baseUrl}/pelanggaran/pembinaan-sanksi'),
          headers: await _headers(needsAuth: true),
        )
        .timeout(AppConfig.timeout);

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      return {'success': false, 'message': 'Gagal mengambil pembinaan'};
    }
  } on SocketException {
    return {
      'success': false,
      'message': 'Koneksi gagal, periksa internet Anda',
    };
  } catch (e) {
    return {'success': false, 'message': 'Error: ${e.toString()}'};
  }
}

/// GET RIWAYAT PELANGGARAN SANTRI (Private - Only Published)
Future<Map<String, dynamic>> getRiwayatPelanggaran({
  int page = 1,
  String? statusKafaroh,
  String? tanggalDari,
  String? tanggalSampai,
}) async {
  try {
    final queryParams = <String, String>{'page': page.toString()};
    
    if (statusKafaroh != null && statusKafaroh.isNotEmpty) {
      queryParams['status_kafaroh'] = statusKafaroh;
    }
    if (tanggalDari != null) queryParams['tanggal_dari'] = tanggalDari;
    if (tanggalSampai != null) queryParams['tanggal_sampai'] = tanggalSampai;

    final url =
        '${AppConfig.baseUrl}/pelanggaran/riwayat?${queryParams.entries.map((e) => '${e.key}=${e.value}').join('&')}';

    final response = await http
        .get(Uri.parse(url), headers: await _headers(needsAuth: true))
        .timeout(AppConfig.timeout);

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      return {
        'success': false,
        'message': 'Gagal mengambil riwayat pelanggaran',
      };
    }
  } on SocketException {
    return {
      'success': false,
      'message': 'Koneksi gagal, periksa internet Anda',
    };
  } catch (e) {
    return {'success': false, 'message': 'Error: ${e.toString()}'};
  }
}

/// GET DETAIL RIWAYAT PELANGGARAN
Future<Map<String, dynamic>> getDetailRiwayatPelanggaran(
  String idRiwayat,
) async {
  try {
    final response = await http
        .get(
          Uri.parse('${AppConfig.baseUrl}/pelanggaran/riwayat/$idRiwayat'),
          headers: await _headers(needsAuth: true),
        )
        .timeout(AppConfig.timeout);

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      return {
        'success': false,
        'message': 'Gagal mengambil detail pelanggaran',
      };
    }
  } on SocketException {
    return {
      'success': false,
      'message': 'Koneksi gagal, periksa internet Anda',
    };
  } catch (e) {
    return {'success': false, 'message': 'Error: ${e.toString()}'};
  }
}

/// GET STATISTIK PELANGGARAN
Future<Map<String, dynamic>> getStatistikPelanggaran() async {
  try {
    final response = await http
        .get(
          Uri.parse('${AppConfig.baseUrl}/pelanggaran/statistik'),
          headers: await _headers(needsAuth: true),
        )
        .timeout(AppConfig.timeout);

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      return {'success': false};
    }
  } catch (e) {
    return {'success': false};
  }
}

/// ==========================================
/// CAPAIAN SANTRI API METHODS
/// ==========================================

/// GET OVERVIEW CAPAIAN
Future<Map<String, dynamic>> getCapaianOverview({String? idSemester}) async {
  try {
    var url = '${AppConfig.baseUrl}/capaian/overview';
    if (idSemester != null && idSemester.isNotEmpty) {
      url += '?id_semester=$idSemester';
    }

    final response = await http
        .get(Uri.parse(url), headers: await _headers(needsAuth: true))
        .timeout(AppConfig.timeout);

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      return {'success': false, 'message': 'Gagal mengambil data capaian'};
    }
  } on SocketException {
    return {
      'success': false,
      'message': 'Koneksi gagal, periksa internet Anda',
    };
  } catch (e) {
    return {'success': false, 'message': 'Error: ${e.toString()}'};
  }
}

/// GET CAPAIAN DASHBOARD (COMPREHENSIVE)
Future<Map<String, dynamic>> getCapaianDashboard({String? idSemester}) async {
  try {
    var url = '${AppConfig.baseUrl}/capaian/dashboard';
    if (idSemester != null && idSemester.isNotEmpty) {
      url += '?id_semester=$idSemester';
    }

    final response = await http
        .get(Uri.parse(url), headers: await _headers(needsAuth: true))
        .timeout(AppConfig.timeout);

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      return {'success': false, 'message': 'Gagal mengambil data dashboard capaian'};
    }
  } on SocketException {
    return {
      'success': false,
      'message': 'Koneksi gagal, periksa internet Anda',
    };
  } catch (e) {
    return {'success': false, 'message': 'Error: ${e.toString()}'};
  }
}

/// GET LIST MATERI PER KATEGORI
Future<Map<String, dynamic>> getMateriByKategori(
  String kategori, {
  String? idSemester,
}) async {
  try {
    var url = '${AppConfig.baseUrl}/capaian/kategori/$kategori';
    if (idSemester != null && idSemester.isNotEmpty) {
      url += '?id_semester=$idSemester';
    }

    final response = await http
        .get(Uri.parse(url), headers: await _headers(needsAuth: true))
        .timeout(AppConfig.timeout);

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      return {'success': false, 'message': 'Gagal mengambil data materi'};
    }
  } on SocketException {
    return {
      'success': false,
      'message': 'Koneksi gagal, periksa internet Anda',
    };
  } catch (e) {
    return {'success': false, 'message': 'Error: ${e.toString()}'};
  }
}

/// GET DETAIL CAPAIAN
Future<Map<String, dynamic>> getDetailCapaian(String idCapaian) async {
  try {
    final response = await http
        .get(
          Uri.parse('${AppConfig.baseUrl}/capaian/detail/$idCapaian'),
          headers: await _headers(needsAuth: true),
        )
        .timeout(AppConfig.timeout);

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      return {'success': false, 'message': 'Gagal mengambil detail capaian'};
    }
  } on SocketException {
    return {
      'success': false,
      'message': 'Koneksi gagal, periksa internet Anda',
    };
  } catch (e) {
    return {'success': false, 'message': 'Error: ${e.toString()}'};
  }
}

/// GET GRAFIK PROGRESS
Future<Map<String, dynamic>> getGrafikProgress() async {
  try {
    final response = await http
        .get(
          Uri.parse('${AppConfig.baseUrl}/capaian/grafik-progress'),
          headers: await _headers(needsAuth: true),
        )
        .timeout(AppConfig.timeout);

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      return {'success': false};
    }
  } catch (e) {
    return {'success': false};
  }
}

/// GET TREND SEMESTER (progress per semester for line chart)
Future<Map<String, dynamic>> getCapaianTrendSemester() async {
  try {
    final response = await http
        .get(
          Uri.parse('${AppConfig.baseUrl}/capaian/trend-semester'),
          headers: await _headers(needsAuth: true),
        )
        .timeout(AppConfig.timeout);

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      return {'success': false, 'message': 'Gagal mengambil data trend semester'};
    }
  } on SocketException {
    return {
      'success': false,
      'message': 'Koneksi gagal, periksa internet Anda',
    };
  } catch (e) {
    return {'success': false, 'message': 'Error: ${e.toString()}'};
  }
}

/// ==========================================
/// ABSENSI KEGIATAN API METHODS
/// ==========================================

/// GET ABSENSI HARI INI
Future<Map<String, dynamic>> getAbsensiToday({String? tanggal}) async {
  try {
    var url = '${AppConfig.baseUrl}/absensi/today';
    if (tanggal != null && tanggal.isNotEmpty) {
      url += '?tanggal=$tanggal';
    }

    final response = await http
        .get(Uri.parse(url), headers: await _headers(needsAuth: true))
        .timeout(AppConfig.timeout);

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      return {'success': false, 'message': 'Gagal mengambil data absensi'};
    }
  } on SocketException {
    return {
      'success': false,
      'message': 'Koneksi gagal, periksa internet Anda',
    };
  } catch (e) {
    return {'success': false, 'message': 'Error: ${e.toString()}'};
  }
}

/// GET ABSENSI MINGGU INI
Future<Map<String, dynamic>> getAbsensiWeek() async {
  try {
    final response = await http
        .get(
          Uri.parse('${AppConfig.baseUrl}/absensi/week'),
          headers: await _headers(needsAuth: true),
        )
        .timeout(AppConfig.timeout);

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      return {'success': false, 'message': 'Gagal mengambil data minggu'};
    }
  } on SocketException {
    return {
      'success': false,
      'message': 'Koneksi gagal, periksa internet Anda',
    };
  } catch (e) {
    return {'success': false, 'message': 'Error: ${e.toString()}'};
  }
}

/// GET ABSENSI BULAN
Future<Map<String, dynamic>> getAbsensiMonth({String? bulan}) async {
  try {
    var url = '${AppConfig.baseUrl}/absensi/month';
    if (bulan != null && bulan.isNotEmpty) {
      url += '?bulan=$bulan';
    }

    final response = await http
        .get(Uri.parse(url), headers: await _headers(needsAuth: true))
        .timeout(AppConfig.timeout);

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      return {'success': false, 'message': 'Gagal mengambil data bulan'};
    }
  } on SocketException {
    return {
      'success': false,
      'message': 'Koneksi gagal, periksa internet Anda',
    };
  } catch (e) {
    return {'success': false, 'message': 'Error: ${e.toString()}'};
  }
}
}
