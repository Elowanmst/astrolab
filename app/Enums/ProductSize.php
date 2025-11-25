<?php

namespace App\Enums;

enum ProductSize: string
{
    case XXS = 'XXS';
    case XS = 'XS';
    case S = 'S';
    case M = 'M';
    case L = 'L';
    case XL = 'XL';
    case XXL = 'XXL';

    public function getLabel(): string
    {
        return match($this) {
            self::XXS => 'XXS (Extra Extra Small)',
            self::XS => 'XS (Extra Small)',
            self::S => 'S (Small)',
            self::M => 'M (Medium)',
            self::L => 'L (Large)',
            self::XL => 'XL (Extra Large)',
            self::XXL => 'XXL (Extra Extra Large)',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())->mapWithKeys(fn($size) => [
            $size->value => $size->getLabel()
        ])->toArray();
    }
}
