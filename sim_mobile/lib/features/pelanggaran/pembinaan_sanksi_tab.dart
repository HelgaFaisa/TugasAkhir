// lib/features/pelanggaran/pembinaan_sanksi_tab.dart

import 'package:flutter/material.dart';
import '../../core/api/api_service.dart';

class PembinaanSanksiTab extends StatefulWidget {
  const PembinaanSanksiTab({super.key});

  @override
  State<PembinaanSanksiTab> createState() => _PembinaanSanksiTabState();
}

class _PembinaanSanksiTabState extends State<PembinaanSanksiTab> {
  final _api = ApiService();
  
  List<Map<String, dynamic>> _pembinaanList = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadPembinaan();
  }

  Future<void> _loadPembinaan() async {
    setState(() => _isLoading = true);

    final result = await _api.getPembinaanSanksi();

    if (mounted) {
      setState(() => _isLoading = false);
      
      if (result['success'] == true && result['data'] != null) {
        setState(() {
          _pembinaanList = List<Map<String, dynamic>>.from(result['data']);
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return RefreshIndicator(
      onRefresh: _loadPembinaan,
      child: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _pembinaanList.isEmpty
              ? _buildEmptyState()
              : ListView.builder(
                  padding: const EdgeInsets.all(16),
                  itemCount: _pembinaanList.length,
                  itemBuilder: (context, index) {
                    final pembinaan = _pembinaanList[index];
                    return _buildPembinaanCard(pembinaan, index);
                  },
                ),
    );
  }

  Widget _buildPembinaanCard(Map<String, dynamic> pembinaan, int index) {
    final judul = pembinaan['judul'] ?? '';
    final konten = pembinaan['konten'] ?? '';

    return Card(
      margin: const EdgeInsets.only(bottom: 16),
      elevation: 2,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
      ),
      child: InkWell(
        borderRadius: BorderRadius.circular(12),
        onTap: () => _showDetailDialog(pembinaan),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header dengan icon
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      color: const Color(0xFF7C3AED).withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: const Icon(
                      Icons.gavel,
                      color: Color(0xFF7C3AED),
                      size: 20,
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Text(
                      judul,
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                      ),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),

              // Preview Konten
              Text(
                konten,
                style: TextStyle(
                  fontSize: 13,
                  color: Colors.grey[700],
                  height: 1.5,
                ),
                maxLines: 4,
                overflow: TextOverflow.ellipsis,
              ),

              const SizedBox(height: 8),

              // "Baca Selengkapnya"
              Align(
                alignment: Alignment.centerRight,
                child: Text(
                  'Baca selengkapnya →',
                  style: TextStyle(
                    fontSize: 12,
                    color: const Color(0xFF7C3AED),
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _showDetailDialog(Map<String, dynamic> pembinaan) {
    final judul = pembinaan['judul'] ?? '';
    final konten = pembinaan['konten'] ?? '';

    showDialog(
      context: context,
      builder: (context) => Dialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        child: Container(
          constraints: BoxConstraints(
            maxHeight: MediaQuery.of(context).size.height * 0.8,
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              // Header
              Container(
                padding: const EdgeInsets.all(16),
                decoration: const BoxDecoration(
                  color: Color(0xFF7C3AED),
                  borderRadius: BorderRadius.only(
                    topLeft: Radius.circular(16),
                    topRight: Radius.circular(16),
                  ),
                ),
                child: Row(
                  children: [
                    const Icon(
                      Icons.gavel,
                      color: Colors.white,
                      size: 22,
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Text(
                        judul,
                        style: const TextStyle(
                          fontSize: 17,
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
                        ),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                  ],
                ),
              ),

              // Konten
              Flexible(
                child: SingleChildScrollView(
                  padding: const EdgeInsets.all(20),
                  child: Text(
                    konten,
                    style: TextStyle(
                      fontSize: 14,
                      color: Colors.grey[800],
                      height: 1.6,
                    ),
                  ),
                ),
              ),

              // Footer
              Padding(
                padding: const EdgeInsets.all(12),
                child: SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: () => Navigator.pop(context),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF7C3AED),
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 12),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                      ),
                    ),
                    child: const Text('Tutup'),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(
            Icons.article_outlined,
            size: 80,
            color: Colors.grey[400],
          ),
          const SizedBox(height: 16),
          Text(
            'Tidak ada data pembinaan & sanksi',
            style: TextStyle(
              fontSize: 15,
              color: Colors.grey[600],
            ),
          ),
        ],
      ),
    );
  }
}