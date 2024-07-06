<?php

namespace App\Models;

use Exception;
use App\Validators\Validator;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use App\Controllers\FunctionController;

class PasswordReset extends Model {
    protected $table = 'password_reset';
    protected $columns = [
        'id',
        'user_id',
        'log_id',
        'token',
        'expiration'
    ];

    protected $guarded = ['id'];
    public $timestamps = false;
    public $allowed_keys = [
        'user_id',
        'log_id',
        'token',
        'expiration'
    ];

    public function check_expired(): bool
    {

        $currentDateTime = new \DateTime();
        $expirationDateTime = new \DateTime($this->expiration);

        if ($currentDateTime > $expirationDateTime) {
            return false;
        } else {
            return true;
        }

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
