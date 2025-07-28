# BitSync Group - CockroachDB Setup Guide

This guide will help you set up CockroachDB for the BitSync Group project and migrate from the current JSON file-based system to a robust database solution.

## 🚀 Quick Start

### Prerequisites
- Docker and Docker Compose installed
- PHP 7.4+ with PDO and PostgreSQL extensions
- Git

### 1. Clone and Setup
```bash
git clone <your-repo>
cd bitsync
```

### 2. Environment Configuration
```bash
cp env.example .env
# Edit .env with your database settings
```

### 3. Run Setup Script
```bash
php setup.php
```

This will:
- Start CockroachDB in Docker
- Create the database schema
- Migrate existing JSON content
- Set up admin user
- Configure system settings

## 📊 Database Schema

### Core Tables

#### `content_pages`
Stores all page content with JSONB for flexible content structure
- `id` (UUID) - Primary key
- `page_key` (VARCHAR) - Unique page identifier
- `title` (VARCHAR) - Page title
- `content` (JSONB) - Page content in JSON format
- `is_published` (BOOLEAN) - Publication status

#### `users`
Admin user management
- `id` (UUID) - Primary key
- `username` (VARCHAR) - Unique username
- `email` (VARCHAR) - User email
- `password_hash` (VARCHAR) - Hashed password
- `role` (VARCHAR) - User role (admin, editor, etc.)

#### `newsletter_subscribers`
Email newsletter management
- `id` (UUID) - Primary key
- `email` (VARCHAR) - Subscriber email
- `is_active` (BOOLEAN) - Subscription status
- `subscribed_at` (TIMESTAMP) - Subscription date

#### `contact_submissions`
Contact form submissions
- `id` (UUID) - Primary key
- `name` (VARCHAR) - Contact name
- `email` (VARCHAR) - Contact email
- `message` (TEXT) - Contact message
- `created_at` (TIMESTAMP) - Submission date

#### `services`
Service offerings management
- `id` (UUID) - Primary key
- `slug` (VARCHAR) - URL-friendly identifier
- `title` (VARCHAR) - Service title
- `content` (JSONB) - Service details
- `is_featured` (BOOLEAN) - Featured service flag

#### `case_studies`
Client case studies
- `id` (UUID) - Primary key
- `slug` (VARCHAR) - URL-friendly identifier
- `title` (VARCHAR) - Case study title
- `client_name` (VARCHAR) - Client name
- `results` (JSONB) - Project results

#### `blog_posts`
Blog content management
- `id` (UUID) - Primary key
- `slug` (VARCHAR) - URL-friendly identifier
- `title` (VARCHAR) - Post title
- `content` (TEXT) - Post content
- `author_id` (UUID) - Author reference
- `is_published` (BOOLEAN) - Publication status

#### `job_openings`
Career opportunities
- `id` (UUID) - Primary key
- `title` (VARCHAR) - Job title
- `description` (TEXT) - Job description
- `requirements` (JSONB) - Job requirements
- `is_active` (BOOLEAN) - Active posting status

#### `system_settings`
Application configuration
- `id` (UUID) - Primary key
- `setting_key` (VARCHAR) - Setting name
- `setting_value` (TEXT) - Setting value
- `setting_type` (VARCHAR) - Value type (string, boolean, json, number)

## 🔧 Database Operations

### Using the ContentManager Class

```php
require_once 'includes/ContentManager.php';

$contentManager = new ContentManager();

// Get page content
$homeContent = $contentManager->getPageContent('home');

// Save page content
$contentManager->savePageContent('about', [
    'hero_title' => 'About BitSync',
    'hero_description' => 'Leading technology solutions'
]);

// Get system setting
$siteName = $contentManager->getSetting('site_name', 'BitSync Group');

// Save system setting
$contentManager->saveSetting('contact_email', 'info@bitsync.com');
```

### Using the Database Class Directly

```php
require_once 'includes/Database.php';

$db = Database::getInstance();

// Simple query
$users = $db->fetchAll("SELECT * FROM users WHERE is_active = ?", [true]);

// Insert data
$userId = $db->insert('users', [
    'username' => 'newuser',
    'email' => 'user@example.com',
    'password_hash' => password_hash('password', PASSWORD_DEFAULT)
]);

// Update data
$db->update('users', ['is_active' => false], 'id = ?', [$userId]);

// Delete data
$db->delete('users', 'id = ?', [$userId]);
```

## 🛠️ Management Commands

### Start/Stop Database
```bash
# Start CockroachDB
docker-compose up -d

# Stop CockroachDB
docker-compose down

# View logs
docker-compose logs cockroachdb
```

### Database Access
```bash
# Connect to database
docker exec -it bitsync-cockroach cockroach sql --insecure

# Access admin UI
# Open http://localhost:8080 in your browser
```

### Backup and Restore
```bash
# Create backup
php database/backup.php

# Restore from backup
php database/restore.php backup-file.sql
```

## 🔒 Security Considerations

### Production Setup
1. **Enable SSL**: Update `.env` with SSL certificates
2. **Strong Passwords**: Use strong admin passwords
3. **Network Security**: Restrict database access
4. **Regular Backups**: Set up automated backups
5. **Updates**: Keep CockroachDB updated

### Environment Variables
```bash
# Required for production
DB_SSL_ENABLED=true
DB_SSL_CA=/path/to/ca.crt
DB_SSL_CERT=/path/to/client.crt
DB_SSL_KEY=/path/to/client.key
SESSION_SECRET=your-very-long-secret-key
```

## 📈 Performance Optimization

### Indexes
The schema includes optimized indexes for:
- Page lookups by key
- User authentication
- Content filtering
- Search operations

### Connection Pooling
Consider implementing connection pooling for high-traffic applications.

### Caching
Implement Redis or Memcached for frequently accessed content.

## 🔄 Migration from JSON Files

The setup script automatically migrates existing JSON content:

1. **Content Migration**: JSON files in `/content` are imported
2. **Settings Migration**: System settings are preserved
3. **Backup Creation**: Original files are backed up

### Manual Migration
```php
require_once 'includes/ContentManager.php';

$contentManager = new ContentManager();

// Migrate specific page
$jsonContent = json_decode(file_get_contents('content/home.json'), true);
$contentManager->savePageContent('home', $jsonContent);

// Backup to JSON (for compatibility)
$backupDir = $contentManager->backupToJson();
```

## 🐛 Troubleshooting

### Common Issues

**Database Connection Failed**
```bash
# Check if CockroachDB is running
docker ps | grep cockroach

# Check logs
docker-compose logs cockroachdb

# Restart container
docker-compose restart cockroachdb
```

**Permission Denied**
```bash
# Fix file permissions
sudo chown -R $USER:$USER .
chmod -R 755 .
```

**Migration Errors**
```bash
# Reset database
docker-compose down -v
docker-compose up -d
php setup.php
```

### Logs and Debugging
```bash
# View application logs
tail -f /var/log/apache2/error.log

# View database logs
docker-compose logs -f cockroachdb

# Test database connection
php -r "require 'includes/Database.php'; \$db = Database::getInstance(); echo 'Connected!';"
```

## 📚 Additional Resources

- [CockroachDB Documentation](https://www.cockroachlabs.com/docs/)
- [PHP PDO Documentation](https://www.php.net/manual/en/book.pdo.php)
- [Docker Compose Documentation](https://docs.docker.com/compose/)

## 🤝 Support

For issues specific to this implementation:
1. Check the troubleshooting section
2. Review the logs
3. Create an issue in the project repository

For CockroachDB issues:
- [CockroachDB Community](https://community.cockroachlabs.com/)
- [CockroachDB GitHub](https://github.com/cockroachdb/cockroach) 