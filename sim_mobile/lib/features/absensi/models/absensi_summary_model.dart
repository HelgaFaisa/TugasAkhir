// lib/features/absensi/models/absensi_summary_model.dart

class AbsensiSummary {
  final int total;
  final int hadir;
  final int izin;
  final int sakit;
  final int alpa;
  final double percentage;

  AbsensiSummary({
    required this.total,
    required this.hadir,
    required this.izin,
    required this.sakit,
    required this.alpa,
    required this.percentage,
  });

  factory AbsensiSummary.fromJson(Map<String, dynamic> json) {
    return AbsensiSummary(
      total: _toInt(json['total']),
      hadir: _toInt(json['hadir']),
      izin: _toInt(json['izin']),
      sakit: _toInt(json['sakit']),
      alpa: _toInt(json['alpa']),
      percentage: _toDouble(json['percentage']),
    );
  }

  static int _toInt(dynamic value) {
    if (value == null) return 0;
    if (value is int) return value;
    if (value is double) return value.toInt();
    if (value is String) return int.tryParse(value) ?? 0;
    return 0;
  }

  static double _toDouble(dynamic value) {
    if (value == null) return 0.0;
    if (value is double) return value;
    if (value is int) return value.toDouble();
    if (value is String) return double.tryParse(value) ?? 0.0;
    return 0.0;
  }
}