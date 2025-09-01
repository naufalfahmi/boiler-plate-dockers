# 🚀 Docker Project Structure

## 📋 Overview

Modular Docker setup untuk multiple PHP applications dengan hybrid architecture:
- **Shared Infrastructure**: Redis, Nginx, Let's Encrypt
- **Isolated Applications**: Setiap app punya PHP dan MySQL sendiri
- **Flexible Configuration**: PHP version, MySQL settings per project

## 🏗️ Architecture

```
app-bosowa-docker/
├── projects/
│   ├── app-bosowa/          # PHP 8.3 + MySQL 8.0
│   │   ├── .env
│   │   ├── docker-compose.yml
│   │   ├── docker/
│   │   │   └── Dockerfile
│   │   ├── config/
│   │   │   ├── php.ini
│   │   │   └── mysql.cnf
│   │   ├── src/
│   │   │   ├── index.php
│   │   │   └── .htaccess
│   │   └── data/
│   │       └── mysql/
│   └── app-lain/            # PHP 8.2 + MySQL 8.0
│       ├── .env
│       ├── docker-compose.yml
│       ├── docker/
│       │   └── Dockerfile
│       ├── config/
│       │   ├── php.ini
│       │   └── mysql.cnf
│       ├── src/
│       │   ├── index.php
│       │   └── .htaccess
│       └── data/
│           └── mysql/
└── shared/                  # Shared Infrastructure
    ├── docker-compose.yml
    ├── nginx/
    │   ├── nginx.conf
    │   └── sites/
    │       ├── app-bosowa.conf
    │       └── app-lain.conf
    ├── certbot/
    └── letsencrypt/
```

## 🚀 Quick Start

### 1. Setup Environment
```bash
# Copy environment files
make setup
```

### 2. Build & Start Services
```bash
# Build semua Docker images
make build

# Start semua services
make up

# Test connections
make test
```

### 3. Access Applications

#### 🌐 Web Applications
- **App Bosowa (PHP 8.3)**: http://localhost:8080
- **App Lain (PHP 8.2)**: http://localhost:8080 (dengan Host: app-lain.localhost)

**Cara Akses App Lain:**
- **Method 1**: Tambahkan entry di `/etc/hosts`:
  ```bash
  127.0.0.1 app-lain.localhost
  ```
  Lalu akses: http://app-lain.localhost:8080

- **Method 2**: Gunakan curl dengan custom Host header:
  ```bash
  curl -H "Host: app-lain.localhost" http://localhost:8080
  ```

- **Method 3**: Gunakan browser extension untuk mengubah Host header

#### 🗄️ Database Access
```
App Bosowa MySQL:
- Host: localhost
- Port: 3307
- Database: app_bosowa
- User: app_bosowa
- Password: supersecretuser

App Lain MySQL:
- Host: localhost
- Port: 3309
- Database: app_lain
- User: app_lain
- Password: supersecretuser

Shared Redis:
- Host: localhost
- Port: 6379
```

## 🛠️ Available Commands

```bash
# Setup & Management
make setup          # Setup semua project (copy .env files)
make build          # Build semua Docker images
make up             # Start semua services
make down           # Stop semua services
make logs           # Show logs dari semua services
make test           # Test semua connections

# Shell Access
make sh-php         # Shell ke PHP container (pilih app)
make sh-mysql       # Shell ke MySQL container (pilih app)
make sh-redis       # Shell ke Redis container
make sh-nginx       # Shell ke Nginx container

# SSL Certificates
make issue-cert     # Issue Let's Encrypt certificates
make renew-cert     # Renew Let's Encrypt certificates

# Cleanup
make clean          # Clean up semua (volumes, images, data)
```

## 🔧 Configuration

### Environment Variables

#### App Bosowa (.env)
```ini
APP_NAME=app-bosowa
PHP_VERSION=8.3
PHP_MEMORY_LIMIT=512M
MYSQL_VERSION=8.0
MYSQL_PORT=3307
MYSQL_DATABASE=app_bosowa
MYSQL_USER=app_bosowa
MYSQL_PASSWORD=supersecretuser
REDIS_HOST=shared-redis
REDIS_PORT=6379
SERVER_NAME=app-bosowa.localhost
TZ=Asia/Jakarta
```

#### App Lain (.env)
```ini
APP_NAME=app-lain
PHP_VERSION=8.2
PHP_MEMORY_LIMIT=256M
MYSQL_VERSION=8.0
MYSQL_PORT=3309
MYSQL_DATABASE=app_lain
MYSQL_USER=app_lain
MYSQL_PASSWORD=supersecretuser
REDIS_HOST=shared-redis
REDIS_PORT=6379
SERVER_NAME=app-lain.localhost
TZ=Asia/Jakarta
```

## 🌐 Network Architecture

```
┌─────────────────┐    ┌─────────────────┐
│   App Bosowa    │    │   App Lain      │
│   (PHP 8.3)     │    │   (PHP 8.2)     │
│   Port: 8080    │    │   Port: 8080    │
└─────────────────┘    └─────────────────┘
         │                       │
         └───────────────────────┼───────────────────────┐
                                 │                       │
                    ┌─────────────▼─────────────┐        │
                    │     Shared Redis          │        │
                    │     Port: 6379            │        │
                    └───────────────────────────┘        │
                                                         │
         ┌───────────────────────────────────────────────┼───┐
         │                                               │   │
         ▼                                               ▼   ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ App Bosowa      │    │ App Lain        │    │ Shared Nginx    │
│ MySQL 8.0       │    │ MySQL 8.0       │    │ (Let's Encrypt) │
│ Port: 3307      │    │ Port: 3309      │    │ Port: 8080/8443 │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

## 🔒 Security Features

- **Isolated Networks**: Setiap app punya network sendiri
- **Shared Redis**: Aman untuk di-share karena stateless
- **Let's Encrypt**: SSL certificates otomatis
- **Non-root Users**: PHP containers run sebagai user `www`
- **Environment Variables**: Sensitive data di .env files

## 📊 Resource Usage

### Current Ports
- **App Bosowa**: localhost:8080
- **App Lain**: localhost:8080 (dengan Host: app-lain.localhost)
- **App Bosowa MySQL**: localhost:3307
- **App Lain MySQL**: localhost:3309
- **Shared Redis**: localhost:6379

### Memory Usage (Estimated)
- **PHP 8.3 Container**: ~150MB
- **PHP 8.2 Container**: ~120MB
- **MySQL 8.0 Container**: ~200MB each
- **Redis Container**: ~50MB
- **Nginx Container**: ~20MB

## 🛠️ Development Features

### PHP Extensions
- **Redis**: Untuk caching
- **PDO MySQL**: Database access
- **GD**: Image processing
- **ZIP**: File compression
- **Intl**: Internationalization
- **OpCache**: Performance optimization

### Development Tools
- **Git**: Version control
- **Composer**: PHP dependency management
- **.htaccess**: Apache-like rewrite rules (via Nginx)

## 🔄 Maintenance

### Daily Operations
```bash
# Check status
docker ps

# View logs
make logs

# Test connections
make test
```

### SSL Certificate Management
```bash
# Issue certificates (first time)
make issue-cert

# Renew certificates (cron job)
make renew-cert
```

### Backup & Restore
```bash
# Backup MySQL data
docker exec app-bosowa-mysql mysqldump -u app_bosowa -p app_bosowa > backup_bosowa.sql
docker exec app-lain-mysql mysqldump -u app_lain -p app_lain > backup_lain.sql

# Backup Redis data
docker exec shared-redis redis-cli BGSAVE
```

## 🚨 Troubleshooting

### Common Issues

1. **Port Conflicts**
   ```bash
   # Check port usage
   lsof -i :8080
   lsof -i :3307
   lsof -i :3309
   ```

2. **MySQL Connection Issues**
   ```bash
   # Check MySQL logs
   docker logs app-bosowa-mysql
   docker logs app-lain-mysql
   ```

3. **PHP Container Issues**
   ```bash
   # Check PHP logs
   docker logs app-bosowa-php
   docker logs app-lain-php
   ```

4. **Network Issues**
   ```bash
   # Check networks
   docker network ls
   docker network inspect shared-network
   ```

### Reset Everything
```bash
# Complete cleanup
make clean
make setup
make build
make up
```

## 📝 Notes

- **Hybrid Architecture**: Shared Redis + Isolated MySQL per app
- **Modular Design**: Setiap project punya config sendiri
- **Production Ready**: Let's Encrypt, health checks, proper networking
- **Development Friendly**: Hot reload, easy debugging, flexible configs
- **Scalable**: Mudah tambah project baru dengan config yang sama

## 🎯 Current Status

✅ **All Services Running**
- App Bosowa (PHP 8.3): http://localhost:8080
- App Lain (PHP 8.2): http://localhost:8080 (dengan Host: app-lain.localhost)
- App Bosowa MySQL: localhost:3307
- App Lain MySQL: localhost:3309
- Shared Redis: localhost:6379
- Shared Nginx: localhost:8080 (reverse proxy)

✅ **All Tests Passing**
- Redis connection: PONG
- App Bosowa MySQL: mysqld is alive
- App Lain MySQL: mysqld is alive
- PHP extensions: All loaded (Redis, PDO MySQL, GD, ZIP, Intl)
- Database connections: Working
- Static assets: Cached properly

✅ **Recent Fixes Applied**
- Fixed Docker Compose version warnings (removed obsolete version attribute)
- Fixed network configuration (shared-network as external)
- Fixed Nginx configuration for HTTP-only development
- Fixed port conflicts and host resolution issues
- All services now running without errors

---

**🎉 Ready for development! Open your browser and start coding!**
