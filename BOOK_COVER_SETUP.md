# Book Cover Images Setup Instructions

## New Features Added

✅ **Book Cover Images in Shop Page**
- Books now display cover images in the shop page
- Automatic fallback to placeholder design if no cover image exists
- Hover effects and professional styling

✅ **Admin Image Upload**
- Admin can upload book cover images when adding new books through "Manage Inventory"
- Support for JPG, PNG, and WEBP formats
- Image preview before adding
- File size limit: 5MB

✅ **Database Updates**
- Added `cover_image` column to books table
- Updated database.sql for new installations
- Created update_database.php for existing databases

## Setup Instructions

### 1. Update Existing Database
If you have an existing database, run this in your web browser:
```
http://localhost/chandrani-book-shop/update_database.php
```

### 2. Admin Login Credentials
- **Email:** adminbook@gmail.com
- **Password:** adminbook

### 3. Adding Books with Images
1. Login as admin
2. Go to Admin Dashboard
3. Click "Manage Inventory" 
4. Fill in book details including category
5. Upload a book cover image (optional)
6. Preview the image before saving
7. Click "Add Book"

### 4. File Structure
```
chandrani-book-shop/
├── images/                 (NEW - for book covers)
├── css/style.css          (UPDATED - new cover styles)
├── shop.php               (UPDATED - displays covers)
├── admin_dashboard.php    (UPDATED - upload form)
├── add_book.php           (UPDATED - handles uploads)
├── database.sql           (UPDATED - cover_image column)
└── update_database.php    (NEW - for existing DBs)
```

### 5. Image Requirements
- **Formats:** JPG, PNG, WEBP
- **Size:** Max 5MB
- **Recommended dimensions:** 300x450px (book cover ratio)
- **Naming:** System auto-generates safe filenames

### 6. Features
- **Shop Page:** Shows book covers with fallback placeholders
- **Admin Panel:** Easy image upload with preview
- **File Management:** Automatic cleanup on errors
- **Security:** File type validation and size limits
- **Responsive:** Works on all screen sizes

## Testing the Features

1. **View Shop Page:** Visit shop.php to see existing books (will show placeholders initially)
2. **Add New Book:** Login as admin and add a book with cover image
3. **Check Upload:** Verify image appears in /images folder
4. **View Result:** Check shop page to see the new book with its cover

## Troubleshooting

### Images Not Showing
- Check if /images folder exists and has write permissions
- Verify file was uploaded successfully
- Check browser console for errors

### Upload Errors
- Ensure file is under 5MB
- Use only JPG, PNG, or WEBP formats
- Check server upload_max_filesize settings

### Database Errors
- Run update_database.php if using existing database
- Verify database connection in includes/db.php
- Check if cover_image column exists in books table

## Future Enhancements

- Image resizing/compression
- Multiple image support
- Bulk upload functionality
- Image editing tools
- CDN integration for better performance

The system is now ready for production use with professional book cover image support!
