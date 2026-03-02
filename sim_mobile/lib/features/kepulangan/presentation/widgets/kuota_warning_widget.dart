// lib/features/kepulangan/presentation/widgets/kuota_warning_widget.dart

import 'package:flutter/material.dart';

class KuotaWarningWidget extends StatefulWidget {
  final String message;
  final bool isOverLimit;

  const KuotaWarningWidget({
    super.key,
    required this.message,
    this.isOverLimit = false,
  });

  @override
  State<KuotaWarningWidget> createState() => _KuotaWarningWidgetState();
}

class _KuotaWarningWidgetState extends State<KuotaWarningWidget>
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _pulseAnimation;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      duration: const Duration(milliseconds: 1500),
      vsync: this,
    )..repeat(reverse: true);

    _pulseAnimation = Tween<double>(begin: 0.95, end: 1.05).animate(
      CurvedAnimation(parent: _controller, curve: Curves.easeInOut),
    );
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    if (widget.message.isEmpty) return const SizedBox();

    final bgColor =
        widget.isOverLimit ? Colors.red.shade50 : Colors.orange.shade50;
    final borderColor =
        widget.isOverLimit ? Colors.red.shade200 : Colors.orange.shade200;
    final iconColor =
        widget.isOverLimit ? Colors.red.shade700 : Colors.orange.shade700;
    final textColor =
        widget.isOverLimit ? Colors.red.shade900 : Colors.orange.shade900;

    return ScaleTransition(
      scale: widget.isOverLimit ? _pulseAnimation : const AlwaysStoppedAnimation(1.0),
      child: Container(
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: bgColor,
          border: Border.all(color: borderColor, width: 2),
          borderRadius: BorderRadius.circular(9),
          boxShadow: [
            BoxShadow(
              color: borderColor.withValues(alpha: 0.3),
              blurRadius: 8,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              padding: const EdgeInsets.all(7),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(7),
              ),
              child: Icon(
                widget.isOverLimit
                    ? Icons.error_outline
                    : Icons.warning_amber_rounded,
                color: iconColor,
                size: 19,
              ),
            ),
            const SizedBox(width: 9),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    widget.isOverLimit ? 'âš ï¸ OVER LIMIT!' : 'âš ï¸ PERHATIAN',
                    style: TextStyle(
                      color: textColor,
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    widget.message,
                    style: TextStyle(
                      color: textColor,
                      fontSize: 11,
                    ),
                  ),
                  if (widget.isOverLimit) ...[
                    const SizedBox(height: 7),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 7,
                        vertical: 2,
                      ),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(5),
                      ),
                      child: Text(
                        'Anda tetap bisa mengajukan, tapi akan melebihi batas',
                        style: TextStyle(
                          color: textColor,
                          fontSize: 8,
                          fontStyle: FontStyle.italic,
                        ),
                      ),
                    ),
                  ],
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}