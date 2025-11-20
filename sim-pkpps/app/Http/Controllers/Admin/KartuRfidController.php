<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class KartuRfidController extends Controller
{
    /**
     * Halaman kelola kartu RFID
     */
    public function index(Request $request)
    {
        $query = Santri::where('status', 'Aktif');

        // Filter: Santri yang sudah/belum punya RFID
        if ($request->filled('filter')) {
            if ($request->filter == 'ada_rfid') {
                $query->whereNotNull('rfid_uid');
            } elseif ($request->filter == 'belum_rfid') {
                $query->whereNull('rfid_uid');
            }
        }

        $santris = $query->select('id', 'id_santri', 'nama_lengkap', 'kelas', 'rfid_uid')
            ->orderBy('nama_lengkap')
            ->paginate(15);

        return view('admin.kegiatan.kartu.index', compact('santris'));
    }

    /**
     * Form daftarkan RFID ke santri
     */
    public function daftarRfid($id_santri)
    {
        $santri = Santri::where('id_santri', $id_santri)->firstOrFail();
        return view('admin.kegiatan.kartu.daftar', compact('santri'));
    }

    /**
     * Simpan RFID UID ke santri
     */
    public function simpanRfid(Request $request, $id_santri)
    {
        $validated = $request->validate([
            'rfid_uid' => 'required|string|max:50|unique:santris,rfid_uid',
        ], [
            'rfid_uid.required' => 'UID RFID wajib diisi.',
            'rfid_uid.unique' => 'UID RFID ini sudah terdaftar pada santri lain.',
        ]);

        $santri = Santri::where('id_santri', $id_santri)->firstOrFail();
        $santri->update(['rfid_uid' => $request->rfid_uid]);

        return redirect()->route('admin.kartu-rfid.index')
            ->with('success', 'RFID berhasil didaftarkan untuk ' . $santri->nama_lengkap);
    }

    /**
     * Hapus RFID dari santri
     */
    public function hapusRfid($id_santri)
    {
        $santri = Santri::where('id_santri', $id_santri)->firstOrFail();
        $santri->update(['rfid_uid' => null]);

        return redirect()->route('admin.kartu-rfid.index')
            ->with('success', 'RFID berhasil dihapus dari ' . $santri->nama_lengkap);
    }

    /**
     * Cetak kartu RFID santri (PDF)
     */
    public function cetakKartu($id_santri)
    {
        $santri = Santri::where('id_santri', $id_santri)->firstOrFail();

        if (!$santri->rfid_uid) {
            return back()->with('error', 'Santri belum memiliki RFID yang terdaftar.');
        }

        $pdf = Pdf::loadView('admin.kegiatan.kartu.cetak', compact('santri'));
        $pdf->setPaper([0, 0, 243, 153], 'landscape'); // Ukuran kartu ID (85.6mm x 54mm)

        return $pdf->stream('Kartu_RFID_' . $santri->id_santri . '.pdf');
    }
}