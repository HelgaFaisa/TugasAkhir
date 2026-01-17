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
    _loadLocalData();
  }

  Future<void> _loadLocalData() async {
    final prefs = await SharedPreferences.getInstance();
    final santriJson = prefs.getString('santri_data');

    if (santriJson != null) {
      setState(() {
        _santriData = json.decode(santriJson);
        _isLoading = false;
      });
    } else {
      // Jika tidak ada data lokal, fetch dari API
      await _fetchProfile();
    }
  }

  Future<void> _fetchProfile() async {
    setState(() => _isLoading = true);

    final result = await _api.getProfile();

    if (mounted) {
      if (result['success'] == true) {
        // Simpan data terbaru
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('santri_data', json.encode(result['data']));

        setState(() {
          _santriData = result['data'];
          _isLoading = false;
        });
      } else {
        setState(() => _isLoading = false);
        _showErrorDialog(result['message'] ?? 'Gagal memuat profil');
      }
    }
  }

  Future<void> _handleLogout() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Konfirmasi Logout'),
        content: const Text('Apakah Anda yakin ingin keluar?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text('Logout'),
          ),
        ],
      ),
    );

    if (confirm == true && mounted) {
      await _api.logout();

      if (mounted) {
        Navigator.pushNamedAndRemoveUntil(
          context,
          '/login',
          (route) => false,
        );
      }
    }
  }

  void _showErrorDialog(String message) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Error'),
        content: Text(message),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('OK'),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Dashboard'),
        backgroundColor: Colors.deepPurple,
        foregroundColor: Colors.white,
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
              child: ListView(
                padding: const EdgeInsets.all(16),
                children: [
                  // Card Profil Santri
                  Card(
                    elevation: 4,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        children: [
                          // Foto Profil
                          CircleAvatar(
                            radius: 50,
                            backgroundColor: Colors.deepPurple[100],
                            child: _santriData?['foto_url'] != null
                                ? ClipOval(
                                    child: Image.network(
                                      _santriData!['foto_url'],
                                      width: 100,
                                      height: 100,
                                      fit: BoxFit.cover,
                                      errorBuilder: (_, __, ___) =>
                                          const Icon(Icons.person, size: 50),
                                    ),
                                  )
                                : const Icon(Icons.person, size: 50),
                          ),
                          const SizedBox(height: 16),

                          // Nama
                          Text(
                            _santriData?['nama_lengkap'] ?? 'Nama Santri',
                            style: const TextStyle(
                              fontSize: 20,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          const SizedBox(height: 4),

                          // ID Santri
                          Text(
                            _santriData?['id_santri'] ?? '-',
                            style: TextStyle(
                              fontSize: 14,
                              color: Colors.grey[600],
                            ),
                          ),
                          const SizedBox(height: 8),

                          // Status Badge
                          Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: 12,
                              vertical: 4,
                            ),
                            decoration: BoxDecoration(
                              color: Colors.green[100],
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Text(
                              _santriData?['status'] ?? 'Aktif',
                              style: TextStyle(
                                fontSize: 12,
                                color: Colors.green[800],
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),

                  // Menu Grid
                  GridView.count(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    crossAxisCount: 2,
                    mainAxisSpacing: 12,
                    crossAxisSpacing: 12,
                    children: [
                      _buildMenuCard(
                        icon: Icons.person,
                        title: 'Profil',
                        color: Colors.blue,
                        onTap: () {
                          Navigator.pushNamed(context, '/profil');
                        },
                      ),
                      _buildMenuCard(
                        icon: Icons.wallet,
                        title: 'Uang Saku',
                        color: Colors.green,
                        onTap: () {
                          // TODO: Implement
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(
                                content: Text('Coming soon: Uang Saku')),
                          );
                        },
                      ),
                      _buildMenuCard(
                        icon: Icons.warning,
                        title: 'Pelanggaran',
                        color: Colors.orange,
                        onTap: () {
                          // TODO: Implement
                        },
                      ),
                      _buildMenuCard(
                        icon: Icons.article,
                        title: 'Berita',
                        color: Colors.purple,
                        onTap: () {
                          // TODO: Implement
                        },
                      ),
                      _buildMenuCard(
                        icon: Icons.medical_services,
                        title: 'Kesehatan',
                        color: Colors.red,
                        onTap: () {
                          // TODO: Implement
                        },
                      ),
                      _buildMenuCard(
                        icon: Icons.flight_takeoff,
                        title: 'Kepulangan',
                        color: Colors.teal,
                        onTap: () {
                          // TODO: Implement
                        },
                      ),
                    ],
                  ),
                ],
              ),
            ),
    );
  }

  Widget _buildMenuCard({
    required IconData icon,
    required String title,
    required Color color,
    required VoidCallback onTap,
  }) {
    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
      ),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width: 60,
              height: 60,
              decoration: BoxDecoration(
                color: color.withOpacity(0.2),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(icon, size: 30, color: color),
            ),
            const SizedBox(height: 12),
            Text(
              title,
              style: const TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w600,
              ),
            ),
          ],
        ),
      ),
    );
  }
}