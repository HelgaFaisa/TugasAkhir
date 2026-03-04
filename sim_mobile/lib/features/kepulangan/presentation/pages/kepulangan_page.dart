// lib/features/kepulangan/presentation/pages/kepulangan_page.dart

import 'package:flutter/material.dart';
import '../../data/models/kepulangan_model.dart';
import '../../data/services/kepulangan_service.dart';
import '../widgets/kepulangan_card.dart';
import '../widgets/kuota_indicator.dart';
import 'kepulangan_detail_page.dart';

class KepulanganPage extends StatefulWidget {
  const KepulanganPage({super.key});

  @override
  State<KepulanganPage> createState() => _KepulanganPageState();
}

class _KepulanganPageState extends State<KepulanganPage> {
  final KepulanganService _service = KepulanganService();

  String? _selectedStatus;
  int _currentPage = 1;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Kepulangan Santri'),
        elevation: 0,
        backgroundColor: const Color(0xFF6FBA9D),
        foregroundColor: Colors.white,
        actions: [
          // Filter Status
          PopupMenuButton<String>(
            icon: const Icon(Icons.filter_list),
            tooltip: 'Filter Status',
            onSelected: (value) {
              setState(() {
                _selectedStatus = value == 'semua' ? null : value;
                _currentPage = 1;
              });
            },
            itemBuilder: (context) => [
              const PopupMenuItem(
                value: 'semua',
                child: Text('Semua Status'),
              ),
              const PopupMenuItem(
                value: 'Menunggu',
                child: Text('Menunggu'),
              ),
              const PopupMenuItem(
                value: 'Disetujui',
                child: Text('Disetujui'),
              ),
              const PopupMenuItem(value: 'Ditolak', child: Text('Ditolak')),
              const PopupMenuItem(value: 'Selesai', child: Text('Selesai')),
            ],
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () async {
          final result = await Navigator.pushNamed(
            context,
            '/kepulangan/pengajuan',
          );

          // Refresh list jika berhasil submit
          if (result == true && mounted) {
            setState(() {
              _currentPage = 1;
            });
          }
        },
        icon: const Icon(Icons.add),
        label: const Text('Ajukan Izin'),
        backgroundColor: const Color(0xFF6FBA9D),
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          setState(() {
            _currentPage = 1;
          });
        },
        child: FutureBuilder<Map<String, dynamic>>(
          future: _service.getListKepulangan(
            page: _currentPage,
            status: _selectedStatus,
          ),
          builder: (context, snapshot) {
            // Loading
            if (snapshot.connectionState == ConnectionState.waiting) {
              return const Center(child: CircularProgressIndicator());
            }

            // Error
            if (snapshot.hasError) {
              return _buildErrorState('Terjadi kesalahan: ${snapshot.error}');
            }

            // Data tidak ada
            if (!snapshot.hasData) {
              return _buildErrorState('Tidak ada data');
            }

            final result = snapshot.data!;

            // API Error
            if (result['success'] != true) {
              return _buildErrorState(
                result['message'] ?? 'Gagal mengambil data',
              );
            }

            // Parse data
            final List<KepulanganModel> kepulanganList =
                result['kepulangan'] ?? [];
            final KuotaInfo kuotaInfo = result['kuota'];
            final KepulanganPaginationModel pagination = result['pagination'];

            // Empty state — kuota tetap di atas
            if (kepulanganList.isEmpty) {
              return _buildEmptyState(kuotaInfo);
            }

            // Success
            return CustomScrollView(
              slivers: [
                // Kuota Indicator
                SliverToBoxAdapter(
                  child: Padding(
                    padding: const EdgeInsets.all(12),
                    child: KuotaIndicator(kuotaInfo: kuotaInfo),
                  ),
                ),

                // Filter info badge
                if (_selectedStatus != null)
                  SliverToBoxAdapter(
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 12),
                      child: Container(
                        padding: const EdgeInsets.all(9),
                        decoration: BoxDecoration(
                          color: Colors.blue.shade50,
                          borderRadius: BorderRadius.circular(7),
                        ),
                        child: Row(
                          children: [
                            Icon(
                              Icons.filter_alt,
                              size: 12,
                              color: Colors.blue.shade700,
                            ),
                            const SizedBox(width: 7),
                            Text(
                              'Filter: $_selectedStatus',
                              style: TextStyle(
                                color: Colors.blue.shade700,
                                fontWeight: FontWeight.w500,
                              ),
                            ),
                            const Spacer(),
                            InkWell(
                              onTap: () {
                                setState(() {
                                  _selectedStatus = null;
                                  _currentPage = 1;
                                });
                              },
                              child: Icon(
                                Icons.close,
                                size: 15,
                                color: Colors.blue.shade700,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),

                const SliverToBoxAdapter(child: SizedBox(height: 7)),

                // List Kepulangan
                SliverList(
                  delegate: SliverChildBuilderDelegate((context, index) {
                    final kepulangan = kepulanganList[index];
                    return KepulanganCard(
                      kepulangan: kepulangan,
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => KepulanganDetailPage(
                              idKepulangan: kepulangan.idKepulangan,
                            ),
                          ),
                        );
                      },
                    );
                  }, childCount: kepulanganList.length),
                ),

                // Pagination info
                SliverToBoxAdapter(
                  child: Padding(
                    padding: const EdgeInsets.all(12),
                    child: Text(
                      'Menampilkan ${pagination.from ?? 0} - ${pagination.to ?? 0} dari ${pagination.total} data',
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        color: Colors.grey.shade600,
                        fontSize: 9,
                      ),
                    ),
                  ),
                ),

                // Pagination buttons
                if (pagination.hasNextPage || pagination.hasPreviousPage)
                  SliverToBoxAdapter(
                    child: Padding(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 12,
                        vertical: 7,
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          if (pagination.hasPreviousPage)
                            ElevatedButton.icon(
                              onPressed: () {
                                setState(() {
                                  _currentPage--;
                                });
                              },
                              icon: const Icon(Icons.chevron_left),
                              label: const Text('Prev'),
                            ),
                          const SizedBox(width: 7),
                          if (pagination.hasNextPage)
                            ElevatedButton.icon(
                              onPressed: () {
                                setState(() {
                                  _currentPage++;
                                });
                              },
                              icon: const Icon(Icons.chevron_right),
                              label: const Text('Next'),
                            ),
                        ],
                      ),
                    ),
                  ),
              ],
            );
          },
        ),
      ),
    );
  }

  Widget _buildErrorState(String message) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Text(message),
      ),
    );
  }

  // ✅ PERBAIKAN: KuotaIndicator selalu di atas, pesan kosong di tengah sisa ruang
  Widget _buildEmptyState(KuotaInfo kuotaInfo) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        // Kuota Indicator tetap di atas — sama seperti saat ada data
        Padding(
          padding: const EdgeInsets.all(12),
          child: KuotaIndicator(kuotaInfo: kuotaInfo),
        ),
        // Pesan "tidak ada data" di tengah sisa ruang layar
        const Expanded(
          child: Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(Icons.inbox, size: 48, color: Colors.grey),
                SizedBox(height: 7),
                Text(
                  'Tidak ada data kepulangan.',
                  style: TextStyle(color: Colors.grey),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }
}