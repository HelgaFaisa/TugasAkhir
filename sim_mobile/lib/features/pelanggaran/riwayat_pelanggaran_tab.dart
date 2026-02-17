// lib/features/pelanggaran/riwayat_pelanggaran_tab.dart

import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/api/api_service.dart';
import 'riwayat_pelanggaran_detail_page.dart';

class RiwayatPelanggaranTab extends StatefulWidget {
  const RiwayatPelanggaranTab({super.key});

  @override
  State<RiwayatPelanggaranTab> createState() => _RiwayatPelanggaranTabState();
}

class _RiwayatPelanggaranTabState extends State<RiwayatPelanggaranTab> {
  final _api = ApiService();
  final _scrollController = ScrollController();

  List<Map<String, dynamic>> _riwayatList = [];
  Map<String, dynamic>? _statistik;
  
  bool _isLoading = true;
  bool _isLoadingMore = false;
  bool _hasMore = true;
  int _currentPage = 1;

  String _filterStatus = 'semua';

  @override
  void initState() {
    super.initState();
    _loadData();
    _scrollController.addListener(_onScroll);
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  void _onScroll() {
    if (_scrollController.position.pixels >=
        _scrollController.position.maxScrollExtent - 200) {
      if (!_isLoadingMore && _hasMore) {
        _loadMore();
      }
    }
  }

  Future<void> _loadData() async {
    setState(() {
      _isLoading = true;
      _currentPage = 1;
      _hasMore = true;
    });

    await Future.wait([
      _loadRiwayat(page: 1),
      _loadStatistik(),
    ]);

    if (mounted) {
      setState(() => _isLoading = false);
    }
  }

  Future<void> _loadRiwayat({int page = 1}) async {
    final statusKafaroh = _filterStatus == 'semua'
        ? null
        : _filterStatus == 'selesai'
            ? 'selesai'
            : 'belum';

    final result = await _api.getRiwayatPelanggaran(
      page: page,
      statusKafaroh: statusKafaroh,
    );

    if (mounted && result['success'] == true) {
      final newData = List<Map<String, dynamic>>.from(result['data'] ?? []);
      
      setState(() {
        if (page == 1) {
          _riwayatList = newData;
        } else {
          _riwayatList.addAll(newData);
        }
        
        _currentPage = result['current_page'] ?? 1;
        final lastPage = result['last_page'] ?? 1;
        _hasMore = _currentPage < lastPage;
      });
    }
  }

  Future<void> _loadStatistik() async {
    final result = await _api.getStatistikPelanggaran();

    if (mounted && result['success'] == true && result['data'] != null) {
      setState(() {
        _statistik = result['data'];
      });
    }
  }

  Future<void> _loadMore() async {
    if (_isLoadingMore || !_hasMore) return;

    setState(() => _isLoadingMore = true);
    await _loadRiwayat(page: _currentPage + 1);
    setState(() => _isLoadingMore = false);
  }

  void _changeFilter(String status) {
    if (_filterStatus != status) {
      setState(() => _filterStatus = status);
      _loadData();
    }
  }

  @override
  Widget build(BuildContext context) {
    return RefreshIndicator(
      onRefresh: _loadData,
      child: Column(
        children: [
          // Statistik Header
          if (_statistik != null) _buildStatistikHeader(),

          // Filter Chips
          _buildFilterChips(),

          const Divider(height: 1),

          // List Riwayat
          Expanded(
            child: _isLoading
                ? const Center(child: CircularProgressIndicator())
                : _riwayatList.isEmpty
                    ? _buildEmptyState()
                    : ListView.builder(
                        controller: _scrollController,
                        padding: const EdgeInsets.all(16),
                        itemCount: _riwayatList.length + (_isLoadingMore ? 1 : 0),
                        itemBuilder: (context, index) {
                          if (index == _riwayatList.length) {
                            return const Center(
                              child: Padding(
                                padding: EdgeInsets.all(16),
                                child: CircularProgressIndicator(),
                              ),
                            );
                          }
                          
                          final riwayat = _riwayatList[index];
                          return _buildRiwayatCard(riwayat);
                        },
                      ),
          ),
        ],
      ),
    );
  }

  // ✅ FIX: Responsive padding untuk statistik header
  Widget _buildStatistikHeader() {
    final total = _statistik?['total_pelanggaran'] ?? 0;
    final poin = _statistik?['total_poin'] ?? 0;
    final selesai = _statistik?['total_kafaroh_selesai'] ?? 0;
    final belum = _statistik?['total_kafaroh_belum'] ?? 0;

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 12), // ✅ Kurangi horizontal padding
      color: const Color(0xFF7C3AED).withValues(alpha: 0.05),
      child: Row(
        children: [
          Expanded(
            child: _buildStatItem(
              icon: Icons.warning_amber,
              label: 'Total',
              value: '$total',
              color: Colors.orange,
            ),
          ),
          Expanded(
            child: _buildStatItem(
              icon: Icons.star,
              label: 'Poin',
              value: '$poin',
              color: Colors.red,
            ),
          ),
          Expanded(
            child: _buildStatItem(
              icon: Icons.check_circle,
              label: 'Selesai',
              value: '$selesai',
              color: Colors.green,
            ),
          ),
          Expanded(
            child: _buildStatItem(
              icon: Icons.pending,
              label: 'Belum',
              value: '$belum',
              color: Colors.blue,
            ),
          ),
        ],
      ),
    );
  }

  // ✅ FIX: Kurangi padding dan ukuran font untuk fit di layar kecil
  Widget _buildStatItem({
    required IconData icon,
    required String label,
    required String value,
    required Color color,
  }) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 2), // ✅ Kurangi horizontal padding
      child: Column(
        mainAxisSize: MainAxisSize.min, // ✅ Tambahkan ini
        children: [
          Icon(icon, size: 18, color: color), // ✅ Kurangi dari 20 ke 18
          const SizedBox(height: 4),
          Text(
            value,
            style: TextStyle(
              fontSize: 15, // ✅ Kurangi dari 16 ke 15
              fontWeight: FontWeight.bold,
              color: color,
            ),
            maxLines: 1, // ✅ Tambahkan ini
            overflow: TextOverflow.ellipsis, // ✅ Tambahkan ini
          ),
          Text(
            label,
            style: TextStyle(
              fontSize: 10, // ✅ Kurangi dari 11 ke 10
              color: Colors.grey[600],
            ),
            maxLines: 1, // ✅ Tambahkan ini
            overflow: TextOverflow.ellipsis, // ✅ Tambahkan ini
          ),
        ],
      ),
    );
  }

  // ✅ FIX: Buat filter chips scrollable
  Widget _buildFilterChips() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 16),
      color: Colors.white,
      child: SingleChildScrollView( // ✅ Wrap dengan SingleChildScrollView
        scrollDirection: Axis.horizontal,
        child: Row(
          children: [
            _buildFilterChip('Semua', 'semua'),
            const SizedBox(width: 8),
            _buildFilterChip('Selesai', 'selesai'),
            const SizedBox(width: 8),
            _buildFilterChip('Belum Selesai', 'belum'),
          ],
        ),
      ),
    );
  }

  Widget _buildFilterChip(String label, String value) {
    final isSelected = _filterStatus == value;
    
    return FilterChip(
      label: Text(label),
      selected: isSelected,
      onSelected: (selected) {
        if (selected) _changeFilter(value);
      },
      selectedColor: const Color(0xFF7C3AED).withValues(alpha: 0.2),
      checkmarkColor: const Color(0xFF7C3AED),
    );
  }

  Widget _buildRiwayatCard(Map<String, dynamic> riwayat) {
    final idRiwayat = riwayat['id_riwayat'] ?? '';
    final tanggal = riwayat['tanggal'] ?? '';
    final poin = riwayat['poin'] ?? 0;
    final poinAsli = riwayat['poin_asli'] ?? 0;
    final keterangan = riwayat['keterangan'];
    final isKafarohSelesai = riwayat['is_kafaroh_selesai'] ?? false;
    
    final kategori = riwayat['kategori'];
    final namaPelanggaran = kategori?['nama_pelanggaran'] ?? '-';
    final klasifikasi = kategori?['klasifikasi'];
    final namaKlasifikasi = klasifikasi?['nama_klasifikasi'] ?? '';

    // Format tanggal
    String tanggalFormat = '-';
    try {
      final date = DateTime.parse(tanggal);
      tanggalFormat = DateFormat('dd MMM yyyy', 'id_ID').format(date);
    } catch (e) {
      // ignore
    }

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      elevation: 2,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
        side: BorderSide(
          color: isKafarohSelesai 
              ? Colors.green.withValues(alpha: 0.3) 
              : Colors.orange.withValues(alpha: 0.3),
          width: 1,
        ),
      ),
      child: InkWell(
        borderRadius: BorderRadius.circular(12),
        onTap: () => _showDetail(riwayat),
        child: Padding(
          padding: const EdgeInsets.all(14),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header: ID + Status Kafaroh
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Flexible( // ✅ Wrap dengan Flexible
                    child: Text(
                      idRiwayat,
                      style: TextStyle(
                        fontSize: 12,
                        color: Colors.grey[600],
                        fontWeight: FontWeight.w500,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                  const SizedBox(width: 8), // ✅ Tambahkan spacing
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 8,
                      vertical: 4,
                    ),
                    decoration: BoxDecoration(
                      color: isKafarohSelesai
                          ? Colors.green[100]
                          : Colors.orange[100],
                      borderRadius: BorderRadius.circular(6),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(
                          isKafarohSelesai
                              ? Icons.check_circle
                              : Icons.pending,
                          size: 12,
                          color: isKafarohSelesai
                              ? Colors.green[800]
                              : Colors.orange[800],
                        ),
                        const SizedBox(width: 4),
                        Text(
                          isKafarohSelesai ? 'Selesai' : 'Belum',
                          style: TextStyle(
                            fontSize: 10,
                            fontWeight: FontWeight.w600,
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
              const SizedBox(height: 10),

              // Nama Pelanggaran
              Text(
                namaPelanggaran,
                style: const TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.bold,
                ),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),

              // Klasifikasi
              if (namaKlasifikasi.isNotEmpty) ...[
                const SizedBox(height: 6),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
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

              const SizedBox(height: 10),

              // Info Row: Tanggal + Poin
              Row(
                children: [
                  Icon(Icons.calendar_today, size: 14, color: Colors.grey[600]),
                  const SizedBox(width: 4),
                  Flexible( // ✅ Wrap dengan Flexible
                    child: Text(
                      tanggalFormat,
                      style: TextStyle(
                        fontSize: 12,
                        color: Colors.grey[600],
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                  const SizedBox(width: 8), // ✅ Tambahkan spacing
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 8,
                      vertical: 4,
                    ),
                    decoration: BoxDecoration(
                      color: Colors.red[100],
                      borderRadius: BorderRadius.circular(6),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        const Icon(Icons.star, size: 12, color: Colors.red),
                        const SizedBox(width: 4),
                        Text(
                          '$poin',
                          style: const TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: Colors.red,
                          ),
                        ),
                        if (isKafarohSelesai && poinAsli != poin) ...[
                          Text(
                            ' ($poinAsli)',
                            style: TextStyle(
                              fontSize: 10,
                              color: Colors.grey[600],
                              decoration: TextDecoration.lineThrough,
                            ),
                          ),
                        ],
                      ],
                    ),
                  ),
                ],
              ),

              // Keterangan (jika ada)
              if (keterangan != null && keterangan.toString().isNotEmpty) ...[
                const SizedBox(height: 8),
                Text(
                  keterangan,
                  style: TextStyle(
                    fontSize: 12,
                    color: Colors.grey[600],
                    fontStyle: FontStyle.italic,
                  ),
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }

  void _showDetail(Map<String, dynamic> riwayat) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (context) => RiwayatPelanggaranDetailPage(
          idRiwayat: riwayat['id_riwayat'],
        ),
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(
            Icons.check_circle_outline,
            size: 80,
            color: Colors.grey[400],
          ),
          const SizedBox(height: 16),
          Text(
            _filterStatus == 'semua'
                ? 'Tidak ada riwayat pelanggaran'
                : _filterStatus == 'selesai'
                    ? 'Tidak ada kafaroh yang diselesaikan'
                    : 'Tidak ada kafaroh yang belum selesai',
            style: TextStyle(
              fontSize: 15,
              color: Colors.grey[600],
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 8),
          Text(
            'Tarik ke bawah untuk refresh',
            style: TextStyle(
              fontSize: 12,
              color: Colors.grey[500],
            ),
          ),
        ],
      ),
    );
  }
}