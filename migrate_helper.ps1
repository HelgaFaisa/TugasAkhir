# Migration Helper Script for Windows PowerShell
# Usage: .\migrate_helper.ps1 [action]

param(
    [string]$action = "help"
)

$simdDir = "sim-pkpps"

function Show-Header {
    Write-Host "╔══════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║        SIM Pondok Pesantren - Kelas Migration       ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
}

function Show-Help {
    Show-Header
    Write-Host "Available actions:" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "  install       - Run migrations and seeders" -ForegroundColor Green
    Write-Host "  migrate-test  - Test data migration (dry-run)" -ForegroundColor Green
    Write-Host "  migrate       - Run actual data migration" -ForegroundColor Green
    Write-Host "  scan          - Scan codebase for kelas usage" -ForegroundColor Green
    Write-Host "  report        - Open refactoring report" -ForegroundColor Green
    Write-Host "  verify        - Verify migration status" -ForegroundColor Green
    Write-Host "  help          - Show this help message" -ForegroundColor Green
    Write-Host ""
    Write-Host "Examples:" -ForegroundColor Yellow
    Write-Host "  .\migrate_helper.ps1 install" -ForegroundColor Gray
    Write-Host "  .\migrate_helper.ps1 migrate-test" -ForegroundColor Gray
    Write-Host "  .\migrate_helper.ps1 scan" -ForegroundColor Gray
    Write-Host ""
}

function Run-Install {
    Show-Header
    Write-Host "📦 Installing new kelas system..." -ForegroundColor Yellow
    Write-Host ""
    
    Write-Host "Step 1: Running migrations..." -ForegroundColor Cyan
    Set-Location $simdDir
    php artisan migrate
    Write-Host "✓ Migrations completed" -ForegroundColor Green
    Write-Host ""
    
    Write-Host "Step 2: Seeding kelompok kelas..." -ForegroundColor Cyan
    php artisan db:seed --class=KelompokKelasSeeder
    Write-Host "✓ Kelompok kelas seeded" -ForegroundColor Green
    Write-Host ""
    
    Write-Host "Step 3: Seeding kelas..." -ForegroundColor Cyan
    php artisan db:seed --class=KelasSeeder
    Write-Host "✓ Kelas seeded" -ForegroundColor Green
    Write-Host ""
    
    Set-Location ..
    
    Write-Host "✓ Installation completed!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Next steps:" -ForegroundColor Yellow
    Write-Host "  1. Run: .\migrate_helper.ps1 migrate-test" -ForegroundColor Gray
    Write-Host "  2. If OK, run: .\migrate_helper.ps1 migrate" -ForegroundColor Gray
    Write-Host ""
}

function Run-MigrateTest {
    Show-Header
    Write-Host "🔍 Testing data migration (dry-run)..." -ForegroundColor Yellow
    Write-Host ""
    
    Set-Location $simdDir
    php artisan migrate:santri-kelas --dry-run
    Set-Location ..
    
    Write-Host ""
    Write-Host "If everything looks good:" -ForegroundColor Yellow
    Write-Host "  Run: .\migrate_helper.ps1 migrate" -ForegroundColor Gray
    Write-Host ""
}

function Run-Migrate {
    Show-Header
    Write-Host "⚠️  This will migrate santri kelas data to new system" -ForegroundColor Yellow
    Write-Host ""
    
    $confirm = Read-Host "Are you sure? (yes/no)"
    
    if ($confirm -eq "yes") {
        Write-Host ""
        Write-Host "🚀 Running migration..." -ForegroundColor Cyan
        Set-Location $simdDir
        php artisan migrate:santri-kelas
        Set-Location ..
        
        Write-Host ""
        Write-Host "✓ Migration completed!" -ForegroundColor Green
        Write-Host ""
        Write-Host "Next steps:" -ForegroundColor Yellow
        Write-Host "  1. Run: .\migrate_helper.ps1 verify" -ForegroundColor Gray
        Write-Host "  2. Run: .\migrate_helper.ps1 scan" -ForegroundColor Gray
        Write-Host ""
    } else {
        Write-Host "Migration cancelled" -ForegroundColor Yellow
    }
}

function Run-Scan {
    Show-Header
    Write-Host "🔍 Scanning codebase for kelas usage..." -ForegroundColor Yellow
    Write-Host ""
    
    php scan_kelas_usage.php
    
    Write-Host ""
    Write-Host "✓ Scan completed!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Review the reports:" -ForegroundColor Yellow
    Write-Host "  - KELAS_USAGE_MAP.md (detailed map)" -ForegroundColor Gray
    Write-Host "  - REFACTORING_GUIDE.md (quick reference)" -ForegroundColor Gray
    Write-Host ""
    Write-Host "Open report? (yes/no)" -ForegroundColor Yellow
    $openReport = Read-Host
    
    if ($openReport -eq "yes") {
        code KELAS_USAGE_MAP.md
    }
}

function Run-Verify {
    Show-Header
    Write-Host "📊 Verifying migration status..." -ForegroundColor Yellow
    Write-Host ""
    
    Set-Location $simdDir
    
    Write-Host "Migration status:" -ForegroundColor Cyan
    php artisan migrate:status | Select-String "kelompok_kelas|kelas|santri_kelas|kegiatan_kelas"
    Write-Host ""
    
    Write-Host "Checking data counts:" -ForegroundColor Cyan
    php artisan tinker --execute="echo 'Kelompok Kelas: ' . App\Models\KelompokKelas::count() . PHP_EOL;"
    php artisan tinker --execute="echo 'Kelas: ' . App\Models\Kelas::count() . PHP_EOL;"
    php artisan tinker --execute="echo 'Santri Kelas: ' . App\Models\SantriKelas::count() . PHP_EOL;"
    php artisan tinker --execute="echo 'Santri with old kelas: ' . App\Models\Santri::whereNotNull('kelas')->count() . PHP_EOL;"
    Write-Host ""
    
    Set-Location ..
    
    Write-Host "✓ Verification completed!" -ForegroundColor Green
    Write-Host ""
}

function Open-Report {
    Show-Header
    Write-Host "📖 Opening refactoring reports..." -ForegroundColor Yellow
    Write-Host ""
    
    if (Test-Path "KELAS_USAGE_MAP.md") {
        code KELAS_USAGE_MAP.md
        Write-Host "✓ Opened KELAS_USAGE_MAP.md" -ForegroundColor Green
    } else {
        Write-Host "⚠️  KELAS_USAGE_MAP.md not found. Run scan first." -ForegroundColor Yellow
    }
    
    if (Test-Path "REFACTORING_GUIDE.md") {
        code REFACTORING_GUIDE.md
        Write-Host "✓ Opened REFACTORING_GUIDE.md" -ForegroundColor Green
    }
    
    Write-Host ""
}

# Main script execution
switch ($action.ToLower()) {
    "install" {
        Run-Install
    }
    "migrate-test" {
        Run-MigrateTest
    }
    "migrate" {
        Run-Migrate
    }
    "scan" {
        Run-Scan
    }
    "verify" {
        Run-Verify
    }
    "report" {
        Open-Report
    }
    "help" {
        Show-Help
    }
    default {
        Write-Host "Unknown action: $action" -ForegroundColor Red
        Write-Host ""
        Show-Help
    }
}
