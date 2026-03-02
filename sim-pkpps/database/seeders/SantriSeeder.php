<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SantriSeeder extends Seeder
{
    /**
     * ============================================================
     * REFERENSI KODE KELAS (dari KelasSeeder)
     * ============================================================
     * Kelas Pondok (KEL001):
     *   KLS001 = PB
     *   KLS002 = Lambatan
     *   KLS003 = Cepatan
     *
     * Sekolah Formal (KEL002):
     *   KLS004 = SD 1   | KLS005 = SD 2   | KLS006 = SD 3
     *   KLS007 = SD 4   | KLS008 = SD 5   | KLS009 = SD 6
     *   KLS010 = SMP 7  | KLS011 = SMP 8  | KLS012 = SMP 9
     *   KLS013 = SMA 10 | KLS014 = SMA 11 | KLS015 = SMA 12
     * ============================================================
     *
     * CARA EDIT KELAS SANTRI:
     * Setiap santri punya array 'kelas' berisi kode kelas yang diikuti.
     * - is_primary: true  = kelas utama (kelas pondok)
     * - is_primary: false = kelas tambahan (kelas formal)
     * Ubah kode kelas di bagian 'kelas' sesuai data asli!
     */

    public function run(): void
    {
        // Hanya hapus data S003 ke atas, S001 & S002 tetap aman
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('santri_kelas')
            ->whereIn('id_santri', array_map(fn($n) => 'S' . str_pad($n, 3, '0', STR_PAD_LEFT), range(3, 99)))
            ->delete();
        DB::table('santris')
            ->whereIn('id_santri', array_map(fn($n) => 'S' . str_pad($n, 3, '0', STR_PAD_LEFT), range(3, 99)))
            ->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $tahunAjaran = '2024/2025';
        $now = Carbon::now();

        $santriList = [
            // ============================================================
            // S003 - S034: Edit bagian 'kelas' sesuai data asli!
            // ============================================================
            [
                'santri' => ['id_santri' => 'S003', 'nis' => '510035160089253003', 'nama_lengkap' => 'Altaf Baihaqi Amrullah', 'jenis_kelamin' => 'Laki-laki', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Tiebuk RT 006 RW 003 Wiyu, Pacet, Mojokerto', 'daerah_asal' => 'Mojokerto Barat', 'nama_orang_tua' => 'Baihaqi', 'nomor_hp_ortu' => '+6281234560003', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS001', 'is_primary' => true],  // PB       ← UBAH SESUAI DATA
                    ['kode' => 'KLS004', 'is_primary' => false],  // SD 1     ← UBAH SESUAI DATA
                ],
            ],
            [
                'santri' => ['id_santri' => 'S004', 'nis' => '510035160089253004', 'nama_lengkap' => 'Aminati Yusrin Isnaini', 'jenis_kelamin' => 'Perempuan', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Brangkal RT 002 RW 001 Brangkal, Sooko, Mojokerto', 'daerah_asal' => 'Mojokerto Barat', 'nama_orang_tua' => 'Yusrin', 'nomor_hp_ortu' => '+6281234560004', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS001', 'is_primary' => true],  // PB       ← UBAH
                    ['kode' => 'KLS005', 'is_primary' => false],  // SD 2     ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S005', 'nis' => '510035160089253005', 'nama_lengkap' => 'Ananda Novreandis', 'jenis_kelamin' => 'Laki-laki', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Jampirogo RT 006 RW 001 Jampirogo, Sooko, Mojokerto', 'daerah_asal' => 'Mojokerto Barat', 'nama_orang_tua' => 'Novreandis Sr.', 'nomor_hp_ortu' => '+6281234560005', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS001', 'is_primary' => true],  // PB       ← UBAH
                    ['kode' => 'KLS004', 'is_primary' => false],  // SD 1     ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S006', 'nis' => '510035160089253006', 'nama_lengkap' => 'Andika Maulana Ishaq', 'jenis_kelamin' => 'Laki-laki', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Jemparing RT 001 RW 001 Pakel, Bareng, Jombang', 'daerah_asal' => 'Jombang Selatan', 'nama_orang_tua' => 'Ishaq', 'nomor_hp_ortu' => '+6281234560006', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS002', 'is_primary' => true],  // Lambatan ← UBAH
                    ['kode' => 'KLS010', 'is_primary' => false],  // SMP 7    ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S007', 'nis' => '510035160089253007', 'nama_lengkap' => 'Anggraini Nur Dina Fahma', 'jenis_kelamin' => 'Perempuan', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Plosarejo RT 002 RW 001 Tlogoagung, Kedungadem, Bojonegoro', 'daerah_asal' => 'Bojonegoro Timur', 'nama_orang_tua' => 'Nur Fahma', 'nomor_hp_ortu' => '+6281234560007', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS002', 'is_primary' => true],  // Lambatan ← UBAH
                    ['kode' => 'KLS011', 'is_primary' => false],  // SMP 8    ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S008', 'nis' => '510035160089253008', 'nama_lengkap' => 'Azalia Calysta Salsabila', 'jenis_kelamin' => 'Perempuan', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Bulu RT 002 RW 001 Gedangan, Kutorejo, Mojokerto', 'daerah_asal' => 'Mojokerto Barat', 'nama_orang_tua' => 'Calysta Sr.', 'nomor_hp_ortu' => '+6281234560008', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS001', 'is_primary' => true],  // PB       ← UBAH
                    ['kode' => 'KLS006', 'is_primary' => false],  // SD 3     ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S009', 'nis' => '510035160089253009', 'nama_lengkap' => 'Bustomi Firman Amrulloh', 'jenis_kelamin' => 'Laki-laki', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Kuwik RT 001 RW 001 Bareng, Bareng, Jombang', 'daerah_asal' => 'Jombang Selatan', 'nama_orang_tua' => 'Firman', 'nomor_hp_ortu' => '+6281234560009', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS002', 'is_primary' => true],  // Lambatan ← UBAH
                    ['kode' => 'KLS007', 'is_primary' => false],  // SD 4     ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S010', 'nis' => '510035160089253010', 'nama_lengkap' => 'Cresya Nirva Arvenda', 'jenis_kelamin' => 'Perempuan', 'status' => 'Aktif', 'alamat_santri' => 'Kedungmaling II RT 014 RW 006 Kedungmaling, Sooko, Mojokerto', 'daerah_asal' => 'Mojokerto Barat', 'nama_orang_tua' => 'Nirva Sr.', 'nomor_hp_ortu' => '+6281234560010', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS003', 'is_primary' => true],  // Cepatan  ← UBAH
                    ['kode' => 'KLS013', 'is_primary' => false],  // SMA 10   ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S011', 'nis' => '510035160089253011', 'nama_lengkap' => 'Daud Fasal', 'jenis_kelamin' => 'Laki-laki', 'status' => 'Aktif', 'alamat_santri' => 'Plumpang RT 017 RW 004 Penambangan, Balongbendo, Sidoarjo', 'daerah_asal' => 'Sidoarjo Utara', 'nama_orang_tua' => 'Fasal Sr.', 'nomor_hp_ortu' => '+6281234560011', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS001', 'is_primary' => true],  // PB       ← UBAH
                    ['kode' => 'KLS010', 'is_primary' => false],  // SMP 7    ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S012', 'nis' => '510035160089253012', 'nama_lengkap' => 'Dwi Melviana Putri', 'jenis_kelamin' => 'Perempuan', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Panggih RT 003 RW 003 Panggih, Trowulan, Mojokerto', 'daerah_asal' => 'Mojokerto Barat', 'nama_orang_tua' => 'Melviana Sr.', 'nomor_hp_ortu' => '+6281234560012', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS002', 'is_primary' => true],  // Lambatan ← UBAH
                    ['kode' => 'KLS014', 'is_primary' => false],  // SMA 11   ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S013', 'nis' => '510035160089253013', 'nama_lengkap' => 'Fina Yusrina Jannah', 'jenis_kelamin' => 'Perempuan', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Kepuhanyar RT 001 RW 001 Kepuhanyar, Mojoanyar, Mojokerto', 'daerah_asal' => 'Mojokerto Kota', 'nama_orang_tua' => 'Yusrina Sr.', 'nomor_hp_ortu' => '+6281234560013', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS003', 'is_primary' => true],  // Cepatan  ← UBAH
                    ['kode' => 'KLS015', 'is_primary' => false],  // SMA 12   ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S014', 'nis' => '510035160089253014', 'nama_lengkap' => 'Gadis Sholikhah', 'jenis_kelamin' => 'Perempuan', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Brangkal RT 002 RW 001 Brangkal, Sooko, Mojokerto', 'daerah_asal' => 'Mojokerto Barat', 'nama_orang_tua' => 'Sholikhah Sr.', 'nomor_hp_ortu' => '+6281234560014', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS001', 'is_primary' => true],  // PB       ← UBAH
                    ['kode' => 'KLS008', 'is_primary' => false],  // SD 5     ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S015', 'nis' => '510035160089253015', 'nama_lengkap' => 'Gilang Aswin Nahar', 'jenis_kelamin' => 'Laki-laki', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Panggih RT 001 RW 003 Panggih, Trowulan, Mojokerto', 'daerah_asal' => 'Mojokerto Barat', 'nama_orang_tua' => 'Aswin', 'nomor_hp_ortu' => '+6281234560015', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS002', 'is_primary' => true],  // Lambatan ← UBAH
                    ['kode' => 'KLS009', 'is_primary' => false],  // SD 6     ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S016', 'nis' => '510035160089253016', 'nama_lengkap' => 'Gustiyar Abdullah Manshurin', 'jenis_kelamin' => 'Laki-laki', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Setro RT 007 RW 001 Jatirejo, Kasreman, Ngawi', 'daerah_asal' => 'Ngawi Kota', 'nama_orang_tua' => 'Abdullah', 'nomor_hp_ortu' => '+6281234560016', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS001', 'is_primary' => true],  // PB       ← UBAH
                    ['kode' => 'KLS012', 'is_primary' => false],  // SMP 9    ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S017', 'nis' => '510035160089253017', 'nama_lengkap' => 'Ilham Maulana Abdillah', 'jenis_kelamin' => 'Laki-laki', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Tundungan RT 004 RW 002 Sidomojo, Krian, Sidoarjo', 'daerah_asal' => 'Sidoarjo Utara', 'nama_orang_tua' => 'Maulana', 'nomor_hp_ortu' => '+6281234560017', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS003', 'is_primary' => true],  // Cepatan  ← UBAH
                    ['kode' => 'KLS013', 'is_primary' => false],  // SMA 10   ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S018', 'nis' => '510035160089253018', 'nama_lengkap' => 'Kafa Septian Ramdan Efendi', 'jenis_kelamin' => 'Laki-laki', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Brangkal RT 002 RW001 Brangkal, Sooko, Mojokerto', 'daerah_asal' => 'Mojokerto Barat', 'nama_orang_tua' => 'Septian', 'nomor_hp_ortu' => '+6281234560018', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS002', 'is_primary' => true],  // Lambatan ← UBAH
                    ['kode' => 'KLS011', 'is_primary' => false],  // SMP 8    ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S019', 'nis' => '510035160089253019', 'nama_lengkap' => "Khalisa Syifa'ul Aini", 'jenis_kelamin' => 'Perempuan', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Tiebuk RT 007 RW 003 Wiyu, Pacet, Mojokerto', 'daerah_asal' => 'Mojokerto Barat', 'nama_orang_tua' => "Syifa'ul Sr.", 'nomor_hp_ortu' => '+6281234560019', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS001', 'is_primary' => true],  // PB       ← UBAH
                    ['kode' => 'KLS005', 'is_primary' => false],  // SD 2     ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S020', 'nis' => '510035160089253020', 'nama_lengkap' => 'Kharisa Nur Qalbi', 'jenis_kelamin' => 'Perempuan', 'status' => 'Aktif', 'alamat_santri' => 'Pasinan Lemah Putih RT 011 RW 003, Wringinanom, Gresik', 'daerah_asal' => 'Gresik Selatan', 'nama_orang_tua' => 'Nur Qalbi Sr.', 'nomor_hp_ortu' => '+6281234560020', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS003', 'is_primary' => true],  // Cepatan  ← UBAH
                    ['kode' => 'KLS014', 'is_primary' => false],  // SMA 11   ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S021', 'nis' => '510035160089253021', 'nama_lengkap' => 'Lana Novpriyanto', 'jenis_kelamin' => 'Laki-laki', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Parengan RT 019 RW 004 Kraton, Krian, Sidoarjo', 'daerah_asal' => 'Sidoarjo Utara', 'nama_orang_tua' => 'Novpriyanto Sr.', 'nomor_hp_ortu' => '+6281234560021', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS001', 'is_primary' => true],  // PB       ← UBAH
                    ['kode' => 'KLS010', 'is_primary' => false],  // SMP 7    ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S022', 'nis' => '510035160089253022', 'nama_lengkap' => 'M. Reyhan Firdaus', 'jenis_kelamin' => 'Laki-laki', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Jampirogo RT 005 RW 001 Jampirogo, Sooko, Mojokerto', 'daerah_asal' => 'Mojokerto Barat', 'nama_orang_tua' => 'Reyhan Sr.', 'nomor_hp_ortu' => '+6281234560022', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS002', 'is_primary' => true],  // Lambatan ← UBAH
                    ['kode' => 'KLS006', 'is_primary' => false],  // SD 3     ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S023', 'nis' => '510035160089253023', 'nama_lengkap' => 'Masrurotin Fatma Ayu Wulandari', 'jenis_kelamin' => 'Perempuan', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Pakis Kulon RT 003 RW 003 Pakis, Trowulan, Mojokerto', 'daerah_asal' => 'Mojokerto Barat', 'nama_orang_tua' => 'Fatma Sr.', 'nomor_hp_ortu' => '+6281234560023', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS003', 'is_primary' => true],  // Cepatan  ← UBAH
                    ['kode' => 'KLS015', 'is_primary' => false],  // SMA 12   ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S024', 'nis' => '510035160089253024', 'nama_lengkap' => 'Mochammad Adam Madinata', 'jenis_kelamin' => 'Laki-laki', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Kepuhanyar RT 001 RW001 Kepuhanyar, Mojoanyar, Mojokerto', 'daerah_asal' => 'Mojokerto Barat', 'nama_orang_tua' => 'Adam Sr.', 'nomor_hp_ortu' => '+6281234560024', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS001', 'is_primary' => true],  // PB       ← UBAH
                    ['kode' => 'KLS007', 'is_primary' => false],  // SD 4     ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S025', 'nis' => '510035160089253025', 'nama_lengkap' => "Muchammad Fachrizal Ta'awamu Insan", 'jenis_kelamin' => 'Laki-laki', 'status' => 'Aktif', 'alamat_santri' => 'Sidokepung RT 015 RW 003 Buduran, Sidoarjo', 'daerah_asal' => 'Sidoarjo Tengah', 'nama_orang_tua' => 'Fachrizal', 'nomor_hp_ortu' => '+6281234560025', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS002', 'is_primary' => true],  // Lambatan ← UBAH
                    ['kode' => 'KLS011', 'is_primary' => false],  // SMP 8    ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S026', 'nis' => '510035160089253026', 'nama_lengkap' => 'Muhammad Revano Fadillah Ramadhan', 'jenis_kelamin' => 'Laki-laki', 'status' => 'Aktif', 'alamat_santri' => 'Kemlagi Timur RT 001 RW 003 Kemlagi, Kemlagi, Mojokerto', 'daerah_asal' => 'Mojokerto Kota', 'nama_orang_tua' => 'Fadillah', 'nomor_hp_ortu' => '+6281234560026', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS003', 'is_primary' => true],  // Cepatan  ← UBAH
                    ['kode' => 'KLS012', 'is_primary' => false],  // SMP 9    ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S027', 'nis' => '510035160089253027', 'nama_lengkap' => 'Muhammad Ibrahim Try Aji', 'jenis_kelamin' => 'Laki-laki', 'status' => 'Aktif', 'alamat_santri' => 'Jln. Patimura RT 002 RW 002 Keboan, Ngusikan, Jombang', 'daerah_asal' => 'Mojokerto Kota', 'nama_orang_tua' => 'Ibrahim', 'nomor_hp_ortu' => '+6281234560027', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS001', 'is_primary' => true],  // PB       ← UBAH
                    ['kode' => 'KLS013', 'is_primary' => false],  // SMA 10   ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S028', 'nis' => '510035160089253028', 'nama_lengkap' => 'Mutiara Dira Ardiana', 'jenis_kelamin' => 'Perempuan', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Tiebuk RT 008 RW 001 Wiyu, Pacet, Mojokerto', 'daerah_asal' => 'Mojokerto Barat', 'nama_orang_tua' => 'Dira Sr.', 'nomor_hp_ortu' => '+6281234560028', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS002', 'is_primary' => true],  // Lambatan ← UBAH
                    ['kode' => 'KLS008', 'is_primary' => false],  // SD 5     ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S029', 'nis' => '510035160089253029', 'nama_lengkap' => 'Nerissa Arviana Maharani', 'jenis_kelamin' => 'Perempuan', 'status' => 'Aktif', 'alamat_santri' => 'Ngangkrik Kidul RT 001 RW 003 Gebangangkrik, Ngimbang, Lamongan', 'daerah_asal' => 'Kambangan', 'nama_orang_tua' => 'Arviana Sr.', 'nomor_hp_ortu' => '+6281234560029', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS003', 'is_primary' => true],  // Cepatan  ← UBAH
                    ['kode' => 'KLS009', 'is_primary' => false],  // SD 6     ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S030', 'nis' => '510035160089253030', 'nama_lengkap' => 'Prisca Zuzin Firdaus', 'jenis_kelamin' => 'Perempuan', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Ngrayung RT 004 RW 001 Brayung, Puri, Mojokerto', 'daerah_asal' => 'Mojokerto Barat', 'nama_orang_tua' => 'Zuzin Sr.', 'nomor_hp_ortu' => '+6281234560030', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS001', 'is_primary' => true],  // PB       ← UBAH
                    ['kode' => 'KLS004', 'is_primary' => false],  // SD 1     ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S031', 'nis' => '510035160089253031', 'nama_lengkap' => 'Shoffiya Fitriani Az Zahra', 'jenis_kelamin' => 'Perempuan', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Dadi RT 027 RW 014 Dadi, Plaosan, Magetan', 'daerah_asal' => 'Magetan', 'nama_orang_tua' => 'Fitriani Sr.', 'nomor_hp_ortu' => '+6281234560031', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS002', 'is_primary' => true],  // Lambatan ← UBAH
                    ['kode' => 'KLS005', 'is_primary' => false],  // SD 2     ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S032', 'nis' => '510035160089253032', 'nama_lengkap' => 'Syifa Putri Ramahdani', 'jenis_kelamin' => 'Perempuan', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Sumbertempur Perum Puri Kencana RT 003 RW003 Sumbergirang, Puri, Mojokerto', 'daerah_asal' => 'Mojokerto Barat', 'nama_orang_tua' => 'Putri Sr.', 'nomor_hp_ortu' => '+6281234560032', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS003', 'is_primary' => true],  // Cepatan  ← UBAH
                    ['kode' => 'KLS006', 'is_primary' => false],  // SD 3     ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S033', 'nis' => '510035160089253033', 'nama_lengkap' => 'Tiara Rahmadhani Faradilah', 'jenis_kelamin' => 'Perempuan', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Bulurejo RT 001 RW 002 Kepuhkajang, Perak, Jombang', 'daerah_asal' => 'Mojokerto Kota', 'nama_orang_tua' => 'Rahmadhani Sr.', 'nomor_hp_ortu' => '+6281234560033', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS001', 'is_primary' => true],  // PB       ← UBAH
                    ['kode' => 'KLS014', 'is_primary' => false],  // SMA 11   ← UBAH
                ],
            ],
            [
                'santri' => ['id_santri' => 'S034', 'nis' => '510035160089253034', 'nama_lengkap' => 'Virlye Andyra Zahra', 'jenis_kelamin' => 'Perempuan', 'status' => 'Aktif', 'alamat_santri' => 'Dsn. Randuwates RT 005 RW 003 Mojowatesrejo, Kemlagi, Mojokerto', 'daerah_asal' => 'Mojokerto Kota', 'nama_orang_tua' => 'Andyra Sr.', 'nomor_hp_ortu' => '+6281234560034', 'rfid_uid' => null, 'foto' => null, 'created_at' => $now, 'updated_at' => $now],
                'kelas' => [
                    ['kode' => 'KLS002', 'is_primary' => true],  // Lambatan ← UBAH
                    ['kode' => 'KLS015', 'is_primary' => false],  // SMA 12   ← UBAH
                ],
            ],
        ];

        // ============================================================
        // PROSES INSERT — jangan ubah bagian ini
        // ============================================================

        // Ambil mapping kode_kelas -> id dari DB
        $kelasMap = DB::table('kelas')->pluck('id', 'kode_kelas')->toArray();

        $totalSantri = 0;
        $totalKelas  = 0;
        $errors      = [];

        foreach ($santriList as $item) {
            DB::table('santris')->insert($item['santri']);
            $totalSantri++;

            $idSantri = $item['santri']['id_santri'];

            foreach ($item['kelas'] as $kelasItem) {
                $kode = $kelasItem['kode'];

                if (!isset($kelasMap[$kode])) {
                    $errors[] = "⚠️  Kelas [{$kode}] tidak ada di DB! Santri {$idSantri} dilewati untuk kelas ini.";
                    continue;
                }

                DB::table('santri_kelas')->insert([
                    'id_santri'    => $idSantri,
                    'id_kelas'     => $kelasMap[$kode],
                    'tahun_ajaran' => $tahunAjaran,
                    'is_primary'   => $kelasItem['is_primary'],
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
                $totalKelas++;
            }
        }

        $this->command->info("✅ SantriSeeder selesai!");
        $this->command->info("   → {$totalSantri} santri ditambahkan (S003–S034)");
        $this->command->info("   → {$totalKelas} record santri_kelas ditambahkan");
        $this->command->info("   → Tahun ajaran: {$tahunAjaran}");
        $this->command->info("   → S001 & S002 tidak tersentuh ✓");

        if (!empty($errors)) {
            $this->command->warn("\nAda masalah:");
            foreach ($errors as $err) {
                $this->command->warn("   " . $err);
            }
        }
    }
}