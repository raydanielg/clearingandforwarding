<?php

namespace App\Services;

use App\Models\NumberSequence;
use Illuminate\Support\Facades\DB;

class FileNumberService
{
    public static function generate(string $type): string
    {
        $type = strtolower($type);
        $key = "file_{$type}";
        $year = (int) now()->format('Y');
        $prefix = "CAF/" . strtoupper($type) . "/{$year}";

        return DB::transaction(function () use ($key, $year, $prefix) {
            $seq = NumberSequence::where('key', $key)->where('year', $year)->lockForUpdate()->first();

            if (!$seq) {
                $seq = NumberSequence::create([
                    'key' => $key,
                    'prefix' => $prefix,
                    'year' => $year,
                    'last_number' => 0,
                ]);
            }

            $seq->increment('last_number');
            $number = $seq->last_number;

            return $prefix . '/' . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }

    public static function generateInvoiceNumber(): string
    {
        $year = (int) now()->format('Y');
        $prefix = "INV/{$year}";

        return DB::transaction(function () use ($year, $prefix) {
            $seq = NumberSequence::where('key', 'invoice')->where('year', $year)->lockForUpdate()->first();

            if (!$seq) {
                $seq = NumberSequence::create([
                    'key' => 'invoice',
                    'prefix' => $prefix,
                    'year' => $year,
                    'last_number' => 0,
                ]);
            }

            $seq->increment('last_number');

            return $prefix . '/' . str_pad($seq->last_number, 4, '0', STR_PAD_LEFT);
        });
    }

    public static function generateReceiptNumber(): string
    {
        $year = (int) now()->format('Y');
        $prefix = "RCT/{$year}";

        return DB::transaction(function () use ($year, $prefix) {
            $seq = NumberSequence::where('key', 'receipt')->where('year', $year)->lockForUpdate()->first();

            if (!$seq) {
                $seq = NumberSequence::create([
                    'key' => 'receipt',
                    'prefix' => $prefix,
                    'year' => $year,
                    'last_number' => 0,
                ]);
            }

            $seq->increment('last_number');

            return $prefix . '/' . str_pad($seq->last_number, 4, '0', STR_PAD_LEFT);
        });
    }

    public static function generateClientCode(): string
    {
        return DB::transaction(function () {
            $year = (int) now()->format('Y');
            $seq = NumberSequence::where('key', 'client')->where('year', $year)->lockForUpdate()->first();

            if (!$seq) {
                $seq = NumberSequence::create([
                    'key' => 'client',
                    'prefix' => 'CL',
                    'year' => $year,
                    'last_number' => 0,
                ]);
            }

            $seq->increment('last_number');

            return 'CL-' . str_pad($seq->last_number, 4, '0', STR_PAD_LEFT);
        });
    }

    public static function generatePolicyNumber(): string
    {
        $year = (int) now()->format('Y');
        $prefix = "POL/{$year}";

        return DB::transaction(function () use ($year, $prefix) {
            $seq = NumberSequence::where('key', 'policy')->where('year', $year)->lockForUpdate()->first();

            if (!$seq) {
                $seq = NumberSequence::create([
                    'key' => 'policy',
                    'prefix' => $prefix,
                    'year' => $year,
                    'last_number' => 0,
                ]);
            }

            $seq->increment('last_number');

            return $prefix . '/' . str_pad($seq->last_number, 4, '0', STR_PAD_LEFT);
        });
    }

    public static function generateClaimNumber(): string
    {
        $year = (int) now()->format('Y');
        $prefix = "CLM/{$year}";

        return DB::transaction(function () use ($year, $prefix) {
            $seq = NumberSequence::where('key', 'claim')->where('year', $year)->lockForUpdate()->first();

            if (!$seq) {
                $seq = NumberSequence::create([
                    'key' => 'claim',
                    'prefix' => $prefix,
                    'year' => $year,
                    'last_number' => 0,
                ]);
            }

            $seq->increment('last_number');

            return $prefix . '/' . str_pad($seq->last_number, 4, '0', STR_PAD_LEFT);
        });
    }
}
