# IBM i (AS/400) Access ODBC Driver for PHP 8 on Ubuntu/Debian

[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
![Last commit](https://img.shields.io/github/last-commit/elightsys/iSeries-Access-ODBC-Driver-for-PHP)
![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen)
![Stars](https://img.shields.io/github/stars/elightsys/iSeries-Access-ODBC-Driver-for-PHP?style=social)

Step-by-step guide to configure **IBM i (AS/400) Access ODBC** with **PHP 8** on **Ubuntu/Debian**, plus a PDO example and minimal Docker/Compose templates.

> **Heads-up**: IBM i Access ODBC packages are license-protected. This repo explains the steps but does **not** redistribute IBM packages. Please obtain them per IBM's terms.

---

## Tested Matrix

| Component | Versions |
|---|---|
| PHP | 8.1 / 8.2 |
| OS  | Ubuntu 20.04 / 22.04, Debian 12 |
| Web | Apache (php:8.2-apache base) |
| ODBC | unixODBC / IBM i Access ODBC Driver |

> Contributions for additional versions are welcome via PRs.

---

## TL;DR

```bash
# System deps
sudo apt-get update && sudo apt-get install -y unixodbc odbcinst

# Install IBM i Access ODBC driver (obtain package from IBM; do NOT commit it here).
# e.g. using alien if you received an RPM:
# sudo apt-get install -y alien
# sudo alien -i iSeriesAccess-*.rpm

# Configure DSN
sudo nano /etc/odbc.ini   # or use ~/.odbc.ini
sudo chmod 600 /etc/odbc.ini

# Verify ODBC
odbcinst -j
isql -v MYIBMI

# PHP PDO ODBC extension
# In Docker we run docker-php-ext-install pdo_odbc; on host use distro packages or rebuild PHP.
```

---

## Detailed Setup

### 1) Install ODBC components
```bash
sudo apt-get update
sudo apt-get install -y unixodbc odbcinst
```

### 2) Install IBM i Access ODBC
Obtain the IBM i Access ODBC package from IBM (download portal or vendor). Follow your license terms. If you have an RPM on Debian/Ubuntu:
```bash
sudo apt-get install -y alien
sudo alien -i iSeriesAccess-*.rpm
```
> Do not commit IBM installers to this repository.

### 3) Create a DSN

Create `/etc/odbc.ini` (system DSN) **or** `~/.odbc.ini` (user DSN). Example:

```ini
[MYIBMI]
Description=IBM i Access ODBC Driver (Example DSN)
Driver=IBM i Access ODBC Driver
System=YOUR_IBMI_HOST_OR_IP
UserID=YOUR_USERNAME
Password=YOUR_PASSWORD
Naming=1
DefaultLibraries=YOURLIB
Database=YOURDB
CCSID=1208
```

**Security**: For system DSN, restrict file permissions:
```bash
sudo chmod 600 /etc/odbc.ini
```

### 4) PHP integration

Prefer **PDO ODBC** for modern code:
```php
<?php
$pdo = new PDO('odbc:DSN=MYIBMI', 'YOUR_USERNAME', 'YOUR_PASSWORD', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
```

Run a sanity query (replace table/db as needed):
```sql
SELECT CURRENT_DATE as today FROM SYSIBM.SYSDUMMY1;
```

---

## Minimal Docker/Compose

`Dockerfile`:
```dockerfile
FROM php:8.2-apache
RUN apt-get update && apt-get install -y --no-install-recommends     unixodbc odbcinst alien wget ca-certificates     && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo_odbc
COPY odbc.ini /etc/odbc.ini
COPY public/ /var/www/html/
```

`docker-compose.yml`:
```yaml
version: "3.9"
services:
  php:
    build: .
    ports:
      - "8080:80"
    volumes:
      - ./public:/var/www/html
      - ./odbc.ini:/etc/odbc.ini:ro
    restart: unless-stopped
```

Place a minimal test page under `public/`:
```php
<?php
phpinfo(); // confirm ODBC/PDO_ODBC listed
```
And a PDO test (`public/test_pdo.php`) that queries `SYSIBM.SYSDUMMY1`.

> **Note**: This image does **not** include IBM packages. Add them in your build process if your license allows, or install at runtime on the host.

---

## Troubleshooting

- **Check ODBC installation**
  ```bash
  odbcinst -j
  isql -v MYIBMI
  ```

- **PHP can’t find ODBC**  
  Ensure `pdo_odbc` is enabled. In Dockerfile we run `docker-php-ext-install pdo_odbc`. On host, verify with:
  ```bash
  php -m | grep -i odbc
  ```
  and check `phpinfo()`.

- **Driver path / lib64 issues**  
  Some systems require a `lib64` symlink for IBM libraries. Verify the actual installed paths and add symlinks if needed.

- **Permissions**  
  If `/etc/odbc.ini` is world-readable, credentials are exposed. Use `chmod 600`.

---

## Security Notes
- Do not commit secrets or IBM installers into the repository.
- Restrict DSN files (`chmod 600`).
- Prefer environment variables or secret managers in production.

---

## Roadmap
- More OS/PHP matrix entries
- GitHub Actions for docs lint + link check
- Community troubleshooting additions

---

## Contributing
See [CONTRIBUTING.md](CONTRIBUTING.md). PRs welcome!

## License
Copyright © 2025 Zoltan Vlasits.
Licensed under [MIT](LICENSE)
