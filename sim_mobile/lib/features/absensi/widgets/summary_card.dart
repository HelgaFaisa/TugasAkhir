// lib/features/absensi/widgets/summary_card.dart

import 'package:flutter/material.dart';
import '../models/absensi_summary_model.dart';

class SummaryCard extends StatelessWidget {
  final AbsensiSummary summary;

  const SummaryCard({
    super.key,
    required this.summary,
  });

  @override
  Widget build(BuildContext context) {
    return GridView.count(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      crossAxisCount: 2,
      mainAxisSpacing: 8,
      crossAxisSpacing: 8,
      childAspectRatio: 2.2,
      padding: EdgeInsets.zero,
      children: [
        _buildSummaryItem(
          icon: Icons.check_circle,
          label: 'Hadir',
          count: summary.hadir,
          color: Colors.green,
          percentage: summary.total > 0 
              ? (summary.hadir / summary.total * 100).toStringAsFixed(0)
              : '0',
        ),
        _buildSummaryItem(
          icon: Icons.info_outline,
          label: 'Izin',
          count: summary.izin,
          color: Colors.orange,
          percentage: summary.total > 0 
              ? (summary.izin / summary.total * 100).toStringAsFixed(0)
              : '0',
        ),
        _buildSummaryItem(
          icon: Icons.local_hospital_outlined,
          label: 'Sakit',
          count: summary.sakit,
          color: Colors.blue,
          percentage: summary.total > 0 
              ? (summary.sakit / summary.total * 100).toStringAsFixed(0)
              : '0',
        ),
        _buildSummaryItem(
          icon: Icons.cancel_outlined,
          label: 'Alpa',
          count: summary.alpa,
          color: Colors.red,
          percentage: summary.total > 0 
              ? (summary.alpa / summary.total * 100).toStringAsFixed(0)
              : '0',
        ),
      ],
    );
  }

  Widget _buildSummaryItem({
    required IconData icon,
    required String label,
    required int count,
    required Color color,
    required String percentage,
  }) {
    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(10),
        side: BorderSide(
          color: color.withOpacity(0.3),
          width: 1.5,
        ),
      ),
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 6),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          mainAxisSize: MainAxisSize.min,
          children: [
            // Icon dan Count dalam satu Row
            Flexible(
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                mainAxisSize: MainAxisSize.min,
                children: [
                  Container(
                    padding: const EdgeInsets.all(4),
                    decoration: BoxDecoration(
                      color: color.withOpacity(0.1),
                      shape: BoxShape.circle,
                    ),
                    child: Icon(
                      icon,
                      color: color,
                      size: 14,
                    ),
                  ),
                  const SizedBox(width: 4),
                  Flexible(
                    child: FittedBox(
                      fit: BoxFit.scaleDown,
                      child: Text(
                        count.toString(),
                        style: TextStyle(
                          fontSize: 22,
                          fontWeight: FontWeight.bold,
                          color: color,
                          height: 1.0,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 2),
            // Label
            Flexible(
              child: Text(
                label,
                style: TextStyle(
                  fontSize: 11,
                  color: Colors.grey[700],
                  fontWeight: FontWeight.w500,
                  height: 1.2,
                ),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),
            ),
            // Percentage
            Flexible(
              child: Text(
                '$percentage%',
                style: TextStyle(
                  fontSize: 9,
                  color: Colors.grey[600],
                  fontWeight: FontWeight.w400,
                  height: 1.0,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
