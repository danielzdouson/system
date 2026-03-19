# SACCO Management System - Complete User Guide

## Table of Contents
1. [System Overview](#system-overview)
2. [Getting Started](#getting-started)
3. [Member Portal](#member-portal)
4. [Administrator Portal](#administrator-portal)
5. [System Features](#system-features)
6. [Troubleshooting](#troubleshooting)

---

## System Overview

### What is the SACCO Management System?

The SACCO Management System is a comprehensive financial management platform designed for Savings and Credit Cooperative Organizations (SACCOs). It provides tools for managing member savings, loans, fines, welfare funds, and financial reporting.

### Key Features

- **Member Management**: Track member information, accounts, and financial activities
- **Group Savings**: Manage monthly deposits, distributions, and balances
- **Loan Management**: Process loan applications, disbursements, and repayments
- **Fines System**: Automated and manual fine management
- **Fiscal Year Management**: Organize financial data by fiscal periods
- **Cashflow Tracking**: Comprehensive transaction monitoring
- **Document Management**: Handle loan documents and guarantor forms
- **Reporting**: Generate financial statements and export data

### User Roles

1. **Members**: Individual SACCO members who can view their accounts and transactions
2. **Administrators**: Staff who manage the entire SACCO operations

---

## Getting Started

### System Access

**URL**: `http://localhost:8080` (or your configured domain)

### First-Time Login

1. Navigate to the system URL
2. Click "Login" 
3. Enter your credentials:
   - **Email**: Your registered email address
   - **Password**: Your assigned password
4. Click "Sign In"

### Password Reset

If you forget your password:
1. Click "Forgot Password?" on the login page
2. Enter your email address
3. Check your email for reset instructions
4. Follow the link to create a new password

---

## Member Portal

### Member Dashboard

After logging in as a member, you'll see your personal dashboard with:

#### Account Summary Cards

1. **My Savings**
   - Total savings amount
   - Monthly growth percentage
   - Visual indicator of savings trend

2. **Active Loans**
   - Total outstanding loan balance
   - Number of active loans
   - Quick access to loan details

3. **Next Payment**
   - Upcoming payment amount
   - Due date
   - Payment status

4. **Available Balance**
   - Current available funds
   - Amount ready for distribution or withdrawal

#### Financial Overview Section

- **Total Contributions**: All deposits made to date
- **Shares**: Your ownership percentage in the SACCO
- **Welfare Fund**: Your welfare contributions
- **Fines Status**:
  - Pending fines (unpaid)
  - Paid fines (historical)

#### Quick Actions

- **View Transactions**: See complete transaction history
- **Download Statement**: Generate financial statements
- **Update Profile**: Modify personal information

### Viewing Transactions

**Path**: Member Dashboard → Quick Actions → View Transactions

#### Transaction Types

- **Deposits**: Money added to your account
- **Distributions**: Funds allocated to savings, welfare, or fines
- **Loan Disbursements**: Loan amounts received
- **Loan Repayments**: Payments made toward loans
- **Fine Payments**: Fines that have been paid

#### Filtering Transactions

1. **By Date Range**:
   - Select "From Date" and "To Date"
   - Click "Apply Filters"

2. **By Type**:
   - Choose transaction type from dropdown
   - View specific transaction category

#### Transaction Details

Each transaction shows:
- Date and time
- Transaction type
- Amount (UGX)
- Description
- Current balance after transaction

### Viewing Loans

**Path**: Member Dashboard → My Loans (or Quick Actions → View Loans)

#### Active Loans Section

For each active loan, you can see:
- Loan amount
- Interest rate
- Outstanding balance
- Monthly payment amount
- Next payment due date
- Loan status

#### Loan Actions

1. **View Payment Schedule**
   - See all scheduled payments
   - Check payment status (Paid/Pending)
   - View due dates

2. **View Loan Details**
   - Complete loan information
   - Disbursement date
   - Repayment terms
   - Guarantor information

3. **View Loan Statement**
   - Payment history
   - Outstanding balance
   - Interest calculations

4. **Download Certificate**
   - Loan completion certificate (for paid loans)

### Understanding Your Fines

Fines appear in two places:

1. **Dashboard - Fines Status Card**
   - Pending Fines: Amount you currently owe
   - Paid Fines: Historical fines you've paid

2. **Transaction History**
   - Fine payments appear as transactions
   - Shows when fines were paid

#### Types of Fines

1. **Missed Saving Fine**: Applied when monthly savings are not made by deadline
2. **Late Payment Fine**: Applied when deposits are made after the due date
3. **Other Fines**: Manually applied by administrators

#### Fine Payment Process

- Fines are automatically detected by the system
- Administrators mark fines as paid after receiving payment
- Once marked as paid, the fine moves from "Pending" to "Paid" status
- You'll see the change reflected immediately on your dashboard

---

## Administrator Portal

### Admin Dashboard

**Path**: Login as Admin → Dashboard

The admin dashboard provides a comprehensive overview of SACCO operations:

#### Key Metrics

- Total Members
- Total Savings
- Active Loans
- Outstanding Fines
- Welfare Fund Balance
- Monthly Growth Statistics

#### Quick Access Sections

- Recent Transactions
- Pending Loan Applications
- Upcoming Payments
- System Alerts

### Member Management

**Path**: Admin → Members

#### Viewing Members

1. **Member List**
   - View all registered members
   - Search by name or ID
   - Filter by status

2. **Member Details**
   - Personal information
   - Account summary
   - Transaction history
   - Loan history
   - Fine records

#### Adding New Members

1. Click "Add New Member"
2. Fill in required information:
   - First Name
   - Last Name
   - National ID
   - Email Address
   - Phone Number
   - Date of Birth
   - Address
3. Set initial shares (if applicable)
4. Click "Save Member"

#### Updating Member Information

1. Navigate to member details
2. Click "Edit Member"
3. Update necessary fields
4. Click "Update"

#### Managing Member Shares

1. Go to member profile
2. Click "Update Shares"
3. Enter new share amount or percentage
4. Add notes explaining the change
5. Click "Update Shares"

### Fiscal Year Management

**Path**: Admin → Fiscal Years

#### Understanding Fiscal Years

Fiscal years organize financial data into manageable periods (typically 12 months). All transactions, savings, and loans are tracked within fiscal years.

#### Creating a Fiscal Year

1. Click "Create Fiscal Year"
2. Enter:
   - **Name**: e.g., "2024-2025"
   - **Start Date**: Beginning of fiscal period
   - **End Date**: End of fiscal period
   - **Status**: Active or Inactive
3. Click "Create"

#### Activating a Fiscal Year

1. Only ONE fiscal year can be active at a time
2. Click "Activate" next to the desired fiscal year
3. Confirm activation
4. All new transactions will be recorded in this fiscal year

#### Carry Forward Process

When starting a new fiscal year, you need to carry forward data:

1. Navigate to "Carry Forward" section
2. Select:
   - **From Fiscal Year**: Previous year
   - **To Fiscal Year**: New year
3. Review items to carry forward:
   - Unpaid fines
   - Outstanding loans
   - Member balances
4. Click "Process Carry Forward"
5. Verify carried forward data

### Group Savings Management

**Path**: Admin → Group Savings → Dashboard

#### Monthly Savings Overview

The dashboard shows all 12 months of the fiscal year with:
- Month name and year
- Status indicator (Current/Past/Future)
- Click any month to view details

#### Recording Member Deposits

**Path**: Group Savings → New Deposit

1. Click "New Deposit"
2. Select:
   - **Member**: Choose from dropdown
   - **Month**: Select month (1-12)
   - **Amount**: Enter deposit amount (UGX)
   - **Deposit Date**: Date payment was received
   - **Notes**: Optional description
3. Click "Create Deposit"
4. You'll be redirected to distribution page

#### Distributing Deposits

After creating a deposit, you must distribute the funds:

1. **Available Balance**: Shows member's current balance
2. **Distribution Options**:
   - **Savings Amount**: Funds for member's savings
   - **Welfare Amount**: Contribution to welfare fund
   - **Fines Amount**: Payment toward pending fines
   - **Other Amount**: Miscellaneous allocations

3. **Distribution Rules**:
   - Total distribution cannot exceed available balance
   - All amounts must be ≥ 0
   - System validates before processing

4. **Target Month**: Select which month to apply the distribution to

5. Click "Distribute Funds"

#### Distributing Member Balance

**Path**: Monthly View → Member → "Distribute Balance"

For members with available balance but no deposit in current month:

1. Click "Distribute Balance" next to member
2. Enter distribution amounts:
   - Savings
   - Welfare
   - Fines
   - Other
3. Select target month
4. Click "Distribute"

#### Viewing Monthly Details

**Path**: Group Savings → Click on any month

Shows detailed breakdown:
- Member list with activity
- Deposit amounts
- Distribution breakdown (Savings/Welfare/Fines/Other)
- Current balance per member
- Unpaid fines
- Members with balance but no deposit

#### Pending Months

**Path**: Group Savings → Pending Months

View months where not all members have made deposits:
- Identifies members who haven't contributed
- Shows missed savings
- Helps track compliance

### Fines Management

**Path**: Admin → Fines

#### Fines Dashboard

Displays comprehensive fine statistics:
- **Total Fines**: All fines in system
- **Pending Fines**: Unpaid fines
- **Paid Fines**: Completed payments
- **Waived Fines**: Forgiven fines

#### Creating Manual Fines

1. Click "New Fine"
2. Fill in details:
   - **Member**: Select member
   - **Fiscal Year**: Choose fiscal year
   - **Month**: Select month (1-12)
   - **Amount**: Enter fine amount (UGX)
   - **Reason**: 
     - Missed Saving
     - Late Payment
     - Other
   - **Description**: Explain the fine
3. Click "Create Fine"

#### Auto-Apply Fines

**Purpose**: Automatically apply fines to members who missed savings

1. Click "Auto Apply"
2. Select:
   - **Fiscal Year**
   - **Month**
3. Click "Auto Apply Fines"
4. System will:
   - Check all members
   - Identify those without savings for selected month
   - Apply missed saving fines automatically
   - Skip members who already have fines

#### Marking Fines as Paid

When a member pays a fine:

1. Find the fine in the list
2. Click the "Pay" button (✓ icon)
3. Optional: Add payment notes
4. Click "Mark as Paid"
5. Fine status changes to "Paid"
6. Member sees updated status immediately

#### Waiving Fines

To forgive a fine:

1. Find the fine in the list
2. Click "Waive" button
3. Enter waiver reason (required)
4. Click "Waive Fine"
5. Fine status changes to "Waived"

#### Filtering Fines

Use filters to find specific fines:
- **Status**: Pending/Paid/Waived
- **Reason**: Missed Saving/Late Payment/Other
- **Month**: Select specific month
- **Search**: Member name or ID

#### Exporting Fine Data

1. Click "Export"
2. System generates CSV file
3. Download includes:
   - Member details
   - Fine amounts
   - Reasons
   - Status
   - Dates

### Loan Management

**Path**: Admin → Group Loans

#### Loan Dashboard

Overview of all loans:
- Active loans
- Pending applications
- Completed loans
- Total loan portfolio
- Outstanding balance

#### Processing Loan Applications

**Path**: Loans → Pending Applications

1. Review application details:
   - Member information
   - Requested amount
   - Purpose
   - Guarantors
   - Member's savings history

2. **Approve or Reject**:
   - Click "Approve" to proceed
   - Click "Reject" with reason if denied

3. **For Approved Loans**:
   - Set interest rate
   - Set repayment period (months)
   - Set monthly payment amount
   - Click "Disburse Loan"

#### Disbursing Loans

After approval:

1. Navigate to approved loan
2. Click "Disburse"
3. Confirm:
   - Loan amount
   - Disbursement date
   - Payment schedule
4. Click "Confirm Disbursement"
5. System creates repayment schedule automatically

#### Recording Loan Repayments

**Path**: Loans → Active Loans → Select Loan → Record Payment

1. Click "Record Payment"
2. Enter:
   - **Payment Amount**: Amount received
   - **Payment Date**: Date of payment
   - **Payment Method**: Cash/Bank Transfer/etc.
   - **Notes**: Optional description
3. Click "Record Payment"
4. System updates:
   - Outstanding balance
   - Payment schedule
   - Member account

#### Loan Penalties

For late payments:

1. System can auto-apply penalties
2. Or manually add penalty:
   - Navigate to loan
   - Click "Add Penalty"
   - Enter penalty amount and reason
   - Click "Apply Penalty"

#### Viewing Loan Details

For any loan, you can view:
- **Basic Information**: Amount, rate, term
- **Repayment Schedule**: All scheduled payments
- **Payment History**: Actual payments made
- **Guarantors**: Who guaranteed the loan
- **Documents**: Related loan documents

### Cashflow Management

**Path**: Admin → Cashflow

#### Understanding Cashflow

The cashflow page shows ALL money movement in the system:

**Income Sources**:
- Member deposits
- Loan repayments
- Fine payments
- Investment returns
- Other income

**Expense Categories**:
- Loan disbursements
- Welfare distributions
- Operating expenses
- Investment outflows

#### Cashflow Categories

1. **Operating Activities**:
   - Day-to-day transactions
   - Member deposits
   - Fine collections

2. **Investing Activities**:
   - Investment purchases
   - Investment returns

3. **Financing Activities**:
   - Loan disbursements
   - Loan repayments

#### Viewing Cashflow

The cashflow page displays:
- Date range filter
- Transaction type filter
- Category filter
- Search functionality
- Total inflows
- Total outflows
- Net balance

#### Filtering Cashflow

1. **By Date**: Select date range
2. **By Type**: Income or Expense
3. **By Category**: Operating/Investing/Financing
4. **By Status**: Pending or Completed
5. **Search**: Transaction description or member

#### Exporting Cashflow Data

1. Set desired filters
2. Click "Export"
3. Choose format (CSV/Excel)
4. Download report

### Document Management

**Path**: Admin → Documents

#### Document Types

1. **Loan Application Forms**
2. **Guarantor Forms**
3. **Member Registration Documents**
4. **Financial Statements**
5. **Meeting Minutes**

#### Uploading Documents

1. Click "Upload Document"
2. Select document type
3. Choose file
4. Add description
5. Link to member (if applicable)
6. Click "Upload"

#### Managing Guarantor Forms

**Path**: Documents → Guarantor Forms

1. **Pending Guarantees**:
   - Forms awaiting guarantor confirmation
   - Send notifications to guarantors
   - Track confirmation status

2. **Confirmed Guarantees**:
   - Approved guarantor commitments
   - Linked to loan applications

3. **Guarantor Actions**:
   - View guarantor history
   - Check guarantee limits
   - Monitor active guarantees

### Reports and Analytics

**Path**: Admin → Reports

#### Available Reports

1. **Member Financial Summary**
   - Individual member statements
   - Savings history
   - Loan history
   - Fine records

2. **Monthly Savings Report**
   - Deposits by month
   - Distribution breakdown
   - Compliance rates

3. **Loan Portfolio Report**
   - Active loans
   - Repayment rates
   - Default analysis
   - Interest income

4. **Fine Report**
   - Fine collections
   - Waived fines
   - Outstanding fines
   - Fine trends

5. **Cashflow Statement**
   - Income vs Expenses
   - Category breakdown
   - Period comparisons

6. **Fiscal Year Summary**
   - Complete year overview
   - Growth metrics
   - Member statistics

#### Generating Reports

1. Select report type
2. Choose parameters:
   - Date range
   - Fiscal year
   - Member (if applicable)
   - Filters
3. Click "Generate Report"
4. View on screen or export

#### Exporting Reports

Available formats:
- **PDF**: For printing and sharing
- **Excel**: For further analysis
- **CSV**: For data import

---

## System Features

### Fiscal Year Context

#### How It Works

- System operates within fiscal year context
- All transactions tied to specific fiscal year
- Only one fiscal year active at a time
- Switching fiscal years changes data view

#### Fiscal Year Selector

Located in top navigation:
- Shows current active fiscal year
- Dropdown to view other fiscal years
- Click to switch between years (view-only for past years)

### Automatic Fine System

#### Automatic Fine Application

System automatically applies fines for:

1. **Missed Savings**:
   - Triggered when member doesn't deposit by deadline
   - Default deadline: 5th of following month
   - Default fine: UGX 2,000

2. **Late Payments**:
   - Applied when deposit made after deadline
   - Calculated: UGX 100 per day late (max UGX 5,000)

#### Fine Lifecycle

1. **Created**: Fine is generated (auto or manual)
2. **Pending**: Awaiting payment
3. **Paid**: Member has paid the fine
4. **Waived**: Administrator forgave the fine

### Member Account System

#### How Member Accounts Work

Each member has an account per fiscal year containing:

1. **Current Balance**: Available funds
2. **Total Deposits**: All money deposited
3. **Total Savings**: Allocated to savings
4. **Welfare Contributions**: Welfare fund allocations
5. **Fine Payments**: Fines paid from account
6. **Shares**: Ownership percentage

#### Balance Management

- Deposits increase balance
- Distributions decrease balance
- Balance can be distributed across multiple months
- Balance carries forward to new fiscal year

### Distribution System

#### Distribution Flow

1. Member makes deposit → Balance increases
2. Administrator distributes funds:
   - To savings (builds member equity)
   - To welfare (group fund)
   - To fines (pays penalties)
   - To other (miscellaneous)
3. Distribution reduces available balance
4. Allocated amounts tracked by category

#### Distribution Rules

- Cannot distribute more than available balance
- All amounts must be non-negative
- Distribution can target any month
- Multiple distributions allowed per deposit

### Welfare Fund

#### Purpose

Collective fund for member welfare needs:
- Emergency assistance
- Medical support
- Death benefits
- Other welfare purposes

#### How It Works

1. Members contribute via deposit distributions
2. Fund accumulates over time
3. Administrators manage disbursements
4. Tracked separately from savings

### Investment System

#### Group Investments

SACCO can invest collective funds:

1. **Investment Types**:
   - Fixed deposits
   - Bonds
   - Real estate
   - Business ventures

2. **Investment Tracking**:
   - Principal amount
   - Returns/Interest
   - Investment period
   - Status (Active/Matured)

3. **Investment Transactions**:
   - Inflows (returns received)
   - Outflows (investments made)

#### ROI Calculation

System automatically calculates:
- Return on Investment percentage
- Total returns vs principal
- Investment performance metrics

---

## Troubleshooting

### Common Issues and Solutions

#### Login Problems

**Issue**: Cannot log in
**Solutions**:
1. Verify email and password are correct
2. Check if account is active
3. Use "Forgot Password" to reset
4. Contact administrator if issue persists

**Issue**: Redirected to wrong dashboard
**Solutions**:
1. Check your user role with administrator
2. Clear browser cache and cookies
3. Log out completely and log back in

#### Member Portal Issues

**Issue**: Transactions not showing
**Solutions**:
1. Check date filters
2. Verify fiscal year selection
3. Refresh the page
4. Contact administrator if data is missing

**Issue**: Fine status not updating
**Solutions**:
1. Refresh the page
2. Verify payment was recorded by administrator
3. Check if viewing correct fiscal year
4. Contact administrator to verify payment status

**Issue**: Balance doesn't match expectations
**Solutions**:
1. Review transaction history
2. Check all distributions
3. Verify fiscal year context
4. Request statement from administrator

#### Administrator Issues

**Issue**: Cannot create deposit
**Solutions**:
1. Verify fiscal year is active
2. Check member exists in system
3. Ensure all required fields filled
4. Verify amount is valid number

**Issue**: Distribution fails
**Solutions**:
1. Check available balance is sufficient
2. Verify all amounts are non-negative
3. Ensure total doesn't exceed balance
4. Check member account exists

**Issue**: Carry forward not working
**Solutions**:
1. Verify both fiscal years exist
2. Check no carry forward already done
3. Ensure source fiscal year is complete
4. Review carry forward history

**Issue**: Fine not applying automatically
**Solutions**:
1. Check automatic fine settings
2. Verify member has no existing fine for that month
3. Ensure deadline date has passed
4. Check member deposit status

#### Data Issues

**Issue**: Missing transactions
**Solutions**:
1. Verify correct fiscal year selected
2. Check date range filters
3. Review transaction status (pending vs completed)
4. Check database backup if critical

**Issue**: Incorrect balances
**Solutions**:
1. Run balance reconciliation
2. Review all transactions for member
3. Check for duplicate entries
4. Verify distribution calculations

**Issue**: Report not generating
**Solutions**:
1. Check date range is valid
2. Verify data exists for selected period
3. Try different export format
4. Check system logs for errors

### Getting Help

#### For Members

1. **First**: Check this user guide
2. **Second**: Contact your SACCO administrator
3. **Third**: Submit support request through system

#### For Administrators

1. **Documentation**: Review this guide and technical documentation
2. **System Logs**: Check application logs for errors
3. **Database**: Verify data integrity
4. **Support**: Contact system developer/vendor

### Best Practices

#### For Members

1. **Regular Monitoring**: Check your account weekly
2. **Timely Deposits**: Make deposits before deadline to avoid fines
3. **Review Statements**: Download and review monthly statements
4. **Update Information**: Keep contact details current
5. **Report Issues**: Notify administrator of any discrepancies immediately

#### For Administrators

1. **Daily Tasks**:
   - Review pending deposits
   - Process loan payments
   - Check for system alerts

2. **Weekly Tasks**:
   - Review fine applications
   - Process loan applications
   - Generate weekly reports

3. **Monthly Tasks**:
   - Close monthly savings
   - Apply automatic fines
   - Generate monthly reports
   - Review member accounts

4. **Fiscal Year Tasks**:
   - Close fiscal year
   - Carry forward data
   - Generate annual reports
   - Archive old data

5. **Data Management**:
   - Regular backups
   - Data validation
   - Reconciliation checks
   - Audit trail review

6. **Security**:
   - Regular password updates
   - Monitor user access
   - Review system logs
   - Maintain data privacy

---

## Appendix

### Glossary

- **SACCO**: Savings and Credit Cooperative Organization
- **Fiscal Year**: 12-month financial period for organizing accounts
- **Distribution**: Allocation of deposited funds to different categories
- **Carry Forward**: Transfer of data from one fiscal year to the next
- **Guarantor**: Member who guarantees another member's loan
- **Welfare Fund**: Collective fund for member welfare needs
- **ROI**: Return on Investment
- **Cashflow**: Movement of money in and out of the SACCO

### System Limits

- **Maximum Loan Amount**: Configured by administrator
- **Minimum Deposit**: Configured by administrator
- **Fine Amounts**: Configurable (default: UGX 2,000 for missed savings)
- **Late Payment Fine**: UGX 100/day (max UGX 5,000)
- **Fiscal Year**: Typically 12 months

### Contact Information

**System Administrator**: [Your SACCO Admin Contact]
**Technical Support**: [Developer/Vendor Contact]
**Emergency Contact**: [Emergency Contact]

### Version Information

**System Version**: 1.0
**Last Updated**: March 2026
**Documentation Version**: 1.0

---

## Quick Reference

### Member Quick Actions

| Action | Path |
|--------|------|
| View Dashboard | Login → Dashboard |
| Check Balance | Dashboard → Available Balance Card |
| View Transactions | Dashboard → View Transactions |
| Check Fines | Dashboard → Fines Status Card |
| View Loans | Dashboard → My Loans |
| Download Statement | Dashboard → Download Statement |

### Admin Quick Actions

| Action | Path |
|--------|------|
| Add Member | Admin → Members → Add New Member |
| Record Deposit | Admin → Group Savings → New Deposit |
| Distribute Funds | After creating deposit → Distribute |
| Apply Fine | Admin → Fines → New Fine |
| Mark Fine Paid | Admin → Fines → Select Fine → Pay |
| Process Loan | Admin → Loans → Pending Applications |
| View Cashflow | Admin → Cashflow |
| Generate Report | Admin → Reports → Select Type |

### Keyboard Shortcuts

- **Ctrl + S**: Save (where applicable)
- **Esc**: Close modal/dialog
- **Ctrl + F**: Search/Filter
- **Ctrl + P**: Print (reports)

---

**End of User Guide**

For additional support or questions not covered in this guide, please contact your system administrator.
