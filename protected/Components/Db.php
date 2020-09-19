<?php

namespace App\Components;

use App\Exceptions\DbException;

class Db
{
    use Singleton;

    protected \PDO $dbh;

    protected function __construct()
    {
        $config = Config::instance();
        try {
            $dsn = $config->data['driver'] . ':host=' . $config->data['host'] . ';dbname=' . $config->data['database'];
            $this->dbh = new \PDO($dsn, $config->data['username'], $config->data['password']);
        } catch (\PDOException $exception) {
            throw new DbException('Нет соединения с БД!');
        }
    }

    public function query($sql, $class, $params = []): array
    {
        $sth = $this->dbh->prepare($sql);
        $res = $sth->execute($params);
        if (!$res) {
            throw new DbException('Ошибка при выполнении запроса: ' . $sql);
        }
        return $sth->fetchAll(\PDO::FETCH_CLASS, $class);
    }

    public function execute($sql, $params = []): bool
    {
        $sth = $this->dbh->prepare($sql);
        $res = $sth->execute($params);
        if (!$res) {
            throw new DbException('Ошибка при выполнении запроса: ' . $sql);
        }
        return $res;
    }

    public function lastId()
    {
        $res = $this->dbh->lastInsertId();
        if (!$res) {
            throw new DbException('Ошибка при выполнении запроса');
        }
        return $res;
    }

    public function queryEach($sql, $class, $params = [])
    {
        $sth = $this->dbh->prepare($sql);
        if (!$sth->execute($params)) {
            throw new DbException('Ошибка при выполнении запроса: ' . $sql);
        }
        $sth->setFetchMode(\PDO::FETCH_CLASS, $class);
        while ($res = $sth->fetch()) {
            yield $res;
        }
    }
}