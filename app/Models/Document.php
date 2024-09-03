<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'type_1',
        'path_1',
        'type_2',
        'path_2',
        'type_3',
        'path_3',
        'type_4',
        'path_4',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
