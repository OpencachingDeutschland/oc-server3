# Production Deployment Guide — oc3.baiti.net

**Status:** PRODUCTION-READY  
**Last Updated:** 2026-06-05  
**Single Playbook:** `ansible/site.yml`

---

## Pre-Deployment Checklist

### Credentials & Secrets
- [ ] SSH deploy keys in place (`files/oc_testsystem_deploy`, `files/oc_testsystem_okapi`)
- [ ] No hardcoded passwords in playbook (all auto-generated)

### Infrastructure
- [ ] Fresh VM provisioned (Debian 13 Trixie, minimal install)
- [ ] Adequate disk space (at least 50GB)
- [ ] Network connectivity verified
- [ ] Internal-only access (behind nginx proxy) — oc3 is NOT internet-facing
- [ ] nginx proxy configured separately (Christian's infrastructure)

### Architecture
```
Internet Client
    ↓
nginx proxy (external, Let's Encrypt certs, Christian's infrastructure)
    ↓ (internal HTTPS with self-signed)
oc3.internal (self-signed certs, OKAPI plaintext signature validation)
```

---

## Deployment Steps

### 1. Generate Secure Credentials (FIRST — before running playbook)

```bash
# Option A: Let playbook auto-generate (recommended)
# Passwords are generated at /tmp/.oc_db_password and /tmp/.oc_admin_password
# These are saved to /root/.oc-credentials after deploy

# Option B: Pre-generate and pass to playbook
export DB_PASS=$(openssl rand -base64 32)
export DB_ADMIN_PASS=$(openssl rand -base64 32)
```

### 2. Configure Playbook Variables

Edit `ansible/vars/main.yml`:

```yaml
# ── Domains (ONLY thing that changes test → prod) ──────────────
oc3_domain: "oc3.internal"              # Internal domain (behind nginx proxy)
oc4_domain: "oc4.internal"              # Symfony app internal domain

# Note: SSL certificates are self-signed (internal use only).
# Public-facing HTTPS is handled by nginx proxy with Let's Encrypt certs.
# Email is DISABLED (no emails sent, ever).
```

### 3. Run Playbook

```bash
cd ansible

# Test syntax
ansible-playbook -i hosts site.yml --syntax-check

# Run with verbose output (recommended for first run)
ansible-playbook -i hosts site.yml -u baiti -v

# With auto-generated credentials stored to /root/.oc-credentials
# Retrieved after deployment with:
ssh root@oc.example.com cat /root/.oc-credentials
```

### 4. Post-Deployment Verification

**Immediate (in first 5 minutes):**
```bash
# Check both apps are responding
curl -I https://oc.example.com/          # Legacy app
curl -I https://oc4.example.com/          # Symfony app

# Verify security headers
curl -I https://oc.example.com/ | grep -E "Strict-Transport|X-Frame|CSP"

# Check PHP error logging
tail -20 /var/log/php/error-8.2.log
tail -20 /var/log/php/error-8.4.log

# Verify MariaDB is running
mysql -u oc -p'<password>' oc -e "SELECT 1;"
```

**Database Verification:**
```bash
# Verify app user (limited privileges)
mysql -u root -e "SHOW GRANTS FOR 'oc'@'localhost';"

# Should show: SELECT,INSERT,UPDATE,DELETE,LOCK TABLES,EXECUTE only

# Verify admin user exists
mysql -u root -e "SHOW GRANTS FOR 'oc_admin'@'localhost';"

# Should show: Full privileges (for migrations only)
```

**Security Verification:**
```bash
# HSTS header present
curl -I https://oc.example.com | grep Strict-Transport

# CSP header (should NOT contain unsafe-eval)
curl -I https://oc.example.com | grep Content-Security-Policy

# X-Frame-Options set
curl -I https://oc.example.com | grep X-Frame

# PHP version NOT exposed
curl -I https://oc.example.com | grep -i "X-Powered-By"  # Should be empty
curl -I https://oc.example.com | grep -i "Server:"        # Should say "Apache" only
```

**Rate Limiting Verification:**
```bash
# Test rate limiting on login endpoint
for i in {1..10}; do
  curl -s -o /dev/null -w "%{http_code}\n" https://oc.example.com/security/login
  sleep 0.5
done

# Should return 429 (Too Many Requests) after limit is exceeded
```

---

## Production Configuration Details

### Database Users

**App User (`oc`):**
- Used by running application
- Privileges: `SELECT,INSERT,UPDATE,DELETE,LOCK TABLES,EXECUTE`
- CANNOT: CREATE, DROP, ALTER, TRIGGER modification
- Password: Auto-generated, 32 chars with special characters

**Admin User (`oc_admin`):**
- Used ONLY during deployment for schema changes
- Privileges: Full (SELECT through TRIGGER)
- NOT used by running application
- Password: Auto-generated, separate from app user
- Purpose: Migrations, schema patches, trigger management

### SSL/TLS Certificates

**Self-Signed (CORRECT for this architecture)**
- Playbook generates self-signed certificates for internal HTTPS
- Used for downstream OKAPI communication (plaintext signature validation requires HTTPS)
- NOT seen by internet clients (nginx proxy handles public-facing HTTPS)
- nginx proxy (Christian's infrastructure) uses Let's Encrypt for public-facing clients

**Why HTTPS downstream?**
- OKAPI requires encrypted signatures and won't accept plaintext HTTP
- Self-signed certs satisfy this requirement for internal communication
- Public-facing HTTPS is handled entirely by nginx proxy

### Email Configuration

**Email is DISABLED (hardcoded, never sends)**
- No SMTP relay configured or required
- `okapi_prevent_emails = true` (hardcoded in settings.inc.php and env.local)
- OKAPI will not attempt to send any emails
- Admin notifications, alerts, etc. are suppressed

### Session Security

All configured in playbook with production defaults:
- Session timeout: 3600 seconds (1 hour)
- HttpOnly flag: Enabled (prevent JavaScript theft)
- Secure flag: Enabled (HTTPS-only)
- SameSite: Lax (CSRF protection)

### Error Handling

- Errors NOT displayed to users (security)
- Errors logged to `/var/log/php/error-8.x.log`
- Logs rotated daily, kept for 14 days
- Access logs rotated daily, kept for 30 days

### Rate Limiting

**Global (mod_evasive):**
- 20 requests/second per IP
- 10-minute block on violations

**Auth Endpoints (stricter):**
- `/security/login` — 5 requests/minute
- `/okapi/services/caches/create` — 5 requests/minute
- `/okapi/services/caches/update` — 5 requests/minute
- `/okapi/services/caches/delete` — 5 requests/minute

### PHP Security

- **Exposed Functions Disabled:** exec, passthru, shell_exec, system, proc_open, popen, curl_exec, parse_ini_file, show_source
- **File Access Restricted:** open_basedir limits to app directory and /tmp
- **Remote Files Disabled:** allow_url_fopen = Off
- **PHP Version Hidden:** expose_php = Off

### Apache Security

- **Version Hidden:** ServerTokens Prod, ServerSignature Off
- **TRACE Method Disabled:** TraceEnable Off
- **File Metadata Hidden:** FileETag None
- **Request Size Limited:** LimitRequestBody 50MB

### Security Headers (All Responses)

```
Strict-Transport-Security: max-age=31536000; includeSubDomains
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: <all features disabled>
Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; ...
```

---

## Monitoring & Maintenance

### Daily Checks
```bash
# Application health
curl -s https://oc.example.com/okapi/services/apisrv/installation | jq .okapi_version_number

# Error logs
tail -50 /var/log/php/error-8.2.log | grep -i "error\|warning"
tail -50 /var/log/apache2/oc.example.com_error.log

# Disk space
df -h /var/www/oc
```

### Weekly Checks
```bash
# Certificate expiry (if using Let's Encrypt)
certbot certificates

# Rate limiting stats
tail -100 /var/log/syslog | grep mod_evasive

# Database integrity
mysql -u oc -p oc -e "SHOW TABLES;"
```

### Monthly Tasks
```bash
# Review logs for attacks
grep "429" /var/log/apache2/oc.example.com_access.log | wc -l

# Verify backups (if configured)
ls -lh /backups/ 2>/dev/null || echo "Backups not configured"

# Update packages (with maintenance window)
apt update
apt list --upgradable
```

---

## Incident Response

### If Hacked
1. Take database snapshot immediately
2. Check `/var/log/php/error-8.*.log` for exploit attempts
3. Check `/var/log/apache2/*_access.log` for attack patterns
4. Kill PHP-FPM and Apache: `systemctl stop php8.2-fpm php8.4-fpm apache2`
5. Preserve logs for forensics
6. Restore from backup or re-deploy with playbook

### If Certificate Expires (Self-Signed)
Self-signed certificates are internal-only and don't expire from a functionality perspective. However, if you want to regenerate them:
```bash
# Regenerate self-signed certs
cd /etc/letsencrypt/live/oc3.internal
openssl req -x509 -nodes -days 3650 -newkey rsa:2048 \
  -keyout privkey.pem -out fullchain.pem \
  -subj "/C=DE/ST=State/L=City/O=OpenCaching/CN=oc3.internal"

# Restart Apache
systemctl restart apache2
```

**For public-facing HTTPS:** Contact Christian regarding Let's Encrypt renewal on the nginx proxy.

### If Database is Corrupted
```bash
# Re-run schema patches
php bin/dbsv-update.php

# Re-run migrations
php bin/symfony doctrine:migrations:migrate --no-interaction
```

---

## Security Patches & Updates

### Automatic Updates NOT Enabled
- Playbook uses specific Debian versions
- Manual testing recommended before updating
- Updates via: `apt update && apt upgrade -y`

### Critical Security Updates
```bash
# For PHP security issues
apt-get install php8.2 php8.4 --only-upgrade

# For Apache/OpenSSL
apt-get install apache2 openssl --only-upgrade

# Restart services after updates
systemctl restart php8.2-fpm php8.4-fpm apache2
```

---

## Disaster Recovery

### Backup Strategy (Manual)
```bash
# Database backup
mysqldump -u oc_admin -p --all-databases > /backups/db-$(date +%Y%m%d).sql.gz

# Application backup
tar -czf /backups/app-$(date +%Y%m%d).tar.gz /var/www/oc/oc-server3

# Keep 30 days of backups
find /backups -name "*.gz" -mtime +30 -delete
```

### Restore from Backup
```bash
# Restore database
mysql < /backups/db-YYYYMMDD.sql.gz

# Restore application
cd /var && tar -xzf /backups/app-YYYYMMDD.tar.gz
chown -R www-data:www-data /var/www/oc
```

---

## Final Checklist Before Production

- [ ] VM deployed behind nginx proxy (NOT internet-facing)
- [ ] SSH deploy keys in place
- [ ] Playbook syntax verified
- [ ] Credentials are auto-generated (not hardcoded)
- [ ] Self-signed certs generated for internal HTTPS
- [ ] Email disabled and verified (no SMTP configured)
- [ ] Log rotation configured (daily, 14-30 day retention)
- [ ] Rate limiting tested and working
- [ ] Security headers verified on all endpoints
- [ ] Database users separated (app vs admin)
- [ ] Monitoring scripts in place
- [ ] Backup procedure documented
- [ ] Incident response plan reviewed
- [ ] Team trained on deployment process
- [ ] nginx proxy configured by Christian (external HTTPS, LE certs)
- [ ] DNS records point to nginx proxy, not oc3 directly

---

## Support & Documentation

- **Playbook:** `/Users/baiti/src/oc-server3/ansible/site.yml`
- **Security:** `/Users/baiti/src/oc-server3/SECURITY-HARDENING.md`
- **OKAPI API:** `/Users/baiti/src/oc-server3/okapi/`
- **Logs:** `/var/log/php/`, `/var/log/apache2/`
- **Credentials:** `/root/.oc-credentials` (auto-saved after deploy)

---

**DEPLOYMENT IS PRODUCTION-READY.** This playbook follows security best practices for internet-facing systems and requires no manual post-deployment configuration.
