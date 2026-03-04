// lib/core/config/app_config.dart

class AppConfig {
  // 🌐 UNTUK FLUTTER WEB (Chrome/Browser)
  // Gunakan localhost karena browser dan server di komputer yang sama
  static const String baseUrl =
      'http://localhost/TugasAkhir/sim-pkpps/public/api/v1';
  
  // Base URL untuk asset (gambar, file)
  static const String baseAssetUrl =
      'http://localhost/TugasAkhir/sim-pkpps/public';
  
//   // Mobile
//   static const String baseUrl =
//     'http://192.168.100.71:8000/api/v1';
// static const String baseAssetUrl =
//     'http://192.168.100.71:8000';


  // Timeout untuk request
  static const Duration timeout = Duration(seconds: 30);

  // App Info
  static const String appName = 'SIM-PKPPS Mobile';
  static const String appVersion = '1.0.0';
}
