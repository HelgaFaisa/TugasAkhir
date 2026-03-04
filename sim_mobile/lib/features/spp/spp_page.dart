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
      setState(() => _statusBulanIni = result['data']);
    }
  }

  Future<void> _loadTunggakan() async {
    final result = await _api.getTunggakanSpp();
    if (mounted && result['success'] == true) {
      setState(() => _tunggakan = result['data']);
    }
  }

  Future<void> _loadStatistik() async {
    final result = await _api.getStatistikSpp();
    if (mounted && result['success'] == true) {
      setState(() => _statistik = result['data']);
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

  String _formatRupiah(num nominal) {
    return 'Rp ${nominal.toInt().toString().replaceAllMapped(
          RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'),
          (Match m) => '${m[1]}.',
        )}';
  }

  /// Deteksi apakah item adalah cicilan:
  /// status "Belum Lunas" + ada field is_cicilan==true ATAU nominal_terbayar > 0
  bool _isCicilan(Map<String, dynamic> item) {
    if (item['status'] != 'Belum Lunas') return false;
    if (item['is_cicilan'] == true) return true;
    final terbayar = (item['nominal_terbayar'] ?? 0) as num;
    return terbayar > 0;
  }

  Color _getStatusColor(Map<String, dynamic> item) {
    final status = item['status'] ?? '';
    final isTelat = item['is_telat'] ?? false;
    if (status == 'Lunas') return Colors.green;
    if (_isCicilan(item)) return Colors.purple;
    if (isTelat) return Colors.red;
    return Colors.orange;
  }

  String _getStatusLabel(Map<String, dynamic> item) {
    final status = item['status'] ?? '';
    final isTelat = item['is_telat'] ?? false;
    if (status == 'Lunas') return 'Lunas';
    if (_isCicilan(item)) {
      final pct = _getPorsentase(item);
      return 'Cicilan $pct%';
    }
    if (isTelat) return 'Telat';
    return 'Belum';
  }

  int _getPorsentase(Map<String, dynamic> item) {
    final nominal = (item['nominal'] ?? 0) as num;
    final terbayar = (item['nominal_terbayar'] ?? 0) as num;
    if (nominal <= 0) return 0;
    return (terbayar / nominal * 100).clamp(0, 100).round();
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
            onPressed: () async {
              await _loadData();
              await _loadRiwayat(isRefresh: true);
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
            if (_statusBulanIni != null) _buildStatusBulanIniCard(),
            const SizedBox(height: 12),

            if (_tunggakan != null && _tunggakan!['ada_tunggakan'] == true)
              _buildAlertTunggakan(),

            if (_statistik != null) _buildStatistikCard(),
            const SizedBox(height: 15),

            _buildFilterChips(),
            const SizedBox(height: 12),

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

  // ─────────────────────────────────────────────
  // STATUS BULAN INI
  // ─────────────────────────────────────────────

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
    final isCicilan = _statusBulanIni!['is_cicilan'] == true ||
        ((_statusBulanIni!['nominal_terbayar'] ?? 0) as num) > 0 && !isLunas;
    final nominal = (_statusBulanIni!['nominal'] ?? 0) as num;
    final nominalTerbayar =
        (_statusBulanIni!['nominal_terbayar'] ?? 0) as num;

    List<Color> gradientColors;
    String statusLabel;
    IconData statusIcon;

    if (isLunas) {
      gradientColors = [Colors.green[400]!, Colors.green[600]!];
      statusLabel = 'Sudah Lunas';
      statusIcon = Icons.check_circle;
    } else if (isCicilan) {
      gradientColors = [Colors.purple[400]!, Colors.purple[600]!];
      statusLabel = 'Cicilan';
      statusIcon = Icons.payments_outlined;
    } else if (isTelat) {
      gradientColors = [Colors.red[400]!, Colors.red[600]!];
      statusLabel = 'Belum Lunas (Telat)';
      statusIcon = Icons.warning_amber_rounded;
    } else {
      gradientColors = [Colors.orange[400]!, Colors.orange[600]!];
      statusLabel = 'Belum Lunas';
      statusIcon = Icons.warning_amber_rounded;
    }

    return Container(
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: gradientColors,
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: gradientColors[0].withValues(alpha: 0.35),
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
                child: Icon(statusIcon, color: Colors.white, size: 21),
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
                      statusLabel,
                      style: const TextStyle(color: Colors.white, fontSize: 11),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),

          // Nominal row
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
                        fontSize: 9),
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
                          fontSize: 9),
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

          // Cicilan progress bar (hanya jika cicilan)
          if (isCicilan) ...[
            const SizedBox(height: 12),
            _buildProgressBar(
              terbayar: nominalTerbayar.toDouble(),
              total: nominal.toDouble(),
              foreground: Colors.white,
              background: Colors.white.withValues(alpha: 0.3),
            ),
            const SizedBox(height: 4),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  'Terbayar: ${_formatRupiah(nominalTerbayar)}',
                  style: TextStyle(
                      color: Colors.white.withValues(alpha: 0.9),
                      fontSize: 10),
                ),
                Text(
                  'Sisa: ${_formatRupiah(nominal - nominalTerbayar)}',
                  style: TextStyle(
                      color: Colors.white.withValues(alpha: 0.9),
                      fontSize: 10),
                ),
              ],
            ),
          ],
        ],
      ),
    );
  }

  // ─────────────────────────────────────────────
  // ALERT TUNGGAKAN
  // ─────────────────────────────────────────────

  Widget _buildAlertTunggakan() {
    final totalTunggakan =
        (_tunggakan!['total_tunggakan'] ?? 0) as num;
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

  // ─────────────────────────────────────────────
  // STATISTIK
  // ─────────────────────────────────────────────

  Widget _buildStatistikCard() {
    return Card(
      elevation: 2,
      shape:
          RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Statistik Pembayaran',
              style:
                  TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 12),
            Row(
              children: [
                Expanded(
                  child: _buildStatItem(
                    'Lunas',
                    _statistik!['total_lunas'].toString(),
                    Colors.green,
                    Icons.check_circle_outline,
                  ),
                ),
                // Divider vertikal
                Container(
                    width: 1, height: 40, color: Colors.grey[200]),
                Expanded(
                  child: _buildStatItem(
                    'Cicilan',
                    (_statistik!['total_cicilan'] ?? 0).toString(),
                    Colors.purple,
                    Icons.payments_outlined,
                  ),
                ),
                Container(
                    width: 1, height: 40, color: Colors.grey[200]),
                Expanded(
                  child: _buildStatItem(
                    'Belum',
                    _statistik!['total_belum_lunas'].toString(),
                    Colors.orange,
                    Icons.schedule_outlined,
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildStatItem(
      String label, String value, Color color, IconData icon) {
    return Column(
      children: [
        Icon(icon, color: color, size: 18),
        const SizedBox(height: 4),
        Text(
          value,
          style: TextStyle(
            fontSize: 21,
            fontWeight: FontWeight.bold,
            color: color,
          ),
        ),
        const SizedBox(height: 2),
        Text(label,
            style: TextStyle(fontSize: 9, color: Colors.grey[600])),
      ],
    );
  }

  // ─────────────────────────────────────────────
  // FILTER CHIPS
  // ─────────────────────────────────────────────

  Widget _buildFilterChips() {
    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      child: Row(
        children: [
          _buildFilterChip('Semua', 'semua'),
          _buildFilterChip('Lunas', 'Lunas'),
          _buildFilterChip('Cicilan', 'Cicilan'),
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
        selectedColor:
            const Color(0xFF6FBA9D).withValues(alpha: 0.2),
        checkmarkColor: const Color(0xFF6FBA9D),
      ),
    );
  }

  // ─────────────────────────────────────────────
  // RIWAYAT CARD
  // ─────────────────────────────────────────────

  Widget _buildRiwayatCard(Map<String, dynamic> item) {
    final isCicilan = _isCicilan(item);
    final statusColor = _getStatusColor(item);
    final statusLabel = _getStatusLabel(item);
    final nominal = (item['nominal'] ?? 0) as num;
    final nominalTerbayar = (item['nominal_terbayar'] ?? 0) as num;
    final nominalSisa = (item['nominal_sisa'] ?? (nominal - nominalTerbayar)) as num;
    final porsentase = _getPorsentase(item);
    final status = item['status'] ?? '';

    return Card(
      margin: const EdgeInsets.only(bottom: 9),
      shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(9)),
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header: periode + badge status
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  item['periode'] ?? '',
                  style: const TextStyle(
                      fontSize: 12, fontWeight: FontWeight.bold),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(
                      horizontal: 8, vertical: 3),
                  decoration: BoxDecoration(
                    color: statusColor.withValues(alpha: 0.12),
                    borderRadius: BorderRadius.circular(9),
                  ),
                  child: Text(
                    statusLabel,
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

            // Nominal utama
            Text(
              _formatRupiah(nominal),
              style: const TextStyle(
                fontSize: 15,
                fontWeight: FontWeight.bold,
                color: Color(0xFF6FBA9D),
              ),
            ),

            // ── Cicilan detail ──────────────────────
            if (isCicilan) ...[
              const SizedBox(height: 9),
              _buildProgressBar(
                terbayar: nominalTerbayar.toDouble(),
                total: nominal.toDouble(),
                foreground: Colors.purple,
                background: Colors.purple.withValues(alpha: 0.12),
              ),
              const SizedBox(height: 6),
              Row(
                children: [
                  Expanded(
                    child: _buildMiniInfo(
                      icon: Icons.check_circle_outline,
                      color: Colors.green,
                      label: 'Terbayar',
                      value: _formatRupiah(nominalTerbayar),
                    ),
                  ),
                  Expanded(
                    child: _buildMiniInfo(
                      icon: Icons.hourglass_bottom_outlined,
                      color: Colors.red,
                      label: 'Sisa',
                      value: _formatRupiah(nominalSisa),
                    ),
                  ),
                  Expanded(
                    child: _buildMiniInfo(
                      icon: Icons.percent_outlined,
                      color: Colors.purple,
                      label: 'Progress',
                      value: '$porsentase%',
                    ),
                  ),
                ],
              ),
            ],

            // ── Footer: tanggal ─────────────────────
            const SizedBox(height: 9),
            if (status == 'Lunas' &&
                item['tanggal_bayar_formatted'] != null)
              _buildDateRow(
                icon: Icons.check_circle,
                color: Colors.green[600]!,
                text: 'Dibayar: ${item['tanggal_bayar_formatted']}',
              )
            else if (isCicilan)
              _buildDateRow(
                icon: Icons.schedule,
                color: Colors.purple[600]!,
                text: 'Batas: ${item['batas_bayar_formatted'] ?? '-'}',
              )
            else
              _buildDateRow(
                icon: Icons.schedule,
                color: Colors.grey[600]!,
                text: 'Batas: ${item['batas_bayar_formatted'] ?? '-'}',
              ),
          ],
        ),
      ),
    );
  }

  // ─────────────────────────────────────────────
  // HELPERS / SHARED WIDGETS
  // ─────────────────────────────────────────────

  Widget _buildProgressBar({
    required double terbayar,
    required double total,
    required Color foreground,
    required Color background,
  }) {
    final fraction = total > 0 ? (terbayar / total).clamp(0.0, 1.0) : 0.0;
    return LayoutBuilder(
      builder: (context, constraints) {
        return Container(
          height: 8,
          width: constraints.maxWidth,
          decoration: BoxDecoration(
            color: background,
            borderRadius: BorderRadius.circular(8),
          ),
          child: Align(
            alignment: Alignment.centerLeft,
            child: Container(
              width: constraints.maxWidth * fraction,
              height: 8,
              decoration: BoxDecoration(
                color: foreground,
                borderRadius: BorderRadius.circular(8),
              ),
            ),
          ),
        );
      },
    );
  }

  Widget _buildMiniInfo({
    required IconData icon,
    required Color color,
    required String label,
    required String value,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Icon(icon, size: 10, color: color),
            const SizedBox(width: 3),
            Text(label,
                style:
                    TextStyle(fontSize: 9, color: Colors.grey[600])),
          ],
        ),
        const SizedBox(height: 2),
        Text(
          value,
          style: TextStyle(
              fontSize: 10,
              fontWeight: FontWeight.bold,
              color: color),
        ),
      ],
    );
  }

  Widget _buildDateRow({
    required IconData icon,
    required Color color,
    required String text,
  }) {
    return Row(
      children: [
        Icon(icon, size: 11, color: color),
        const SizedBox(width: 4),
        Text(text,
            style: TextStyle(fontSize: 9, color: Colors.grey[600])),
      ],
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
            Icon(Icons.receipt_long_outlined,
                size: 60, color: Colors.grey[400]),
            const SizedBox(height: 12),
            Text(
              'Belum ada riwayat pembayaran',
              style:
                  TextStyle(fontSize: 12, color: Colors.grey[600]),
            ),
          ],
        ),
      ),
    );
  }
}