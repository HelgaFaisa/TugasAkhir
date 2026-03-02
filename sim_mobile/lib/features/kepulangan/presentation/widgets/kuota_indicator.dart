// lib/features/kepulangan/presentation/widgets/kuota_indicator.dart

import 'package:flutter/material.dart';
import '../../data/models/kepulangan_model.dart';

class KuotaIndicator extends StatelessWidget {
  final KuotaInfo kuotaInfo;
  final bool showDetail;

  const KuotaIndicator({
    super.key,
    required this.kuotaInfo,
    this.showDetail = true,
  });

  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(9)),
      child: Container(
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(9),
          gradient: _getGradient(),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header
            Row(
              children: [
                Icon(_getIcon(), color: Colors.white, size: 19),
                const SizedBox(width: 7),
                Expanded(
                  child: Text(
                    'Kuota Kepulangan Tahunan',
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 12,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
                _buildStatusBadge(),
              ],
            ),

            const SizedBox(height: 12),

            // Progress Bar
            ClipRRect(
              borderRadius: BorderRadius.circular(7),
              child: LinearProgressIndicator(
                value: kuotaInfo.persentase / 100,
                backgroundColor: Colors.white.withValues(alpha: 0.3),
                valueColor: AlwaysStoppedAnimation<Color>(
                  kuotaInfo.isOverLimit ? Colors.red.shade900 : Colors.white,
                ),
                minHeight: 12,
              ),
            ),

            const SizedBox(height: 9),

            // Info
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                _buildInfoItem(
                  'Terpakai',
                  '${kuotaInfo.totalTerpakai} hari',
                  Icons.calendar_today,
                ),
                _buildInfoItem(
                  'Maksimal',
                  '${kuotaInfo.kuotaMaksimal} hari',
                  Icons.event_available,
                ),
                _buildInfoItem(
                  kuotaInfo.isOverLimit ? 'Kelebihan' : 'Sisa',
                  kuotaInfo.isOverLimit
                      ? '${kuotaInfo.sisaKuota.abs()} hari'
                      : '${kuotaInfo.sisaKuota} hari',
                  kuotaInfo.isOverLimit ? Icons.warning : Icons.check_circle,
                ),
              ],
            ),

            if (showDetail) ...[
              const SizedBox(height: 9),
              const Divider(color: Colors.white38, height: 1),
              const SizedBox(height: 9),

              // Periode
              Row(
                children: [
                  const Icon(Icons.date_range, color: Colors.white70, size: 12),
                  const SizedBox(width: 7),
                  Text(
                    'Periode: ${_formatDate(kuotaInfo.periodeMulai)} - ${_formatDate(kuotaInfo.periodeAkhir)}',
                    style: const TextStyle(color: Colors.white70, fontSize: 9),
                  ),
                ],
              ),

              if (kuotaInfo.isOverLimit) ...[
                const SizedBox(height: 7),
                Container(
                  padding: const EdgeInsets.all(7),
                  decoration: BoxDecoration(
                    color: Colors.red.shade900.withValues(alpha: 0.5),
                    borderRadius: BorderRadius.circular(7),
                  ),
                  child: Row(
                    children: [
                      const Icon(
                        Icons.error_outline,
                        color: Colors.white,
                        size: 12,
                      ),
                      const SizedBox(width: 7),
                      Expanded(
                        child: Text(
                          'âš ï¸ Kuota kepulangan sudah melebihi batas maksimal',
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 9,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ],
          ],
        ),
      ),
    );
  }

  Widget _buildInfoItem(String label, String value, IconData icon) {
    return Column(
      children: [
        Icon(icon, color: Colors.white70, size: 15),
        const SizedBox(height: 2),
        Text(
          value,
          style: const TextStyle(
            color: Colors.white,
            fontSize: 12,
            fontWeight: FontWeight.bold,
          ),
        ),
        Text(
          label,
          style: const TextStyle(color: Colors.white70, fontSize: 9),
        ),
      ],
    );
  }

  Widget _buildStatusBadge() {
    String text;
    Color bgColor;

    if (kuotaInfo.isOverLimit) {
      text = 'OVER LIMIT';
      bgColor = Colors.red.shade900;
    } else if (kuotaInfo.isHampirHabis) {
      text = 'HAMPIR HABIS';
      bgColor = Colors.orange.shade800;
    } else {
      text = 'AMAN';
      bgColor = Colors.green.shade700;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 9, vertical: 5),
      decoration: BoxDecoration(
        color: bgColor,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Text(
        text,
        style: const TextStyle(
          color: Colors.white,
          fontSize: 8,
          fontWeight: FontWeight.bold,
        ),
      ),
    );
  }

  LinearGradient _getGradient() {
    if (kuotaInfo.isOverLimit) {
      return LinearGradient(
        colors: [Colors.red.shade700, Colors.red.shade900],
        begin: Alignment.topLeft,
        end: Alignment.bottomRight,
      );
    } else if (kuotaInfo.isHampirHabis) {
      return LinearGradient(
        colors: [Colors.orange.shade600, Colors.orange.shade800],
        begin: Alignment.topLeft,
        end: Alignment.bottomRight,
      );
    } else {
      return LinearGradient(
        colors: [Colors.green.shade600, Colors.green.shade800],
        begin: Alignment.topLeft,
        end: Alignment.bottomRight,
      );
    }
  }

  IconData _getIcon() {
    if (kuotaInfo.isOverLimit) {
      return Icons.warning_amber_rounded;
    } else if (kuotaInfo.isHampirHabis) {
      return Icons.info_outline;
    } else {
      return Icons.check_circle_outline;
    }
  }

  String _formatDate(String date) {
    try {
      final parts = date.split('-');
      if (parts.length == 3) {
        final months = [
          '',
          'Jan',
          'Feb',
          'Mar',
          'Apr',
          'Mei',
          'Jun',
          'Jul',
          'Agu',
          'Sep',
          'Okt',
          'Nov',
          'Des',
        ];
        return '${parts[2]} ${months[int.parse(parts[1])]} ${parts[0]}';
      }
      return date;
    } catch (e) {
      return date;
    }
  }
}
