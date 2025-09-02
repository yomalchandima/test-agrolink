# AgroLink Database & PHP Setup Instructions

## 🗄️ Database Setup

### 1. Create Database
1. Open your MySQL/phpMyAdmin
2. Create a new database named `agrolink_db`
3. Import the schema from `database/schema.sql`

### 2. Configure Database Connection
Edit `config/database.php` with your database credentials:

```php
$host = 'localhost';
$dbname = 'agrolink_db'; // Your database name
$username = 'root'; // Your database username
$password = ''; // Your database password
```

## 🐘 PHP Requirements

### Server Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- PDO MySQL extension enabled

### PHP Extensions Required
- PDO
- PDO_MySQL
- JSON
- Session

## 📁 File Structure

```
AgroLink/
├── config/
│   └── database.php          # Database connection
├── auth/
│   ├── login.php            # Login handler
│   ├── register.php         # Registration handler
│   ├── logout.php           # Logout handler
│   └── check_session.php    # Session validation
├── database/
│   └── schema.sql           # Database schema
├── assets/
│   ├── css/
│   ├── js/
│   └── img/
├── *.html                   # Frontend pages
└── SETUP_INSTRUCTIONS.md    # This file
```

## 🔧 Installation Steps

### 1. Database Setup
```bash
# Import database schema
mysql -u your_username -p agrolink_db < database/schema.sql
```

### 2. Configure Database
Update `config/database.php` with your credentials.

### 3. Test Connection
Visit `auth/test_connection.php` to verify database connectivity.

## 👥 Default Demo Accounts

After importing the schema, these demo accounts will be available:

| Role | Email | Password | Dashboard |
|------|-------|----------|-----------|
| Admin | admin@agrolink.com | demo123 | dashboard_admin.html |
| Farmer | farmer@demo.com | demo123 | dashboard_farmer.html |
| Buyer | buyer@demo.com | demo123 | dashboard_buyer.html |
| Transporter | transporter@demo.com | demo123 | dashboard_transporter.html |

## 🔐 Security Features

### Password Hashing
- All passwords are hashed using PHP's `password_hash()` function
- Uses `PASSWORD_DEFAULT` algorithm (bcrypt)

### Session Management
- Sessions are created on successful login
- Session timeout: 24 hours
- Secure session handling with proper cleanup

### Input Validation
- Email format validation
- Password strength requirements (minimum 6 characters)
- SQL injection prevention using prepared statements
- XSS protection through input sanitization

## 📊 Database Schema

### Tables Created
1. **users** - All user accounts
2. **products** - Product listings
3. **orders** - Order management
4. **order_items** - Individual order items
5. **cart** - Shopping cart
6. **wishlist** - User wishlists
7. **reviews** - Product reviews
8. **notifications** - User notifications
9. **user_preferences** - User settings

### Key Features
- Foreign key relationships for data integrity
- Timestamps for audit trails
- Soft delete support (is_active flag)
- Email verification support

## 🚀 Usage

### Registration
1. Users can register as Farmer, Buyer, or Transporter
2. All required fields are validated
3. Email uniqueness is enforced
4. Default preferences are created automatically

### Login
1. Users must provide email, password, and role
2. Role-based authentication
3. Session creation with user data
4. Automatic redirect to appropriate dashboard

### Logout
1. Session destruction
2. Cookie cleanup
3. Redirect to login page

## 🔧 Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Check database credentials in `config/database.php`
   - Verify MySQL service is running
   - Ensure database exists

2. **Registration Fails**
   - Check if email already exists
   - Verify password meets requirements
   - Check PHP error logs

3. **Login Fails**
   - Verify email and role combination
   - Check if account is active
   - Ensure password is correct

4. **Session Issues**
   - Check PHP session configuration
   - Verify session directory permissions
   - Clear browser cookies

### Debug Mode
To enable debug mode, add this to your PHP files:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📝 API Endpoints

### Authentication
- `POST auth/register.php` - User registration
- `POST auth/login.php` - User login
- `GET auth/logout.php` - User logout

### Response Format
```json
{
    "success": true/false,
    "message": "Response message",
    "data": {} // Optional additional data
}
```

## 🔄 Next Steps

1. **Email Verification**: Implement email verification system
2. **Password Reset**: Add password reset functionality
3. **Profile Management**: Connect profile updates to database
4. **Product Management**: Implement product CRUD operations
5. **Order System**: Connect order management to database

## 📞 Support

For issues or questions:
- Check PHP error logs
- Verify database connectivity
- Test with demo accounts first
- Review browser console for JavaScript errors
