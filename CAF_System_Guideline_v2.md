# CAF — Clearing And Forwarding Management System
### Full System Design & Development Guideline Document
**Stack:** Laravel 11 (Backend) + Blade/Livewire or Vue 3 (Frontend) + MySQL
**Prepared for:** Internal Development Team
**Version:** 2.0 — Three Business Lines Edition

---

## 1. SYSTEM OVERVIEW

CAF is a web-based management system for a company operating in Tanzania with **THREE business lines** under one roof. The system digitizes all three, sharing one client base, one finance engine, and one document store.

### 1.0 The Three Business Lines

**LINE 1 — CLEARING & FORWARDING (core business)**
Clients import cargo from abroad through Tanzanian ports (Dar es Salaam, Tanga, Mtwara) and border posts. CAF handles the full customs clearing process with TRA — for containers, motor vehicles, machinery, general cargo — including TANSAD declaration, duty/tax payment, port/ICD charges, physical verification, and release. CAF operates at **all Tanzanian ports and all border stations** (Tunduma, Kasumulu, Rusumo, Kabanga, Mutukula, Holili, Namanga, Sirari, Horohoro, Kigoma port, Mwanza port, etc.).

**LINE 2 — INSURANCE AGENCY**
CAF acts as a licensed insurance agent. It does not underwrite risk itself — it works with partner **insurers** (underwriters). When a client needs cover — Marine Cargo insurance for an import, Goods-In-Transit (GIT) for road movement, Motor insurance for a cleared vehicle, or any other class — CAF quotes, places the policy with an insurer, collects premium, and earns **commission**. Insurance can be sold standalone OR attached to a clearing/transport job (cross-sell).

**LINE 3 — TRANSPORTATION & CROSS-BORDER LOGISTICS**
Once cargo lands at a Tanzanian port (e.g., Dar es Salaam), the client may need it moved anywhere — **within Tanzania** (Mwanza, Mbeya, Arusha…) or **across borders in transit** to DRC (Congo), Zambia, Burundi, Rwanda, Uganda, Malawi. CAF arranges the transport (own fleet or subcontracted transporters), manages transit documentation (T1, RCTG/COMESA carnet, transit bonds), border crossing formalities, GPS/checkpoint tracking, and proof of delivery. Transport can be sold standalone (client already cleared elsewhere) or as a continuation of a clearing job.

**The power of the system:** one client, one profile, three revenue streams — a client importing a container gets clearing (Line 1) + marine/GIT cover (Line 2) + trucking to Lubumbashi (Line 3), all visible in one File group, one statement, one portal.

### 1.1 Core Objectives
- Track every job (clearing, transport, insurance) from opening to closing, each with its own workflow.
- Manage customs documentation (Bill of Lading, TANSAD, Packing List, Invoices, Exemptions, Permits) and transit documents (T1, bonds, RCTG carnets).
- Manage insurance placements: quotes, policies, premiums, insurer remittance, and CAF commission.
- Manage payments to authorities (TRA duties, Port charges, Shipping line, ICD, transit bonds) and reconcile them.
- Bill clients (proforma → invoice → receipt) across all three lines and track debtors on one statement.
- Support multiple **branches/stations** (HQ Dar, ports, and every border post) — every job belongs to a station.
- Give each role exactly what they need — nothing more (RBAC + Permissions).
- Give clients a portal to see status, documents, policies, and invoices in real time.
- Full audit trail: who did what, when.
- Cross-sell visibility: a clearing job without insurance or transport attached = a sales opportunity flagged on the dashboard.

### 1.2 Key Business Terms
| Term | Meaning |
|---|---|
| Job / File | One unit of work. `business_line` = clearing, transport, or insurance. File No e.g. `CAF/IMP/2026/0001`, `CAF/TR/2026/0001`, `CAF/INS/2026/0001` |
| BL / AWB | Bill of Lading (sea) / Airway Bill (air) |
| TANSAD | Tanzania Single Administrative Document — the customs declaration |
| DO | Delivery Order from the shipping line |
| Demurrage / Storage | Charges when cargo overstays at port/ICD |
| Disbursement | Money CAF pays on behalf of the client (duties, port charges, bonds) |
| Agency Fee | CAF's own service fee (the profit line) |
| Transit (T1) | Cargo passing through Tanzania under customs bond to another country (e.g. DRC, Zambia) |
| Transit Bond | Security guaranteeing duties if transit cargo is diverted locally |
| RCTG Carnet | COMESA Regional Customs Transit Guarantee for cross-border movement |
| Marine Cargo Cover | Insurance on imported cargo (mandatory for TZ imports — local marine cover rule) |
| GIT | Goods-In-Transit insurance for road transport |
| Insurer / Underwriter | The insurance company carrying the risk; CAF is the agent |
| Premium / Commission | Client pays premium → insurer pays CAF a % commission |
| Station / Branch | CAF office at a port or border post (Dar HQ, Tunduma, Rusumo, Mwanza…) |
| POD | Proof of Delivery — signed delivery note at destination |

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
| 10 | **Insurance Officer** | Bima desk | Manages insurers, quotes, policies, premium collection follow-up, commission tracking, renewals & claims assistance. |
| 11 | **Branch Manager** | Station head (e.g. Tunduma) | Full operations view **scoped to own branch/station only**; opens & monitors jobs at that station. |
| 12 | **Client** | Customer | Portal only: own jobs, documents, policies, invoices, statements, tracking. |
| 13 | **Driver** (optional) | Truck driver | Mobile-friendly view: assigned trips, update delivery status, upload POD. |

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

**Additional permissions for v2.0:** `insurers.crud`, `insurance-quotes.crud`, `insurance-policies.crud`, `insurance-policies.issue`, `commissions.view`, `claims.crud` (Insurance Officer + Admin/SA); `transit-docs.crud`, `border-milestones.update` (Transport Officer, Clearing Officer, Branch Manager); `branches.manage` (SA only); `reports.insurance` (Insurance Officer, Accountant, Admin, SA).

**Branch scoping:** users have a `branch_id`. Branch Manager & station staff see only jobs where `jobs.branch_id = user.branch_id` (global query scope). HQ roles (SA, Admin, Ops Mgr, Accountant, Insurance Officer) see all branches.

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
📊 Dashboard (all 3 business lines KPIs + cross-sell opportunities)
📁 Clearing Jobs (Line 1)
   ├── All Files
   ├── Open New File
   ├── Import Files
   ├── Export Files
   ├── Transit Files (T1 → DRC/Zambia/etc.)
   ├── Motor Vehicle Files
   └── Closed Files
🛃 Customs
   ├── Declarations (TANSAD)
   ├── Tariff / HS Codes
   ├── Exemptions & Permits
   ├── Transit Docs & Bonds (T1, RCTG)
   └── Customs Queries
🛡️ Insurance (Line 2)
   ├── Quotes
   ├── Policies (all classes: Marine, GIT, Motor, Other)
   ├── Renewals Due
   ├── Premium Collection (unpaid premiums)
   ├── Commissions (earned / received from insurers)
   ├── Claims Assistance
   └── Insurers (partner companies)
🚚 Transport Jobs (Line 3)
   ├── All Transport Jobs
   ├── New Transport Job
   ├── Local Deliveries (within TZ)
   ├── Cross-Border / Transit Trips (DRC, Zambia…)
   ├── Trip Board (live status)
   ├── Delivery Orders
   ├── Trucks
   ├── Drivers
   └── Transporters (3rd party / subcontracted)
📄 Documents
   ├── Document Register
   ├── Pending Originals
   └── Document Types
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
   ├── Insurers (also reachable from Insurance menu)
   ├── Border Agencies / Government Agencies
   └── Transporters
🏛️ Branches / Stations
   ├── All Stations (Dar HQ, ports, border posts)
   └── Station Performance
📈 Reports
   ├── Operations Report (per line, per branch)
   ├── Revenue Report (per business line: Clearing / Insurance / Transport)
   ├── Insurance Production Report (premiums, commissions per insurer/class)
   ├── Transit / Cross-Border Report (trips per corridor, border dwell time)
   ├── Disbursement Report
   ├── Officer Performance
   ├── Client Volume Report (with cross-sell penetration)
   └── Demurrage/Storage Report
🔔 Notifications & SMS Log
⚙️ Administration (Super Admin only)
   ├── Users
   ├── Roles & Permissions
   ├── Branches / Stations Management
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

**Insurance Officer:** Dashboard (policies expiring, unpaid premiums, monthly production), Insurance (full: quotes, policies, renewals, commissions, claims, insurers), Clients (view/create), Clearing & Transport jobs (read-only — to spot uninsured jobs = leads), Documents (upload policy docs), Reports → Insurance, Notifications.

**Branch Manager (e.g. Tunduma station):** Dashboard (own station), Clearing Jobs (own station), Transport Jobs (border milestones), Documents, Customs → Transit Docs, Notifications. *Everything auto-filtered by `branch_id`.*

**Client (Portal):**
```
🏠 Overview (active jobs across all 3 services + balance)
📦 My Shipments   → clearing files with status timeline
🚚 My Transport   → trips, live status, border crossings, POD download
🛡️ My Insurance   → policies, cover notes, expiry dates, renew request
📄 My Documents   → download BL, TANSAD, DO, T1, policy, POD
💳 My Invoices & Receipts
📑 My Statement (all services combined)
🔍 Track Shipment (by File No / BL / Trip No)
🛒 Request Service (new clearing / transport / insurance quote)
👤 Profile / Users (client can add own sub-users)
💬 Support / Queries
```

### 4.4 Key Screens (per module)
- **File Detail Page (the heart of the system)** — Tabs:
  `Summary | Consignment | Documents | Declaration | Charges & Costing | Milestones/Timeline | Transport | Insurance | Invoices | Notes & Queries | Activity`
  → The **Insurance tab** shows attached policies + a one-click "Create Marine/GIT quote for this job" button (cross-sell).
  → The **Transport tab** shows attached trips + "Create transport job from this file" button.
- **Transport Job Detail Page** — Tabs: `Summary | Route & Borders | Transit Docs (T1/Bond) | Trips & Trucks | Milestones | Insurance (GIT) | Invoices | Activity`
- **Policy Detail Page** — insurer, class, sum insured, premium, commission, linked job, documents (cover note, policy schedule), renewal history.
- **Kanban boards:** clearing files by milestone stage; transport trips by trip status (planned → loading → in transit → at border → delivered).
- **Dashboard widgets:** jobs by status per business line, files at risk of demurrage, trips currently at borders, policies expiring in 30 days, unpaid premiums, unpaid invoices, revenue this month split by line (Clearing vs Insurance commission vs Transport), cross-sell leads (cleared jobs with no insurance/transport), officer workload, branch comparison.

### 4.5 Frontend Folder Structure (Blade + Livewire)
```
resources/
├── views/
│   ├── layouts/            app.blade.php, portal.blade.php, guest.blade.php
│   ├── components/         card, stat-widget, status-badge, timeline, modal, datatable
│   ├── admin/
│   │   ├── dashboard/
│   │   ├── files/          index, create, show(tabs), edit        (Line 1)
│   │   ├── declarations/   + transit-docs/
│   │   ├── insurance/      quotes, policies, renewals, commissions, claims, insurers   (Line 2)
│   │   ├── transport-jobs/ index, create, show(tabs), trip-board  (Line 3)
│   │   ├── documents/
│   │   ├── transport/      trucks, drivers, transporters, delivery-orders
│   │   ├── finance/        invoices, receipts, disbursements, pettycash, expenses
│   │   ├── clients/
│   │   ├── partners/
│   │   ├── branches/
│   │   ├── reports/
│   │   └── settings/       users, roles, company, numbering, activitylog
│   ├── portal/             overview, shipments, transport, insurance, documents, invoices, statement, track, request-service
│   └── public/             home, services(3 lines), track, contact
├── js/  (Alpine components, charts - ApexCharts)
└── css/ (Tailwind)

app/Livewire/
├── Files/ (FileTable, FileForm, MilestoneUpdater, ChargeSheet)
├── Insurance/ (QuoteForm, PolicyForm, RenewalsTable, CommissionTracker, ClaimForm)
├── TransportJobs/ (JobForm, TripBoard, BorderMilestoneUpdater, TransitDocForm)
├── Finance/ (InvoiceBuilder, ReceiptForm, DebtorsTable)
└── Shared/ (GlobalSearch, NotificationBell, CrossSellBanner)
```

---

## 5. BACKEND STRUCTURE (LARAVEL)

### 5.1 Folder Structure
```
app/
├── Models/
│   ├── User.php, Client.php, ClientContact.php, Branch.php
│   ├── ShipmentFile.php, Consignment.php, Container.php, Milestone.php
│   ├── Declaration.php, DeclarationItem.php, HsCode.php, CustomsQuery.php
│   ├── TransitDocument.php  (T1, bonds, RCTG carnets)
│   ├── Insurer.php, InsuranceQuote.php, InsurancePolicy.php,
│   │   InsuranceClass.php, Commission.php, InsuranceClaim.php
│   ├── TransportJob.php, Trip.php, TripCheckpoint.php,
│   │   Truck.php, Driver.php, Transporter.php, DeliveryOrder.php, Route.php
│   ├── Document.php, DocumentType.php
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
│   ├── PremiumService.php          (premium calc: sum insured × rate, min premium, levies)
│   ├── CommissionService.php       (commission earned per policy, insurer remittance recon)
│   ├── RenewalService.php          (finds policies expiring in 30/14/7 days → notify)
│   ├── TransitService.php          (T1/bond lifecycle, bond acquittal at exit border)
│   ├── TripTrackingService.php     (checkpoints, border crossing timestamps, ETA)
│   ├── CrossSellService.php        (cleared jobs missing insurance/transport → leads)
│   └── StatementService.php        (client ledger, all business lines combined)
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
branches 1─∞ users ; branches 1─∞ shipment_files ; branches 1─∞ transport_jobs

LINE 1 — CLEARING
clients 1─∞ shipment_files 1─∞ consignments 1─∞ containers
shipment_files 1─1 declarations 1─∞ declaration_items ∞─1 hs_codes
shipment_files 1─∞ transit_documents (T1/bond, for transit files)
shipment_files 1─∞ documents ∞─1 document_types
shipment_files 1─∞ milestones (timeline)

LINE 2 — INSURANCE
insurers 1─∞ insurance_quotes 1─1 insurance_policies
clients 1─∞ insurance_policies ∞─1 insurance_classes
insurance_policies ∞─1 shipment_files (nullable — attached to clearing job)
insurance_policies ∞─1 transport_jobs (nullable — GIT attached to trip)
insurance_policies 1─∞ commissions ; 1─∞ insurance_claims

LINE 3 — TRANSPORT
clients 1─∞ transport_jobs 1─∞ trips ∞─1 trucks ∞─1 drivers/transporters
transport_jobs ∞─1 shipment_files (nullable — continuation of clearing)
transport_jobs ∞─1 routes ; trips 1─∞ trip_checkpoints (border crossings)
transport_jobs 1─∞ transit_documents

SHARED FINANCE (all lines)
invoices (morphs to: shipment_file | transport_job | insurance_policy)
invoices 1─∞ invoice_items ; invoices 1─∞ receipts
disbursements (morph to job) ; file_charges (morph to job)
users ∞─∞ roles ∞─∞ permissions (spatie) ; clients 1─∞ users (portal)
```
**Design decision:** invoices, charges, documents & disbursements use **polymorphic relations** (`jobable_type`, `jobable_id`) so one finance engine serves all three business lines.

### 6.2 Core Tables

**branches** — id, name (Dar HQ, Tunduma, Rusumo, Kabanga, Mutukula, Holili, Namanga, Sirari, Kasumulu, Horohoro, Mwanza Port, Kigoma Port, Tanga Port, Mtwara Port…), code, type ENUM(hq, port, border, icd, office), country_border(nullable — e.g. Zambia side), phone, is_active.

**users** — id, name, email(unique), phone, password, client_id(nullable FK), **branch_id FK**, is_active, last_login_at, timestamps, softDeletes.

**clients** — id, code(unique e.g. CL-0001), name, tin, vrn, type(company/individual), phone, email, address, credit_limit DECIMAL, payment_terms_days, is_active, created_by.

**shipment_files** — id, file_no(unique), client_id FK, **branch_id FK**, type ENUM(import, export, transit), **cargo_category ENUM(container, motor_vehicle, machinery, loose_cargo, bulk)**, **destination_country (for transit files — TZ, DRC, ZM, BI, RW, UG, MW)**, mode ENUM(sea, air, road), status ENUM(draft, open, declaration, port_clearance, transport, delivered, invoiced, closed, cancelled), bl_awb_no, vessel_flight, shipping_line_id, port_of_loading, port_of_discharge, terminal_id, eta DATE, discharge_date, free_days_expiry DATE, currency, exchange_rate, assigned_declaration_officer_id, assigned_clearing_officer_id, opened_by, opened_at, closed_at, remarks, softDeletes. *Indexes: file_no, bl_awb_no, client_id, status.*

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

### 6.2b LINE 3 — TRANSPORT TABLES

**transport_jobs** — id, job_no(unique e.g. CAF/TR/2026/0001), client_id FK, branch_id FK, shipment_file_id nullable FK (if continuation of a clearing job), route_id FK, cargo_description, is_cross_border BOOL, destination_country, status ENUM(quotation, confirmed, in_progress, at_border, delivered, invoiced, closed, cancelled), agreed_rate, currency, opened_by, remarks, softDeletes.

**routes** — id, name (Dar–Lubumbashi, Dar–Mwanza, Dar–Lusaka, Tunduma–Kolwezi…), origin, destination, via_borders (JSON e.g. ["Tunduma","Kasumbalesa"]), distance_km, est_days, standard_rate_per_ton/container.

**trips** — id, transport_job_id FK, shipment_file_id nullable FK, container_id nullable FK, truck_id, driver_id, origin, destination, dispatched_at, delivered_at, status ENUM(planned, loading, in_transit, at_border, cleared_border, delivered, closed), pod_document_id nullable, transport_cost (what CAF pays truck/subcontractor), notes.

**trip_checkpoints** — id, trip_id FK, location (Morogoro, Tunduma TZ side, Kasumbalesa DRC side…), branch_id nullable FK, checkpoint_type ENUM(departure, weighbridge, border_arrival, border_cleared, waypoint, arrival), reached_at, recorded_by, remarks. *(this powers live tracking + border dwell-time report)*

**transit_documents** — id, jobable morph (shipment_file or transport_job), doc_type ENUM(t1, transit_bond, rctg_carnet, road_manifest, c2_dr), reference_no, issued_at, expiry_date, bond_amount, bond_provider, acquitted BOOL, acquitted_at, exit_border_branch_id, status ENUM(open, acquitted, expired, claimed). ⚠ Un-acquitted bonds past expiry = dashboard red alert (real money risk).

### 6.2c LINE 2 — INSURANCE TABLES

**insurers** — id, name (partner underwriters), code, tin, contact_person, phone, email, default_commission_rate, is_active.

**insurance_classes** — id, name ENUM-like (marine_cargo, git, motor_comprehensive, motor_third_party, fire, other), default_rate_percent, min_premium, levies_percent (VAT/duty on premium where applicable).

**insurance_quotes** — id, quote_no, client_id FK, insurer_id FK, insurance_class_id FK, jobable morph nullable (shipment_file / transport_job), sum_insured, currency, rate_percent, premium_amount, valid_until, status ENUM(draft, sent, accepted, rejected, expired), created_by.

**insurance_policies** — id, policy_no(unique — insurer's number), internal_ref (CAF/INS/2026/0001), quote_id nullable FK, client_id FK, insurer_id FK, insurance_class_id FK, jobable morph nullable, sum_insured, premium_amount, commission_rate, commission_amount, start_date, end_date, status ENUM(pending_payment, active, expired, renewed, cancelled), premium_paid BOOL, premium_paid_at, remitted_to_insurer BOOL, remitted_at, issued_by, cover_note_document_id, softDeletes. *Indexes: policy_no, client_id+status, end_date (renewals query).*

**commissions** — id, insurance_policy_id FK, amount_earned, amount_received, received_at, insurer_statement_ref, status ENUM(pending, received, disputed). *(reconciles what insurers actually pay CAF)*

**insurance_claims** — id, insurance_policy_id FK, claim_no, incident_date, reported_at, description, claimed_amount, status ENUM(reported, docs_submitted, under_assessment, approved, paid, rejected), settled_amount, notes. *(CAF assists client to follow up — service value)*

### 6.2d SHARED / FINANCE TABLES

**charge_items** — id, name (Agency Fee, Transport, Documentation, TRA Duty, Port Charges, Shipping Line, ICD Storage, VAT…), category ENUM(income, disbursement), default_amount, taxable BOOL.

**file_charges** — id, **jobable_type + jobable_id (morph: shipment_file / transport_job / insurance_policy)**, charge_item_id FK, description, amount, currency, is_billable BOOL. *(costing sheet per job, any line)*

**proforma_invoices / invoices** — id, invoice_no(unique), **jobable_type + jobable_id (morph)**, client_id FK, **business_line ENUM(clearing, insurance, transport)**, date, due_date, currency, exchange_rate, subtotal, vat_amount, total, amount_paid, status ENUM(draft, sent, partially_paid, paid, overdue, cancelled), created_by.
**invoice_items** — id, invoice_id FK, charge_item_id, description, qty, unit_price, vat_rate, line_total.

**receipts** — id, receipt_no(unique), invoice_id nullable FK, client_id FK, date, amount, method ENUM(cash, bank, mobile_money, cheque), reference, received_by.

**disbursements** — id, **jobable morph**, payee ENUM/(agency_id FK / insurer_id FK), description, amount, currency, paid_at, method, reference_no, receipt_scan(document_id), approved_by, recorded_by. *(money paid OUT on behalf of client — TRA, port, line, transit bond, premium remitted to insurer)*

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

### 7.2b TRANSIT CLEARING FLOW (cargo passing through TZ → DRC/Zambia/etc.)
```
File opened (type=transit, destination_country=DRC)
→ Transit declaration lodged (T1) + TRANSIT BOND secured (transit_documents)
→ Cargo released from port under bond → Transport job created (often auto-linked)
→ Trip dispatched → checkpoints recorded → arrives exit border (e.g. Tunduma)
→ Tunduma Branch Manager records border_arrival → customs exit processing
→ border_cleared → cargo exits TZ → BOND ACQUITTAL recorded (critical!)
→ Delivery at destination (Kasumbalesa/Lubumbashi…) → POD → invoice → close
⚠ Un-acquitted bond = CAF liable for full duties. System alerts until acquitted.
```

### 7.2c INSURANCE WORKFLOW (Line 2)
```
1. LEAD  — Client requests cover (portal/walk-in) OR CrossSellService flags an
   uninsured clearing/transport job → Insurance Officer picks it up.
2. QUOTE — Officer selects class (Marine/GIT/Motor…), insurer, sum insured
   → PremiumService computes premium (rate % × sum insured, min premium, levies)
   → quote PDF sent to client (status: sent).
3. ACCEPT — Client accepts → policy created (status: pending_payment).
4. PREMIUM — Client pays premium → Receipt recorded → policy ACTIVE
   → cover note / policy schedule uploaded → client notified + portal download.
5. REMIT — Accountant remits premium (minus commission, per insurer agreement)
   → Disbursement to insurer, remitted_to_insurer = true.
6. COMMISSION — CommissionService logs commission earned; reconciled monthly
   against insurer statements (status: pending → received).
7. LIFECYCLE — RenewalService alerts at D-30/D-14/D-7 before expiry → renewal
   quote auto-drafted. If incident: claim opened, CAF assists follow-up to settlement.
```

### 7.2d TRANSPORT-ONLY WORKFLOW (Line 3 standalone)
```
Client already has cleared cargo (at port/ICD/warehouse) and needs movement:
1. Transport job opened (job_no CAF/TR/…, route selected e.g. Dar–Lubumbashi)
2. Quotation → client confirms rate → status confirmed
3. GIT insurance offered (cross-sell → Line 2 quote in one click)
4. Truck/driver assigned (own fleet) OR subcontracted transporter (their cost
   recorded as transport_cost — margin = client rate − transporter cost)
5. If cross-border: transit docs prepared (T1/RCTG/manifest/bond)
6. Trip dispatched → checkpoints: weighbridges, border arrival, border cleared
   → client sees live status in portal
7. Delivered → POD uploaded (signed) → empty return if container
8. Invoice → payment → job closed
```

### 7.3 Finance Flow (all lines feed one engine)
```
file_charges (planned costs, any job type)   disbursements (actual money out)
        └──────────────┬──────────────────────────┘
                 CostingService
                        │
   Invoice (income) – Disbursements (pass-through) = MARGIN per job
                        │
   Revenue Report SPLIT BY LINE:
   • Clearing  → agency fees + billable charges
   • Insurance → commission earned (premium is pass-through to insurer)
   • Transport → client rate − truck/subcontractor cost
Receipts → invoice.amount_paid → Debtors Aging (0-30/31-60/61-90/90+)
Client Statement = ALL invoices/receipts across all 3 lines, one ledger.
```

### 7.4 Status Machines (transitions enforced in Services — no stage jumping)
- **shipment_files:** `draft → open → declaration → port_clearance → transport → delivered → invoiced → closed` (+ `cancelled` pre-delivery, permission-guarded)
- **transport_jobs:** `quotation → confirmed → in_progress → at_border → delivered → invoiced → closed` (+ `cancelled`)
- **insurance_policies:** `pending_payment → active → expired | renewed | cancelled`
- **transit_documents (bonds):** `open → acquitted` (or `expired/claimed` = escalation)

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
| 25 | Branches / Stations | ✔ | ✔ | ✔ | soft | SA only; performance view |
| 26 | Transport Jobs | ✔ | ✔ | ✔ | soft | confirm, dispatch, link to clearing file, close |
| 27 | Routes | ✔ | ✔ | ✔ | soft | standard rates per corridor |
| 28 | Trip Checkpoints | ✔ | ✔ | ✔ | ✔(SA) | border arrival/cleared, live board |
| 29 | Transit Docs & Bonds | ✔ | ✔ | ✔ | soft | acquit bond, expiry alerts |
| 30 | Insurers | ✔ | ✔ | ✔ | soft | commission rate agreements |
| 31 | Insurance Classes | ✔ | ✔ | ✔ | soft | rates, min premium |
| 32 | Insurance Quotes | ✔ | ✔ | ✔ | soft | send PDF, accept → policy |
| 33 | Insurance Policies | ✔ | ✔ | ✔* | soft | issue, mark premium paid, renew, cancel (*locked after active except SA) |
| 34 | Commissions | auto | ✔ | ✔ | — | mark received, insurer reconciliation |
| 35 | Insurance Claims | ✔ | ✔ | ✔ | soft | status pipeline to settlement |
| 36 | Settings | — | ✔ | ✔ | — | SA only |
| 37 | Number Sequences | — | ✔ | ✔ | — | SA only |
| 38 | Reports | — | ✔ | — | — | filter, Excel/PDF export |
| 39 | Activity Log | — | ✔ | — | — | filter by user/model/date |

**Finance integrity rule:** invoices & receipts are never hard-edited after issue — corrections via credit note / void with reason, always logged.

---

## 9. API STRUCTURE (api/v1 — Sanctum)

```
POST   /api/v1/login
GET    /api/v1/track/{fileNo}                 (public, rate-limited)
GET    /api/v1/portal/shipments               (client token)
GET    /api/v1/portal/shipments/{id}          + /documents + /milestones
GET    /api/v1/portal/invoices  /statement
GET    /api/v1/portal/transport-jobs          + /{id}/checkpoints (live tracking)
GET    /api/v1/portal/policies                + /{id}/documents
POST   /api/v1/portal/service-requests        (request clearing/transport/insurance)
POST   /api/v1/driver/trips/{id}/status       (driver app)
POST   /api/v1/driver/trips/{id}/checkpoint   (border/waypoint reached + GPS)
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
| Trip dispatched / at border / cleared border | ✔ | ✔(Transport) | |
| POD delivered | ✔ | ✔ | ✔ (trigger invoice) |
| Transit bond nearing expiry / un-acquitted | | ✔ + Branch Mgr | ✔ |
| Insurance quote sent / policy active | ✔ | | ✔(Ins. Officer) |
| Policy expiring (D-30/14/7) — renewal | ✔ | ✔(Ins. Officer) | |
| Premium unpaid reminder | ✔ | ✔(Ins. Officer) | ✔ |
| Claim status changed | ✔ | ✔(Ins. Officer) | |
| Uninsured job opened (cross-sell lead) | | ✔(Ins. Officer) | |

---

## 11. REPORTS

1. **Operations:** files opened/closed per period, by type/mode/officer; average clearance days (open→gate_out); files stuck per stage.
2. **Demurrage/Storage exposure:** files past free days + estimated cost.
3. **Revenue:** invoiced vs collected, margin per file, revenue by client.
4. **Disbursement report:** per authority (TRA/Port/Lines) per period.
5. **Debtors aging** + client statements (batch email monthly).
6. **Officer performance:** files handled, avg days, queries per officer.
7. **Transport:** trips per truck, margin per route/corridor, POD compliance, border dwell time (avg hours stuck at Tunduma/Rusumo etc. — from trip_checkpoints).
8. **Insurance production:** premiums written & commissions earned per insurer, per class, per month; renewal retention rate; unpaid premiums; commission receivable from insurers.
9. **Transit/bond exposure:** open bonds by amount & age; acquittal turnaround.
10. **Branch/station performance:** jobs, revenue & margin per station.
11. **Cross-sell report:** % of clearing jobs with insurance attached, with transport attached — the upsell scoreboard.

All reports: date-range + filters (business line, branch, officer), export Excel & PDF, permission-guarded.

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
16. **Un-acquitted transit bonds** → the biggest hidden risk. Dashboard red list + escalating alerts; bond cannot be marked acquitted without exit-border confirmation (Branch Manager at exit station).
17. **Premium collected but not remitted to insurer** → regulatory risk (TIRA). Aging report of collected-not-remitted premiums; remittance recorded as disbursement with insurer statement ref.
18. **Commission disputes** → insurer statement reconciliation screen: expected vs received per policy; status `disputed` with notes.
19. **Policy backdating** → forbidden; `start_date >= premium_paid_at` enforced (no cover before payment — standard cash-and-carry rule).
20. **One transport job, many trips** (e.g. 10 containers Dar→Lubumbashi) → trips are children of the job; job is delivered only when all trips have POD.
21. **Subcontractor truck breakdown mid-route** → trip can be split/reassigned to a new truck; checkpoint history preserved; extra cost logged against the trip.
22. **Cross-border currency** → DRC/Zambia legs often quoted USD; job currency + exchange rate snapshot, same rule as invoices.
23. **Border offline connectivity** → checkpoint updates must work on mobile with poor network: lightweight pages, queued submission (retry), or SMS-to-system fallback (phase 2).
24. **Client with jobs in all 3 lines** → statement groups by line but totals one balance; portal overview shows unified picture.
25. **Insurance sold to non-clearing walk-in client** → policies work standalone; jobable morph nullable by design.

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
| 1 | Auth, roles, branches, clients, clearing files, documents, milestones, basic dashboard | 4–5 wks |
| 2 | Declarations, customs queries, HS codes, transit docs & bonds, notifications (SMS/Email) | 3–4 wks |
| 3 | Finance (polymorphic): charges, invoices, receipts, disbursements, debtors | 4 wks |
| 4 | Transport Jobs module: routes, trips, checkpoints, driver mobile view, POD | 3–4 wks |
| 5 | Insurance module: insurers, quotes, policies, premiums, commissions, renewals, claims | 3 wks |
| 6 | Client portal (all 3 lines) + public tracking + service requests | 2–3 wks |
| 7 | Reports (per line & branch), exports, activity log UI, backups, hardening | 2 wks |

---

## 15. SEEDERS TO SHIP WITH v1

- Roles + full permission set (Section 3), including Insurance Officer & Branch Manager.
- Branches/stations: Dar HQ, Tanga, Mtwara, Mwanza & Kigoma ports, Tunduma, Kasumulu, Rusumo, Kabanga, Mutukula, Holili, Namanga, Sirari, Horohoro.
- Document types (BL, AWB, Invoice, Packing List, TANSAD, DO, Gate Pass, T1, Transit Bond, RCTG Carnet, Road Manifest, Cover Note, Policy Schedule, POD…).
- Charge items (Agency Fee, Transport, TRA Duty, Port Charges, Wharfage, Shipping Line, ICD Storage, Documentation Fee, Bond Fee, Border Handling).
- Insurance classes (Marine Cargo, GIT, Motor Comprehensive, Motor Third Party, Other) with default rates & minimum premiums; sample insurers.
- Routes/corridors (Dar–Lubumbashi via Tunduma–Kasumbalesa, Dar–Lusaka, Dar–Bujumbura via Kobero, Dar–Kigali via Rusumo, Dar–Kampala via Mutukula, Dar–Mwanza, Dar–Mbeya…).
- Milestone stages, shipping lines (Maersk, MSC, CMA CGM, PIL…), terminals/ICDs.
- Demo Super Admin: `admin@caf.co.tz` (force password change on first login).

---

*End of document — CAF v2.0 System Guideline (Clearing & Forwarding • Insurance Agency • Transportation).*
