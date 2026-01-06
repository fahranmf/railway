<?php

namespace App\Filament\Resources\Purchases\Pages; // <--- Perhatikan Namespace ini

use App\Filament\Resources\Purchases\PurchaseResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchase extends CreateRecord
{
    protected static string $resource = PurchaseResource::class;
}
