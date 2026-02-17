// lib/features/kepulangan/presentation/pages/pengajuan_kepulangan_form_page.dart

import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../data/models/kepulangan_model.dart';
import '../../data/services/kepulangan_service.dart';
import '../../state/pengajuan_kepulangan_controller.dart';
import '../widgets/durasi_preview_widget.dart';
import '../widgets/kuota_warning_widget.dart';
import '../widgets/kuota_indicator.dart';

class PengajuanKepulanganFormPage extends StatefulWidget {
  const PengajuanKepulanganFormPage({super.key});

  @override
  State<PengajuanKepulanganFormPage> createState() =>
      _PengajuanKepulanganFormPageState();
}

class _PengajuanKepulanganFormPageState
    extends State<PengajuanKepulanganFormPage> {
  final KepulanganService _service = KepulanganService();
  final TextEditingController _alasanController = TextEditingController();

  KuotaInfo? _kuotaInfo;
  bool _isLoadingKuota = true;

  @override
  void initState() {
    super.initState();
    _loadKuotaInfo();
  }

  @override
  void dispose() {
    _alasanController.dispose();
    super.dispose();
  }

  Future<void> _loadKuotaInfo() async {
    setState(() => _isLoadingKuota = true);

    final result = await _service.getKuotaInfo();

    if (mounted && result['success'] == true) {
      setState(() {
        _kuotaInfo = result['kuota'];
        _isLoadingKuota = false;
      });
    } else {
      setState(() => _isLoadingKuota = false);
    }
  }

  Future<void> _handleSubmit(BuildContext context) async {
    final controller = context.read<PengajuanKepulanganController>();

    // Validasi
    if (!controller.canSubmit) {
      _showSnackBar('Mohon lengkapi semua field', isError: true);
      return;
    }

    // Jika over limit, tampilkan konfirmasi
    if (controller.isOverLimit) {
      final confirm = await _showOverLimitDialog(context, controller);
      if (confirm != true) return;
    }

    // Submit
    final result = await controller.submitPengajuan();

    if (!mounted) return;

    if (result['success'] == true) {
      _showSnackBar('✅ Pengajuan berhasil dikirim! Menunggu persetujuan admin.');
      
      // Reset form & kembali
      controller.reset();
      Navigator.pop(context, true); // Return true untuk refresh list
    } else {
      _showSnackBar(
        result['message'] ?? 'Gagal mengirim pengajuan',
        isError: true,
      );
    }
  }

  Future<bool?> _showOverLimitDialog(
    BuildContext context,
    PengajuanKepulanganController controller,
  ) {
    return showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Row(
          children: [
            Icon(Icons.warning_amber_rounded, color: Colors.red.shade700),
            const SizedBox(width: 8),
            const Expanded(child: Text('Konfirmasi Over Limit')),
          ],
        ),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.red.shade50,
                border: Border.all(color: Colors.red.shade200),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Text(
                controller.warningMessage,
                style: TextStyle(color: Colors.red.shade900, fontSize: 14),
              ),
            ),
            const SizedBox(height: 16),
            const Text(
              'Pengajuan tetap bisa diproses, tetapi Anda akan melebihi kuota maksimal.',
              style: TextStyle(fontSize: 14),
            ),
            const SizedBox(height: 8),
            Text(
              'Apakah Anda yakin ingin melanjutkan?',
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.bold,
                color: Colors.grey.shade700,
              ),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.orange,
              foregroundColor: Colors.white,
            ),
            child: const Text('Ya, Lanjutkan'),
          ),
        ],
      ),
    );
  }

  void _showSnackBar(String message, {bool isError = false}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: isError ? Colors.red : Colors.green,
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return ChangeNotifierProvider(
      create: (_) => PengajuanKepulanganController(),
      child: Scaffold(
        backgroundColor: Colors.grey[100],
        appBar: AppBar(
          title: const Text('Ajukan Izin Kepulangan'),
          backgroundColor: Colors.deepPurple,
          foregroundColor: Colors.white,
          elevation: 0,
        ),
        body: _isLoadingKuota
            ? const Center(child: CircularProgressIndicator())
            : _kuotaInfo == null
                ? _buildErrorState()
                : _buildForm(),
      ),
    );
  }

  Widget _buildErrorState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.error_outline, size: 64, color: Colors.red.shade300),
          const SizedBox(height: 16),
          const Text('Gagal memuat data kuota'),
          const SizedBox(height: 16),
          ElevatedButton.icon(
            onPressed: _loadKuotaInfo,
            icon: const Icon(Icons.refresh),
            label: const Text('Coba Lagi'),
          ),
        ],
      ),
    );
  }

  Widget _buildForm() {
    return Consumer<PengajuanKepulanganController>(
      builder: (context, controller, _) {
        return SingleChildScrollView(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Kuota Indicator
              KuotaIndicator(kuotaInfo: _kuotaInfo!, showDetail: false),

              const SizedBox(height: 24),

              // Form Card
              Card(
                elevation: 2,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Form Pengajuan',
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 16),

                      // Tanggal Pulang
                      _buildDateField(
                        label: 'Tanggal Pulang',
                        icon: Icons.flight_takeoff,
                        selectedDate: controller.tanggalPulang,
                        onSelect: (date) {
                          controller.setTanggalPulang(date);
                          // Validate kuota setelah pilih tanggal
                          controller.validateKuota(_kuotaInfo!);
                        },
                        firstDate: DateTime.now(),
                      ),

                      const SizedBox(height: 16),

                      // Tanggal Kembali
                      _buildDateField(
                        label: 'Tanggal Kembali',
                        icon: Icons.flight_land,
                        selectedDate: controller.tanggalKembali,
                        onSelect: (date) {
                          controller.setTanggalKembali(date);
                          // Validate kuota setelah pilih tanggal
                          controller.validateKuota(_kuotaInfo!);
                        },
                        firstDate: controller.tanggalPulang?.add(
                              const Duration(days: 1),
                            ) ??
                            DateTime.now(),
                      ),

                      const SizedBox(height: 16),

                      // Alasan
                      TextField(
                        controller: _alasanController,
                        maxLines: 4,
                        maxLength: 500,
                        decoration: InputDecoration(
                          labelText: 'Alasan Kepulangan *',
                          hintText: 'Jelaskan alasan kepulangan...',
                          prefixIcon: const Icon(Icons.subject),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(8),
                          ),
                        ),
                        onChanged: controller.setAlasan,
                      ),
                    ],
                  ),
                ),
              ),

              const SizedBox(height: 16),

              // Durasi Preview
              DurasiPreviewWidget(
                durasiIzin: controller.durasiIzin,
                totalSetelahIzin: controller.totalSetelahIzin,
                sisaKuotaSetelah: controller.sisaKuotaSetelah,
                kuotaMaksimal: _kuotaInfo!.kuotaMaksimal,
                isOverLimit: controller.isOverLimit,
              ),

              const SizedBox(height: 16),

              // Warning
              if (controller.warningMessage.isNotEmpty)
                KuotaWarningWidget(
                  message: controller.warningMessage,
                  isOverLimit: controller.isOverLimit,
                ),

              const SizedBox(height: 24),

              // Submit Button
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: controller.canSubmit && !controller.isSubmitting
                      ? () => _handleSubmit(context)
                      : null,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.deepPurple,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                  ),
                  child: controller.isSubmitting
                      ? const SizedBox(
                          height: 20,
                          width: 20,
                          child: CircularProgressIndicator(
                            color: Colors.white,
                            strokeWidth: 2,
                          ),
                        )
                      : const Text(
                          'Ajukan Izin Kepulangan',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                ),
              ),

              const SizedBox(height: 32),
            ],
          ),
        );
      },
    );
  }

  Widget _buildDateField({
    required String label,
    required IconData icon,
    required DateTime? selectedDate,
    required Function(DateTime) onSelect,
    required DateTime firstDate,
  }) {
    return InkWell(
      onTap: () async {
        final date = await showDatePicker(
          context: context,
          initialDate: selectedDate ?? firstDate,
          firstDate: firstDate,
          lastDate: DateTime.now().add(const Duration(days: 365)),
        );

        if (date != null) {
          onSelect(date);
        }
      },
      child: InputDecorator(
        decoration: InputDecoration(
          labelText: label,
          prefixIcon: Icon(icon),
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
        ),
        child: Text(
          selectedDate != null
              ? '${selectedDate.day}/${selectedDate.month}/${selectedDate.year}'
              : 'Pilih tanggal',
          style: TextStyle(
            fontSize: 16,
            color: selectedDate != null ? Colors.black : Colors.grey,
          ),
        ),
      ),
    );
  }
}