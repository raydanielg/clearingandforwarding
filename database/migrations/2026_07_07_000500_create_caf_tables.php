<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Clients ──
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('tin')->nullable();
            $table->string('vrn')->nullable();
            $table->enum('type', ['company', 'individual'])->default('company');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->integer('payment_terms_days')->default(30);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['code', 'is_active']);
        });

        // ── Partners: Shipping Lines ──
        Schema::create('shipping_lines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Partners: Terminals / ICDs ──
        Schema::create('terminals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('type')->default('port');
            $table->string('location')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Partners: Government Agencies ──
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Shipment Files ──
        Schema::create('shipment_files', function (Blueprint $table) {
            $table->id();
            $table->string('file_no')->unique();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->enum('type', ['import', 'export', 'transit'])->default('import');
            $table->enum('mode', ['sea', 'air', 'road'])->default('sea');
            $table->enum('status', ['draft', 'open', 'declaration', 'port_clearance', 'transport', 'delivered', 'invoiced', 'closed', 'cancelled'])->default('draft');
            $table->string('bl_awb_no')->nullable();
            $table->string('vessel_flight')->nullable();
            $table->foreignId('shipping_line_id')->nullable()->constrained()->nullOnDelete();
            $table->string('port_of_loading')->nullable();
            $table->string('port_of_discharge')->nullable();
            $table->foreignId('terminal_id')->nullable()->constrained()->nullOnDelete();
            $table->date('eta')->nullable();
            $table->date('discharge_date')->nullable();
            $table->date('free_days_expiry')->nullable();
            $table->string('currency', 3)->default('TZS');
            $table->decimal('exchange_rate', 12, 4)->default(1);
            $table->foreignId('assigned_declaration_officer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_clearing_officer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('opened_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['file_no', 'status']);
            $table->index(['client_id', 'status']);
            $table->index('bl_awb_no');
        });

        // ── Consignments ──
        Schema::create('consignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_file_id')->constrained()->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->string('marks_numbers')->nullable();
            $table->integer('packages_count')->default(0);
            $table->string('package_type')->nullable();
            $table->decimal('gross_weight', 12, 2)->default(0);
            $table->decimal('volume_cbm', 12, 3)->default(0);
            $table->decimal('cargo_value', 15, 2)->default(0);
            $table->string('cargo_currency', 3)->default('USD');
            $table->timestamps();
        });

        // ── Containers ──
        Schema::create('containers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consignment_id')->constrained()->cascadeOnDelete();
            $table->string('container_no');
            $table->enum('size', ['20ft', '40ft', '40hc'])->default('20ft');
            $table->string('seal_no')->nullable();
            $table->enum('status', ['at_port', 'at_icd', 'released', 'gated_out', 'delivered', 'empty_returned'])->default('at_port');
            $table->date('empty_return_due')->nullable();
            $table->timestamps();
        });

        // ── HS Codes ──
        Schema::create('hs_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->text('description');
            $table->decimal('duty_rate', 5, 2)->default(0);
            $table->decimal('vat_rate', 5, 2)->default(18);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Declarations ──
        Schema::create('declarations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_file_id')->constrained()->cascadeOnDelete();
            $table->string('tansad_no')->nullable()->unique();
            $table->string('regime_code')->nullable();
            $table->timestamp('declared_at')->nullable();
            $table->timestamp('assessed_at')->nullable();
            $table->decimal('duty_amount', 15, 2)->default(0);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('other_taxes', 15, 2)->default(0);
            $table->decimal('total_taxes', 15, 2)->default(0);
            $table->enum('status', ['draft', 'lodged', 'queried', 'assessed', 'paid', 'released'])->default('draft');
            $table->foreignId('officer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Declaration Items ──
        Schema::create('declaration_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('declaration_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hs_code_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description')->nullable();
            $table->decimal('qty', 12, 2)->default(0);
            $table->decimal('customs_value', 15, 2)->default(0);
            $table->decimal('duty_rate', 5, 2)->default(0);
            $table->decimal('computed_duty', 15, 2)->default(0);
            $table->timestamps();
        });

        // ── Customs Queries ──
        Schema::create('customs_queries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('declaration_id')->constrained()->cascadeOnDelete();
            $table->timestamp('raised_at')->useCurrent();
            $table->text('query_text');
            $table->text('response_text')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->enum('status', ['open', 'responded', 'resolved'])->default('open');
            $table->timestamps();
        });

        // ── Document Types ──
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_mandatory_import')->default(false);
            $table->boolean('is_mandatory_export')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Documents ──
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_file_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_type_id')->constrained()->restrictOnDelete();
            $table->string('reference_no')->nullable();
            $table->string('file_path');
            $table->boolean('original_received')->default(false);
            $table->timestamp('received_at')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Milestones ──
        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_file_id')->constrained()->cascadeOnDelete();
            $table->enum('stage', [
                'file_opened', 'docs_complete', 'declaration_lodged', 'taxes_paid',
                'customs_released', 'do_collected', 'port_charges_paid', 'verification',
                'gate_out', 'insurance_arranged', 'in_transit', 'delivered', 'empty_returned', 'file_closed'
            ]);
            $table->timestamp('happened_at')->useCurrent();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->index(['shipment_file_id', 'stage']);
        });

        // ── Delivery Orders ──
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_file_id')->constrained()->cascadeOnDelete();
            $table->string('do_no');
            $table->foreignId('shipping_line_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('issued_at')->nullable();
            $table->date('expiry_date')->nullable();
            $table->decimal('deposit_amount', 15, 2)->default(0);
            $table->boolean('deposit_refunded')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Trucks ──
        Schema::create('trucks', function (Blueprint $table) {
            $table->id();
            $table->string('plate_no')->unique();
            $table->string('trailer_no')->nullable();
            $table->decimal('capacity_tons', 8, 2)->default(0);
            $table->foreignId('transporter_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Drivers ──
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('license_no')->nullable();
            $table->date('license_expiry')->nullable();
            $table->foreignId('truck_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Transporters ──
        Schema::create('transporters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tin')->nullable();
            $table->string('phone')->nullable();
            $table->string('contact_person')->nullable();
            $table->text('rate_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Trips ──
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_file_id')->constrained()->cascadeOnDelete();
            $table->foreignId('container_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('truck_id')->constrained()->restrictOnDelete();
            $table->foreignId('driver_id')->constrained()->restrictOnDelete();
            $table->string('origin')->nullable();
            $table->string('destination')->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->enum('status', ['planned', 'loading', 'in_transit', 'delivered', 'closed'])->default('planned');
            $table->foreignId('pod_document_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->decimal('transport_cost', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Charge Items ──
        Schema::create('charge_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['income', 'disbursement'])->default('income');
            $table->decimal('default_price', 15, 2)->default(0);
            $table->decimal('vat_rate', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // ── File Charges (Costing Sheet) ──
        Schema::create('file_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_file_id')->constrained()->cascadeOnDelete();
            $table->foreignId('charge_item_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('currency', 3)->default('TZS');
            $table->boolean('is_billable')->default(false);
            $table->timestamps();
        });

        // ── Invoices ──
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->foreignId('shipment_file_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->date('date');
            $table->date('due_date')->nullable();
            $table->string('currency', 3)->default('TZS');
            $table->decimal('exchange_rate', 12, 4)->default(1);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->enum('status', ['draft', 'sent', 'partially_paid', 'paid', 'overdue', 'cancelled'])->default('draft');
            $table->enum('kind', ['proforma', 'invoice'])->default('invoice');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['client_id', 'status']);
        });

        // ── Invoice Items ──
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('charge_item_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description')->nullable();
            $table->decimal('qty', 12, 2)->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('vat_rate', 5, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->timestamps();
        });

        // ── Receipts ──
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no')->unique();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->date('date');
            $table->decimal('amount', 15, 2)->default(0);
            $table->enum('method', ['cash', 'bank', 'mobile_money', 'cheque'])->default('bank');
            $table->string('reference')->nullable();
            $table->foreignId('received_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Disbursements ──
        Schema::create('disbursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_file_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agency_id')->nullable()->constrained()->nullOnDelete();
            $table->string('payee')->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('currency', 3)->default('TZS');
            $table->timestamp('paid_at')->nullable();
            $table->enum('method', ['cash', 'bank', 'mobile_money', 'cheque'])->default('bank');
            $table->string('reference_no')->nullable();
            $table->foreignId('receipt_scan_document_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Petty Cash ──
        Schema::create('petty_cash', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->enum('type', ['in', 'out'])->default('out');
            $table->foreignId('shipment_file_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Expenses ──
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('receipt_path')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Number Sequences ──
        Schema::create('number_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('prefix');
            $table->integer('year');
            $table->integer('last_number')->default(0);
            $table->timestamps();
            $table->unique(['key', 'year']);
        });

        // ── Settings ──
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // ── SMS Logs ──
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('to')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('pending');
            $table->string('cost')->nullable();
            $table->string('provider_ref')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('number_sequences');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('petty_cash');
        Schema::dropIfExists('disbursements');
        Schema::dropIfExists('receipts');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('file_charges');
        Schema::dropIfExists('charge_items');
        Schema::dropIfExists('trips');
        Schema::dropIfExists('transporters');
        Schema::dropIfExists('drivers');
        Schema::dropIfExists('trucks');
        Schema::dropIfExists('delivery_orders');
        Schema::dropIfExists('milestones');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('document_types');
        Schema::dropIfExists('customs_queries');
        Schema::dropIfExists('declaration_items');
        Schema::dropIfExists('declarations');
        Schema::dropIfExists('hs_codes');
        Schema::dropIfExists('containers');
        Schema::dropIfExists('consignments');
        Schema::dropIfExists('shipment_files');
        Schema::dropIfExists('agencies');
        Schema::dropIfExists('terminals');
        Schema::dropIfExists('shipping_lines');
        Schema::dropIfExists('clients');
    }
};
