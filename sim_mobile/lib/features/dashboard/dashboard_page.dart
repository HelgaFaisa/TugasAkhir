// lib/features/dashboard/dashboard_page.dart

import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../core/api/api_service.dart';

// ─── Color palette ───
const _primary = Color(0xFF6FBA9D);
const _primaryDark = Color(0xFF4D987B);
const _primaryLight = Color(0xFFE8F7F2);
const _bg = Color(0xFFF8FBF9);

class DashboardPage extends StatefulWidget {
  const DashboardPage({super.key});

  @override
  State<DashboardPage> createState() => _DashboardPageState();
}

class _DashboardPageState extends State<DashboardPage> {
  final _api = ApiService();
  Map<String, dynamic>? _santriData;
  bool _isLoading = true;
  bool _saldoVisible = false;

  // Summary data
  String _saldo = '-';
  String _sppStatus = '-';
  bool _sppLunas = false;
  List<Map<String, dynamic>> _beritaList = [];

  // Kepulangan data
  bool _sedangPulang = false;
  String _tanggalKembali = '';
  int _sisaHari = 0;
  String _statusKepulangan = '';

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  // ─── Data loading ───
  Future<void> _loadData() async {
    final prefs = await SharedPreferences.getInstance();
    final santriJson = prefs.getString('santri_data');
    if (santriJson != null) {
      setState(() {
        _santriData = json.decode(santriJson);
        _isLoading = false;
      });
    } else {
      await _fetchProfile();
    }
    _fetchSummaries();
  }

  Future<void> _fetchProfile() async {
    setState(() => _isLoading = true);
    final result = await _api.getProfile();
    if (mounted) {
      if (result['success'] == true && result['data'] != null) {
        setState(() {
          _santriData = result['data'];
          _isLoading = false;
        });
      } else {
        setState(() => _isLoading = false);
        if (result['message'] != null) _showSnackBar(result['message']);
      }
    }
  }

  Future<void> _fetchSummaries() async {
    // Saldo uang saku
    try {
      final saldoRes = await _api.getSaldoUangSaku();
      if (mounted && saldoRes['success'] == true) {
        final data = saldoRes['data'];
        if (data != null && data is Map) {
          final saldoVal = data['saldo'] ?? data['total_saldo'] ?? 0;
          setState(() => _saldo = _formatRupiah(saldoVal));
        }
      }
    } catch (_) {}

    // SPP bulan ini
    try {
      final sppRes = await _api.getStatusSppBulanIni();
      if (mounted && sppRes['success'] == true) {
        final data = sppRes['data'];
        if (data != null && data is Map) {
          final lunas = data['status'] == 'Lunas' || data['lunas'] == true;
          setState(() {
            _sppLunas = lunas;
            _sppStatus = lunas ? 'Lunas' : 'Belum Lunas';
          });
        }
      }
    } catch (_) {}

    // Berita terbaru
    try {
      final beritaRes = await _api.getBerita(page: 1);
      if (mounted && beritaRes['success'] == true) {
        final raw = beritaRes['data'];
        List items = [];
        if (raw is List) {
          items = raw;
        } else if (raw is Map && raw['data'] is List) {
          items = raw['data'] as List;
        }
        if (items.isNotEmpty) {
          setState(() {
            _beritaList = items
                .take(3)
                .whereType<Map<String, dynamic>>()
                .toList();
          });
        }
      }
    } catch (_) {}

    // Notifikasi kepulangan
    try {
      final kepRes = await _api.getNotifikasiKepulangan();
      if (mounted && kepRes['success'] == true) {
        final data = kepRes['data'];
        if (data != null && data is Map) {
          setState(() {
            _sedangPulang = data['sedang_pulang'] == true;
            _tanggalKembali = data['tanggal_kembali_formatted'] ??
                data['tanggal_kembali'] ??
                '';
            _sisaHari = data['sisa_hari'] ?? 0;
            _statusKepulangan = data['status'] ?? '';
          });
        }
      }
    } catch (_) {}
  }

  String _formatRupiah(dynamic value) {
    final n = int.tryParse(value.toString()) ?? 0;
    final s = n.abs().toString();
    final buf = StringBuffer();
    for (var i = 0; i < s.length; i++) {
      if (i > 0 && (s.length - i) % 3 == 0) buf.write('.');
      buf.write(s[i]);
    }
    return 'Rp ${n < 0 ? '-' : ''}${buf.toString()}';
  }

  Future<void> _handleLogout() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Row(
          children: [
            Icon(Icons.logout_rounded, color: Colors.redAccent, size: 22),
            SizedBox(width: 8),
            Text('Konfirmasi Logout', style: TextStyle(fontSize: 16)),
          ],
        ),
        content: const Text('Yakin ingin keluar dari aplikasi?'),
        actionsPadding: const EdgeInsets.fromLTRB(16, 0, 16, 12),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: Text('Batal', style: TextStyle(color: Colors.grey[600])),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(ctx, true),
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.redAccent,
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10)),
            ),
            child: const Text('Logout'),
          ),
        ],
      ),
    );
    if (confirm == true && mounted) {
      await _api.logout();
      if (mounted) {
        Navigator.pushNamedAndRemoveUntil(context, '/login', (r) => false);
      }
    }
  }

  void _showSnackBar(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message, maxLines: 2, overflow: TextOverflow.ellipsis),
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
      ),
    );
  }

  // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  //  BUILD
  // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: _bg,
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: _primary))
          : RefreshIndicator(
              color: _primary,
              onRefresh: () async {
                await _fetchProfile();
                await _fetchSummaries();
              },
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _buildHeader(),
                    const SizedBox(height: 16),
                    if (_sedangPulang) ...[
                      _buildKepulanganBanner(),
                      const SizedBox(height: 16),
                    ],
                    _buildSummaryCards(),
                    const SizedBox(height: 20),
                    _buildBeritaSection(),
                    const SizedBox(height: 20),
                    _buildMenuSection(),
                    const SizedBox(height: 24),
                  ],
                ),
              ),
            ),
    );
  }

  // ━━━ 1) HEADER ━━━
  Widget _buildHeader() {
    final nama = _santriData?['nama_lengkap'] ?? 'Santri';
    final kelas = _santriData?['kelas'] ?? '-';
    final status = _santriData?['status'] ?? 'Aktif';
    final namaWali =
        _santriData?['nama_wali'] ?? _santriData?['wali'] ?? '';

    return Container(
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [_primary, _primaryDark],
        ),
        borderRadius: BorderRadius.only(
          bottomLeft: Radius.circular(28),
          bottomRight: Radius.circular(28),
        ),
      ),
      child: SafeArea(
        bottom: false,
        child: Padding(
          padding: const EdgeInsets.fromLTRB(20, 12, 20, 20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // ── top row: greeting + actions ──
              Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Halo! \u{1F44B}',
                          style: TextStyle(
                            fontSize: 14,
                            color: Colors.white.withValues(alpha: 0.8),
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          namaWali.isNotEmpty ? namaWali : 'Wali Santri',
                          style: const TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                            color: Colors.white,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ],
                    ),
                  ),
                  _headerIcon(Icons.notifications_none_rounded, () {}),
                  const SizedBox(width: 8),
                  _headerIcon(Icons.logout_rounded, _handleLogout),
                ],
              ),

              const SizedBox(height: 16),

              // ── santri info card ──
              Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(16),
                  boxShadow: [
                    BoxShadow(
                      color: _primaryDark.withValues(alpha: 0.18),
                      blurRadius: 12,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Row(
                  children: [
                    Container(
                      width: 50,
                      height: 50,
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(
                            colors: [_primary, _primaryDark]),
                        borderRadius: BorderRadius.circular(14),
                      ),
                      child: const Icon(Icons.person_rounded,
                          color: Colors.white, size: 26),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            nama,
                            style: const TextStyle(
                              fontSize: 15,
                              fontWeight: FontWeight.w700,
                              color: Color(0xFF1A1A2E),
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                          const SizedBox(height: 4),
                          Text('Kelas $kelas',
                              style: TextStyle(
                                  fontSize: 12, color: Colors.grey[600])),
                        ],
                      ),
                    ),
                    // status badge
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 10, vertical: 5),
                      decoration: BoxDecoration(
                        color: status == 'Aktif'
                            ? _primaryLight
                            : const Color(0xFFFFF3E0),
                        borderRadius: BorderRadius.circular(20),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Container(
                            width: 6,
                            height: 6,
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              color: status == 'Aktif'
                                  ? _primary
                                  : Colors.orange,
                            ),
                          ),
                          const SizedBox(width: 5),
                          Text(
                            status,
                            style: TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.w600,
                              color: status == 'Aktif'
                                  ? _primaryDark
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
        ),
      ),
    );
  }

  Widget _headerIcon(IconData icon, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: 36,
        height: 36,
        decoration: BoxDecoration(
          color: Colors.white.withValues(alpha: 0.18),
          borderRadius: BorderRadius.circular(11),
        ),
        child: Icon(icon, color: Colors.white, size: 20),
      ),
    );
  }

  // ━━━ KEPULANGAN BANNER ━━━
  Widget _buildKepulanganBanner() {
    final isLate = _statusKepulangan == 'terlambat';
    final bgColor =
        isLate ? const Color(0xFFFEE2E2) : const Color(0xFFFFF7ED);
    final accentColor =
        isLate ? const Color(0xFFDC2626) : const Color(0xFFF59E0B);
    final textColor =
        isLate ? const Color(0xFF991B1B) : const Color(0xFF92400E);

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20),
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: bgColor,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: accentColor.withValues(alpha: 0.3)),
        ),
        child: Row(
          children: [
            Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(
                color: accentColor.withValues(alpha: 0.15),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(
                isLate ? Icons.warning_rounded : Icons.home_rounded,
                color: accentColor,
                size: 24,
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    isLate ? 'Terlambat Kembali!' : 'Sedang Pulang',
                    style: TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.w700,
                      color: textColor,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    isLate
                        ? 'Sudah lewat ${_sisaHari.abs()} hari dari jadwal kembali'
                        : 'Kembali $_tanggalKembali ($_sisaHari hari lagi)',
                    style: TextStyle(
                        fontSize: 12,
                        color: textColor.withValues(alpha: 0.8)),
                  ),
                ],
              ),
            ),
            Container(
              padding:
                  const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
              decoration: BoxDecoration(
                color: accentColor,
                borderRadius: BorderRadius.circular(20),
              ),
              child: Text(
                isLate ? 'Terlambat' : '$_sisaHari hari',
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 11,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  // ━━━ 2) SUMMARY CARDS ━━━
  Widget _buildSummaryCards() {
    return SizedBox(
      height: 100,
      child: ListView(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 20),
        children: [
          _summaryCard(
            icon: Icons.account_balance_wallet_rounded,
            label: 'Saldo Uang Saku',
            value: _saldoVisible
                ? _saldo
                : '\u2022\u2022\u2022\u2022\u2022\u2022',
            gradient: const [_primary, _primaryDark],
            trailing: GestureDetector(
              onTap: () =>
                  setState(() => _saldoVisible = !_saldoVisible),
              child: Icon(
                _saldoVisible
                    ? Icons.visibility_rounded
                    : Icons.visibility_off_rounded,
                color: Colors.white70,
                size: 20,
              ),
            ),
          ),
          const SizedBox(width: 12),
          _summaryCard(
            icon: Icons.receipt_long_rounded,
            label: 'SPP Bulan Ini',
            value: _sppStatus,
            gradient: _sppLunas
                ? [const Color(0xFF10B981), const Color(0xFF059669)]
                : [const Color(0xFFF59E0B), const Color(0xFFD97706)],
            trailing: Icon(
              _sppLunas
                  ? Icons.check_circle_rounded
                  : Icons.warning_rounded,
              color: Colors.white70,
              size: 20,
            ),
          ),
        ],
      ),
    );
  }

  Widget _summaryCard({
    required IconData icon,
    required String label,
    required String value,
    required List<Color> gradient,
    Widget? trailing,
  }) {
    return Container(
      width: 200,
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: gradient,
        ),
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: gradient.first.withValues(alpha: 0.3),
            blurRadius: 8,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
            children: [
              Icon(icon, color: Colors.white, size: 18),
              const SizedBox(width: 6),
              Expanded(
                child: Text(label,
                    style: const TextStyle(
                        color: Colors.white70, fontSize: 11),
                    overflow: TextOverflow.ellipsis),
              ),
              if (trailing != null) trailing,
            ],
          ),
          Text(
            value,
            style: const TextStyle(
              color: Colors.white,
              fontSize: 18,
              fontWeight: FontWeight.bold,
              letterSpacing: 0.5,
            ),
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
          ),
        ],
      ),
    );
  }

  // ━━━ 3) BERITA TERBARU ━━━
  Widget _buildBeritaSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 20),
          child: Row(
            children: [
              _sectionTitle('Berita Terbaru', Icons.article_rounded),
              const Spacer(),
              GestureDetector(
                onTap: () => Navigator.pushNamed(context, '/berita'),
                child: const Text('Lihat Semua',
                    style: TextStyle(
                        fontSize: 12,
                        color: _primary,
                        fontWeight: FontWeight.w600)),
              ),
            ],
          ),
        ),
        const SizedBox(height: 12),
        SizedBox(
          height: 140,
          child: _beritaList.isEmpty
              ? Center(
                  child: Text('Belum ada berita',
                      style: TextStyle(
                          color: Colors.grey[400], fontSize: 12)))
              : ListView.separated(
                  scrollDirection: Axis.horizontal,
                  padding: const EdgeInsets.symmetric(horizontal: 20),
                  itemCount: _beritaList.length,
                  separatorBuilder: (_, __) => const SizedBox(width: 12),
                  itemBuilder: (_, i) => _beritaCard(_beritaList[i]),
                ),
        ),
      ],
    );
  }

  Widget _beritaCard(Map<String, dynamic> b) {
    final judul = b['judul'] ?? b['title'] ?? 'Berita';
    final tanggal = b['tanggal'] ?? b['created_at'] ?? '';
    final kategori = b['kategori'] ?? b['category'] ?? '';

    return GestureDetector(
      onTap: () => Navigator.pushNamed(context, '/berita'),
      child: Container(
        width: 220,
        padding: const EdgeInsets.all(14),
        decoration: _cardDeco(),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (kategori.toString().isNotEmpty)
              Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                decoration: BoxDecoration(
                  color: _primaryLight,
                  borderRadius: BorderRadius.circular(6),
                ),
                child: Text(kategori.toString(),
                    style: const TextStyle(
                        fontSize: 9,
                        color: _primaryDark,
                        fontWeight: FontWeight.w600)),
              ),
            if (kategori.toString().isNotEmpty) const SizedBox(height: 8),
            Expanded(
              child: Text(
                judul.toString(),
                style: const TextStyle(
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                  color: Color(0xFF1A1A2E),
                  height: 1.3,
                ),
                maxLines: 3,
                overflow: TextOverflow.ellipsis,
              ),
            ),
            const SizedBox(height: 6),
            Row(
              children: [
                Icon(Icons.access_time_rounded,
                    size: 12, color: Colors.grey[400]),
                const SizedBox(width: 4),
                Expanded(
                  child: Text(
                    tanggal.toString().length > 10
                        ? tanggal.toString().substring(0, 10)
                        : tanggal.toString(),
                    style:
                        TextStyle(fontSize: 10, color: Colors.grey[400]),
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

  // ━━━ 4) MENU LAINNYA ━━━
  Widget _buildMenuSection() {
    final items = [
      _QMenu(Icons.payments_rounded, 'SPP', '/spp'),
      _QMenu(Icons.account_balance_wallet_rounded, 'Uang Saku', '/uang-saku'),
      _QMenu(Icons.calendar_month_rounded, 'Absensi', '/absensi'),
      _QMenu(Icons.warning_amber_rounded, 'Pelanggaran', '/pelanggaran'),
      _QMenu(Icons.local_hospital_rounded, 'Kesehatan', '/kesehatan'),
      _QMenu(Icons.flight_takeoff_rounded, 'Kepulangan', '/kepulangan'),
    ];

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _sectionTitle('Menu Lainnya', Icons.grid_view_rounded),
          const SizedBox(height: 12),
          GridView.count(
            crossAxisCount: 3,
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            mainAxisSpacing: 12,
            crossAxisSpacing: 12,
            childAspectRatio: 1,
            children: items.map(_menuTile).toList(),
          ),
        ],
      ),
    );
  }

  Widget _menuTile(_QMenu m) {
    return GestureDetector(
      onTap: () => Navigator.pushNamed(context, m.route),
      child: Container(
        decoration: _cardDeco(),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(
                color: _primaryLight,
                borderRadius: BorderRadius.circular(13),
              ),
              child: Icon(m.icon, size: 22, color: _primary),
            ),
            const SizedBox(height: 8),
            Text(
              m.label,
              style: const TextStyle(
                fontSize: 11,
                fontWeight: FontWeight.w600,
                color: Color(0xFF1A1A2E),
              ),
              textAlign: TextAlign.center,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
          ],
        ),
      ),
    );
  }

  // ─── shared helpers ───
  Widget _sectionTitle(String text, IconData icon) {
    return Row(
      children: [
        Icon(icon, size: 18, color: _primary),
        const SizedBox(width: 6),
        Text(text,
            style: const TextStyle(
                fontSize: 15,
                fontWeight: FontWeight.w700,
                color: Color(0xFF1A1A2E))),
      ],
    );
  }

  BoxDecoration _cardDeco() => BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.04),
            blurRadius: 10,
            offset: const Offset(0, 2),
          ),
        ],
      );
}

class _QMenu {
  final IconData icon;
  final String label;
  final String route;
  const _QMenu(this.icon, this.label, this.route);
}