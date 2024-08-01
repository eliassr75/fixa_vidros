<?php

namespace App\Models;

use Exception;
use App\Validators\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Controllers\CookieController;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

class Orders extends Model {
    protected $table = 'orders';
    protected $columns = [
        'id',
        'name',
        'status',
        'url',
        'client_id',
        'user_id',
        'total_price',
        'obs_client',
        'date_delivery',
        'updated_at',
        'created_at'
    ];

    public function status()
    {

    }

    public function items()
    {
        return $this->hasMany(OrdersItems::class, 'order_id', 'id');
    }

    /**
     * @throws Exception
     */
    public function validate(): bool
    {
        return true;
    }

}

class OrdersItems extends Model
{
    protected $table = 'order_items';
    protected $columns = [
        'id',
        'order_id',
        'category_id',
        'sub_category_id',
        'product_id',
        'glass_thickness_id',
        'glass_color_id',
        'glass_finish_id',
        'glass_clearances_id',
        'quantity',
        'width',
        'height',
        'obs_factory',
        'obs_client',
        'obs_tempera',
        'price',
        'updated_at',
        'created_at'
    ];

    use HasFactory;
    protected $fillable = [
        'order_id',
        'category_id',
        'sub_category_id',
        'product_id',
        'glass_thickness_id',
        'glass_color_id',
        'glass_finish_id',
        'glass_clearances_id',
        'glass_type_id',
        'quantity',
        'width',
        'height',
        'obs_factory',
        'obs_client',
        'obs_tempera',
        'price',
        'date_delivery'
    ];

    public function orders(): BelongsTo
    {
        return $this->belongsTo(Orders::class, 'order_id', 'id');
    }

    /**
     * @throws Exception
     */
    public function validate(): bool
    {
        return true;
    }

}
?>
