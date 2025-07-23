<?php

namespace App\Filament\Resources\PaymentConfigResource\Pages;

use App\Filament\Resources\PaymentConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentConfigs extends ListRecords
{
    protected static string $resource = PaymentConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
