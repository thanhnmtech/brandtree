# Laravel 12 Authentication System

This Laravel 12 project includes a comprehensive authentication system with the following features:

## Features

1. **User Registration with OTP Email Verification**
2. **User Login (Email/Password)**
3. **Google OAuth Login**
4. **Forgot Password with OTP Email**
5. **Email Verification via OTP**

## Installation & Setup

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Environment Configuration

Update your `.env` file with the following configurations:

#### Database Configuration
The project is pre-configured with SQLite:
```env
DB_CONNECTION=sqlite
```

#### Mail Configuration
Configure your mail settings for OTP emails:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

**Note:** For Gmail, you need to create an App Password:
1. Go to your Google Account settings
2. Navigate to Security > 2-Step Verification
3. Scroll down to "App passwords"
4. Generate a new app password for "Mail"
5. Use this password in `MAIL_PASSWORD`

#### Google OAuth Configuration
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable Google+ API
4. Go to Credentials > Create Credentials > OAuth 2.0 Client ID
5. Configure the OAuth consent screen
6. Add authorized redirect URIs:
   - `http://localhost:8000/auth/google/callback` (for local development)
   - `https://yourdomain.com/auth/google/callback` (for production)
7. Copy the Client ID and Client Secret

Add to your `.env`:
```env
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

### 3. Run Migrations

```bash
php artisan migrate
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Build Assets (Optional)

```bash
npm run build
```

### 6. Start the Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Features Documentation

### 1. User Registration with OTP Verification

**Flow:**
1. User fills out the registration form (name, email, password)
2. System creates the user account
3. An OTP (6-digit code) is generated and sent to the user's email
4. User is redirected to OTP verification page
5. User enters the OTP code
6. Upon successful verification, the email is verified and user is logged in

**Routes:**
- `GET /register` - Registration form
- `POST /register` - Process registration
- `GET /verify-otp` - OTP verification form
- `POST /verify-otp` - Verify OTP
- `POST /resend-otp` - Resend OTP

### 2. User Login

**Flow:**
1. User enters email and password
2. System authenticates the user
3. User is redirected to dashboard

**Routes:**
- `GET /login` - Login form
- `POST /login` - Process login

### 3. Google OAuth Login

**Flow:**
1. User clicks "Login with Google" button
2. User is redirected to Google for authentication
3. After successful authentication, Google redirects back to the application
4. System checks if user exists:
   - If user exists with Google ID: Log them in
   - If user exists with email: Link Google account and log them in
   - If new user: Create account and log them in
5. Email is automatically verified for Google users

**Routes:**
- `GET /auth/google` - Redirect to Google
- `GET /auth/google/callback` - Handle Google callback

### 4. Forgot Password with OTP

**Flow:**
1. User clicks "Forgot Password" on login page
2. User enters their email address
3. System sends OTP to the email
4. User enters the OTP code
5. Upon successful verification, user is shown password reset form
6. User enters new password
7. Password is updated and user is redirected to login

**Routes:**
- `GET /forgot-password-otp` - Forgot password form
- `POST /forgot-password-otp` - Send OTP
- `GET /verify-password-otp` - OTP verification form
- `POST /verify-password-otp` - Verify OTP
- `POST /resend-password-otp` - Resend OTP
- `GET /reset-password-otp` - Reset password form
- `POST /reset-password-otp` - Update password

## Database Schema

### Users Table
The users table includes the following additional fields:
- `otp` - Stores the current OTP code
- `otp_expires_at` - OTP expiration timestamp (10 minutes from generation)
- `google_id` - Google OAuth user ID
- `email_verified_at` - Email verification timestamp

## Code Structure

### Models
- `app/Models/User.php` - User model with HasOtp trait

### Traits
- `app/Traits/HasOtp.php` - OTP generation and verification logic

### Controllers
- `app/Http/Controllers/Auth/RegisteredUserController.php` - Registration with OTP
- `app/Http/Controllers/Auth/OtpVerificationController.php` - OTP verification
- `app/Http/Controllers/Auth/OtpPasswordResetController.php` - Password reset with OTP
- `app/Http/Controllers/Auth/GoogleAuthController.php` - Google OAuth

### Mail
- `app/Mail/OtpMail.php` - OTP email template

### Views
- `resources/views/auth/register.blade.php` - Registration form
- `resources/views/auth/login.blade.php` - Login form
- `resources/views/auth/verify-otp.blade.php` - OTP verification form
- `resources/views/auth/forgot-password-otp.blade.php` - Forgot password form
- `resources/views/auth/verify-password-otp.blade.php` - Password reset OTP verification
- `resources/views/auth/reset-password-otp.blade.php` - Reset password form
- `resources/views/emails/otp.blade.php` - OTP email template

## Security Features

1. **OTP Expiration**: OTPs expire after 10 minutes
2. **Password Hashing**: All passwords are hashed using bcrypt
3. **CSRF Protection**: All forms include CSRF tokens
4. **Session Management**: Secure session handling
5. **Email Verification**: Users must verify their email via OTP
6. **Rate Limiting**: Built-in Laravel rate limiting on authentication routes

## Testing

To test the authentication system:

1. **Registration with OTP:**
   - Register a new user
   - Check your email for the OTP
   - Enter the OTP to verify your account

2. **Login:**
   - Use your email and password to log in

3. **Google OAuth:**
   - Click "Login with Google"
   - Authenticate with your Google account

4. **Forgot Password:**
   - Click "Forgot Password"
   - Enter your email
   - Check your email for the OTP
   - Enter the OTP
   - Set a new password

## Troubleshooting

### Email not sending
- Check your mail configuration in `.env`
- For Gmail, ensure you're using an App Password, not your regular password
- Check Laravel logs: `storage/logs/laravel.log`

### Google OAuth not working
- Verify your Google Client ID and Secret
- Ensure the redirect URI matches exactly in Google Console
- Check that the Google+ API is enabled

### OTP expired
- OTPs expire after 10 minutes
- Use the "Resend OTP" button to get a new code

## Production Deployment

Before deploying to production:

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false` in `.env`
3. Update `APP_URL` to your production domain
4. Update Google OAuth redirect URI to production URL
5. Use a production mail service (e.g., SendGrid, Mailgun, Amazon SES)
6. Run `php artisan config:cache`
7. Run `php artisan route:cache`
8. Run `php artisan view:cache`

## License

This project is open-sourced software licensed under the MIT license.

