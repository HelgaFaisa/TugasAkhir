import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart' show kIsWeb;

/// Widget untuk menampilkan frame Android seperti di Android Studio
/// Frame dengan warna menarik dan ukuran Infinix Hot 30i
class AndroidFrameWrapper extends StatelessWidget {
  final Widget child;
  final bool showFrame;

  const AndroidFrameWrapper({
    super.key,
    required this.child,
    this.showFrame = true,
  });

  @override
  Widget build(BuildContext context) {
    // Jika bukan web atau showFrame false, tampilkan child langsung
    if (!kIsWeb || !showFrame) {
      return child;
    }

    // Di web, tampilkan dengan frame HP
    return Scaffold(
      backgroundColor: const Color(0xFFE3F2FD), // Background biru muda lembut
      body: Center(
        child: Container(
          // Ukuran Infinix Hot 30i: 6.6 inch, 720 x 1612 pixels
          width: 360,  // Infinix Hot 30i width
          height: 806, // Infinix Hot 30i height (scaled)
          decoration: BoxDecoration(
            // Gradient putih ke biru muda
            gradient: const LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [
                Color(0xFFFFFFFF), // Putih
                Color(0xFFE3F2FD), // Biru muda
              ],
            ),
            borderRadius: BorderRadius.circular(40),
            border: Border.all(
              color: const Color(0xFF90CAF9), // Border biru muda
              width: 8,
            ),
            boxShadow: [
              // Shadow luar (biru)
              BoxShadow(
                color: const Color(0xFF2196F3).withOpacity(0.3),
                blurRadius: 30,
                spreadRadius: 5,
                offset: const Offset(0, 10),
              ),
              // Shadow dalam (putih)
              BoxShadow(
                color: Colors.white.withOpacity(0.8),
                blurRadius: 15,
                spreadRadius: -5,
                offset: const Offset(0, -5),
              ),
            ],
          ),
          child: ClipRRect(
            borderRadius: BorderRadius.circular(34),
            child: Container(
              decoration: BoxDecoration(
                color: Colors.white,
                border: Border.all(
                  color: const Color(0xFFBBDEFB), // Inner border biru muda
                  width: 2,
                ),
              ),
              child: ClipRRect(
                borderRadius: BorderRadius.circular(32),
                child: Column(
                  children: [
                    _buildNotch(),
                    _buildStatusBar(),
                    Expanded(child: child),
                    _buildNavigationBar(),
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }

  // Notch camera (seperti Infinix Hot 30i)
  Widget _buildNotch() {
    return Container(
      height: 30,
      color: Colors.black,
      child: Center(
        child: Container(
          margin: const EdgeInsets.only(top: 8),
          width: 90,
          height: 22,
          decoration: BoxDecoration(
            color: Colors.black,
            borderRadius: BorderRadius.circular(15),
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              // Speaker grille
              Container(
                width: 30,
                height: 3,
                decoration: BoxDecoration(
                  color: const Color(0xFF1a1a1a),
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
              const SizedBox(width: 8),
              // Camera
              Container(
                width: 8,
                height: 8,
                decoration: BoxDecoration(
                  color: const Color(0xFF0D47A1), // Camera biru gelap
                  shape: BoxShape.circle,
                  border: Border.all(
                    color: const Color(0xFF1976D2),
                    width: 1,
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  // Status bar Android
  Widget _buildStatusBar() {
    return Container(
      height: 28,
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          colors: [
            Color(0xFF1976D2), // Biru medium
            Color(0xFF2196F3), // Biru cerah
          ],
        ),
      ),
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          const Text(
            '10:30',
            style: TextStyle(
              color: Colors.white,
              fontSize: 14,
              fontWeight: FontWeight.w600,
              shadows: [
                Shadow(
                  color: Color(0x40000000),
                  offset: Offset(0, 1),
                  blurRadius: 2,
                ),
              ],
            ),
          ),
          Row(
            children: const [
              Icon(Icons.signal_cellular_4_bar, 
                color: Colors.white, 
                size: 14,
                shadows: [
                  Shadow(
                    color: Color(0x40000000),
                    offset: Offset(0, 1),
                    blurRadius: 2,
                  ),
                ],
              ),
              SizedBox(width: 4),
              Icon(Icons.wifi, 
                color: Colors.white, 
                size: 16,
                shadows: [
                  Shadow(
                    color: Color(0x40000000),
                    offset: Offset(0, 1),
                    blurRadius: 2,
                  ),
                ],
              ),
              SizedBox(width: 4),
              Icon(Icons.battery_full, 
                color: Colors.white, 
                size: 18,
                shadows: [
                  Shadow(
                    color: Color(0x40000000),
                    offset: Offset(0, 1),
                    blurRadius: 2,
                  ),
                ],
              ),
            ],
          ),
        ],
      ),
    );
  }

  // Navigation bar (gesture bar modern)
  Widget _buildNavigationBar() {
    return Container(
      height: 48,
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors: [
            Color(0xFF0D47A1), // Biru gelap
            Color(0xFF1565C0), // Biru medium
          ],
        ),
      ),
      alignment: Alignment.center,
      child: Container(
        width: 140,
        height: 4,
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.8),
          borderRadius: BorderRadius.circular(10),
          boxShadow: [
            BoxShadow(
              color: Colors.white.withOpacity(0.3),
              blurRadius: 8,
              spreadRadius: 2,
            ),
          ],
        ),
      ),
    );
  }
}