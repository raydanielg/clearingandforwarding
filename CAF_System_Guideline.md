# CAF — Clearing And Forwarding Management System
### Full System Design & Development Guideline Document
**Stack:** Laravel 11 (Backend) + Blade/Livewire or Vue 3 (Frontend) + MySQL
**Prepared for:** Internal Development Team
**Version:** 1.0

---

## 1. SYSTEM OVERVIEW

CAF is a web-based management system for a Clearing & Forwarding (C&F) company operating in Tanzania. It digitizes the full lifecycle of a shipment — from client instruction, customs declaration (TANSAD/TRA), port clearance, transport, delivery, up to billing and payment — with role-based access for every staff member and a client portal for customers to track their cargo.

### 1.1 Core Objectives
- Track every shipment (File/Job) from opening to closing.
- Manage customs documentation (Bill of Lading, TANSAD, Packing List, Invoices, Exemptions, Permits).
- Manage payments to authorities (TRA duties, Port charges, Shipping line, ICD) and reconcile them.
- Bill clients (proforma → invoice → receipt) and track debtors.
- Give each role exactly what they need — nothing more (RBAC + Permissions).
- Give clients a portal to see status, documents, and invoices in real time.
- Full audit trail: who did what, when.

### 1.2 Key Business Terms
| Term | Meaning |
|---|---|
| File / Job | One clearing job for one consignment. Has a unique File No. e.g. `CAF/IMP/2026/0001` |
| BL / AWB | Bill of Lading (sea) / Airway Bill (air) |
| TANSAD | Tanzania Single Administrative Document — the customs declaration |
| DO | Delivery Order from the shipping line |
| Demurrage / Storage | Charges when cargo overstays at port/ICD |
| Disbursement | Money CAF pays on behalf of the client (duties, port charges) |
| Agency Fee | CAF's own service fee (the profit line) |

---

## 2. TECHNOLOGY STACK

| Layer | Technology | Notes |
|---|---|---|
| Backend | Laravel 11 (PHP 8.3) | API + server-rendered admin |
| Frontend (Staff) | Blade + Livewire 3 + Alpine.js + TailwindCSS | Fast to build, reactive |
| Frontend (Client Portal) | Same app, separate layout/guard (or Vue 3 SPA if preferred) | |
| Database | MySQL 8 | InnoDB, utf8mb4 |
| Auth | Laravel Breeze/Fortify + Sanctum (API tokens for portal/mobile) | |
| Permissions | `spatie/laravel-permission` | Roles + granular permissions |
| Files/Docs | Laravel Storage (local → S3-compatible later) | PDF scans of BL, TANSAD etc. |
| PDF Generation | `barryvdh/laravel-dompdf` | Invoices, receipts, job cards |
| Excel Export | `maatwebsite/excel` | Reports |
| Activity Log | `spatie/laravel-activitylog` | Audit trail |
| Queues | Laravel Queue (database driver → Redis later) | Emails, SMS, heavy reports |
| Notifications | Mail + SMS gateway (e.g. Beem/NextSMS TZ) | Status updates to clients |
| Backups | `spatie/laravel-backup` | Nightly DB + files |

---

## 3. ROLES & PERMISSIONS (RBAC)

Use **spatie/laravel-permission**. Roles group permissions; permissions guard every action.

### 3.1 Roles

| # | Role | Who | Summary of Power |
|---|---|---|---|
| 1 | **Super Admin** | Owner / Director | Everything. Manages users, roles, settings, sees all money. |
| 2 | **Admin / General Manager** | GM | All operations + finance visibility, cannot edit system settings/roles unless granted. |
| 3 | **Operations Manager** | Head of Ops | Opens files, assigns officers, monitors all shipments, approves job closure. |
| 4 | **Declaration Officer** | Customs declarant | Creates & manages TANSAD entries, tariff classification, uploads customs docs. |
| 5 | **Documentation Officer** | Docs clerk | Registers documents (BL, invoices, packing lists), files scans, tracks originals. |
| 6 | **Clearing Officer (Field)** | Port runner | Updates port/ICD milestones: verification, release, loading, gate-out. |
| 7 | **Transport Officer** | Fleet coordinator | Assigns trucks/transporters, tracks delivery, manages drivers & delivery notes. |
| 8 | **Accountant** | Finance | Client invoices, receipts, disbursements, expenses, debtors, reports. |
| 9 | **Cashier** | Petty cash | Records petty cash & small disbursements only (no invoicing). |
| 10 | **Client** | Customer | Portal only: own shipments, documents, invoices, statements, tracking. |
| 11 | **Driver** (optional) | Truck driver | Mobile-friendly view: assigned trips, update delivery status, upload POD. |

### 3.2 Permission Matrix (sample — full list in Section 10)

| Permission | Super Admin | Admin | Ops Mgr | Decl. Off. | Doc. Off. | Clearing Off. | Transport | Accountant | Cashier | Client |
|---|---|---|---|---|---|---|---|---|---|---|
| users.manage | ✔ | | | | | | | | | |
| roles.manage | ✔ | | | | | | | | | |
| settings.manage | ✔ | ✔* | | | | | | | | |
| clients.crud | ✔ | ✔ | ✔ | | ✔ | | | view | | |
| files.create | ✔ | ✔ | ✔ | | ✔ | | | | | |
| files.assign | ✔ | ✔ | ✔ | | | | | | | |
| files.close | ✔ | ✔ | ✔ | | | | | | | |
| declarations.crud | ✔ | ✔ | ✔ | ✔ | | | | | | |
| documents.upload | ✔ | ✔ | ✔ | ✔ | ✔ | ✔ | ✔ | | | |
| milestones.update | ✔ | ✔ | ✔ | ✔ | | ✔ | ✔ | | | |
| transport.crud | ✔ | ✔ | ✔ | | | | ✔ | | | |
| invoices.crud | ✔ | ✔ | | | | | | ✔ | | |
| receipts.create | ✔ | ✔ | | | | | | ✔ | ✔ | |
| disbursements.crud | ✔ | ✔ | | | | | | ✔ | ✔* | |
| reports.finance | ✔ | ✔ | | | | | | ✔ | | |
| reports.operations | ✔ | ✔ | ✔ | | | | | | | |
| portal.view-own | | | | | | | | | | ✔ |

`*` = limited (e.g. Cashier only petty-cash disbursements, capped amount from settings).

**Rule:** every controller action is guarded by `->middleware('permission:xxx')` or Policy classes. Never check roles directly in code — always check **permissions** (roles can change).

---

## 4. FRONTEND STRUCTURE

Two faces of the same Laravel app:

1. **Staff Panel** — `/admin/*` (guard: `web`, staff roles)
2. **Client Portal** — `/portal/*` (guard: `web`, role `Client`) 
3. **Public site** — `/` landing page (company profile, tracking-by-number, contact)

### 4.1 Layout Anatomy (Staff Panel)
```
┌──────────────────────────────────────────────────────┐
│ Topbar: logo | global search (File No / BL / TANSAD) │
│         notifications 🔔 | user menu                 │
├──────────┬───────────────────────────────────────────┤
│ Sidebar  │  Breadcrumb                               │
│ (menu    │  Page header + action buttons             │
│  per     │  Content: cards / tables / forms / tabs   │
│  role)   │  DataTables: search, filter, export       │
└──────────┴───────────────────────────────────────────┘
```

### 4.2 Full Sidebar Menu — Super Admin / Admin
```
📊 Dashboard
📁 Shipments (Files)
   ├── All Files
   ├── Open New File
   ├── Import Files
   ├── Export Files
   ├── Transit Files
   └── Closed Files
🛃 Customs
   ├── Declarations (TANSAD)
   ├── Tariff / HS Codes
   ├── Exemptions & Permits
   └── Customs Queries
📄 Documents
   ├── Document Register
   ├── Pending Originals
   └── Document Types
🚚 Transport
   ├── Delivery Orders
   ├── Trips / Deliveries
   ├── Trucks
   ├── Drivers
   └── Transporters (3rd party)
💰 Finance
   ├── Proforma Invoices
   ├── Invoices
   ├── Receipts / Payments
   ├── Disbursements
   ├── Petty Cash
   ├── Expenses
   ├── Debtors (Aging)
   └── Charge Items (price list)
👥 Clients
   ├── All Clients
   ├── Add Client
   └── Client Statements
🏢 Partners
   ├── Shipping Lines
   ├── ICDs / Terminals
   └── Government Agencies
📈 Reports
   ├── Operations Report
   ├── Revenue Report
   ├── Disbursement Report
   ├── Officer Performance
   ├── Client Volume Report
   └── Demurrage/Storage Report
🔔 Notifications & SMS Log
⚙️ Administration (Super Admin only)
   ├── Users
   ├── Roles & Permissions
   ├── Company Settings
   ├── Numbering Formats
   ├── Audit Trail / Activity Log
   └── Backups
```

### 4.3 Menus per Role (what each role sees)

**Operations Manager:** Dashboard, Shipments (all), Customs, Documents, Transport, Clients (view/create), Reports → Operations, Notifications.

**Declaration Officer:** Dashboard (my files), Shipments (assigned), Customs → Declarations / HS Codes / Queries, Documents (upload), Notifications.

**Documentation Officer:** Dashboard, Shipments (create/view), Documents (full), Clients (create/view), Notifications.

**Clearing Officer:** Dashboard (my field jobs), Shipments (assigned — milestone update view), Documents (upload gate passes, release orders), Notifications.

**Transport Officer:** Dashboard, Transport (full), Shipments (delivery tab only), Documents (upload POD), Notifications.

**Accountant:** Dashboard (finance KPIs), Finance (full), Clients → Statements, Reports → all finance, Shipments (read-only for costing).

**Cashier:** Dashboard (cash position), Finance → Petty Cash + Receipts + small Disbursements, Notifications.

**Client (Portal):**
```
🏠 Overview (active shipments + balance)
📦 My Shipments  → status timeline per file
📄 My Documents  → download BL, TANSAD, DO, POD
💳 My Invoices & Receipts
📑 My Statement
🔍 Track Shipment (by File No / BL)
👤 Profile / Users (client can add own sub-users)
💬 Support / Queries
```

### 4.4 Key Screens (per module)
- **File Detail Page (the heart of the system)** — Tabs:
  `Summary | Consignment | Documents | Declaration | Charges & Costing | Milestones/Timeline | Transport | Invoices | Notes & Queries | Activity`
- **Kanban board** for operations: columns = milestone stages, cards = files.
- **Dashboard widgets:** files by status, files at risk of demurrage (free days countdown), unpaid invoices, today's deliveries, revenue this month, officer workload.

### 4.5 Frontend Folder Structure (Blade + Livewire)
```
resources/
├── views/
│   ├── layouts/            app.blade.php, portal.blade.php, guest.blade.php
│   ├── components/         card, stat-widget, status-badge, timeline, modal, datatable
│   ├── admin/
│   │   ├── dashboard/
│   │   ├── files/          index, create, show(tabs), edit
│   │   ├── declarations/
│   │   ├── documents/
│   │   ├── transport/
│   │   ├── finance/        invoices, receipts, disbursements, pettycash, expenses
│   │   ├── clients/
│   │   ├── partners/
│   │   ├── reports/
│   │   └── settings/       users, roles, company, numbering, activitylog
│   ├── portal/             overview, shipments, documents, invoices, statement, track
│   └── public/             home, track, contact
├── js/  (Alpine components, charts - ApexCharts)
└── css/ (Tailwind)

app/Livewire/
├── Files/ (FileTable, FileForm, MilestoneUpdater, ChargeSheet)
├── Finance/ (InvoiceBuilder, ReceiptForm, DebtorsTable)
├── Transport/ (TripBoard)
└── Shared/ (GlobalSearch, NotificationBell)
```

---

## 5. BACKEND STRUCTURE (LARAVEL)

### 5.1 Folder Structure
```
app/
├── Models/
│   ├── User.php, Client.php, ClientContact.php
│   ├── ShipmentFile.php, Consignment.php, Container.php, Milestone.php
│   ├── Declaration.php, DeclarationItem.php, HsCode.php, CustomsQuery.php
│   ├── Document.php, DocumentType.php
│   ├── Trip.php, Truck.php, Driver.php, Transporter.php, DeliveryOrder.php
│   ├── ProformaInvoice.php, Invoice.php, InvoiceItem.php, Receipt.php
│   ├── Disbursement.php, PettyCash.php, Expense.php, ChargeItem.php
│   ├── ShippingLine.php, Terminal.php, Agency.php
│   └── Setting.php, NumberSequence.php
├── Http/
│   ├── Controllers/
│   │   ├── Admin/    (FileController, DeclarationController, InvoiceController…)
│   │   ├── Portal/   (ShipmentController, InvoiceController, TrackController)
│   │   ├── Api/V1/   (mobile / integrations — Sanctum)
│   │   └── PublicController.php
│   ├── Requests/     (StoreFileRequest, StoreInvoiceRequest… all validation here)
│   └── Middleware/   (EnsureClientOwnsResource, ActiveUserOnly)
├── Policies/         (FilePolicy, InvoicePolicy, DocumentPolicy…)
├── Services/         ← BUSINESS LOGIC LIVES HERE, not in controllers
│   ├── FileNumberService.php       (generates CAF/IMP/2026/0001 atomically)
│   ├── MilestoneService.php        (advance stage + fire events)
│   ├── CostingService.php          (charges, disbursements vs invoice = margin)
│   ├── InvoiceService.php          (proforma→invoice, VAT 18%, PDF)
│   ├── DemurrageService.php        (free days countdown, alerts)
│   └── StatementService.php        (client ledger)
├── Events/    (FileCreated, MilestoneReached, InvoicePaid, QueryRaised)
├── Listeners/ (SendClientSms, SendClientEmail, NotifyOpsManager)
├── Notifications/ (ShipmentStatusChanged, InvoiceIssued, DemurrageWarning)
├── Jobs/      (GenerateMonthlyReport, SendBulkStatements)
├── Exports/   (FilesExport, DebtorsExport — maatwebsite)
└── Observers/ (FileObserver — auto-log, auto-number)

routes/
├── web.php       (public + auth)
├── admin.php     (prefix admin, middleware: auth, role:staff)
├── portal.php    (prefix portal, middleware: auth, role:Client)
└── api.php       (prefix api/v1, Sanctum)

database/
├── migrations/  seeders/ (RoleSeeder, PermissionSeeder, DemoDataSeeder)
```

### 5.2 Golden Rules
1. **Thin controllers, fat services.** Controller = validate (FormRequest) → call Service → return.
2. **Policies for ownership:** Client can only see `ShipmentFile` where `client_id = auth()->user()->client_id`.
3. **Everything numbered via `NumberSequence`** table with `lockForUpdate()` inside a DB transaction — no duplicate File/Invoice numbers ever.
4. **Money = `DECIMAL(15,2)`**, never float. Currency column everywhere (TZS/USD) + exchange_rate snapshot on the file.
5. **Soft deletes** on all business tables. Hard delete = Super Admin + logged.
6. **Status columns are enums backed by PHP Enum classes** (`FileStatus::Open`), never magic strings.
7. Every state change fires an **Event** → notifications & activity log happen in Listeners, not controllers.

---

## 6. DATABASE DESIGN (MySQL)

### 6.1 ERD Summary (main relationships)
```
clients 1─∞ shipment_files 1─∞ consignments 1─∞ containers
shipment_files 1─1 declarations 1─∞ declaration_items ∞─1 hs_codes
shipment_files 1─∞ documents ∞─1 document_types
shipment_files 1─∞ milestones (timeline)
shipment_files 1─∞ trips ∞─1 trucks ∞─1 drivers / transporters
shipment_files 1─∞ file_charges ∞─1 charge_items
shipment_files 1─∞ invoices 1─∞ invoice_items ; invoices 1─∞ receipts
shipment_files 1─∞ disbursements
users ∞─∞ roles ∞─∞ permissions   (spatie)
clients 1─∞ users (portal sub-users)
```

### 6.2 Core Tables

**users** — id, name, email(unique), phone, password, client_id(nullable FK), is_active, last_login_at, timestamps, softDeletes.

**clients** — id, code(unique e.g. CL-0001), name, tin, vrn, type(company/individual), phone, email, address, credit_limit DECIMAL, payment_terms_days, is_active, created_by.

**shipment_files** — id, file_no(unique), client_id FK, type ENUM(import, export, transit), mode ENUM(sea, air, road), status ENUM(draft, open, declaration, port_clearance, transport, delivered, invoiced, closed, cancelled), bl_awb_no, vessel_flight, shipping_line_id, port_of_loading, port_of_discharge, terminal_id, eta DATE, discharge_date, free_days_expiry DATE, currency, exchange_rate, assigned_declaration_officer_id, assigned_clearing_officer_id, opened_by, opened_at, closed_at, remarks, softDeletes. *Indexes: file_no, bl_awb_no, client_id, status.*

**consignments** — id, shipment_file_id FK, description, marks_numbers, packages_count, package_type, gross_weight, volume_cbm, cargo_value DECIMAL, cargo_currency.

**containers** — id, consignment_id FK, container_no, size ENUM(20ft, 40ft, 40hc), seal_no, status ENUM(at_port, at_icd, released, gated_out, delivered, empty_returned), empty_return_due DATE.

**declarations** — id, shipment_file_id FK(unique), tansad_no, regime_code, declared_at, assessed_at, duty_amount, vat_amount, other_taxes, total_taxes, status ENUM(draft, lodged, queried, assessed, paid, released), officer_id, notes.

**declaration_items** — id, declaration_id FK, hs_code_id FK, description, qty, customs_value, duty_rate, computed_duty.

**hs_codes** — id, code, description, duty_rate, vat_rate, notes.

**customs_queries** — id, declaration_id FK, raised_at, query_text, response_text, responded_at, status ENUM(open, responded, resolved).

**document_types** — id, name (BL, AWB, Commercial Invoice, Packing List, TANSAD, DO, Gate Pass, Release Order, Exemption, Permit, POD, Interchange), is_mandatory_import, is_mandatory_export.

**documents** — id, shipment_file_id FK, document_type_id FK, reference_no, file_path, original_received BOOL, received_at, uploaded_by, verified_by, verified_at.

**milestones** — id, shipment_file_id FK, stage ENUM(file_opened, docs_complete, declaration_lodged, taxes_paid, customs_released, do_collected, port_charges_paid, verification, gate_out, in_transit, delivered, empty_returned, file_closed), happened_at, recorded_by, remarks. *(This table = the client-facing tracking timeline.)*

**delivery_orders** — id, shipment_file_id FK, do_no, shipping_line_id, issued_at, expiry_date, deposit_amount, deposit_refunded BOOL.

**trucks** — id, plate_no, trailer_no, capacity_tons, transporter_id(nullable = own fleet), is_active.
**drivers** — id, name, phone, license_no, license_expiry, truck_id nullable.
**transporters** — id, name, tin, phone, contact_person, rate_notes.

**trips** — id, shipment_file_id FK, container_id nullable FK, truck_id, driver_id, origin, destination, dispatched_at, delivered_at, status ENUM(planned, loading, in_transit, delivered, closed), pod_document_id nullable, transport_cost, notes.

**charge_items** — id, name (Agency Fee, Transport, Documentation, TRA Duty, Port Charges, Shipping Line, ICD Storage, VAT…), category ENUM(income, disbursement), default_amount, taxable BOOL.

**file_charges** — id, shipment_file_id FK, charge_item_id FK, description, amount, currency, is_billable BOOL. *(costing sheet per file)*

**proforma_invoices / invoices** — id, invoice_no(unique), shipment_file_id FK, client_id FK, date, due_date, currency, exchange_rate, subtotal, vat_amount, total, amount_paid, status ENUM(draft, sent, partially_paid, paid, overdue, cancelled), created_by.
**invoice_items** — id, invoice_id FK, charge_item_id, description, qty, unit_price, vat_rate, line_total.

**receipts** — id, receipt_no(unique), invoice_id nullable FK, client_id FK, date, amount, method ENUM(cash, bank, mobile_money, cheque), reference, received_by.

**disbursements** — id, shipment_file_id FK, payee ENUM/(agency_id FK), description, amount, currency, paid_at, method, reference_no, receipt_scan(document_id), approved_by, recorded_by. *(money paid OUT on behalf of client — TRA, port, line)*

**petty_cash** — id, date, description, amount, type ENUM(in, out), shipment_file_id nullable, recorded_by, approved_by.

**expenses** — id, date, category, description, amount, receipt_path, recorded_by. *(company overheads, not tied to files)*

**shipping_lines / terminals / agencies** — id, name, code, contacts.

**number_sequences** — id, key (file_import, file_export, invoice, receipt), prefix, year, last_number. Locked & incremented in transaction.

**settings** — key, value (company name, TIN, VAT rate 18, logo, SMS sender ID, petty cash limit, free-days default…).

**activity_log** — (spatie) subject, causer, event, properties(old/new), created_at.

**notifications** — Laravel default table + `sms_logs` (to, message, status, cost, provider_ref).

### 6.3 Critical Indexes & Constraints
- Unique: `file_no`, `invoice_no`, `receipt_no`, `tansad_no`, `container_no + shipment_file` combo.
- FK all with `restrictOnDelete` for finance tables (never lose money history), `cascadeOnDelete` only for child detail rows (invoice_items).
- Composite index `(client_id, status)` on shipment_files and invoices — powers the portal & debtors.

---

## 7. WORKFLOWS

### 7.1 IMPORT CLEARANCE — the main flow
```
1. CLIENT INSTRUCTION
   Client sends docs (BL, Invoice, Packing List)
   → Documentation Officer creates Client (if new) + opens FILE
   → System generates File No, status = OPEN, Event: FileCreated
   → SMS/Email to client: "File CAF/IMP/2026/0142 opened"

2. DOCUMENT REGISTRATION
   Doc Officer uploads scans, marks originals received
   → System checks mandatory docs checklist → milestone: docs_complete

3. DECLARATION (Ops Mgr assigns Declaration Officer)
   Officer classifies HS codes → creates TANSAD entry → lodges
   → status = DECLARATION, milestone: declaration_lodged
   → If TRA raises QUERY → customs_queries record → resolve loop
   → TRA assessment received → duty/vat amounts recorded

4. TAX & CHARGES PAYMENT (Accountant)
   Client pays taxes (direct or through CAF)
   → Disbursements recorded: TRA duties, Port charges, Shipping line DO fee
   → milestones: taxes_paid, do_collected, port_charges_paid

5. PORT / ICD CLEARANCE (Clearing Officer — field)
   Physical verification (if flagged) → Customs release → Gate pass
   → milestones: verification, customs_released, gate_out
   ⚠ DemurrageService watches free_days_expiry → alerts at D-3, D-1, D-0

6. TRANSPORT & DELIVERY (Transport Officer)
   Assign truck+driver (or 3rd-party transporter) → Trip created
   → in_transit → delivered → upload POD (signed delivery note)
   → Container: track empty return (deposit refund!)

7. BILLING (Accountant)
   Costing sheet (file_charges) reviewed → Proforma → Invoice (agency fee
   + billable disbursements + VAT 18%) → PDF emailed to client
   → Receipts recorded against invoice → status paid

8. FILE CLOSURE (Ops Manager)
   Checklist: delivered ✔ empty returned ✔ deposit refunded ✔ invoice paid ✔
   originals returned ✔ → status = CLOSED. Locked from edits (except SA).
```

### 7.2 Export Flow (condensed)
Booking with shipping line → file opened → export declaration → cargo received/stuffed → customs release → gate-in at port → shipped on board → BL issued to client → invoice → close.

### 7.3 Finance Flow
```
file_charges (planned costs)          disbursements (actual money out)
        └──────────────┬──────────────────────┘
                 CostingService
                        │
        Invoice (income) – Disbursements (pass-through)
                        │
              MARGIN per file  →  Revenue Report
Receipts → invoice.amount_paid → Debtors Aging (0-30/31-60/61-90/90+)
```

### 7.4 Status Machine (shipment_files.status)
`draft → open → declaration → port_clearance → transport → delivered → invoiced → closed`
(+ `cancelled` from any pre-delivered state, permission-guarded). Transitions enforced in `MilestoneService` — you cannot jump stages.

---

## 8. FULL CRUD MODULE LIST

| # | Module | C | R | U | D | Extra actions |
|---|--------|---|---|---|---|---------------|
| 1 | Users | ✔ | ✔ | ✔ | soft | activate/deactivate, reset password, assign role |
| 2 | Roles & Permissions | ✔ | ✔ | ✔ | ✔ | sync permissions |
| 3 | Clients | ✔ | ✔ | ✔ | soft | statement, credit limit, portal access toggle |
| 4 | Shipment Files | ✔ | ✔ | ✔ | soft | assign officers, change status, close, cancel, job card PDF |
| 5 | Consignments/Containers | ✔ | ✔ | ✔ | ✔ | container status update, empty return |
| 6 | Declarations | ✔ | ✔ | ✔ | soft | lodge, record assessment, mark released |
| 7 | Declaration Items | ✔ | ✔ | ✔ | ✔ | auto duty computation |
| 8 | HS Codes | ✔ | ✔ | ✔ | soft | import from Excel |
| 9 | Customs Queries | ✔ | ✔ | ✔ | ✔ | respond, resolve |
| 10 | Document Types | ✔ | ✔ | ✔ | soft | mandatory flags |
| 11 | Documents | ✔ | ✔ | ✔ | soft | upload, download, verify, original tracking |
| 12 | Milestones | ✔ | ✔ | ✔ | ✔(SA) | timeline view, auto-notify client |
| 13 | Delivery Orders | ✔ | ✔ | ✔ | soft | deposit refund tracking |
| 14 | Trucks / Drivers / Transporters | ✔ | ✔ | ✔ | soft | license expiry alerts |
| 15 | Trips | ✔ | ✔ | ✔ | soft | dispatch, deliver, attach POD |
| 16 | Charge Items | ✔ | ✔ | ✔ | soft | price list |
| 17 | File Charges (Costing) | ✔ | ✔ | ✔ | ✔ | copy to proforma |
| 18 | Proforma Invoices | ✔ | ✔ | ✔ | soft | convert to invoice, PDF, email |
| 19 | Invoices | ✔ | ✔ | ✔* | soft | send, PDF, record payment (*locked once paid) |
| 20 | Receipts | ✔ | ✔ | ✘ | void-only | PDF, email |
| 21 | Disbursements | ✔ | ✔ | ✔ | soft | approve, attach receipt scan |
| 22 | Petty Cash | ✔ | ✔ | ✔ | soft | approve, daily balance |
| 23 | Expenses | ✔ | ✔ | ✔ | soft | monthly summary |
| 24 | Partners (Lines/Terminals/Agencies) | ✔ | ✔ | ✔ | soft | |
| 25 | Settings | — | ✔ | ✔ | — | SA only |
| 26 | Number Sequences | — | ✔ | ✔ | — | SA only |
| 27 | Reports | — | ✔ | — | — | filter, Excel/PDF export |
| 28 | Activity Log | — | ✔ | — | — | filter by user/model/date |

**Finance integrity rule:** invoices & receipts are never hard-edited after issue — corrections via credit note / void with reason, always logged.

---

## 9. API STRUCTURE (api/v1 — Sanctum)

```
POST   /api/v1/login
GET    /api/v1/track/{fileNo}                 (public, rate-limited)
GET    /api/v1/portal/shipments               (client token)
GET    /api/v1/portal/shipments/{id}          + /documents + /milestones
GET    /api/v1/portal/invoices  /statement
POST   /api/v1/driver/trips/{id}/status       (driver app)
POST   /api/v1/driver/trips/{id}/pod          (upload photo)
```
Resources via `JsonResource`, pagination default 25, all guarded by Policies.

---

## 10. NOTIFICATIONS MATRIX

| Event | Client SMS/Email | Ops Mgr | Accountant |
|---|---|---|---|
| File opened | ✔ | | |
| Declaration lodged / assessed | ✔ | ✔ | ✔ (taxes due) |
| Customs query raised | ✔ | ✔ | |
| Demurrage warning (D-3/D-1) | ✔ | ✔ | |
| Gate out / In transit / Delivered | ✔ | | |
| Invoice issued / Receipt | ✔ | | ✔ |
| Invoice overdue (7d, 14d) | ✔ | | ✔ |
| Driver license expiring | | ✔(Transport) | |

---

## 11. REPORTS

1. **Operations:** files opened/closed per period, by type/mode/officer; average clearance days (open→gate_out); files stuck per stage.
2. **Demurrage/Storage exposure:** files past free days + estimated cost.
3. **Revenue:** invoiced vs collected, margin per file, revenue by client.
4. **Disbursement report:** per authority (TRA/Port/Lines) per period.
5. **Debtors aging** + client statements (batch email monthly).
6. **Officer performance:** files handled, avg days, queries per officer.
7. **Transport:** trips per truck, cost per route, POD compliance.

All reports: date-range + filters, export Excel & PDF, permission-guarded.

---

## 12. KNOWN ISSUES / EDGE CASES TO HANDLE (design for these from day 1)

1. **Duplicate numbers under concurrency** → NumberSequence with `lockForUpdate()` in transaction.
2. **Multi-currency mess** → snapshot `exchange_rate` on file & invoice at creation; never convert live historical records.
3. **Client disputes invoice** → credit notes module (phase 2), never edit issued invoices.
4. **Partial payments** → receipts many-to-one invoice, auto status `partially_paid`.
5. **One BL, many containers, many trips** → containers & trips are children, not columns.
6. **TRA query loops** → queries table with open/resolved status, SLA timer on dashboard.
7. **Demurrage blame** → milestones timestamped + recorded_by = evidence of where delay happened.
8. **Container deposit forgotten** → dashboard widget "Deposits not refunded > 30 days".
9. **Original documents lost** → original_received flag + "Pending Originals" register.
10. **Officer leaves company** → deactivate user (never delete), reassign open files (bulk action).
11. **Client sub-users** → users.client_id; policy restricts to own client data only.
12. **Big file uploads** → validate mime/size (pdf, jpg, png ≤ 10MB), store outside public, serve via signed routes.
13. **Cancelled files with money spent** → cancelled files still billable for disbursements incurred.
14. **Year change numbering** → sequence resets per year via key `file_import_2027`.
15. **Timezone** → app timezone `Africa/Dar_es_Salaam`, store UTC, display local.

---

## 13. SECURITY CHECKLIST

- HTTPS only, `APP_DEBUG=false` in production.
- FormRequest validation on every write; mass-assignment via `$fillable` only.
- Policies + permission middleware on every route; client data isolation tested.
- Signed URLs for document downloads; no direct storage links.
- Rate-limit login & public tracking; lockout after 5 failed attempts.
- 2FA for Super Admin & Accountant (Fortify).
- Activity log on all finance models; DB backup nightly, tested restore monthly.
- File uploads scanned/validated; stored with hashed names.

---

## 14. DEVELOPMENT PHASES (suggested roadmap)

| Phase | Scope | Est. |
|---|---|---|
| 1 | Auth, roles, clients, files, documents, milestones, basic dashboard | 4–5 wks |
| 2 | Declarations, customs queries, HS codes, notifications (SMS/Email) | 3 wks |
| 3 | Finance: charges, invoices, receipts, disbursements, debtors | 4 wks |
| 4 | Transport module + driver mobile view | 2–3 wks |
| 5 | Client portal + public tracking | 2 wks |
| 6 | Reports, exports, activity log UI, backups, hardening | 2 wks |

---

## 15. SEEDERS TO SHIP WITH v1

- Roles + full permission set (Section 3).
- Document types (BL, AWB, Invoice, Packing List, TANSAD, DO, Gate Pass, POD…).
- Charge items (Agency Fee, Transport, TRA Duty, Port Charges, Wharfage, Shipping Line, ICD Storage, Documentation Fee).
- Milestone stages, shipping lines (Maersk, MSC, CMA CGM, PIL…), terminals/ICDs.
- Demo Super Admin: `admin@caf.co.tz` (force password change on first login).

---

*End of document — CAF v1.0 System Guideline.*
