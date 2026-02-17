// lib/features/absensi/widgets/absensi_timeline_item.dart

import 'package:flutter/material.dart';
import '../models/absensi_kegiatan_model.dart';

class AbsensiTimelineItem extends StatelessWidget {
  final AbsensiKegiatan absensi;
  final bool isLast;

  const AbsensiTimelineItem({
    super.key,
    required this.absensi,
    this.isLast = false,
  });

  @override
  Widget build(BuildContext context) {
    return IntrinsicHeight(
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Timeline Indicator
          Column(
            children: [
              Container(
                width: 12,
                height: 12,
                decoration: BoxDecoration(
                  color: _getStatusColor(absensi.status),
                  shape: BoxShape.circle,
                  border: Border.all(
                    color: Colors.white,
                    width: 2,
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: _getStatusColor(absensi.status).withOpacity(0.3),
                      blurRadius: 4,
                      spreadRadius: 1,
                    ),
                  ],
                ),
              ),
              if (!isLast)
                Expanded(
                  child: Container(
                    width: 2,
                    color: Colors.grey[300],
                  ),
                ),
            ],
          ),
          const SizedBox(width: 12),

          // Card Content
          Expanded(
            child: Padding(
              padding: EdgeInsets.only(bottom: isLast ? 0 : 16),
              child: Card(
                elevation: 1,
                margin: EdgeInsets.zero,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                  side: BorderSide(
                    color: Color(
                      int.parse(
                        absensi.kategori.warna.replaceFirst('#', '0xFF'),
                      ),
                    ).withOpacity(0.3),
                    width: 2,
                  ),
                ),
                child: Padding(
                  padding: const EdgeInsets.all(14),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Header: Nama Kegiatan + Status Badge
                      Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Icon Kategori
                          Container(
                            width: 40,
                            height: 40,
                            decoration: BoxDecoration(
                              color: Color(
                                int.parse(
                                  absensi.kategori.warna.replaceFirst(
                                    '#',
                                    '0xFF',
                                  ),
                                ),
                              ).withOpacity(0.15),
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: Icon(
                              _getIconFromString(absensi.kategori.icon),
                              color: Color(
                                int.parse(
                                  absensi.kategori.warna.replaceFirst(
                                    '#',
                                    '0xFF',
                                  ),
                                ),
                              ),
                              size: 20,
                            ),
                          ),
                          const SizedBox(width: 12),

                          // Nama Kegiatan
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  absensi.namaKegiatan,
                                  style: const TextStyle(
                                    fontSize: 15,
                                    fontWeight: FontWeight.bold,
                                  ),
                                  maxLines: 2,
                                  overflow: TextOverflow.ellipsis,
                                ),
                                const SizedBox(height: 4),
                                Text(
                                  absensi.kategori.nama,
                                  style: TextStyle(
                                    fontSize: 12,
                                    color: Colors.grey[600],
                                  ),
                                ),
                              ],
                            ),
                          ),

                          // Status Badge
                          Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: 10,
                              vertical: 4,
                            ),
                            decoration: BoxDecoration(
                              color: _getStatusColor(absensi.status)
                                  .withOpacity(0.15),
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Text(
                              _getStatusEmoji(absensi.status) +
                                  ' ' +
                                  absensi.status,
                              style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.w600,
                                color: _getStatusColor(absensi.status),
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 12),

                      // Info Row
                      Row(
                        children: [
                          // Waktu Kegiatan
                          Icon(
                            Icons.access_time,
                            size: 14,
                            color: Colors.grey[600],
                          ),
                          const SizedBox(width: 4),
                          Text(
                            '${absensi.waktuMulai} - ${absensi.waktuSelesai}',
                            style: TextStyle(
                              fontSize: 12,
                              color: Colors.grey[700],
                            ),
                          ),
                          if (absensi.waktuAbsen != null) ...[
                            const SizedBox(width: 12),
                            Icon(
                              Icons.check_circle_outline,
                              size: 14,
                              color: Colors.grey[600],
                            ),
                            const SizedBox(width: 4),
                            Text(
                              absensi.waktuAbsen!,
                              style: TextStyle(
                                fontSize: 12,
                                color: Colors.grey[700],
                              ),
                            ),
                          ],
                        ],
                      ),

                      // Metode Absen
                      if (absensi.metodeAbsen.isNotEmpty) ...[
                        const SizedBox(height: 8),
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 8,
                            vertical: 4,
                          ),
                          decoration: BoxDecoration(
                            color: absensi.metodeAbsen == 'RFID'
                                ? const Color(0xFF7C3AED).withOpacity(0.1)
                                : Colors.grey[200],
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(
                                absensi.metodeAbsen == 'RFID'
                                    ? Icons.credit_card
                                    : Icons.touch_app,
                                size: 12,
                                color: absensi.metodeAbsen == 'RFID'
                                    ? const Color(0xFF7C3AED)
                                    : Colors.grey[700],
                              ),
                              const SizedBox(width: 4),
                              Text(
                                absensi.metodeAbsen,
                                style: TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.w500,
                                  color: absensi.metodeAbsen == 'RFID'
                                      ? const Color(0xFF7C3AED)
                                      : Colors.grey[700],
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],

                      // Punctuality (jika ada)
                      if (absensi.punctuality != null) ...[
                        const SizedBox(height: 8),
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 8,
                            vertical: 4,
                          ),
                          decoration: BoxDecoration(
                            color: absensi.punctuality!.contains('Tepat')
                                ? Colors.green[50]
                                : Colors.orange[50],
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(
                                absensi.punctuality!.contains('Tepat')
                                    ? Icons.access_time
                                    : Icons.schedule,
                                size: 12,
                                color: absensi.punctuality!.contains('Tepat')
                                    ? Colors.green[700]
                                    : Colors.orange[700],
                              ),
                              const SizedBox(width: 4),
                              Text(
                                absensi.punctuality!,
                                style: TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.w500,
                                  color: absensi.punctuality!.contains('Tepat')
                                      ? Colors.green[700]
                                      : Colors.orange[700],
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],

                      // Keterangan (jika ada)
                      if (absensi.keterangan != null &&
                          absensi.keterangan!.isNotEmpty) ...[
                        const SizedBox(height: 8),
                        Container(
                          padding: const EdgeInsets.all(8),
                          decoration: BoxDecoration(
                            color: Colors.blue[50],
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: Row(
                            children: [
                              Icon(
                                Icons.info_outline,
                                size: 14,
                                color: Colors.blue[700],
                              ),
                              const SizedBox(width: 6),
                              Expanded(
                                child: Text(
                                  absensi.keterangan!,
                                  style: TextStyle(
                                    fontSize: 11,
                                    color: Colors.blue[900],
                                  ),
                                  maxLines: 2,
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ],
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'Hadir':
        return Colors.green;
      case 'Izin':
        return Colors.orange;
      case 'Sakit':
        return Colors.blue;
      case 'Alpa':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  String _getStatusEmoji(String status) {
    switch (status) {
      case 'Hadir':
        return '✅';
      case 'Izin':
        return '⚠️';
      case 'Sakit':
        return '💊';
      case 'Alpa':
        return '❌';
      default:
        return '⚪';
    }
  }

  IconData _getIconFromString(String iconString) {
    // Simple mapping - extend as needed
    final iconMap = {
      'fa-calendar': Icons.calendar_today,
      'fa-book': Icons.menu_book,
      'fa-flag': Icons.flag,
      'fa-mosque': Icons.home,
      'fa-pray': Icons.wc,
      'fa-graduation-cap': Icons.school,
    };

    return iconMap[iconString] ?? Icons.event;
  }
}