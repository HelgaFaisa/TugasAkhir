// lib/features/absensi/pages/absensi_page.dart

import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../../core/api/api_service.dart';
import '../models/absensi_summary_model.dart';
import '../models/absensi_kegiatan_model.dart';
import '../widgets/summary_card.dart';
import '../widgets/absensi_timeline_item.dart';

class AbsensiPage extends StatefulWidget {
  const AbsensiPage({super.key});

  @override
  State<AbsensiPage> createState() => _AbsensiPageState();
}

class _AbsensiPageState extends State<AbsensiPage> {
  final _api = ApiService();
  
  bool _isLoading = true;
  String? _errorMessage;
  
  // Data
  DateTime _selectedDate = DateTime.now();
  String _tanggalLabel = '';
  AbsensiSummary? _summaryToday;
  List<AbsensiKegiatan> _timeline = [];
  
  Map<String, dynamic>? _weekData;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      // Load hari ini + minggu ini secara parallel
      final results = await Future.wait([
        _api.getAbsensiToday(
          tanggal: DateFormat('yyyy-MM-dd').format(_selectedDate),
        ),
        _api.getAbsensiWeek(),
      ]);

      final todayResult = results[0];
      final weekResult = results[1];

      if (mounted) {
        if (todayResult['success'] == true) {
          final data = todayResult['data'];
          setState(() {
            _tanggalLabel = data['tanggal'] ?? '';
            _summaryToday = AbsensiSummary.fromJson(data['summary'] ?? {});
            _timeline = (data['timeline'] as List?)
                    ?.map((json) => AbsensiKegiatan.fromJson(json))
                    .toList() ??
                [];
          });
        } else {
          setState(() {
            _errorMessage = todayResult['message'] ?? 'Gagal memuat data';
          });
        }

        if (weekResult['success'] == true) {
          setState(() {
            _weekData = weekResult['data'];
          });
        }

        setState(() => _isLoading = false);
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _isLoading = false;
          _errorMessage = 'Error: ${e.toString()}';
        });
      }
    }
  }

  Future<void> _changeDate(int days) async {
    setState(() {
      _selectedDate = _selectedDate.add(Duration(days: days));
    });
    await _loadData();
  }

  Future<void> _pickDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _selectedDate,
      firstDate: DateTime.now().subtract(const Duration(days: 365)),
      lastDate: DateTime.now().add(const Duration(days: 30)),
    );

    if (picked != null && mounted) {
      setState(() {
        _selectedDate = picked;
      });
      await _loadData();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[100],
      appBar: AppBar(
        title: const Text('Absensi Kegiatan'),
        backgroundColor: const Color(0xFF6FBA9D),
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _errorMessage != null
              ? _buildError()
              : RefreshIndicator(
                  onRefresh: _loadData,
                  child: SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Header dengan Summary Hari Ini
                        _buildHeaderSection(),

                        const SizedBox(height: 12),

                        // Summary Minggu Ini Card
                        if (_weekData != null) _buildWeekSummaryCard(),

                        const SizedBox(height: 12),

                        // Date Navigation
                        _buildDateNavigation(),

                        const SizedBox(height: 12),

                        // Timeline Absensi Hari Ini
                        _buildTimelineSection(),

                        const SizedBox(height: 19),
                      ],
                    ),
                  ),
                ),
    );
  }

  Widget _buildError() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(19),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(
              Icons.error_outline,
              size: 48,
              color: Colors.red,
            ),
            const SizedBox(height: 12),
            Text(
              _errorMessage ?? 'Terjadi kesalahan',
              textAlign: TextAlign.center,
              style: const TextStyle(fontSize: 12),
            ),
            const SizedBox(height: 19),
            ElevatedButton.icon(
              onPressed: _loadData,
              icon: const Icon(Icons.refresh),
              label: const Text('Coba Lagi'),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildHeaderSection() {
    if (_summaryToday == null) return const SizedBox();

    return Container(
      width: double.infinity,
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors: [
            Color(0xFF6FBA9D),
            Color(0xFF4D987B),
          ],
        ),
        borderRadius: BorderRadius.only(
          bottomLeft: Radius.circular(24),
          bottomRight: Radius.circular(24),
        ),
      ),
      child: SafeArea(
        top: false,
        child: Padding(
          padding: const EdgeInsets.fromLTRB(12, 0, 12, 15),
          child: Column(
            children: [
              // Label Tanggal
              Text(
                _tanggalLabel,
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 12,
                  fontWeight: FontWeight.w500,
                ),
              ),
              const SizedBox(height: 12),

              // Summary Cards Grid
              SummaryCard(summary: _summaryToday!),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildWeekSummaryCard() {
    final summary = AbsensiSummary.fromJson(_weekData!['summary'] ?? {});
    final periode = _weekData!['periode'] ?? '';

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 12),
      child: Card(
        elevation: 2,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(9),
        ),
        child: InkWell(
          onTap: () {
            Navigator.pushNamed(context, '/absensi/detail-minggu');
          },
          borderRadius: BorderRadius.circular(9),
          child: Padding(
            padding: const EdgeInsets.all(12),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Row(
                        children: [
                          Icon(
                            Icons.calendar_today,
                            size: 15,
                            color: Colors.blue[700],
                          ),
                          const SizedBox(width: 7),
                          Flexible(
                            child: Text(
                              'Ringkasan Minggu Ini',
                              style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.bold,
                                color: Colors.blue[700],
                              ),
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                        ],
                      ),
                    ),
                    const Icon(
                      Icons.arrow_forward_ios,
                      size: 12,
                      color: Colors.grey,
                    ),
                  ],
                ),
                const SizedBox(height: 2),
                Text(
                  periode,
                  style: TextStyle(
                    fontSize: 11,
                    color: Colors.grey[600],
                  ),
                ),
                const SizedBox(height: 12),

                // Summary Stats
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceAround,
                  children: [
                    _buildMiniStat(
                      'Total',
                      summary.total.toString(),
                      Colors.blue,
                    ),
                    _buildMiniStat(
                      'Hadir',
                      summary.hadir.toString(),
                      Colors.green,
                    ),
                    _buildMiniStat(
                      'Izin',
                      summary.izin.toString(),
                      Colors.orange,
                    ),
                    _buildMiniStat(
                      'Alpa',
                      summary.alpa.toString(),
                      Colors.red,
                    ),
                  ],
                ),
                const SizedBox(height: 9),

                // Progress Bar
                ClipRRect(
                  borderRadius: BorderRadius.circular(7),
                  child: LinearProgressIndicator(
                    value: summary.percentage / 100,
                    minHeight: 8,
                    backgroundColor: Colors.grey[200],
                    valueColor: AlwaysStoppedAnimation<Color>(
                      _getColorByPercentage(summary.percentage),
                    ),
                  ),
                ),
                const SizedBox(height: 5),
                Text(
                  'Kehadiran: ${summary.percentage.toStringAsFixed(1)}%',
                  style: TextStyle(
                    fontSize: 9,
                    fontWeight: FontWeight.w600,
                    color: _getColorByPercentage(summary.percentage),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildMiniStat(String label, String value, Color color) {
    return Flexible(
      child: Column(
        children: [
          FittedBox(
            fit: BoxFit.scaleDown,
            child: Text(
              value,
              style: TextStyle(
                fontSize: 15,
                fontWeight: FontWeight.bold,
                color: color,
                height: 1.0,
              ),
            ),
          ),
          const SizedBox(height: 2),
          FittedBox(
            fit: BoxFit.scaleDown,
            child: Text(
              label,
              style: TextStyle(
                fontSize: 9,
                color: Colors.grey[600],
                height: 1.0,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDateNavigation() {
    final isToday = DateFormat('yyyy-MM-dd').format(_selectedDate) ==
        DateFormat('yyyy-MM-dd').format(DateTime.now());

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 9),
      child: Row(
        children: [
          // Previous Day
          IconButton(
            onPressed: () => _changeDate(-1),
            icon: const Icon(Icons.chevron_left, size: 15),
            style: IconButton.styleFrom(
              backgroundColor: Colors.white,
              foregroundColor: const Color(0xFF6FBA9D),
              padding: const EdgeInsets.all(7),
              minimumSize: const Size(40, 40),
            ),
          ),
          const SizedBox(width: 7),

          // Date Picker Button
          Expanded(
            child: ElevatedButton.icon(
              onPressed: _pickDate,
              icon: const Icon(Icons.calendar_month, size: 12),
              label: Text(
                DateFormat('dd MMM yyyy').format(_selectedDate),
                style: const TextStyle(fontSize: 11),
              ),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.white,
                foregroundColor: Colors.black87,
                elevation: 1,
                padding: const EdgeInsets.symmetric(
                  horizontal: 9,
                  vertical: 8,
                ),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(7),
                ),
              ),
            ),
          ),
          const SizedBox(width: 7),

          // Next Day
          IconButton(
            onPressed: () => _changeDate(1),
            icon: const Icon(Icons.chevron_right, size: 15),
            style: IconButton.styleFrom(
              backgroundColor: Colors.white,
              foregroundColor: const Color(0xFF6FBA9D),
              padding: const EdgeInsets.all(7),
              minimumSize: const Size(40, 40),
            ),
          ),

          // Today Button (conditional)
          if (!isToday) ...[
            const SizedBox(width: 7),
            ElevatedButton(
              onPressed: () {
                setState(() {
                  _selectedDate = DateTime.now();
                });
                _loadData();
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF6FBA9D),
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(
                  horizontal: 9,
                  vertical: 8,
                ),
                minimumSize: const Size(65, 40),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(7),
                ),
              ),
              child: const Text('Hari Ini', style: TextStyle(fontSize: 9)),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildTimelineSection() {
    if (_timeline.isEmpty) {
      return Padding(
        padding: const EdgeInsets.all(19),
        child: Center(
          child: Column(
            children: [
              Icon(
                Icons.calendar_today_outlined,
                size: 48,
                color: Colors.grey[400],
              ),
              const SizedBox(height: 12),
              Text(
                'Tidak ada kegiatan dijadwalkan',
                style: TextStyle(
                  fontSize: 12,
                  color: Colors.grey[600],
                ),
              ),
            ],
          ),
        ),
      );
    }

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 12),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Daftar Kegiatan',
                style: TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.bold,
                ),
              ),
              TextButton.icon(
                onPressed: () {
                  Navigator.pushNamed(context, '/absensi/riwayat-bulan');
                },
                icon: const Icon(Icons.history, size: 15),
                label: const Text('Riwayat'),
              ),
            ],
          ),
          const SizedBox(height: 9),

          // Timeline List
          ListView.builder(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: _timeline.length,
            itemBuilder: (context, index) {
              return AbsensiTimelineItem(
                absensi: _timeline[index],
                isLast: index == _timeline.length - 1,
              );
            },
          ),
        ],
      ),
    );
  }

  Color _getColorByPercentage(double percentage) {
    if (percentage >= 85) return Colors.green;
    if (percentage >= 70) return Colors.orange;
    return Colors.red;
  }
}