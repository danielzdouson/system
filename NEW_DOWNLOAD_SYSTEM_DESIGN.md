# New Download & Upload System Design

## 🎯 OBJECTIVES

1. **Fine Every Download** - Each document download incurs a fine
2. **10-Day Upload Window** - Members have 10 days to upload after downloading
3. **Form Expiration** - Prevent using old forms for new loans
4. **One Upload Per Download** - Each download allows only one upload

## 📋 SYSTEM LOGIC

### **Download Process:**
1. Member clicks download
2. System checks if fine required (always YES for loan forms)
3. Apply fine for THIS download
4. Create download record with `upload_window_expires_at` (10 days from now)
5. Allow file download
6. Track that this download can be used for ONE upload

### **Upload Process:**
1. Member tries to upload completed form
2. System checks if member has valid download record
3. Check if `upload_window_expires_at` > now()
4. Check if download hasn't been used for upload yet
5. If all valid: Allow upload and mark download as used
6. If invalid: Show error message

### **Validation Rules:**
- **No valid download**: Must download fresh form first
- **Expired window**: Must download fresh form (10 days expired)
- **Already used**: Must download fresh form for new loan

## 🗄️ DATABASE CHANGES NEEDED

### **DocumentDownload Table - Add Columns:**
```sql
- upload_window_expires_at (datetime) - When upload window closes
- used_for_upload (boolean) - If this download was used for upload
- fine_amount (decimal) - Amount of fine charged for this download
- download_purpose (enum) - 'loan_application', 'general', etc.
```

### **UploadedForm Table - Add Columns:**
```sql
- document_download_id (foreign_key) - Link to the download record
- form_download_date (datetime) - When the form was originally downloaded
```

## 🔄 USER FLOW

### **New Loan Application Flow:**
1. **Download Form** → Pay fine → Get 10-day upload window
2. **Fill Form** → Complete within 10 days
3. **Upload Form** → System validates download window
4. **Loan Processing** → Form accepted for processing
5. **Window Closes** → Cannot upload with this form again

### **Rejected Application Flow:**
1. **If rejected** → Member must download fresh form
2. **New fine** → New 10-day window starts
3. **Fresh upload** → New loan application

## 🚀 IMPLEMENTATION STEPS

### **Step 1: Database Migration**
- Add new columns to DocumentDownload table
- Add relationship to UploadedForm table

### **Step 2: Update Download Controller**
- Always apply fine for downloads
- Set upload window expiration
- Track download purpose

### **Step 3: Update Upload Controller**
- Validate download window
- Check if download already used
- Mark download as used after upload

### **Step 4: Update Views**
- Show upload window status
- Show remaining days for upload
- Clear error messages for expired/used downloads

### **Step 5: Add Validation Messages**
- "You must download a fresh form first"
- "Your 10-day upload window has expired"
- "This form has already been used for an application"

## 💡 BENEFITS

1. **Revenue Generation** - Fines for every download
2. **Form Freshness** - Always current forms used
3. **Process Control** - Clear upload windows
4. **Audit Trail** - Track download-to-upload lifecycle
5. **User Clarity** - Clear rules and timelines
