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

    // Coba ambil dari cache dulu
    final prefs = await SharedPreferences.getInstance();
    final cachedData = prefs.getString('santri_data');

    if (cachedData != null) {
      setState(() {
        _santriData = json.decode(cachedData);
        _isLoading = false;
      });
    }

    // Refresh dari API
    final result = await _api.getProfile();

    if (mounted && result['success'] == true) {
      await prefs.setString('santri_data', json.encode(result['data']));
      setState(() {
        _santriData = result['data'];
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Profil Saya'),
        backgroundColor: Colors.deepPurple,
        foregroundColor: Colors.white,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _loadProfile,
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.all(16),
                child: Column(
                  children: [
                    // Header Card dengan Foto
                    Card(
                      elevation: 4,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(16),
                      ),
                      child: Padding(
                        padding: const EdgeInsets.all(20),
                        child: Column(
                          children: [
                            // Foto Profil
                            CircleAvatar(
                              radius: 60,
                              backgroundColor: Colors.deepPurple[100],
                              child: _santriData?['foto_url'] != null
                                  ? ClipOval(
                                      child: Image.network(
                                        _santriData!['foto_url'],
                                        width: 120,
                                        height: 120,
                                        fit: BoxFit.cover,
                                        errorBuilder: (_, __, ___) =>
                                            const Icon(Icons.person, size: 60),
                                      ),
                                    )
                                  : const Icon(Icons.person, size: 60),
                            ),
                            const SizedBox(height: 16),

                            // Nama
                            Text(
                              _santriData?['nama_lengkap'] ?? '-',
                              style: const TextStyle(
                                fontSize: 22,
                                fontWeight: FontWeight.bold,
                              ),
                              textAlign: TextAlign.center,
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
                            const SizedBox(height: 12),

                            // Status Badge
                            Container(
                              padding: const EdgeInsets.symmetric(
                                horizontal: 16,
                                vertical: 6,
                              ),
                              decoration: BoxDecoration(
                                color: Colors.green[100],
                                borderRadius: BorderRadius.circular(20),
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

                    // Data Diri Section
                    _buildSectionTitle('Data Diri'),
                    _buildInfoCard([
                      _buildInfoRow('NIS', _santriData?['nis'] ?? '-'),
                      _buildInfoRow('Jenis Kelamin',
                          _santriData?['jenis_kelamin'] ?? '-'),
                      _buildInfoRow('Kelas', _santriData?['kelas'] ?? '-'),
                      _buildInfoRow('Daerah Asal',
                          _santriData?['daerah_asal'] ?? '-'),
                    ]),
                    const SizedBox(height: 16),

                    // Alamat Section
                    _buildSectionTitle('Alamat'),
                    _buildInfoCard([
                      _buildInfoRow('Alamat Lengkap',
                          _santriData?['alamat_santri'] ?? '-',
                          isMultiline: true),
                    ]),
                    const SizedBox(height: 16),

                    // Data Orang Tua Section
                    _buildSectionTitle('Data Orang Tua'),
                    _buildInfoCard([
                      _buildInfoRow('Nama Orang Tua',
                          _santriData?['nama_orang_tua'] ?? '-'),
                      _buildInfoRow('Nomor HP Orang Tua',
                          _santriData?['nomor_hp_ortu'] ?? '-'),
                    ]),
                    const SizedBox(height: 16),

                    // Info Tambahan
                    _buildSectionTitle('Informasi Lainnya'),
                    _buildInfoCard([
                      _buildInfoRow('Bergabung Sejak',
                          _santriData?['bergabung_sejak'] ?? '-'),
                    ]),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Align(
      alignment: Alignment.centerLeft,
      child: Padding(
        padding: const EdgeInsets.only(bottom: 8),
        child: Text(
          title,
          style: const TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.bold,
            color: Colors.deepPurple,
          ),
        ),
      ),
    );
  }

  Widget _buildInfoCard(List<Widget> children) {
    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: children,
        ),
      ),
    );
  }

  Widget _buildInfoRow(String label, String value,
      {bool isMultiline = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        crossAxisAlignment:
            isMultiline ? CrossAxisAlignment.start : CrossAxisAlignment.center,
        children: [
          Expanded(
            flex: 2,
            child: Text(
              label,
              style: TextStyle(
                fontSize: 14,
                color: Colors.grey[700],
              ),
            ),
          ),
          Expanded(
            flex: 3,
            child: Text(
              value,
              style: const TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w600,
              ),
              textAlign: isMultiline ? TextAlign.left : TextAlign.right,
            ),
          ),
        ],
      ),
    );
  }
}