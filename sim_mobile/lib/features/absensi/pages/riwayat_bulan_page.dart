// lib/features/absensi/pages/riwayat_bulan_page.dart

import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../../core/api/api_service.dart';
import '../models/absensi_summary_model.dart';

class RiwayatBulanPage extends StatefulWidget {
  const RiwayatBulanPage({super.key});

  @override
  State<RiwayatBulanPage> createState() => _RiwayatBulanPageState();
}

class _RiwayatBulanPageState extends State<RiwayatBulanPage> {
  final _api = ApiService();

  bool _isLoading = true;
  String? _errorMessage;

  String _selectedBulan = DateFormat('yyyy-MM').format(DateTime.now());
  String _periode = '';
  AbsensiSummary? _summary;
  List<Map<String, dynamic>> _heatmap = [];
  List<Map<String, dynamic>> _riwayat = [];

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
      final result = await _api.getAbsensiMonth(bulan: _selectedBulan);

      if (mounted) {
        if (result['success'] == true) {
          final data = result['data'];
          setState(() {
            _periode = data['periode'] ?? '';
            _summary = AbsensiSummary.fromJson(data['summary'] ?? {});
            _heatmap = List<Map<String, dynamic>>.from(data['heatmap'] ?? []);
            _riwayat = List<Map<String, dynamic>>.from(data['riwayat'] ?? []);
            _isLoading = false;
          });
        } else {
          setState(() {
            _errorMessage = result['message'] ?? 'Gagal memuat data';
            _isLoading = false;
          });
        }
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

  Future<void> _pickMonth() async {
    final initialDate = DateTime.parse('$_selectedBulan-01');

    final picked = await showDatePicker(
      context: context,
      initialDate: initialDate,
      firstDate: DateTime.now().subtract(const Duration(days: 365)),
      lastDate: DateTime.now(),
      initialDatePickerMode: DatePickerMode.year,
    );

    if (picked != null && mounted) {
      setState(() {
        _selectedBulan = DateFormat('yyyy-MM').format(picked);
      });
      await _loadData();
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[100],
      appBar: AppBar(
        title: const Text('Riwayat Bulan'),
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
                    padding: const EdgeInsets.all(12),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Month Picker Button
                        _buildMonthPicker(),
                        const SizedBox(height: 12),

                        // Summary Card
                        _buildSummaryCard(),
                        const SizedBox(height: 15),

                        // Heatmap Calendar
                        _buildHeatmapCalendar(),
                        const SizedBox(height: 15),

                        // Riwayat List
                        _buildRiwayatList(),
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
            const Icon(Icons.error_outline, size: 48, color: Colors.red),
            const SizedBox(height: 12),
            Text(
              _errorMessage ?? 'Terjadi kesalahan',
              textAlign: TextAlign.center,
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

  Widget _buildMonthPicker() {
    return ElevatedButton.icon(
      onPressed: _pickMonth,
      icon: const Icon(Icons.calendar_month),
      label: Text(_periode),
      style: ElevatedButton.styleFrom(
        backgroundColor: Colors.white,
        foregroundColor: Colors.black87,
        padding: const EdgeInsets.symmetric(horizontal: 15, vertical: 11),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(9),
        ),
      ),
    );
  }

  Widget _buildSummaryCard() {
    if (_summary == null) return const SizedBox();

    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(9)),
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Column(
          children: [
            Text(
              'Ringkasan $_periode',
              style: const TextStyle(
                fontSize: 11,
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 12),

            Row(
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: [
                Flexible(child: _buildSummaryItem('Total', _summary!.total.toString())),
                Flexible(child: _buildSummaryItem('Hadir', _summary!.hadir.toString())),
                Flexible(child: _buildSummaryItem('Izin', _summary!.izin.toString())),
                Flexible(child: _buildSummaryItem('Alpa', _summary!.alpa.toString())),
              ],
            ),
            const SizedBox(height: 12),

            ClipRRect(
              borderRadius: BorderRadius.circular(7),
              child: LinearProgressIndicator(
                value: _summary!.percentage / 100,
                minHeight: 12,
                backgroundColor: Colors.grey[200],
                valueColor: AlwaysStoppedAnimation<Color>(
                  _getColorByPercentage(_summary!.percentage),
                ),
              ),
            ),
            const SizedBox(height: 7),
            Text(
              'Kehadiran: ${_summary!.percentage.toStringAsFixed(1)}%',
              style: TextStyle(
                fontSize: 11,
                fontWeight: FontWeight.bold,
                color: _getColorByPercentage(_summary!.percentage),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSummaryItem(String label, String value) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        FittedBox(
          fit: BoxFit.scaleDown,
          child: Text(
            value,
            style: const TextStyle(
              fontSize: 17,
              fontWeight: FontWeight.bold,
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
              fontSize: 8,
              color: Colors.grey[600],
              height: 1.0,
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildHeatmapCalendar() {
    if (_heatmap.isEmpty) return const SizedBox();

    // Group by week
    List<List<Map<String, dynamic>>> weeks = [];
    List<Map<String, dynamic>> currentWeek = [];

    // Pad start of month
    final firstDay = DateTime.parse(_heatmap.first['date']);
    final startPadding = (firstDay.weekday % 7);
    for (int i = 0; i < startPadding; i++) {
      currentWeek.add({});
    }

    for (var day in _heatmap) {
      currentWeek.add(day);
      if (currentWeek.length == 7) {
        weeks.add(List.from(currentWeek));
        currentWeek.clear();
      }
    }

    // Pad end
    if (currentWeek.isNotEmpty) {
      while (currentWeek.length < 7) {
        currentWeek.add({});
      }
      weeks.add(currentWeek);
    }

    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(9)),
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Icon(Icons.calendar_view_month,
                    color: Colors.indigo[700], size: 15),
                const SizedBox(width: 7),
                Text(
                  'Kalender Kehadiran',
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                    color: Colors.indigo[700],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),

            // Day Labels
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']
                  .map((day) => Expanded(
                        child: Text(
                          day,
                          textAlign: TextAlign.center,
                          style: TextStyle(
                            fontSize: 8,
                            fontWeight: FontWeight.w600,
                            color: Colors.grey[600],
                          ),
                        ),
                      ))
                  .toList(),
            ),
            const SizedBox(height: 7),

            // Calendar Grid
            LayoutBuilder(
              builder: (context, constraints) {
                final cellSize = (constraints.maxWidth - 24) / 7; // 24 for 6px gaps
                return Column(
                  children: weeks.map((week) {
                    return Padding(
                      padding: const EdgeInsets.only(bottom: 2),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceAround,
                        children: week.map((day) {
                          if (day.isEmpty) {
                            return SizedBox(
                              width: cellSize,
                              height: cellSize,
                            );
                          }

                          final level = _toInt(day['level']);
                          final isToday = day['is_today'] == true;

                          return Container(
                            width: cellSize,
                            height: cellSize,
                            decoration: BoxDecoration(
                              color: _getHeatmapColor(level),
                              borderRadius: BorderRadius.circular(5),
                              border: isToday
                                  ? Border.all(
                                      color: const Color(0xFF6FBA9D),
                                      width: 2,
                                    )
                                  : null,
                            ),
                            child: Center(
                              child: FittedBox(
                                fit: BoxFit.scaleDown,
                                child: Text(
                                  day['day'].toString(),
                                  style: TextStyle(
                                    fontSize: 9,
                                    fontWeight: isToday
                                        ? FontWeight.bold
                                        : FontWeight.normal,
                                    color: level > 0
                                        ? Colors.white
                                        : Colors.black87,
                                  ),
                                ),
                              ),
                            ),
                          );
                        }).toList(),
                      ),
                    );
                  }).toList(),
                );
              },
            ),
            const SizedBox(height: 9),

            // Legend
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text('Rendah',
                    style:
                        TextStyle(fontSize: 7, color: Colors.grey[600])),
                const SizedBox(width: 2),
                ...List.generate(5, (index) {
                  return Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 1.5),
                    child: Container(
                      width: 11,
                      height: 11,
                      decoration: BoxDecoration(
                        color: _getHeatmapColor(index),
                        borderRadius: BorderRadius.circular(2),
                      ),
                    ),
                  );
                }),
                const SizedBox(width: 2),
                Text('Tinggi',
                    style:
                        TextStyle(fontSize: 7, color: Colors.grey[600])),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildRiwayatList() {
    if (_riwayat.isEmpty) {
      return Card(
        child: Padding(
          padding: const EdgeInsets.all(31),
          child: Center(
            child: Text(
              'Belum ada riwayat absensi',
              style: TextStyle(color: Colors.grey[600]),
            ),
          ),
        ),
      );
    }

    return Card(
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(9)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.all(12),
            child: Row(
              children: [
                Icon(Icons.history, color: Colors.teal[700], size: 15),
                const SizedBox(width: 7),
                Text(
                  'Riwayat Harian',
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                    color: Colors.teal[700],
                  ),
                ),
              ],
            ),
          ),
          const Divider(height: 1),
          ListView.separated(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: _riwayat.length,
            separatorBuilder: (context, index) => const Divider(height: 1),
            itemBuilder: (context, index) {
              final item = _riwayat[index];
              final percentage = _toDouble(item['percentage']);
              final hadir = _toInt(item['hadir']);
              final total = _toInt(item['total']);

              return ExpansionTile(
                title: Text(
                  item['tanggal'] ?? '',
                  style: const TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.w600,
                  ),
                ),
                subtitle: Text(
                  '$hadir/$total kegiatan (${percentage.toStringAsFixed(1)}%)',
                  style: TextStyle(fontSize: 9, color: Colors.grey[600]),
                ),
                trailing: Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                  decoration: BoxDecoration(
                    color: _getColorByPercentage(percentage).withOpacity(0.15),
                    borderRadius: BorderRadius.circular(9),
                  ),
                  child: Text(
                    '${percentage.toStringAsFixed(0)}%',
                    style: TextStyle(
                      fontSize: 9,
                      fontWeight: FontWeight.bold,
                      color: _getColorByPercentage(percentage),
                    ),
                  ),
                ),
                children: [
                  Padding(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 12, vertical: 7),
                    child: Column(
                      children: (item['items'] as List?)?.map<Widget>((kegiatan) {
                            return Padding(
                              padding: const EdgeInsets.symmetric(vertical: 2),
                              child: Row(
                                children: [
                                  Icon(
                                    _getStatusIcon(kegiatan['status']),
                                    size: 12,
                                    color: _getStatusColor(kegiatan['status']),
                                  ),
                                  const SizedBox(width: 7),
                                  Expanded(
                                    child: Text(
                                      kegiatan['kegiatan'] ?? '',
                                      style: const TextStyle(fontSize: 9),
                                    ),
                                  ),
                                  if (kegiatan['waktu_absen'] != null)
                                    Text(
                                      kegiatan['waktu_absen'],
                                      style: TextStyle(
                                        fontSize: 8,
                                        color: Colors.grey[600],
                                      ),
                                    ),
                                ],
                              ),
                            );
                          }).toList() ??
                          [],
                    ),
                  ),
                ],
              );
            },
          ),
        ],
      ),
    );
  }

  static int _toInt(dynamic value) {
    if (value == null) return 0;
    if (value is int) return value;
    if (value is double) return value.toInt();
    if (value is String) return int.tryParse(value) ?? 0;
    return 0;
  }

  static double _toDouble(dynamic value) {
    if (value == null) return 0.0;
    if (value is double) return value;
    if (value is int) return value.toDouble();
    if (value is String) return double.tryParse(value) ?? 0.0;
    return 0.0;
  }

  Color _getHeatmapColor(int level) {
    switch (level) {
      case 4:
        return const Color(0xFF10b981); // Dark green
      case 3:
        return const Color(0xFF34d399); // Green
      case 2:
        return const Color(0xFFfbbf24); // Yellow
      case 1:
        return const Color(0xFFef4444); // Red
      default:
        return const Color(0xFFe5e7eb); // Gray
    }
  }

  Color _getColorByPercentage(double percentage) {
    if (percentage >= 85) return Colors.green;
    if (percentage >= 70) return Colors.orange;
    return Colors.red;
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

  IconData _getStatusIcon(String status) {
    switch (status) {
      case 'Hadir':
        return Icons.check_circle;
      case 'Izin':
        return Icons.info;
      case 'Sakit':
        return Icons.local_hospital;
      case 'Alpa':
        return Icons.cancel;
      default:
        return Icons.help;
    }
  }
}