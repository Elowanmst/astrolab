<?php

namespace App\Filament\Resources\PaymentConfigResource\Pages;

use App\Filament\Resources\PaymentConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentConfig extends EditRecord
{
    protected static string $resource = PaymentConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
