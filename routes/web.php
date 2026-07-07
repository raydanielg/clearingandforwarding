<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ShipmentFileController;
use App\Http\Controllers\Admin\DeclarationController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\TransportController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ReceiptController;
use App\Http\Controllers\Admin\DisbursementController;
use App\Http\Controllers\Admin\PettyCashController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\ChargeItemController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\HsCodeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\InsuranceController;
use App\Http\Controllers\PortalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->hasRole('Client')) {
            return redirect()->route('portal.dashboard');
        }
        return redirect()->route('admin.dashboard');
    }
    return view('welcome');
})->name('home');

// Public tracking (no auth required)
Route::post('/track', function (\Illuminate\Http\Request $request) {
    $request->validate(['query' => 'required|string']);
    $file = \App\Models\ShipmentFile::where('file_no', $request->query)
        ->orWhere('bl_awb_no', $request->query)
        ->with(['milestones.recordedBy', 'shippingLine', 'terminal', 'client'])
        ->first();

    if (!$file) {
        return response()->json(['success' => false, 'message' => 'No shipment found with that reference number.']);
    }

    return response()->json([
        'success' => true,
        'file_no' => $file->file_no,
        'status' => $file->status,
        'bl_awb' => $file->bl_awb_no,
        'origin' => $file->port_of_loading,
        'destination' => $file->port_of_discharge,
        'eta' => $file->eta?->format('M d, Y'),
        'milestones' => $file->milestones->map(fn($m) => [
            'stage' => str_replace('_', ' ', $m->stage),
            'date' => $m->happened_at->format('M d, Y H:i'),
            'remarks' => $m->remarks,
        ]),
    ]);
})->name('public.track');

Auth::routes();

/*------------------------------------------------------------------------------
 | Admin / Staff Panel Routes
 |-----------------------------------------------------------------------------
 */
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Clients
    Route::middleware('permission:clients.view')->group(function () {
        Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
        Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
        Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
        Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    });

    // Shipment Files
    Route::middleware('permission:files.view')->group(function () {
        Route::get('/files', [ShipmentFileController::class, 'index'])->name('files.index');
        Route::get('/files/create', [ShipmentFileController::class, 'create'])->name('files.create');
        Route::post('/files', [ShipmentFileController::class, 'store'])->name('files.store');
        Route::get('/files/{file}', [ShipmentFileController::class, 'show'])->name('files.show');
        Route::get('/files/{file}/edit', [ShipmentFileController::class, 'edit'])->name('files.edit');
        Route::put('/files/{file}', [ShipmentFileController::class, 'update'])->name('files.update');
        Route::delete('/files/{file}', [ShipmentFileController::class, 'destroy'])->name('files.destroy');
        Route::post('/files/{file}/assign', [ShipmentFileController::class, 'assignOfficers'])->name('files.assign');
        Route::post('/files/{file}/close', [ShipmentFileController::class, 'closeFile'])->name('files.close');
        Route::post('/files/{file}/milestone', [ShipmentFileController::class, 'addMilestone'])->name('files.milestone');
    });

    // Declarations
    Route::middleware('permission:declarations.view')->group(function () {
        Route::get('/declarations', [DeclarationController::class, 'index'])->name('declarations.index');
        Route::get('/declarations/create', [DeclarationController::class, 'create'])->name('declarations.create');
        Route::post('/declarations', [DeclarationController::class, 'store'])->name('declarations.store');
        Route::get('/declarations/{declaration}', [DeclarationController::class, 'show'])->name('declarations.show');
        Route::get('/declarations/{declaration}/edit', [DeclarationController::class, 'edit'])->name('declarations.edit');
        Route::put('/declarations/{declaration}', [DeclarationController::class, 'update'])->name('declarations.update');
        Route::delete('/declarations/{declaration}', [DeclarationController::class, 'destroy'])->name('declarations.destroy');
        Route::post('/declarations/{declaration}/items', [DeclarationController::class, 'addItem'])->name('declarations.items.store');
        Route::delete('/declarations/{declaration}/items/{item}', [DeclarationController::class, 'destroyItem'])->name('declarations.items.destroy');
        Route::post('/declarations/{declaration}/query', [DeclarationController::class, 'respondToQuery'])->name('declarations.query');
    });

    // HS Codes
    Route::middleware('permission:hscodes.view')->group(function () {
        Route::get('/hs-codes', [HsCodeController::class, 'index'])->name('hs-codes.index');
        Route::post('/hs-codes', [HsCodeController::class, 'store'])->name('hs-codes.store');
        Route::put('/hs-codes/{hsCode}', [HsCodeController::class, 'update'])->name('hs-codes.update');
        Route::delete('/hs-codes/{hsCode}', [HsCodeController::class, 'destroy'])->name('hs-codes.destroy');
    });

    // Documents
    Route::middleware('permission:documents.view')->group(function () {
        Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
        Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
        Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
        Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
        Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
        Route::post('/documents/{document}/verify', [DocumentController::class, 'verify'])->name('documents.verify');
        Route::post('/documents/{document}/original', [DocumentController::class, 'markOriginalReceived'])->name('documents.original');
        Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    });

    // Transport
    Route::middleware('permission:transport.view')->group(function () {
        // Trips
        Route::get('/transport/trips', [TransportController::class, 'tripsIndex'])->name('transport.trips.index');
        Route::get('/transport/trips/create', [TransportController::class, 'tripsCreate'])->name('transport.trips.create');
        Route::post('/transport/trips', [TransportController::class, 'tripsStore'])->name('transport.trips.store');
        Route::get('/transport/trips/{trip}', [TransportController::class, 'tripsShow'])->name('transport.trips.show');
        Route::post('/transport/trips/{trip}/status', [TransportController::class, 'tripsUpdateStatus'])->name('transport.trips.status');
        Route::delete('/transport/trips/{trip}', [TransportController::class, 'tripsDestroy'])->name('transport.trips.destroy');
        // Trucks
        Route::get('/transport/trucks', [TransportController::class, 'trucksIndex'])->name('transport.trucks.index');
        Route::post('/transport/trucks', [TransportController::class, 'trucksStore'])->name('transport.trucks.store');
        Route::put('/transport/trucks/{truck}', [TransportController::class, 'trucksUpdate'])->name('transport.trucks.update');
        Route::delete('/transport/trucks/{truck}', [TransportController::class, 'trucksDestroy'])->name('transport.trucks.destroy');
        // Drivers
        Route::get('/transport/drivers', [TransportController::class, 'driversIndex'])->name('transport.drivers.index');
        Route::post('/transport/drivers', [TransportController::class, 'driversStore'])->name('transport.drivers.store');
        Route::put('/transport/drivers/{driver}', [TransportController::class, 'driversUpdate'])->name('transport.drivers.update');
        Route::delete('/transport/drivers/{driver}', [TransportController::class, 'driversDestroy'])->name('transport.drivers.destroy');
        // Transporters
        Route::get('/transport/transporters', [TransportController::class, 'transportersIndex'])->name('transport.transporters.index');
        Route::post('/transport/transporters', [TransportController::class, 'transportersStore'])->name('transport.transporters.store');
        Route::put('/transport/transporters/{transporter}', [TransportController::class, 'transportersUpdate'])->name('transport.transporters.update');
        Route::delete('/transport/transporters/{transporter}', [TransportController::class, 'transportersDestroy'])->name('transport.transporters.destroy');
    });

    // Finance - Invoices
    Route::middleware('permission:invoices.view')->group(function () {
        Route::get('/finance/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/finance/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/finance/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/finance/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/finance/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('/finance/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::delete('/finance/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
        Route::post('/finance/invoices/{invoice}/payment', [InvoiceController::class, 'recordPayment'])->name('invoices.payment');
        Route::post('/finance/invoices/{invoice}/convert', [InvoiceController::class, 'convertProforma'])->name('invoices.convert');
    });

    // Finance - Receipts
    Route::middleware('permission:receipts.view')->group(function () {
        Route::get('/finance/receipts', [ReceiptController::class, 'index'])->name('receipts.index');
        Route::get('/finance/receipts/create', [ReceiptController::class, 'create'])->name('receipts.create');
        Route::post('/finance/receipts', [ReceiptController::class, 'store'])->name('receipts.store');
        Route::get('/finance/receipts/{receipt}', [ReceiptController::class, 'show'])->name('receipts.show');
        Route::delete('/finance/receipts/{receipt}', [ReceiptController::class, 'destroy'])->name('receipts.destroy');
    });

    // Finance - Disbursements
    Route::middleware('permission:disbursements.view')->group(function () {
        Route::get('/finance/disbursements', [DisbursementController::class, 'index'])->name('disbursements.index');
        Route::get('/finance/disbursements/create', [DisbursementController::class, 'create'])->name('disbursements.create');
        Route::post('/finance/disbursements', [DisbursementController::class, 'store'])->name('disbursements.store');
        Route::get('/finance/disbursements/{disbursement}', [DisbursementController::class, 'show'])->name('disbursements.show');
        Route::get('/finance/disbursements/{disbursement}/edit', [DisbursementController::class, 'edit'])->name('disbursements.edit');
        Route::put('/finance/disbursements/{disbursement}', [DisbursementController::class, 'update'])->name('disbursements.update');
        Route::post('/finance/disbursements/{disbursement}/approve', [DisbursementController::class, 'approve'])->name('disbursements.approve');
        Route::delete('/finance/disbursements/{disbursement}', [DisbursementController::class, 'destroy'])->name('disbursements.destroy');
    });

    // Finance - Petty Cash
    Route::middleware('permission:pettycash.view')->group(function () {
        Route::get('/finance/petty-cash', [PettyCashController::class, 'index'])->name('petty-cash.index');
        Route::post('/finance/petty-cash', [PettyCashController::class, 'store'])->name('petty-cash.store');
        Route::delete('/finance/petty-cash/{pettyCash}', [PettyCashController::class, 'destroy'])->name('petty-cash.destroy');
    });

    // Finance - Expenses
    Route::middleware('permission:expenses.view')->group(function () {
        Route::get('/finance/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::post('/finance/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::delete('/finance/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    });

    // Finance - Charge Items
    Route::middleware('permission:charges.view')->group(function () {
        Route::get('/finance/charges', [ChargeItemController::class, 'index'])->name('charges.index');
        Route::post('/finance/charges', [ChargeItemController::class, 'store'])->name('charges.store');
        Route::put('/finance/charges/{charge}', [ChargeItemController::class, 'update'])->name('charges.update');
        Route::delete('/finance/charges/{charge}', [ChargeItemController::class, 'destroy'])->name('charges.destroy');
    });

    // Partners
    Route::middleware('permission:partners.view')->group(function () {
        Route::get('/partners/lines', [PartnerController::class, 'linesIndex'])->name('partners.lines.index');
        Route::post('/partners/lines', [PartnerController::class, 'linesStore'])->name('partners.lines.store');
        Route::put('/partners/lines/{line}', [PartnerController::class, 'linesUpdate'])->name('partners.lines.update');
        Route::delete('/partners/lines/{line}', [PartnerController::class, 'linesDestroy'])->name('partners.lines.destroy');

        Route::get('/partners/terminals', [PartnerController::class, 'terminalsIndex'])->name('partners.terminals.index');
        Route::post('/partners/terminals', [PartnerController::class, 'terminalsStore'])->name('partners.terminals.store');
        Route::put('/partners/terminals/{terminal}', [PartnerController::class, 'terminalsUpdate'])->name('partners.terminals.update');
        Route::delete('/partners/terminals/{terminal}', [PartnerController::class, 'terminalsDestroy'])->name('partners.terminals.destroy');

        Route::get('/partners/agencies', [PartnerController::class, 'agenciesIndex'])->name('partners.agencies.index');
        Route::post('/partners/agencies', [PartnerController::class, 'agenciesStore'])->name('partners.agencies.store');
        Route::put('/partners/agencies/{agency}', [PartnerController::class, 'agenciesUpdate'])->name('partners.agencies.update');
        Route::delete('/partners/agencies/{agency}', [PartnerController::class, 'agenciesDestroy'])->name('partners.agencies.destroy');
    });

    // Insurance
    Route::middleware('permission:insurance.view')->group(function () {
        // Insurers
        Route::get('/insurance/insurers', [InsuranceController::class, 'insurersIndex'])->name('insurance.insurers.index');
        Route::post('/insurance/insurers', [InsuranceController::class, 'insurersStore'])->name('insurance.insurers.store');
        Route::put('/insurance/insurers/{insurer}', [InsuranceController::class, 'insurersUpdate'])->name('insurance.insurers.update');
        Route::delete('/insurance/insurers/{insurer}', [InsuranceController::class, 'insurersDestroy'])->name('insurance.insurers.destroy');

        // Policies
        Route::get('/insurance/policies', [InsuranceController::class, 'policiesIndex'])->name('insurance.policies.index');
        Route::get('/insurance/policies/create', [InsuranceController::class, 'policiesCreate'])->name('insurance.policies.create');
        Route::post('/insurance/policies', [InsuranceController::class, 'policiesStore'])->name('insurance.policies.store');
        Route::get('/insurance/policies/{policy}', [InsuranceController::class, 'policiesShow'])->name('insurance.policies.show');
        Route::get('/insurance/policies/{policy}/edit', [InsuranceController::class, 'policiesEdit'])->name('insurance.policies.edit');
        Route::put('/insurance/policies/{policy}', [InsuranceController::class, 'policiesUpdate'])->name('insurance.policies.update');
        Route::delete('/insurance/policies/{policy}', [InsuranceController::class, 'policiesDestroy'])->name('insurance.policies.destroy');

        // Claims
        Route::get('/insurance/claims', [InsuranceController::class, 'claimsIndex'])->name('insurance.claims.index');
        Route::get('/insurance/claims/create', [InsuranceController::class, 'claimsCreate'])->name('insurance.claims.create');
        Route::post('/insurance/claims', [InsuranceController::class, 'claimsStore'])->name('insurance.claims.store');
        Route::get('/insurance/claims/{claim}', [InsuranceController::class, 'claimsShow'])->name('insurance.claims.show');
        Route::put('/insurance/claims/{claim}', [InsuranceController::class, 'claimsUpdate'])->name('insurance.claims.update');
        Route::delete('/insurance/claims/{claim}', [InsuranceController::class, 'claimsDestroy'])->name('insurance.claims.destroy');
    });

    // Reports
    Route::middleware('permission:reports.view')->group(function () {
        Route::get('/reports/operations', [ReportController::class, 'operations'])->name('reports.operations');
        Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
        Route::get('/reports/disbursements', [ReportController::class, 'disbursements'])->name('reports.disbursements');
        Route::get('/reports/debtors', [ReportController::class, 'debtors'])->name('reports.debtors');
        Route::get('/reports/officer-performance', [ReportController::class, 'officerPerformance'])->name('reports.officer-performance');
        Route::get('/reports/insurance', [ReportController::class, 'insurance'])->name('reports.insurance');
    });

    // Settings / Administration
    Route::middleware('permission:users.manage')->group(function () {
        Route::get('/settings/users', [SettingsController::class, 'usersIndex'])->name('settings.users.index');
        Route::post('/settings/users', [SettingsController::class, 'usersStore'])->name('settings.users.store');
        Route::put('/settings/users/{user}', [SettingsController::class, 'usersUpdate'])->name('settings.users.update');
        Route::delete('/settings/users/{user}', [SettingsController::class, 'usersDestroy'])->name('settings.users.destroy');
    });

    Route::middleware('permission:roles.manage')->group(function () {
        Route::get('/settings/roles', [SettingsController::class, 'rolesIndex'])->name('settings.roles.index');
        Route::post('/settings/roles', [SettingsController::class, 'rolesStore'])->name('settings.roles.store');
        Route::put('/settings/roles/{role}', [SettingsController::class, 'rolesUpdate'])->name('settings.roles.update');
        Route::delete('/settings/roles/{role}', [SettingsController::class, 'rolesDestroy'])->name('settings.roles.destroy');
    });

    Route::middleware('permission:settings.manage')->group(function () {
        Route::get('/settings/company', [SettingsController::class, 'companyIndex'])->name('settings.company');
        Route::put('/settings/company', [SettingsController::class, 'companyUpdate'])->name('settings.company.update');
    });
});

/*------------------------------------------------------------------------------
 | Client Portal Routes
 |-----------------------------------------------------------------------------
 */
Route::middleware(['auth'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/dashboard', [PortalController::class, 'dashboard'])->name('dashboard');

    // Shipments
    Route::get('/shipments', [PortalController::class, 'shipmentsIndex'])->name('shipments.index');
    Route::get('/shipments/{file}', [PortalController::class, 'shipmentsShow'])->name('shipments.show');

    // Documents
    Route::get('/documents', [PortalController::class, 'documentsIndex'])->name('documents.index');

    // Invoices
    Route::get('/invoices', [PortalController::class, 'invoicesIndex'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [PortalController::class, 'invoicesShow'])->name('invoices.show');

    // Insurance
    Route::get('/insurance', [PortalController::class, 'insuranceIndex'])->name('insurance.index');
    Route::get('/insurance/{policy}', [PortalController::class, 'insuranceShow'])->name('insurance.show');

    // Statement
    Route::get('/statement', [PortalController::class, 'statement'])->name('statement');

    // Track
    Route::get('/track', [PortalController::class, 'track'])->name('track');
    Route::post('/track', [PortalController::class, 'trackSearch'])->name('track.search');

    // Profile
    Route::get('/profile', [PortalController::class, 'profile'])->name('profile');

    // Support
    Route::get('/support', [PortalController::class, 'support'])->name('support');
});

// Legacy redirect: /home -> dashboard based on role
Route::get('/home', function () {
    if (auth()->check() && auth()->user()->hasRole('Client')) {
        return redirect()->route('portal.dashboard');
    }
    return redirect()->route('admin.dashboard');
})->name('home');
