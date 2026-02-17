// lib/features/kesehatan/kesehatan_page.dart

import 'package:flutter/material.dart';
import '../../core/api/api_service.dart';
import 'kesehatan_detail_page.dart';

class KesehatanPage extends StatefulWidget {
  const KesehatanPage({super.key});

  @override
  State<KesehatanPage> createState() => _KesehatanPageState();
}

class _KesehatanPageState extends State<KesehatanPage> {
  final _api = ApiService();
  List<dynamic> _kesehatanList = [];
  Map<String, dynamic>? _statistik;
  bool _isLoading = true;
  String _selectedStatus = 'semua';
  int _currentPage = 1;
  int _lastPage = 1;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    await Future.wait([
      _loadKesehatan(),
      _loadStatistik(),
    ]);
  }

  Future<void> _loadKesehatan({bool isRefresh = false}) async {
    if (isRefresh) {
      setState(() {
        _currentPage = 1;
        _kesehatanList = [];
      });
    }

    setState(() => _isLoading = true);

    final result = await _api.getRiwayatKesehatan(
      page: _currentPage,
      status: _selectedStatus,
    );

    if (mounted) {
      setState(() {
        if (result['success'] == true) {
          if (isRefresh) {
            _kesehatanList = result['data'];
          } else {
            _kesehatanList.addAll(result['data']);
          }

          if (result['pagination'] != null) {
            _lastPage = result['pagination']['last_page'] ?? 1;
          }
        }
        _isLoading = false;
      });
    }
  }

  Future<void> _loadStatistik() async {
    final result = await _api.getStatistikKesehatan();
    if (mounted && result['success'] == true) {
      setState(() {
        _statistik = result['data'];
      });
    }
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'dirawat':
        return Colors.red;
      case 'sembuh':
        return Colors.green;
      case 'izin':
        return Colors.orange;
      default:
        return Colors.grey;
    }
  }

  String _getStatusLabel(String status) {
    switch (status) {
      case 'dirawat':
        return 'Dirawat';
      case 'sembuh':
        return 'Sembuh';
      case 'izin':
        return 'Izin';
      default:
        return status;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[100],
      appBar: AppBar(
        title: const Text('Riwayat Kesehatan'),
        backgroundColor: const Color(0xFF7C3AED),
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => _loadKesehatan(isRefresh: true),
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () => _loadKesehatan(isRefresh: true),
        child: ListView(
          padding: const EdgeInsets.all(16),
          children: [
            // Card Statistik
            if (_statistik != null) _buildStatistikCard(),
            const SizedBox(height: 20),

            // Filter Status
            _buildFilterChips(),
            const SizedBox(height: 16),

            // Alert jika sedang dirawat
            if (_statistik?['sedang_dirawat'] != null)
              _buildAlertDirawat(_statistik!['sedang_dirawat']),

            // List Riwayat
            if (_isLoading && _kesehatanList.isEmpty)
              const Center(child: CircularProgressIndicator())
            else if (_kesehatanList.isEmpty)
              _buildEmptyState()
            else
              ..._kesehatanList.map((item) => _buildKesehatanCard(item)),

            // Load More
            if (_currentPage < _lastPage) _buildLoadMoreButton(),
          ],
        ),
      ),
    );
  }

  Widget _buildStatistikCard() {
    return Card(
      elevation: 4,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Statistik Kesehatan',
              style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 16),
            Row(
              children: [
                Expanded(
                  child: _buildStatItem(
                    'Total',
                    _statistik!['total_riwayat'].toString(),
                    Colors.blue,
                  ),
                ),
                Expanded(
                  child: _buildStatItem(
                    'Dirawat',
                    _statistik!['total_dirawat'].toString(),
                    Colors.red,
                  ),
                ),
                Expanded(
                  child: _buildStatItem(
                    'Sembuh',
                    _statistik!['total_sembuh'].toString(),
                    Colors.green,
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatItem(String label, String value, Color color) {
    return Column(
      children: [
        Text(
          value,
          style: TextStyle(
            fontSize: 24,
            fontWeight: FontWeight.bold,
            color: color,
          ),
        ),
        const SizedBox(height: 4),
        Text(
          label,
          style: TextStyle(fontSize: 12, color: Colors.grey[600]),
        ),
      ],
    );
  }

  Widget _buildFilterChips() {
    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      child: Row(
        children: [
          _buildFilterChip('Semua', 'semua'),
          _buildFilterChip('Dirawat', 'dirawat'),
          _buildFilterChip('Sembuh', 'sembuh'),
          _buildFilterChip('Izin', 'izin'),
        ],
      ),
    );
  }

  Widget _buildFilterChip(String label, String value) {
    final isSelected = _selectedStatus == value;
    return Padding(
      padding: const EdgeInsets.only(right: 8),
      child: FilterChip(
        label: Text(label),
        selected: isSelected,
        onSelected: (selected) {
          setState(() {
            _selectedStatus = value;
            _currentPage = 1;
            _kesehatanList = [];
          });
          _loadKesehatan();
        },
        selectedColor: const Color(0xFF7C3AED).withValues(alpha: 0.2),
        checkmarkColor: const Color(0xFF7C3AED),
      ),
    );
  }

  Widget _buildAlertDirawat(Map<String, dynamic> data) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.red[50],
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.red[200]!),
      ),
      child: Row(
        children: [
          Icon(Icons.warning_amber_rounded, color: Colors.red[700]),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Sedang Dirawat',
                  style: TextStyle(
                    fontWeight: FontWeight.bold,
                    color: Colors.red[900],
                  ),
                ),
                Text(
                  '${data['keluhan']} (${data['lama_dirawat']})',
                  style: TextStyle(fontSize: 12, color: Colors.red[800]),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildKesehatanCard(Map<String, dynamic> item) {
    final status = item['status'] ?? '';
    final statusColor = _getStatusColor(status);

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: InkWell(
        onTap: () {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (_) => KesehatanDetailPage(idKesehatan: item['id_kesehatan']),
            ),
          );
        },
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: statusColor.withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Text(
                      _getStatusLabel(status),
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                        color: statusColor,
                      ),
                    ),
                  ),
                  const Spacer(),
                  Text(
                    item['tanggal_masuk_formatted'] ?? '',
                    style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              Text(
                item['keluhan'] ?? '',
                style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
              const SizedBox(height: 8),
              Row(
                children: [
                  Icon(Icons.calendar_today, size: 14, color: Colors.grey[600]),
                  const SizedBox(width: 4),
                  Text(
                    'Lama: ${item['lama_dirawat']}',
                    style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildLoadMoreButton() {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 16),
      child: Center(
        child: ElevatedButton(
          onPressed: () {
            setState(() => _currentPage++);
            _loadKesehatan();
          },
          child: const Text('Muat Lebih Banyak'),
        ),
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          children: [
            Icon(Icons.medical_services_outlined, size: 80, color: Colors.grey[400]),
            const SizedBox(height: 16),
            Text(
              'Belum ada riwayat kesehatan',
              style: TextStyle(fontSize: 16, color: Colors.grey[600]),
            ),
          ],
        ),
      ),
    );
  }
}