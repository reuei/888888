<?php
/**
 * 模型基类
 */
class Model
{
    protected $table = '';
    protected $pk = 'id';

    public function __construct($table = '')
    {
        if ($table) {
            $this->table = $table;
        }
    }

    public function find($id)
    {
        return Db::table($this->table)->where($this->pk, $id)->find();
    }

    public function where($field, $op, $value = null)
    {
        return Db::table($this->table)->where($field, $op, $value);
    }

    public function insert($data)
    {
        return Db::insert($this->table, $data);
    }

    public function update($data, $where, $params = [])
    {
        return Db::update($this->table, $data, $where, $params);
    }

    public function delete($where, $params = [])
    {
        return Db::delete($this->table, $where, $params);
    }
}
