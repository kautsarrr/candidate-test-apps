<?php

namespace App\Services;

use App\Models\Supplier;

class ImportService
{
    public function import(array $data)
    {
        foreach ($data as $supplierData) {

            $supplier = Supplier::updateOrCreate(
                ['name' => $supplierData['name']],
                ['name' => $supplierData['name']]
            );

            foreach ($supplierData['layups'] ?? [] as $layupData) {

                $layup = $supplier->layups()->updateOrCreate(
                    ['name' => $layupData['name']],
                    ['name' => $layupData['name']]
                );

                    $layup->layers()->updateOrCreate(
                        ['layer_order' => $layerData['layer_order']],
                        [
                            'thickness' => $layerData['thickness'] ?? 0,
                            'width' => $layerData['width'] ?? 0,
                            'angle' => $layerData['angle'] ?? 0,
                        ]
                    );
                }
            }
        }
    }
}