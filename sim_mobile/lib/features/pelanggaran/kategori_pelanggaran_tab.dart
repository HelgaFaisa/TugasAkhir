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
              padding: const EdgeInsets.symmetric(vertical: 9, horizontal: 12),
              color: Colors.white,
              child: SingleChildScrollView(
                scrollDirection: Axis.horizontal,
                child: Row(
                  children: [
                    // Chip "Semua"
                    Padding(
                      padding: const EdgeInsets.only(right: 7),
                      child: FilterChip(
                        label: const Text('Semua'),
                        selected: _selectedKlasifikasi == null,
                        onSelected: (selected) {
                          if (selected) _filterByKlasifikasi(null);
                        },
                        selectedColor: const Color(0xFF6FBA9D).withValues(alpha: 0.2),
                        checkmarkColor: const Color(0xFF6FBA9D),
                      ),
                    ),
                    
                    // Chips per klasifikasi
                    ..._klasifikasiList.map((klasifikasi) {
                      final isSelected = _selectedKlasifikasi == klasifikasi['id_klasifikasi'];
                      
                      return Padding(
                        padding: const EdgeInsets.only(right: 7),
                        child: FilterChip(
                          label: Text(klasifikasi['nama_klasifikasi'] ?? ''),
                          selected: isSelected,
                          onSelected: (selected) {
                            if (selected) {
                              _filterByKlasifikasi(klasifikasi['id_klasifikasi']);
                            }
                          },
                          selectedColor: const Color(0xFF6FBA9D).withValues(alpha: 0.2),
                          checkmarkColor: const Color(0xFF6FBA9D),
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
                        padding: const EdgeInsets.all(12),
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
      margin: const EdgeInsets.only(bottom: 9),
      elevation: 2,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(9),
      ),
      child: InkWell(
        borderRadius: BorderRadius.circular(9),
        onTap: () => _showDetailDialog(kategori),
        child: Padding(
          padding: const EdgeInsets.all(11),
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
                            fontSize: 11,
                            fontWeight: FontWeight.bold,
                          ),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                        if (namaKlasifikasi.isNotEmpty) ...[
                          const SizedBox(height: 2),
                          Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: 7,
                              vertical: 2,
                            ),
                            decoration: BoxDecoration(
                              color: const Color(0xFF6FBA9D).withValues(alpha: 0.1),
                              borderRadius: BorderRadius.circular(2),
                            ),
                            child: Text(
                              namaKlasifikasi,
                              style: const TextStyle(
                                fontSize: 8,
                                color: Color(0xFF6FBA9D),
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ),
                        ],
                      ],
                    ),
                  ),
                  const SizedBox(width: 9),
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 8,
                      vertical: 5,
                    ),
                    decoration: BoxDecoration(
                      color: Colors.red[100],
                      borderRadius: BorderRadius.circular(7),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        const Icon(
                          Icons.star,
                          size: 11,
                          color: Colors.red,
                        ),
                        const SizedBox(width: 2),
                        Text(
                          '$poin',
                          style: const TextStyle(
                            fontSize: 11,
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
                const SizedBox(height: 8),
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: Colors.orange[50],
                    borderRadius: BorderRadius.circular(7),
                    border: Border.all(
                      color: Colors.orange[200]!,
                      width: 1,
                    ),
                  ),
                  child: Row(
                    children: [
                      Icon(
                        Icons.info_outline,
                        size: 12,
                        color: Colors.orange[700],
                      ),
                      const SizedBox(width: 7),
                      Expanded(
                        child: Text(
                          kafaroh,
                          style: TextStyle(
                            fontSize: 9,
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
              const SizedBox(height: 7),
              Text(
                'Tap untuk detail',
                style: TextStyle(
                  fontSize: 8,
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
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              namaPelanggaran,
              style: const TextStyle(fontSize: 13),
            ),
            if (namaKlasifikasi.isNotEmpty) ...[
              const SizedBox(height: 7),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                decoration: BoxDecoration(
                  color: const Color(0xFF6FBA9D).withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(5),
                ),
                child: Text(
                  namaKlasifikasi,
                  style: const TextStyle(
                    fontSize: 9,
                    color: Color(0xFF6FBA9D),
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
                  const SizedBox(width: 7),
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 9,
                      vertical: 2,
                    ),
                    decoration: BoxDecoration(
                      color: Colors.red[100],
                      borderRadius: BorderRadius.circular(7),
                    ),
                    child: Text(
                      '$poin Poin',
                      style: const TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                        color: Colors.red,
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),

              // Kafaroh
              const Text(
                'Kafaroh / Taqorrub:',
                style: TextStyle(
                  fontWeight: FontWeight.w600,
                  fontSize: 11,
                ),
              ),
              const SizedBox(height: 7),
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(9),
                decoration: BoxDecoration(
                  color: Colors.orange[50],
                  borderRadius: BorderRadius.circular(7),
                  border: Border.all(color: Colors.orange[200]!),
                ),
                child: Text(
                  kafaroh,
                  style: TextStyle(
                    fontSize: 11,
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
            size: 60,
            color: Colors.grey[400],
          ),
          const SizedBox(height: 12),
          Text(
            'Tidak ada data kategori pelanggaran',
            style: TextStyle(
              fontSize: 11,
              color: Colors.grey[600],
            ),
          ),
        ],
      ),
    );
  }
}