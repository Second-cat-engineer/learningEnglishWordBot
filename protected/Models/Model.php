<?php

namespace App\Models;

use App\Components\Db;

abstract class Model
{
    protected const TABLE = '';

    protected $id;

    public function getId()
    {
        return $this->id;
    }

    public static function findAllByUser($user_id): array
    {
        $parameters[':user_id'] = $user_id;
        $db = Db::instance();
        $sql = 'SELECT * FROM ' . static::TABLE . ' WHERE user_id=:user_id';
        return $db->query($sql, static::class, $parameters);
    }

    public function saveNewWord(): bool
    {
        if (!empty($this->findByWord())) {
            throw new \Exception('введенное слово уже есть в Вашем словаре!');
        }
        return $this->insert();
    }

    public function insert(): bool
    {
        $props = get_object_vars($this);

        $columns = [];
        $binds = [];
        $data = [];
        foreach ($props as $name => $value) {
            if ('id' == $name) {
                continue;
            }

            $columns[] = $name;
            $binds[] = ':' . $name;
            $data[':' . $name] = $value;
        }

        $sql = 'INSERT INTO ' . static::TABLE . ' 
        (' . implode(', ', $columns) . ') 
        VALUES (' . implode(', ', $binds) . ' )';

        $db = Db::instance();
        $res = $db->execute($sql, $data);
        $this->id = $db->lastId();

        return $res;
    }

    public function delete(): bool
    {
        $data[':id'] = $this->id;
        $sql = 'DELETE FROM ' . static::TABLE . ' WHERE id=:id';

        $db = Db::instance();
        return $db->execute($sql, $data);
    }
}