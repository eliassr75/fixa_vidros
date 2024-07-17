<?php

namespace App\Models;

use Exception;
use App\Validators\Validator;
use Illuminate\Database\Eloquent\Model;
use App\Controllers\CookieController;
use Ramsey\Uuid\Uuid;

class Category extends Model {
    protected $table = 'category';
    protected $columns = [
        'id',
        'name',
        'cod',
        'obs',
        'image',
        'glass_type_id',
        'size_id',
        'color_id',
        'finish_id',
        'glass_clearances_id',
        'created_at',
        'updated_at'
    ];

    protected $guarded = ['id'];

    /**
     * @throws Exception
     */
    public function validate(): bool
    {
        return true;
    }

}
?>
