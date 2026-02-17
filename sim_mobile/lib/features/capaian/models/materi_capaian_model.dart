// lib/features/capaian/models/materi_capaian_model.dart

class MateriCapaian {
  final String idCapaian;
  final MateriInfo materi;
  final ProgressInfo progress;
  final String tanggalInput;

  MateriCapaian({
    required this.idCapaian,
    required this.materi,
    required this.progress,
    required this.tanggalInput,
  });

  factory MateriCapaian.fromJson(Map<String, dynamic> json) {
    return MateriCapaian(
      idCapaian: json['id_capaian'] ?? '',
      materi: MateriInfo.fromJson(json['materi']),
      progress: ProgressInfo.fromJson(json['progress']),
      tanggalInput: json['tanggal_input'] ?? '',
    );
  }
}

class MateriInfo {
  final String idMateri;
  final String namaKitab;
  final int totalHalaman;
  final int halamanMulai;
  final int halamanAkhir;
  final String? kategori;
  final String? kelas;
  final String? deskripsi;

  MateriInfo({
    required this.idMateri,
    required this.namaKitab,
    required this.totalHalaman,
    required this.halamanMulai,
    required this.halamanAkhir,
    this.kategori,
    this.kelas,
    this.deskripsi,
  });

  factory MateriInfo.fromJson(Map<String, dynamic> json) {
    return MateriInfo(
      idMateri: json['id_materi'] ?? '',
      namaKitab: json['nama_kitab'] ?? '',
      totalHalaman: json['total_halaman'] ?? 0,
      halamanMulai: json['halaman_mulai'] ?? 0,
      halamanAkhir: json['halaman_akhir'] ?? 0,
      kategori: json['kategori'],
      kelas: json['kelas'],
      deskripsi: json['deskripsi'],
    );
  }
}

class ProgressInfo {
  final int halamanSelesai;
  final double persentase;
  final String status;
  final String statusColor;

  ProgressInfo({
    required this.halamanSelesai,
    required this.persentase,
    required this.status,
    required this.statusColor,
  });

  factory ProgressInfo.fromJson(Map<String, dynamic> json) {
    return ProgressInfo(
      halamanSelesai: json['halaman_selesai'] ?? 0,
      persentase: (json['persentase'] ?? 0).toDouble(),
      status: json['status'] ?? 'Belum Mulai',
      statusColor: json['status_color'] ?? '#6B7280',
    );
  }
}

class DetailCapaian {
  final String idCapaian;
  final MateriInfo materi;
  final SemesterDetailInfo semester;
  final ProgressInfo progress;
  final BreakdownInfo breakdown;
  final String? catatan;
  final String tanggalInput;
  final String lastUpdated;

  DetailCapaian({
    required this.idCapaian,
    required this.materi,
    required this.semester,
    required this.progress,
    required this.breakdown,
    this.catatan,
    required this.tanggalInput,
    required this.lastUpdated,
  });

  factory DetailCapaian.fromJson(Map<String, dynamic> json) {
    return DetailCapaian(
      idCapaian: json['id_capaian'] ?? '',
      materi: MateriInfo.fromJson(json['materi']),
      semester: SemesterDetailInfo.fromJson(json['semester']),
      progress: ProgressInfo.fromJson(json['progress']),
      breakdown: BreakdownInfo.fromJson(json['breakdown']),
      catatan: json['catatan'],
      tanggalInput: json['tanggal_input'] ?? '',
      lastUpdated: json['last_updated'] ?? '',
    );
  }
}

class SemesterDetailInfo {
  final String idSemester;
  final String namaSemester;

  SemesterDetailInfo({
    required this.idSemester,
    required this.namaSemester,
  });

  factory SemesterDetailInfo.fromJson(Map<String, dynamic> json) {
    return SemesterDetailInfo(
      idSemester: json['id_semester'] ?? '',
      namaSemester: json['nama_semester'] ?? '',
    );
  }
}

class BreakdownInfo {
  final List<int> halamanSelesaiList;
  final int jumlahHalamanSelesai;
  final int halamanBelumSelesai;
  final String halamanSelesaiText;

  BreakdownInfo({
    required this.halamanSelesaiList,
    required this.jumlahHalamanSelesai,
    required this.halamanBelumSelesai,
    required this.halamanSelesaiText,
  });

  factory BreakdownInfo.fromJson(Map<String, dynamic> json) {
    return BreakdownInfo(
      halamanSelesaiList: (json['halaman_selesai_list'] as List? ?? [])
          .map((h) => h as int)
          .toList(),
      jumlahHalamanSelesai: json['jumlah_halaman_selesai'] ?? 0,
      halamanBelumSelesai: json['halaman_belum_selesai'] ?? 0,
      halamanSelesaiText: json['halaman_selesai_text'] ?? '',
    );
  }
}