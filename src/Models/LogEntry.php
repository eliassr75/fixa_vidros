<?php

namespace App\Models;

use Exception;
use App\Validators\Validator;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class LogEntry extends Model {
    protected $table = 'log_entry';
    protected $columns = [
        'id',
        'title',
        'description',
        'request_type',
        'user_agent'
    ];

    protected $guarded = ['id'];
    public $timestamps = false;
    public $allowed_keys = [
        'title',
        'description',
        'request_type',
        'user_agent'
    ];


    public function users()
    {
        return $this->belongsToMany(User::class, 'log_entry_user', 'log_id', 'user_id');
    }

    /**
     * @throws Exception
     */
    public function validate(): bool
    {
        return Validator::validateLog($this);
    }

}
?>
