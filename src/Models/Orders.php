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
        'cod',
        'status',
        'obs',
        'url',
        'client_id',
        'user_id',
        'category_id',
        'sub_category_id',
        'product_id',
        'glass_thickness_id',
        'glass_color_id',
        'glass_finish_id',
        'glass_clearances_id',
        'updated_at',
        'created_at'
    ];

    /**
     * @throws Exception
     */
    public function validate(): bool
    {
        return true;
    }

}
?>
