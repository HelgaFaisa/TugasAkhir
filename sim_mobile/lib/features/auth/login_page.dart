// lib/features/auth/login_page.dart

import 'dart:math' as math;
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../core/api/api_service.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final _formKey = GlobalKey<FormState>();
  final _usernameController = TextEditingController();
  final _passwordController = TextEditingController();
  final _api = ApiService();

  bool _isLoading = false;
  bool _obscurePassword = true;
  bool _rememberMe = false;

  @override
  void initState() {
    super.initState();
    _loadSavedCredentials();
  }

  Future<void> _loadSavedCredentials() async {
    final prefs = await SharedPreferences.getInstance();
    final remember = prefs.getBool('remember_me') ?? false;
    if (remember && mounted) {
      setState(() {
        _rememberMe = true;
        _usernameController.text = prefs.getString('saved_username') ?? '';
        _passwordController.text = prefs.getString('saved_password') ?? '';
      });
    }
  }

  Future<void> _saveCredentials() async {
    final prefs = await SharedPreferences.getInstance();
    if (_rememberMe) {
      await prefs.setBool('remember_me', true);
      await prefs.setString('saved_username', _usernameController.text.trim());
      await prefs.setString('saved_password', _passwordController.text);
    } else {
      await prefs.remove('remember_me');
      await prefs.remove('saved_username');
      await prefs.remove('saved_password');
    }
  }

  @override
  void dispose() {
    _usernameController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  Future<void> _handleLogin() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _isLoading = true);
    try {
      await _saveCredentials();
      final result = await _api.login(
        username: _usernameController.text.trim(),
        password: _passwordController.text,
      );
      if (!mounted) return;
      if (result['success'] == true) {
        final user = result['user'];
        if (user != null && user['role'] == 'wali') {
          Navigator.pushReplacementNamed(context, '/dashboard');
        } else {
          _showErrorDialog(
              'Akun ini tidak memiliki akses ke aplikasi mobile.\nHanya Wali Santri yang dapat menggunakan aplikasi ini.');
        }
      } else {
        _showErrorDialog(result['message'] ?? 'Username atau password salah');
      }
    } catch (e) {
      if (!mounted) return;
      _showErrorDialog('Koneksi gagal, periksa internet Anda');
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  void _showErrorDialog(String message) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Row(children: [
          const Icon(Icons.error_outline, color: Colors.red, size: 17),
          const SizedBox(width: 7),
          Flexible(
            child: Text('Login Gagal',
                style: Theme.of(context).textTheme.titleLarge,
                maxLines: 1,
                overflow: TextOverflow.ellipsis),
          ),
        ]),
        content: Text(message, maxLines: 5, overflow: TextOverflow.ellipsis),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('OK',
                style: TextStyle(color: Color(0xFF6FBA9D))),
          ),
        ],
      ),
    );
  }

  void _showForgotPasswordDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Row(children: [
          const Icon(Icons.help_outline, color: Color(0xFF6FBA9D), size: 17),
          const SizedBox(width: 7),
          Flexible(
            child: Text('Lupa Password?',
                style: Theme.of(context).textTheme.titleLarge,
                maxLines: 1,
                overflow: TextOverflow.ellipsis),
          ),
        ]),
        content: const Text(
          'Silakan hubungi admin pesantren untuk reset password.',
          style: TextStyle(fontSize: 11),
          maxLines: 3,
          overflow: TextOverflow.ellipsis,
        ),
        actions: [
          ElevatedButton(
            onPressed: () => Navigator.pop(context),
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF6FBA9D),
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(9)),
            ),
            child: const Text('Mengerti'),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.of(context).size;

    return Scaffold(
      backgroundColor: Colors.white,
      body: Stack(
        children: [
          // ── Background hijau atas dengan rounded bottom ───────────
          Positioned(
            top: 0,
            left: 0,
            right: 0,
            child: Container(
              height: size.height * 0.48,
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [Color(0xFF7ECBAE), Color(0xFF5AAF94)],
                ),
                borderRadius: BorderRadius.only(
                  bottomLeft: Radius.circular(40),
                  bottomRight: Radius.circular(40),
                ),
              ),
            ),
          ),

          // ── Ornamen geometris kiri atas ───────────────────────────
          Positioned(
            top: -30,
            left: -30,
            child: _GeometricOrnament(size: 180, opacity: 0.13),
          ),

          // ── Ornamen geometris kanan atas ──────────────────────────
          Positioned(
            top: -10,
            right: -40,
            child: _GeometricOrnament(size: 150, opacity: 0.10),
          ),

          // ── Main content ──────────────────────────────────────────
          SafeArea(
            child: SingleChildScrollView(
              physics: const ClampingScrollPhysics(),
              padding: EdgeInsets.symmetric(
                horizontal: size.width > 600 ? 60 : 24,
              ),
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    SizedBox(height: size.height * 0.05),

                    // ── Logo + Judul berdampingan ─────────────────
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.center,
                      children: [
                        // Logo
                        ClipOval(
                          child: Image.asset(
                            'assets/images/logo.png',
                            width: 52,
                            height: 52,
                            fit: BoxFit.cover,
                            errorBuilder: (_, __, ___) => Container(
                              width: 52,
                              height: 52,
                              decoration: BoxDecoration(
                                shape: BoxShape.circle,
                                color: Colors.white.withValues(alpha: 0.25),
                              ),
                              child: const Icon(
                                Icons.mosque_rounded,
                                color: Colors.white,
                                size: 26,
                              ),
                            ),
                          ),
                        ),
                        const SizedBox(width: 14),

                        // Teks kanan logo
                        Flexible(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'PKPPS',
                                style: GoogleFonts.cinzel(
                                  fontSize: 9,
                                  fontWeight: FontWeight.w500,
                                  color: Colors.white.withValues(alpha: 0.85),
                                  letterSpacing: 2,
                                ),
                              ),
                              Text(
                                'RIYADLUL JANNAH',
                                maxLines: 1,
                                softWrap: false,
                                overflow: TextOverflow.visible,
                                style: GoogleFonts.cinzel(
                                  fontSize: 14,
                                  fontWeight: FontWeight.w700,
                                  color: Colors.white,
                                  letterSpacing: 0.8,
                                ),
                              ),
                              const SizedBox(height: 3),
                              Container(
                                height: 1,
                                color: Colors.white.withValues(alpha: 0.3),
                              ),
                              const SizedBox(height: 4),
                              Text(
                                'SISTEM INFORMASI PESANTREN',
                                maxLines: 1,
                                softWrap: false,
                                overflow: TextOverflow.visible,
                                style: GoogleFonts.cinzel(
                                  fontSize: 6,
                                  color: Colors.white.withValues(alpha: 0.75),
                                  letterSpacing: 1.2,
                                  fontWeight: FontWeight.w500,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),

                    SizedBox(height: size.height * 0.045),

                    // ── Form Card ─────────────────────────────────
                    Container(
                      padding: const EdgeInsets.fromLTRB(20, 22, 20, 8),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(20),
                        boxShadow: [
                          BoxShadow(
                            color: const Color(0xFF6FBA9D)
                                .withValues(alpha: 0.18),
                            blurRadius: 30,
                            offset: const Offset(0, 10),
                          ),
                          BoxShadow(
                            color: Colors.black.withValues(alpha: 0.06),
                            blurRadius: 12,
                            offset: const Offset(0, 4),
                          ),
                        ],
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          // Header card
                          Row(
                            children: [
                              Container(
                                width: 3,
                                height: 20,
                                decoration: BoxDecoration(
                                  color: const Color(0xFF6FBA9D),
                                  borderRadius: BorderRadius.circular(2),
                                ),
                              ),
                              const SizedBox(width: 8),
                              Text(
                                'Masuk ke Akun',
                                style: GoogleFonts.lora(
                                  fontSize: 14,
                                  fontWeight: FontWeight.w600,
                                  color: const Color(0xFF1A3C2E),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 3),
                          Padding(
                            padding: const EdgeInsets.only(left: 11),
                            child: Text(
                              'Selamat datang, Wali Santri',
                              style: TextStyle(
                                fontSize: 10,
                                color: Colors.grey[500],
                              ),
                            ),
                          ),
                          const SizedBox(height: 18),

                          // Username
                          _buildField(
                            controller: _usernameController,
                            label: 'USERNAME',
                            hint: 'Masukkan username',
                            icon: Icons.person_outline_rounded,
                            textInputAction: TextInputAction.next,
                            validator: (v) => (v == null || v.trim().isEmpty)
                                ? 'Username wajib diisi'
                                : null,
                          ),
                          const SizedBox(height: 12),

                          // Password
                          _buildField(
                            controller: _passwordController,
                            label: 'PASSWORD',
                            hint: 'Masukkan password',
                            icon: Icons.lock_outline_rounded,
                            obscureText: _obscurePassword,
                            textInputAction: TextInputAction.done,
                            onFieldSubmitted: (_) => _handleLogin(),
                            suffixIcon: IconButton(
                              icon: Icon(
                                _obscurePassword
                                    ? Icons.visibility_outlined
                                    : Icons.visibility_off_outlined,
                                color: Colors.grey[400],
                                size: 18,
                              ),
                              onPressed: () => setState(
                                  () => _obscurePassword = !_obscurePassword),
                            ),
                            validator: (v) => (v == null || v.isEmpty)
                                ? 'Password wajib diisi'
                                : null,
                          ),
                          const SizedBox(height: 10),

                          // Ingat Saya + Lupa Password
                          Row(
                            children: [
                              GestureDetector(
                                onTap: () => setState(
                                    () => _rememberMe = !_rememberMe),
                                child: Row(
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    // Custom checkbox manual
                                    Container(
                                      width: 18,
                                      height: 18,
                                      decoration: BoxDecoration(
                                        color: _rememberMe
                                            ? const Color(0xFF6FBA9D)
                                            : Colors.transparent,
                                        borderRadius: BorderRadius.circular(4),
                                        border: Border.all(
                                          color: _rememberMe
                                              ? const Color(0xFF6FBA9D)
                                              : Colors.grey[400]!,
                                          width: 1.5,
                                        ),
                                      ),
                                      child: _rememberMe
                                          ? const Icon(Icons.check,
                                              size: 12, color: Colors.white)
                                          : null,
                                    ),
                                    const SizedBox(width: 6),
                                    Text(
                                      'Ingat Saya',
                                      style: TextStyle(
                                        fontSize: 11,
                                        color: Colors.grey[600],
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                              const Spacer(),
                              TextButton(
                                onPressed: _showForgotPasswordDialog,
                                style: TextButton.styleFrom(
                                  padding: EdgeInsets.zero,
                                  minimumSize: Size.zero,
                                  tapTargetSize:
                                      MaterialTapTargetSize.shrinkWrap,
                                ),
                                child: const Text(
                                  'Lupa Password?',
                                  style: TextStyle(
                                    color: Color(0xFF6FBA9D),
                                    fontSize: 11,
                                    fontWeight: FontWeight.w500,
                                  ),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 16),

                          // Tombol MASUK
                          SizedBox(
                            height: 46,
                            child: ElevatedButton(
                              onPressed: _isLoading ? null : _handleLogin,
                              style: ElevatedButton.styleFrom(
                                backgroundColor: const Color(0xFF6FBA9D),
                                foregroundColor: Colors.white,
                                disabledBackgroundColor: Colors.grey[200],
                                elevation: 2,
                                shadowColor: const Color(0xFF6FBA9D)
                                    .withValues(alpha: 0.4),
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(12),
                                ),
                              ),
                              child: _isLoading
                                  ? const SizedBox(
                                      width: 20,
                                      height: 20,
                                      child: CircularProgressIndicator(
                                        valueColor: AlwaysStoppedAnimation(
                                            Colors.white),
                                        strokeWidth: 2,
                                      ),
                                    )
                                  : Row(
                                      mainAxisAlignment:
                                          MainAxisAlignment.center,
                                      children: [
                                        Text(
                                          'MASUK',
                                          style: GoogleFonts.cinzel(
                                            fontSize: 13,
                                            fontWeight: FontWeight.bold,
                                            letterSpacing: 3,
                                          ),
                                        ),
                                        const SizedBox(width: 8),
                                        const Icon(
                                            Icons.arrow_forward_rounded,
                                            size: 16),
                                      ],
                                    ),
                            ),
                          ),
                          const SizedBox(height: 14),
                        ],
                      ),
                    ),

                    const SizedBox(height: 20),

                    // Footer
                    Center(
                      child: Text(
                        '© 2026 PKPPS Riyadlul Jannah',
                        style: TextStyle(
                          fontSize: 9,
                          color: Colors.grey[400],
                          letterSpacing: 0.8,
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildField({
    required TextEditingController controller,
    required String label,
    required String hint,
    required IconData icon,
    bool obscureText = false,
    TextInputAction textInputAction = TextInputAction.next,
    ValueChanged<String>? onFieldSubmitted,
    Widget? suffixIcon,
    String? Function(String?)? validator,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: TextStyle(
            fontSize: 9,
            fontWeight: FontWeight.w700,
            color: Colors.grey[500],
            letterSpacing: 1.5,
          ),
        ),
        const SizedBox(height: 5),
        TextFormField(
          controller: controller,
          obscureText: obscureText,
          textInputAction: textInputAction,
          onFieldSubmitted: onFieldSubmitted,
          style: const TextStyle(
            color: Color(0xFF1A3C2E),
            fontSize: 13,
          ),
          decoration: InputDecoration(
            hintText: hint,
            hintStyle: TextStyle(color: Colors.grey[400], fontSize: 12),
            prefixIcon: Icon(icon, color: const Color(0xFF6FBA9D), size: 18),
            suffixIcon: suffixIcon,
            filled: true,
            fillColor: const Color(0xFFF4FAF7),
            contentPadding:
                const EdgeInsets.symmetric(horizontal: 14, vertical: 13),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(10),
              borderSide: const BorderSide(color: Color(0xFFD4EDE4)),
            ),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(10),
              borderSide: const BorderSide(color: Color(0xFFD4EDE4)),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(10),
              borderSide:
                  const BorderSide(color: Color(0xFF6FBA9D), width: 1.5),
            ),
            errorBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(10),
              borderSide: const BorderSide(color: Colors.red),
            ),
            focusedErrorBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(10),
              borderSide: const BorderSide(color: Colors.red),
            ),
            errorStyle: const TextStyle(fontSize: 10),
          ),
          validator: validator,
        ),
      ],
    );
  }
}

// ── Islamic Geometric Ornament ───────────────────────────────────────────────

class _GeometricOrnament extends StatelessWidget {
  final double size;
  final double opacity;

  const _GeometricOrnament({required this.size, required this.opacity});

  @override
  Widget build(BuildContext context) {
    return Opacity(
      opacity: opacity,
      child: CustomPaint(
        size: Size(size, size),
        painter: _IslamicPatternPainter(),
      ),
    );
  }
}

class _IslamicPatternPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = Colors.white
      ..style = PaintingStyle.stroke
      ..strokeWidth = 1.2;

    final center = Offset(size.width / 2, size.height / 2);
    final radius = size.width / 2;

    for (int ring = 1; ring <= 5; ring++) {
      final r = radius * ring / 5;
      _drawGeometricRing(canvas, paint, center, r, 8);
    }

    for (int i = 0; i < 16; i++) {
      final angle = (i * math.pi * 2) / 16;
      final x1 = center.dx + (radius * 0.15) * math.cos(angle);
      final y1 = center.dy + (radius * 0.15) * math.sin(angle);
      final x2 = center.dx + radius * math.cos(angle);
      final y2 = center.dy + radius * math.sin(angle);
      canvas.drawLine(Offset(x1, y1), Offset(x2, y2), paint);
    }
  }

  void _drawGeometricRing(
      Canvas canvas, Paint paint, Offset center, double radius, int points) {
    final path = Path();
    for (int i = 0; i < points; i++) {
      final angle = (i * math.pi * 2) / points - math.pi / 2;
      final nextAngle = ((i + 1) * math.pi * 2) / points - math.pi / 2;

      final x1 = center.dx + radius * math.cos(angle);
      final y1 = center.dy + radius * math.sin(angle);
      final x2 = center.dx + radius * math.cos(nextAngle);
      final y2 = center.dy + radius * math.sin(nextAngle);

      final crossAngle = angle + math.pi * 3 / points;
      final x3 = center.dx + radius * 0.6 * math.cos(crossAngle);
      final y3 = center.dy + radius * 0.6 * math.sin(crossAngle);

      if (i == 0) path.moveTo(x1, y1);
      path.lineTo(x3, y3);
      path.lineTo(x2, y2);
    }
    path.close();
    canvas.drawPath(path, paint);
  }

  @override
  bool shouldRepaint(_IslamicPatternPainter oldDelegate) => false;
}