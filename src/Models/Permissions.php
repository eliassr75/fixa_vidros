<?php

namespace App\Models;

use Exception;
use App\Validators\Validator;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Permissions extends Model {
    protected $table = 'users';
    protected $columns = [
        'id',
        'name',
        'description',
        'created_at',
        'updated_at'
    ];

    protected $guarded = ['id'];
    public $allowed_keys = [
        'name',
        'description',
    ];


    public function users()
    {
        return $this->belongsTo(User::class);
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
