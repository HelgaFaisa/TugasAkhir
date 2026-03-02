<?php
// app/Services/CapaianAccessService.php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Service untuk mengelola akses input capaian oleh santri.
 * Menggunakan Laravel Cache (no migration needed).
 * Data disimpan di cache dengan key 'capaian_access_config'.
 */
class CapaianAccessService
{
    const CACHE_KEY = 'capaian_access_config';
    const CACHE_TTL = 60 * 24 * 30; // 30 hari (dalam menit)

    /**
     * Ambil konfigurasi akses saat ini.
     */
    public static function getConfig(): array
    {
        return Cache::get(self::CACHE_KEY, [
            'is_open'      => false,
            'opened_by'    => null,
            'opened_at'    => null,
            'closed_at'    => null,
            'catatan'      => null,
            'id_semester'  => null,
            // Opsional: auto-close setelah X jam (null = manual)
            'auto_close_at'=> null,
        ]);
    }

    /**
     * Buka akses input capaian untuk santri.
     */
    public static function open(array $params = []): void
    {
        $config = self::getConfig();

        $autoCloseAt = null;
        if (!empty($params['durasi_jam'])) {
            $autoCloseAt = now()->addHours((int) $params['durasi_jam'])->toIso8601String();
        }

        $config = array_merge($config, [
            'is_open'       => true,
            'opened_by'     => $params['opened_by'] ?? auth()->user()?->name,
            'opened_at'     => now()->toIso8601String(),
            'closed_at'     => null,
            'catatan'       => $params['catatan'] ?? null,
            'id_semester'   => $params['id_semester'] ?? null,
            'auto_close_at' => $autoCloseAt,
        ]);

        Cache::put(self::CACHE_KEY, $config, now()->addMinutes(self::CACHE_TTL));
    }

    /**
     * Tutup akses input capaian.
     */
    public static function close(): void
    {
        $config = self::getConfig();
        $config['is_open']    = false;
        $config['closed_at']  = now()->toIso8601String();
        Cache::put(self::CACHE_KEY, $config, now()->addMinutes(self::CACHE_TTL));
    }

    /**
     * Cek apakah akses sedang dibuka.
     * Otomatis tutup jika sudah melewati auto_close_at.
     */
    public static function isOpen(): bool
    {
        $config = self::getConfig();

        if (!$config['is_open']) return false;

        // Auto-close check
        if (!empty($config['auto_close_at'])) {
            if (now()->isAfter($config['auto_close_at'])) {
                self::close();
                return false;
            }
        }

        return true;
    }

    /**
     * Cek apakah semester yang dibuka cocok dengan semester tertentu.
     * Jika id_semester di config null, berarti semua semester boleh.
     */
    public static function isOpenForSemester(?string $idSemester): bool
    {
        if (!self::isOpen()) return false;

        $config = self::getConfig();
        if (empty($config['id_semester'])) return true; // semua semester

        return $config['id_semester'] === $idSemester;
    }

    /**
     * Ambil sisa waktu auto-close dalam format human readable.
     */
    public static function getSisaWaktu(): ?string
    {
        $config = self::getConfig();
        if (empty($config['auto_close_at'])) return null;

        $close = \Carbon\Carbon::parse($config['auto_close_at']);
        if (now()->isAfter($close)) return 'Sudah berakhir';

        return now()->diffForHumans($close, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]);
    }
}