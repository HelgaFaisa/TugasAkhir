// lib/features/uang_saku/uang_saku_page.dart

import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../core/api/api_service.dart';

class UangSakuPage extends StatefulWidget {
  const UangSakuPage({super.key});

  @override
  State<UangSakuPage> createState() => _UangSakuPageState();
}

class _UangSakuPageState extends State<UangSakuPage> {
  final _api = ApiService();

  Map<String, dynamic>? _saldoData;
  List<dynamic> _transaksiList = [];
  bool _isLoadingSaldo = true;
  bool _isLoadingTransaksi = true;
  int _currentPage = 1;
  int _lastPage = 1;

  // Filter variables
  String _selectedPeriode =
      'bulan_ini'; // hari_ini, minggu_ini, bulan_ini, tahun_ini, custom
  String _selectedJenis = 'semua'; // semua, pemasukan, pengeluaran
  DateTime? _customTanggalDari;
  DateTime? _customTanggalSampai;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    await Future.wait([_loadSaldo(), _loadTransaksi()]);
  }

  Future<void> _loadSaldo() async {
    setState(() => _isLoadingSaldo = true);

    final dateRange = _getDateRange();
    final result = await _api.getSaldoUangSaku(
      tanggalDari: dateRange['dari'],
      tanggalSampai: dateRange['sampai'],
    );

    if (mounted) {
      setState(() {
        if (result['success'] == true) {
          _saldoData = result['data'];
        }
        _isLoadingSaldo = false;
      });
    }
  }

  Future<void> _loadTransaksi({bool isRefresh = false}) async {
    if (isRefresh) {
      setState(() {
        _currentPage = 1;
        _transaksiList = [];
      });
    }

    setState(() => _isLoadingTransaksi = true);

    final dateRange = _getDateRange();
    final result = await _api.getRiwayatUangSaku(
      page: _currentPage,
      jenisTrans: _selectedJenis,
      tanggalDari: dateRange['dari'],
      tanggalSampai: dateRange['sampai'],
    );

    if (mounted) {
      setState(() {
        if (result['success'] == true) {
          if (isRefresh) {
            _transaksiList = result['data'];
          } else {
            _transaksiList.addAll(result['data']);
          }

          if (result['pagination'] != null) {
            _lastPage = result['pagination']['last_page'] ?? 1;
          }
        }
        _isLoadingTransaksi = false;
      });
    }
  }

  // Helper: Get date range based on selected periode
  Map<String, String?> _getDateRange() {
    final now = DateTime.now();
    String? dari;
    String? sampai;

    switch (_selectedPeriode) {
      case 'hari_ini':
        dari = DateFormat('yyyy-MM-dd').format(now);
        sampai = DateFormat('yyyy-MM-dd').format(now);
        break;
      case 'minggu_ini':
        final startOfWeek = now.subtract(Duration(days: now.weekday - 1));
        dari = DateFormat('yyyy-MM-dd').format(startOfWeek);
        sampai = DateFormat('yyyy-MM-dd').format(now);
        break;
      case 'bulan_ini':
        dari = DateFormat(
          'yyyy-MM-dd',
        ).format(DateTime(now.year, now.month, 1));
        sampai = DateFormat('yyyy-MM-dd').format(now);
        break;
      case 'tahun_ini':
        dari = DateFormat('yyyy-MM-dd').format(DateTime(now.year, 1, 1));
        sampai = DateFormat('yyyy-MM-dd').format(now);
        break;
      case 'custom':
        dari =
            _customTanggalDari != null
                ? DateFormat('yyyy-MM-dd').format(_customTanggalDari!)
                : null;
        sampai =
            _customTanggalSampai != null
                ? DateFormat('yyyy-MM-dd').format(_customTanggalSampai!)
                : null;
        break;
    }

    return {'dari': dari, 'sampai': sampai};
  }

  // Show filter bottom sheet
  void _showFilterSheet() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder:
          (context) => StatefulBuilder(
            builder: (context, setModalState) {
              return Padding(
                padding: EdgeInsets.only(
                  bottom: MediaQuery.of(context).viewInsets.bottom,
                  left: 16,
                  right: 16,
                  top: 16,
                ),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Header
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text(
                          'Filter Transaksi',
                          style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        IconButton(
                          icon: const Icon(Icons.close),
                          onPressed: () => Navigator.pop(context),
                        ),
                      ],
                    ),
                    const Divider(),
                    const SizedBox(height: 16),

                    // Periode Filter
                    const Text(
                      'Periode',
                      style: TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Wrap(
                      spacing: 8,
                      runSpacing: 8,
                      children: [
                        _buildPeriodeChip(
                          'Hari Ini',
                          'hari_ini',
                          setModalState,
                        ),
                        _buildPeriodeChip(
                          'Minggu Ini',
                          'minggu_ini',
                          setModalState,
                        ),
                        _buildPeriodeChip(
                          'Bulan Ini',
                          'bulan_ini',
                          setModalState,
                        ),
                        _buildPeriodeChip(
                          'Tahun Ini',
                          'tahun_ini',
                          setModalState,
                        ),
                        _buildPeriodeChip('Custom', 'custom', setModalState),
                      ],
                    ),

                    // Custom Date Range (if selected)
                    if (_selectedPeriode == 'custom') ...[
                      const SizedBox(height: 16),
                      Row(
                        children: [
                          Expanded(
                            child: OutlinedButton.icon(
                              icon: const Icon(Icons.calendar_today, size: 16),
                              label: Text(
                                _customTanggalDari != null
                                    ? DateFormat(
                                      'dd/MM/yyyy',
                                    ).format(_customTanggalDari!)
                                    : 'Dari Tanggal',
                                style: const TextStyle(fontSize: 12),
                              ),
                              onPressed: () async {
                                final date = await showDatePicker(
                                  context: context,
                                  initialDate:
                                      _customTanggalDari ?? DateTime.now(),
                                  firstDate: DateTime(2020),
                                  lastDate: DateTime.now(),
                                );
                                if (date != null) {
                                  setModalState(
                                    () => _customTanggalDari = date,
                                  );
                                }
                              },
                            ),
                          ),
                          const SizedBox(width: 8),
                          Expanded(
                            child: OutlinedButton.icon(
                              icon: const Icon(Icons.calendar_today, size: 16),
                              label: Text(
                                _customTanggalSampai != null
                                    ? DateFormat(
                                      'dd/MM/yyyy',
                                    ).format(_customTanggalSampai!)
                                    : 'Sampai Tanggal',
                                style: const TextStyle(fontSize: 12),
                              ),
                              onPressed: () async {
                                final date = await showDatePicker(
                                  context: context,
                                  initialDate:
                                      _customTanggalSampai ?? DateTime.now(),
                                  firstDate:
                                      _customTanggalDari ?? DateTime(2020),
                                  lastDate: DateTime.now(),
                                );
                                if (date != null) {
                                  setModalState(
                                    () => _customTanggalSampai = date,
                                  );
                                }
                              },
                            ),
                          ),
                        ],
                      ),
                    ],

                    const SizedBox(height: 24),

                    // Jenis Transaksi Filter
                    const Text(
                      'Jenis Transaksi',
                      style: TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Wrap(
                      spacing: 8,
                      children: [
                        _buildJenisChip('Semua', 'semua', setModalState),
                        _buildJenisChip(
                          'Pemasukan',
                          'pemasukan',
                          setModalState,
                        ),
                        _buildJenisChip(
                          'Pengeluaran',
                          'pengeluaran',
                          setModalState,
                        ),
                      ],
                    ),

                    const SizedBox(height: 24),

                    // Apply Button
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFF7C3AED),
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                        ),
                        onPressed: () {
                          Navigator.pop(context);
                          setState(() {
                            _currentPage = 1;
                            _transaksiList = [];
                          });
                          _loadData();
                        },
                        child: const Text(
                          'Terapkan Filter',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),
                  ],
                ),
              );
            },
          ),
    );
  }

  Widget _buildPeriodeChip(
    String label,
    String value,
    StateSetter setModalState,
  ) {
    final isSelected = _selectedPeriode == value;
    return FilterChip(
      label: Text(label),
      selected: isSelected,
      onSelected: (selected) {
        setModalState(() => _selectedPeriode = value);
      },
      selectedColor: const Color(0xFF7C3AED).withValues(alpha: 0.2),
      checkmarkColor: const Color(0xFF7C3AED),
      labelStyle: TextStyle(
        color: isSelected ? const Color(0xFF7C3AED) : Colors.grey[700],
        fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
      ),
    );
  }

  Widget _buildJenisChip(
    String label,
    String value,
    StateSetter setModalState,
  ) {
    final isSelected = _selectedJenis == value;
    return FilterChip(
      label: Text(label),
      selected: isSelected,
      onSelected: (selected) {
        setModalState(() => _selectedJenis = value);
      },
      selectedColor: const Color(0xFF7C3AED).withValues(alpha: 0.2),
      checkmarkColor: const Color(0xFF7C3AED),
      labelStyle: TextStyle(
        color: isSelected ? const Color(0xFF7C3AED) : Colors.grey[700],
        fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
      ),
    );
  }

  String _formatRupiah(int nominal) {
    return 'Rp ${nominal.toString().replaceAllMapped(RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'), (Match m) => '${m[1]}.')}';
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[100],
      appBar: AppBar(
        title: const Text('Uang Saku'),
        backgroundColor: const Color(0xFF7C3AED),
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.filter_list),
            onPressed: _showFilterSheet,
          ),
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => _loadData(),
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () => _loadTransaksi(isRefresh: true),
        child: ListView(
          padding: const EdgeInsets.all(16),
          children: [
            // Filter Info Badge
            _buildFilterInfoBadge(),
            const SizedBox(height: 12),

            // Card Saldo
            _buildSaldoCard(),
            const SizedBox(height: 24),

            // Title Riwayat
            const Text(
              'Riwayat Transaksi',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 12),

            // List Transaksi
            if (_isLoadingTransaksi && _transaksiList.isEmpty)
              const Center(child: CircularProgressIndicator())
            else if (_transaksiList.isEmpty)
              _buildEmptyState()
            else
              ..._transaksiList
                  .map((transaksi) => _buildTransaksiCard(transaksi))
                  ,

            // Load More Button
            if (_currentPage < _lastPage)
              Padding(
                padding: const EdgeInsets.symmetric(vertical: 16),
                child: ElevatedButton(
                  onPressed: () {
                    setState(() => _currentPage++);
                    _loadTransaksi();
                  },
                  child: const Text('Muat Lebih Banyak'),
                ),
              ),
          ],
        ),
      ),
    );
  }

  Widget _buildFilterInfoBadge() {
    String periodeText = '';
    switch (_selectedPeriode) {
      case 'hari_ini':
        periodeText = 'Hari Ini';
        break;
      case 'minggu_ini':
        periodeText = 'Minggu Ini';
        break;
      case 'bulan_ini':
        periodeText = 'Bulan Ini';
        break;
      case 'tahun_ini':
        periodeText = 'Tahun Ini';
        break;
      case 'custom':
        if (_customTanggalDari != null && _customTanggalSampai != null) {
          periodeText =
              '${DateFormat('dd/MM/yy').format(_customTanggalDari!)} - ${DateFormat('dd/MM/yy').format(_customTanggalSampai!)}';
        } else {
          periodeText = 'Custom';
        }
        break;
    }

    String jenisText = '';
    switch (_selectedJenis) {
      case 'semua':
        jenisText = 'Semua Transaksi';
        break;
      case 'pemasukan':
        jenisText = 'Pemasukan Saja';
        break;
      case 'pengeluaran':
        jenisText = 'Pengeluaran Saja';
        break;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
      decoration: BoxDecoration(
        color: const Color(0xFF7C3AED).withValues(alpha: 0.1),
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: const Color(0xFF7C3AED).withValues(alpha: 0.3)),
      ),
      child: Row(
        children: [
          const Icon(Icons.info_outline, size: 16, color: Color(0xFF7C3AED)),
          const SizedBox(width: 8),
          Expanded(
            child: Text(
              'Filter: $periodeText • $jenisText',
              style: const TextStyle(
                fontSize: 12,
                color: Color(0xFF7C3AED),
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSaldoCard() {
    if (_isLoadingSaldo) {
      return const Card(
        child: Padding(
          padding: EdgeInsets.all(24),
          child: Center(child: CircularProgressIndicator()),
        ),
      );
    }

    final saldo = _saldoData?['saldo'] ?? 0;
    final pemasukan = _saldoData?['total_pemasukan'] ?? 0;
    final pengeluaran = _saldoData?['total_pengeluaran'] ?? 0;

    return Container(
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Color(0xFF7C3AED), Color(0xFF5B21B6)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF7C3AED).withValues(alpha: 0.3),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      padding: const EdgeInsets.all(24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: 0.2),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: const Icon(
                  Icons.account_balance_wallet,
                  color: Colors.white,
                  size: 24,
                ),
              ),
              const SizedBox(width: 12),
              const Text(
                'Saldo Saat Ini',
                style: TextStyle(color: Colors.white70, fontSize: 14),
              ),
            ],
          ),
          const SizedBox(height: 16),

          // Saldo Besar
          Text(
            _formatRupiah(saldo),
            style: const TextStyle(
              color: Colors.white,
              fontSize: 32,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 20),

          // Info Pemasukan & Pengeluaran
          Row(
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Total Pemasukan',
                      style: TextStyle(color: Colors.white70, fontSize: 12),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      _formatRupiah(pemasukan),
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 14,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ],
                ),
              ),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Total Pengeluaran',
                      style: TextStyle(color: Colors.white70, fontSize: 12),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      _formatRupiah(pengeluaran),
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 14,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildTransaksiCard(Map<String, dynamic> transaksi) {
    final jenis = transaksi['jenis_transaksi'] ?? '';
    final nominal = transaksi['nominal'] ?? 0;
    final keterangan = transaksi['keterangan'] ?? 'Tidak ada keterangan';
    final tanggal = transaksi['tanggal_transaksi'] ?? '';
    final saldoSebelum = transaksi['saldo_sebelum'] ?? 0;
    final saldoSesudah = transaksi['saldo_sesudah'] ?? 0;

    final isPemasukan = jenis == 'pemasukan';

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                // Badge Jenis Transaksi
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 12,
                    vertical: 4,
                  ),
                  decoration: BoxDecoration(
                    color: isPemasukan ? Colors.green[100] : Colors.red[100],
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Text(
                    isPemasukan ? 'Pemasukan' : 'Pengeluaran',
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.bold,
                      color: isPemasukan ? Colors.green[800] : Colors.red[800],
                    ),
                  ),
                ),
                const Spacer(),

                // Tanggal
                Text(
                  tanggal,
                  style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                ),
              ],
            ),
            const SizedBox(height: 12),

            // Nominal
            Text(
              _formatRupiah(nominal),
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
                color: isPemasukan ? Colors.green[700] : Colors.red[700],
              ),
            ),
            const SizedBox(height: 8),

            // Keterangan
            Text(
              keterangan,
              style: TextStyle(fontSize: 14, color: Colors.grey[700]),
            ),
            const SizedBox(height: 12),

            // Saldo Sebelum → Sesudah
            Row(
              children: [
                Flexible(
                  child: Text(
                    _formatRupiah(saldoSebelum),
                    style: const TextStyle(fontSize: 12, color: Colors.grey),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
                const Padding(
                  padding: EdgeInsets.symmetric(horizontal: 6),
                  child: Icon(
                    Icons.arrow_forward,
                    size: 12,
                    color: Colors.grey,
                  ),
                ),
                Flexible(
                  child: Text(
                    _formatRupiah(saldoSesudah),
                    style: const TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.bold,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
              ],
            ),
          ],
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
            Icon(
              Icons.receipt_long_outlined,
              size: 64,
              color: Colors.grey[400],
            ),
            const SizedBox(height: 16),
            Text(
              'Belum ada transaksi',
              style: TextStyle(fontSize: 16, color: Colors.grey[600]),
            ),
          ],
        ),
      ),
    );
  }
}
