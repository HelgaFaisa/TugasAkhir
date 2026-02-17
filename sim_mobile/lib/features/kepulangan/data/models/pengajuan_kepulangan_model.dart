// lib/features/kepulangan/data/models/pengajuan_kepulangan_model.dart

class PengajuanKepulanganModel {
  final String? idPengajuan;
  final String idSantri;
  final String tanggalPulang;
  final String tanggalKembali;
  final int durasiIzin;
  final String alasan;
  final String status; // Menunggu, Disetujui, Ditolak
  final String? catatanReview;
  final String? reviewedAt;

  PengajuanKepulanganModel({
    this.idPengajuan,
    required this.idSantri,
    required this.tanggalPulang,
    required this.tanggalKembali,
    required this.durasiIzin,
    required this.alasan,
    this.status = 'Menunggu',
    this.catatanReview,
    this.reviewedAt,
  });

  factory PengajuanKepulanganModel.fromJson(Map<String, dynamic> json) {
    return PengajuanKepulanganModel(
      idPengajuan: json['id_pengajuan'],
      idSantri: json['id_santri'] ?? '',
      tanggalPulang: json['tanggal_pulang'] ?? '',
      tanggalKembali: json['tanggal_kembali'] ?? '',
      durasiIzin: _parseInt(json['durasi_izin']),
      alasan: json['alasan'] ?? '',
      status: json['status'] ?? 'Menunggu',
      catatanReview: json['catatan_review'],
      reviewedAt: json['reviewed_at'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id_santri': idSantri,
      'tanggal_pulang': tanggalPulang,
      'tanggal_kembali': tanggalKembali,
      'durasi_izin': durasiIzin,
      'alasan': alasan,
    };
  }

  static int _parseInt(dynamic value) {
    if (value == null) return 0;
    if (value is int) return value;
    if (value is String) return int.tryParse(value) ?? 0;
    return 0;
  }

  // Helper getters
  bool get isMenunggu => status == 'Menunggu';
  bool get isDisetujui => status == 'Disetujui';
  bool get isDitolak => status == 'Ditolak';
}