<?php

namespace App\Models;

use Exception;
use App\Validators\Validator;
use Illuminate\Database\Eloquent\Model;
use App\Controllers\CookieController;
use Ramsey\Uuid\Uuid;

class Orders extends Model {
    protected $table = 'orders';
    protected $columns = [
        'id',
        'name',
        'status',
        'obs_factory',
        'obs_client',
        'obs_tempera',
        'url',
        'client_id',
        'user_id',
        'total_price',
        'updated_at',
        'created_at'
    ];

    public function items()
    {
        return $this->hasMany(OrdersItems::class);
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
    protected $table = 'orders_items';
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
        'category',
        'type',
        'price',
        'updated_at',
        'created_at'
    ];

    public function orders()
    {
        return $this->belongsTo(Orders::class);
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
