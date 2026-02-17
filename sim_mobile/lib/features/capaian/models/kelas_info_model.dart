// lib/features/capaian/models/kelas_info_model.dart
// Model untuk data kelas santri dari sistem kelas baru (santri_kelas pivot)

class KelasInfo {
  final int? idKelas;
  final String? kodeKelas;
  final String namaKelas;
  final String? kelompok;
  final String? idKelompok;
  final String? tahunAjaran;
  final bool isPrimary;

  const KelasInfo({
    this.idKelas,
    this.kodeKelas,
    required this.namaKelas,
    this.kelompok,
    this.idKelompok,
    this.tahunAjaran,
    this.isPrimary = false,
  });

  factory KelasInfo.fromJson(Map<String, dynamic> json) {
    return KelasInfo(
      idKelas: json['id_kelas'],
      kodeKelas: json['kode_kelas'],
      namaKelas: json['nama_kelas'] ?? 'Belum Ada Kelas',
      kelompok: json['kelompok'],
      idKelompok: json['id_kelompok'],
      tahunAjaran: json['tahun_ajaran'],
      isPrimary: json['is_primary'] ?? false,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id_kelas': idKelas,
      'kode_kelas': kodeKelas,
      'nama_kelas': namaKelas,
      'kelompok': kelompok,
      'id_kelompok': idKelompok,
      'tahun_ajaran': tahunAjaran,
      'is_primary': isPrimary,
    };
  }

  /// Display string: "Kelompok: NamaKelas"
  String get displayName {
    if (kelompok != null && kelompok!.isNotEmpty) {
      return '$kelompok: $namaKelas';
    }
    return namaKelas;
  }

  /// Short display for badges
  String get shortName => namaKelas;
}
