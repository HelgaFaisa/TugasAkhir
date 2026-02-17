// lib/features/splash/splash_screen.dart

import 'dart:convert';
import 'package:flutter/material.dart';
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

    // Tidak ada token → Login
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
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [
              Color(0xFF7C3AED),
              Color(0xFF5B21B6),
            ],
          ),
        ),
        child: const Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              // Logo
              Icon(
                Icons.school_rounded,
                size: 80,
                color: Colors.white,
              ),
              SizedBox(height: 20),
              
              // App Name
              Text(
                'SIM-PKPPS',
                style: TextStyle(
                  fontSize: 28,
                  fontWeight: FontWeight.bold,
                  color: Colors.white,
                  letterSpacing: 2,
                ),
              ),
              SizedBox(height: 8),
              Text(
                'Mobile',
                style: TextStyle(
                  fontSize: 16,
                  color: Colors.white70,
                  letterSpacing: 1,
                ),
              ),
              SizedBox(height: 40),
              
              // Loading indicator
              SizedBox(
                width: 30,
                height: 30,
                child: CircularProgressIndicator(
                  valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                  strokeWidth: 3,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}