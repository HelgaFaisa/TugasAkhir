// lib/features/kepulangan/presentation/widgets/kepulangan_card.dart

import 'package:flutter/material.dart';
import '../../data/models/kepulangan_model.dart';

class KepulanganCard extends StatelessWidget {
  final KepulanganModel kepulangan;
  final VoidCallback? onTap;

  const KepulanganCard({super.key, required this.kepulangan, this.onTap});

  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: 2,
      margin: const EdgeInsets.symmetric(horizontal: 12, vertical: 7),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(9)),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(9),
        child: Padding(
          padding: const EdgeInsets.all(12),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header: Status dan Durasi
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  _buildStatusBadge(),
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 9,
                      vertical: 5,
                    ),
                    decoration: BoxDecoration(
                      color: Colors.blue.shade50,
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Row(
                      children: [
                        Icon(
                          Icons.access_time,
                          size: 11,
                          color: Colors.blue.shade700,
                        ),
                        const SizedBox(width: 2),
                        Text(
                          '${kepulangan.durasiIzin} hari',
                          style: TextStyle(
                            color: Colors.blue.shade700,
                            fontSize: 9,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),

              const SizedBox(height: 9),

              // Tanggal Pulang - Kembali
              Row(
                children: [
                  Icon(
                    Icons.calendar_today,
                    size: 12,
                    color: Colors.grey.shade600,
                  ),
                  const SizedBox(width: 7),
                  Expanded(
                    child: Text(
                      '${kepulangan.tanggalPulangFormatted} - ${kepulangan.tanggalKembaliFormatted}',
                      style: TextStyle(
                        color: Colors.grey.shade800,
                        fontSize: 11,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ),
                ],
              ),

              const SizedBox(height: 7),

              // Alasan
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Icon(Icons.subject, size: 12, color: Colors.grey.shade600),
                  const SizedBox(width: 7),
                  Expanded(
                    child: Text(
                      kepulangan.alasan,
                      style: TextStyle(
                        color: Colors.grey.shade700,
                        fontSize: 11,
                      ),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                ],
              ),

              // Catatan (jika ada)
              if (kepulangan.catatan != null &&
                  kepulangan.catatan!.isNotEmpty) ...[
                const SizedBox(height: 7),
                Container(
                  padding: const EdgeInsets.all(7),
                  decoration: BoxDecoration(
                    color: Colors.amber.shade50,
                    borderRadius: BorderRadius.circular(7),
                    border: Border.all(color: Colors.amber.shade200),
                  ),
                  child: Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Icon(
                        Icons.info_outline,
                        size: 11,
                        color: Colors.amber.shade800,
                      ),
                      const SizedBox(width: 5),
                      Expanded(
                        child: Text(
                          kepulangan.catatan!,
                          style: TextStyle(
                            color: Colors.amber.shade900,
                            fontSize: 9,
                          ),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                ),
              ],

              // Indicators (Aktif / Terlambat)
              if (kepulangan.isAktif || kepulangan.isTerlambat) ...[
                const SizedBox(height: 7),
                Wrap(
                  spacing: 8,
                  children: [
                    if (kepulangan.isAktif)
                      _buildIndicatorChip(
                        'Sedang Berlangsung',
                        Icons.hourglass_bottom,
                        Colors.blue,
                      ),
                    if (kepulangan.isTerlambat)
                      _buildIndicatorChip(
                        'Terlambat Kembali',
                        Icons.warning,
                        Colors.red,
                      ),
                  ],
                ),
              ],

              // Approved date (jika sudah disetujui)
              if (kepulangan.status == 'Disetujui' &&
                  kepulangan.approvedAtFormatted != null) ...[
                const SizedBox(height: 7),
                Text(
                  'Disetujui: ${kepulangan.approvedAtFormatted}',
                  style: TextStyle(color: Colors.grey.shade500, fontSize: 8),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildStatusBadge() {
    Color bgColor;
    Color textColor;
    IconData icon;

    switch (kepulangan.status) {
      case 'Disetujui':
        bgColor = Colors.green.shade100;
        textColor = Colors.green.shade700;
        icon = Icons.check_circle;
        break;
      case 'Menunggu':
        bgColor = Colors.orange.shade100;
        textColor = Colors.orange.shade700;
        icon = Icons.pending;
        break;
      case 'Ditolak':
        bgColor = Colors.red.shade100;
        textColor = Colors.red.shade700;
        icon = Icons.cancel;
        break;
      case 'Selesai':
        bgColor = Colors.grey.shade200;
        textColor = Colors.grey.shade700;
        icon = Icons.done_all;
        break;
      default:
        bgColor = Colors.grey.shade100;
        textColor = Colors.grey.shade700;
        icon = Icons.help_outline;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 9, vertical: 5),
      decoration: BoxDecoration(
        color: bgColor,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 11, color: textColor),
          const SizedBox(width: 2),
          Text(
            kepulangan.status,
            style: TextStyle(
              color: textColor,
              fontSize: 9,
              fontWeight: FontWeight.bold,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildIndicatorChip(String label, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 2),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.1),
        borderRadius: BorderRadius.circular(9),
        border: Border.all(color: color.withValues(alpha: 0.3)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 9, color: color),
          const SizedBox(width: 2),
          Text(
            label,
            style: TextStyle(
              color: color,
              fontSize: 8,
              fontWeight: FontWeight.w600,
            ),
          ),
        ],
      ),
    );
  }
}
