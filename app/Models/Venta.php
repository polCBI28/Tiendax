<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id', 'user_id', 'numero_boleta',
        'fecha_venta', 'descripcion', 'total', 'adelanto',
        'descuento_tipo', 'descuento_valor',
        'recargo_tipo', 'recargo_valor', 'estado'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }
}