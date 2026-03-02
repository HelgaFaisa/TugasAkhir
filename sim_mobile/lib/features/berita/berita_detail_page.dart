// lib/features/berita/berita_detail_page.dart

import 'package:flutter/material.dart';
import 'package:flutter_widget_from_html_core/flutter_widget_from_html_core.dart';
import '../../core/api/api_service.dart';
import '../../core/widgets/berita_image.dart';

class BeritaDetailPage extends StatefulWidget {
  final String idBerita;

  const BeritaDetailPage({super.key, required this.idBerita});

  @override
  State<BeritaDetailPage> createState() => _BeritaDetailPageState();
}

class _BeritaDetailPageState extends State<BeritaDetailPage> {
  final _api = ApiService();
  Map<String, dynamic>? _berita;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadDetail();
  }

  Future<void> _loadDetail() async {
    setState(() => _isLoading = true);

    final result = await _api.getDetailBerita(widget.idBerita);

    if (mounted) {
      if (result['success'] == true) {
        setState(() {
          _berita = result['data'];
          _isLoading = false;
        });
      } else {
        setState(() => _isLoading = false);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              result['message'] ?? 'Gagal memuat berita',
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
            ),
          ),
        );
        Navigator.pop(context);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text('Detail Berita'),
        backgroundColor: const Color(0xFF6FBA9D),
        foregroundColor: Colors.white,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Gambar
                  BeritaImage(
                    imageUrl: _berita?['gambar_url'],
                    height: MediaQuery.of(context).size.height * 0.3,
                  ),

                  // Content
                  Padding(
                    padding: const EdgeInsets.all(15),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Tanggal
                        Text(
                          _berita?['tanggal_lengkap'] ?? '',
                          style: TextStyle(fontSize: 9, color: Colors.grey[600]),
                        ),
                        const SizedBox(height: 9),

                        // Judul
                        Text(
                          _berita?['judul'] ?? '',
                          style: const TextStyle(
                            fontSize: 19,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const SizedBox(height: 9),

                        // Penulis
                        Row(
                          children: [
                            CircleAvatar(
                              radius: 16,
                              backgroundColor: const Color(0xFF6FBA9D),
                              child: const Icon(Icons.person, size: 15, color: Colors.white),
                            ),
                            const SizedBox(width: 7),
                            Expanded(
                              child: Text(
                                _berita?['penulis'] ?? '',
                                style: const TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.w600,
                                ),
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                          ],
                        ),
                        const Divider(height: 24),

                        // Konten HTML (dari Quill Editor)
                        HtmlWidget(
                          _berita?['konten'] ?? '',
                          textStyle: const TextStyle(
                            fontSize: 12,
                            height: 1.6,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
    );
  }
}