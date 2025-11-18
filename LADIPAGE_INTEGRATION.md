# Ladipage Integration - Implementation Summary

## Overview
This document describes the implementation of Ladipage integration for the BrandTree Laravel application. The integration allows Ladipage to publish content directly to the website via API.

## Database Structure

### Tables Created
1. **page_contents** - Main table for storing page metadata
   - `id` - Primary key
   - `code` - Ladipage ID (unique identifier from Ladipage)
   - `type` - Enum: 'page' or 'ladipage'
   - `status` - Enum: 'draft' or 'published'
   - `created_at`, `updated_at` - Timestamps

2. **page_contents_translations** - Translation table for multilingual content
   - `id` - Primary key
   - `page_content_id` - Foreign key to page_contents
   - `locale` - Language code (e.g., 'vi', 'en')
   - `title` - Page title
   - `slug` - URL slug (must be unique per locale)
   - `content` - HTML content from Ladipage
   - Unique constraint on (`page_content_id`, `locale`)

## Models

### PageContent Model
- Uses `Astrotomic\Translatable\Translatable` trait for multilingual support
- Translatable fields: `title`, `slug`, `content`
- Fillable fields: `code`, `type`, `status`

### PageContentTranslation Model
- No timestamps
- Fillable fields: `page_content_id`, `locale`, `title`, `slug`, `content`

## API Endpoint

### Route
```
POST /api/ladipage/store
```

### Request Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| secret_key | string | Yes | Secret key for authentication (from .env) |
| api_key | string | Yes | API key for authentication (from .env) |
| title | string | Yes | Page title |
| slug | string | Yes | URL slug (must be valid format: lowercase, numbers, hyphens) |
| html | string | Yes | HTML content from Ladipage |
| ladiID | string | Yes | Unique Ladipage page identifier |

### Response Format

**Success (200):**
```json
{
    "code": 200,
    "id": 1,
    "url": "http://example.com/test-page.html"
}
```

**Error (401/500):**
```json
{
    "code": 401,
    "msg": "Error message in Vietnamese"
}
```

## Validation Rules

1. **Secret Key Validation**: Must match `LADIPAGE_SECRET_KEY` from .env
2. **API Key Validation**: Must match `LADIPAGE_API_KEY` from .env
3. **Required Fields**: title, slug, html, ladiID must not be empty
4. **Slug Format**: Must match pattern `^[a-z0-9]+(?:-[a-z0-9]+)*$` (lowercase letters, numbers, hyphens only)
5. **Slug Uniqueness**: 
   - For new pages: Slug must not exist in Vietnamese locale
   - For updates: Slug must not exist for other pages in Vietnamese locale

## Business Logic

### Creating New Page
1. Check if ladiID already exists
2. If not exists:
   - Validate slug is unique for Vietnamese locale
   - Create new `PageContent` record with type='ladipage', status='published'
   - Create Vietnamese translation with title, slug, and HTML content
   - Return success with page ID and URL

### Updating Existing Page
1. Find page by ladiID
2. If exists:
   - Validate slug is unique (excluding current page) for Vietnamese locale
   - Update Vietnamese translation with new title, slug, and HTML content
   - Set status to 'published'
   - Return success with page ID and URL

## Configuration

### Environment Variables (.env)
```env
LADIPAGE_SECRET_KEY=2V6SYPcwY5b5dcXM
LADIPAGE_API_KEY=FNp2GrcNJDZvc0rMgmKn0cJh4QIIa5hLg3oqod0AKs
```

### Config File (config/app.php)
```php
'ladipage' => [
    'secret_key' => env('LADIPAGE_SECRET_KEY', ''),
    'api_key' => env('LADIPAGE_API_KEY', ''),
],
```

## Error Messages

| Code | Message | Description |
|------|---------|-------------|
| 401 | Secret Key không hợp lệ | Invalid secret key |
| 401 | Mã tích hợp không hợp lệ | Invalid API key |
| 401 | content empty | Missing required fields (title, slug, or html) |
| 401 | slug invalid | Slug format is invalid |
| 401 | ladiID empty | Missing ladiID parameter |
| 401 | Slug đã tồn tại | Slug already exists for another page |
| 500 | Lỗi khi lưu dữ liệu: {error} | Database error during save |

## Testing

A test script is provided in `test_ladipage.php` to verify:
1. Secret key validation
2. API key validation
3. Required field validation
4. Slug format validation
5. New page creation
6. Existing page update
7. Duplicate slug detection

Run the test:
```bash
php test_ladipage.php
```

## Files Modified/Created

### Created:
- `app/Http/Controllers/LadipageController.php` - Main controller
- `app/Models/PageContent.php` - Page content model
- `app/Models/PageContentTranslation.php` - Translation model
- `database/migrations/2025_11_17_113815_create_page_content_table.php` - Database migration
- `test_ladipage.php` - Test script
- `LADIPAGE_INTEGRATION.md` - This documentation

### Modified:
- `routes/web.php` - Added API route
- `config/app.php` - Added Ladipage configuration
- `.env` - Added Ladipage keys

## Next Steps

1. Configure Ladipage webhook to point to: `https://yourdomain.com/api/ladipage/store`
2. Add the secret_key and api_key parameters to Ladipage webhook configuration
3. Test the integration with a real Ladipage form
4. Create frontend views to display the published pages
5. Add admin interface to manage published pages (optional)

