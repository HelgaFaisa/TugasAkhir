// lib/features/capaian/presentation/widgets/kelas_badge.dart
// Widget badge kelas dengan color coding berdasarkan kelompok

import 'package:flutter/material.dart';
import '../../models/kelas_info_model.dart';

class KelasBadge extends StatelessWidget {
  final KelasInfo kelasInfo;
  final bool showKelompok;
  final bool compact;

  const KelasBadge({
    super.key,
    required this.kelasInfo,
    this.showKelompok = true,
    this.compact = false,
  });

  /// Color mapping berdasarkan nama kelompok
  static Color getKelompokColor(String? kelompok) {
    if (kelompok == null) return const Color(0xFF6FBA9D);
    final lower = kelompok.toLowerCase();
    if (lower.contains('pb') || lower.contains('persiapan')) {
      return const Color(0xFF3B82F6); // Blue
    } else if (lower.contains('lambat')) {
      return const Color(0xFFFB923C); // Orange
    } else if (lower.contains('cepat')) {
      return const Color(0xFF10B981); // Green
    }
    return const Color(0xFF6FBA9D); // Green default
  }

  static Color getKelompokColorLight(String? kelompok) {
    return getKelompokColor(kelompok).withValues(alpha: 0.12);
  }

  @override
  Widget build(BuildContext context) {
    final color = getKelompokColor(kelasInfo.kelompok);

    if (compact) {
      return Container(
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
        decoration: BoxDecoration(
          color: color.withValues(alpha: 0.12),
          borderRadius: BorderRadius.circular(9),
          border: Border.all(color: color.withValues(alpha: 0.3)),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(Icons.school_rounded, size: 9, color: color),
            const SizedBox(width: 2),
            Flexible(
              child: Text(
                kelasInfo.shortName,
                style: TextStyle(
                  fontSize: 8,
                  fontWeight: FontWeight.w600,
                  color: color,
                ),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),
            ),
            if (kelasInfo.isPrimary) ...[
              const SizedBox(width: 2),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 2, vertical: 1),
                decoration: BoxDecoration(
                  color: color,
                  borderRadius: BorderRadius.circular(5),
                ),
                child: const Text(
                  'Utama',
                  style: TextStyle(fontSize: 7, fontWeight: FontWeight.bold, color: Colors.white),
                ),
              ),
            ],
          ],
        ),
      );
    }

    // Full badge
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 9, vertical: 7),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [color.withValues(alpha: 0.15), color.withValues(alpha: 0.05)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(9),
        border: Border.all(color: color.withValues(alpha: 0.25)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            padding: const EdgeInsets.all(5),
            decoration: BoxDecoration(
              color: color.withValues(alpha: 0.15),
              shape: BoxShape.circle,
            ),
            child: Icon(Icons.school_rounded, size: 12, color: color),
          ),
          const SizedBox(width: 8),
          Flexible(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                if (showKelompok && kelasInfo.kelompok != null)
                  Text(
                    'Kelompok: ${kelasInfo.kelompok}',
                    style: TextStyle(fontSize: 8, color: color.withValues(alpha: 0.8)),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                Text(
                  kelasInfo.namaKelas,
                  style: TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.bold,
                    color: color,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
              ],
            ),
          ),
          if (kelasInfo.isPrimary) ...[
            const SizedBox(width: 7),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 2),
              decoration: BoxDecoration(
                color: color,
                borderRadius: BorderRadius.circular(7),
              ),
              child: const Text(
                'Utama',
                style: TextStyle(fontSize: 7, fontWeight: FontWeight.bold, color: Colors.white),
              ),
            ),
          ],
        ],
      ),
    );
  }
}

/// Chip badge "+X kelas lainnya" yang bisa di-tap
class KelasLainnyaChip extends StatelessWidget {
  final int count;
  final VoidCallback onTap;

  const KelasLainnyaChip({
    super.key,
    required this.count,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    if (count <= 0) return const SizedBox.shrink();

    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
        decoration: BoxDecoration(
          color: Colors.grey[200],
          borderRadius: BorderRadius.circular(9),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(Icons.add, size: 9, color: Colors.grey[600]),
            const SizedBox(width: 2),
            Text(
              '$count kelas lainnya',
              style: TextStyle(
                fontSize: 8,
                fontWeight: FontWeight.w600,
                color: Colors.grey[600],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
