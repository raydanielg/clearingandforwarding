<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Insurers (partner insurance companies) ──
        Schema::create('insurers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable()->unique();
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('license_no')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Insurance Policies ──
        Schema::create('insurance_policies', function (Blueprint $table) {
            $table->id();
            $table->string('policy_no')->unique();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->foreignId('shipment_file_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('insurer_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['marine_cargo', 'goods_in_transit', 'warehouse', 'other'])->default('marine_cargo');
            $table->enum('status', ['draft', 'quoted', 'issued', 'active', 'expired', 'cancelled', 'claimed'])->default('draft');
            $table->date('cover_start_date')->nullable();
            $table->date('cover_end_date')->nullable();
            $table->string('origin')->nullable();
            $table->string('destination')->nullable();
            $table->string('cargo_description')->nullable();
            $table->decimal('cargo_value', 15, 2)->default(0);
            $table->string('cargo_currency', 3)->default('USD');
            $table->decimal('sum_insured', 15, 2)->default(0);
            $table->decimal('premium_amount', 15, 2)->default(0);
            $table->string('premium_currency', 3)->default('TZS');
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->decimal('commission_amount', 15, 2)->default(0);
            $table->text('terms_conditions')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('issued_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['client_id', 'status']);
            $table->index('policy_no');
        });

        // ── Insurance Claims ──
        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->id();
            $table->string('claim_no')->unique();
            $table->foreignId('insurance_policy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->foreignId('shipment_file_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'settled'])->default('draft');
            $table->date('incident_date')->nullable();
            $table->text('incident_description')->nullable();
            $textColumn = $table->text('loss_location')->nullable();
            $table->decimal('claimed_amount', 15, 2)->default(0);
            $table->decimal('approved_amount', 15, 2)->default(0);
            $table->decimal('settled_amount', 15, 2)->default(0);
            $table->date('submitted_at')->nullable();
            $table->date('settled_at')->nullable();
            $table->text('insurer_response')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['insurance_policy_id', 'status']);
            $table->index('claim_no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_claims');
        Schema::dropIfExists('insurance_policies');
        Schema::dropIfExists('insurers');
    }
};
