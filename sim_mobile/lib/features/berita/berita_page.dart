// lib/features/berita/berita_page.dart

import 'package:flutter/material.dart';
import '../../core/api/api_service.dart';
import '../../core/widgets/berita_image.dart';
import 'berita_detail_page.dart';

class BeritaPage extends StatefulWidget {
  const BeritaPage({super.key});

  @override
  State<BeritaPage> createState() => _BeritaPageState();
}

class _BeritaPageState extends State<BeritaPage> {
  final _api = ApiService();
  List<dynamic> _beritaList = [];
  bool _isLoading = true;
  int _currentPage = 1;
  int _lastPage = 1;

  @override
  void initState() {
    super.initState();
    _loadBerita();
  }

  Future<void> _loadBerita({bool isRefresh = false}) async {
    if (isRefresh) {
      setState(() {
        _currentPage = 1;
        _beritaList = [];
      });
    }

    setState(() => _isLoading = true);

    final result = await _api.getBerita(page: _currentPage);

    if (mounted) {
      setState(() {
        if (result['success'] == true) {
          if (isRefresh) {
            _beritaList = result['data'];
          } else {
            _beritaList.addAll(result['data']);
          }

          if (result['pagination'] != null) {
            _lastPage = result['pagination']['last_page'] ?? 1;
          }
        }
        _isLoading = false;
      });
    }
  }

  /// Strip HTML tags untuk preview konten di card
  String _stripHtml(String html) {
    return html
        .replaceAll(RegExp(r'<[^>]*>'), '')
        .replaceAll(RegExp(r'&nbsp;'), ' ')
        .replaceAll(RegExp(r'&amp;'), '&')
        .replaceAll(RegExp(r'&lt;'), '<')
        .replaceAll(RegExp(r'&gt;'), '>')
        .replaceAll(RegExp(r'\s+'), ' ')
        .trim();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[100],
      appBar: AppBar(
        title: const Text('Berita'),
        backgroundColor: const Color(0xFF6FBA9D),
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => _loadBerita(isRefresh: true),
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () => _loadBerita(isRefresh: true),
        child: _isLoading && _beritaList.isEmpty
            ? const Center(child: CircularProgressIndicator())
            : _beritaList.isEmpty
                ? _buildEmptyState()
                : ListView.builder(
                    padding: const EdgeInsets.all(12),
                    itemCount: _beritaList.length + (_currentPage < _lastPage ? 1 : 0),
                    itemBuilder: (context, index) {
                      if (index == _beritaList.length) {
                        return _buildLoadMoreButton();
                      }
                      return _buildBeritaCard(_beritaList[index]);
                    },
                  ),
      ),
    );
  }

  Widget _buildBeritaCard(Map<String, dynamic> berita) {
    final judul = berita['judul'] ?? '';
    final konten = _stripHtml(berita['konten'] ?? '');
    final penulis = berita['penulis'] ?? '';
    final tanggal = berita['tanggal'] ?? '';

    return Card(
      margin: const EdgeInsets.only(bottom: 9),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(9)),
      child: InkWell(
        onTap: () async {
          await Navigator.push(
            context,
            MaterialPageRoute(
              builder: (_) => BeritaDetailPage(idBerita: berita['id_berita']),
            ),
          );
        },
        borderRadius: BorderRadius.circular(9),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Gambar
            BeritaImage(
              imageUrl: berita['gambar_url'],
              height: MediaQuery.of(context).size.height * 0.22,
              borderRadius: const BorderRadius.vertical(top: Radius.circular(12)),
            ),

            // Content
            Padding(
              padding: const EdgeInsets.all(12),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Tanggal
                  Row(
                    children: [
                      const Spacer(),
                      Icon(Icons.access_time, size: 11, color: Colors.grey[600]),
                      const SizedBox(width: 2),
                      Flexible(
                        child: Text(
                          tanggal,
                          style: TextStyle(fontSize: 9, color: Colors.grey[600]),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 9),

                  // Judul
                  Text(
                    judul,
                    style: const TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.bold,
                      color: Colors.black,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 7),

                  // Konten Preview (stripped HTML)
                  Text(
                    konten,
                    style: TextStyle(fontSize: 11, color: Colors.grey[600]),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 9),

                  // Penulis
                  Row(
                    children: [
                      Icon(Icons.person_outline, size: 11, color: Colors.grey[600]),
                      const SizedBox(width: 2),
                      Expanded(
                        child: Text(
                          penulis,
                          style: TextStyle(fontSize: 9, color: Colors.grey[600]),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildLoadMoreButton() {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 12),
      child: Center(
        child: ElevatedButton(
          onPressed: () {
            setState(() => _currentPage++);
            _loadBerita();
          },
          child: const Text('Muat Lebih Banyak'),
        ),
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.article_outlined, size: 60, color: Colors.grey[400]),
          const SizedBox(height: 12),
          Text(
            'Belum ada berita',
            style: TextStyle(fontSize: 12, color: Colors.grey[600]),
          ),
        ],
      ),
    );
  }
}