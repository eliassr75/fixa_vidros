<?php

namespace App\Models;

use Exception;
use App\Validators\Validator;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class User extends Model {
    protected $table = 'users';
    protected $columns = [
        'id',
        'name',
        'email',
        'username',
        'password',
        'language',
        'token',
        'created_at',
        'updated_at'
    ];

    protected $guarded = ['id'];
    public $allowed_keys = [
        'name',
        'email',
        'username',
        'password',
        'language'
    ];

//    public function permissions()
//    {
//        return $this->hasMany(Permissions::class, 'id', 'permission_id');
//    }

    /**
     * @throws Exception
     */
    public function validate(): bool
    {

        if(empty($this->token)){
            $this->token = Uuid::uuid4();
        }

        return Validator::validateUser($this);
    }

}
?>
