# Deploy Process — oc3 (Test) and ocde (Production)

## 1. Fresh Deployment (Playbook — oc3 only)

```bash
cd ~/src/oc-server3/ansible
rm -f /tmp/.oc_db_password /tmp/.oc_admin_password /tmp/.oc_app_secret
ansible-playbook -i inventory.ini site.yml -u baiti \
  -e "test_data_file=files/test-data.sql.gz" \
  -e "db_local=true"
# Expect: ok=~90 changed=~50 failed=0
```

## 2. Update Main App Code

```bash
ssh <user>@<server>
cd /var/www/oc/oc-server3
sudo -u www-data git pull origin <branch>

# Legacy composer
cd htdocs
sudo -u www-data composer install --no-interaction

# Symfony composer
cd ../htdocs_symfony
sudo -u www-data composer install --no-interaction
sudo -u www-data php bin/console cache:clear

# Schema patches
cd /var/www/oc/oc-server3
sudo -u www-data php bin/dbsv-update.php

# Rebuild triggers/stored procs (if schema changed)
cd sql/stored-proc
echo "" | sudo -u www-data php maintain.php
```

## 3. Update OKAPI Code

OKAPI lives at `/var/www/oc/oc-server3/htdocs/okapi/` (part of DocumentRoot, no Alias).
It is deployed from the okapi fork (`hxdimpf/okapi`, branch `oc4-combined`).

```bash
ssh <user>@<server>
cd /var/www/oc/oc-server3/htdocs/okapi
sudo -u www-data git pull origin oc4-combined
sudo -u www-data composer install --no-interaction

# Apply database mutations
curl -k https://<host>/okapi/update?install=true
```

### If on ocde (different okapi branch, different consumer tokens):
- Branch may differ — adjust `git pull origin <branch>` accordingly
- Consumer tokens in `okapi_consumers` table may differ

## 4. Running Tests

```bash
cd ~/src/okapi/tests

# config.js controls target:
#   oc3:   export const baseUrl = 'https://oc3.baiti.net'
#   ocde:  export const baseUrl = 'https://www.opencaching.de'

NODE_TLS_REJECT_UNAUTHORIZED=0 node oc-create-circle.mjs
```
