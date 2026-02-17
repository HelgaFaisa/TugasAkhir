// lib/features/absensi/models/absensi_kegiatan_model.dart

class AbsensiKegiatan {
  final String absensiId;
  final String kegiatanId;
  final String namaKegiatan;
  final KategoriKegiatan kategori;
  final String waktuMulai;
  final String waktuSelesai;
  final String status;
  final String? waktuAbsen;
  final String metodeAbsen;
  final String? punctuality;
  final String? keterangan;

  AbsensiKegiatan({
    required this.absensiId,
    required this.kegiatanId,
    required this.namaKegiatan,
    required this.kategori,
    required this.waktuMulai,
    required this.waktuSelesai,
    required this.status,
    this.waktuAbsen,
    required this.metodeAbsen,
    this.punctuality,
    this.keterangan,
  });

  factory AbsensiKegiatan.fromJson(Map<String, dynamic> json) {
    return AbsensiKegiatan(
      absensiId: json['absensi_id'] ?? '',
      kegiatanId: json['kegiatan_id'] ?? '',
      namaKegiatan: json['nama_kegiatan'] ?? '',
      kategori: KategoriKegiatan.fromJson(json['kategori'] ?? {}),
      waktuMulai: json['waktu_mulai'] ?? '',
      waktuSelesai: json['waktu_selesai'] ?? '',
      status: json['status'] ?? '',
      waktuAbsen: json['waktu_absen'],
      metodeAbsen: json['metode_absen'] ?? '',
      punctuality: json['punctuality'],
      keterangan: json['keterangan'],
    );
  }
}

class KategoriKegiatan {
  final String nama;
  final String icon;
  final String warna;

  KategoriKegiatan({
    required this.nama,
    required this.icon,
    required this.warna,
  });

  factory KategoriKegiatan.fromJson(Map<String, dynamic> json) {
    return KategoriKegiatan(
      nama: json['nama'] ?? '',
      icon: json['icon'] ?? 'fa-calendar',
      warna: json['warna'] ?? '#6FBAA5',
    );
  }
}