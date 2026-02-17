<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Scans public/ directory for unauthorized files, injected .htaccess,
 * dangerous file types, and other indicators of compromise.
 *
 * Usage:
 *   php artisan security:audit              # Scan only
 *   php artisan security:audit --fix        # Quarantine suspicious files
 *   php artisan security:audit --notify     # Send email alert
 *
 * Schedule in routes/console.php:
 *   Schedule::command('security:audit --fix --notify')->everySixHours();
 *
 * CUSTOMIZE: Edit $allowedPublicFiles and $allowedPublicDirs for your project.
 */
class SecurityAudit extends Command
{
    protected $signature = 'security:audit {--fix : Quarantine unauthorized files} {--notify : Send alert email}';

    protected $description = 'Audit public directory for unauthorized files and indicators of compromise';

    /**
     * Files allowed in public/ root. Everything else gets flagged.
     * CUSTOMIZE this list for your project.
     */
    private array $allowedPublicFiles = [
        '.htaccess',
        'index.php',
        'robots.txt',
        'favicon.ico',
        'favicon.svg',
        'favicon.png',
        'apple-touch-icon.png',
        'site.webmanifest',
    ];

    /**
     * Directories allowed in public/.
     * CUSTOMIZE this list for your project.
     */
    private array $allowedPublicDirs = [
        'build',
        'css',
        'js',
        'storage',
        'fonts',
        'images',
        'vendor', // Some packages publish assets here
    ];

    /**
     * File extensions that should NEVER exist in public asset directories.
     */
    private array $dangerousExtensions = [
        'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps',
        'cgi', 'pl', 'py', 'rb', 'sh', 'bash',
        'asp', 'aspx', 'jsp',
        'exe', 'dll', 'com', 'bat', 'cmd',
        'htaccess', 'htpasswd',
    ];

    private int $issueCount = 0;

    private array $findings = [];

    public function handle(): int
    {
        $this->info('Security Audit - Scanning public directory...');
        $this->newLine();

        $publicPath = public_path();

        $this->checkUnauthorizedRootFiles($publicPath);
        $this->checkUnauthorizedDirectories($publicPath);
        $this->checkDangerousFiles($publicPath);
        $this->checkHtaccessIntegrity($publicPath);
        $this->checkSymlinks($publicPath);
        $this->checkFilePermissions($publicPath);
        $this->checkRecentlyModifiedFiles($publicPath);

        $this->newLine();

        if ($this->issueCount === 0) {
            $this->info('All clear - no security issues found.');

            return self::SUCCESS;
        }

        $this->error("Found {$this->issueCount} security issue(s)!");

        if ($this->option('fix')) {
            $this->fixIssues();
        } else {
            $this->warn('Run with --fix to automatically quarantine unauthorized files.');
        }

        if ($this->option('notify') && $this->issueCount > 0) {
            $this->sendAlert();
        }

        return self::FAILURE;
    }

    private function checkUnauthorizedRootFiles(string $publicPath): void
    {
        $this->info('Checking for unauthorized files in public root...');

        $files = File::files($publicPath);
        foreach ($files as $file) {
            $filename = $file->getFilename();
            if (! in_array($filename, $this->allowedPublicFiles)) {
                $this->reportIssue('UNAUTHORIZED_FILE', $file->getPathname(), "Unexpected file in public root: {$filename}");
            }
        }
    }

    private function checkUnauthorizedDirectories(string $publicPath): void
    {
        $this->info('Checking for unauthorized directories...');

        $dirs = File::directories($publicPath);
        foreach ($dirs as $dir) {
            $dirname = basename($dir);
            if (! in_array($dirname, $this->allowedPublicDirs)) {
                $this->reportIssue('UNAUTHORIZED_DIR', $dir, "Unexpected directory in public: {$dirname}/");
            }
        }
    }

    private function checkDangerousFiles(string $publicPath): void
    {
        $this->info('Scanning for dangerous file types in asset directories...');

        foreach ($this->allowedPublicDirs as $dir) {
            $dirPath = $publicPath . '/' . $dir;
            if (! File::isDirectory($dirPath)) {
                continue;
            }

            $files = File::allFiles($dirPath);
            foreach ($files as $file) {
                $extension = strtolower($file->getExtension());

                if (in_array($extension, $this->dangerousExtensions)) {
                    $this->reportIssue(
                        'DANGEROUS_FILE',
                        $file->getPathname(),
                        "Dangerous file type (.{$extension}) found in asset directory"
                    );
                }

                if ($file->getFilename() === '.htaccess') {
                    $this->reportIssue(
                        'INJECTED_HTACCESS',
                        $file->getPathname(),
                        'Suspicious .htaccess found in asset directory'
                    );
                }

                // Check non-PHP files for embedded PHP code
                if (in_array($extension, ['html', 'htm', 'txt', 'css', 'js', 'svg', 'ico'])) {
                    $content = file_get_contents($file->getPathname());
                    if ($content !== false && preg_match('/<\?php|<\?=/', $content)) {
                        $this->reportIssue(
                            'PHP_IN_ASSET',
                            $file->getPathname(),
                            'PHP code detected inside non-PHP file'
                        );
                    }
                }
            }
        }
    }

    private function checkHtaccessIntegrity(string $publicPath): void
    {
        $this->info('Verifying .htaccess integrity...');

        $htaccessPath = $publicPath . '/.htaccess';
        if (! File::exists($htaccessPath)) {
            $this->reportIssue('MISSING_HTACCESS', $htaccessPath, 'Main .htaccess is missing!');

            return;
        }

        $content = File::get($htaccessPath);

        $suspiciousPatterns = [
            '/RewriteRule.*https?:\/\/(?!%\{HTTP_HOST\})/' => 'External redirect rule detected',
            '/Header\s+set\s+Location/i' => 'Header Location redirect detected',
            '/Redirect\s+(301|302|permanent|temp)/i' => 'Redirect directive detected',
            '/php_value\s+auto_prepend_file/i' => 'PHP auto_prepend_file detected',
            '/php_value\s+auto_append_file/i' => 'PHP auto_append_file detected',
            '/SetHandler\s+application\/x-httpd-php/i' => 'PHP handler override detected',
            '/AddType\s+application\/x-httpd-php/i' => 'PHP MIME type override detected',
        ];

        foreach ($suspiciousPatterns as $pattern => $description) {
            if (preg_match($pattern, $content)) {
                $this->reportIssue('HTACCESS_SUSPICIOUS', $htaccessPath, $description);
            }
        }

        // Compare against git-tracked version
        $gitHash = trim(shell_exec("cd " . escapeshellarg(base_path()) . " && git hash-object " . escapeshellarg($htaccessPath) . " 2>/dev/null") ?: '');
        $trackedHash = trim(shell_exec("cd " . escapeshellarg(base_path()) . " && git rev-parse HEAD:public/.htaccess 2>/dev/null | xargs git cat-file -p 2>/dev/null | git hash-object --stdin 2>/dev/null") ?: '');

        if ($gitHash && $trackedHash && $gitHash !== $trackedHash) {
            $this->reportIssue('HTACCESS_MODIFIED', $htaccessPath, '.htaccess has been modified from git-tracked version');
        }
    }

    private function checkSymlinks(string $publicPath): void
    {
        $this->info('Checking for unauthorized symlinks...');

        $allowedSymlinks = [
            $publicPath . '/storage' => storage_path('app/public'),
        ];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($publicPath, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if (is_link($file->getPathname())) {
                $target = readlink($file->getPathname());
                if (! isset($allowedSymlinks[$file->getPathname()]) || $allowedSymlinks[$file->getPathname()] !== $target) {
                    $this->reportIssue(
                        'UNAUTHORIZED_SYMLINK',
                        $file->getPathname(),
                        "Symlink pointing to: {$target}"
                    );
                }
            }
        }
    }

    private function checkFilePermissions(string $publicPath): void
    {
        $this->info('Checking file permissions...');

        $perms = fileperms($publicPath);
        if ($perms & 0x0002) {
            $this->reportIssue('WORLD_WRITABLE', $publicPath, 'Public directory is world-writable!');
        }

        $indexPhp = $publicPath . '/index.php';
        if (File::exists($indexPhp)) {
            $perms = fileperms($indexPhp);
            if ($perms & 0x0010) {
                $this->reportIssue('GROUP_WRITABLE', $indexPhp, 'index.php is group-writable');
            }
        }
    }

    private function checkRecentlyModifiedFiles(string $publicPath): void
    {
        $this->info('Checking for recently modified files (last 24h)...');

        $cutoff = time() - 86400;
        $skipDirs = [$publicPath . '/build'];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($publicPath, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getMTime() > $cutoff) {
                $path = $file->getPathname();

                $skip = false;
                foreach ($skipDirs as $skipDir) {
                    if (str_starts_with($path, $skipDir)) {
                        $skip = true;
                        break;
                    }
                }
                if ($skip) {
                    continue;
                }

                $modified = date('Y-m-d H:i:s', $file->getMTime());
                $this->warn("  Recently modified: {$path} (at {$modified})");
            }
        }
    }

    private function reportIssue(string $type, string $path, string $description): void
    {
        $this->issueCount++;
        $this->findings[] = [
            'type' => $type,
            'path' => $path,
            'description' => $description,
        ];

        $this->error("  [{$type}] {$description}");
        $this->line("    Path: {$path}");
    }

    private function fixIssues(): void
    {
        $this->newLine();
        $this->warn('Quarantining suspicious files...');

        foreach ($this->findings as $finding) {
            switch ($finding['type']) {
                case 'UNAUTHORIZED_FILE':
                case 'DANGEROUS_FILE':
                case 'PHP_IN_ASSET':
                case 'INJECTED_HTACCESS':
                    if (File::exists($finding['path'])) {
                        $quarantine = storage_path('quarantine/' . date('Y-m-d_His'));
                        File::ensureDirectoryExists($quarantine);
                        $dest = $quarantine . '/' . basename($finding['path']);
                        File::move($finding['path'], $dest);
                        $this->info("  Quarantined: {$finding['path']} -> {$dest}");
                    }
                    break;

                case 'UNAUTHORIZED_DIR':
                    if (File::isDirectory($finding['path'])) {
                        $quarantine = storage_path('quarantine/' . date('Y-m-d_His'));
                        File::ensureDirectoryExists($quarantine);
                        $dest = $quarantine . '/' . basename($finding['path']);
                        File::moveDirectory($finding['path'], $dest);
                        $this->info("  Quarantined directory: {$finding['path']} -> {$dest}");
                    }
                    break;

                case 'UNAUTHORIZED_SYMLINK':
                    File::delete($finding['path']);
                    $this->info("  Removed symlink: {$finding['path']}");
                    break;

                default:
                    $this->warn("  Cannot auto-fix {$finding['type']}: {$finding['path']}");
            }
        }
    }

    private function sendAlert(): void
    {
        $mailTo = config('mail.to.address', env('MAIL_TO', env('MAIL_FROM_ADDRESS')));

        if (! $mailTo) {
            $this->error('No email address configured for alerts. Set MAIL_TO in .env');

            return;
        }

        $subject = 'SECURITY ALERT: Unauthorized files detected on ' . config('app.url');

        $body = "Security audit found {$this->issueCount} issue(s) on " . config('app.url') . ":\n\n";
        foreach ($this->findings as $finding) {
            $body .= "[{$finding['type']}] {$finding['description']}\n";
            $body .= "  Path: {$finding['path']}\n\n";
        }
        $body .= "\nTimestamp: " . now()->toDateTimeString() . "\n";
        $body .= "Server: " . gethostname() . "\n";

        try {
            \Illuminate\Support\Facades\Mail::raw($body, function ($message) use ($mailTo, $subject) {
                $message->to($mailTo)->subject($subject);
            });
            $this->info('Alert email sent to: ' . $mailTo);
        } catch (\Exception $e) {
            $this->error('Failed to send alert: ' . $e->getMessage());
        }
    }
}
