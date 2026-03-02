// lib/features/spp/spp_page.dart

import 'package:flutter/material.dart';
import '../../core/api/api_service.dart';

class SppPage extends StatefulWidget {
  const SppPage({super.key});

  @override
  State<SppPage> createState() => _SppPageState();
}

class _SppPageState extends State<SppPage> {
  final _api = ApiService();
  
  Map<String, dynamic>? _statusBulanIni;
  Map<String, dynamic>? _tunggakan;
  Map<String, dynamic>? _statistik;
  List<dynamic> _riwayatList = [];
  
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
      _loadStatusBulanIni(),
      _loadTunggakan(),
      _loadStatistik(),
      _loadRiwayat(),
    ]);
  }

  Future<void> _loadStatusBulanIni() async {
    final result = await _api.getStatusSppBulanIni();
    if (mounted && result['success'] == true) {
      setState(() {
        _statusBulanIni = result['data'];
      });
    }
  }

  Future<void> _loadTunggakan() async {
    final result = await _api.getTunggakanSpp();
    if (mounted && result['success'] == true) {
      setState(() {
        _tunggakan = result['data'];
      });
    }
  }

  Future<void> _loadStatistik() async {
    final result = await _api.getStatistikSpp();
    if (mounted && result['success'] == true) {
      setState(() {
        _statistik = result['data'];
      });
    }
  }

  Future<void> _loadRiwayat({bool isRefresh = false}) async {
    if (isRefresh) {
      setState(() {
        _currentPage = 1;
        _riwayatList = [];
      });
    }

    setState(() => _isLoading = true);

    final result = await _api.getRiwayatSpp(
      page: _currentPage,
      status: _selectedStatus,
    );

    if (mounted) {
      setState(() {
        if (result['success'] == true) {
          if (isRefresh) {
            _riwayatList = result['data'];
          } else {
            _riwayatList.addAll(result['data']);
          }

          if (result['pagination'] != null) {
            _lastPage = result['pagination']['last_page'] ?? 1;
          }
        }
        _isLoading = false;
      });
    }
  }

  String _formatRupiah(int nominal) {
    return 'Rp ${nominal.toString().replaceAllMapped(
      RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'),
      (Match m) => '${m[1]}.',
    )}';
  }

  Color _getStatusColor(String status, {bool isTelat = false}) {
    if (status == 'Lunas') return Colors.green;
    if (isTelat) return Colors.red;
    return Colors.orange;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[100],
      appBar: AppBar(
        title: const Text('Pembayaran SPP'),
        backgroundColor: const Color(0xFF6FBA9D),
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () {
              _loadData();
              _loadRiwayat(isRefresh: true);
            },
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          await _loadData();
          await _loadRiwayat(isRefresh: true);
        },
        child: ListView(
          padding: const EdgeInsets.all(12),
          children: [
            // Status Bulan Ini
            if (_statusBulanIni != null) _buildStatusBulanIniCard(),
            const SizedBox(height: 12),

            // Alert Tunggakan
            if (_tunggakan != null && _tunggakan!['ada_tunggakan'] == true)
              _buildAlertTunggakan(),

            // Statistik
            if (_statistik != null) _buildStatistikCard(),
            const SizedBox(height: 15),

            // Filter
            _buildFilterChips(),
            const SizedBox(height: 12),

            // Riwayat
            const Text(
              'Riwayat Pembayaran',
              style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 9),

            if (_isLoading && _riwayatList.isEmpty)
              const Center(child: CircularProgressIndicator())
            else if (_riwayatList.isEmpty)
              _buildEmptyState()
            else
              ..._riwayatList.map((item) => _buildRiwayatCard(item)),

            if (_currentPage < _lastPage) _buildLoadMoreButton(),
          ],
        ),
      ),
    );
  }

  Widget _buildStatusBulanIniCard() {
    final adaTagihan = _statusBulanIni!['ada_tagihan'] ?? false;
    final status = _statusBulanIni!['status'] ?? '';
    final periode = _statusBulanIni!['periode'] ?? '';

    if (!adaTagihan) {
      return Container(
        padding: const EdgeInsets.all(15),
        decoration: BoxDecoration(
          color: Colors.blue[50],
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: Colors.blue[200]!),
        ),
        child: Row(
          children: [
            Icon(Icons.info_outline, color: Colors.blue[700], size: 24),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    periode,
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.bold,
                      color: Colors.blue[900],
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    'Belum Ada Tagihan',
                    style: TextStyle(fontSize: 11, color: Colors.blue[700]),
                  ),
                ],
              ),
            ),
          ],
        ),
      );
    }

    final isLunas = status == 'Lunas';
    final isTelat = _statusBulanIni!['is_telat'] ?? false;
    final nominal = _statusBulanIni!['nominal'] ?? 0;

    return Container(
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: isLunas
              ? [Colors.green[400]!, Colors.green[600]!]
              : isTelat
                  ? [Colors.red[400]!, Colors.red[600]!]
                  : [Colors.orange[400]!, Colors.orange[600]!],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: (isLunas ? Colors.green : isTelat ? Colors.red : Colors.orange)
                .withValues(alpha: 0.3),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      padding: const EdgeInsets.all(15),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: 0.2),
                  borderRadius: BorderRadius.circular(9),
                ),
                child: Icon(
                  isLunas ? Icons.check_circle : Icons.warning_amber_rounded,
                  color: Colors.white,
                  size: 21,
                ),
              ),
              const SizedBox(width: 9),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'SPP $periode',
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      isLunas
                          ? 'Sudah Lunas'
                          : isTelat
                              ? 'Belum Lunas (Telat)'
                              : 'Belum Lunas',
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 11,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Nominal',
                    style: TextStyle(
                      color: Colors.white.withValues(alpha: 0.9),
                      fontSize: 9,
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    _formatRupiah(nominal),
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 15,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ],
              ),
              if (!isLunas)
                Column(
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    Text(
                      'Batas Bayar',
                      style: TextStyle(
                        color: Colors.white.withValues(alpha: 0.9),
                        fontSize: 9,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      _statusBulanIni!['batas_bayar_formatted'] ?? '',
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 11,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ],
                ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildAlertTunggakan() {
    final totalTunggakan = _tunggakan!['total_tunggakan'] ?? 0;
    final jumlahBulan = _tunggakan!['jumlah_bulan'] ?? 0;

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.red[50],
        borderRadius: BorderRadius.circular(9),
        border: Border.all(color: Colors.red[200]!),
      ),
      child: Row(
        children: [
          Icon(Icons.error_outline, color: Colors.red[700], size: 24),
          const SizedBox(width: 9),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Tunggakan: ${_formatRupiah(totalTunggakan)}',
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                    color: Colors.red[900],
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  '$jumlahBulan bulan belum dibayar',
                  style: TextStyle(fontSize: 9, color: Colors.red[700]),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatistikCard() {
    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Statistik Pembayaran',
              style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 12),
            Row(
              children: [
                Expanded(
                  child: _buildStatItem(
                    'Lunas',
                    _statistik!['total_lunas'].toString(),
                    Colors.green,
                  ),
                ),
                Expanded(
                  child: _buildStatItem(
                    'Belum',
                    _statistik!['total_belum_lunas'].toString(),
                    Colors.orange,
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
            fontSize: 21,
            fontWeight: FontWeight.bold,
            color: color,
          ),
        ),
        const SizedBox(height: 2),
        Text(
          label,
          style: TextStyle(fontSize: 9, color: Colors.grey[600]),
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
          _buildFilterChip('Lunas', 'Lunas'),
          _buildFilterChip('Belum Lunas', 'Belum Lunas'),
        ],
      ),
    );
  }

  Widget _buildFilterChip(String label, String value) {
    final isSelected = _selectedStatus == value;
    return Padding(
      padding: const EdgeInsets.only(right: 7),
      child: FilterChip(
        label: Text(label),
        selected: isSelected,
        onSelected: (selected) {
          setState(() {
            _selectedStatus = value;
            _currentPage = 1;
            _riwayatList = [];
          });
          _loadRiwayat();
        },
        selectedColor: const Color(0xFF6FBA9D).withValues(alpha: 0.2),
        checkmarkColor: const Color(0xFF6FBA9D),
      ),
    );
  }

  Widget _buildRiwayatCard(Map<String, dynamic> item) {
    final status = item['status'] ?? '';
    final isTelat = item['is_telat'] ?? false;
    final statusColor = _getStatusColor(status, isTelat: isTelat);

    return Card(
      margin: const EdgeInsets.only(bottom: 9),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(9)),
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  item['periode'] ?? '',
                  style: const TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                  decoration: BoxDecoration(
                    color: statusColor.withValues(alpha: 0.1),
                    borderRadius: BorderRadius.circular(9),
                  ),
                  child: Text(
                    status == 'Lunas'
                        ? 'Lunas'
                        : isTelat
                            ? 'Telat'
                            : 'Belum',
                    style: TextStyle(
                      fontSize: 9,
                      fontWeight: FontWeight.bold,
                      color: statusColor,
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 9),
            Text(
              _formatRupiah(item['nominal'] ?? 0),
              style: const TextStyle(
                fontSize: 15,
                fontWeight: FontWeight.bold,
                color: Color(0xFF6FBA9D),
              ),
            ),
            const SizedBox(height: 9),
            if (item['tanggal_bayar_formatted'] != null)
              Row(
                children: [
                  Icon(Icons.check_circle, size: 11, color: Colors.green[600]),
                  const SizedBox(width: 2),
                  Text(
                    'Dibayar: ${item['tanggal_bayar_formatted']}',
                    style: TextStyle(fontSize: 9, color: Colors.grey[600]),
                  ),
                ],
              )
            else
              Row(
                children: [
                  Icon(Icons.schedule, size: 11, color: Colors.grey[600]),
                  const SizedBox(width: 2),
                  Text(
                    'Batas: ${item['batas_bayar_formatted']}',
                    style: TextStyle(fontSize: 9, color: Colors.grey[600]),
                  ),
                ],
              ),
          ],
        ),
      ),
    );
  }

  Widget _buildLoadMoreButton() {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 12),
      child: Center(
        child: ElevatedButton(
          onPressed: () {
            setState(() => _currentPage++);
            _loadRiwayat();
          },
          child: const Text('Muat Lebih Banyak'),
        ),
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          children: [
            Icon(Icons.receipt_long_outlined, size: 60, color: Colors.grey[400]),
            const SizedBox(height: 12),
            Text(
              'Belum ada riwayat pembayaran',
              style: TextStyle(fontSize: 12, color: Colors.grey[600]),
            ),
          ],
        ),
      ),
    );
  }
}