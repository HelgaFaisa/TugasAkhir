// lib/features/capaian/presentation/pages/capaian_page.dart
// REDESIGNED: Comprehensive capaian dashboard with new kelas system support

import 'dart:math';
import 'package:flutter/material.dart';
import '../../../../core/api/api_service.dart';
import '../../models/capaian_dashboard_model.dart';
import '../widgets/kelas_badge.dart';
import '../widgets/kelas_list_modal.dart';
import 'detail_capaian_page.dart';
import 'semester_report_page.dart';

// ============================================
// COLOR CONSTANTS
// ============================================
const _kPrimary = Color(0xFF6FBA9D);
const _kPrimaryDark = Color(0xFF4D987B);
const _kPrimaryLight = Color(0xFFE8F7F2);
const _kOrange = Color(0xFFF59E0B);
const _kRed = Color(0xFFEF4444);
const _kBlue = Color(0xFF3B82F6);

class CapaianPage extends StatefulWidget {
  const CapaianPage({super.key});

  @override
  State<CapaianPage> createState() => _CapaianPageState();
}

class _CapaianPageState extends State<CapaianPage> with TickerProviderStateMixin {
  final _api = ApiService();

  CapaianDashboard? _data; 
  bool _isLoading = true;
  String? _selectedSemester;
  late PageController _semesterPageController;
  int _currentSemesterPage = 0;

  @override
  void initState() {
    super.initState();
    _semesterPageController = PageController(viewportFraction: 0.92);
    _loadData();
  }

  @override
  void dispose() {
    _semesterPageController.dispose();
    super.dispose();
  }

  Future<void> _loadData() async {
    setState(() => _isLoading = true);
    final result = await _api.getCapaianDashboard(idSemester: _selectedSemester);

    if (mounted) {
      if (result['success'] == true && result['data'] != null) {
        setState(() {
          _data = CapaianDashboard.fromJson(result['data']);
          _isLoading = false;
        });
      } else {
        setState(() => _isLoading = false);
        if (result['message'] != null) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text(result['message']), backgroundColor: Colors.red),
          );
        }
      }
    }
  }

  void _showSemesterPicker() {
    if (_data == null) return;
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      builder: (ctx) => _SemesterPickerSheet(
        semesters: _data!.listSemester,
        selected: _selectedSemester,
        onSelect: (id) {
          setState(() => _selectedSemester = id);
          Navigator.pop(ctx);
          _loadData();
        },
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F3FF),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: _kPrimary))
          : _data == null
              ? _buildError()
              : RefreshIndicator(
                  color: _kPrimary,
                  onRefresh: _loadData,
                  child: CustomScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    slivers: [
                      _buildSliverAppBar(),
                      SliverToBoxAdapter(child: _buildBody()),
                    ],
                  ),
                ),
    );
  }

  // ============================================
  // SLIVER APP BAR WITH GRADIENT
  // ============================================
  Widget _buildSliverAppBar() {
    final d = _data!;
    return SliverAppBar(
      expandedHeight: MediaQuery.of(context).size.height * 0.28,
      pinned: true,
      backgroundColor: _kPrimary,
      foregroundColor: Colors.white,
      title: const Text('Capaian Santri', style: TextStyle(fontSize: 15, fontWeight: FontWeight.w600)),
      actions: [
        IconButton(
          icon: const Icon(Icons.calendar_month_rounded),
          onPressed: _showSemesterPicker,
          tooltip: 'Pilih Semester',
        ),
      ],
      flexibleSpace: FlexibleSpaceBar(
        background: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [_kPrimary, _kPrimaryDark, Color(0xFF3D8A6B)],
            ),
          ),
          child: SafeArea(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(19, 43, 19, 12),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  Text(
                    d.santri.namaLengkap,
                    style: const TextStyle(
                      fontSize: 17, fontWeight: FontWeight.bold, color: Colors.white,
                    ),
                    maxLines: 1, overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 5),
                  Row(
                    children: [
                      // Kelas Primary Badge
                      _buildKelasChip(d.santri),
                      // "+X kelas lainnya" chip
                      if (d.santri.hasMultipleKelas) ...[
                        const SizedBox(width: 5),
                        GestureDetector(
                          onTap: () => KelasListModal.show(
                            context,
                            kelasList: d.santri.allKelas,
                            santriName: d.santri.namaLengkap,
                          ),
                          child: Container(
                            padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 2),
                            decoration: BoxDecoration(
                              color: Colors.white.withValues(alpha: 0.2),
                              borderRadius: BorderRadius.circular(15),
                            ),
                            child: Text(
                              '+${d.santri.kelasLainnyaCount} kelas lainnya',
                              style: const TextStyle(fontSize: 8, color: Colors.white70, fontWeight: FontWeight.w500),
                            ),
                          ),
                        ),
                      ],
                    ],
                  ),
                  const SizedBox(height: 2),
                  _chip(d.semester.namaSemester, Icons.date_range_rounded),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  /// Build kelas chip from kelasPrimary or fallback
  Widget _buildKelasChip(SantriInfo santri) {
    final kelasPrimary = santri.kelasPrimary;
    final kelompokColor = KelasBadge.getKelompokColor(kelasPrimary?.kelompok);

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
      decoration: BoxDecoration(
        color: kelompokColor.withValues(alpha: 0.25),
        borderRadius: BorderRadius.circular(15),
        border: Border.all(color: Colors.white.withValues(alpha: 0.2)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          const Icon(Icons.school_rounded, size: 11, color: Colors.white),
          const SizedBox(width: 2),
          Text(
            kelasPrimary != null
                ? (kelasPrimary.kelompok != null
                    ? '${kelasPrimary.kelompok}: ${kelasPrimary.namaKelas}'
                    : kelasPrimary.namaKelas)
                : santri.kelasDisplayName,
            style: const TextStyle(fontSize: 9, color: Colors.white, fontWeight: FontWeight.w600),
          ),
        ],
      ),
    );
  }

  Widget _chip(String label, IconData icon) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
      decoration: BoxDecoration(
        color: Colors.white.withValues(alpha: 0.15),
        borderRadius: BorderRadius.circular(15),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 11, color: Colors.white70),
          const SizedBox(width: 2),
          Text(label, style: const TextStyle(fontSize: 9, color: Colors.white, fontWeight: FontWeight.w500)),
        ],
      ),
    );
  }

  // ============================================
  // BODY
  // ============================================
  Widget _buildBody() {
    final d = _data!;
    final isWali = d.role == 'wali';

    return Padding(
      padding: const EdgeInsets.only(bottom: 24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // 8. Parent Summary (if wali) â€” shown first and prominent
          if (isWali) _buildParentSummary(d),

          // Summary Stats Cards (2x2 grid)
          _buildSummaryStatsGrid(d),

          // Kalkulasi Progress (progress, kecepatan, estimasi khatam)
          _buildSectionTitle('Kalkulasi Progress', Icons.analytics_rounded),
          _buildProgressKalkulasi(d),

          // 1. Semester Progress Cards (swipeable)
          _buildSectionTitle('Progress Semester', Icons.auto_graph_rounded),
          _buildSemesterProgressCards(d),

          // 3. Achievement Badges
          if (d.achievements.isNotEmpty) ...[
            _buildSectionTitle('Pencapaian', Icons.emoji_events_rounded),
            _buildAchievements(d.achievements),
          ],

          // 2. Progress Comparison Timeline
          if (d.semesterHistory.length > 1) ...[
            _buildSectionTitle('Timeline Progress', Icons.timeline_rounded),
            _buildProgressTimeline(d.semesterHistory),
          ],

          // 7. Historical Graph
          if (d.semesterHistory.length >= 2) ...[
            _buildSectionTitle('Grafik Historis', Icons.show_chart_rounded),
            _buildHistoricalGraph(d.semesterHistory),
          ],

          // 4. Materi Completion Status
          _buildSectionTitle('Status Materi', Icons.checklist_rounded),
          _buildMateriStatus(d.materiStatus),

          // 5. Semester Report Card Button
          _buildRaporButton(d),
        ],
      ),
    );
  }

  // ============================================
  // SUMMARY STATS GRID (2x2)
  // ============================================
  Widget _buildSummaryStatsGrid(CapaianDashboard d) {
    final progress = d.currentProgress;
    final rank = d.rank;
    final screenWidth = MediaQuery.of(context).size.width;
    // Responsive aspect ratio: taller cards on smaller screens
    final aspectRatio = screenWidth < 360 ? 1.1 : (screenWidth < 400 ? 1.2 : 1.35);

    return Padding(
      padding: const EdgeInsets.fromLTRB(15, 12, 15, 0),
      child: GridView.count(
        crossAxisCount: 2,
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        mainAxisSpacing: 12,
        crossAxisSpacing: 12,
        childAspectRatio: aspectRatio,
        children: [
          _summaryCard(
            icon: Icons.book_rounded,
            label: 'Total Capaian',
            value: '${progress.totalMateri}',
            color: _kPrimary,
          ),
          _summaryCard(
            icon: Icons.trending_up_rounded,
            label: 'Rata-rata Progress',
            value: '${progress.totalProgress.toStringAsFixed(1)}%',
            color: _kBlue,
          ),
          _summaryCard(
            icon: Icons.check_circle_rounded,
            label: 'Materi Selesai',
            value: '${progress.materiSelesai}',
            color: _kPrimary,
          ),
          _summaryCard(
            icon: Icons.emoji_events_rounded,
            label: 'Ranking Kelas',
            value: rank != null ? '#${rank.position}/${rank.total}' : '-',
            color: _kOrange,
          ),
        ],
      ),
    );
  }

  Widget _summaryCard({
    required IconData icon,
    required String label,
    required String value,
    required Color color,
  }) {
    return Container(
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [color.withValues(alpha: 0.12), color.withValues(alpha: 0.04)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(11),
        border: Border.all(color: color.withValues(alpha: 0.15)),
      ),
      padding: const EdgeInsets.all(9),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Container(
            padding: const EdgeInsets.all(5),
            decoration: BoxDecoration(
              color: color.withValues(alpha: 0.15),
              borderRadius: BorderRadius.circular(7),
            ),
            child: Icon(icon, size: 12, color: color),
          ),
          const Spacer(),
          Text(
            value,
            style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: color),
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
          ),
          const SizedBox(height: 2),
          Text(
            label,
            style: TextStyle(fontSize: 8, color: Colors.grey[600]),
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
          ),
        ],
      ),
    );
  }

  // ============================================
  // KALKULASI PROGRESS (Progress, Kecepatan, Estimasi Khatam)
  // ============================================
  Widget _buildProgressKalkulasi(CapaianDashboard d) {
    final progress = d.currentProgress;
    final history = d.semesterHistory;
    final currentPct = progress.totalProgress.clamp(0.0, 100.0);

    // Growth rate: avg difference between consecutive semesters (chronological order)
    // semesterHistory comes newest-first, so reverse for chronological
    double growthRate = 0;
    if (history.length >= 2) {
      final chrono = history.reversed.toList();
      final diffs = <double>[];
      for (int i = 1; i < chrono.length; i++) {
        diffs.add(chrono[i].rataRataProgress - chrono[i - 1].rataRataProgress);
      }
      growthRate = diffs.reduce((a, b) => a + b) / diffs.length;
    } else if (history.length == 1) {
      growthRate = history.first.rataRataProgress;
    } else {
      growthRate = currentPct;
    }
    growthRate = double.parse(growthRate.toStringAsFixed(2));

    // Estimasi khatam
    String estimasiValue;
    String estimasiSub;
    if (currentPct >= 100) {
      estimasiValue = 'Khatam!';
      estimasiSub = 'Semua materi selesai 🎉';
    } else if (growthRate <= 0) {
      estimasiValue = 'Stagnan';
      estimasiSub = 'Progress belum meningkat';
    } else {
      final semToFinish = ((100 - currentPct) / growthRate).ceil();
      estimasiValue = '$semToFinish Sem';
      estimasiSub = 'lagi dari sekarang';
    }

    // Kecepatan label
    String kecepatanValue;
    String kecepatanSub;
    if (growthRate > 0) {
      kecepatanValue = '+${growthRate.toStringAsFixed(1)}%';
      kecepatanSub = 'per semester (rata-rata)';
    } else if (growthRate < 0) {
      kecepatanValue = '${growthRate.toStringAsFixed(1)}%';
      kecepatanSub = 'penurunan per semester';
    } else {
      kecepatanValue = 'Stagnan';
      kecepatanSub = 'belum ada data cukup';
    }

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 15),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(14),
          boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.06), blurRadius: 12, offset: const Offset(0, 4))],
        ),
        padding: const EdgeInsets.all(14),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Progress Saat Ini — full bar
            Row(
              children: [
                const Icon(Icons.donut_large_rounded, size: 13, color: _kPrimary),
                const SizedBox(width: 5),
                const Expanded(child: Text('Progress Saat Ini', style: TextStyle(fontSize: 10, fontWeight: FontWeight.w600))),
                Text(
                  '${currentPct.toStringAsFixed(1)}%',
                  style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: _kPrimary),
                ),
              ],
            ),
            const SizedBox(height: 6),
            ClipRRect(
              borderRadius: BorderRadius.circular(5),
              child: LinearProgressIndicator(
                value: currentPct / 100,
                backgroundColor: Colors.grey[200],
                valueColor: const AlwaysStoppedAnimation(_kPrimary),
                minHeight: 10,
              ),
            ),
            const SizedBox(height: 5),
            Text(
              '${progress.materiSelesai} dari ${progress.totalMateri} materi selesai',
              style: TextStyle(fontSize: 9, color: Colors.grey[500]),
            ),
            const SizedBox(height: 13),
            const Divider(height: 1, color: Color(0xFFEEEEEE)),
            const SizedBox(height: 13),
            // Kecepatan Belajar + Estimasi Khatam side by side
            Row(
              children: [
                Expanded(
                  child: _kalkulasiItem(
                    icon: Icons.speed_rounded,
                    color: _kBlue,
                    label: 'Kecepatan Belajar',
                    value: kecepatanValue,
                    sub: kecepatanSub,
                  ),
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: _kalkulasiItem(
                    icon: Icons.flag_rounded,
                    color: currentPct >= 100 ? _kPrimary : (growthRate <= 0 ? _kRed : _kOrange),
                    label: 'Estimasi Khatam',
                    value: estimasiValue,
                    sub: estimasiSub,
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _kalkulasiItem({
    required IconData icon,
    required Color color,
    required String label,
    required String value,
    required String sub,
  }) {
    return Container(
      padding: const EdgeInsets.all(10),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.07),
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: color.withValues(alpha: 0.18)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, size: 14, color: color),
          const SizedBox(height: 6),
          Text(value,
              style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: color),
              maxLines: 1,
              overflow: TextOverflow.ellipsis),
          const SizedBox(height: 2),
          Text(label,
              style: const TextStyle(fontSize: 9, fontWeight: FontWeight.w600, color: Color(0xFF374151))),
          const SizedBox(height: 1),
          Text(sub,
              style: TextStyle(fontSize: 8, color: Colors.grey[500]),
              maxLines: 2,
              overflow: TextOverflow.ellipsis),
        ],
      ),
    );
  }

  Widget _buildError() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Icon(Icons.cloud_off_rounded, size: 55, color: Colors.grey),
          const SizedBox(height: 12),
          const Text('Gagal memuat data', style: TextStyle(fontSize: 12, color: Colors.grey)),
          const SizedBox(height: 12),
          ElevatedButton.icon(
            onPressed: _loadData,
            icon: const Icon(Icons.refresh),
            label: const Text('Coba Lagi'),
            style: ElevatedButton.styleFrom(backgroundColor: _kPrimary, foregroundColor: Colors.white),
          ),
        ],
      ),
    );
  }

  Widget _buildSectionTitle(String title, IconData icon) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(15, 19, 15, 9),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(5),
            decoration: BoxDecoration(
              color: _kPrimaryLight,
              borderRadius: BorderRadius.circular(7),
            ),
            child: Icon(icon, size: 15, color: _kPrimary),
          ),
          const SizedBox(width: 8),
          Text(title, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold, color: Color(0xFF1E1B4B))),
        ],
      ),
    );
  }

  // ============================================
  // 1. SEMESTER PROGRESS CARDS (Swipeable PageView)
  // ============================================
  Widget _buildSemesterProgressCards(CapaianDashboard d) {
    // Build cards: current semester first, then history (reversed so latest first)
    final history = d.semesterHistory.reversed.toList();
    if (history.isEmpty) {
      // Show just current progress as single card
      return Padding(
        padding: const EdgeInsets.symmetric(horizontal: 15),
        child: _SemesterProgressCard(
          semesterName: d.semester.namaSemester,
          totalProgress: d.currentProgress.totalProgress,
          perKategori: d.currentProgress.perKategori,
          isCurrent: true,
        ),
      );
    }

    return Column(
      children: [
        SizedBox(
          height: MediaQuery.of(context).size.height * 0.28,
          child: PageView.builder(
            controller: _semesterPageController,
            itemCount: history.length,
            onPageChanged: (i) => setState(() => _currentSemesterPage = i),
            itemBuilder: (ctx, i) {
              final sem = history[i];
              // If this is the current semester, use detailed per_kategori data
              final isCurrentSem = sem.isCurrent;
              return Padding(
                padding: const EdgeInsets.symmetric(horizontal: 2),
                child: _SemesterProgressCard(
                  semesterName: sem.namaSemester,
                  totalProgress: isCurrentSem ? d.currentProgress.totalProgress : sem.rataRataProgress,
                  perKategori: isCurrentSem ? d.currentProgress.perKategori : null,
                  totalMateri: sem.totalMateri,
                  materiSelesai: sem.materiSelesai,
                  isCurrent: sem.isCurrent,
                ),
              );
            },
          ),
        ),
        const SizedBox(height: 7),
        // Dot indicators
        Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: List.generate(history.length, (i) {
            return AnimatedContainer(
              duration: const Duration(milliseconds: 300),
              margin: const EdgeInsets.symmetric(horizontal: 2),
              width: _currentSemesterPage == i ? 24 : 8,
              height: 7,
              decoration: BoxDecoration(
                color: _currentSemesterPage == i ? _kPrimary : Colors.grey[300],
                borderRadius: BorderRadius.circular(2),
              ),
            );
          }),
        ),
      ],
    );
  }

  // ============================================
  // 2. PROGRESS COMPARISON TIMELINE
  // ============================================
  Widget _buildProgressTimeline(List<SemesterHistoryItem> history) {
    final reversed = history.reversed.toList();
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 15),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.04), blurRadius: 12, offset: const Offset(0, 4))],
        ),
        padding: const EdgeInsets.all(12),
        child: Column(
          children: List.generate(reversed.length, (i) {
            final item = reversed[i];
            double? change;
            if (i < reversed.length - 1) {
              change = item.rataRataProgress - reversed[i + 1].rataRataProgress;
            }
            final isLast = i == reversed.length - 1;

            return Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Timeline line + dot
                SizedBox(
                  width: 27,
                  child: Column(
                    children: [
                      Container(
                        width: 11,
                        height: 11,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          color: item.isCurrent ? _kPrimary : Colors.grey[300],
                          border: Border.all(color: item.isCurrent ? _kPrimaryDark : Colors.grey[400]!, width: 2),
                        ),
                      ),
                      if (!isLast)
                        Container(width: 2, height: 36, color: Colors.grey[300]),
                    ],
                  ),
                ),
                // Content
                Expanded(
                  child: Padding(
                    padding: EdgeInsets.only(bottom: isLast ? 0 : 12),
                    child: Row(
                      children: [
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                item.namaSemester,
                                style: TextStyle(
                                  fontSize: 11,
                                  fontWeight: item.isCurrent ? FontWeight.bold : FontWeight.w500,
                                  color: item.isCurrent ? _kPrimary : Colors.grey[800],
                                ),
                              ),
                              if (item.isCurrent)
                                const Text('Saat Ini', style: TextStyle(fontSize: 8, color: _kPrimary)),
                            ],
                          ),
                        ),
                        Text(
                          '${item.rataRataProgress.toStringAsFixed(1)}%',
                          style: TextStyle(
                            fontSize: 12, fontWeight: FontWeight.bold,
                            color: item.isCurrent ? _kPrimary : Colors.grey[700],
                          ),
                        ),
                        const SizedBox(width: 5),
                        if (change != null)
                          _trendBadge(change)
                        else
                          const SizedBox(width: 27),
                      ],
                    ),
                  ),
                ),
              ],
            );
          }),
        ),
      ),
    );
  }

  Widget _trendBadge(double change) {
    final isUp = change > 0;
    final isNeutral = change == 0;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 2),
      decoration: BoxDecoration(
        color: isNeutral ? Colors.grey[100] : (isUp ? _kPrimary.withValues(alpha: 0.1) : _kRed.withValues(alpha: 0.1)),
        borderRadius: BorderRadius.circular(7),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(
            isNeutral ? Icons.remove : (isUp ? Icons.arrow_upward : Icons.arrow_downward),
            size: 9,
            color: isNeutral ? Colors.grey : (isUp ? _kPrimary : _kRed),
          ),
          if (!isNeutral)
            Text(
              '${change.abs().toStringAsFixed(1)}%',
              style: TextStyle(fontSize: 8, fontWeight: FontWeight.bold, color: isUp ? _kPrimary : _kRed),
            ),
        ],
      ),
    );
  }

  // ============================================
  // 3. ACHIEVEMENT BADGES
  // ============================================
  Widget _buildAchievements(List<AchievementItem> achvs) {
    return SizedBox(
      height: 43,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 15),
        itemCount: achvs.length,
        separatorBuilder: (_, __) => const SizedBox(width: 8),
        itemBuilder: (ctx, i) {
          final a = achvs[i];
          return Container(
            padding: const EdgeInsets.symmetric(horizontal: 11, vertical: 8),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: _achvGradient(a.type),
              ),
              borderRadius: BorderRadius.circular(21),
              boxShadow: [BoxShadow(color: _achvGradient(a.type)[0].withValues(alpha: 0.3), blurRadius: 8, offset: const Offset(0, 3))],
            ),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(_achvEmoji(a.type), style: const TextStyle(fontSize: 15)),
                const SizedBox(width: 7),
                Text(a.text, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: Colors.white)),
              ],
            ),
          );
        },
      ),
    );
  }

  List<Color> _achvGradient(String type) {
    switch (type) {
      case 'khatam': return [const Color(0xFFFFB020), const Color(0xFFFF8C00)];
      case 'growth': return [_kPrimary, const Color(0xFF059669)];
      case 'rank': return [_kPrimary, _kPrimaryDark];
      case 'decline': return [_kOrange, _kRed];
      default: return [_kBlue, const Color(0xFF2563EB)];
    }
  }

  String _achvEmoji(String type) {
  const map = {
    'khatam': '🏆',
    'growth': '📈',
    'rank': '⭐',
    'decline': '📉',
  };
  return map[type] ?? '🎯';

  }

  // ============================================
  // 4. MATERI COMPLETION STATUS
  // ============================================
  Widget _buildMateriStatus(List<MateriStatusItem> items) {
    if (items.isEmpty) {
      return Padding(
        padding: const EdgeInsets.symmetric(horizontal: 15),
        child: Container(
          padding: const EdgeInsets.all(19),
          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12)),
          child: const Center(child: Text('Belum ada data materi', style: TextStyle(color: Colors.grey))),
        ),
      );
    }

    final selesai = items.where((m) => m.status == 'selesai').toList();
    final progres = items.where((m) => m.status == 'progres').toList();
    final belum = items.where((m) => m.status == 'belum_mulai').toList();

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 15),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.04), blurRadius: 12, offset: const Offset(0, 4))],
        ),
        child: Column(
          children: [
            // Summary row
            Padding(
              padding: const EdgeInsets.all(12),
              child: Row(
                children: [
                  _statusChip('Selesai', selesai.length, _kPrimary),
                  const SizedBox(width: 7),
                  _statusChip('Progres', progres.length, _kOrange),
                  const SizedBox(width: 7),
                  _statusChip('Belum', belum.length, Colors.grey),
                ],
              ),
            ),
            const Divider(height: 1),
            // Materi list
            ...items.take(10).map((m) => _materiTile(m)),
            if (items.length > 10)
              Padding(
                padding: const EdgeInsets.all(9),
                child: Text('+ ${items.length - 10} materi lainnya', style: TextStyle(fontSize: 11, color: Colors.grey[500])),
              ),
          ],
        ),
      ),
    );
  }

  Widget _statusChip(String label, int count, Color color) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 7),
        decoration: BoxDecoration(
          color: color.withValues(alpha: 0.1),
          borderRadius: BorderRadius.circular(8),
        ),
        child: Column(
          children: [
            Text('$count', style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: color)),
            Text(label, style: TextStyle(fontSize: 8, color: color)),
          ],
        ),
      ),
    );
  }

  Widget _materiTile(MateriStatusItem m) {
    final statusColor = _parseColor(m.statusColor);
    return InkWell(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(builder: (_) => DetailCapaianPage(idCapaian: m.idCapaian, color: _parseColor(m.color))),
        );
      },
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
        child: Row(
          children: [
            Container(
              width: 27, height: 27,
              decoration: BoxDecoration(
                color: _parseColor(m.color).withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(7),
              ),
              child: Icon(_getIconData(m.icon), size: 15, color: _parseColor(m.color)),
            ),
            const SizedBox(width: 9),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(m.namaKitab, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600), maxLines: 1, overflow: TextOverflow.ellipsis),
                  Text(m.kategori, style: TextStyle(fontSize: 8, color: Colors.grey[500])),
                ],
              ),
            ),
            const SizedBox(width: 7),
            SizedBox(
              width: 36,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Text(
                    '${m.persentase.toStringAsFixed(0)}%',
                    style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: statusColor),
                  ),
                  const SizedBox(height: 2),
                  ClipRRect(
                    borderRadius: BorderRadius.circular(2),
                    child: LinearProgressIndicator(
                      value: m.persentase / 100,
                      backgroundColor: Colors.grey[200],
                      valueColor: AlwaysStoppedAnimation(statusColor),
                      minHeight: 4,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(width: 2),
            Icon(Icons.chevron_right, size: 15, color: Colors.grey[400]),
          ],
        ),
      ),
    );
  }

  // ============================================
  // 5. SEMESTER REPORT CARD BUTTON
  // ============================================
  Widget _buildRaporButton(CapaianDashboard d) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(15, 19, 15, 0),
      child: Container(
        decoration: BoxDecoration(
          gradient: const LinearGradient(
            colors: [_kPrimary, _kPrimaryDark],
          ),
          borderRadius: BorderRadius.circular(12),
          boxShadow: [BoxShadow(color: _kPrimary.withValues(alpha: 0.3), blurRadius: 12, offset: const Offset(0, 4))],
        ),
        child: Material(
          color: Colors.transparent,
          child: InkWell(
            borderRadius: BorderRadius.circular(12),
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => SemesterReportPage(data: d)),
              );
            },
            child: Padding(
              padding: const EdgeInsets.all(15),
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(9),
                    decoration: BoxDecoration(
                      color: Colors.white.withValues(alpha: 0.2),
                      borderRadius: BorderRadius.circular(9),
                    ),
                    child: const Icon(Icons.description_rounded, color: Colors.white, size: 21),
                  ),
                  const SizedBox(width: 12),
                  const Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('Lihat Rapor Semester', style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.white)),
                        SizedBox(height: 2),
                        Text('Summary lengkap capaian semester ini', style: TextStyle(fontSize: 11, color: Colors.white70)),
                      ],
                    ),
                  ),
                  const Icon(Icons.arrow_forward_ios_rounded, color: Colors.white70, size: 15),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  // ============================================
  // 6. PEER COMPARISON
  // ============================================
  Widget _buildPeerComparison(CapaianDashboard d) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 15),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.04), blurRadius: 12, offset: const Offset(0, 4))],
        ),
        padding: const EdgeInsets.all(12),
        child: Column(
          children: [
            // Legend
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                _legendDot(_kPrimary, d.santri.namaLengkap.split(' ').first),
                const SizedBox(width: 12),
                _legendDot(Colors.grey[400]!, 'Rata-rata Kelas'),
              ],
            ),
            const SizedBox(height: 12),
            ...d.peerComparison.map((p) => _peerBar(p, d.santri.namaLengkap.split(' ').first)),
          ],
        ),
      ),
    );
  }

  Widget _legendDot(Color color, String label) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(width: 8, height: 8, decoration: BoxDecoration(color: color, shape: BoxShape.circle)),
        const SizedBox(width: 5),
        Text(label, style: TextStyle(fontSize: 9, color: Colors.grey[600])),
      ],
    );
  }

  Widget _peerBar(PeerComparisonItem p, String santriName) {
    final catColor = _parseColor(p.color);
    final maxVal = max(p.santriProgress, p.kelasAvg).clamp(1.0, 100.0);
    final isAhead = p.santriProgress >= p.kelasAvg;

    return Padding(
      padding: const EdgeInsets.only(bottom: 11),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(_getIconData(p.icon), size: 12, color: catColor),
              const SizedBox(width: 5),
              Text(p.kategori, style: TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: Colors.grey[800])),
              const Spacer(),
              if (isAhead && p.santriProgress > 0)
                Text('ðŸŸ¢', style: TextStyle(fontSize: 8))
              else if (!isAhead && p.kelasAvg > 0)
                Text('ðŸ”´', style: TextStyle(fontSize: 8)),
            ],
          ),
          const SizedBox(height: 5),
          // Santri bar
          _dualBar(santriName, p.santriProgress, maxVal, _kPrimary),
          const SizedBox(height: 2),
          // Kelas avg bar
          _dualBar('Kelas', p.kelasAvg, maxVal, Colors.grey[400]!),
        ],
      ),
    );
  }

  Widget _dualBar(String label, double value, double maxVal, Color color) {
    return Row(
      children: [
        SizedBox(
          width: 31,
          child: Text(label, style: TextStyle(fontSize: 8, color: Colors.grey[500]), overflow: TextOverflow.ellipsis),
        ),
        Expanded(
          child: Stack(
            children: [
              Container(
                height: 11,
                decoration: BoxDecoration(color: Colors.grey[100], borderRadius: BorderRadius.circular(5)),
              ),
              FractionallySizedBox(
                widthFactor: maxVal > 0 ? (value / 100).clamp(0.0, 1.0) : 0,
                child: Container(
                  height: 11,
                  decoration: BoxDecoration(
                    color: color,
                    borderRadius: BorderRadius.circular(5),
                  ),
                ),
              ),
            ],
          ),
        ),
        const SizedBox(width: 5),
        SizedBox(
          width: 29,
          child: Text('${value.toStringAsFixed(1)}%', style: TextStyle(fontSize: 8, fontWeight: FontWeight.bold, color: color), textAlign: TextAlign.right),
        ),
      ],
    );
  }

  // ============================================
  // 7. HISTORICAL GRAPH (Custom painted line chart)
  // ============================================
  Widget _buildHistoricalGraph(List<SemesterHistoryItem> history) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 15),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.04), blurRadius: 12, offset: const Offset(0, 4))],
        ),
        padding: const EdgeInsets.fromLTRB(9, 12, 12, 12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            SizedBox(
              height: MediaQuery.of(context).size.height * 0.22,
              child: _LineChart(
                data: history.map((h) => h.rataRataProgress).toList(),
                labels: history.map((h) => _shortSemLabel(h.namaSemester)).toList(),
                onTap: (i) {
                  final item = history[i];
                  _showSemesterDetail(item);
                },
              ),
            ),
            const SizedBox(height: 7),
            Center(
              child: Text(
                'Tap titik untuk detail semester',
                style: TextStyle(fontSize: 8, color: Colors.grey[400], fontStyle: FontStyle.italic),
              ),
            ),
          ],
        ),
      ),
    );
  }

  String _shortSemLabel(String name) {
    // "Semester Ganjil 2024/2025" -> "Gnjl 24/25"
    return name
      .replaceAll('Semester ', '')
      .replaceAll('Ganjil', 'Gnjl')
      .replaceAll('Genap', 'Gnp')
      .replaceAllMapped(RegExp(r'20(\d{2})/20(\d{2})'), (m) => '${m[1]}/${m[2]}')
      .replaceAllMapped(RegExp(r'20(\d{2})'), (m) => "'${m[1]}");
  }

  void _showSemesterDetail(SemesterHistoryItem item) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (ctx) => SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(19),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  const Icon(Icons.calendar_month_rounded, color: _kPrimary),
                  const SizedBox(width: 7),
                  Text(item.namaSemester, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.bold)),
                  if (item.isCurrent) ...[
                    const SizedBox(width: 7),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 2),
                      decoration: BoxDecoration(color: _kPrimary, borderRadius: BorderRadius.circular(8)),
                      child: const Text('Saat Ini', style: TextStyle(fontSize: 8, color: Colors.white, fontWeight: FontWeight.bold)),
                    ),
                  ],
                ],
              ),
              const SizedBox(height: 15),
              Row(
                children: [
                  _detailStat('Rata-rata', '${item.rataRataProgress.toStringAsFixed(1)}%', _kPrimary),
                  const SizedBox(width: 12),
                  _detailStat('Total Materi', '${item.totalMateri}', _kBlue),
                  const SizedBox(width: 12),
                  _detailStat('Selesai', '${item.materiSelesai}', _kPrimary),
                ],
              ),
              const SizedBox(height: 12),
            ],
          ),
        ),
      ),
    );
  }

  Widget _detailStat(String label, String value, Color color) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.all(9),
        decoration: BoxDecoration(color: color.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(9)),
        child: Column(
          children: [
            Text(value, style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: color)),
            const SizedBox(height: 2),
            Text(label, style: TextStyle(fontSize: 8, color: color), textAlign: TextAlign.center),
          ],
        ),
      ),
    );
  }

  // ============================================
  // 8. PARENT SUMMARY VIEW (for Wali)
  // ============================================
  Widget _buildParentSummary(CapaianDashboard d) {
    final rapor = d.raporSummary;
    final isNaik = rapor.trend == 'naik';
    final isTurun = rapor.trend == 'turun';
    final trendColor = isNaik ? _kPrimary : (isTurun ? _kRed : _kOrange);
    final trendIcon = isNaik ? Icons.trending_up_rounded : (isTurun ? Icons.trending_down_rounded : Icons.trending_flat_rounded);

    Color predikatColor;
    switch (rapor.predikat) {
      case 'Baik Sekali': predikatColor = _kPrimary; break;
      case 'Baik': predikatColor = _kBlue; break;
      case 'Cukup': predikatColor = _kOrange; break;
      default: predikatColor = _kRed;
    }

    return Padding(
      padding: const EdgeInsets.fromLTRB(15, 12, 15, 0),
      child: Container(
        decoration: BoxDecoration(
          gradient: LinearGradient(
            colors: [predikatColor.withValues(alpha: 0.08), predikatColor.withValues(alpha: 0.02)],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
          borderRadius: BorderRadius.circular(15),
          border: Border.all(color: predikatColor.withValues(alpha: 0.2)),
        ),
        padding: const EdgeInsets.all(15),
        child: Column(
          children: [
            const Text('Progress Semester Ini', style: TextStyle(fontSize: 11, color: Colors.grey)),
            const SizedBox(height: 7),
            Text(
              '${rapor.totalProgress.toStringAsFixed(1)}%',
              style: TextStyle(fontSize: 36, fontWeight: FontWeight.bold, color: predikatColor),
            ),
            const SizedBox(height: 2),
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(trendIcon, size: 15, color: trendColor),
                const SizedBox(width: 2),
                Text(
                  rapor.perubahan != 0
                      ? '${rapor.perubahan > 0 ? '+' : ''}${rapor.perubahan.toStringAsFixed(1)}% dari semester lalu'
                      : 'Semester pertama',
                  style: TextStyle(fontSize: 11, color: trendColor, fontWeight: FontWeight.w500),
                ),
              ],
            ),
            const SizedBox(height: 9),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 7),
              decoration: BoxDecoration(color: predikatColor.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(15)),
              child: Text(
                'Status: ${rapor.predikat}',
                style: TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: predikatColor),
              ),
            ),
          ],
        ),
      ),
    );
  }

  // ============================================
  // HELPERS
  // ============================================
  Color _parseColor(String hex) {
    try {
      return Color(int.parse(hex.replaceFirst('#', '0xFF')));
    } catch (_) {
      return Colors.grey;
    }
  }

  IconData _getIconData(String iconName) {
    switch (iconName) {
      case 'book_quran': return Icons.menu_book;
      case 'scroll': return Icons.article;
      case 'book': return Icons.book;
      default: return Icons.book;
    }
  }
}

// ============================================
// SEMESTER PROGRESS CARD WIDGET
// ============================================
class _SemesterProgressCard extends StatelessWidget {
  final String semesterName;
  final double totalProgress;
  final List<KategoriProgress>? perKategori;
  final int? totalMateri;
  final int? materiSelesai;
  final bool isCurrent;

  const _SemesterProgressCard({
    required this.semesterName,
    required this.totalProgress,
    this.perKategori,
    this.totalMateri,
    this.materiSelesai,
    required this.isCurrent,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: isCurrent
              ? [_kPrimary, _kPrimaryDark]
              : [Colors.grey[600]!, Colors.grey[800]!],
        ),
        borderRadius: BorderRadius.circular(15),
        boxShadow: [
          BoxShadow(
            color: (isCurrent ? _kPrimary : Colors.grey).withValues(alpha: 0.3),
            blurRadius: 12, offset: const Offset(0, 6),
          ),
        ],
      ),
      padding: const EdgeInsets.all(15),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Top row
          Row(
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      semesterName,
                      style: const TextStyle(fontSize: 11, color: Colors.white70, fontWeight: FontWeight.w500),
                      maxLines: 1, overflow: TextOverflow.ellipsis,
                    ),
                    if (isCurrent)
                      const Text('Semester Aktif', style: TextStyle(fontSize: 8, color: Colors.white38)),
                  ],
                ),
              ),
              // Big percentage
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 11, vertical: 7),
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: 0.15),
                  borderRadius: BorderRadius.circular(11),
                ),
                child: Text(
                  '${totalProgress.toStringAsFixed(1)}%',
                  style: const TextStyle(fontSize: 17, fontWeight: FontWeight.bold, color: Colors.white),
                ),
              ),
            ],
          ),
          const SizedBox(height: 9),

          // Progress bar
          ClipRRect(
            borderRadius: BorderRadius.circular(5),
            child: LinearProgressIndicator(
              value: (totalProgress / 100).clamp(0.0, 1.0),
              backgroundColor: Colors.white.withValues(alpha: 0.15),
              valueColor: const AlwaysStoppedAnimation(Colors.white),
              minHeight: 8,
            ),
          ),
          const SizedBox(height: 11),

          // Per kategori or simple stats
          if (perKategori != null && perKategori!.isNotEmpty)
            Row(
              children: perKategori!.map((k) {
                final c = _parseHex(k.color);
                return Expanded(
                  child: Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 2),
                    child: Column(
                      children: [
                        Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Container(width: 5, height: 5, decoration: BoxDecoration(color: c, shape: BoxShape.circle)),
                            const SizedBox(width: 2),
                            Flexible(
                              child: Text(
                                k.kategori.replaceAll('Materi ', ''),
                                style: const TextStyle(fontSize: 8, color: Colors.white60),
                                maxLines: 1, overflow: TextOverflow.ellipsis,
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 2),
                        Text(
                          '${k.rataRataProgress.toStringAsFixed(0)}%',
                          style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.white),
                        ),
                      ],
                    ),
                  ),
                );
              }).toList(),
            )
          else
            Row(
              children: [
                _miniStat('Materi', '${totalMateri ?? 0}'),
                const SizedBox(width: 12),
                _miniStat('Selesai', '${materiSelesai ?? 0}'),
              ],
            ),
        ],
      ),
    );
  }

  Widget _miniStat(String label, String value) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontSize: 8, color: Colors.white54)),
        Text(value, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.white)),
      ],
    );
  }

  Color _parseHex(String hex) {
    try {
      return Color(int.parse(hex.replaceFirst('#', '0xFF')));
    } catch (_) {
      return Colors.white;
    }
  }
}

// ============================================
// SEMESTER PICKER BOTTOM SHEET
// ============================================
class _SemesterPickerSheet extends StatelessWidget {
  final List<SemesterOption> semesters;
  final String? selected;
  final ValueChanged<String> onSelect;

  const _SemesterPickerSheet({required this.semesters, this.selected, required this.onSelect});

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 12),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(width: 31, height: 2, decoration: BoxDecoration(color: Colors.grey[300], borderRadius: BorderRadius.circular(2))),
            const SizedBox(height: 12),
            const Text('Pilih Semester', style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold)),
            const Divider(height: 19),
            Flexible(
              child: ListView.builder(
                shrinkWrap: true,
                itemCount: semesters.length,
                itemBuilder: (ctx, i) {
                  final s = semesters[i];
                  final isSel = selected == s.idSemester || (selected == null && s.isAktif);
                  return ListTile(
                    leading: Icon(
                      isSel ? Icons.check_circle_rounded : Icons.circle_outlined,
                      color: isSel ? _kPrimary : Colors.grey,
                    ),
                    title: Text(s.namaSemester, style: TextStyle(fontWeight: isSel ? FontWeight.bold : FontWeight.normal)),
                    trailing: s.isAktif
                        ? Container(
                            padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 2),
                            decoration: BoxDecoration(color: _kPrimary, borderRadius: BorderRadius.circular(9)),
                            child: const Text('Aktif', style: TextStyle(color: Colors.white, fontSize: 8, fontWeight: FontWeight.bold)),
                          )
                        : null,
                    selected: isSel,
                    onTap: () => onSelect(s.idSemester),
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

// ============================================
// CUSTOM LINE CHART (lightweight, no external dependency)
// ============================================
class _LineChart extends StatefulWidget {
  final List<double> data;
  final List<String> labels;
  final ValueChanged<int>? onTap;

  const _LineChart({required this.data, required this.labels, this.onTap});

  @override
  State<_LineChart> createState() => _LineChartState();
}

class _LineChartState extends State<_LineChart> {
  int? _hoveredIndex;

  @override
  Widget build(BuildContext context) {
    return LayoutBuilder(
      builder: (ctx, constraints) {
        return GestureDetector(
          onTapDown: (details) {
            final idx = _hitTest(details.localPosition, constraints.maxWidth, constraints.maxHeight);
            if (idx != null && widget.onTap != null) {
              setState(() => _hoveredIndex = idx);
              widget.onTap!(idx);
            }
          },
          child: CustomPaint(
            size: Size(constraints.maxWidth, constraints.maxHeight),
            painter: _LineChartPainter(
              data: widget.data,
              labels: widget.labels,
              hoveredIndex: _hoveredIndex,
            ),
          ),
        );
      },
    );
  }

  int? _hitTest(Offset pos, double w, double h) {
    if (widget.data.isEmpty) return null;
    final leftPad = 36.0;
    final rightPad = 12.0;
    final topPad = 20.0;
    final botPad = 30.0;
    final chartW = w - leftPad - rightPad;
    final chartH = h - topPad - botPad;
    final stepX = widget.data.length > 1 ? chartW / (widget.data.length - 1) : chartW;

    for (int i = 0; i < widget.data.length; i++) {
      final x = leftPad + i * stepX;
      final y = topPad + chartH * (1 - widget.data[i] / 100);
      final dist = (Offset(x, y) - pos).distance;
      if (dist < 24) return i;
    }
    return null;
  }
}

class _LineChartPainter extends CustomPainter {
  final List<double> data;
  final List<String> labels;
  final int? hoveredIndex;

  _LineChartPainter({required this.data, required this.labels, this.hoveredIndex});

  @override
  void paint(Canvas canvas, Size size) {
    if (data.isEmpty) return;

    const leftPad = 36.0;
    const rightPad = 12.0;
    const topPad = 20.0;
    const botPad = 30.0;
    final chartW = size.width - leftPad - rightPad;
    final chartH = size.height - topPad - botPad;
    final stepX = data.length > 1 ? chartW / (data.length - 1) : chartW;

    // Grid lines
    final gridPaint = Paint()..color = Colors.grey.withValues(alpha: 0.15)..strokeWidth = 1;
    final textStyle = TextStyle(fontSize: 8, color: Colors.grey[500]);

    for (int pct = 0; pct <= 100; pct += 25) {
      final y = topPad + chartH * (1 - pct / 100);
      canvas.drawLine(Offset(leftPad, y), Offset(size.width - rightPad, y), gridPaint);
      final tp = TextPainter(text: TextSpan(text: '$pct', style: textStyle), textDirection: TextDirection.ltr);
      tp.layout();
      tp.paint(canvas, Offset(leftPad - tp.width - 6, y - tp.height / 2));
    }

    // Fill gradient
    final gradPath = Path();
    for (int i = 0; i < data.length; i++) {
      final x = leftPad + i * stepX;
      final y = topPad + chartH * (1 - data[i] / 100);
      if (i == 0) {
        gradPath.moveTo(x, y);
      } else {
        gradPath.lineTo(x, y);
      }
    }
    gradPath.lineTo(leftPad + (data.length - 1) * stepX, topPad + chartH);
    gradPath.lineTo(leftPad, topPad + chartH);
    gradPath.close();

    final gradPaint = Paint()
      ..shader = LinearGradient(
        begin: Alignment.topCenter,
        end: Alignment.bottomCenter,
        colors: [_kPrimary.withValues(alpha: 0.3), _kPrimary.withValues(alpha: 0.0)],
      ).createShader(Rect.fromLTWH(leftPad, topPad, chartW, chartH));
    canvas.drawPath(gradPath, gradPaint);

    // Line
    final linePaint = Paint()
      ..color = _kPrimary
      ..strokeWidth = 2.5
      ..style = PaintingStyle.stroke
      ..strokeCap = StrokeCap.round;

    final linePath = Path();
    for (int i = 0; i < data.length; i++) {
      final x = leftPad + i * stepX;
      final y = topPad + chartH * (1 - data[i] / 100);
      if (i == 0) {
        linePath.moveTo(x, y);
      } else {
        linePath.lineTo(x, y);
      }
    }
    canvas.drawPath(linePath, linePaint);

    // Dots + labels
    for (int i = 0; i < data.length; i++) {
      final x = leftPad + i * stepX;
      final y = topPad + chartH * (1 - data[i] / 100);
      final isHovered = hoveredIndex == i;

      // Dot
      canvas.drawCircle(Offset(x, y), isHovered ? 7 : 5, Paint()..color = Colors.white);
      canvas.drawCircle(Offset(x, y), isHovered ? 6 : 4, Paint()..color = _kPrimary);

      // Value label on hover
      if (isHovered) {
        final tp = TextPainter(
          text: TextSpan(text: '${data[i].toStringAsFixed(1)}%', style: const TextStyle(fontSize: 8, fontWeight: FontWeight.bold, color: _kPrimary)),
          textDirection: TextDirection.ltr,
        );
        tp.layout();
        tp.paint(canvas, Offset(x - tp.width / 2, y - tp.height - 10));
      }

      // Bottom label
      if (i < labels.length) {
        final tp = TextPainter(
          text: TextSpan(text: labels[i], style: TextStyle(fontSize: 7, color: Colors.grey[500])),
          textDirection: TextDirection.ltr,
        );
        tp.layout(maxWidth: stepX + 10);
        tp.paint(canvas, Offset(x - tp.width / 2, topPad + chartH + 8));
      }
    }
  }

  @override
  bool shouldRepaint(covariant _LineChartPainter old) =>
      old.data != data || old.hoveredIndex != hoveredIndex;
}
