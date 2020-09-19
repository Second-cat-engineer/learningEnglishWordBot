<?php

namespace App\Models;

use App\Models\Model;
use App\Components\Db;

class Word extends Model
{
    protected const TABLE = 'words';

    public int $user_id;
    public string $eng_word;
    public string $rus_word;
    public string $image_url;
    public string $sound_url;

    public function findByWord()
    {
        $parameters[':eng_word'] = $this->eng_word;
        $parameters[':rus_word'] = $this->rus_word;
        $parameters[':user_id'] = $this->user_id;

        $db = Db::instance();
        $sql = 'SELECT * FROM ' . static::TABLE .
            ' WHERE user_id=:user_id AND eng_word=:eng_word OR rus_word=:rus_word';
        $res = $db->query($sql, static::class, $parameters);
        if (empty($res)) {
            return false;
        }
        return $res;
    }
}