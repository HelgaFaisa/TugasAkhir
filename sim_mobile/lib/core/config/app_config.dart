// lib/core/config/app_config.dart

class AppConfig {
  // 🌐 UNTUK FLUTTER WEB (Chrome/Browser)
  // Gunakan localhost karena browser dan server di komputer yang sama
  static const String baseUrl =
      'http://localhost/TugasAkhir/sim-pkpps/public/api/v1';
  
  // Base URL untuk asset (gambar, file)
  static const String baseAssetUrl =
      'http://localhost/TugasAkhir/sim-pkpps/public';

  // ============================================
  // 📱 UNTUK MOBILE (Uncomment jika pakai HP/Emulator)
  // ============================================
  // Real Device (HP):
  // static const String baseUrl =
  //     'http://10.130.244.240/TugasAkhir/sim-pkpps/public/api/v1';
  // static const String baseAssetUrl =
  //     'http://10.130.244.240/TugasAkhir/sim-pkpps/public';
  //
  // Emulator Android:
  // static const String baseUrl =
  //     'http://10.0.2.2/TugasAkhir/sim-pkpps/public/api/v1';
  // static const String baseAssetUrl =
  //     'http://10.0.2.2/TugasAkhir/sim-pkpps/public';

  // Timeout untuk request
  static const Duration timeout = Duration(seconds: 30);

  // App Info
  static const String appName = 'SIM-PKPPS Mobile';
  static const String appVersion = '1.0.0';
}
