// lib/features/pelanggaran/kategori_pelanggaran_tab.dart

import 'package:flutter/material.dart';
import '../../core/api/api_service.dart';

class KategoriPelanggaranTab extends StatefulWidget {
  const KategoriPelanggaranTab({super.key});

  @override
  State<KategoriPelanggaranTab> createState() => _KategoriPelanggaranTabState();
}

class _KategoriPelanggaranTabState extends State<KategoriPelanggaranTab> {
  final _api = ApiService();
  
  List<Map<String, dynamic>> _klasifikasiList = [];
  List<Map<String, dynamic>> _kategoriList = [];
  String? _selectedKlasifikasi;
  
  bool _isLoadingKategori = false;

  @override
  void initState() {
    super.initState();
    _loadKlasifikasi();
  }

  Future<void> _loadKlasifikasi() async {
    final result = await _api.getKlasifikasiPelanggaran();

    if (mounted) {
      if (result['success'] == true && result['data'] != null) {
        setState(() {
          _klasifikasiList = List<Map<String, dynamic>>.from(result['data']);
        });
        
        // Auto load semua kategori
        _loadKategori();
      }
    }
  }

  Future<void> _loadKategori({String? idKlasifikasi}) async {
    setState(() => _isLoadingKategori = true);

    final result = await _api.getKategoriPelanggaran(
      idKlasifikasi: idKlasifikasi,
    );

    if (mounted) {
      setState(() => _isLoadingKategori = false);
      
      if (result['success'] == true && result['data'] != null) {
        setState(() {
          _kategoriList = List<Map<String, dynamic>>.from(result['data']);
        });
      }
    }
  }

  void _filterByKlasifikasi(String? idKlasifikasi) {
    setState(() => _selectedKlasifikasi = idKlasifikasi);
    _loadKategori(idKlasifikasi: idKlasifikasi);
  }

  @override
  Widget build(BuildContext context) {
    return RefreshIndicator(
      onRefresh: () async {
        await _loadKlasifikasi();
      },
      child: Column(
        children: [
          // Filter Chips
          if (_klasifikasiList.isNotEmpty)
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 16),
              color: Colors.white,
              child: SingleChildScrollView(
                scrollDirection: Axis.horizontal,
                child: Row(
                  children: [
                    // Chip "Semua"
                    Padding(
                      padding: const EdgeInsets.only(right: 8),
                      child: FilterChip(
                        label: const Text('Semua'),
                        selected: _selectedKlasifikasi == null,
                        onSelected: (selected) {
                          if (selected) _filterByKlasifikasi(null);
                        },
                        selectedColor: const Color(0xFF7C3AED).withValues(alpha: 0.2),
                        checkmarkColor: const Color(0xFF7C3AED),
                      ),
                    ),
                    
                    // Chips per klasifikasi
                    ..._klasifikasiList.map((klasifikasi) {
                      final isSelected = _selectedKlasifikasi == klasifikasi['id_klasifikasi'];
                      
                      return Padding(
                        padding: const EdgeInsets.only(right: 8),
                        child: FilterChip(
                          label: Text(klasifikasi['nama_klasifikasi'] ?? ''),
                          selected: isSelected,
                          onSelected: (selected) {
                            if (selected) {
                              _filterByKlasifikasi(klasifikasi['id_klasifikasi']);
                            }
                          },
                          selectedColor: const Color(0xFF7C3AED).withValues(alpha: 0.2),
                          checkmarkColor: const Color(0xFF7C3AED),
                        ),
                      );
                    }),
                  ],
                ),
              ),
            ),

          const Divider(height: 1),

          // List Kategori
          Expanded(
            child: _isLoadingKategori
                ? const Center(child: CircularProgressIndicator())
                : _kategoriList.isEmpty
                    ? _buildEmptyState()
                    : ListView.builder(
                        padding: const EdgeInsets.all(16),
                        itemCount: _kategoriList.length,
                        itemBuilder: (context, index) {
                          final kategori = _kategoriList[index];
                          return _buildKategoriCard(kategori);
                        },
                      ),
          ),
        ],
      ),
    );
  }

  Widget _buildKategoriCard(Map<String, dynamic> kategori) {
    final namaPelanggaran = kategori['nama_pelanggaran'] ?? '-';
    final poin = kategori['poin'] ?? 0;
    final kafaroh = kategori['kafaroh'] ?? '-';
    final klasifikasi = kategori['klasifikasi'];
    final namaKlasifikasi = klasifikasi?['nama_klasifikasi'] ?? '';

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      elevation: 2,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
      ),
      child: InkWell(
        borderRadius: BorderRadius.circular(12),
        onTap: () => _showDetailDialog(kategori),
        child: Padding(
          padding: const EdgeInsets.all(14),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header: Nama + Poin
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          namaPelanggaran,
                          style: const TextStyle(
                            fontSize: 15,
                            fontWeight: FontWeight.bold,
                          ),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                        if (namaKlasifikasi.isNotEmpty) ...[
                          const SizedBox(height: 4),
                          Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: 8,
                              vertical: 3,
                            ),
                            decoration: BoxDecoration(
                              color: const Color(0xFF7C3AED).withValues(alpha: 0.1),
                              borderRadius: BorderRadius.circular(4),
                            ),
                            child: Text(
                              namaKlasifikasi,
                              style: const TextStyle(
                                fontSize: 11,
                                color: Color(0xFF7C3AED),
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ),
                        ],
                      ],
                    ),
                  ),
                  const SizedBox(width: 12),
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 10,
                      vertical: 6,
                    ),
                    decoration: BoxDecoration(
                      color: Colors.red[100],
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        const Icon(
                          Icons.star,
                          size: 14,
                          color: Colors.red,
                        ),
                        const SizedBox(width: 4),
                        Text(
                          '$poin',
                          style: const TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.bold,
                            color: Colors.red,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),

              // Kafaroh Preview
              if (kafaroh != '-') ...[
                const SizedBox(height: 10),
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: Colors.orange[50],
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(
                      color: Colors.orange[200]!,
                      width: 1,
                    ),
                  ),
                  child: Row(
                    children: [
                      Icon(
                        Icons.info_outline,
                        size: 16,
                        color: Colors.orange[700],
                      ),
                      const SizedBox(width: 8),
                      Expanded(
                        child: Text(
                          kafaroh,
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey[700],
                          ),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                ),
              ],

              // Tap untuk detail
              const SizedBox(height: 8),
              Text(
                'Tap untuk detail',
                style: TextStyle(
                  fontSize: 11,
                  color: Colors.grey[500],
                  fontStyle: FontStyle.italic,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _showDetailDialog(Map<String, dynamic> kategori) {
    final namaPelanggaran = kategori['nama_pelanggaran'] ?? '-';
    final poin = kategori['poin'] ?? 0;
    final kafaroh = kategori['kafaroh'] ?? 'Tidak ada kafaroh';
    final klasifikasi = kategori['klasifikasi'];
    final namaKlasifikasi = klasifikasi?['nama_klasifikasi'] ?? '';

    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              namaPelanggaran,
              style: const TextStyle(fontSize: 17),
            ),
            if (namaKlasifikasi.isNotEmpty) ...[
              const SizedBox(height: 8),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: const Color(0xFF7C3AED).withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(6),
                ),
                child: Text(
                  namaKlasifikasi,
                  style: const TextStyle(
                    fontSize: 12,
                    color: Color(0xFF7C3AED),
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
            ],
          ],
        ),
        content: SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              // Poin
              Row(
                children: [
                  const Text(
                    'Poin Pelanggaran:',
                    style: TextStyle(fontWeight: FontWeight.w600),
                  ),
                  const SizedBox(width: 8),
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 4,
                    ),
                    decoration: BoxDecoration(
                      color: Colors.red[100],
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(
                      '$poin Poin',
                      style: const TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.bold,
                        color: Colors.red,
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 16),

              // Kafaroh
              const Text(
                'Kafaroh / Taqorrub:',
                style: TextStyle(
                  fontWeight: FontWeight.w600,
                  fontSize: 14,
                ),
              ),
              const SizedBox(height: 8),
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
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Tutup'),
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(
            Icons.warning_amber_outlined,
            size: 80,
            color: Colors.grey[400],
          ),
          const SizedBox(height: 16),
          Text(
            'Tidak ada data kategori pelanggaran',
            style: TextStyle(
              fontSize: 15,
              color: Colors.grey[600],
            ),
          ),
        ],
      ),
    );
  }
}