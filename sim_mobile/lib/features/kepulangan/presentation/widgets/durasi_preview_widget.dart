// lib/features/kepulangan/presentation/widgets/durasi_preview_widget.dart

import 'package:flutter/material.dart';

class DurasiPreviewWidget extends StatelessWidget {
  final int durasiIzin;
  final int totalSetelahIzin;
  final int sisaKuotaSetelah;
  final int kuotaMaksimal;
  final bool isOverLimit;

  const DurasiPreviewWidget({
    super.key,
    required this.durasiIzin,
    required this.totalSetelahIzin,
    required this.sisaKuotaSetelah,
    required this.kuotaMaksimal,
    required this.isOverLimit,
  });

  @override
  Widget build(BuildContext context) {
    if (durasiIzin == 0) return const SizedBox();

    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(12),
          gradient: LinearGradient(
            colors: isOverLimit
                ? [Colors.red.shade400, Colors.red.shade600]
                : totalSetelahIzin >= kuotaMaksimal * 0.8
                    ? [Colors.orange.shade400, Colors.orange.shade600]
                    : [Colors.blue.shade400, Colors.blue.shade600],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header
            Row(
              children: [
                Icon(
                  isOverLimit
                      ? Icons.warning_amber_rounded
                      : Icons.info_outline,
                  color: Colors.white,
                  size: 20,
                ),
                const SizedBox(width: 8),
                const Text(
                  'Preview Durasi Izin',
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 14,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),

            const SizedBox(height: 16),

            // Info cards
            Row(
              children: [
                Expanded(
                  child: _buildInfoCard(
                    'Durasi',
                    '$durasiIzin',
                    'hari',
                    Icons.access_time,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: _buildInfoCard(
                    'Total Setelah',
                    '$totalSetelahIzin',
                    'hari',
                    Icons.calendar_today,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: _buildInfoCard(
                    isOverLimit ? 'Kelebihan' : 'Sisa',
                    isOverLimit
                        ? '${sisaKuotaSetelah.abs()}'
                        : '$sisaKuotaSetelah',
                    'hari',
                    isOverLimit ? Icons.error_outline : Icons.check_circle,
                  ),
                ),
              ],
            ),

            // Progress bar
            const SizedBox(height: 12),
            ClipRRect(
              borderRadius: BorderRadius.circular(8),
              child: LinearProgressIndicator(
                value: (totalSetelahIzin / kuotaMaksimal).clamp(0.0, 1.0),
                backgroundColor: Colors.white.withValues(alpha: 0.3),
                valueColor: AlwaysStoppedAnimation<Color>(
                  isOverLimit ? Colors.red.shade900 : Colors.white,
                ),
                minHeight: 8,
              ),
            ),

            const SizedBox(height: 8),

            // Percentage text
            Text(
              '${((totalSetelahIzin / kuotaMaksimal) * 100).toStringAsFixed(1)}% dari kuota $kuotaMaksimal hari',
              style: const TextStyle(
                color: Colors.white70,
                fontSize: 12,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoCard(
    String label,
    String value,
    String unit,
    IconData icon,
  ) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white.withValues(alpha: 0.2),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Column(
        children: [
          Icon(icon, color: Colors.white, size: 20),
          const SizedBox(height: 4),
          Text(
            value,
            style: const TextStyle(
              color: Colors.white,
              fontSize: 20,
              fontWeight: FontWeight.bold,
            ),
          ),
          Text(
            unit,
            style: const TextStyle(
              color: Colors.white70,
              fontSize: 10,
            ),
          ),
          const SizedBox(height: 2),
          Text(
            label,
            style: const TextStyle(
              color: Colors.white70,
              fontSize: 11,
            ),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }
}