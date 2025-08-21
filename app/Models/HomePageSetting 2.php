<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePageSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }
        
        $value = $setting->value;
        
        // S'assurer que la valeur retournée n'est pas un array serialized
        if (is_string($value) && (str_starts_with($value, 'a:') || str_starts_with($value, 'O:'))) {
            $unserialized = @unserialize($value);
            if ($unserialized !== false) {
                if (is_array($unserialized)) {
                    return !empty($unserialized) ? (string)$unserialized[0] : $default;
                }
                return (string)$unserialized;
            }
        }
        
        return $value;
    }

    public static function set(string $key, $value): void
    {
        // S'assurer que la valeur est une chaîne ou null
        if (is_array($value)) {
            $value = !empty($value) ? $value[0] : null;
        }
        
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
