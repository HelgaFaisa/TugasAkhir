<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

class KartuRfidController extends Controller
{
    public function index(Request $request)
    {
        $query = Santri::where('status', 'Aktif');

        if ($request->filled('filter')) {
            if ($request->filter == 'ada_rfid') {
                $query->whereNotNull('rfid_uid');
            } elseif ($request->filter == 'belum_rfid') {
                $query->whereNull('rfid_uid');
            }
        }

        $santris = $query
            ->select('id', 'id_santri', 'nis', 'nama_lengkap', 'rfid_uid', 'foto', 'status')
            ->with(['kelasSantri.kelas'])
            ->orderBy('nama_lengkap')
            ->paginate(15);

        return view('admin.kegiatan.kartu.index', compact('santris'));
    }

    public function daftarRfid($id_santri)
    {
        $santri = Santri::where('id_santri', $id_santri)->firstOrFail();
        return view('admin.kegiatan.kartu.daftar', compact('santri'));
    }

    public function simpanRfid(Request $request, $id_santri)
    {
        $request->validate([
            'rfid_uid' => 'required|string|max:50|unique:santris,rfid_uid',
        ], [
            'rfid_uid.required' => 'UID RFID wajib diisi.',
            'rfid_uid.unique'   => 'UID RFID ini sudah terdaftar pada santri lain.',
        ]);

        $santri = Santri::where('id_santri', $id_santri)->firstOrFail();
        $santri->update(['rfid_uid' => $request->rfid_uid]);

        return redirect()->route('admin.kartu-rfid.index')
            ->with('success', 'RFID berhasil didaftarkan untuk ' . $santri->nama_lengkap);
    }

    public function hapusRfid($id_santri)
    {
        $santri = Santri::where('id_santri', $id_santri)->firstOrFail();
        $santri->update(['rfid_uid' => null]);

        return redirect()->route('admin.kartu-rfid.index')
            ->with('success', 'RFID berhasil dihapus dari ' . $santri->nama_lengkap);
    }

    public function cetakKartu($id_santri)
    {
        $santri = Santri::where('id_santri', $id_santri)
            ->with([
                'kelasPrimary.kelas',
                'kelasSantri' => fn($q) => $q->orderByDesc('is_primary')->orderBy('id'),
                'kelasSantri.kelas',
            ])
            ->firstOrFail();

        if (!$santri->rfid_uid) {
            return back()->with('error', 'Santri belum memiliki RFID yang terdaftar.');
        }

        // ── Siapkan data untuk view ──────────────────────────────────────
        $namaSantri = strtoupper($santri->nama_lengkap ?? 'NAMA SANTRI');
        $initial    = strtoupper(substr($santri->nama_lengkap ?? 'S', 0, 1));
        $nis        = !empty($santri->nis) ? $santri->nis : '-';
        $uid        = !empty($santri->rfid_uid) ? $santri->rfid_uid : '-';

        // Kelas: pakai kelasPrimary, fallback ke first kelasSantri
        $kelasNama = '-';
        if ($santri->kelasPrimary && $santri->kelasPrimary->kelas) {
            $kelasNama = strtoupper($santri->kelasPrimary->kelas->nama_kelas);
        } elseif ($santri->kelasSantri->first() && $santri->kelasSantri->first()->kelas) {
            $kelasNama = strtoupper($santri->kelasSantri->first()->kelas->nama_kelas);
        }

        // Logo — embed base64 (tidak butuh GD)
        $logoBase64 = '';
        $logoMime   = 'image/png';
        foreach ([
            public_path('images/logo.png'),
            public_path('images/logo.jpg'),
            public_path('img/logo.png'),
            public_path('logo.png'),
        ] as $lp) {
            if (file_exists($lp)) {
                $ext        = strtolower(pathinfo($lp, PATHINFO_EXTENSION));
                $logoMime   = $ext === 'png' ? 'image/png' : 'image/jpeg';
                $logoBase64 = base64_encode(file_get_contents($lp));
                break;
            }
        }

        // Foto santri — embed base64 (tidak butuh GD)
        $fotoBase64 = '';
        $fotoMime   = 'image/jpeg';
        if (!empty($santri->foto)) {
            foreach ([
                storage_path('app/public/' . $santri->foto),
                public_path('storage/' . $santri->foto),
                public_path($santri->foto),
            ] as $fp) {
                if (file_exists($fp)) {
                    $ext        = strtolower(pathinfo($fp, PATHINFO_EXTENSION));
                    $fotoMime   = in_array($ext, ['png', 'gif', 'webp']) ? 'image/' . $ext : 'image/jpeg';
                    $fotoBase64 = base64_encode(file_get_contents($fp));
                    break;
                }
            }
        }

        // ── Render HTML dari blade ────────────────────────────────────────
        $html = view('admin.kegiatan.kartu.cetak', compact(
            'santri',
            'namaSantri', 'initial', 'nis', 'uid', 'kelasNama',
            'logoBase64', 'logoMime',
            'fotoBase64', 'fotoMime'
        ))->render();

        // ── Inisialisasi mPDF ─────────────────────────────────────────────
        // Format: 54mm × 85.6mm (ukuran kartu ID standar)
        $mpdf = new Mpdf([
            'mode'              => 'utf-8',
            'format'            => [54, 85.6],
            'orientation'       => 'P',
            'margin_top'        => 0,
            'margin_bottom'     => 0,
            'margin_left'       => 0,
            'margin_right'      => 0,
            'margin_header'     => 0,
            'margin_footer'     => 0,
            'default_font'      => 'dejavusans',
            'tempDir'           => storage_path('app/mpdf_tmp'),
            'autoScriptToLang'  => false,
            'autoLangToFont'    => false,
            // Aktifkan dukungan SVG (untuk foto bulat)
            'enableImports'     => true,
        ]);

        // Matikan page break otomatis
        $mpdf->SetAutoPageBreak(false);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('Kartu_RFID_' . $santri->id_santri . '.pdf', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="Kartu_RFID_' . $santri->id_santri . '.pdf"');
    }
}