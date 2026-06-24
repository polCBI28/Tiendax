<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'categoria_id', 'subcategoria_id', 'nombre', 'sku',
        'descripcion', 'precio_venta', 'precio_costo',
        'stock', 'stock_minimo', 'imagen', 'estado', 'activo'
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function subcategoria()
    {
        return $this->belongsTo(Subcategoria::class);
    }

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }
}