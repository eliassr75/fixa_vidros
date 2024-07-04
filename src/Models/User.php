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
        'cpf',
        'rg',
        'phone_number',
        'zip_code',
        'address',
        'complement',
        'address_number',
        'zone',
        'city',
        'state',
        'birthday',
        'age',
        'obs',
        'created_at',
        'updated_at'
    ];

    protected $guarded = ['id'];
    public $allowed_keys = [
        'name',
        'email',
        'username',
        'password',
        'language',
        'cpf',
        'rg',
        'phone_number',
        'zip_code',
        'address',
        'complement',
        'address_number',
        'zone',
        'city',
        'state',
        'birthday',
        'age',
        'obs'
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permissions::class, 'permission_user', 'user_id', 'permission_id');
    }

    public function log_entry()
    {
        return $this->belongsToMany(LogEntry::class, 'log_entry_user', 'user_id', 'log_id');
    }

    public function setLog($title, $description)
    {

        $log = new LogEntry();
        $log->request_type = $_SERVER['REQUEST_METHOD'];
        $log->title = $title;
        $log->description = $description;

        if($log->validate()){
            $log->save();
            $this->log_entry()->attach($log->id);
        }

    }

    public function startSession()
    {
        foreach ($this->columns as $column) {
            $_SESSION[$column] = $this->$column;
        }
    }

    /**
     * @throws Exception
     */
    public function validate(): bool
    {

        if(empty($this->token)){
            $this->token = Uuid::uuid4();
        }

        if(empty($this->language)){
            $this->language = "pt";
        }

        return Validator::validateUser($this);
    }

}
?>
