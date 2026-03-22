# Backup System Setup Guide

## Overview

The SACCO Management System now includes a comprehensive backup solution that automatically backs up your database and uploaded files to both local storage and AWS S3 cloud storage.

## Features

- **Dual Storage**: Every backup is stored locally AND in AWS S3 for maximum redundancy
- **Automated Backups**: Weekly automated backups every Sunday at 2:00 AM
- **Manual Backups**: Create backups on-demand from the admin dashboard
- **One-Click Restore**: Restore from any backup with automatic pre-restore backup creation
- **Manual Deletion**: Full control over backup retention (no automatic cleanup)
- **Comprehensive Coverage**: Backs up MySQL database + all uploaded documents/files

## Installation Steps

### 1. Install AWS SDK for PHP

Run the following command in your project directory:

```bash
composer require league/flysystem-aws-s3-v3 "^3.0"
```

**Note**: If you encounter missing PHP extensions (xml, dom), install them first:

```bash
# For Ubuntu/Debian
sudo apt-get install php8.3-xml php8.3-dom php8.3-mbstring

# For other systems, adjust the PHP version accordingly
```

### 2. Configure AWS S3

#### Create an S3 Bucket

1. Log in to [AWS Console](https://console.aws.amazon.com/)
2. Navigate to **S3** service
3. Click **Create bucket**
4. Configure:
   - **Bucket name**: `sacco-system-backups` (or your preferred name)
   - **Region**: Choose your preferred region (e.g., `us-east-1`)
   - **Block all public access**: ✅ ENABLED (keep backups private)
   - Leave other settings as default
5. Click **Create bucket**

#### Create IAM User for Backups

1. Navigate to **IAM** service in AWS Console
2. Click **Users** → **Add users**
3. **User name**: `sacco-backup-user`
4. **Access type**: Select **Access key - Programmatic access**
5. Click **Next: Permissions**
6. Click **Attach existing policies directly**
7. Search and select **AmazonS3FullAccess** (or create a custom policy with S3 access)
8. Click **Next** through remaining steps
9. Click **Create user**
10. **IMPORTANT**: Copy the **Access Key ID** and **Secret Access Key** (you won't see the secret again!)

#### Configure Environment Variables

Edit your `.env` file and add/update the following:

```env
# AWS S3 Configuration
AWS_ACCESS_KEY_ID=your_access_key_id_here
AWS_SECRET_ACCESS_KEY=your_secret_access_key_here
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=sacco-system-backups
AWS_USE_PATH_STYLE_ENDPOINT=false

# Backup System Configuration
BACKUP_ENABLED=true
BACKUP_NOTIFICATION_EMAIL=admin@example.com
```

**Replace**:
- `your_access_key_id_here` with your actual AWS Access Key ID
- `your_secret_access_key_here` with your actual AWS Secret Access Key
- `sacco-system-backups` with your bucket name if different
- `us-east-1` with your chosen region
- `admin@example.com` with your admin email

### 3. Create Storage Directory

Ensure the local backup directory exists and has proper permissions:

```bash
mkdir -p storage/app/backups
chmod 755 storage/app/backups
```

### 4. Test the Backup System

#### Test Manual Backup via CLI

```bash
php artisan backup:create
```

You should see output like:
```
Starting backup process...
Creating backup of database and files...
✓ Backup created successfully!
  Filename: sacco_backup_2026-03-22_12-00-00.zip
  Size: 15.43 MB
  Location: Local storage + AWS S3
```

#### Test via Admin Dashboard

1. Log in as admin
2. Navigate to **Admin** → **Backups** (or visit `/admin/backups`)
3. Click **Create Backup Now**
4. Wait for the backup to complete
5. Verify the backup appears in the list with "Local + S3" badge

### 5. Verify S3 Upload

1. Go to AWS S3 Console
2. Open your backup bucket
3. Navigate to the `backups/` folder
4. Verify your backup file is present

## Usage

### Creating Backups

**Manual Backup (Admin Dashboard)**:
1. Go to `/admin/backups`
2. Click **Create Backup Now**
3. Wait for completion notification

**Manual Backup (CLI)**:
```bash
php artisan backup:create
```

**Automated Backups**:
- Runs automatically every Sunday at 2:00 AM
- No action required
- Ensure your server's cron is configured to run Laravel's scheduler:

```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

### Downloading Backups

1. Go to `/admin/backups`
2. Find the backup you want
3. Click the **Download** button (📥 icon)
4. The ZIP file will download to your computer

### Restoring from Backup

**⚠️ WARNING**: Restoring will replace ALL current data with the backup data!

1. Go to `/admin/backups`
2. Find the backup to restore
3. Click the **Restore** button (↩️ icon)
4. Type `RESTORE` in the confirmation dialog
5. Click **Restore Backup**
6. Wait for the process to complete
7. The system automatically creates a pre-restore backup for safety

### Deleting Backups

1. Go to `/admin/backups`
2. Find the backup to delete
3. Click the **Delete** button (🗑️ icon)
4. Confirm deletion
5. The backup is removed from both local storage and S3

## Backup Contents

Each backup includes:

- **Complete MySQL Database**: All tables and data from `saco_db`
- **Uploaded Documents**: All files in `storage/app/public/documents`
- **Uploaded Forms**: All files in `storage/app/public/uploaded_forms`

## Backup Schedule

- **Frequency**: Weekly (every Sunday)
- **Time**: 2:00 AM server time
- **Retention**: Manual deletion only (no automatic cleanup)
- **Storage**: Dual (Local + AWS S3)

## Disaster Recovery

If your server crashes completely:

1. Set up a fresh Laravel installation from Git
2. Configure `.env` with database credentials and AWS S3 credentials
3. Install dependencies: `composer install`
4. Run migrations: `php artisan migrate`
5. Access `/admin/backups`
6. Download the latest backup from S3 (or upload a local backup)
7. Click **Restore**
8. Your system is back to the backed-up state!

## Troubleshooting

### Backup Creation Fails

**Error**: "Database export failed"
- **Solution**: Ensure `mysqldump` is installed and accessible
- Check database credentials in `.env`
- Verify MySQL is running

**Error**: "Failed to upload backup to S3"
- **Solution**: Verify AWS credentials in `.env`
- Check S3 bucket exists and is accessible
- Verify IAM user has S3 permissions

### S3 Upload Not Working

1. Test AWS credentials:
```bash
php artisan tinker
>>> Storage::disk('s3')->put('test.txt', 'Hello World');
>>> Storage::disk('s3')->exists('test.txt');
```

2. Check bucket permissions in AWS Console
3. Verify region matches in `.env`

### Restore Fails

- **Solution**: Check that the backup file exists
- Ensure sufficient disk space
- Verify MySQL user has import permissions
- Check Laravel logs: `storage/logs/laravel.log`

### Scheduled Backups Not Running

1. Verify cron is configured:
```bash
crontab -l
```

2. Should see:
```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

3. Test scheduler manually:
```bash
php artisan schedule:list
```

## Security Best Practices

1. **Never commit `.env` to Git** - Contains AWS credentials
2. **Use IAM policies** - Grant minimum required S3 permissions
3. **Enable S3 encryption** - Encrypt backups at rest
4. **Rotate credentials** - Periodically update AWS access keys
5. **Monitor S3 costs** - Set up billing alerts in AWS
6. **Test restores regularly** - Verify backups are valid

## Cost Estimation

AWS S3 costs (approximate):
- **Storage**: ~$0.023 per GB/month (Standard tier)
- **Uploads**: Free
- **Downloads**: $0.09 per GB (first 10 TB)

Example: 52 weekly backups × 50 MB each = 2.6 GB
- Monthly storage cost: ~$0.06
- Annual storage cost: ~$0.72

## Support

For issues or questions:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Review this documentation
3. Contact system administrator

## Changelog

### Version 1.0 (March 2026)
- Initial backup system implementation
- AWS S3 integration
- Weekly automated backups
- Manual backup/restore functionality
- Admin dashboard interface
