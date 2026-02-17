-- ============================================
-- SAMPLE DATA BERITA - 3 KATEGORI
-- ============================================

-- KATEGORI 1: BERITA UNTUK SEMUA SANTRI
-- ======================================
INSERT INTO berita (id_berita, judul, konten, penulis, status, target_berita, created_at, updated_at)
VALUES 
('B001', 'Pengumuman Libur Pondok', 
 'Assalamualaikum wr. wb. Diberitahukan kepada seluruh santri bahwa pondok akan libur pada tanggal 10-15 Februari 2026 dalam rangka peringatan Maulid Nabi Muhammad SAW. Mohon untuk kembali ke pondok pada tanggal 16 Februari 2026 pukul 07:00 pagi. Jazakumullah khairan.', 
 'Admin Pondok', 'published', 'semua', NOW(), NOW()),

('B002', 'Jadwal Ujian Semester Genap', 
 'Kepada seluruh santri, jadwal ujian semester genap akan dimulai tanggal 20 Februari 2026. Harap mempersiapkan diri dengan baik. Jadwal lengkap akan diumumkan kemudian. Semoga Allah memudahkan.', 
 'Bagian Pendidikan', 'published', 'semua', NOW(), NOW()),

('B003', 'Pengumuman Kegiatan Haul Akbar', 
 'Bismillah. Dalam rangka memperingati Haul Kyai Pendiri Pondok yang ke-50, akan diadakan kegiatan haul akbar pada tanggal 25 Februari 2026. Seluruh santri diwajibkan mengikuti acara. Mohon kehadiran dan partisipasinya.', 
 'Pengurus Pondok', 'published', 'semua', NOW(), NOW());


-- KATEGORI 2: BERITA UNTUK KELAS TERTENTU
-- ========================================

-- Berita untuk Kelas PB saja
INSERT INTO berita (id_berita, judul, konten, penulis, status, target_berita, target_kelas, created_at, updated_at)
VALUES 
('B004', 'Jadwal Tambahan Kelas PB', 
 'Kepada santri kelas PB, mulai minggu depan akan ada kelas tambahan setiap hari Kamis jam 15:00-16:30 untuk pendalaman materi Nahwu. Mohon kehadirannya tepat waktu. Barakallah.', 
 'Ustadz Nahwu', 'published', 'kelas_tertentu', '["PB"]', NOW(), NOW());

-- Berita untuk Kelas Lambatan saja
INSERT INTO berita (id_berita, judul, konten, penulis, status, target_berita, target_kelas, created_at, updated_at)
VALUES 
('B005', 'Ujian Kenaikan Kelas Lambatan', 
 'Santri kelas Lambatan akan mengikuti ujian kenaikan tingkat pada tanggal 5 Maret 2026. Materi ujian meliputi Nahwu, Shorof, Fiqih, dan Tajwid. Harap mempersiapkan diri dengan sungguh-sungguh. Semoga sukses!', 
 'Bagian Pendidikan', 'published', 'kelas_tertentu', '["Lambatan"]', NOW(), NOW());

-- Berita untuk Kelas Cepatan saja
INSERT INTO berita (id_berita, judul, konten, penulis, status, target_berita, target_kelas, created_at, updated_at)
VALUES 
('B006', 'Kegiatan Muhadhoroh Kelas Cepatan', 
 'Kepada santri kelas Cepatan, akan diadakan kegiatan muhadhoroh (latihan pidato) setiap hari Jumat malam. Setiap santri akan mendapat giliran. Jadwal akan dibagikan minggu depan. Siapkan materi pidato dengan baik.', 
 'Ustadz Pembimbing', 'published', 'kelas_tertentu', '["Cepatan"]', NOW(), NOW());

-- Berita untuk PB dan Lambatan (2 kelas sekaligus)
INSERT INTO berita (id_berita, judul, konten, penulis, status, target_berita, target_kelas, created_at, updated_at)
VALUES 
('B007', 'Info Kegiatan Ekstrakurikuler', 
 'Kepada santri kelas PB dan Lambatan, dibuka pendaftaran ekstrakurikuler Tahfidz dan Kaligrafi. Pendaftaran dilakukan di kantor pondok mulai Senin-Rabu jam 16:00-17:00. Kuota terbatas, siapa cepat dia dapat!', 
 'Bagian Kegiatan', 'published', 'kelas_tertentu', '["PB", "Lambatan"]', NOW(), NOW());


-- KATEGORI 3: BERITA UNTUK SANTRI TERTENTU
-- =========================================

-- Berita pribadi untuk santri tertentu
INSERT INTO berita (id_berita, judul, konten, penulis, status, target_berita, created_at, updated_at)
VALUES 
('B008', 'Pesan Khusus - Harap Menemui Admin', 
 'Assalamualaikum. Saudara diminta untuk menemui bagian administrasi pondok pada hari Senin jam 10:00 untuk pengecekan data dan pemberkasan. Mohon datang tepat waktu. Terima kasih.', 
 'Admin', 'published', 'santri_tertentu', NOW(), NOW()),

('B009', 'Reminder Pembayaran Uang Saku', 
 'Assalamualaikum. Kami informasikan bahwa saldo uang saku Anda sudah menipis (di bawah Rp 50.000). Harap segera melakukan top up agar tidak terkendala dalam pembelian kebutuhan sehari-hari. Barakallah.', 
 'Bagian Keuangan', 'published', 'santri_tertentu', NOW(), NOW()),

('B010', 'Undangan Pertemuan Wali', 
 'Kepada Bapak/Ibu Wali Santri, kami mengundang untuk hadir dalam pertemuan membahas perkembangan santri pada hari Sabtu, 22 Februari 2026 pukul 09:00 di aula pondok. Kehadiran sangat kami harapkan. Jazakumullah khairan.', 
 'Pengurus Pondok', 'published', 'santri_tertentu', NOW(), NOW());


-- ============================================
-- PIVOT TABLE untuk SANTRI TERTENTU (B008, B009, B010)
-- ============================================
-- 
-- CATATAN: Sesuaikan id_santri dengan data di database Anda!
-- 
-- Contoh: Jika di database ada santri dengan id_santri 'S001', 'S002', 'S003'
-- maka insert seperti ini:

-- Berita B008 untuk S001 dan S002
INSERT INTO berita_santri (id_berita, id_santri, sudah_dibaca, created_at, updated_at)
VALUES 
('B008', 'S001', FALSE, NOW(), NOW()),
('B008', 'S002', FALSE, NOW(), NOW());

-- Berita B009 untuk S001 saja
INSERT INTO berita_santri (id_berita, id_santri, sudah_dibaca, created_at, updated_at)
VALUES 
('B009', 'S001', FALSE, NOW(), NOW());

-- Berita B010 untuk S001, S002, dan S003
INSERT INTO berita_santri (id_berita, id_santri, sudah_dibaca, created_at, updated_at)
VALUES 
('B010', 'S001', FALSE, NOW(), NOW()),
('B010', 'S002', FALSE, NOW(), NOW()),
('B010', 'S003', FALSE, NOW(), NOW());


-- ============================================
-- CARA CEK ID SANTRI YANG ADA
-- ============================================
-- Jalankan query ini dulu untuk lihat id_santri yang tersedia:
-- 
-- SELECT id_santri, nama_lengkap, kelas, status 
-- FROM santris 
-- WHERE status = 'Aktif' 
-- ORDER BY nama_lengkap;
--
-- Kemudian sesuaikan INSERT berita_santri di atas dengan id_santri yang sesuai


-- ============================================
-- HASIL YANG DIHARAPKAN
-- ============================================
-- 
-- 1. Berita B001-B003 (target: semua)
--    → Muncul untuk SEMUA santri yang login
--
-- 2. Berita B004 (target: kelas PB)
--    → Hanya muncul untuk santri kelas PB
--
-- 3. Berita B005 (target: kelas Lambatan)
--    → Hanya muncul untuk santri kelas Lambatan
--
-- 4. Berita B006 (target: kelas Cepatan)
--    → Hanya muncul untuk santri kelas Cepatan
--
-- 5. Berita B007 (target: kelas PB dan Lambatan)
--    → Muncul untuk santri kelas PB DAN Lambatan
--
-- 6. Berita B008-B010 (target: santri tertentu)
--    → Hanya muncul untuk santri yang ada di pivot table berita_santri
--    → Akan ada badge "BARU" sampai mereka buka beritanya
