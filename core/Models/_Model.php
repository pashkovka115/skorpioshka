<?php


namespace Core\Models;

/*
https://meliorem.ru/backend/mysql/alter-table-izmenenie-tablic-v-baze-dannyx-mysql/

ALTER TABLE table_name MODIFY COLUMN column_name data_type AFTER column_after_name;


 ALTER TABLE `students`
	CHANGE COLUMN `parent_id` `parent_id` INT(11) NULL DEFAULT NULL AFTER `id`;
Изменить последовательность столбцов
В таблице students, столбец parent_id поставить после столбца id
 */


use Core\Db\Db;

abstract class Model
{ // \HOST, \USER, \PASS, \DB_NAME
//    const TABLE = '';
//    protected $id;

    public static function findAll()
    {
        return Db::getInstance()->query(
            'SELECT * FROM ' . static::TABLE,
            static::class
        );
    }

    public function insert()
    {
        if (!$this->isNew()) {
            return;
        }
        $columns = [];
        $values = [];
        foreach ($this as $k => $v) {
            if ('id' == $k) {
                continue;
            }
            $columns[] = $k;
            $values[':' . $k] = $v;
        }

        $sql = 'INSERT INTO ' . static::TABLE
            . ' (' . implode(', ', $columns) . ') VALUES(' .
            implode(', ', array_keys($values)) . ')';

        Db::getInstance()->execute($sql, $values);
    }


    private function isNew()
    {
        return empty($this->id);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function __get($name)
    {
        if (isset(Storage::getInstance()->$name))
            return Storage::getInstance()->$name;
    }

    public function __set($name, $value)
    {
        if (isset(Storage::getInstance()->$name))
            Storage::getInstance()->$name = $value;
    }


    /*public function db()
    {
        return Db::getInstance();
    }*/


}




























