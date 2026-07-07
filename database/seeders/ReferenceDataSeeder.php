<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentType;
use App\Models\ChargeItem;
use App\Models\ShippingLine;
use App\Models\Terminal;
use App\Models\Agency;
use App\Models\HsCode;
use App\Models\Insurer;

class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Document Types ──
        $docTypes = [
            ['name' => 'Bill of Lading', 'is_mandatory_import' => true, 'is_mandatory_export' => false],
            ['name' => 'Airway Bill', 'is_mandatory_import' => true, 'is_mandatory_export' => true],
            ['name' => 'Commercial Invoice', 'is_mandatory_import' => true, 'is_mandatory_export' => true],
            ['name' => 'Packing List', 'is_mandatory_import' => true, 'is_mandatory_export' => true],
            ['name' => 'TANSAD', 'is_mandatory_import' => true, 'is_mandatory_export' => true],
            ['name' => 'Delivery Order', 'is_mandatory_import' => true, 'is_mandatory_export' => false],
            ['name' => 'Gate Pass', 'is_mandatory_import' => true, 'is_mandatory_export' => false],
            ['name' => 'Release Order', 'is_mandatory_import' => true, 'is_mandatory_export' => false],
            ['name' => 'Exemption', 'is_mandatory_import' => false, 'is_mandatory_export' => false],
            ['name' => 'Permit', 'is_mandatory_import' => false, 'is_mandatory_export' => false],
            ['name' => 'Proof of Delivery', 'is_mandatory_import' => true, 'is_mandatory_export' => true],
            ['name' => 'Interchange', 'is_mandatory_import' => false, 'is_mandatory_export' => false],
        ];
        foreach ($docTypes as $dt) {
            DocumentType::firstOrCreate(['name' => $dt['name']], $dt);
        }

        // ── Charge Items ──
        $charges = [
            ['name' => 'Agency Fee', 'category' => 'income', 'default_price' => 150000, 'vat_rate' => 18, 'is_active' => true],
            ['name' => 'Transport', 'category' => 'disbursement', 'default_price' => 800000, 'vat_rate' => 0, 'is_active' => true],
            ['name' => 'Documentation Fee', 'category' => 'income', 'default_price' => 50000, 'vat_rate' => 18, 'is_active' => true],
            ['name' => 'TRA Duty', 'category' => 'disbursement', 'default_price' => 0, 'vat_rate' => 0, 'is_active' => true],
            ['name' => 'Port Charges', 'category' => 'disbursement', 'default_price' => 0, 'vat_rate' => 0, 'is_active' => true],
            ['name' => 'Wharfage', 'category' => 'disbursement', 'default_price' => 0, 'vat_rate' => 0, 'is_active' => true],
            ['name' => 'Shipping Line', 'category' => 'disbursement', 'default_price' => 0, 'vat_rate' => 0, 'is_active' => true],
            ['name' => 'ICD Storage', 'category' => 'disbursement', 'default_price' => 0, 'vat_rate' => 0, 'is_active' => true],
            ['name' => 'VAT', 'category' => 'disbursement', 'default_price' => 0, 'vat_rate' => 0, 'is_active' => true],
            ['name' => 'Insurance Premium', 'category' => 'disbursement', 'default_price' => 0, 'vat_rate' => 0, 'is_active' => true],
            ['name' => 'Insurance Commission', 'category' => 'income', 'default_price' => 0, 'vat_rate' => 18, 'is_active' => true],
        ];
        foreach ($charges as $c) {
            ChargeItem::firstOrCreate(['name' => $c['name']], $c);
        }

        // ── Shipping Lines ──
        $lines = ['Maersk', 'MSC', 'CMA CGM', 'PIL', 'Evergreen', 'OOCL'];
        foreach ($lines as $l) {
            ShippingLine::firstOrCreate(['name' => $l]);
        }

        // ── Terminals / ICDs ──
        $terminals = [
            ['name' => 'Dar es Salaam Port', 'type' => 'port'],
            ['name' => 'ICD Mikumi', 'type' => 'icd'],
            ['name' => 'ICD Isaka', 'type' => 'icd'],
            ['name' => 'ICD Arusha', 'type' => 'icd'],
        ];
        foreach ($terminals as $t) {
            Terminal::firstOrCreate(['name' => $t['name']], $t);
        }

        // ── Government Agencies ──
        $agencies = ['TRA', 'TBS', 'TFDA', 'Sumatra', 'Tanzania Ports Authority'];
        foreach ($agencies as $a) {
            Agency::firstOrCreate(['name' => $a]);
        }

        // ── Sample HS Codes ──
        $hsCodes = [
            ['code' => '0102.21.00', 'description' => 'Cattle for breeding', 'duty_rate' => 0, 'vat_rate' => 18],
            ['code' => '1006.30.00', 'description' => 'Rice, semi-milled or wholly milled', 'duty_rate' => 10, 'vat_rate' => 18],
            ['code' => '2710.12.11', 'description' => 'Motor spirit (petrol)', 'duty_rate' => 10, 'vat_rate' => 18],
            ['code' => '3004.90.00', 'description' => 'Other pharmaceutical goods', 'duty_rate' => 0, 'vat_rate' => 18],
            ['code' => '7308.30.00', 'description' => 'Doors, windows and frames, iron or steel', 'duty_rate' => 25, 'vat_rate' => 18],
            ['code' => '8703.23.00', 'description' => 'Motor cars, spark ignition 1500cc-3000cc', 'duty_rate' => 25, 'vat_rate' => 18],
            ['code' => '8517.12.00', 'description' => 'Smartphones / mobile phones', 'duty_rate' => 10, 'vat_rate' => 18],
            ['code' => '6109.10.00', 'description' => 'T-shirts, cotton, knitted', 'duty_rate' => 20, 'vat_rate' => 18],
        ];
        foreach ($hsCodes as $h) {
            HsCode::firstOrCreate(['code' => $h['code']], $h);
        }

        // ── Settings ──
        $settings = [
            ['key' => 'company_name', 'value' => 'CAF Clearing and Forwarding'],
            ['key' => 'company_tin', 'value' => '000-000-000'],
            ['key' => 'vat_rate', 'value' => '18'],
            ['key' => 'currency', 'value' => 'TZS'],
            ['key' => 'petty_cash_limit', 'value' => '500000'],
            ['key' => 'free_days_default', 'value' => '7'],
            ['key' => 'sms_sender_id', 'value' => 'CAF'],
            ['key' => 'insurance_commission_default', 'value' => '15'],
        ];

        // ── Insurers ──
        $insurers = [
            ['name' => 'Jubilee Insurance', 'code' => 'JUB', 'phone' => '+255 22 213 7300', 'email' => 'info@jubileetanzania.com'],
            ['name' => 'NIC Insurance', 'code' => 'NIC', 'phone' => '+255 22 211 3100', 'email' => 'info@nicinsurance.co.tz'],
            ['name' => 'AAR Insurance', 'code' => 'AAR', 'phone' => '+255 22 270 1700', 'email' => 'info@aar.co.tz'],
            ['name' => 'Resolution Insurance', 'code' => 'RES', 'phone' => '+255 22 296 4400', 'email' => 'info@resolution.co.tz'],
            ['name' => 'Heritage Insurance', 'code' => 'HER', 'phone' => '+255 22 211 2777', 'email' => 'info@heritage.co.tz'],
        ];
        foreach ($insurers as $ins) {
            Insurer::firstOrCreate(['name' => $ins['name']], $ins);
        }

        foreach ($settings as $s) {
            \App\Models\Setting::firstOrCreate(['key' => $s['key']], ['value' => $s['value']]);
        }
    }
}
