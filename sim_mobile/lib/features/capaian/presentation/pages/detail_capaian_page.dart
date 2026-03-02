// lib/features/capaian/presentation/pages/detail_capaian_page.dart

import 'package:flutter/material.dart';
import '../../../../core/api/api_service.dart';
import '../../models/materi_capaian_model.dart';
import '../../models/kelas_info_model.dart';
import '../widgets/kelas_badge.dart';

class DetailCapaianPage extends StatefulWidget {
  final String idCapaian;
  final Color color;

  const DetailCapaianPage({
    super.key,
    required this.idCapaian,
    required this.color,
  });

  @override
  State<DetailCapaianPage> createState() => _DetailCapaianPageState();
}

class _DetailCapaianPageState extends State<DetailCapaianPage> {
  final _api = ApiService();
  
  DetailCapaian? _detail;
  KelasInfo? _kelasPrimary;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    setState(() => _isLoading = true);

    final result = await _api.getDetailCapaian(widget.idCapaian);

    if (mounted) {
      if (result['success'] == true && result['data'] != null) {
        final data = result['data'];
        // Parse kelas_primary from santri_info if available
        KelasInfo? kelasPrimary;
        if (data['santri_info'] != null && data['santri_info']['kelas_primary'] != null) {
          kelasPrimary = KelasInfo.fromJson(data['santri_info']['kelas_primary']);
        }
        setState(() {
          _detail = DetailCapaian.fromJson(data);
          _kelasPrimary = kelasPrimary;
          _isLoading = false;
        });
      } else {
        setState(() => _isLoading = false);
        if (result['message'] != null) {
          _showError(result['message']);
        }
      }
    }
  }

  void _showError(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message), backgroundColor: Colors.red),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[100],
      appBar: AppBar(
        title: const Text('Detail Capaian'),
        backgroundColor: widget.color,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _detail == null
              ? _buildError()
              : RefreshIndicator(
                  onRefresh: _loadData,
                  child: SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    padding: const EdgeInsets.all(12),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildHeaderCard(),
                        const SizedBox(height: 12),
                        _buildProgressCard(),
                        const SizedBox(height: 12),
                        _buildBreakdownCard(),
                        if (_detail!.catatan != null &&
                            _detail!.catatan!.isNotEmpty) ...[
                          const SizedBox(height: 12),
                          _buildCatatanCard(),
                        ],
                      ],
                    ),
                  ),
                ),
    );
  }

  Widget _buildError() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Icon(Icons.error_outline, size: 60, color: Colors.grey),
          const SizedBox(height: 12),
          const Text('Gagal memuat data'),
          const SizedBox(height: 12),
          ElevatedButton(
            onPressed: _loadData,
            child: const Text('Coba Lagi'),
          ),
        ],
      ),
    );
  }

  Widget _buildHeaderCard() {
    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(9),
                  decoration: BoxDecoration(
                    color: widget.color.withValues(alpha: 0.1),
                    borderRadius: BorderRadius.circular(9),
                  ),
                  child: Icon(
                    _getIconByCategory(_detail!.materi.kategori ?? ''),
                    color: widget.color,
                    size: 21,
                  ),
                ),
                const SizedBox(width: 9),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        _detail!.materi.namaKitab,
                        style: const TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 2),
                      Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 2),
                            decoration: BoxDecoration(
                              color: widget.color.withValues(alpha: 0.1),
                              borderRadius: BorderRadius.circular(7),
                            ),
                            child: Text(
                              _detail!.materi.kategori ?? '',
                              style: TextStyle(
                                fontSize: 9,
                                fontWeight: FontWeight.w600,
                                color: widget.color,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ],
            ),
            const Divider(height: 19),
            // Kelas badge (new system)
            if (_kelasPrimary != null) ...[
              KelasBadge(kelasInfo: _kelasPrimary!, compact: true),
              const SizedBox(height: 7),
            ] else
              _buildInfoRow(
                Icons.class_,
                'Kelas',
                _detail!.materi.kelas ?? '-',
              ),
            if (_kelasPrimary != null) const SizedBox(height: 0),
            _buildInfoRow(
              Icons.calendar_month,
              'Semester',
              _detail!.semester.namaSemester,
            ),
            const SizedBox(height: 7),
            _buildInfoRow(
              Icons.pages,
              'Total Halaman',
              '${_detail!.materi.totalHalaman} hal (${_detail!.materi.halamanMulai} - ${_detail!.materi.halamanAkhir})',
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildProgressCard() {
    final statusColor = _parseColor(_detail!.progress.statusColor);

    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Progress Capaian',
              style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 12),
            Row(
              children: [
                Expanded(
                  child: _buildStatBox(
                    'Halaman Selesai',
                    _detail!.breakdown.jumlahHalamanSelesai.toString(),
                    Icons.check_circle,
                    Colors.green,
                  ),
                ),
                const SizedBox(width: 9),
                Expanded(
                  child: _buildStatBox(
                    'Sisa Halaman',
                    _detail!.breakdown.halamanBelumSelesai.toString(),
                    Icons.pending,
                    Colors.orange,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),
            Row(
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Persentase',
                        style: TextStyle(fontSize: 11, color: Colors.grey[600]),
                      ),
                      const SizedBox(height: 7),
                      ClipRRect(
                        borderRadius: BorderRadius.circular(7),
                        child: LinearProgressIndicator(
                          value: _detail!.progress.persentase / 100,
                          backgroundColor: Colors.grey[200],
                          valueColor: AlwaysStoppedAnimation(statusColor),
                          minHeight: 24,
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: 12),
                Column(
                  children: [
                    Text(
                      '${_detail!.progress.persentase.toStringAsFixed(1)}%',
                      style: TextStyle(
                        fontSize: 19,
                        fontWeight: FontWeight.bold,
                        color: statusColor,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 9,
                        vertical: 2,
                      ),
                      decoration: BoxDecoration(
                        color: statusColor.withValues(alpha: 0.1),
                        borderRadius: BorderRadius.circular(9),
                      ),
                      child: Text(
                        _detail!.progress.status,
                        style: TextStyle(
                          fontSize: 8,
                          fontWeight: FontWeight.bold,
                          color: statusColor,
                        ),
                      ),
                    ),
                  ],
                ),
              ],
            ),
            const SizedBox(height: 12),
            Row(
              children: [
                Icon(Icons.access_time, size: 12, color: Colors.grey[600]),
                const SizedBox(width: 2),
                Text(
                  'Terakhir diperbarui ${_detail!.lastUpdated}',
                  style: TextStyle(fontSize: 9, color: Colors.grey[600]),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildBreakdownCard() {
    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Halaman yang Sudah Selesai',
              style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 9),
            Container(
              padding: const EdgeInsets.all(9),
              decoration: BoxDecoration(
                color: Colors.grey[100],
                borderRadius: BorderRadius.circular(7),
              ),
              child: Text(
                _detail!.breakdown.halamanSelesaiText.isEmpty
                    ? 'Belum ada halaman yang diselesaikan'
                    : _detail!.breakdown.halamanSelesaiText,
                style: const TextStyle(
                  fontSize: 11,
                  fontFamily: 'monospace',
                ),
              ),
            ),
            if (_detail!.breakdown.halamanSelesaiList.isNotEmpty) ...[
              const SizedBox(height: 9),
              Wrap(
                spacing: 8,
                runSpacing: 8,
                children: _detail!.breakdown.halamanSelesaiList
                    .take(20)
                    .map((hal) => _buildPageChip(hal))
                    .toList(),
              ),
              if (_detail!.breakdown.halamanSelesaiList.length > 20)
                Padding(
                  padding: const EdgeInsets.only(top: 7),
                  child: Text(
                    '... dan ${_detail!.breakdown.halamanSelesaiList.length - 20} halaman lainnya',
                    style: TextStyle(fontSize: 9, color: Colors.grey[600]),
                  ),
                ),
            ],
          ],
        ),
      ),
    );
  }

  Widget _buildCatatanCard() {
    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Icon(Icons.note, color: widget.color, size: 15),
                const SizedBox(width: 7),
                const Text(
                  'Catatan',
                  style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
                ),
              ],
            ),
            const SizedBox(height: 9),
            Text(
              _detail!.catatan!,
              style: TextStyle(fontSize: 11, color: Colors.grey[800]),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Row(
      children: [
        Icon(icon, size: 15, color: Colors.grey[600]),
        const SizedBox(width: 7),
        Text(
          '$label: ',
          style: TextStyle(fontSize: 11, color: Colors.grey[600]),
        ),
        Expanded(
          child: Text(
            value,
            style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600),
          ),
        ),
      ],
    );
  }

  Widget _buildStatBox(String label, String value, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.all(9),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.1),
        borderRadius: BorderRadius.circular(9),
      ),
      child: Column(
        children: [
          Icon(icon, color: color, size: 19),
          const SizedBox(height: 7),
          Text(
            value,
            style: TextStyle(
              fontSize: 15,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
          const SizedBox(height: 2),
          Text(
            label,
            style: TextStyle(fontSize: 9, color: Colors.grey[700]),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }

  Widget _buildPageChip(int halaman) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 5),
      decoration: BoxDecoration(
        color: widget.color.withValues(alpha: 0.1),
        borderRadius: BorderRadius.circular(7),
        border: Border.all(color: widget.color.withValues(alpha: 0.3)),
      ),
      child: Text(
        halaman.toString(),
        style: TextStyle(
          fontSize: 11,
          fontWeight: FontWeight.w600,
          color: widget.color,
        ),
      ),
    );
  }

  IconData _getIconByCategory(String kategori) {
    switch (kategori) {
      case 'Al-Qur\'an':
        return Icons.menu_book;
      case 'Hadist':
        return Icons.article;
      case 'Materi Tambahan':
        return Icons.book;
      default:
        return Icons.book;
    }
  }

  Color _parseColor(String hexColor) {
    try {
      return Color(int.parse(hexColor.replaceFirst('#', '0xFF')));
    } catch (e) {
      return Colors.grey;
    }
  }
}