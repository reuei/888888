<?php
namespace Framework\Database;

use PDO;

class QueryBuilder
{
    protected $pdo;
    protected $table;
    protected $wheres = [];
    protected $params = [];
    protected $orderBy = '';
    protected $limit = '';
    protected $selects = '*';

    public function __construct($pdo, $table)
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    public function where($column, $operator, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        $placeholder = ':w_' . count($this->params);
        $this->wheres[] = "$column $operator $placeholder";
        $this->params[] = $value;
        return $this;
    }

    public function whereRaw($raw, $params = [])
    {
        $this->wheres[] = "($raw)";
        foreach ($params as $p) {
            $this->params[] = $p;
        }
        return $this;
    }

    public function orderBy($column, $direction = 'ASC')
    {
        $this->orderBy = "ORDER BY $column $direction";
        return $this;
    }

    public function limit($n, $offset = 0)
    {
        $this->limit = "LIMIT $offset, $n";
        return $this;
    }

    public function select($fields = '*')
    {
        $this->selects = $fields;
        return $this->get();
    }

    public function get()
    {
        $sql = $this->buildSql();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);
        return $stmt->fetchAll();
    }

    public function first()
    {
        $this->limit(1);
        $result = $this->get();
        return $result[0] ?? null;
    }

    public function count()
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}" . $this->buildWhere();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);
        return (int) $stmt->fetchColumn();
    }

    public function find($id)
    {
        return $this->where('id', $id)->first();
    }

    public function insert($data)
    {
        $fields = array_keys($data);
        $placeholders = array_map(function ($f) {
            static $i = 0;
            return ':p' . $i++;
        }, $fields);
        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->pdo->prepare($sql);
        $i = 0;
        foreach ($data as $v) {
            $stmt->bindValue($placeholders[$i++], $v);
        }
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }

    public function update($data)
    {
        $sets = [];
        $values = [];
        foreach ($data as $k => $v) {
            $ph = ':s_' . count($values);
            $sets[] = "$k = $ph";
            $values[] = $v;
        }
        $sql = "UPDATE {$this->table} SET " . implode(',', $sets) . $this->buildWhere();
        $stmt = $this->pdo->prepare($sql);
        foreach ($values as $i => $v) {
            $stmt->bindValue(':s_' . $i, $v);
        }
        foreach ($this->params as $i => $v) {
            $stmt->bindValue(':w_' . $i, $v);
        }
        return $stmt->execute();
    }

    public function delete()
    {
        $sql = "DELETE FROM {$this->table}" . $this->buildWhere();
        $stmt = $this->pdo->prepare($sql);
        foreach ($this->params as $i => $v) {
            $stmt->bindValue(':w_' . $i, $v);
        }
        return $stmt->execute();
    }

    protected function buildWhere()
    {
        if (empty($this->wheres)) {
            return '';
        }
        return ' WHERE ' . implode(' AND ', $this->wheres);
    }

    protected function buildSql()
    {
        $sql = "SELECT {$this->selects} FROM {$this->table}" . $this->buildWhere();
        if ($this->orderBy) {
            $sql .= ' ' . $this->orderBy;
        }
        if ($this->limit) {
            $sql .= ' ' . $this->limit;
        }
        return $sql;
    }
}
