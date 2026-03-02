// lib/features/splash/splash_screen.dart

import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../core/api/api_service.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  final _api = ApiService();

  @override
  void initState() {
    super.initState();
    _checkAuth();
  }

  Future<void> _checkAuth() async {
    // Minimal delay untuk UX (500ms)
    await Future.delayed(const Duration(milliseconds: 500));

    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');
    final userJson = prefs.getString('user_data');

    if (!mounted) return;

    // Tidak ada token â†’ Login
    if (token == null) {
      Navigator.pushReplacementNamed(context, '/login');
      return;
    }

    // Cek role dari local storage
    if (userJson != null) {
      final userData = json.decode(userJson);
      final role = userData['role'];
      
      // Hanya wali yang boleh akses mobile
      if (role != 'wali') {
        await prefs.clear();
        if (mounted) {
          Navigator.pushReplacementNamed(context, '/login');
        }
        return;
      }
    }

    // Validasi token ke server
    final isValid = await _api.isTokenValid();

    if (!mounted) return;

    if (isValid) {
      Navigator.pushReplacementNamed(context, '/dashboard');
    } else {
      await prefs.clear();
      Navigator.pushReplacementNamed(context, '/login');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // Logo
            Image.asset(
              'assets/images/logo.png',
              width: 120,
              height: 120,
            ),
            const SizedBox(height: 20),

            // App Name
            Text(
              'PKPPS RIYADLUL JANNAH',
              style: GoogleFonts.cinzel(
                fontSize: 16,
                fontWeight: FontWeight.w700,
                color: const Color(0xFF2C3E50),
                letterSpacing: 2.5,
              ),
            ),
            const SizedBox(height: 30),

            // Loading indicator
            const SizedBox(
              width: 24,
              height: 24,
              child: CircularProgressIndicator(
                valueColor: AlwaysStoppedAnimation<Color>(Color(0xFF6FBA9D)),
                strokeWidth: 3,
              ),
            ),
          ],
        ),
      ),
    );
  }
}