<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    protected $table = 'words';

    public int $user_id;
    public string $eng_word;
    public string $rus_word;
    public string $imageUrl;
    public string $soundUrl;

}