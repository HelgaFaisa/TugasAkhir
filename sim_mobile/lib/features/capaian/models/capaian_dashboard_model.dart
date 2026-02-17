// lib/features/capaian/models/capaian_dashboard_model.dart

import 'kelas_info_model.dart';

class CapaianDashboard {
  final String role;
  final SantriInfo santri;
  final SemesterDetail semester;
  final List<SemesterOption> listSemester;
  final CurrentProgress currentProgress;
  final List<SemesterHistoryItem> semesterHistory;
  final List<AchievementItem> achievements;
  final List<MateriStatusItem> materiStatus;
  final List<PeerComparisonItem> peerComparison;
  final RaporSummary raporSummary;
  final RankInfo? rank;

  CapaianDashboard({
    required this.role,
    required this.santri,
    required this.semester,
    required this.listSemester,
    required this.currentProgress,
    required this.semesterHistory,
    required this.achievements,
    required this.materiStatus,
    required this.peerComparison,
    required this.raporSummary,
    this.rank,
  });

  factory CapaianDashboard.fromJson(Map<String, dynamic> json) {
    return CapaianDashboard(
      role: json['role'] ?? 'santri',
      santri: SantriInfo.fromJson(json['santri'] ?? {}),
      semester: SemesterDetail.fromJson(json['semester'] ?? {}),
      listSemester: (json['list_semester'] as List? ?? [])
          .map((s) => SemesterOption.fromJson(s))
          .toList(),
      currentProgress: CurrentProgress.fromJson(json['current_progress'] ?? {}),
      semesterHistory: (json['semester_history'] as List? ?? [])
          .map((s) => SemesterHistoryItem.fromJson(s))
          .toList(),
      achievements: (json['achievements'] as List? ?? [])
          .map((a) => AchievementItem.fromJson(a))
          .toList(),
      materiStatus: (json['materi_status'] as List? ?? [])
          .map((m) => MateriStatusItem.fromJson(m))
          .toList(),
      peerComparison: (json['peer_comparison'] as List? ?? [])
          .map((p) => PeerComparisonItem.fromJson(p))
          .toList(),
      raporSummary: RaporSummary.fromJson(json['rapor_summary'] ?? {}),
      rank: json['rank'] != null ? RankInfo.fromJson(json['rank']) : null,
    );
  }
}

class SantriInfo {
  final String idSantri;
  final String namaLengkap;
  final String kelas; // backward compatible string
  final KelasInfo? kelasPrimary;
  final List<KelasInfo> allKelas;

  SantriInfo({
    required this.idSantri,
    required this.namaLengkap,
    required this.kelas,
    this.kelasPrimary,
    this.allKelas = const [],
  });

  factory SantriInfo.fromJson(Map<String, dynamic> json) {
    return SantriInfo(
      idSantri: json['id_santri'] ?? '',
      namaLengkap: json['nama_lengkap'] ?? '',
      kelas: json['kelas'] ?? '',
      kelasPrimary: json['kelas_primary'] != null
          ? KelasInfo.fromJson(json['kelas_primary'])
          : null,
      allKelas: (json['all_kelas'] as List? ?? [])
          .map((k) => KelasInfo.fromJson(k))
          .toList(),
    );
  }

  /// Nama kelas untuk display (dari kelas_primary atau fallback ke string kelas)
  String get kelasDisplayName {
    if (kelasPrimary != null) {
      return kelasPrimary!.namaKelas;
    }
    return kelas.isNotEmpty ? kelas : 'Belum Ada Kelas';
  }

  /// Nama kelompok dari kelas primary
  String? get kelompokName => kelasPrimary?.kelompok;

  /// Apakah santri punya multiple kelas
  bool get hasMultipleKelas => allKelas.length > 1;

  /// Jumlah kelas lainnya (selain primary)
  int get kelasLainnyaCount => allKelas.length > 1 ? allKelas.length - 1 : 0;
}

class SemesterDetail {
  final String? idSemester;
  final String namaSemester;
  final String? tahunAjaran;

  SemesterDetail({this.idSemester, required this.namaSemester, this.tahunAjaran});

  factory SemesterDetail.fromJson(Map<String, dynamic> json) {
    return SemesterDetail(
      idSemester: json['id_semester'],
      namaSemester: json['nama_semester'] ?? '',
      tahunAjaran: json['tahun_ajaran'],
    );
  }
}

class SemesterOption {
  final String idSemester;
  final String namaSemester;
  final String? tahunAjaran;
  final int? periode;
  final bool isAktif;

  SemesterOption({
    required this.idSemester,
    required this.namaSemester,
    this.tahunAjaran,
    this.periode,
    required this.isAktif,
  });

  factory SemesterOption.fromJson(Map<String, dynamic> json) {
    return SemesterOption(
      idSemester: json['id_semester'] ?? '',
      namaSemester: json['nama_semester'] ?? '',
      tahunAjaran: json['tahun_ajaran'],
      periode: json['periode'],
      isAktif: json['is_aktif'] ?? false,
    );
  }
}

class CurrentProgress {
  final double totalProgress;
  final int totalMateri;
  final int materiSelesai;
  final List<KategoriProgress> perKategori;

  CurrentProgress({
    required this.totalProgress,
    required this.totalMateri,
    required this.materiSelesai,
    required this.perKategori,
  });

  factory CurrentProgress.fromJson(Map<String, dynamic> json) {
    return CurrentProgress(
      totalProgress: (json['total_progress'] ?? 0).toDouble(),
      totalMateri: json['total_materi'] ?? 0,
      materiSelesai: json['materi_selesai'] ?? 0,
      perKategori: (json['per_kategori'] as List? ?? [])
          .map((k) => KategoriProgress.fromJson(k))
          .toList(),
    );
  }
}

class KategoriProgress {
  final String kategori;
  final String icon;
  final String color;
  final int totalMateri;
  final double rataRataProgress;
  final int materiSelesai;

  KategoriProgress({
    required this.kategori,
    required this.icon,
    required this.color,
    required this.totalMateri,
    required this.rataRataProgress,
    required this.materiSelesai,
  });

  factory KategoriProgress.fromJson(Map<String, dynamic> json) {
    return KategoriProgress(
      kategori: json['kategori'] ?? '',
      icon: json['icon'] ?? 'book',
      color: json['color'] ?? '#6B7280',
      totalMateri: json['total_materi'] ?? 0,
      rataRataProgress: (json['rata_rata_progress'] ?? 0).toDouble(),
      materiSelesai: json['materi_selesai'] ?? 0,
    );
  }
}

class SemesterHistoryItem {
  final String idSemester;
  final String namaSemester;
  final double rataRataProgress;
  final int totalMateri;
  final int materiSelesai;
  final bool isCurrent;

  SemesterHistoryItem({
    required this.idSemester,
    required this.namaSemester,
    required this.rataRataProgress,
    required this.totalMateri,
    required this.materiSelesai,
    required this.isCurrent,
  });

  factory SemesterHistoryItem.fromJson(Map<String, dynamic> json) {
    return SemesterHistoryItem(
      idSemester: json['id_semester'] ?? '',
      namaSemester: json['nama_semester'] ?? '',
      rataRataProgress: (json['rata_rata_progress'] ?? 0).toDouble(),
      totalMateri: json['total_materi'] ?? 0,
      materiSelesai: json['materi_selesai'] ?? 0,
      isCurrent: json['is_current'] ?? false,
    );
  }
}

class AchievementItem {
  final String icon;
  final String text;
  final String type;

  AchievementItem({required this.icon, required this.text, required this.type});

  factory AchievementItem.fromJson(Map<String, dynamic> json) {
    return AchievementItem(
      icon: json['icon'] ?? '',
      text: json['text'] ?? '',
      type: json['type'] ?? '',
    );
  }
}

class MateriStatusItem {
  final String idCapaian;
  final String namaKitab;
  final String kategori;
  final double persentase;
  final String status; // selesai, progres, belum_mulai
  final String statusLabel;
  final String statusColor;
  final String icon;
  final String color;

  MateriStatusItem({
    required this.idCapaian,
    required this.namaKitab,
    required this.kategori,
    required this.persentase,
    required this.status,
    required this.statusLabel,
    required this.statusColor,
    required this.icon,
    required this.color,
  });

  factory MateriStatusItem.fromJson(Map<String, dynamic> json) {
    return MateriStatusItem(
      idCapaian: json['id_capaian'] ?? '',
      namaKitab: json['nama_kitab'] ?? '',
      kategori: json['kategori'] ?? '',
      persentase: (json['persentase'] ?? 0).toDouble(),
      status: json['status'] ?? 'belum_mulai',
      statusLabel: json['status_label'] ?? 'Belum Mulai',
      statusColor: json['status_color'] ?? '#6B7280',
      icon: json['icon'] ?? 'book',
      color: json['color'] ?? '#6B7280',
    );
  }
}

class PeerComparisonItem {
  final String kategori;
  final String icon;
  final String color;
  final double santriProgress;
  final double kelasAvg;

  PeerComparisonItem({
    required this.kategori,
    required this.icon,
    required this.color,
    required this.santriProgress,
    required this.kelasAvg,
  });

  factory PeerComparisonItem.fromJson(Map<String, dynamic> json) {
    return PeerComparisonItem(
      kategori: json['kategori'] ?? '',
      icon: json['icon'] ?? 'book',
      color: json['color'] ?? '#6B7280',
      santriProgress: (json['santri_progress'] ?? 0).toDouble(),
      kelasAvg: (json['kelas_avg'] ?? 0).toDouble(),
    );
  }
}

class RaporSummary {
  final double totalProgress;
  final int totalMateri;
  final int materiSelesai;
  final double perubahan;
  final String trend; // naik, turun, tetap
  final String predikat;

  RaporSummary({
    required this.totalProgress,
    required this.totalMateri,
    required this.materiSelesai,
    required this.perubahan,
    required this.trend,
    required this.predikat,
  });

  factory RaporSummary.fromJson(Map<String, dynamic> json) {
    return RaporSummary(
      totalProgress: (json['total_progress'] ?? 0).toDouble(),
      totalMateri: json['total_materi'] ?? 0,
      materiSelesai: json['materi_selesai'] ?? 0,
      perubahan: (json['perubahan'] ?? 0).toDouble(),
      trend: json['trend'] ?? 'tetap',
      predikat: json['predikat'] ?? 'Cukup',
    );
  }
}

class RankInfo {
  final int position;
  final int total;

  RankInfo({required this.position, required this.total});

  factory RankInfo.fromJson(Map<String, dynamic> json) {
    return RankInfo(
      position: json['position'] ?? 0,
      total: json['total'] ?? 0,
    );
  }
}
