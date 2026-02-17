// lib/features/dashboard/dashboard_page.dart

import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../core/api/api_service.dart';

class DashboardPage extends StatefulWidget {
  const DashboardPage({super.key});

  @override
  State<DashboardPage> createState() => _DashboardPageState();
}

class _DashboardPageState extends State<DashboardPage> {
  final _api = ApiService();
  Map<String, dynamic>? _santriData;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    final prefs = await SharedPreferences.getInstance();
    
    // Load santri data dari cache dulu
    final santriJson = prefs.getString('santri_data');
    if (santriJson != null) {
      setState(() {
        _santriData = json.decode(santriJson);
        _isLoading = false;
      });
    } else {
      await _fetchProfile();
    }
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
        if (result['message'] != null) {
          _showSnackBar(result['message']);
        }
      }
    }
  }

  Future<void> _handleLogout() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Row(
          children: [
            const Icon(Icons.logout, color: Colors.red, size: 22),
            const SizedBox(width: 8),
            Flexible(
              child: Text(
                'Konfirmasi Logout',
                style: Theme.of(context).textTheme.titleLarge,
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),
            ),
          ],
        ),
        content: const Text(
          'Apakah Anda yakin ingin keluar dari aplikasi?',
          maxLines: 3,
          overflow: TextOverflow.ellipsis,
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.red,
              foregroundColor: Colors.white,
            ),
            child: const Text('Logout'),
          ),
        ],
      ),
    );

    if (confirm == true && mounted) {
      await _api.logout();
      if (mounted) {
        Navigator.pushNamedAndRemoveUntil(context, '/login', (route) => false);
      }
    }
  }

  void _showSnackBar(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(
          message,
          maxLines: 2,
          overflow: TextOverflow.ellipsis,
        ),
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

  void _showComingSoon(String feature) {
    _showSnackBar('$feature - Coming soon');
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[100],
      appBar: AppBar(
        title: const Text('Dashboard Wali'),
        backgroundColor: const Color(0xFF7C3AED),
        foregroundColor: Colors.white,
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.logout),
            onPressed: _handleLogout,
            tooltip: 'Logout',
          ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _fetchProfile,
              child: LayoutBuilder(
                builder: (context, constraints) {
                  return SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    child: Column(
                      children: [
                        // Header dengan info Wali dan Santri
                        _buildHeader(),
                        
                        // Menu Grid
                        Padding(
                          padding: EdgeInsets.symmetric(
                            horizontal: constraints.maxWidth > 600 ? 24 : 16,
                            vertical: 16,
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text(
                                'Menu',
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              const SizedBox(height: 16),
                              _buildMenuGrid(constraints),
                            ],
                          ),
                        ),
                      ],
                    ),
                  );
                },
              ),
            ),
    );
  }

  Widget _buildHeader() {
    final namaLengkap = _santriData?['nama_lengkap'] ?? 'Nama Santri';
    final idSantri = _santriData?['id_santri'] ?? '-';
    final status = _santriData?['status'] ?? 'Aktif';
    final kelas = _santriData?['kelas'] ?? '-';

    return Container(
      width: double.infinity,
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors: [
            Color(0xFF7C3AED),
            Color(0xFF5B21B6),
          ],
        ),
        borderRadius: BorderRadius.only(
          bottomLeft: Radius.circular(24),
          bottomRight: Radius.circular(24),
        ),
      ),
      child: SafeArea(
        top: false,
        child: Padding(
          padding: const EdgeInsets.fromLTRB(16, 0, 16, 20),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              // Banner Wali Santri
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: 0.2),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: const Text(
                  'Wali Santri dari:',
                  style: TextStyle(
                    color: Colors.white70,
                    fontSize: 12,
                  ),
                ),
              ),
              const SizedBox(height: 16),
              
              // Card Info Santri
              Container(
                padding: const EdgeInsets.all(14),
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
                child: Row(
                  children: [
                    // Avatar
                    CircleAvatar(
                      radius: 32,
                      backgroundColor: const Color(0xFF7C3AED).withValues(alpha: 0.1),
                      child: const Icon(
                        Icons.person,
                        size: 36,
                        color: Color(0xFF7C3AED),
                      ),
                    ),
                    const SizedBox(width: 12),
                    
                    // Info
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Text(
                            namaLengkap,
                            style: const TextStyle(
                              fontSize: 17,
                              fontWeight: FontWeight.bold,
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                          const SizedBox(height: 4),
                          Text(
                            'ID: $idSantri • Kelas: $kelas',
                            style: TextStyle(
                              fontSize: 13,
                              color: Colors.grey[600],
                            ),
                          ),
                          const SizedBox(height: 8),
                          Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: 10,
                              vertical: 4,
                            ),
                            decoration: BoxDecoration(
                              color: status == 'Aktif'
                                  ? Colors.green[100]
                                  : Colors.orange[100],
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Text(
                              status,
                              style: TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.w600,
                                color: status == 'Aktif'
                                    ? Colors.green[800]
                                    : Colors.orange[800],
                              ),
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

  Widget _buildMenuGrid(BoxConstraints constraints) {
    final menuItems = [
      _MenuItem(
        icon: Icons.person,
        title: 'Profil Santri',
        color: Colors.blue,
        onTap: () => Navigator.pushNamed(context, '/profil'),
      ),
      _MenuItem(
        icon: Icons.account_balance_wallet,
        title: 'Uang Saku',
        color: Colors.green,
        onTap: () => Navigator.pushNamed(context, '/uang-saku'),
      ),
      _MenuItem(
        icon: Icons.warning_amber,
        title: 'Pelanggaran',
        color: Colors.orange,
        onTap: () => Navigator.pushNamed(context, '/pelanggaran'),
      ),
      _MenuItem(
        icon: Icons.article,
        title: 'Berita',
        color: Colors.purple,
        onTap: () => Navigator.pushNamed(context, '/berita'),
      ),
      _MenuItem(
        icon: Icons.medical_services,
        title: 'Kesehatan',
        color: Colors.red,
        onTap: () => Navigator.pushNamed(context, '/kesehatan'),
      ),
      _MenuItem(
        icon: Icons.flight_takeoff,
        title: 'Kepulangan',
        color: Colors.teal,
        onTap: () => Navigator.pushNamed(context, '/kepulangan'),
      ),
      _MenuItem(
        icon: Icons.emoji_events,
        title: 'Capaian',
        color: Colors.amber,
        onTap: () => Navigator.pushNamed(context, '/capaian'),
      ),
      _MenuItem(
        icon: Icons.calendar_today,
        title: 'Absensi',
        color: Colors.indigo,
        onTap: () => Navigator.pushNamed(context, '/absensi'),
      ),
      _MenuItem(
        icon: Icons.payments,
        title: 'SPP',
        color: Colors.cyan,
        onTap: () => Navigator.pushNamed(context, '/spp'),
      ),
    ];

    final crossAxisCount = constraints.maxWidth > 600 ? 4 : 3;

    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: crossAxisCount,
        mainAxisSpacing: 12,
        crossAxisSpacing: 12,
        childAspectRatio: 1,
      ),
      itemCount: menuItems.length,
      itemBuilder: (context, index) {
        final item = menuItems[index];
        return _buildMenuCard(item);
      },
    );
  }

  Widget _buildMenuCard(_MenuItem item) {
    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
      ),
      child: InkWell(
        onTap: item.onTap,
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: const EdgeInsets.all(8),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                width: 44,
                height: 44,
                decoration: BoxDecoration(
                  color: item.color.withValues(alpha: 0.15),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(
                  item.icon,
                  size: 24,
                  color: item.color,
                ),
              ),
              const SizedBox(height: 6),
              Flexible(
                child: Text(
                  item.title,
                  style: const TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.w600,
                  ),
                  textAlign: TextAlign.center,
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _MenuItem {
  final IconData icon;
  final String title;
  final Color color;
  final VoidCallback onTap;

  _MenuItem({
    required this.icon,
    required this.title,
    required this.color,
    required this.onTap,
  });
}