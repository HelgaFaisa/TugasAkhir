// lib/main.dart - FIXED: Frame Android Tetap Muncul

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'core/widgets/android_frame_wrapper.dart';
import 'features/splash/splash_screen.dart';
import 'features/auth/login_page.dart';
import 'features/dashboard/dashboard_page.dart';
import 'features/profil/profil_page.dart';
import 'features/uang_saku/uang_saku_page.dart';
import 'features/berita/berita_page.dart';
import 'features/kesehatan/kesehatan_page.dart';
import 'features/spp/spp_page.dart';
import 'features/kepulangan/presentation/pages/kepulangan_page.dart';
import 'features/kepulangan/presentation/pages/pengajuan_kepulangan_page.dart';
import 'features/pelanggaran/pelanggaran_page.dart';
import 'features/capaian/presentation/pages/capaian_page.dart';
import 'features/absensi/pages/absensi_page.dart';
import 'features/absensi/pages/detail_minggu_page.dart';
import 'features/absensi/pages/riwayat_bulan_page.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();

  SystemChrome.setPreferredOrientations([
    DeviceOrientation.portraitUp,
    DeviceOrientation.portraitDown,
  ]);

  SystemChrome.setSystemUIOverlayStyle(
    const SystemUiOverlayStyle(
      statusBarColor: Colors.transparent,
      statusBarIconBrightness: Brightness.dark,
    ),
  );

  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'SIM-PKPPS Mobile',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        useMaterial3: true,
        colorScheme: ColorScheme.fromSeed(
          seedColor: Colors.deepPurple,
          brightness: Brightness.light,
        ),
        platform: TargetPlatform.android,
        fontFamily: 'Roboto',
        cardTheme: CardTheme(
          elevation: 2,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
        ),
        appBarTheme: const AppBarTheme(centerTitle: true, elevation: 0),
      ),
      // 🔥 KUNCI SOLUSI: Wrap builder dengan AndroidFrameWrapper
      builder: (context, child) {
        return AndroidFrameWrapper(
          showFrame: true,
          child: child ?? const SizedBox(),
        );
      },
      // Named Routes
      initialRoute: '/',
      routes: {
        '/': (context) => const SplashScreen(),
        '/login': (context) => const LoginPage(),
        '/dashboard': (context) => const DashboardPage(),
        '/profil': (context) => const ProfilPage(),
        '/uang-saku': (context) => const UangSakuPage(),
        '/berita': (context) => const BeritaPage(),
        '/kesehatan': (context) => const KesehatanPage(),
        '/spp': (context) => const SppPage(),
        '/kepulangan': (context) => const KepulanganPage(),
        '/kepulangan/pengajuan': (context) => const PengajuanKepulanganFormPage(),
        '/pelanggaran': (context) => const PelanggaranPage(),
        '/capaian': (context) => const CapaianPage(),
        '/absensi': (context) => const AbsensiPage(),
        '/absensi/detail-minggu': (context) => const DetailMingguPage(),
        '/absensi/riwayat-bulan': (context) => const RiwayatBulanPage(),
      },
    );
  }
}
