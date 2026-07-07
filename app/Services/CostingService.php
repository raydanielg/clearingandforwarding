<?php

namespace App\Services;

use App\Models\ShipmentFile;
use App\Models\FileCharge;
use App\Models\Disbursement;
use App\Models\Invoice;

class CostingService
{
    public static function getFileCosts(ShipmentFile $file): array
    {
        $charges = $file->charges()->with('chargeItem')->get();
        $disbursements = $file->disbursements;

        $incomeCharges = $charges->filter(fn($c) => $c->chargeItem && $c->chargeItem->category === 'income')->sum('amount');
        $disbursementCharges = $charges->filter(fn($c) => $c->chargeItem && $c->chargeItem->category === 'disbursement')->sum('amount');
        $actualDisbursements = $disbursements->sum('amount');

        $totalIncome = $incomeCharges;
        $totalDisbursements = $disbursementCharges + $actualDisbursements;
        $margin = $totalIncome - $totalDisbursements;

        return [
            'income' => $totalIncome,
            'disbursements' => $totalDisbursements,
            'margin' => $margin,
            'charges' => $charges,
            'disbursement_records' => $disbursements,
        ];
    }

    public static function getFileMargin(ShipmentFile $file): float
    {
        $costs = self::getFileCosts($file);
        return $costs['margin'];
    }

    public static function getInvoiceTotal(Invoice $invoice): array
    {
        $items = $invoice->items;
        $subtotal = $items->sum('line_total');
        $vat = $items->sum(function ($item) {
            return $item->line_total * ($item->vat_rate / 100);
        });

        return [
            'subtotal' => $subtotal,
            'vat' => $vat,
            'total' => $subtotal + $vat,
        ];
    }
}
