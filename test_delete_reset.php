<!DOCTYPE html>
<html>
<head>
    <title>Test Delete & Reset Wali Account</title>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial; padding: 20px; }
        .form-group { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; }
        .delete { background: #dc3545; color: white; }
        .reset { background: #ffc107; color: black; }
        .info { background: #17a2b8; color: white; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Test Delete & Reset Akun Wali</h1>
    
    <div class="info">
        <strong>ID Akun Wali yang tersedia:</strong><br>
        ID 6: Aydin Fauzan (S002)<br>
        ID 7: HELGA FAISA_1 (S001)<br>
        ID 9: Leni Yulia (S004)<br>
        ID 10: Mifta Okta Yanti (S003)
    </div>

    <div class="form-group">
        <h3>Test Delete Akun (User ID: 9 - Leni Yulia)</h3>
        <form action="http://localhost/TugasAkhir/sim-pkpps/public/admin/users/wali/9/delete" method="POST">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <button type="submit" class="delete" onclick="return confirm('Yakin hapus?')">
                🗑️ Hapus Akun ID 9
            </button>
        </form>
    </div>

    <div class="form-group">
        <h3>Test Reset Password (User ID: 10 - Mifta Okta Yanti)</h3>
        <form action="http://localhost/TugasAkhir/sim-pkpps/public/admin/users/wali/10/reset-password" method="POST">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <button type="submit" class="reset" onclick="return confirm('Reset password?')">
                🔑 Reset Password ID 10
            </button>
        </form>
    </div>

    <div class="form-group">
        <h3>Kembali ke Admin Panel</h3>
        <a href="http://localhost/TugasAkhir/sim-pkpps/public/admin/users/wali">
            <button>📋 Lihat Daftar Akun Wali</button>
        </a>
    </div>

    <hr>
    <h3>Cara Pakai:</h3>
    <ol>
        <li>Login dulu ke admin panel di tab lain</li>
        <li>Kembali ke halaman ini</li>
        <li>Klik tombol Delete atau Reset</li>
        <li>Cek hasilnya di admin panel</li>
    </ol>

    <script>
        // Auto-generate CSRF token dari cookie atau meta tag
        const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
        document.querySelectorAll('input[name="_token"]').forEach(input => {
            if (!input.value) {
                // Jika token kosong, user harus login dulu
                const forms = document.querySelectorAll('form');
                forms.forEach(form => {
                    form.addEventListener('submit', function(e) {
                        alert('⚠️ Login dulu ke admin panel, baru refresh halaman ini!');
                        e.preventDefault();
                    });
                });
            }
        });
    </script>
</body>
</html>
