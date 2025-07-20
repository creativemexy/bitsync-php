# BitSync Admin - Serverless CMS

A lightweight, file-based content management system that allows you to update website content without a database.

## Features

- **No Database Required** - Content is stored in JSON files
- **Secure Admin Panel** - Password-protected access
- **Real-time Updates** - Changes appear immediately on the website
- **Automatic Backups** - Previous versions are saved automatically
- **Responsive Design** - Works on all devices
- **Easy to Use** - Simple form-based editing

## Setup

1. **Access the Admin Panel**
   - Navigate to `/admin/` in your browser
   - Default credentials:
     - Username: `admin`
     - Password: `bitsync2024`

2. **Change Default Password**
   - Edit `admin/config.php`
   - Update the `ADMIN_PASSWORD` constant

3. **Directory Structure**
   ```
   admin/
   ├── config.php          # Configuration and functions
   ├── index.php           # Login page
   ├── dashboard.php       # Main admin interface
   └── README.md           # This file
   
   content/                # Generated automatically
   ├── home.json          # Home page content
   ├── about.json         # About page content
   ├── services.json      # Services content
   └── contact.json       # Contact information
   
   backups/               # Generated automatically
   └── [timestamped backups]
   ```

## How It Works

1. **Content Storage**: All content is stored in JSON files in the `content/` directory
2. **Dynamic Loading**: The website loads content from these JSON files
3. **Fallback System**: If no custom content exists, default content is used
4. **Automatic Backups**: Every change creates a timestamped backup

## Managing Content

### Home Page
- Hero title, subtitle, and description
- Statistics (clients, countries, projects, support)

### About Page
- Mission and vision statements
- Company values

### Services Page
- Service titles and descriptions
- Feature lists for each service

### Contact Page
- Email, phone, address
- Business hours

## Security

- **Password Protection**: Admin panel requires authentication
- **Session Management**: Secure PHP sessions
- **Input Validation**: All user input is sanitized
- **File Permissions**: Content files are protected

## Customization

### Adding New Pages
1. Add the page to the navigation in `includes/header.php`
2. Create a new page file in `pages/`
3. Add content structure to `admin/config.php`
4. Update the admin dashboard to handle the new page

### Adding New Content Fields
1. Update the content structure in `admin/config.php`
2. Add form fields to `admin/dashboard.php`
3. Update the content loader in `includes/content.php`
4. Use the new content in your page templates

## Backup and Restore

### Automatic Backups
- Every content update creates a backup
- Backups are stored in `backups/` directory
- Format: `page_YYYY-MM-DD_HH-MM-SS.json`

### Manual Backup
```bash
# Backup all content
cp -r content/ backups/manual_backup_$(date +%Y%m%d_%H%M%S)/

# Restore from backup
cp backups/page_2024-01-15_14-30-00.json content/page.json
```

## Troubleshooting

### Common Issues

1. **Can't Access Admin Panel**
   - Check file permissions (755 for directories, 644 for files)
   - Verify PHP is enabled
   - Check for syntax errors in config files

2. **Content Not Updating**
   - Check file permissions on `content/` directory
   - Verify JSON syntax in content files
   - Clear browser cache

3. **Login Issues**
   - Verify username/password in `config.php`
   - Check session configuration
   - Clear browser cookies

### File Permissions
```bash
# Set correct permissions
chmod 755 admin/
chmod 644 admin/*.php
chmod 755 content/
chmod 644 content/*.json
chmod 755 backups/
```

## Performance

- **Lightweight**: No database queries
- **Fast Loading**: JSON files load quickly
- **Caching Ready**: Easy to implement caching
- **CDN Compatible**: Static content can be cached

## Future Enhancements

- [ ] Image upload functionality
- [ ] Rich text editor
- [ ] Content versioning
- [ ] User management
- [ ] API endpoints
- [ ] Content scheduling
- [ ] SEO management
- [ ] Analytics integration

## Support

For issues or questions:
1. Check the troubleshooting section
2. Review file permissions
3. Check PHP error logs
4. Verify JSON syntax in content files

---

**BitSync Admin CMS** - Simple, secure, serverless content management. 