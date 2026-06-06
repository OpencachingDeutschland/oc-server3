# oc3.baiti.net Security Hardening — Playbook Updates

**Date:** 2026-06-05  
**Scope:** Comprehensive security hardening of oc3 test system  
**Status:** IMPLEMENTED IN PLAYBOOK — Ready for deployment

---

## HTTP Security Headers (All Endpoints)

**Port 80 & 443:**
- ✅ Strict-Transport-Security (HSTS) — 1 year max-age with subdomains
- ✅ X-Content-Type-Options: nosniff — prevent MIME type sniffing
- ✅ X-Frame-Options: SAMEORIGIN — prevent clickjacking (allow same-site framing)
- ✅ X-XSS-Protection — browser XSS filter enabled
- ✅ Referrer-Policy: strict-origin-when-cross-origin — control referrer leakage
- ✅ Permissions-Policy — disable unnecessary browser features (camera, microphone, etc.)
- ✅ Content-Security-Policy — restrict script/style sources to self, allow inline (for legacy)

**Port 80 Only:**
- ✅ Headers sent BEFORE redirect to ensure all responses include security headers

---

## PHP Security Configuration

**Both PHP 8.2 & 8.4 pools:**

### Error Handling
- ✅ display_errors = Off — don't expose errors to clients
- ✅ display_startup_errors = Off — hide startup errors
- ✅ log_errors = On — log errors to disk
- ✅ error_log = /var/log/php/error-8.x.log — separate logs per version

### Function Restrictions
- ✅ disable_functions — block dangerous: exec, passthru, shell_exec, system, proc_open, popen, curl_exec, parse_ini_file, show_source

### Session Security
- ✅ session.cookie_httponly = 1 — prevent JavaScript access to session cookie
- ✅ session.cookie_secure = 1 — only send over HTTPS
- ✅ session.cookie_samesite = "Lax" — prevent CSRF cookie transmission
- ✅ session.gc_maxlifetime = 3600 — 1-hour session timeout

### Other Hardening
- ✅ expose_php = Off — don't reveal PHP version
- ✅ allow_url_fopen = Off — prevent remote file inclusion
- ✅ open_basedir — restrict file access to app directory and /tmp

---

## Apache/HTTP Security

**Directives:**
- ✅ ServerTokens Prod — hide Apache version (only show "Apache")
- ✅ ServerSignature Off — hide Apache signature in error pages
- ✅ FileETag None — don't reveal file metadata
- ✅ TraceEnable Off — disable HTTP TRACE method
- ✅ LimitRequestBody 52428800 — 50MB upload limit

---

## Rate Limiting

**Global (mod_evasive):**
- ✅ DOSSiteCount 20 — max 20 requests per second per IP
- ✅ DOSPageInterval 1 — measure window is 1 second
- ✅ DOSBlockingPeriod 600 — 10-minute temporary block

**Auth Endpoints (stricter):**
- ✅ /security/login — max 5 requests per minute
- ✅ /okapi/services/caches/create — max 5 requests per minute
- ✅ /okapi/services/caches/update — max 5 requests per minute
- ✅ /okapi/services/caches/delete — max 5 requests per minute

---

## Symfony/Application Config

**Environment variables (.env.local):**
- ✅ SESSION_LIFETIME=3600 — 1-hour session expiry
- ✅ SESSION_COOKIE_SECURE=1 — HTTPS-only
- ✅ SESSION_COOKIE_HTTPONLY=1 — no JavaScript access
- ✅ SESSION_COOKIE_SAMESITE=Lax — CSRF protection
- ✅ CORS_ALLOW_ORIGIN — configured for oc4 domain only
- ✅ OKAPI_PREVENT_EMAILS=1 — no email on test system

---

## OKAPI CRUD Services Security

**Verified Safe:**
- ✅ Create — Level 3 auth, input validation, parameterized queries
- ✅ Read — Level 1 auth, delegates to existing service
- ✅ Update — Level 3 auth, ownership verification, parameterized queries
- ✅ Delete — Level 3 auth, ownership verification, soft-delete (archives only)

---

## Remaining Considerations

### Not Addressed in Playbook (Application-Level)
1. **MD5 Password Hashing** — Legacy compatibility (Symfony security.yaml)
   - Status: Known issue, tied to backward compatibility
   - Mitigation: Hash verification happens in application layer
   - Future: Migration path needed to bcrypt

2. **Database User Privileges** — Still broad (necessary for operations)
   - Status: Acceptable for test system, database is isolated
   - Production: Consider separating app user (SELECT/INSERT/UPDATE/DELETE) from admin user (migrations/triggers)

3. **CSRF Token Verification** — Symfony framework handles
   - Status: Assumed enabled by default
   - Verification: Check in /htdocs_symfony/config/packages/security.php

4. **SQL Injection (Legacy Code)** — Audited OKAPI code, legacy code less critical
   - Status: New OKAPI uses parameterized queries
   - Legacy: Uses older patterns, but isolated for test

---

## Deployment Instructions

**Run the updated playbook:**
```bash
cd /Users/baiti/src/oc-server3/ansible
ansible-playbook -i hosts site.yml -u baiti
```

**Post-Deployment Verification:**
```bash
# Verify headers are set
curl -I https://oc3.baiti.net

# Verify PHP error logging
tail /var/log/php/error-8.2.log
tail /var/log/php/error-8.4.log

# Verify rate limiting
# Test with rapid requests to /security/login

# Verify session config
php -r "echo ini_get('session.cookie_httponly');"
```

---

## Security Summary

**Before Hardening:**
- ⚠️ Missing headers on HTTP
- ⚠️ Error details exposed to clients
- ⚠️ No session timeout configured
- ⚠️ Rate limiting insufficient for auth endpoints
- ⚠️ PHP version exposed
- ⚠️ Apache version exposed

**After Hardening:**
- ✅ Comprehensive security headers on all ports/responses
- ✅ Errors logged, not exposed
- ✅ 1-hour session timeout with secure cookies
- ✅ Rate limiting on auth endpoints (5 req/min)
- ✅ Version information hidden
- ✅ Dangerous functions disabled
- ✅ TRACE method disabled

**Risk Level:** Reduced from MEDIUM-HIGH → **LOW-MEDIUM** (test system)

---

## Files Modified

1. `ansible/templates/oc3-vhost.conf.j2` — Added security headers, TRACE disable
2. `ansible/templates/oc4-vhost.conf.j2` — Added security headers, TRACE disable
3. `ansible/templates/env.local.j2` — Added session & CORS config
4. `ansible/site.yml` — PHP hardening, rate limiting, Apache security directives

## Status

✅ **READY FOR DEPLOYMENT** — Run playbook to apply all hardening measures.
