<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Configuracion extends Model
{
    protected $table = 'configuracion';

    protected $fillable = [
        'logo_path',
        'logo_quitar_fondo',
    ];

    protected $casts = [
        'logo_quitar_fondo' => 'boolean',
    ];

    public static function actual(): self
    {
        return Cache::rememberForever('configuracion', fn () => static::query()->firstOrCreate(
            ['id' => 1],
            ['logo_quitar_fondo' => false]
        ));
    }

    public static function olvidarCache(): void
    {
        Cache::forget('configuracion');
    }
}
