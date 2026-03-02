// lib/core/widgets/berita_image.dart
// Widget RINGAN untuk load gambar berita - TANPA package tambahan

import 'package:flutter/material.dart';

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

  @override
  Widget build(BuildContext context) {
    // Jika null atau kosong, return empty
    if (imageUrl == null || imageUrl!.isEmpty) {
      return const SizedBox.shrink();
    }

    return ClipRRect(
      borderRadius: borderRadius ?? BorderRadius.zero,
      child: Image.network(
        imageUrl!,
        width: double.infinity,
        height: height,
        fit: BoxFit.cover,
        
        // Optimasi: cacheWidth untuk resize otomatis
        cacheWidth: 800,
        
        // Loading placeholder ringan
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
        
        // Error handler
        errorBuilder: (context, error, stackTrace) {
          debugPrint('ðŸ”´ Image error: $imageUrl');
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
