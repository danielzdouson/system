# Guarantor and Form System Access Guide

## 🚀 How to Access the System

### **Main Application URL:**
```
http://localhost:8080
```

---

## 👤 **Admin Access & Monitoring**

### **1. Login as Admin**
- Go to: `http://localhost:8080/login`
- Use your admin credentials

### **2. Document Management**
**URL:** `http://localhost:8080/admin/documents`

**What you can do here:**
- ✅ **Upload Documents** - Add constitution, legal docs, loan forms
- ✅ **Set Fine Amounts** - Configure download fines (5000 UGX for loan forms)
- ✅ **View All Documents** - Monitor uploaded and system documents
- ✅ **Edit/Delete** - Manage existing documents

### **3. Monitor Uploaded Forms**
**URL:** `http://localhost:8080/admin/documents/uploaded-forms`

**What you can monitor:**
- 📋 **All Loan Applications** - See every submitted loan form
- 👥 **Guarantor Status** - Track who has guaranteed what percentage
- 📊 **Progress Tracking** - See if forms need more guarantors
- 🏷️ **Status Overview** - Pending, Approved, Rejected forms

### **4. Review Individual Forms**
**URL:** `http://localhost:8080/admin/documents/uploaded-forms/{id}/review`

**For each form you can:**
- 👀 **View Uploaded PDF** - Download and review the loan application
- 👥 **See All Guarantors** - Who guaranteed and what percentage
- ✅ **Approve** - Mark form as approved for loan processing
- ❌ **Reject** - Reject with admin notes
- 📝 **Add Notes** - Document review decisions

### **5. View Guarantor Details**
**URL:** `http://localhost:8080/admin/documents/uploaded-forms/{id}/guarantors`

**Monitor guarantor information:**
- 📊 **Guarantee Percentages** - Who guaranteed what %
- 💰 **Guaranteed Amounts** - UGX amounts per guarantor
- 📅 **Confirmation Dates** - When guarantees were made
- 🔄 **Status Changes** - Confirmed, withdrawn, called upon

---

## 👥 **Member Access & Guarantor Functions**

### **1. Member Dashboard**
**URL:** `http://localhost:8080/member/dashboard`

### **2. Browse Documents**
**URL:** `http://localhost:8080/member/documents`

**Members can:**
- 📄 **View Available Documents** - Constitution, legal docs, loan forms
- 💰 **See Fine Requirements** - Know costs before download
- ⬇️ **Download with Fine** - Pay 5000 UGX for loan forms

### **3. Upload Loan Forms**
**URL:** `http://localhost:8080/member/documents/{id}/upload-form`

**Process:**
1. 📄 Download loan form (pay fine)
2. ✅ Fill out the form
3. 📤 Upload completed form with loan amount
4. 🎯 System calculates required guarantors automatically

### **4. View Pending Guarantees**
**URL:** `http://localhost:8080/member/documents/pending-guarantees`

**See all guarantee opportunities:**
- 🎯 **Available Forms** - All loans needing guarantors
- 💰 **Loan Amounts** - How much is being requested
- 👥 **Current Guarantors** - Who has guaranteed so far
- 📊 **Remaining Percentage** - How much still needs guarantee

### **5. Guarantee a Loan**
**URL:** `http://localhost:8080/member/documents/guarantee-details/{id}`

**Before guaranteeing, members see:**
- 👤 **Borrower Information** - Who is requesting the loan
- 💰 **Loan Details** - Amount, purpose, repayment history
- 📊 **Current Guarantees** - Who has already guaranteed
- 🎯 **Available Percentage** - How much you can guarantee

**Guarantee Process:**
1. ✅ **Enter Percentage** - Choose % to guarantee (1-100%)
2. 📝 **Add Confirmation** - Write guarantee statement
3. 🔄 **IP Logged** - Digital confirmation recorded
4. ✅ **Instant Update** - Form status updated immediately

### **6. Guarantor History**
**URL:** `http://localhost:8080/member/documents/guarantor-history`

**Track all your guarantees:**
- 📊 **Active Guarantees** - Currently guaranteed loans
- 📈 **Total Guaranteed** - Sum of all guaranteed amounts
- 🔄 **Status History** - Confirmed, withdrawn, called upon
- 💰 **Guarantee Limits** - How much more you can guarantee

---

## 📊 **Monitoring Dashboard**

### **Admin Overview**
From the admin dashboard, monitor:
- 📋 **Total Forms** - All uploaded loan forms
- ⏳ **Pending Forms** - Waiting for guarantors
- ✅ **Ready for Review** - Complete guarantees
- 👥 **Active Guarantors** - Members who can guarantee
- 💰 **Total Guaranteed Amount** - System-wide guarantee exposure

### **Key Metrics to Track**
1. **Form Processing Time** - From upload to approval
2. **Guarantor Response Rate** - How quickly members respond
3. **Guarantee Distribution** - How guarantees are spread
4. **Fine Revenue** - Income from document downloads
5. **Risk Exposure** - Total guaranteed amounts

---

## 🔄 **Complete Workflow Example**

### **Step 1: Admin Setup**
1. Go to `http://localhost:8080/admin/documents`
2. Upload a loan form PDF
3. Set fine amount to 5000 UGX

### **Step 2: Member Applies**
1. Member goes to `http://localhost:8080/member/documents`
2. Downloads loan form (5000 UGX fine applied)
3. Uploads completed form for 800K loan
4. System: "This form requires 2 guarantors"

### **Step 3: Guarantor Process**
1. All members get notification
2. Members go to `http://localhost:8080/member/documents/pending-guarantees`
3. John guarantees 50% (400K UGX)
4. Mary guarantees 50% (400K UGX)
5. Form status: "Ready for review"

### **Step 4: Admin Review**
1. Admin goes to `http://localhost:8080/admin/documents/uploaded-forms`
2. Clicks "Review" on the completed form
3. Views guarantor details and uploaded PDF
4. Approves the form
5. Loan can now be processed

---

## 🚨 **Important URLs to Bookmark**

### **Admin URLs:**
- 📋 Documents: `http://localhost:8080/admin/documents`
- 📊 Uploaded Forms: `http://localhost:8080/admin/documents/uploaded-forms`
- 👥 Guarantor View: `http://localhost:8080/admin/documents/uploaded-forms/{id}/guarantors`

### **Member URLs:**
- 📄 Browse Documents: `http://localhost:8080/member/documents`
- 🎯 Pending Guarantees: `http://localhost:8080/member/documents/pending-guarantees`
- 📈 Guarantor History: `http://localhost:8080/member/documents/guarantor-history`

---

## 🎯 **Quick Start Checklist**

### **For Admins:**
- [ ] Upload at least one loan form document
- [ ] Set fine amounts appropriately
- [ ] Monitor uploaded forms regularly
- [ ] Review and approve completed forms

### **For Members:**
- [ ] Browse available documents
- [ ] Download and fill loan forms
- [ ] Check pending guarantees regularly
- [ ] Guarantee loans you're comfortable with

---

## 🔧 **Troubleshooting**

If you see "Internal Server Error":
1. Check if Docker containers are running: `docker ps`
2. Restart containers: `docker-compose restart`
3. Clear Laravel cache: `docker exec saco_system_php php artisan cache:clear`

The system is now fully functional and ready to use! 🎉
