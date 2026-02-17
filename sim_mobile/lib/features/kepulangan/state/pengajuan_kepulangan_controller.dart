// lib/features/kepulangan/state/pengajuan_kepulangan_controller.dart

import 'package:flutter/material.dart';
import '../data/services/pengajuan_kepulangan_service.dart';
import '../data/models/kepulangan_model.dart';

class PengajuanKepulanganController extends ChangeNotifier {
  final PengajuanKepulanganService _service = PengajuanKepulanganService();

  // Form fields
  DateTime? _tanggalPulang;
  DateTime? _tanggalKembali;
  String _alasan = '';

  // Calculated values
  int _durasiIzin = 0;
  int _totalSetelahIzin = 0;
  int _sisaKuotaSetelah = 0;
  bool _isOverLimit = false;
  String _warningMessage = '';

  // Loading states
  bool _isCalculating = false;
  bool _isSubmitting = false;

  // Getters
  DateTime? get tanggalPulang => _tanggalPulang;
  DateTime? get tanggalKembali => _tanggalKembali;
  String get alasan => _alasan;
  int get durasiIzin => _durasiIzin;
  int get totalSetelahIzin => _totalSetelahIzin;
  int get sisaKuotaSetelah => _sisaKuotaSetelah;
  bool get isOverLimit => _isOverLimit;
  String get warningMessage => _warningMessage;
  bool get isCalculating => _isCalculating;
  bool get isSubmitting => _isSubmitting;
  bool get canSubmit =>
      _tanggalPulang != null &&
      _tanggalKembali != null &&
      _alasan.trim().isNotEmpty &&
      !_isSubmitting;

  /// Set tanggal pulang
  void setTanggalPulang(DateTime date) {
    _tanggalPulang = date;
    
    // Auto-set tanggal kembali minimal H+1
    if (_tanggalKembali == null || _tanggalKembali!.isBefore(date.add(const Duration(days: 1)))) {
      _tanggalKembali = date.add(const Duration(days: 1));
    }
    
    _calculateDurasi();
    notifyListeners();
  }

  /// Set tanggal kembali
  void setTanggalKembali(DateTime date) {
    if (_tanggalPulang != null && date.isBefore(_tanggalPulang!.add(const Duration(days: 1)))) {
      return; // Validasi: tanggal kembali minimal H+1 dari pulang
    }
    
    _tanggalKembali = date;
    _calculateDurasi();
    notifyListeners();
  }

  /// Set alasan
  void setAlasan(String value) {
    _alasan = value;
    notifyListeners();
  }

  /// Calculate durasi & validasi kuota
  void _calculateDurasi() {
    if (_tanggalPulang == null || _tanggalKembali == null) {
      _durasiIzin = 0;
      return;
    }

    // Hitung durasi (termasuk hari pertama)
    final diff = _tanggalKembali!.difference(_tanggalPulang!).inDays + 1;
    _durasiIzin = diff;

    notifyListeners();
  }

  /// Validasi kuota dengan API (optional, bisa dipanggil manual)
  Future<void> validateKuota(KuotaInfo kuotaInfo) async {
    _isCalculating = true;
    notifyListeners();

    try {
      final currentUsage = kuotaInfo.totalTerpakai;
      final kuotaMaks = kuotaInfo.kuotaMaksimal;

      _totalSetelahIzin = currentUsage + _durasiIzin;
      _sisaKuotaSetelah = kuotaMaks - _totalSetelahIzin;

      if (_totalSetelahIzin > kuotaMaks) {
        _isOverLimit = true;
        final kelebihan = _totalSetelahIzin - kuotaMaks;
        _warningMessage =
            'Izin ini akan melebihi batas $kuotaMaks hari per tahun. Kelebihan: $kelebihan hari.';
      } else if (_totalSetelahIzin >= kuotaMaks * 0.8) {
        _isOverLimit = false;
        _warningMessage =
            'Kuota hampir habis! Sisa kuota setelah izin ini hanya $_sisaKuotaSetelah hari.';
      } else {
        _isOverLimit = false;
        _warningMessage = '';
      }
    } catch (e) {
      print('🔴 Validate Kuota Error: $e');
    } finally {
      _isCalculating = false;
      notifyListeners();
    }
  }

  /// Submit pengajuan
  Future<Map<String, dynamic>> submitPengajuan() async {
    if (!canSubmit) {
      return {
        'success': false,
        'message': 'Mohon lengkapi semua field',
      };
    }

    _isSubmitting = true;
    notifyListeners();

    try {
      final result = await _service.submitPengajuan(
        tanggalPulang: _formatDate(_tanggalPulang!),
        tanggalKembali: _formatDate(_tanggalKembali!),
        alasan: _alasan.trim(),
      );

      return result;
    } catch (e) {
      return {
        'success': false,
        'message': 'Error: ${e.toString()}',
      };
    } finally {
      _isSubmitting = false;
      notifyListeners();
    }
  }

  /// Reset form
  void reset() {
    _tanggalPulang = null;
    _tanggalKembali = null;
    _alasan = '';
    _durasiIzin = 0;
    _totalSetelahIzin = 0;
    _sisaKuotaSetelah = 0;
    _isOverLimit = false;
    _warningMessage = '';
    _isCalculating = false;
    _isSubmitting = false;
    notifyListeners();
  }

  /// Helper: Format date to YYYY-MM-DD
  String _formatDate(DateTime date) {
    return '${date.year}-${date.month.toString().padLeft(2, '0')}-${date.day.toString().padLeft(2, '0')}';
  }
}