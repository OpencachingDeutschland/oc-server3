# Test System Rules & Guardrails

**STATUS: LOCAL-ONLY — DO NOT COMMIT TO REPO**  
**READ THIS AT SESSION START AND WHEN CONFUSED**

---

## Core Rule: Local vs. Repository

### ✅ COMMIT to Repository (htdocs/, docs/, sql/)
- Source code changes (PHP, Symfony, JS)
- Architecture decisions and code-level documentation
- SQL schema migrations (`sql/migrations/`)
- Test fixtures and code-level test data

### ❌ NEVER COMMIT (in .gitignore or local-only)
- **`ansible/`** — entire directory is local-only
  - Playbook (`site.yml`)
  - Variables (`vars/main.yml`)
  - Templates (`templates/`)
  - Deploy keys
  - Reason: Infrastructure-specific, credentials, domain-specific
  
- **`files/test-data.sql.gz`** — extracted from ocde
  - Reason: Environment-specific data dump, regenerated per environment
  
- **Infrastructure documentation** — testsystem-2.md, TESTSYSTEM-RULES.md, deployment guides
  - Reason: Contains private infrastructure details, deployment topology, domain names
  
- **Security review documents** — docs/security-review-test-system.md
  - Reason: Private security analysis, not for public distribution

- **Credentials & secrets** — .env files, passwords, keys
  - Reason: Never commit secrets; use environment variables

- **Generated files** — .env.local, SSL certificates, cache directories
  - Reason: Generated at deploy time, not source

---

## Playbook Rules

### ✅ Playbook Must
- **Be idempotent** — can run multiple times, same result
- **Require zero manual post-steps** — everything works when it finishes
- **Include all prerequisites** — the system must be complete and testable immediately
- **Be version-controlled locally** — track changes but don't push to remote
- **Use strong passwords** — auto-generate, never hardcode weak ones
- **Apply security hardening** — treat as production-like (APP_DEBUG=0, security headers, rate limiting)
- **Ensure test data completeness** — all caches must have descriptions (lorem ipsum step is **mandatory**)
- **Work in both environments**:
  - Intranet (current test system with self-signed certs)
  - Public (deployed behind Christian's nginx with LE certs)

### ❌ Playbook Must NOT
- **Require manual database initialization** — dump + scripts must be automatic
- **Leave manual tasks for the user** — "run this command after playbook" is a bug
- **Assume external resources** — must work on isolated network
- **Hardcode domains in code** — use environment config or reverse-proxy headers
- **Break on re-runs** — must be safe to run twice
- **Mix development and test configs** — ocde (dev with APP_DEBUG=1) stays separate from oc3 (test with APP_DEBUG=0)

---

## Code Separation Rules

### ocde (Development Environment)
- **Runs on:** ddev (Docker-based, local)
- **Debug:** APP_DEBUG=1, SYMFONY_ENV=dev, full logging
- **Purpose:** Feature development, debugging, experimentation
- **Settings:** debug aids enabled everywhere
- **Credentials:** Can use test/weak passwords (local-only)

### oc3 (Test System)
- **Runs on:** Bare metal VM (Debian 13)
- **Debug:** APP_DEBUG=0, SYMFONY_ENV=prod, hardened
- **Purpose:** Production-like validation, final testing before deployment
- **Settings:** Match production exactly (except LE certs on intranet)
- **Credentials:** Strong, auto-generated, no hardcoding

### Production (Christian's Infrastructure)
- **Runs on:** VM behind nginx reverse proxy
- **SSL:** LE certs on nginx, self-signed internally
- **Debug:** OFF
- **Settings:** Exact replica of oc3 test system
- **Difference from oc3:** Only the nginx routing and external domain

**RULE:** Never copy settings from ocde to oc3. Don't enable debug mode "temporarily" on test. Keep clear separation.

---

## Data Rules

### Test Data Strategy
- **Source:** Extract from ocde (latest state)
- **Location:** `files/test-data.sql.gz` (local-only, not in repo)
- **Regeneration:** Run `ansible/extract-test-data.sh` when ocde changes
- **Completeness:** Must include:
  - All caches with descriptions (lorem ipsum fallback for missing)
  - Test users
  - Sample cache logs
  - Relevant static data

- **Application:** Playbook Phase 8
  - Import test-data.sql.gz
  - Mandatory: Add lorem descriptions to any missing
  - Clear and regenerate caches
  - Result: All caches render, zero 404s

### Dump & Schema Rules
- **Base dump:** `sql/dump_v158.sql` (committed, canonical schema)
- **Migrations:** Via Doctrine (committed)
- **Static data:** `sql/static-data/` (committed)
- **Patches:** `dbsv-update.php`, `db-import-static.php` (committed)

**Order matters:**
1. Base dump
2. Schema patches
3. Static data
4. Trigger refresh (maintain.php)
5. Doctrine migrations
6. Test data import
7. Completeness check (lorem descriptions)

---

## Credential & Secret Rules

### ✅ Safe Practices
- Database password: Auto-generated per deployment (32-char, random)
- APP_SECRET: Generated fresh, not reused
- Store in: Ansible variables (local-only), environment variables (systemd)
- Never: Hardcoded in PHP, committed to repo, visible in logs

### ❌ Forbidden
- Plaintext credentials in code (settings.inc.php must use env vars)
- Hardcoded API keys or secrets
- Committed .env files or credentials
- Using same password across environments

**Implementation:**
```php
// ✅ CORRECT
$db_pass = getenv('OC_DB_PASS') ?: 'fallback';

// ❌ WRONG
$db_pass = 'hardcoded_password';
```

---

## Domain & Routing Rules

### ✅ Config-Driven Domains
- Read from config files or environment
- Pass through reverse-proxy headers (X-Forwarded-Proto, X-Forwarded-Host)
- Use `<meta>` tags in templates for cross-domain routing

### ❌ Never Hardcode
- Domain names in PHP code
- Domain names in JavaScript
- URLs in Nginx configs (use variables)

**Example (Wrong):**
```javascript
// ❌ WRONG
const newDomain = 'https://oc4.baiti.net';
```

**Example (Right):**
```javascript
// ✅ CORRECT
var meta = document.querySelector('meta[name="symfony-domain"]');
var newDomain = (meta && meta.content) ? meta.content : window.location.hostname;
```

---

## Deployment Rules

### For Intranet (Current)
- Self-signed certificates (ok for internal)
- oc3.baiti.net, oc4.baiti.net as test domains
- HTTPS internally (required for OKAPI plaintext signatures)
- No LE certs needed

### For Public (Christian's Infrastructure)
- **No code changes required** — playbook works as-is
- **Deployment differs only in:**
  - nginx handles external HTTPS (LE certs)
  - oc3 listens on internal HTTPS (self-signed)
  - DNS via Christian's registered domains
  - nginx proxies to `https://oc3.internal/`

**Rule:** Test system playbook IS the production playbook. No separate configs.

---

## What NOT to Do (Common Mistakes)

### ❌ Committing Playbook
- **Mistake:** Adding `ansible/site.yml` to repo
- **Why it's wrong:** Infrastructure-specific, credentials embedded, not part of codebase
- **Prevention:** Keep ansible/ in .gitignore always

### ❌ Hardcoding Test Domains
- **Mistake:** Using `oc3.baiti.net` directly in code
- **Why it's wrong:** Fails on different deployments, ties code to environment
- **Prevention:** Use config/env vars, read from headers

### ❌ Breaking Idempotency
- **Mistake:** Running manual SQL after playbook, or documenting "run this after"
- **Why it's wrong:** Next playbook run won't apply the fix, manual step required forever
- **Prevention:** Fix the playbook instead

### ❌ Mixing ocde & oc3 Configs
- **Mistake:** Copying APP_DEBUG=1 from ocde to oc3, or vice versa
- **Why it's wrong:** Breaks separation, test system no longer matches production
- **Prevention:** Keep separate settings files, never copy between environments

### ❌ Incomplete Test Data
- **Mistake:** Playbook finishes but some caches return 404
- **Why it's wrong:** Makes system untestable, creates false bugs
- **Prevention:** Lorem descriptions are mandatory, Phase 8 must apply them

### ❌ Manual Cache Clearing
- **Mistake:** Clearing caches manually during playbook troubleshooting
- **Why it's wrong:** Next re-run won't find/clear those caches, playbook becomes non-idempotent
- **Prevention:** Let playbook handle it; if cache issues persist, fix the playbook

---

## Session Checklist

**At start of each session, verify:**
- [ ] No playbook files committed to repo (check .gitignore)
- [ ] Test data is local-only (not in git history)
- [ ] Private docs (testsystem-2.md, RULES) stay local
- [ ] Source code changes (htdocs/, SQL migrations) are ready to commit
- [ ] Idempotency preserved — no manual steps documented
- [ ] Domains config-driven, not hardcoded
- [ ] Credentials in env vars, not in code

**If any rule is broken during work:**
- [ ] Revert the commit
- [ ] Fix the underlying issue (not the symptom)
- [ ] Test on clean VM
- [ ] Re-read this document

---

## Questions to Ask Yourself

**Before committing something:**
- Is this infrastructure-specific? → Keep local
- Is this a secret or credential? → Environment variable only
- Does this hardcode a domain? → Use config instead
- Is this test data? → Keep local, regenerate from ocde
- Would this work on a different VM? → Yes? Safe to commit. No? Keep local.

**Before running the playbook:**
- Is the VM clean/fresh? 
- Can I run this playbook twice without issues?
- Will everything work when it finishes?
- Are all test caches guaranteed to render?

**Before declaring "done":**
- Did the playbook complete with 0 failures?
- Can I access oc3 and oc4?
- Do all caches render (no 404s)?
- Is OKAPI responding?
- Could I run this on a different VM and get the same result?

---

## Remember

> **The test system IS the production system.** The only difference is where it runs and who provides SSL termination. Never treat test as a sandbox for experiments.

> **The playbook must be a single source of truth.** If you're doing something manually, the playbook is incomplete.

> **Local-only means local-only.** Don't sneak infrastructure details into the repo "just to document them." Keep them local, always.

