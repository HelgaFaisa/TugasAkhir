// lib/features/capaian/presentation/pages/semester_report_page.dart
// Full semester report card view (shareable/screenshot-friendly)

import 'package:flutter/material.dart';
import '../../models/capaian_dashboard_model.dart';

const _kPurple = Color(0xFF7C3AED);
const _kPurpleDark = Color(0xFF5B21B6);
const _kGreen = Color(0xFF10B981);
const _kOrange = Color(0xFFF59E0B);
const _kRed = Color(0xFFEF4444);
const _kBlue = Color(0xFF3B82F6);

class SemesterReportPage extends StatelessWidget {
  final CapaianDashboard data;

  const SemesterReportPage({super.key, required this.data});

  @override
  Widget build(BuildContext context) {
    final rapor = data.raporSummary;
    final santri = data.santri;
    final semester = data.semester;

    Color predikatColor;
    IconData predikatIcon;
    switch (rapor.predikat) {
      case 'Baik Sekali':
        predikatColor = _kGreen;
        predikatIcon = Icons.emoji_events_rounded;
        break;
      case 'Baik':
        predikatColor = _kBlue;
        predikatIcon = Icons.thumb_up_rounded;
        break;
      case 'Cukup':
        predikatColor = _kOrange;
        predikatIcon = Icons.thumbs_up_down_rounded;
        break;
      default:
        predikatColor = _kRed;
        predikatIcon = Icons.warning_rounded;
    }

    return Scaffold(
      backgroundColor: const Color(0xFFF5F3FF),
      appBar: AppBar(
        title: const Text('Rapor Semester'),
        backgroundColor: _kPurple,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            // ===== HEADER CARD =====
            Container(
              width: double.infinity,
              decoration: BoxDecoration(
                gradient: const LinearGradient(colors: [_kPurple, _kPurpleDark]),
                borderRadius: BorderRadius.circular(20),
              ),
              padding: const EdgeInsets.all(24),
              child: Column(
                children: [
                  const Text(
                    'RAPOR CAPAIAN',
                    style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: Colors.white60, letterSpacing: 2),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    semester.namaSemester,
                    style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.white),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 16),
                  const Divider(color: Colors.white24),
                  const SizedBox(height: 12),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      _infoItem('Nama', santri.namaLengkap),
                      _infoItem('Kelas', santri.kelas),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // ===== PREDIKAT =====
            Container(
              width: double.infinity,
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.05), blurRadius: 12, offset: const Offset(0, 4))],
              ),
              padding: const EdgeInsets.symmetric(vertical: 24, horizontal: 20),
              child: Column(
                children: [
                  Icon(predikatIcon, size: 48, color: predikatColor),
                  const SizedBox(height: 8),
                  Text(
                    rapor.predikat,
                    style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: predikatColor),
                  ),
                  const SizedBox(height: 8),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                    decoration: BoxDecoration(
                      color: predikatColor.withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Text(
                      'Progress: ${rapor.totalProgress.toStringAsFixed(1)}%',
                      style: TextStyle(fontSize: 16, fontWeight: FontWeight.w600, color: predikatColor),
                    ),
                  ),
                  if (rapor.perubahan != 0) ...[
                    const SizedBox(height: 8),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(
                          rapor.trend == 'naik' ? Icons.trending_up_rounded : Icons.trending_down_rounded,
                          size: 16,
                          color: rapor.trend == 'naik' ? _kGreen : _kRed,
                        ),
                        const SizedBox(width: 4),
                        Text(
                          '${rapor.perubahan > 0 ? '+' : ''}${rapor.perubahan.toStringAsFixed(1)}% dari semester lalu',
                          style: TextStyle(fontSize: 13, color: rapor.trend == 'naik' ? _kGreen : _kRed),
                        ),
                      ],
                    ),
                  ],
                ],
              ),
            ),
            const SizedBox(height: 20),

            // ===== STATISTIK RINGKASAN =====
            Row(
              children: [
                _statBox('Total Materi', '${rapor.totalMateri}', Icons.menu_book_rounded, _kBlue),
                const SizedBox(width: 12),
                _statBox('Selesai', '${rapor.materiSelesai}', Icons.check_circle_rounded, _kGreen),
                const SizedBox(width: 12),
                _statBox('Sisa', '${rapor.totalMateri - rapor.materiSelesai}', Icons.pending_rounded, _kOrange),
              ],
            ),
            const SizedBox(height: 20),

            // ===== PER KATEGORI =====
            Container(
              width: double.infinity,
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.05), blurRadius: 12, offset: const Offset(0, 4))],
              ),
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Row(
                    children: [
                      Icon(Icons.category_rounded, size: 20, color: _kPurple),
                      SizedBox(width: 8),
                      Text('Progress per Kategori', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                    ],
                  ),
                  const SizedBox(height: 16),
                  ...data.currentProgress.perKategori.map((k) => _kategoriRow(k)),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // ===== DAFTAR MATERI =====
            Container(
              width: double.infinity,
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.05), blurRadius: 12, offset: const Offset(0, 4))],
              ),
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Row(
                    children: [
                      Icon(Icons.list_alt_rounded, size: 20, color: _kPurple),
                      SizedBox(width: 8),
                      Text('Detail Materi', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                    ],
                  ),
                  const SizedBox(height: 12),
                  // Header
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                    decoration: BoxDecoration(color: const Color(0xFFF5F3FF), borderRadius: BorderRadius.circular(8)),
                    child: Row(
                      children: [
                        const Expanded(flex: 3, child: Text('Materi', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: _kPurple))),
                        const Expanded(flex: 2, child: Text('Kategori', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: _kPurple))),
                        SizedBox(width: 60, child: const Text('Status', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: _kPurple), textAlign: TextAlign.right)),
                      ],
                    ),
                  ),
                  ...data.materiStatus.map((m) => _materiRow(m)),
                  if (data.materiStatus.isEmpty)
                    const Padding(
                      padding: EdgeInsets.all(16),
                      child: Center(child: Text('Belum ada data', style: TextStyle(color: Colors.grey))),
                    ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // ===== PEER COMPARISON =====
            if (data.peerComparison.isNotEmpty)
              Container(
                width: double.infinity,
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(16),
                  boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.05), blurRadius: 12, offset: const Offset(0, 4))],
                ),
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Row(
                      children: [
                        Icon(Icons.people_rounded, size: 20, color: _kPurple),
                        SizedBox(width: 8),
                        Text('Perbandingan Kelas', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                      ],
                    ),
                    const SizedBox(height: 16),
                    ...data.peerComparison.map((p) => _peerRow(p)),
                  ],
                ),
              ),
            const SizedBox(height: 20),

            // ===== RANKING =====
            if (data.rank != null)
              Container(
                width: double.infinity,
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [_kPurple.withValues(alpha: 0.08), _kPurple.withValues(alpha: 0.03)],
                  ),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: _kPurple.withValues(alpha: 0.2)),
                ),
                padding: const EdgeInsets.all(20),
                child: Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: _kPurple.withValues(alpha: 0.15),
                        shape: BoxShape.circle,
                      ),
                      child: Text(
                        '${data.rank!.position}',
                        style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: _kPurple),
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text('Peringkat di Kelas', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                          Text('Dari ${data.rank!.total} santri aktif', style: TextStyle(fontSize: 13, color: Colors.grey[600])),
                        ],
                      ),
                    ),
                    Icon(
                      data.rank!.position <= 3 ? Icons.star_rounded : Icons.emoji_events_outlined,
                      size: 32,
                      color: data.rank!.position <= 3 ? const Color(0xFFFFB020) : _kPurple,
                    ),
                  ],
                ),
              ),
          ],
        ),
      ),
    );
  }

  Widget _infoItem(String label, String value) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontSize: 11, color: Colors.white54)),
        const SizedBox(height: 2),
        Text(value, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: Colors.white)),
      ],
    );
  }

  Widget _statBox(String label, String value, IconData icon, Color color) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(14),
          boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.05), blurRadius: 10, offset: const Offset(0, 2))],
        ),
        child: Column(
          children: [
            Icon(icon, size: 24, color: color),
            const SizedBox(height: 6),
            Text(value, style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: color)),
            Text(label, style: TextStyle(fontSize: 11, color: Colors.grey[600]), textAlign: TextAlign.center),
          ],
        ),
      ),
    );
  }

  Widget _kategoriRow(KategoriProgress k) {
    final color = _parseColor(k.color);
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(_getIcon(k.icon), size: 16, color: color),
              const SizedBox(width: 6),
              Expanded(child: Text(k.kategori, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w500))),
              Text('${k.rataRataProgress.toStringAsFixed(1)}%', style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: color)),
            ],
          ),
          const SizedBox(height: 6),
          ClipRRect(
            borderRadius: BorderRadius.circular(4),
            child: LinearProgressIndicator(
              value: (k.rataRataProgress / 100).clamp(0.0, 1.0),
              backgroundColor: Colors.grey[200],
              valueColor: AlwaysStoppedAnimation(color),
              minHeight: 8,
            ),
          ),
          const SizedBox(height: 2),
          Text('${k.materiSelesai}/${k.totalMateri} materi selesai', style: TextStyle(fontSize: 11, color: Colors.grey[500])),
        ],
      ),
    );
  }

  Widget _materiRow(MateriStatusItem m) {
    final statusColor = _parseColor(m.statusColor);
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
      child: Row(
        children: [
          Expanded(
            flex: 3,
            child: Text(m.namaKitab, style: const TextStyle(fontSize: 13), maxLines: 1, overflow: TextOverflow.ellipsis),
          ),
          Expanded(
            flex: 2,
            child: Text(m.kategori, style: TextStyle(fontSize: 11, color: Colors.grey[600]), maxLines: 1, overflow: TextOverflow.ellipsis),
          ),
          SizedBox(
            width: 60,
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
              decoration: BoxDecoration(
                color: statusColor.withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Text(
                '${m.persentase.toStringAsFixed(0)}%',
                style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: statusColor),
                textAlign: TextAlign.center,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _peerRow(PeerComparisonItem p) {
    final color = _parseColor(p.color);
    final diff = p.santriProgress - p.kelasAvg;
    final isAhead = diff >= 0;
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Row(
        children: [
          Icon(_getIcon(p.icon), size: 18, color: color),
          const SizedBox(width: 8),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(p.kategori, style: TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: Colors.grey[800])),
                const SizedBox(height: 2),
                Text(
                  'Kamu: ${p.santriProgress.toStringAsFixed(1)}%  |  Kelas: ${p.kelasAvg.toStringAsFixed(1)}%',
                  style: TextStyle(fontSize: 12, color: Colors.grey[500]),
                ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
            decoration: BoxDecoration(
              color: (isAhead ? _kGreen : _kRed).withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Text(
              '${isAhead ? '+' : ''}${diff.toStringAsFixed(1)}%',
              style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: isAhead ? _kGreen : _kRed),
            ),
          ),
        ],
      ),
    );
  }

  Color _parseColor(String hex) {
    try {
      return Color(int.parse(hex.replaceFirst('#', '0xFF')));
    } catch (_) {
      return Colors.grey;
    }
  }

  IconData _getIcon(String name) {
    switch (name) {
      case 'book_quran': return Icons.menu_book;
      case 'scroll': return Icons.article;
      case 'book': return Icons.book;
      default: return Icons.book;
    }
  }
}
