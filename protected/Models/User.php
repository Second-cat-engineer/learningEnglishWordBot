<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected $user_id;
    protected $first_name;
    protected $last_name;
    protected $username;



}