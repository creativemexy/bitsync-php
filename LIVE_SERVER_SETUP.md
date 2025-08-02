# Live Server Database Connection Fix

## 🔍 **Problem**
You can't log in to the admin page on your live server because it says "database connection failed".

## 🚨 **Emergency Access**
**You can still access the admin panel using fallback credentials:**
- **Username:** `admin`
- **Password:** `admin123`

## 🔧 **Step-by-Step Solution**

### **Step 1: Run the Diagnostic**
1. Go to: `https://yourdomain.com/admin/live-server-diagnostic.php`
2. This will show you exactly what's wrong

### **Step 2: Common Issues & Fixes**

#### **Issue 1: Missing .env file**
**Symptoms:** "Environment file: File not found"
**Solution:**
1. Create a `.env` file in your root directory
2. Add your database credentials:
```env
DB_HOST=tangy-spirit-7966.jxf.cockroachlabs.cloud
DB_PORT=26257
DB_NAME=ken
DB_USER=demilade
DB_PASSWORD=your_password_here
```

#### **Issue 2: Missing database config**
**Symptoms:** "Database config: File not found"
**Solution:**
1. Check if `config/database.php` exists
2. If missing, upload it from your local version

#### **Issue 3: Network connectivity**
**Symptoms:** "Network connection failed"
**Solution:**
1. Contact your hosting provider
2. Ask them to allow outbound connections to port 26257
3. Verify they support PostgreSQL connections

#### **Issue 4: Missing PHP extensions**
**Symptoms:** "pdo_pgsql extension not loaded"
**Solution:**
1. Contact your hosting provider
2. Ask them to enable PDO and PostgreSQL extensions

### **Step 3: Test the Fix**
1. After making changes, visit: `https://yourdomain.com/admin/live-server-diagnostic.php`
2. All tests should show ✅ green checkmarks
3. Try logging in to admin panel

### **Step 4: Alternative Solutions**

#### **Option A: Use Fallback Authentication**
If database connection can't be fixed immediately:
1. Use fallback credentials: `admin` / `admin123`
2. This will work even without database connection
3. You can still access admin dashboard

#### **Option B: Local Database**
If hosting provider doesn't support external databases:
1. Set up a local database on your hosting
2. Update `.env` file with local database credentials
3. Run database migrations locally

#### **Option C: Different Hosting Provider**
If current provider has restrictions:
1. Consider a provider that supports PostgreSQL
2. Popular options: DigitalOcean, AWS, Vultr
3. Many support outbound database connections

## 📋 **Files to Check**

### **Required Files:**
- ✅ `.env` - Database credentials
- ✅ `config/database.php` - Database configuration
- ✅ `includes/Database.php` - Database class
- ✅ `includes/User.php` - User authentication

### **File Permissions:**
- `.env` should be readable (644)
- Other files should be readable (644)
- Directories should be executable (755)

## 🆘 **Still Having Issues?**

### **Contact Your Hosting Provider:**
1. **Ask about PostgreSQL support**
2. **Request PDO extensions**
3. **Check outbound connection policies**
4. **Verify SSL certificate requirements**

### **Use Diagnostic Tool:**
Visit: `https://yourdomain.com/admin/live-server-diagnostic.php`
This will show exactly what's wrong and how to fix it.

## ✅ **Success Indicators**
When everything is working:
- ✅ Database connection successful
- ✅ Users table accessible
- ✅ Admin login works
- ✅ All admin features functional

## 🚀 **Quick Fix Summary**
1. **Run diagnostic:** `/admin/live-server-diagnostic.php`
2. **Create .env file** with database credentials
3. **Contact hosting provider** if network/extensions fail
4. **Use fallback login:** `admin` / `admin123` for emergency access 