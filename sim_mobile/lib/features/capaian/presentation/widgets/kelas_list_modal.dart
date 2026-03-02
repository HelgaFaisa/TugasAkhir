// lib/features/capaian/presentation/widgets/kelas_list_modal.dart
// Modal bottom sheet untuk menampilkan semua kelas santri

import 'package:flutter/material.dart';
import '../../models/kelas_info_model.dart';
import 'kelas_badge.dart';

class KelasListModal extends StatelessWidget {
  final List<KelasInfo> kelasList;
  final String santriName;

  const KelasListModal({
    super.key,
    required this.kelasList,
    required this.santriName,
  });

  /// Show modal bottom sheet
  static void show(BuildContext context, {
    required List<KelasInfo> kelasList,
    required String santriName,
  }) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      backgroundColor: Colors.white,
      isScrollControlled: true,
      builder: (ctx) => KelasListModal(
        kelasList: kelasList,
        santriName: santriName,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: Container(
        constraints: BoxConstraints(
          maxHeight: MediaQuery.of(context).size.height * 0.6,
        ),
        padding: const EdgeInsets.fromLTRB(15, 9, 15, 15),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Drag handle
            Container(
              width: 31,
              height: 2,
              decoration: BoxDecoration(
                color: Colors.grey[300],
                borderRadius: BorderRadius.circular(2),
              ),
            ),
            const SizedBox(height: 12),
            // Title
            Row(
              children: [
                const Icon(Icons.school_rounded, color: Color(0xFF6FBA9D), size: 17),
                const SizedBox(width: 8),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Daftar Kelas',
                        style: TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.bold,
                          color: Color(0xFF1E1B4B),
                        ),
                      ),
                      Text(
                        santriName,
                        style: TextStyle(fontSize: 11, color: Colors.grey[500]),
                      ),
                    ],
                  ),
                ),
                IconButton(
                  icon: const Icon(Icons.close_rounded, color: Colors.grey),
                  onPressed: () => Navigator.pop(context),
                ),
              ],
            ),
            const Divider(height: 19),
            // Kelas list
            Flexible(
              child: ListView.separated(
                shrinkWrap: true,
                itemCount: kelasList.length,
                separatorBuilder: (_, __) => const SizedBox(height: 8),
                itemBuilder: (ctx, i) {
                  final kelas = kelasList[i];
                  final color = KelasBadge.getKelompokColor(kelas.kelompok);

                  return Container(
                    padding: const EdgeInsets.all(11),
                    decoration: BoxDecoration(
                      color: color.withValues(alpha: 0.06),
                      borderRadius: BorderRadius.circular(11),
                      border: Border.all(
                        color: kelas.isPrimary
                            ? color.withValues(alpha: 0.4)
                            : Colors.grey.withValues(alpha: 0.15),
                        width: kelas.isPrimary ? 1.5 : 1,
                      ),
                    ),
                    child: Row(
                      children: [
                        Container(
                          width: 32,
                          height: 32,
                          decoration: BoxDecoration(
                            color: color.withValues(alpha: 0.15),
                            borderRadius: BorderRadius.circular(9),
                          ),
                          child: Icon(Icons.school_rounded, color: color, size: 17),
                        ),
                        const SizedBox(width: 11),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                kelas.namaKelas,
                                style: TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.bold,
                                  color: color,
                                ),
                              ),
                              if (kelas.kelompok != null) ...[
                                const SizedBox(height: 2),
                                Text(
                                  'Kelompok: ${kelas.kelompok}',
                                  style: TextStyle(
                                    fontSize: 9,
                                    color: Colors.grey[600],
                                  ),
                                ),
                              ],
                              if (kelas.tahunAjaran != null) ...[
                                const SizedBox(height: 2),
                                Text(
                                  'TA: ${kelas.tahunAjaran}',
                                  style: TextStyle(
                                    fontSize: 8,
                                    color: Colors.grey[400],
                                  ),
                                ),
                              ],
                            ],
                          ),
                        ),
                        if (kelas.isPrimary)
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                            decoration: BoxDecoration(
                              color: color,
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: const Text(
                              'Utama',
                              style: TextStyle(
                                fontSize: 8,
                                fontWeight: FontWeight.bold,
                                color: Colors.white,
                              ),
                            ),
                          ),
                      ],
                    ),
                  );
                },
              ),
            ),
          ],
        ),
      ),
    );
  }
}
