<?php

namespace Blixter\ActiveRecord;

use Anax\DatabaseActiveRecord\ActiveRecordModel;

/**
 * An implementation of the Active Record pattern to be used as
 * base class for database driven models.
 */
class ActiveRecordModelExtra extends ActiveRecordModel
{

    /**
     * Find and return first object found by search criteria and use
     * its data to populate this instance.
     *
     * The search criteria `$where` of can be set up like this:
     *  `id = ?`
     *  `id1 = ? and id2 = ?`
     *
     * The `$value` can be a single value or an array of values.
     *
     * @param string $where to use in where statement.
     * @param mixed  $value to use in where statement.
     *
     * @return this
     */
    public function findWhere($where, $value, $select = "*"): object
    {
        $this->checkDb();
        $params = is_array($value) ? $value : [$value];
        $this->db->connect()
            ->select($select)
            ->from($this->tableName)
            ->where($where)
            ->execute($params)
            ->fetchInto($this);
        return $this;
    }

    /**
     * Find and return first object found by search criteria and use
     * its data to populate this instance.
     *
     * The search criteria `$where` of can be set up like this:
     *  `id = ?`
     *  `id1 = ? and id2 = ?`
     *
     * The `$value` can be a single value or an array of values.
     *
     * @param string $where to use in where statement.
     * @param mixed  $value to use in where statement.
     * @param string $joinTable Which table to join.
     * @param string $joinOn Where to join the table.
     * @param string $select What to select.
     *
     * @return this
     */
    public function findWhereJoin($where, $value, $joinTable, $joinOn, $select = "*"): object
    {
        $this->checkDb();
        $params = is_array($value) ? $value : [$value];
        $this->db->connect()
            ->select($select)
            ->from($this->tableName)
            ->where($where)
            ->join($joinTable, $joinOn)
            ->execute($params)
            ->fetchInto($this);
        return $this;
    }

    /**
     * Find and return all matching the search criteria.
     *
     *
     * The `$value` can be a single value or an array of values.
     *
     * @param string  $orderBy for building the order by part of the query.
     * @param string  $limit for building the LIMIT part of the query.
     *
     * @return array of object of this class
     */
    public function findAllOrderBy($orderBy, $limit = 10000)
    {
        $this->checkDb();
        return $this->db->connect()
            ->select()
            ->from($this->tableName)
            ->orderBy($orderBy)
            ->limit($limit)
            ->execute()
            ->fetchAllClass(get_class($this));
    }

    /**
     * Find and return all matching the search criteria.
     *
     *
     * The `$value` can be a single value or an array of values.
     *
     * @param string  $orderBy for building the order by part of the query.
     * @param string  $groupBy for building the group by part of the query.
     * @param string  $limit for building the LIMIT part of the query.
     * @param string $joinTable Which table to join.
     * @param string $joinOn Where to join the table.
     *
     * @return array of object of this class
     */
    public function findAllJoinOrderBy($orderBy, $joinTable, $joinOn, $limit = 10000, $select = "*")
    {
        $this->checkDb();
        return $this->db->connect()
            ->select($select)
            ->from($this->tableName)
            ->orderBy($orderBy)
            ->join($joinTable, $joinOn)
            ->limit($limit)
            ->execute()
            ->fetchAllClass(get_class($this));
    }

    /**
     * Find and return all matching the search criteria.
     *
     *
     * The `$value` can be a single value or an array of values.
     *
     * @param string  $orderBy for building the order by part of the query.
     * @param string  $groupBy for building the group by part of the query.
     * @param string  $limit for building the LIMIT part of the query.
     * @param string $joinTable Which table to join.
     * @param string $joinOn Where to join the table.
     *
     * @return array of object of this class
     */
    public function findAllJoinOrderByGroupBy($orderBy, $groupBy, $joinTable, $joinOn, $limit = 10000, $select = "*")
    {
        $this->checkDb();
        return $this->db->connect()
            ->select($select)
            ->from($this->tableName)
            ->groupBy($groupBy)
            ->orderBy($orderBy)
            ->join($joinTable, $joinOn)
            ->limit($limit)
            ->execute()
            ->fetchAllClass(get_class($this));
    }

    /**
     * Find and return all matching the search criteria.
     *
     * The search criteria `$where` of can be set up like this:
     *  `id = ?`
     *  `id IN [?, ?]`
     *
     * The `$value` can be a single value or an array of values.
     *
     * @param string $where to use in where statement.
     * @param mixed  $value to use in where statement.
     *
     * @return array of object of this class
     */
    public function findAllWhere($where, $value, $select = "*")
    {
        $this->checkDb();
        $params = is_array($value) ? $value : [$value];
        return $this->db->connect()
            ->select($select)
            ->from($this->tableName)
            ->where($where)
            ->execute($params)
            ->fetchAllClass(get_class($this));
    }

    /**
     * Find and return all matching the search criteria.
     *
     * The search criteria `$where` of can be set up like this:
     *  `id = ?`
     *  `id IN [?, ?]`
     *
     * The `$value` can be a single value or an array of values.
     *
     * @param string $where to use in where statement.
     * @param mixed  $value to use in where statement.
     * @param string  $orderBy for building the order by part of the query.
     * @param string  $limit for building the LIMIT part of the query.
     *
     * @return array of object of this class
     */
    public function findAllWhereOrderBy($where, $value, $orderBy, $limit = 10000)
    {
        $this->checkDb();
        $params = is_array($value) ? $value : [$value];
        return $this->db->connect()
            ->select()
            ->from($this->tableName)
            ->where($where)
            ->orderBy($orderBy)
            ->limit($limit)
            ->execute($params)
            ->fetchAllClass(get_class($this));
    }

    /**
     * Find and return all matching the search criteria.
     *
     *
     * The `$value` can be a single value or an array of values.
     *
     * @param string $where to use in where statement.
     * @param mixed $value to use in where statement.
     * @param string $joinTable Which table to join.
     * @param string $joinOn Where to join the table.
     * @param string $select What to select.
     *
     *
     * @return array of object of this class
     */
    public function findAllWhereJoin($where, $value, $joinTable, $joinOn, $select = "*")
    {
        $this->checkDb();
        $params = is_array($value) ? $value : [$value];
        return $this->db->connect()
            ->select($select ?? null)
            ->from($this->tableName)
            ->where($where)
            ->join($joinTable, $joinOn)
            ->execute($params)
            ->fetchAllClass(get_class($this));
    }

    /**
     * Find and return all matching the search criteria.
     *
     *
     * The `$value` can be a single value or an array of values.
     *
     * @param string $where to use in where statement.
     * @param mixed $value to use in where statement.
     * @param string $joinTable Which table to join.
     * @param string $joinOn Where to join the table.
     * @param string $orderBy for building the order by part of the query.
     * @param string $select What to select.
     *
     *
     * @return array of object of this class
     */
    public function findAllWhereJoinOrderBy($where, $value, $joinTable, $joinOn, $orderBy, $select = "*")
    {
        $this->checkDb();
        $params = is_array($value) ? $value : [$value];
        return $this->db->connect()
            ->select($select ?? null)
            ->from($this->tableName)
            ->where($where)
            ->join($joinTable, $joinOn)
            ->orderBy($orderBy)
            ->execute($params)
            ->fetchAllClass(get_class($this));
    }

    /**
     * Create new row.
     *
     * @return void
     */
    protected function create()
    {
        $this->checkDb();
        $properties = $this->getProperties();
        unset($properties[$this->tableIdColumn]);
        $columns = array_keys($properties);
        $values = array_values($properties);

        $this->db->connect()
            ->insert($this->tableName, $columns)
            ->execute($values);

        $this->{$this->tableIdColumn} = $this->db->lastInsertId();
        return $this->db->lastInsertId();
    }
}
