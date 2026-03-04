import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart'; // ← tambahkan ini

class BeritaImage extends StatelessWidget {
  final String? imageUrl;
  final double height;
  final BorderRadius? borderRadius;

  const BeritaImage({
    super.key,
    required this.imageUrl,
    this.height = 180,
    this.borderRadius,
  });

  String _fixUrl(String url) {
    if (kIsWeb) {
      // Chrome: tetap pakai localhost, jangan diganti
      return url;
    }
    // Android emulator
    return url.replaceFirst('http://localhost', 'http://192.168.100.71');
}

  @override
  Widget build(BuildContext context) {
    if (imageUrl == null || imageUrl!.isEmpty) {
      return const SizedBox.shrink();
    }

    final fixedUrl = _fixUrl(imageUrl!);

    return ClipRRect(
      borderRadius: borderRadius ?? BorderRadius.zero,
      child: Image.network(
        fixedUrl,
        width: double.infinity,
        height: height,
        fit: BoxFit.cover,
        cacheWidth: 800,
        loadingBuilder: (context, child, loadingProgress) {
          if (loadingProgress == null) return child;
          return Container(
            height: height,
            color: Colors.grey[100],
            child: const Center(
              child: SizedBox(
                width: 15,
                height: 15,
                child: CircularProgressIndicator(strokeWidth: 2),
              ),
            ),
          );
        },
        errorBuilder: (context, error, stackTrace) {
          debugPrint('🔴 Image error: $error');
          return Container(
            height: height,
            color: Colors.grey[200],
            child: Icon(
              Icons.image_not_supported_outlined,
              size: height * 0.2,
              color: Colors.grey[400],
            ),
          );
        },
      ),
    );
  }
}