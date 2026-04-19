<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\CltLayup;
use App\Models\CltLayer;
use Exception;

class ImportService
{
    public function import($supplierId, array $data, $strategy = 'overwrite')
    {
        $supplier = Supplier::findOrFail($supplierId);

        foreach ($data['layups'] as $layupData) {

            $layup = CltLayup::firstOrCreate([
                'supplier_id' => $supplier->id,
                'name' => $layupData['name']
            ]);

            foreach ($layupData['layers'] as $layerData) {

                $existingLayer = CltLayer::where('layup_id', $layup->id)
                    ->where('layer_order', $layerData['layer_order'])
                    ->first();

                if ($existingLayer) {

                    if ($this->isConflict($existingLayer, $layerData)) {
                        $this->resolveConflict($existingLayer, $layerData, $strategy);
                    }

                } else {
                    CltLayer::create([
                        'layup_id' => $layup->id,
                        ...$layerData
                    ]);
                }
            }
        }
    }

    private function isConflict($existing, $incoming)
    {
        return
            $existing->thickness != $incoming['thickness'] ||
            $existing->width != $incoming['width'] ||
            $existing->angle != $incoming['angle'];
    }

    private function resolveConflict($existing, $incoming, $strategy)
    {
        switch ($strategy) {
            case 'overwrite':
                $existing->update($incoming);
                break;

            case 'skip':
                // do nothing
                break;

            case 'reject':
                throw new Exception('Conflict detected');

            default:
                $existing->update($incoming);
        }
    }
}