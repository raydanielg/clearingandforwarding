<?php

namespace App\Services;

use App\Models\ShipmentFile;
use App\Models\Milestone;
use Illuminate\Support\Facades\DB;

class MilestoneService
{
    private static array $stageOrder = [
        'file_opened', 'docs_complete', 'declaration_lodged', 'taxes_paid',
        'customs_released', 'do_collected', 'port_charges_paid', 'verification',
        'gate_out', 'insurance_arranged', 'in_transit', 'delivered', 'empty_returned', 'file_closed'
    ];

    private static array $statusMap = [
        'file_opened' => 'open',
        'docs_complete' => 'open',
        'declaration_lodged' => 'declaration',
        'taxes_paid' => 'declaration',
        'customs_released' => 'port_clearance',
        'do_collected' => 'port_clearance',
        'port_charges_paid' => 'port_clearance',
        'verification' => 'port_clearance',
        'gate_out' => 'port_clearance',
        'insurance_arranged' => 'port_clearance',
        'in_transit' => 'transport',
        'delivered' => 'delivered',
        'empty_returned' => 'delivered',
        'file_closed' => 'closed',
    ];

    public static function record(ShipmentFile $file, string $stage, int $userId, ?string $remarks = null): Milestone
    {
        return DB::transaction(function () use ($file, $stage, $userId, $remarks) {
            $milestone = Milestone::create([
                'shipment_file_id' => $file->id,
                'stage' => $stage,
                'happened_at' => now(),
                'recorded_by' => $userId,
                'remarks' => $remarks,
            ]);

            $newStatus = self::$statusMap[$stage] ?? null;
            if ($newStatus && $file->status !== 'closed' && $file->status !== 'cancelled') {
                $file->update(['status' => $newStatus]);
            }

            if ($stage === 'file_closed') {
                $file->update(['status' => 'closed', 'closed_at' => now()]);
            }

            return $milestone;
        });
    }

    public static function canTransitionTo(ShipmentFile $file, string $stage): bool
    {
        if ($file->status === 'closed' || $file->status === 'cancelled') {
            return false;
        }

        $lastMilestone = $file->milestones()->latest('happened_at')->first();
        if (!$lastMilestone) {
            return $stage === 'file_opened';
        }

        $currentIndex = array_search($lastMilestone->stage, self::$stageOrder);
        $targetIndex = array_search($stage, self::$stageOrder);

        return $targetIndex !== false && $targetIndex >= $currentIndex;
    }
}
