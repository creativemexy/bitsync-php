# Database Setup Guide for BitSync Blog System

## Overview
This guide will help you set up the database for the dynamic blog system. The blog system requires database tables to store posts, categories, tags, and page content.

## Prerequisites
- PHP 7.4 or higher
- PDO and PostgreSQL extensions enabled
- Database credentials (CockroachDB or PostgreSQL)
- Access to your hosting environment

## Step 1: Configure Database Credentials

### Option A: Using .env file (Recommended)
1. Create or edit the `.env` file in your project root:
```bash
# Database Configuration
DB_HOST=your-database-host.com
DB_PORT=26257
DB_NAME=your-database-name
DB_USER=your-username
DB_PASSWORD=your-password
```

### Option B: Direct Configuration
If you can't use .env files, edit `config/database.php`:
```php
'cockroachdb' => [
    'host' => 'your-database-host.com',
    'port' => '26257',
    'database' => 'your-database-name',
    'username' => 'your-username',
    'password' => 'your-password',
    // ... other settings
],
```

## Step 2: Test Database Connection

1. Visit your website and go to: `/admin/test-db.php`
2. This will show you:
   - Database connection status
   - Required PHP extensions
   - Network connectivity
   - SSL connection status

## Step 3: Run Database Migration

### Method 1: Command Line (Recommended)
```bash
# Navigate to your project directory
cd /path/to/your/bitsync/project

# Run the migration
php database/migrate-blog.php
```

### Method 2: Web Interface
1. Create a temporary file called `run-migration.php` in your project root:
```php
<?php
// Temporary migration runner
require_once 'database/migrate-blog.php';
?>
```

2. Visit `http://yourdomain.com/run-migration.php`
3. Delete the file after successful migration

## Step 4: Verify Installation

After running the migration, you should see:
```
✅ Blog system database migration completed successfully!
📊 Created tables:
   - blog_categories
   - blog_posts
   - blog_tags
   - blog_post_tags
   - page_content
🔧 Added indexes for optimal performance
📝 Inserted default categories and sample posts
💾 Saved current page content to database
```

## Step 5: Access Admin Panel

1. Go to `/admin/login.php`
2. Use the default credentials:
   - Username: `admin`
   - Password: `admin123`
3. Navigate to "Blog Posts" to create your first post

## Troubleshooting

### Common Issues

#### 1. Database Connection Failed
**Error**: `SQLSTATE[08006] [7] connection to server failed`
**Solution**:
- Check your database credentials
- Verify network connectivity
- Ensure your hosting provider allows external database connections

#### 2. Missing PHP Extensions
**Error**: `PDO extension not loaded`
**Solution**:
- Contact your hosting provider to enable PDO and PostgreSQL extensions
- Check if OpenSSL is available for SSL connections

#### 3. Permission Denied
**Error**: `Permission denied` when creating tables
**Solution**:
- Ensure your database user has CREATE TABLE permissions
- Check if the database exists and is accessible

#### 4. SSL Connection Issues
**Error**: `SSL connection failed`
**Solution**:
- Try connecting without SSL first
- Check if your hosting provider supports SSL connections
- Verify SSL certificates if required

### Testing Your Setup

#### 1. Database Connection Test
Visit `/admin/test-db.php` to run comprehensive diagnostics.

#### 2. Blog Page Test
Visit `/blog` - you should see either:
- The blog with sample posts (if migration successful)
- A setup message with instructions (if tables don't exist)

#### 3. Admin Panel Test
Visit `/admin/login.php` and try logging in.

## Database Schema

The migration creates the following tables:

### blog_categories
- `id` (Primary Key)
- `name` (Category name)
- `slug` (URL-friendly name)
- `description` (Category description)
- `is_active` (Active status)
- `sort_order` (Display order)
- `created_at`, `updated_at` (Timestamps)

### blog_posts
- `id` (Primary Key)
- `title` (Post title)
- `slug` (URL-friendly title)
- `excerpt` (Post summary)
- `content` (Full post content)
- `featured_image` (Image URL)
- `category_id` (Foreign key to categories)
- `author_id` (Author reference)
- `status` (draft/published)
- `published_at` (Publication date)
- `meta_title`, `meta_description`, `meta_keywords` (SEO)
- `view_count` (View tracking)
- `is_featured` (Featured post flag)
- `is_active` (Active status)
- `created_at`, `updated_at` (Timestamps)

### blog_tags
- `id` (Primary Key)
- `name` (Tag name)
- `slug` (URL-friendly name)
- `is_active` (Active status)
- `created_at` (Timestamp)

### blog_post_tags
- `id` (Primary Key)
- `post_id` (Foreign key to posts)
- `tag_id` (Foreign key to tags)
- `created_at` (Timestamp)

### page_content
- `id` (Primary Key)
- `page_slug` (Page identifier)
- `page_title` (Page title)
- `page_description` (Page description)
- `content` (Page content)
- `meta_title`, `meta_description`, `meta_keywords` (SEO)
- `is_active` (Active status)
- `created_at`, `updated_at` (Timestamps)

## Security Notes

1. **Delete temporary files** after migration
2. **Change default admin password** after first login
3. **Use strong passwords** for database and admin accounts
4. **Enable SSL** for database connections in production
5. **Regular backups** of your database

## Support

If you encounter issues:

1. Check the error logs in your hosting control panel
2. Run the database test at `/admin/test-db.php`
3. Verify your database credentials
4. Contact your hosting provider for database access issues

## Next Steps

After successful setup:

1. **Create your first blog post** via the admin panel
2. **Organize content** with categories and tags
3. **Customize the design** to match your brand
4. **Set up SEO** with proper meta tags
5. **Monitor performance** and optimize as needed

The blog system is now ready to use! 🎉 