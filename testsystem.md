- read the following, understand it, provide feedback for chat-style discussion

- prerequisite: team always talks about going to php 8.4... should we do this and test
  in the dev env we have? Push that into my fork and then do the deploy (see below) from
  my fork?

# Switching opencaching.de's hoster

# Context

- Current hoster, Host Europe is too expensive

- prod environment grew over years, nobody really understands the full compositon

- team has not managed to make this move in 3 years (!)

- current approach: bi-weekly meetingss to coordinate .... bi-weekly meetings?
  This is a task that can be done over a weekend (my claim)

# Strategy

- I provide a clean debian vm on a regisgtered domain, with ssh access and sudo capability

- we instantiate a testsystem, this is your task and I will guide you

- we need to manually and inrementally install and post-install-configure many artifacts

- we will need to fix things not working

- we need to document all steps, everything in detail, once it does, we transform all we
  did into an ansible playbook.

- we dispose the VM, create a new one, replay the ansible and verify funcitonality.

- once we have this, we can instantiate a "real prod server" without running into any problems

# Questions

- will this approach work? Do you have other suggestions?

# Plan, interactively refine strategy w/ me

- make a detailed plan on what we need to do in what sequence, basically what later needs to be
  formalized in the playbook but at first we do it manually step by step and verifying results

- For instance

  1. apt-get install <all packages we already know we definitely need>

  2. configure all services and start them

  3. establish github repo access

  4. pull openaching/development

  5. pull okapi/release

  6. run the legacy app on https://oc3.somedomain.de

  7. run the symfony app on htps://oc4.somedomain.de


In fact these domains may be the only parameters to be adjusted in the ansible playbook

---

# Decisions made (2026-06-02)

After a full discovery pass on `ocde` (the DDEV dev environment) and discussion, here
are the conclusions. Full rationale is in the project memory files
(`~/.claude/projects/.../memory/`).

## Resolved questions

### PHP 8.4? → Deferred
Do NOT upgrade PHP as part of this migration. Stick with PHP 8.2 (same as ocde).
Coupling a PHP version bump with a hoster move makes debugging impossible.
The Ansible playbook makes PHP 8.4 a one-line change later.

### Apache or nginx? → Apache
Stay on Apache. The legacy app has 20 `.htaccess` files with mod_rewrite rules.
Translating them to nginx is added risk with no benefit for this phase.
Can be revisited after the migration is stable.

### Bare metal or Docker/DDEV? → Bare metal
DDEV is a dev tool. Production runs Apache + PHP-FPM + MariaDB directly on the OS.
The Ansible playbook models exactly what prod will look like.

### Database source? → mysqldump from ocde
Full dump (3.2MB gzipped, 161 tables, 17MB raw). More complete than the repo's
base dump. Skips the entire init.sh chain.

### GitHub access? → Deploy keys (one per repo)
Two separate keys (GitHub doesn't allow same key across repos):
- `oc_testsystem_deploy` → hxdimpf/oc-server3
- `oc_testsystem_okapi` → hxdimpf/okapi (for future PRs)

### OKAPI? → Upstream first, fork later
Use public `opencaching/okapi` for initial setup. Switch to `hxdimpf/okapi` fork
(with unmerged PRs) via one-line Ansible var change when ready.

### Domains? → oc3.cirasi.de + oc4.cirasi.de
These are the only two parameters that change between test and production.

## What's prepared

| Asset | Location |
|-------|----------|
| Ansible playbook (358 lines, 8 phases) | `ansible/site.yml` |
| Config templates (6 Jinja2 files) | `ansible/templates/` |
| Apache vhost templates (2) | `ansible/templates/` |
| Deploy keys (2) | `ansible/files/` |
| Stored procedure patch | `ansible/files/sp_update_logstat.sql` |
| Database dump | `db_dump_dev.sql.gz` |
| Variables (domains, paths, settings) | `ansible/vars/main.yml` |
| Inventory placeholder | `ansible/inventory.ini` |

## Pending

- VM IP + SSH user from Christian (currently in Kyoto)
- DNS: oc3.cirasi.de + oc4.cirasi.de → VM
- Let's Encrypt setup (user handles)

## The moment the VM is ready

SSH in → run Phase 1–8 manually → fix issues → fold fixes into Ansible templates
→ destroy VM → replay Ansible → verify → done.
