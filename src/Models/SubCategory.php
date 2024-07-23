<?php

namespace App\Models;

use Exception;
use App\Validators\Validator;
use Illuminate\Database\Eloquent\Model;
use App\Controllers\CookieController;
use Ramsey\Uuid\Uuid;

class SubCategory extends Model {
    protected $table = 'sub_category';
    protected $columns = [
        'id',
        'name',
        'additional_name',
        'active',
        'image',
        'category_id',
        'glass_type_id',
        'created_at',
        'updated_at'
    ];

    protected $guarded = ['id'];

    public function glass_thickness()
    {
        return $this->hasMany(GlassThickness::class);
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
