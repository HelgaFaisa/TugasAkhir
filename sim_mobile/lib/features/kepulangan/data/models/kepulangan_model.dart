// lib/features/kepulangan/data/models/kepulangan_model.dart

class KepulanganModel {
  final String idKepulangan;
  final String tanggalIzin;
  final String tanggalIzinFormatted;
  final String tanggalPulang;
  final String tanggalPulangFormatted;
  final String tanggalKembali;
  final String tanggalKembaliFormatted;
  final int durasiIzin;
  final String alasan;
  final String status;
  final String? catatan;
  final String? approvedAt;
  final String? approvedAtFormatted;
  final bool isAktif;
  final bool isTerlambat;

  KepulanganModel({
    required this.idKepulangan,
    required this.tanggalIzin,
    required this.tanggalIzinFormatted,
    required this.tanggalPulang,
    required this.tanggalPulangFormatted,
    required this.tanggalKembali,
    required this.tanggalKembaliFormatted,
    required this.durasiIzin,
    required this.alasan,
    required this.status,
    this.catatan,
    this.approvedAt,
    this.approvedAtFormatted,
    required this.isAktif,
    required this.isTerlambat,
  });

  factory KepulanganModel.fromJson(Map<String, dynamic> json) {
    return KepulanganModel(
      idKepulangan: json['id_kepulangan'] ?? '',
      tanggalIzin: json['tanggal_izin'] ?? '',
      tanggalIzinFormatted: json['tanggal_izin_formatted'] ?? '',
      tanggalPulang: json['tanggal_pulang'] ?? '',
      tanggalPulangFormatted: json['tanggal_pulang_formatted'] ?? '',
      tanggalKembali: json['tanggal_kembali'] ?? '',
      tanggalKembaliFormatted: json['tanggal_kembali_formatted'] ?? '',
      durasiIzin: _parseInt(json['durasi_izin']),
      alasan: json['alasan'] ?? '',
      status: json['status'] ?? '',
      catatan: json['catatan'],
      approvedAt: json['approved_at'],
      approvedAtFormatted: json['approved_at_formatted'],
      isAktif: json['is_aktif'] ?? false,
      isTerlambat: json['is_terlambat'] ?? false,
    );
  }

  // Helper untuk parse int (handle String atau int dari API)
  static int _parseInt(dynamic value) {
    if (value == null) return 0;
    if (value is int) return value;
    if (value is String) return int.tryParse(value) ?? 0;
    return 0;
  }

  Map<String, dynamic> toJson() {
    return {
      'id_kepulangan': idKepulangan,
      'tanggal_izin': tanggalIzin,
      'tanggal_izin_formatted': tanggalIzinFormatted,
      'tanggal_pulang': tanggalPulang,
      'tanggal_pulang_formatted': tanggalPulangFormatted,
      'tanggal_kembali': tanggalKembali,
      'tanggal_kembali_formatted': tanggalKembaliFormatted,
      'durasi_izin': durasiIzin,
      'alasan': alasan,
      'status': status,
      'catatan': catatan,
      'approved_at': approvedAt,
      'approved_at_formatted': approvedAtFormatted,
      'is_aktif': isAktif,
      'is_terlambat': isTerlambat,
    };
  }
}

class KuotaInfo {
  final int kuotaMaksimal;
  final int totalTerpakai;
  final int sisaKuota;
  final double persentase;
  final String status; // aman, hampir_habis, melebihi
  final String badgeColor; // success, warning, danger
  final String periodeMulai;
  final String periodeAkhir;

  KuotaInfo({
    required this.kuotaMaksimal,
    required this.totalTerpakai,
    required this.sisaKuota,
    required this.persentase,
    required this.status,
    required this.badgeColor,
    required this.periodeMulai,
    required this.periodeAkhir,
  });

  factory KuotaInfo.fromJson(Map<String, dynamic> json) {
    return KuotaInfo(
      kuotaMaksimal: _parseInt(json['kuota_maksimal']),
      totalTerpakai: _parseInt(json['total_terpakai']),
      sisaKuota: _parseInt(json['sisa_kuota']),
      persentase: _parseDouble(json['persentase']),
      status: json['status'] ?? 'aman',
      badgeColor: json['badge_color'] ?? 'success',
      periodeMulai: _parseDate(json['periode_mulai']),
      periodeAkhir: _parseDate(json['periode_akhir']),
    );
  }

  // Helper untuk parse int (handle String atau int dari API)
  static int _parseInt(dynamic value) {
    if (value == null) return 0;
    if (value is int) return value;
    if (value is String) return int.tryParse(value) ?? 0;
    return 0;
  }

  // Helper untuk parse double (handle String, int, atau double dari API)
  static double _parseDouble(dynamic value) {
    if (value == null) return 0.0;
    if (value is double) return value;
    if (value is int) return value.toDouble();
    if (value is String) return double.tryParse(value) ?? 0.0;
    return 0.0;
  }

  // Helper untuk parse date (handle ISO 8601 atau date string)
  static String _parseDate(dynamic value) {
    if (value == null) return '';
    String dateStr = value.toString();
    // Jika format ISO 8601 (2026-01-01T00:00:00.000000Z), ambil bagian tanggal saja
    if (dateStr.contains('T')) {
      return dateStr.split('T')[0];
    }
    return dateStr;
  }

  bool get isOverLimit => status == 'melebihi';
  bool get isHampirHabis => status == 'hampir_habis';
  bool get isAman => status == 'aman';
}

class KepulanganPaginationModel {
  final int currentPage;
  final int lastPage;
  final int perPage;
  final int total;
  final int? from;
  final int? to;

  KepulanganPaginationModel({
    required this.currentPage,
    required this.lastPage,
    required this.perPage,
    required this.total,
    this.from,
    this.to,
  });

  factory KepulanganPaginationModel.fromJson(Map<String, dynamic> json) {
    return KepulanganPaginationModel(
      currentPage: _parseInt(json['current_page'], defaultValue: 1),
      lastPage: _parseInt(json['last_page'], defaultValue: 1),
      perPage: _parseInt(json['per_page'], defaultValue: 15),
      total: _parseInt(json['total'], defaultValue: 0),
      from: _parseIntNullable(json['from']),
      to: _parseIntNullable(json['to']),
    );
  }

  // Helper untuk parse int (handle String atau int dari API)
  static int _parseInt(dynamic value, {int defaultValue = 0}) {
    if (value == null) return defaultValue;
    if (value is int) return value;
    if (value is String) return int.tryParse(value) ?? defaultValue;
    return defaultValue;
  }

  // Helper untuk parse int nullable
  static int? _parseIntNullable(dynamic value) {
    if (value == null) return null;
    if (value is int) return value;
    if (value is String) return int.tryParse(value);
    return null;
  }

  bool get hasNextPage => currentPage < lastPage;
  bool get hasPreviousPage => currentPage > 1;
}
