// lib/features/kesehatan/kesehatan_detail_page.dart

import 'package:flutter/material.dart';
import '../../core/api/api_service.dart';

class KesehatanDetailPage extends StatefulWidget {
  final String idKesehatan;

  const KesehatanDetailPage({super.key, required this.idKesehatan});

  @override
  State<KesehatanDetailPage> createState() => _KesehatanDetailPageState();
}

class _KesehatanDetailPageState extends State<KesehatanDetailPage> {
  final _api = ApiService();
  Map<String, dynamic>? _kesehatan;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadDetail();
  }

  Future<void> _loadDetail() async {
    setState(() => _isLoading = true);

    final result = await _api.getDetailKesehatan(widget.idKesehatan);

    if (mounted) {
      if (result['success'] == true) {
        setState(() {
          _kesehatan = result['data'];
          _isLoading = false;
        });
      } else {
        setState(() => _isLoading = false);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              result['message'] ?? 'Gagal memuat data',
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
            ),
          ),
        );
        Navigator.pop(context);
      }
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[100],
      appBar: AppBar(
        title: const Text('Detail Kesehatan'),
        backgroundColor: const Color(0xFF6FBA9D),
        foregroundColor: Colors.white,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: const EdgeInsets.all(12),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Status Badge
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 7),
                    decoration: BoxDecoration(
                      color: _getStatusColor(_kesehatan!['status']).withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(15),
                    ),
                    child: Text(
                      _kesehatan!['status'].toString().toUpperCase(),
                      style: TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                        color: _getStatusColor(_kesehatan!['status']),
                      ),
                    ),
                  ),
                  const SizedBox(height: 15),

                  // Tanggal Section
                  _buildInfoCard(
                    'Tanggal Perawatan',
                    Icons.calendar_today,
                    [
                      _buildInfoRow('Tanggal Masuk', _kesehatan!['tanggal_masuk_formatted']),
                      _buildInfoRow(
                        'Tanggal Keluar',
                        _kesehatan!['tanggal_keluar_formatted'] ?? 'Masih dirawat',
                      ),
                      _buildInfoRow('Lama Dirawat', '${_kesehatan!['lama_dirawat']} hari'),
                    ],
                  ),
                  const SizedBox(height: 12),

                  // Keluhan Section
                  _buildInfoCard(
                    'Keluhan',
                    Icons.sick_outlined,
                    [
                      Text(
                        _kesehatan!['keluhan'] ?? '-',
                        style: const TextStyle(fontSize: 11, height: 1.5),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),

                  // Catatan Section
                  if (_kesehatan!['catatan'] != null && _kesehatan!['catatan'].toString().isNotEmpty)
                    _buildInfoCard(
                      'Catatan',
                      Icons.note_alt_outlined,
                      [
                        Text(
                          _kesehatan!['catatan'],
                          style: const TextStyle(fontSize: 11, height: 1.5),
                        ),
                      ],
                    ),
                ],
              ),
            ),
    );
  }

  Widget _buildInfoCard(String title, IconData icon, List<Widget> children) {
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
                Icon(icon, size: 15, color: const Color(0xFF6FBA9D)),
                const SizedBox(width: 7),
                Text (
title,
style: const TextStyle(
fontSize: 12,
fontWeight: FontWeight.bold,
color: Color(0xFF6FBA9D),
),
),
],
),
const Divider(height: 19),
...children,
],
),
),
);
}
Widget _buildInfoRow(String label, String value) {
  return LayoutBuilder(
    builder: (context, constraints) {
      final labelWidth = constraints.maxWidth * 0.35;
      return Padding(
        padding: const EdgeInsets.only(bottom: 9),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            SizedBox(
              width: labelWidth,
              child: Text(
                label,
                style: TextStyle(fontSize: 11, color: Colors.grey[600]),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
            ),
            const SizedBox(width: 7),
            Expanded(
              child: Text(
                value,
                style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600),
                maxLines: 3,
                overflow: TextOverflow.ellipsis,
              ),
            ),
          ],
        ),
      );
    },
  );
}
}