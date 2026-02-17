// lib/features/profil/profil_page.dart

import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../core/api/api_service.dart';

class ProfilPage extends StatefulWidget {
  const ProfilPage({super.key});

  @override
  State<ProfilPage> createState() => _ProfilPageState();
}

class _ProfilPageState extends State<ProfilPage> {
  final _api = ApiService();
  Map<String, dynamic>? _santriData;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadProfile();
  }

  Future<void> _loadProfile() async {
    setState(() => _isLoading = true);

    // Ambil dari cache dulu untuk UX yang cepat
    final prefs = await SharedPreferences.getInstance();
    final cachedData = prefs.getString('santri_data');

    if (cachedData != null) {
      if (mounted) {
        setState(() {
          _santriData = json.decode(cachedData);
          _isLoading = false;
        });
      }
    }

    // Refresh dari API di background
    final result = await _api.getProfile();

    if (mounted && result['success'] == true && result['data'] != null) {
      setState(() {
        _santriData = result['data'];
        _isLoading = false;
      });
    } else if (mounted && _santriData == null) {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[100],
      appBar: AppBar(
        title: const Text('Profil Santri'),
        backgroundColor: const Color(0xFF7C3AED),
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: _isLoading && _santriData == null
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _loadProfile,
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                child: Column(
                  children: [
                    // Header dengan foto
                    _buildHeader(),
                    
                    // Content
                    Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Informasi Dasar
                          _buildSectionCard(
                            title: 'Informasi Dasar',
                            icon: Icons.info_outline,
                            children: [
                              _buildInfoRow('ID Santri', _santriData?['id_santri']),
                              _buildInfoRow('NIS', _santriData?['nis']),
                              _buildInfoRow('Nama Lengkap', _santriData?['nama_lengkap']),
                              _buildInfoRow('Jenis Kelamin', _santriData?['jenis_kelamin']),
                              _buildInfoRow('Status', _santriData?['status'], isLast: true),
                            ],
                          ),
                          const SizedBox(height: 16),

                          // Kelas yang Diikuti (NEW)
                          if (_santriData?['kelas_list'] != null && (_santriData!['kelas_list'] as List).isNotEmpty)
                            _buildKelasListSection(),
                          if (_santriData?['kelas_list'] != null && (_santriData!['kelas_list'] as List).isNotEmpty)
                            const SizedBox(height: 16),

                          // Alamat & Asal
                          _buildSectionCard(
                            title: 'Alamat & Asal',
                            icon: Icons.location_on_outlined,
                            children: [
                              _buildInfoRow('Alamat Santri', _santriData?['alamat_santri'], isMultiline: true),
                              _buildInfoRow('Daerah Asal', _santriData?['daerah_asal'], isLast: true),
                            ],
                          ),
                          const SizedBox(height: 16),

                          // Data Orang Tua / Wali
                          _buildSectionCard(
                            title: 'Data Orang Tua / Wali',
                            icon: Icons.family_restroom,
                            children: [
                              _buildInfoRow('Nama Orang Tua', _santriData?['nama_orang_tua']),
                              _buildInfoRow('Nomor HP Orang Tua', _santriData?['nomor_hp_ortu'], isLast: true),
                            ],
                          ),
                          const SizedBox(height: 24),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildHeader() {
    final namaLengkap = _santriData?['nama_lengkap'] ?? 'Nama Santri';
    final idSantri = _santriData?['id_santri'] ?? '-';
    final status = _santriData?['status'] ?? 'Aktif';

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
          bottomLeft: Radius.circular(32),
          bottomRight: Radius.circular(32),
        ),
      ),
      child: Padding(
        padding: const EdgeInsets.fromLTRB(24, 0, 24, 32),
        child: Column(
          children: [
            // Avatar
            Container(
              padding: const EdgeInsets.all(4),
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                border: Border.all(color: Colors.white, width: 3),
              ),
              child: CircleAvatar(
                radius: 50,
                backgroundColor: Colors.white,
                child: Icon(
                  Icons.person,
                  size: 50,
                  color: const Color(0xFF7C3AED).withValues(alpha: 0.7),
                ),
              ),
            ),
            const SizedBox(height: 16),

            // Nama
            Text(
              namaLengkap,
              style: const TextStyle(
                fontSize: 22,
                fontWeight: FontWeight.bold,
                color: Colors.white,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 4),

            // ID Santri
            Text(
              idSantri,
              style: const TextStyle(
                fontSize: 14,
                color: Colors.white70,
              ),
            ),
            const SizedBox(height: 12),

            // Primary Kelas Badge (NEW)
            _buildPrimaryKelasBadge(),
            const SizedBox(height: 8),

            // Status Badge
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
              decoration: BoxDecoration(
                color: status == 'Aktif' ? Colors.green : Colors.orange,
                borderRadius: BorderRadius.circular(20),
              ),
              child: Text(
                status,
                style: const TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                  color: Colors.white,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSectionCard({
    required String title,
    required IconData icon,
    required List<Widget> children,
  }) {
    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Section Title
            Row(
              children: [
                Icon(icon, size: 20, color: const Color(0xFF7C3AED)),
                const SizedBox(width: 8),
                Text(
                  title,
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                    color: Color(0xFF7C3AED),
                  ),
                ),
              ],
            ),
            const Divider(height: 24),
            // Content
            ...children,
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(String label, String? value, {bool isMultiline = false, bool isLast = false}) {
    return Padding(
      padding: EdgeInsets.only(bottom: isLast ? 0 : 12),
      child: Row(
        crossAxisAlignment: isMultiline ? CrossAxisAlignment.start : CrossAxisAlignment.center,
        children: [
          SizedBox(
            width: 130,
            child: Text(
              label,
              style: TextStyle(
                fontSize: 14,
                color: Colors.grey[600],
              ),
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
            ),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: Text(
              value ?? '-',
              style: const TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w600,
              ),
              maxLines: isMultiline ? 4 : 2,
              overflow: TextOverflow.ellipsis,
            ),
          ),
        ],
      ),
    );
  }

  // ==========================================
  // METHODS FOR MULTI-CLASS DISPLAY
  // ==========================================

  /// Build primary kelas badge with class count
  Widget _buildPrimaryKelasBadge() {
    final kelasName = _santriData?['kelas'] ?? '-';
    final kelasList = _santriData?['kelas_list'] as List?;
    
    // Count total kelas
    int totalKelas = 0;
    if (kelasList != null) {
      for (var kelompok in kelasList) {
        final kelasDalam = kelompok['kelas'] as List? ?? [];
        totalKelas += kelasDalam.length;
      }
    }

    return Column(
      children: [
        // Primary class badge
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          decoration: BoxDecoration(
            color: Colors.white.withValues(alpha: 0.2),
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: Colors.white.withValues(alpha: 0.3), width: 1.5),
          ),
          child: Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Icon(Icons.school, color: Colors.white, size: 16),
              const SizedBox(width: 6),
              Text(
                kelasName,
                style: const TextStyle(
                  fontSize: 13,
                  fontWeight: FontWeight.bold,
                  color: Colors.white,
                ),
              ),
            ],
          ),
        ),
        // Class count hint
        if (totalKelas > 1) ...[
          const SizedBox(height: 6),
          Text(
            '+${totalKelas - 1} kelas lainnya',
            style: TextStyle(
              fontSize: 11,
              color: Colors.white.withValues(alpha: 0.8),
            ),
          ),
        ],
      ],
    );
  }

  /// Build kelas list section with ExpansionTile grouped by kelompok
  Widget _buildKelasListSection() {
    final kelasList = _santriData?['kelas_list'] as List? ?? [];

    if (kelasList.isEmpty) {
      return _buildSectionCard(
        title: 'Kelas yang Diikuti',
        icon: Icons.class_,
        children: [
          Center(
            child: Padding(
              padding: const EdgeInsets.all(16.0),
              child: Text(
                'Belum mengikuti kelas apapun',
                style: TextStyle(
                  color: Colors.grey[600],
                  fontSize: 14,
                ),
              ),
            ),
          ),
        ],
      );
    }

    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Section Title
            Row(
              children: [
                const Icon(Icons.class_, size: 20, color: Color(0xFF7C3AED)),
                const SizedBox(width: 8),
                const Text(
                  'Kelas yang Diikuti',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                    color: Color(0xFF7C3AED),
                  ),
                ),
              ],
            ),
            const Divider(height: 24),
            // Kelompok list
            ...kelasList.asMap().entries.map((entry) {
              final index = entry.key;
              final kelompok = entry.value;
              final kelompokName = kelompok['kelompok_name'] ?? 'Unknown';
              final kelasItems = kelompok['kelas'] as List? ?? [];
              
              return Column(
                children: [
                  if (index > 0) const SizedBox(height: 8),
                  _buildKelompokExpansionTile(kelompokName, kelasItems),
                ],
              );
            }),
          ],
        ),
      ),
    );
  }

  /// Build ExpansionTile for each kelompok
  Widget _buildKelompokExpansionTile(String kelompokName, List kelasItems) {
    final color = _getKelompokColor(kelompokName);
    final icon = _getKelompokIcon(kelompokName);

    return Container(
decoration: BoxDecoration(
        border: Border.all(color: color.withValues(alpha: 0.3)),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Theme(
        data: Theme.of(context).copyWith(dividerColor: Colors.transparent),
        child: ExpansionTile(
          tilePadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
          childrenPadding: const EdgeInsets.fromLTRB(12, 0, 12, 8),
          leading: Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: color.withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Icon(icon, color: color, size: 20),
          ),
          title: Text(
            kelompokName,
            style: TextStyle(
              fontSize: 15,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
          subtitle: Text(
            '${kelasItems.length} kelas',
            style: TextStyle(
              fontSize: 12,
              color: Colors.grey[600],
            ),
          ),
          children: kelasItems.map((kelas) {
            final namaKelas = kelas['nama_kelas'] ?? '-';
            final kodeKelas = kelas['kode_kelas'] ?? '-';
            final isPrimary = kelas['is_primary'] == true;

            return Container(
              margin: const EdgeInsets.only(top: 8),
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: isPrimary 
                    ? color.withValues(alpha: 0.1) 
                    : Colors.grey.withValues(alpha: 0.05),
                borderRadius: BorderRadius.circular(8),
                border: isPrimary 
                    ? Border.all(color: color.withValues(alpha: 0.3), width: 1.5) 
                    : null,
              ),
              child: Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          namaKelas,
                          style: TextStyle(
                            fontSize: 14,
                            fontWeight: isPrimary ? FontWeight.bold : FontWeight.w600,
                            color: isPrimary ? color : Colors.black87,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          kodeKelas,
                          style: TextStyle(
                            fontSize: 11,
                            color: Colors.grey[600],
                          ),
                        ),
                      ],
                    ),
                  ),
                  if (isPrimary) ...[
                    const SizedBox(width: 8),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: const Color(0xFFfbbf24),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: const Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(Icons.star, color: Colors.white, size: 12),
                          SizedBox(width: 4),
                          Text(
                            'Utama',
                            style: TextStyle(
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                              color: Colors.white,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ],
              ),
            );
          }).toList(),
        ),
      ),
    );
  }

  /// Get color for kelompok
  Color _getKelompokColor(String kelompokName) {
    final name = kelompokName.toLowerCase();
    if (name.contains('pb') || name.contains('pondok')) {
      return const Color(0xFF3b82f6); // Blue
    } else if (name.contains('lambatan')) {
      return const Color(0xFFfb923c); // Orange
    } else if (name.contains('cepatan')) {
      return const Color(0xFF10b981); // Green
    } else if (name.contains('tahfidz') || name.contains('tahfid')) {
      return const Color(0xFF7C3AED); // Purple
    } else if (name.contains('hadist') || name.contains('hadis')) {
      return const Color(0xFF14b8a6); // Teal
    } else {
      return const Color(0xFF6b7280); // Gray
    }
  }

  /// Get icon for kelompok
  IconData _getKelompokIcon(String kelompokName) {
    final name = kelompokName.toLowerCase();
    if (name.contains('pb') || name.contains('pondok')) {
      return Icons.school;
    } else if (name.contains('lambatan')) {
      return Icons.menu_book;
    } else if (name.contains('cepatan')) {
      return Icons.speed;
    } else if (name.contains('tahfidz') || name.contains('tahfid')) {
      return Icons.auto_stories;
    } else if (name.contains('hadist') || name.contains('hadis')) {
      return Icons.import_contacts;
    } else {
      return Icons.class_;
    }
  }
}
