// lib/features/pelanggaran/pelanggaran_page.dart

import 'package:flutter/material.dart';
import 'kategori_pelanggaran_tab.dart';
import 'pembinaan_sanksi_tab.dart';
import 'riwayat_pelanggaran_tab.dart';

class PelanggaranPage extends StatefulWidget {
  const PelanggaranPage({super.key});

  @override
  State<PelanggaranPage> createState() => _PelanggaranPageState();
}

class _PelanggaranPageState extends State<PelanggaranPage>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[100],
      appBar: AppBar(
        title: const Text('Pelanggaran Santri'),
        backgroundColor: const Color(0xFF6FBA9D),
        foregroundColor: Colors.white,
        elevation: 0,
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: Colors.white,
          labelColor: Colors.white,
          unselectedLabelColor: Colors.white70,
          tabs: const [
            Tab(
              icon: Icon(Icons.list_alt, size: 15),
              text: 'Kategori',
            ),
            Tab(
              icon: Icon(Icons.gavel, size: 15),
              text: 'Pembinaan',
            ),
            Tab(
              icon: Icon(Icons.history, size: 15),
              text: 'Riwayat',
            ),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tabController,
        children: const [
          KategoriPelanggaranTab(),
          PembinaanSanksiTab(),
          RiwayatPelanggaranTab(),
        ],
      ),
    );
  }
}