// lib/features/pelanggaran/riwayat_pelanggaran_detail_page.dart

import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/api/api_service.dart';

class RiwayatPelanggaranDetailPage extends StatefulWidget {
  final String idRiwayat;

  const RiwayatPelanggaranDetailPage({
    super.key,
    required this.idRiwayat,
  });

  @override
  State<RiwayatPelanggaranDetailPage> createState() =>
      _RiwayatPelanggaranDetailPageState();
}

class _RiwayatPelanggaranDetailPageState
    extends State<RiwayatPelanggaranDetailPage> {
  final _api = ApiService();

  Map<String, dynamic>? _riwayat;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadDetail();
  }

  Future<void> _loadDetail() async {
    setState(() => _isLoading = true);

    final result = await _api.getDetailRiwayatPelanggaran(widget.idRiwayat);

    if (mounted) {
      setState(() => _isLoading = false);

      if (result['success'] == true && result['data'] != null) {
        setState(() {
          _riwayat = result['data'];
        });
      } else {
        _showSnackBar(
          result['message'] ?? 'Gagal memuat detail pelanggaran',
          isError: true,
        );
        Navigator.pop(context);
      }
    }
  }

  void _showSnackBar(String message, {bool isError = false}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: isError ? Colors.red : Colors.green,
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[100],
      appBar: AppBar(
        title: const Text('Detail Pelanggaran'),
        backgroundColor: const Color(0xFF7C3AED),
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _riwayat == null
              ? _buildErrorState()
              : RefreshIndicator(
                  onRefresh: _loadDetail,
                  child: SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    child: Column(
                      children: [
                        _buildHeader(),
                        _buildInfoSection(),
                        _buildKafarohSection(),
                        if (_riwayat!['keterangan'] != null)
                          _buildKeteranganSection(),
                        _buildPublishInfo(),
                        const SizedBox(height: 20),
                      ],
                    ),
                  ),
                ),
    );
  }

  Widget _buildHeader() {
    final idRiwayat = _riwayat!['id_riwayat'] ?? '';
    final isKafarohSelesai = _riwayat!['is_kafaroh_selesai'] ?? false;
    final poin = _riwayat!['poin'] ?? 0;
    final poinAsli = _riwayat!['poin_asli'] ?? 0;

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors: [
            const Color(0xFF7C3AED),
            const Color(0xFF7C3AED).withValues(alpha: 0.8),
          ],
        ),
      ),
      child: Column(
        children: [
          // ID Badge
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            decoration: BoxDecoration(
              color: Colors.white.withValues(alpha: 0.2),
              borderRadius: BorderRadius.circular(20),
            ),
            child: Text(
              idRiwayat,
              style: const TextStyle(
                color: Colors.white,
                fontSize: 14,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
          const SizedBox(height: 16),

          // Poin Display
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.1),
                  blurRadius: 10,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: Column(
              children: [
                const Text(
                  'Poin Pelanggaran',
                  style: TextStyle(
                    fontSize: 14,
                    color: Colors.grey,
                  ),
                ),
                const SizedBox(height: 8),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(Icons.star, color: Colors.red, size: 32),
                    const SizedBox(width: 8),
                    Text(
                      '$poin',
                      style: const TextStyle(
                        fontSize: 36,
                        fontWeight: FontWeight.bold,
                        color: Colors.red,
                      ),
                    ),
                  ],
                ),
                if (isKafarohSelesai && poinAsli != poin) ...[
                  const SizedBox(height: 4),
                  Text(
                    'Poin asli: $poinAsli (Dilebur)',
                    style: TextStyle(
                      fontSize: 12,
                      color: Colors.grey[600],
                      decoration: TextDecoration.lineThrough,
                    ),
                  ),
                ],
                const SizedBox(height: 12),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 12,
                    vertical: 6,
                  ),
                  decoration: BoxDecoration(
                    color: isKafarohSelesai
                        ? Colors.green[100]
                        : Colors.orange[100],
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(
                        isKafarohSelesai
                            ? Icons.check_circle
                            : Icons.pending,
                        size: 16,
                        color: isKafarohSelesai
                            ? Colors.green[800]
                            : Colors.orange[800],
                      ),
                      const SizedBox(width: 6),
                      Text(
                        isKafarohSelesai
                            ? 'Kafaroh Selesai'
                            : 'Kafaroh Belum Selesai',
                        style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.bold,
                          color: isKafarohSelesai
                              ? Colors.green[800]
                              : Colors.orange[800],
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoSection() {
    final kategori = _riwayat!['kategori'];
    final namaPelanggaran = kategori?['nama_pelanggaran'] ?? '-';
    final klasifikasi = kategori?['klasifikasi'];
    final namaKlasifikasi = klasifikasi?['nama_klasifikasi'] ?? '';
    
    final tanggal = _riwayat!['tanggal'] ?? '';
    String tanggalFormat = '-';
    try {
      final date = DateTime.parse(tanggal);
      tanggalFormat = DateFormat('EEEE, dd MMMM yyyy', 'id_ID').format(date);
    } catch (e) {
      // ignore
    }

    return Padding(
      padding: const EdgeInsets.all(16),
      child: Card(
        elevation: 2,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(12),
        ),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  const Icon(
                    Icons.info_outline,
                    color: Color(0xFF7C3AED),
                    size: 20,
                  ),
                  const SizedBox(width: 8),
                  const Text(
                    'Informasi Pelanggaran',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF7C3AED),
                    ),
                  ),
                ],
              ),
              const Divider(height: 24),
              _buildInfoRow('Pelanggaran', namaPelanggaran),
              const SizedBox(height: 12),
              _buildInfoRow('Klasifikasi', namaKlasifikasi),
              const SizedBox(height: 12),
              _buildInfoRow('Tanggal', tanggalFormat),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildKafarohSection() {
    final kategori = _riwayat!['kategori'];
    final kafaroh = kategori?['kafaroh'] ?? 'Tidak ada kafaroh';
    final isKafarohSelesai = _riwayat!['is_kafaroh_selesai'] ?? false;
    final tanggalSelesai = _riwayat!['tanggal_kafaroh_selesai'];
    final adminKafaroh = _riwayat!['admin_kafaroh'];
    final catatanKafaroh = _riwayat!['catatan_kafaroh'];

    String tanggalSelesaiFormat = '-';
    if (tanggalSelesai != null) {
      try {
        final date = DateTime.parse(tanggalSelesai);
        tanggalSelesaiFormat = DateFormat('dd MMMM yyyy HH:mm', 'id_ID').format(date);
      } catch (e) {
        // ignore
      }
    }

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Card(
        elevation: 2,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(12),
        ),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Icon(
                    Icons.info_outline,
                    color: isKafarohSelesai ? Colors.green : Colors.orange,
                    size: 20,
                  ),
                  const SizedBox(width: 8),
                  Text(
                    'Kafaroh / Taqorrub',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: isKafarohSelesai ? Colors.green : Colors.orange,
                    ),
                  ),
                ],
              ),
              const Divider(height: 24),
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.orange[50],
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(color: Colors.orange[200]!),
                ),
                child: Text(
                  kafaroh,
                  style: TextStyle(
                    fontSize: 13,
                    color: Colors.grey[800],
                    height: 1.5,
                  ),
                ),
              ),
              
              if (isKafarohSelesai) ...[
                const SizedBox(height: 16),
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.green[50],
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(color: Colors.green[200]!),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Icon(Icons.check_circle, size: 16, color: Colors.green[700]),
                          const SizedBox(width: 6),
                          Text(
                            'Kafaroh Telah Diselesaikan',
                            style: TextStyle(
                              fontSize: 13,
                              fontWeight: FontWeight.bold,
                              color: Colors.green[800],
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 8),
                      Text(
                        'Tanggal: $tanggalSelesaiFormat',
                        style: TextStyle(
                          fontSize: 12,
                          color: Colors.grey[700],
                        ),
                      ),
                      if (adminKafaroh != null) ...[
                        const SizedBox(height: 4),
                        Text(
                          'Oleh: ${adminKafaroh['name']}',
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey[700],
                          ),
                        ),
                      ],
                      if (catatanKafaroh != null && catatanKafaroh.toString().isNotEmpty) ...[
                        const SizedBox(height: 8),
                        const Divider(),
                        const SizedBox(height: 8),
                        Text(
                          'Catatan:',
                          style: TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.w600,
                            color: Colors.grey[600],
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          catatanKafaroh,
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey[700],
                            fontStyle: FontStyle.italic,
                          ),
                        ),
                      ],
                    ],
                  ),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildKeteranganSection() {
    final keterangan = _riwayat!['keterangan'];

    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 0),
      child: Card(
        elevation: 2,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(12),
        ),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Icon(
                    Icons.note_outlined,
                    color: Colors.grey[600],
                    size: 20,
                  ),
                  const SizedBox(width: 8),
                  Text(
                    'Keterangan',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: Colors.grey[800],
                    ),
                  ),
                ],
              ),
              const Divider(height: 24),
              Text(
                keterangan,
                style: TextStyle(
                  fontSize: 13,
                  color: Colors.grey[700],
                  height: 1.5,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildPublishInfo() {
    final tanggalPublished = _riwayat!['tanggal_published'];

    String tanggalPublishedFormat = '-';
    if (tanggalPublished != null) {
      try {
        final date = DateTime.parse(tanggalPublished);
        tanggalPublishedFormat = DateFormat('dd MMMM yyyy HH:mm', 'id_ID').format(date);
      } catch (e) {
        // ignore
      }
    }

    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 0),
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: Colors.blue[50],
          borderRadius: BorderRadius.circular(8),
          border: Border.all(color: Colors.blue[200]!),
        ),
        child: Row(
          children: [
            Icon(Icons.info, size: 16, color: Colors.blue[700]),
            const SizedBox(width: 8),
            Expanded(
              child: Text(
                'Dikirim ke wali santri: $tanggalPublishedFormat',
                style: TextStyle(
                  fontSize: 11,
                  color: Colors.blue[900],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(
          width: 110,
          child: Text(
            label,
            style: TextStyle(
              fontSize: 13,
              color: Colors.grey[600],
            ),
          ),
        ),
        const SizedBox(width: 8),
        Expanded(
          child: Text(
            value,
            style: const TextStyle(
              fontSize: 13,
              fontWeight: FontWeight.w600,
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildErrorState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(
            Icons.error_outline,
            size: 80,
            color: Colors.grey[400],
          ),
          const SizedBox(height: 16),
          Text(
            'Gagal memuat data',
            style: TextStyle(
              fontSize: 16,
              color: Colors.grey[600],
            ),
          ),
          const SizedBox(height: 16),
          ElevatedButton.icon(
            onPressed: _loadDetail,
            icon: const Icon(Icons.refresh),
            label: const Text('Coba Lagi'),
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF7C3AED),
              foregroundColor: Colors.white,
            ),
          ),
        ],
      ),
    );
  }
}