@echo off
echo ============================================
echo   SIM-PKPPS Login Test Script
echo ============================================
echo.

echo [1/4] Checking Laravel server...
curl -s http://localhost:8000 > nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Laravel server NOT running!
    echo    Please run: cd sim-pkpps ^&^& php artisan serve
    pause
    exit /b 1
)
echo ✅ Laravel server is running

echo.
echo [2/4] Testing API health...
curl -s http://localhost:8000/api/v1/login > nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ API endpoint not accessible
    pause
    exit /b 1
)
echo ✅ API endpoint accessible

echo.
echo [3/4] Checking database connection...
cd sim-pkpps
php artisan tinker --execute="echo 'DB Connected: ' . (DB::connection()->getPdo() ? 'Yes' : 'No');"
if %errorlevel% neq 0 (
    echo ❌ Database connection failed
    echo    Check .env file configuration
    pause
    exit /b 1
)
echo ✅ Database connected

echo.
echo [4/4] Listing wali accounts...
php artisan tinker --execute="$users = App\Models\User::where('role', 'wali')->with('santri')->get(); foreach($users as $u) { echo 'Username: ' . $u->username . ' | Santri: ' . ($u->santri ? $u->santri->nama_lengkap : 'N/A') . ' | NIS: ' . ($u->santri ? $u->santri->nis : 'N/A') . PHP_EOL; }"

echo.
echo ============================================
echo   All checks passed! ✅
echo ============================================
echo.
echo Now you can test login with:
echo   - Username: [nama_lengkap_santri]
echo   - Password: [nis_santri]
echo.
pause
