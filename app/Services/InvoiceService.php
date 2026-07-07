<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Receipt;
use App\Models\ShipmentFile;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public static function createFromCharges(ShipmentFile $file, array $data, int $userId, string $kind = 'invoice'): Invoice
    {
        return DB::transaction(function () use ($file, $data, $userId, $kind) {
            $invoiceNo = FileNumberService::generateInvoiceNumber();

            $invoice = Invoice::create([
                'invoice_no' => $invoiceNo,
                'shipment_file_id' => $file->id,
                'client_id' => $file->client_id,
                'date' => $data['date'] ?? now()->format('Y-m-d'),
                'due_date' => $data['due_date'] ?? null,
                'currency' => $data['currency'] ?? 'TZS',
                'exchange_rate' => $data['exchange_rate'] ?? 1,
                'subtotal' => 0,
                'vat_amount' => 0,
                'total' => 0,
                'amount_paid' => 0,
                'status' => 'draft',
                'kind' => $kind,
                'created_by' => $userId,
            ]);

            $subtotal = 0;
            $vatTotal = 0;

            if (isset($data['items'])) {
                foreach ($data['items'] as $item) {
                    $lineTotal = $item['qty'] * $item['unit_price'];
                    $vat = $lineTotal * ($item['vat_rate'] / 100);

                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'charge_item_id' => $item['charge_item_id'] ?? null,
                        'description' => $item['description'] ?? null,
                        'qty' => $item['qty'],
                        'unit_price' => $item['unit_price'],
                        'vat_rate' => $item['vat_rate'],
                        'line_total' => $lineTotal,
                    ]);

                    $subtotal += $lineTotal;
                    $vatTotal += $vat;
                }
            }

            $invoice->update([
                'subtotal' => $subtotal,
                'vat_amount' => $vatTotal,
                'total' => $subtotal + $vatTotal,
            ]);

            return $invoice;
        });
    }

    public static function recordPayment(Invoice $invoice, array $data, int $userId): Receipt
    {
        return DB::transaction(function () use ($invoice, $data, $userId) {
            $receiptNo = FileNumberService::generateReceiptNumber();

            $receipt = Receipt::create([
                'receipt_no' => $receiptNo,
                'invoice_id' => $invoice->id,
                'client_id' => $invoice->client_id,
                'date' => $data['date'] ?? now()->format('Y-m-d'),
                'amount' => $data['amount'],
                'method' => $data['method'] ?? 'bank',
                'reference' => $data['reference'] ?? null,
                'received_by' => $userId,
            ]);

            $newPaid = $invoice->amount_paid + $receipt->amount;

            if ($newPaid >= $invoice->total) {
                $invoice->update(['amount_paid' => $newPaid, 'status' => 'paid']);
            } else {
                $invoice->update(['amount_paid' => $newPaid, 'status' => 'partially_paid']);
            }

            return $receipt;
        });
    }

    public static function convertProformaToInvoice(Invoice $proforma): Invoice
    {
        if ($proforma->kind !== 'proforma') {
            return $proforma;
        }

        return DB::transaction(function () use ($proforma) {
            $newInvoiceNo = FileNumberService::generateInvoiceNumber();

            $invoice = $proforma->replicate();
            $invoice->invoice_no = $newInvoiceNo;
            $invoice->kind = 'invoice';
            $invoice->status = 'sent';
            $invoice->save();

            foreach ($proforma->items as $item) {
                $newItem = $item->replicate();
                $newItem->invoice_id = $invoice->id;
                $newItem->save();
            }

            return $invoice;
        });
    }
}
