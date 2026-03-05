# 🚀 System Startup & Demo Process

## 📋 **STEP-BY-STEP PROCESS TO RUN THE SYSTEM**

### **Step 1: Start Docker Containers**
```bash
# Navigate to project directory
cd /path/to/your/project

# Start all containers
docker-compose up -d

# Check if containers are running
docker ps
```

### **Step 2: Verify System Status**
```bash
# Check Laravel routes are working
docker exec saco_system_php php artisan route:list --name=documents

# Check database connection
docker exec saco_system_php php artisan tinker
# In tinker: \DB::connection()->getPdo()
```

### **Step 3: Access the Application**
```
Main URL: http://localhost:8080
```

---

## 🎯 **COMPLETE DEMO WORKFLOW**

### **Part 1: Admin Setup**

#### **1.1 Login as Admin**
- Go to: `http://localhost:8080/login`
- Login with admin credentials

#### **1.2 Upload a Loan Form Document**
- Navigate to: `http://localhost:8080/admin/documents`
- Click "Create New Document"
- Fill in:
  - Title: "Personal Loan Application Form"
  - Description: "Standard personal loan application form"
  - Document Type: "loan_form"
  - Fine Amount: 5000
  - Upload a PDF file
- Click "Save"

#### **1.3 Verify Document Upload**
- You should see the document in the list
- Note the document ID for next steps

---

### **Part 2: Member Workflow**

#### **2.1 Switch to Member Role**
- Go to: `http://localhost:8080/make-member` (temp testing route)
- Or logout and login as a member

#### **2.2 Browse Documents**
- Navigate to: `http://localhost:8080/member/documents`
- You should see the loan form you just uploaded
- Note the fine amount (5000 UGX)

#### **2.3 Download Loan Form**
- Click on the loan form
- Click "Download" 
- Confirm the fine payment
- Download the PDF file

#### **2.4 Upload Completed Form**
- Go back to document details
- Click "Upload Completed Form"
- Fill in:
  - Loan Amount: 800000 (800K UGX - requires 2 guarantors)
  - Upload the filled PDF
- Click "Upload"

#### **2.5 Check Form Status**
- Navigate to: `http://localhost:8080/member/documents/my-uploads`
- You should see your uploaded form
- Status: "Pending Guarantors"
- System calculated: 2 guarantors needed

---

### **Part 3: Guarantor Process**

#### **3.1 View Pending Guarantees**
- Navigate to: `http://localhost:8080/member/documents/pending-guarantees`
- You should see the loan form needing guarantors
- Shows: 800K UGX loan, 0% guaranteed so far

#### **3.2 Guarantee the Loan (First Guarantor)**
- Click "View Details" on the pending form
- Review borrower information and loan details
- Enter:
  - Guarantee Percentage: 50
  - Confirmation: "I guarantee 50% of this loan as the borrower is creditworthy"
- Click "Confirm Guarantee"

#### **3.3 Second Guarantor**
- Switch to another member account or use different browser
- Go to pending guarantees again
- Guarantee the remaining 50%

#### **3.4 Check Form Status**
- Go back to "My Uploads"
- Status should now be: "Ready for Review"
- Both guarantors listed with their percentages

---

### **Part 4: Admin Review & Approval**

#### **4.1 Review Uploaded Forms**
- Login as admin again
- Navigate to: `http://localhost:8080/admin/documents/uploaded-forms`
- You should see the completed form with 100% guarantee

#### **4.2 Review Individual Form**
- Click "Review" on the form
- Review:
  - Uploaded PDF document
  - Borrower details
  - Both guarantors with 50% each
  - Total guaranteed amount: 800K UGX

#### **4.3 Approve the Form**
- Click "Approve"
- Add admin notes if needed
- Form status changes to "Approved"

---

### **Part 5: Monitor Guarantor History**

#### **5.1 Check Guarantor History**
- As a member who guaranteed
- Navigate to: `http://localhost:8080/member/documents/guarantor-history`
- You should see:
  - The loan you guaranteed
  - 50% guarantee (400K UGX)
  - Status: "Confirmed"

#### **5.2 Admin Monitor All Guarantees**
- As admin
- Navigate to: `http://localhost:8080/admin/documents/uploaded-forms/{id}/guarantors`
- See all guarantor details for the approved form

---

## 🔧 **TROUBLESHOOTING STEPS**

### **If you see "Internal Server Error":**
```bash
# 1. Check containers
docker ps

# 2. Restart containers
docker-compose restart

# 3. Check Apache config
docker exec saco_system_php cat /etc/apache2/sites-available/000-default.conf

# 4. Verify DocumentRoot is /var/www/html/public

# 5. Restart Apache
docker exec saco_system_php service apache2 restart

# 6. Clear Laravel cache
docker exec saco_system_php php artisan cache:clear
docker exec saco_system_php php artisan config:clear
docker exec saco_system_php php artisan route:clear
```

### **If database connection fails:**
```bash
# 1. Check MySQL container
docker exec saco_system_db mysql -u root -p

# 2. Verify database exists
SHOW DATABASES;

# 3. Check Laravel .env
docker exec saco_system_php cat /var/www/html/.env
```

### **If routes don't work:**
```bash
# 1. Clear routes
docker exec saco_system_php php artisan route:clear

# 2. Check if routes are registered
docker exec saco_system_php php artisan route:list

# 3. Verify web.php is correct
docker exec saco_system_php cat /var/www/html/routes/web.php
```

---

## 📊 **KEY THINGS TO MONITOR**

### **Admin Dashboard:**
- Total uploaded forms
- Forms pending guarantors
- Forms ready for review
- Approved vs rejected forms

### **Member Dashboard:**
- Available documents to download
- Pending guarantee opportunities
- Personal guarantee history
- Upload status

### **System Health:**
- Docker containers running
- Apache serving correctly
- Database connected
- Routes registered

---

## 🎯 **QUIDK START COMMANDS**

```bash
# Start system
docker-compose up -d

# Check status
docker ps

# Verify routes
docker exec saco_system_php php artisan route:list --name=documents

# Access application
# Open browser to: http://localhost:8080
```

---

## 📝 **TESTING CHECKLIST**

### **Admin Functions:**
- [ ] Upload loan form document
- [ ] Set fine amount
- [ ] Review uploaded forms
- [ ] Approve/reject forms
- [ ] View guarantor details

### **Member Functions:**
- [ ] Browse documents
- [ ] Download with fine payment
- [ ] Upload completed forms
- [ ] View pending guarantees
- [ ] Guarantee loans by percentage
- [ ] View guarantee history

### **System Features:**
- [ ] Automatic guarantor requirement calculation
- [ ] Percentage-based guarantees
- [ ] Status updates (pending → ready → approved)
- [ ] Fine application for downloads
- [ ] IP logging for guarantees

---

**🎉 That's it! The system is now fully functional and ready for use!**
