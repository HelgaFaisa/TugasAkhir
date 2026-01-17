// lib/core/config/app_config.dart

class AppConfig {
  // 🔥 PERBAIKAN: URL kamu salah strukturnya
  // Dari: http://10.0.2.2/sim-pkpps/public/api/mobile
  // Ke:   http://10.0.2.2/sim-pkpps/public/api/v1
  
  static const String baseUrl = 'http://10.0.2.2/sim-pkpps/public/api/v1';
  
  // Jika pakai XAMPP/Apache, pastikan ini:
  // - Apache running di port 80
  // - File .htaccess aktif di Laravel
  // - URL_rewrite sudah aktif di Apache
  
  // Jika pakai php artisan serve, gunakan ini:
  // static const String baseUrl = 'http://10.0.2.2:8000/api/v1';
  
  // Timeout untuk request
  static const Duration timeout = Duration(seconds: 30);
  
  // App Info
  static const String appName = 'SIM-PKPPS Mobile';
  static const String appVersion = '1.0.0';
}