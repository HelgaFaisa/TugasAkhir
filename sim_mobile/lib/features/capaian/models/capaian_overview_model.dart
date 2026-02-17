// lib/features/capaian/models/capaian_overview_model.dart

class CapaianOverview {
  final SantriInfo santri;
  final SemesterInfo semester;
  final StatistikUmum statistikUmum;
  final List<KategoriCapaian> perKategori;

  CapaianOverview({
    required this.santri,
    required this.semester,
    required this.statistikUmum,
    required this.perKategori,
  });

  factory CapaianOverview.fromJson(Map<String, dynamic> json) {
    return CapaianOverview(
      santri: SantriInfo.fromJson(json['santri']),
      semester: SemesterInfo.fromJson(json['semester']),
      statistikUmum: StatistikUmum.fromJson(json['statistik_umum']),
      perKategori: (json['per_kategori'] as List)
          .map((k) => KategoriCapaian.fromJson(k))
          .toList(),
    );
  }
}

class SantriInfo {
  final String idSantri;
  final String namaLengkap;
  final String kelas;

  SantriInfo({
    required this.idSantri,
    required this.namaLengkap,
    required this.kelas,
  });

  factory SantriInfo.fromJson(Map<String, dynamic> json) {
    return SantriInfo(
      idSantri: json['id_santri'] ?? '',
      namaLengkap: json['nama_lengkap'] ?? '',
      kelas: json['kelas'] ?? '',
    );
  }
}

class SemesterInfo {
  final String? idSemester;
  final String namaSemester;
  final List<SemesterItem> listSemester;

  SemesterInfo({
    this.idSemester,
    required this.namaSemester,
    required this.listSemester,
  });

  factory SemesterInfo.fromJson(Map<String, dynamic> json) {
    return SemesterInfo(
      idSemester: json['id_semester'],
      namaSemester: json['nama_semester'] ?? 'Semua Semester',
      listSemester: (json['list_semester'] as List? ?? [])
          .map((s) => SemesterItem.fromJson(s))
          .toList(),
    );
  }
}

class SemesterItem {
  final String idSemester;
  final String namaSemester;
  final bool isAktif;

  SemesterItem({
    required this.idSemester,
    required this.namaSemester,
    required this.isAktif,
  });

  factory SemesterItem.fromJson(Map<String, dynamic> json) {
    return SemesterItem(
      idSemester: json['id_semester'] ?? '',
      namaSemester: json['nama_semester'] ?? '',
      isAktif: json['is_aktif'] ?? false,
    );
  }
}

class StatistikUmum {
  final int totalMateri;
  final double rataRataProgress;
  final int materiSelesai;

  StatistikUmum({
    required this.totalMateri,
    required this.rataRataProgress,
    required this.materiSelesai,
  });

  factory StatistikUmum.fromJson(Map<String, dynamic> json) {
    return StatistikUmum(
      totalMateri: json['total_materi'] ?? 0,
      rataRataProgress: (json['rata_rata_progress'] ?? 0).toDouble(),
      materiSelesai: json['materi_selesai'] ?? 0,
    );
  }
}

class KategoriCapaian {
  final String kategori;
  final String icon;
  final String color;
  final int totalMateri;
  final double rataRataProgress;
  final int materiSelesai;

  KategoriCapaian({
    required this.kategori,
    required this.icon,
    required this.color,
    required this.totalMateri,
    required this.rataRataProgress,
    required this.materiSelesai,
  });

  factory KategoriCapaian.fromJson(Map<String, dynamic> json) {
    return KategoriCapaian(
      kategori: json['kategori'] ?? '',
      icon: json['icon'] ?? 'book',
      color: json['color'] ?? '#6B7280',
      totalMateri: json['total_materi'] ?? 0,
      rataRataProgress: (json['rata_rata_progress'] ?? 0).toDouble(),
      materiSelesai: json['materi_selesai'] ?? 0,
    );
  }
}