// lib/features/kepulangan/presentation/pages/kepulangan_detail_page.dart

import 'package:flutter/material.dart';
import '../../data/models/kepulangan_model.dart';
import '../../data/services/kepulangan_service.dart';
import '../widgets/kuota_indicator.dart';

class KepulanganDetailPage extends StatelessWidget {
  final String idKepulangan;

  const KepulanganDetailPage({super.key, required this.idKepulangan});

  @override
  Widget build(BuildContext context) {
    final KepulanganService service = KepulanganService();

    return Scaffold(
      appBar: AppBar(
        title: const Text('Detail Kepulangan'),
        elevation: 0,
        backgroundColor: Colors.deepPurple,
        foregroundColor: Colors.white,
      ),
      body: FutureBuilder<Map<String, dynamic>>(
        future: service.getDetailKepulangan(idKepulangan),
        builder: (context, snapshot) {
          // Loading
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }

          // Error
          if (snapshot.hasError) {
            return _buildErrorState(
              context,
              'Terjadi kesalahan: ${snapshot.error}',
            );
          }

          // Data tidak ada
          if (!snapshot.hasData) {
            return _buildErrorState(context, 'Tidak ada data');
          }

          final result = snapshot.data!;

          // API Error
          if (result['success'] != true) {
            return _buildErrorState(
              context,
              result['message'] ?? 'Gagal mengambil detail',
            );
          }

          // Parse data
          final KepulanganModel kepulangan = result['kepulangan'];
          final KuotaInfo kuotaInfo = result['kuota'];
          final Map<String, dynamic> santriInfo = result['santri'];

          // Success
          return RefreshIndicator(
            onRefresh: () async {
              // Reload page
              (context as Element).markNeedsBuild();
            },
            child: SingleChildScrollView(
              physics: const AlwaysScrollableScrollPhysics(),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Kuota Indicator
                  Padding(
                    padding: const EdgeInsets.all(16),
                    child: KuotaIndicator(
                      kuotaInfo: kuotaInfo,
                      showDetail: false,
                    ),
                  ),

                  // Status Card
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    child: _buildStatusCard(kepulangan),
                  ),

                  const SizedBox(height: 16),

                  // Detail Info
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    child: Card(
                      elevation: 2,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // Header
                            const Text(
                              'Informasi Kepulangan',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 16),

                            // Santri
                            _buildDetailRow(
                              'Nama Santri',
                              santriInfo['nama_lengkap'] ?? '-',
                              Icons.person,
                            ),
                            const Divider(height: 24),

                            // NIS
                            _buildDetailRow(
                              'NIS',
                              santriInfo['nis'] ?? '-',
                              Icons.badge,
                            ),
                            const Divider(height: 24),

                            // Tanggal Izin
                            _buildDetailRow(
                              'Tanggal Pengajuan',
                              kepulangan.tanggalIzinFormatted,
                              Icons.calendar_today,
                            ),
                            const Divider(height: 24),

                            // Tanggal Pulang
                            _buildDetailRow(
                              'Tanggal Pulang',
                              kepulangan.tanggalPulangFormatted,
                              Icons.flight_takeoff,
                            ),
                            const Divider(height: 24),

                            // Tanggal Kembali
                            _buildDetailRow(
                              'Tanggal Kembali',
                              kepulangan.tanggalKembaliFormatted,
                              Icons.flight_land,
                            ),
                            const Divider(height: 24),

                            // Durasi
                            _buildDetailRow(
                              'Durasi Izin',
                              '${kepulangan.durasiIzin} hari',
                              Icons.access_time,
                            ),
                            const Divider(height: 24),

                            // Alasan
                            _buildDetailColumn(
                              'Alasan Kepulangan',
                              kepulangan.alasan,
                              Icons.subject,
                            ),

                            // Catatan (jika ada)
                            if (kepulangan.catatan != null &&
                                kepulangan.catatan!.isNotEmpty) ...[
                              const Divider(height: 24),
                              _buildDetailColumn(
                                'Catatan Persetujuan',
                                kepulangan.catatan!,
                                Icons.note,
                                backgroundColor: Colors.amber.shade50,
                                borderColor: Colors.amber.shade200,
                                textColor: Colors.amber.shade900,
                              ),
                            ],

                            // Approved date
                            if (kepulangan.approvedAtFormatted != null) ...[
                              const Divider(height: 24),
                              _buildDetailRow(
                                'Waktu Persetujuan',
                                kepulangan.approvedAtFormatted!,
                                Icons.check_circle,
                              ),
                            ],
                          ],
                        ),
                      ),
                    ),
                  ),

                  const SizedBox(height: 16),

                  // Indicators Card
                  if (kepulangan.isAktif || kepulangan.isTerlambat)
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 16),
                      child: Card(
                        elevation: 2,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Padding(
                          padding: const EdgeInsets.all(16),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text(
                                'Status Kepulangan',
                                style: TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              const SizedBox(height: 12),
                              if (kepulangan.isAktif)
                                _buildIndicatorBanner(
                                  'Kepulangan Sedang Berlangsung',
                                  'Santri saat ini sedang dalam masa kepulangan',
                                  Icons.hourglass_bottom,
                                  Colors.blue,
                                ),
                              if (kepulangan.isAktif && kepulangan.isTerlambat)
                                const SizedBox(height: 8),
                              if (kepulangan.isTerlambat)
                                _buildIndicatorBanner(
                                  'Terlambat Kembali',
                                  'Santri belum kembali melewati batas waktu yang ditentukan',
                                  Icons.warning,
                                  Colors.red,
                                ),
                            ],
                          ),
                        ),
                      ),
                    ),

                  const SizedBox(height: 32),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildStatusCard(KepulanganModel kepulangan) {
    Color bgColor;
    Color iconColor;
    IconData icon;
    String statusText;

    switch (kepulangan.status) {
      case 'Disetujui':
        bgColor = Colors.green.shade50;
        iconColor = Colors.green.shade700;
        icon = Icons.check_circle;
        statusText = 'Disetujui';
        break;
      case 'Menunggu':
        bgColor = Colors.orange.shade50;
        iconColor = Colors.orange.shade700;
        icon = Icons.pending;
        statusText = 'Menunggu Persetujuan';
        break;
      case 'Ditolak':
        bgColor = Colors.red.shade50;
        iconColor = Colors.red.shade700;
        icon = Icons.cancel;
        statusText = 'Ditolak';
        break;
      case 'Selesai':
        bgColor = Colors.grey.shade100;
        iconColor = Colors.grey.shade700;
        icon = Icons.done_all;
        statusText = 'Selesai';
        break;
      default:
        bgColor = Colors.grey.shade50;
        iconColor = Colors.grey.shade700;
        icon = Icons.help_outline;
        statusText = kepulangan.status;
    }

    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: bgColor,
          borderRadius: BorderRadius.circular(12),
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(icon, color: iconColor, size: 32),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Status Pengajuan',
                    style: TextStyle(color: Colors.grey.shade600, fontSize: 12),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    statusText,
                    style: TextStyle(
                      color: iconColor,
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
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

  Widget _buildDetailRow(String label, String value, IconData icon) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, size: 20, color: Colors.grey.shade600),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                label,
                style: TextStyle(color: Colors.grey.shade600, fontSize: 12),
              ),
              const SizedBox(height: 4),
              Text(
                value,
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w500,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildDetailColumn(
    String label,
    String value,
    IconData icon, {
    Color? backgroundColor,
    Color? borderColor,
    Color? textColor,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Icon(icon, size: 20, color: Colors.grey.shade600),
            const SizedBox(width: 12),
            Text(
              label,
              style: TextStyle(color: Colors.grey.shade600, fontSize: 12),
            ),
          ],
        ),
        const SizedBox(height: 8),
        Container(
          width: double.infinity,
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(
            color: backgroundColor ?? Colors.grey.shade50,
            borderRadius: BorderRadius.circular(8),
            border: borderColor != null ? Border.all(color: borderColor) : null,
          ),
          child: Text(
            value,
            style: TextStyle(
              fontSize: 14,
              color: textColor ?? Colors.grey.shade800,
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildIndicatorBanner(
    String title,
    String subtitle,
    IconData icon,
    Color color,
  ) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.1),
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: color.withValues(alpha: 0.3)),
      ),
      child: Row(
        children: [
          Icon(icon, color: color, size: 24),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: TextStyle(
                    color: color,
                    fontWeight: FontWeight.bold,
                    fontSize: 14,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  subtitle,
                  style: TextStyle(color: color.withValues(alpha: 0.8), fontSize: 12),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildErrorState(BuildContext context, String message) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.error_outline, size: 64, color: Colors.red.shade300),
            const SizedBox(height: 16),
            Text(
              message,
              textAlign: TextAlign.center,
              style: TextStyle(color: Colors.grey.shade600, fontSize: 14),
            ),
            const SizedBox(height: 24),
            ElevatedButton.icon(
              onPressed: () {
                Navigator.pop(context);
              },
              icon: const Icon(Icons.arrow_back),
              label: const Text('Kembali'),
            ),
          ],
        ),
      ),
    );
  }
}
