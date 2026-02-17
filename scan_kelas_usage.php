<?php
/**
 * Scan Kelas Usage Script
 * 
 * Script untuk scan semua penggunaan $santri->kelas di codebase
 * dan generate laporan markdown untuk refactoring guidance
 * 
 * Usage:
 *   php scan_kelas_usage.php
 * 
 * Output:
 *   KELAS_USAGE_MAP.md
 */

// Configuration
$baseDir = __DIR__ . '/sim-pkpps';
$outputFile = __DIR__ . '/KELAS_USAGE_MAP.md';

// Check if base directory exists
if (!is_dir($baseDir)) {
    echo "❌ Error: Base directory not found: {$baseDir}\n";
    echo "Current directory: " . __DIR__ . "\n";
    exit(1);
}

// Directories to scan
$scanDirs = [
    'app/Http/Controllers',
    'app/Models',
    'resources/views',
    'database/migrations',
    'database/seeders',
    'routes',
];

// Patterns to search (regex)
$patterns = [
    'property_access' => '/\$santri\s*->\s*kelas(?!\w)/',
    'array_access' => '/\$santri\[[\'"]kelas[\'"]\]/',
    'blade_kelas' => '/\{\{\s*\$santri\s*->\s*kelas\s*\}\}/',
    'where_kelas' => '/->where\([\'"]kelas[\'"]\s*,/',
    'wherein_kelas' => '/->whereIn\([\'"]kelas[\'"]\s*,/',
    'select_kelas' => '/SELECT.*santris\.kelas/i',
    'enum_values' => '/(\'PB\'|\'Lambatan\'|\'Cepatan\')\s*(,|\]|\))/i',
    'kelas_column' => '/[\'"]kelas[\'"]\s*=>/i',
];

echo "╔══════════════════════════════════════════════════════╗\n";
echo "║        Scanning Santri.kelas Usage in Codebase      ║\n";
echo "╚══════════════════════════════════════════════════════╝\n\n";

// Initialize results
$results = [];
$totalFiles = 0;
$totalMatches = 0;

// Scan each directory
foreach ($scanDirs as $dir) {
    $fullPath = $baseDir . '/' . $dir;
    
    if (!is_dir($fullPath)) {
        echo "⚠️  Directory not found: {$dir}\n";
        continue;
    }
    
    echo "📁 Scanning: {$dir}\n";
    
    $files = scanDirectory($fullPath, $dir);
    
    foreach ($files as $file) {
        $matches = scanFile($file['full_path'], $patterns);
        
        if (!empty($matches)) {
            $totalFiles++;
            $totalMatches += count($matches);
            
            $results[$dir][] = [
                'file' => $file['relative_path'],
                'full_path' => $file['full_path'],
                'matches' => $matches,
            ];
            
            echo "  ✓ Found " . count($matches) . " match(es) in: " . basename($file['relative_path']) . "\n";
        }
    }
}

echo "\n";
echo "Summary:\n";
echo "  📊 Files scanned: " . countAllFiles($scanDirs, $baseDir) . "\n";
echo "  ✓ Files with matches: {$totalFiles}\n";
echo "  🔍 Total matches: {$totalMatches}\n";
echo "\n";

// Generate markdown report
echo "📝 Generating report: KELAS_USAGE_MAP.md\n";
generateMarkdownReport($results, $outputFile);

echo "✓ Report generated successfully!\n";
echo "\nNext steps:\n";
echo "  1. Review KELAS_USAGE_MAP.md\n";
echo "  2. Prioritize refactoring (HIGH -> MEDIUM -> LOW)\n";
echo "  3. Test each change thoroughly\n";
echo "  4. Use \$santri->kelas_name for backward compatibility\n\n";

// ============================================
// HELPER FUNCTIONS
// ============================================

/**
 * Recursively scan directory for PHP and Blade files
 */
function scanDirectory($dir, $relativePath)
{
    $files = [];
    $items = scandir($dir);
    
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        
        $fullPath = $dir . '/' . $item;
        $relPath = $relativePath . '/' . $item;
        
        if (is_dir($fullPath)) {
            $files = array_merge($files, scanDirectory($fullPath, $relPath));
        } elseif (preg_match('/\.(php|blade\.php)$/', $item)) {
            $files[] = [
                'full_path' => $fullPath,
                'relative_path' => $relPath,
            ];
        }
    }
    
    return $files;
}

/**
 * Scan file for patterns
 */
function scanFile($filePath, $patterns)
{
    $content = file_get_contents($filePath);
    $lines = explode("\n", $content);
    $matches = [];
    
    foreach ($lines as $lineNum => $line) {
        foreach ($patterns as $type => $pattern) {
            if (preg_match($pattern, $line)) {
                $matches[] = [
                    'line' => $lineNum + 1,
                    'type' => $type,
                    'content' => trim($line),
                ];
            }
        }
    }
    
    return $matches;
}

/**
 * Count all files in directories
 */
function countAllFiles($dirs, $baseDir)
{
    $count = 0;
    foreach ($dirs as $dir) {
        $fullPath = $baseDir . '/' . $dir;
        if (is_dir($fullPath)) {
            $count += count(scanDirectory($fullPath, $dir));
        }
    }
    return $count;
}

/**
 * Generate markdown report
 */
function generateMarkdownReport($results, $outputFile)
{
    $md = "# Santri.kelas Usage Mapping\n\n";
    $md .= "_Generated: " . date('Y-m-d H:i:s') . "_\n\n";
    $md .= "This document maps all usage of `\$santri->kelas` and related patterns in the codebase ";
    $md .= "to guide refactoring to the new kelas system.\n\n";
    $md .= "---\n\n";
    
    $md .= "## 📊 Summary\n\n";
    $totalFiles = 0;
    $totalMatches = 0;
    foreach ($results as $dir => $files) {
        $totalFiles += count($files);
        foreach ($files as $file) {
            $totalMatches += count($file['matches']);
        }
    }
    $md .= "- **Total files with kelas usage:** {$totalFiles}\n";
    $md .= "- **Total matches found:** {$totalMatches}\n\n";
    $md .= "---\n\n";
    
    // Priority mapping
    $priorities = categorizePriority($results);
    
    $md .= "## 🎯 Priority Levels\n\n";
    $md .= "### 🔴 HIGH Priority (Break functionality)\n\n";
    if (!empty($priorities['high'])) {
        foreach ($priorities['high'] as $item) {
            $md .= "- **{$item['file']}**\n";
            $md .= "  - Issue: {$item['reason']}\n";
            $md .= "  - Action Required: {$item['action']}\n\n";
        }
    } else {
        $md .= "_No high priority items found_\n\n";
    }
    
    $md .= "### 🟡 MEDIUM Priority (UI/Display)\n\n";
    if (!empty($priorities['medium'])) {
        foreach ($priorities['medium'] as $item) {
            $md .= "- **{$item['file']}**\n";
            $md .= "  - Issue: {$item['reason']}\n";
            $md .= "  - Action Required: {$item['action']}\n\n";
        }
    } else {
        $md .= "_No medium priority items found_\n\n";
    }
    
    $md .= "### 🟢 LOW Priority (Backward compatible)\n\n";
    if (!empty($priorities['low'])) {
        foreach ($priorities['low'] as $item) {
            $md .= "- **{$item['file']}**\n";
            $md .= "  - Note: {$item['reason']}\n\n";
        }
    } else {
        $md .= "_No low priority items found_\n\n";
    }
    
    $md .= "---\n\n";
    
    // Detailed listing by directory
    $md .= "## 📂 Detailed Listing by Directory\n\n";
    
    foreach ($results as $dir => $files) {
        $md .= "### " . ucfirst(str_replace('/', ' / ', $dir)) . "\n\n";
        
        foreach ($files as $file) {
            $md .= "#### 📄 `{$file['file']}`\n\n";
            
            // Group matches by type
            $byType = [];
            foreach ($file['matches'] as $match) {
                $byType[$match['type']][] = $match;
            }
            
            foreach ($byType as $type => $matches) {
                $md .= "**Pattern: `{$type}`**\n\n";
                foreach ($matches as $match) {
                    $md .= "- **Line {$match['line']}:** `{$match['content']}`\n";
                }
                $md .= "\n";
            }
            
            // Suggested action
            $action = getRefactoringAction($file['file'], $byType);
            $md .= "**💡 Suggested Action:**\n";
            $md .= $action . "\n\n";
            $md .= "---\n\n";
        }
    }
    
    // Migration guide
    $md .= "## 📖 Refactoring Guide\n\n";
    $md .= "### General Patterns\n\n";
    $md .= "#### 1. Display in Views (Blade)\n";
    $md .= "```php\n";
    $md .= "// OLD:\n";
    $md .= "{{ \$santri->kelas }}\n\n";
    $md .= "// NEW (backward compatible):\n";
    $md .= "{{ \$santri->kelas_name }}\n";
    $md .= "```\n\n";
    
    $md .= "#### 2. Filter in Controllers\n";
    $md .= "```php\n";
    $md .= "// OLD:\n";
    $md .= "\$santris = Santri::where('kelas', 'PB')->get();\n\n";
    $md .= "// NEW:\n";
    $md .= "\$santris = Santri::whereHas('kelasSantri', function(\$q) {\n";
    $md .= "    \$q->where('id_kelas', 1); // PB = 1\n";
    $md .= "})->get();\n";
    $md .= "```\n\n";
    
    $md .= "#### 3. Kegiatan-Kelas Relation\n";
    $md .= "```php\n";
    $md .= "// OLD: Filter santri by kelas for kegiatan\n";
    $md .= "\$santris = Santri::whereIn('kelas', ['PB', 'Lambatan'])->get();\n\n";
    $md .= "// NEW: Use kegiatan relation\n";
    $md .= "\$santris = \$kegiatan->getEligibleSantris();\n";
    $md .= "```\n\n";
    
    $md .= "### Testing Checklist\n\n";
    $md .= "- [ ] Santri detail page displays correct kelas\n";
    $md .= "- [ ] Santri list filter by kelas works\n";
    $md .= "- [ ] Dashboard statistics by kelas accurate\n";
    $md .= "- [ ] Kegiatan filtering by kelas works\n";
    $md .= "- [ ] Absensi shows correct santri per kegiatan\n";
    $md .= "- [ ] Reports include correct kelas information\n";
    $md .= "- [ ] Mobile API returns kelas data correctly\n\n";
    
    // Write to file
    file_put_contents($outputFile, $md);
}

/**
 * Categorize by priority
 */
function categorizePriority($results)
{
    $priorities = [
        'high' => [],
        'medium' => [],
        'low' => [],
    ];
    
    foreach ($results as $dir => $files) {
        foreach ($files as $file) {
            $fileName = basename($file['file']);
            $priority = determinePriority($file['file'], $file['matches']);
            
            $priorities[$priority['level']][] = [
                'file' => $file['file'],
                'reason' => $priority['reason'],
                'action' => $priority['action'] ?? 'Review and update',
            ];
        }
    }
    
    return $priorities;
}

/**
 * Determine priority level
 */
function determinePriority($filePath, $matches)
{
    $fileName = basename($filePath);
    
    // HIGH: Controllers with where/whereIn
    if (strpos($filePath, 'Controller') !== false) {
        foreach ($matches as $match) {
            if (in_array($match['type'], ['where_kelas', 'wherein_kelas'])) {
                return [
                    'level' => 'high',
                    'reason' => 'Query filtering by kelas column',
                    'action' => 'Update to use kelasSantri relationship',
                ];
            }
        }
    }
    
    // HIGH: Migration files
    if (strpos($filePath, 'migration') !== false) {
        return [
            'level' => 'high',
            'reason' => 'Database schema definition',
            'action' => 'Review but DO NOT modify old migrations',
        ];
    }
    
    // MEDIUM: Views
    if (strpos($filePath, 'views') !== false || strpos($filePath, '.blade.php') !== false) {
        return [
            'level' => 'medium',
            'reason' => 'Display kelas in UI',
            'action' => 'Change to use $santri->kelas_name accessor',
        ];
    }
    
    // MEDIUM: Models
    if (strpos($filePath, 'Models') !== false) {
        return [
            'level' => 'medium',
            'reason' => 'Model attribute or accessor',
            'action' => 'Review accessor implementation',
        ];
    }
    
    // LOW: Everything else
    return [
        'level' => 'low',
        'reason' => 'Other usage',
        'action' => 'Review as needed',
    ];
}

/**
 * Get refactoring action suggestion
 */
function getRefactoringAction($filePath, $matchesByType)
{
    $action = "";
    
    if (strpos($filePath, 'Controller') !== false) {
        if (isset($matchesByType['where_kelas']) || isset($matchesByType['wherein_kelas'])) {
            $action .= "1. Replace `where('kelas')` with `whereHas('kelasSantri')`\n";
            $action .= "2. Update query to use kelas ID instead of name\n";
            $action .= "3. Test filter functionality thoroughly\n";
        }
    }
    
    if (strpos($filePath, '.blade.php') !== false) {
        if (isset($matchesByType['blade_kelas']) || isset($matchesByType['property_access'])) {
            $action .= "1. Replace `{{ \$santri->kelas }}` with `{{ \$santri->kelas_name }}`\n";
            $action .= "2. Test display in browser\n";
        }
    }
    
    if (strpos($filePath, 'Model') !== false) {
        $action .= "1. Review model methods and accessors\n";
        $action .= "2. Ensure backward compatibility\n";
        $action .= "3. Add tests for new relations\n";
    }
    
    if (empty($action)) {
        $action = "Review usage and update as needed based on context.";
    }
    
    return $action;
}
