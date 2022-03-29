<?php

namespace MusicPlayer;

use MusicPlayer\Exception\DatabaseException;

/**
 * Database Model
 * Encapsulate basic database interactions
 *
 * @author Adrian Pennington <git@penningtonfamily.net>
 */
abstract class Model {
    use Database;

    protected $table;

    protected $id_field;

    public function __construct($sqlite_file = null) {
        $this->connect($sqlite_file);

        if (empty($this->table) || empty($this->id_field)) {
            throw new DatabaseException($this->db, 'Missing table or id field model params');
        }
    }

    /**
     * Load model from database
     */
    protected function load($id) {
        $model = $this->select(['*'], [$this->id_field => $id]);

        return reset($model);
    }

    /**
     * Get results
     * 
     * @param array $fields
     * @return array[]
     */
    public function select(array $fields = ['*'], array $wheres = []) {
        $where = $this->queryConditions($wheres);

        $stmt = $this->db->prepare("SELECT " . join(',', $fields) . " FROM " . $this->table . $where);

        foreach ($wheres as $field => $value) {
            $stmt->bindValue(':' . $field, $value);
        }

        $result = $stmt->execute();
        $results = [];

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $results[] = $row;
        }

        return $results;
    }

    /**
     * Insert database row
     * 
     * @param $map of values to insert
     * @return int row Id if successful
     * @throws DatabaseException
     */
    public function insert(array $map) {
        $fields = array_keys($map);

        $stmt = $this->db->prepare("INSERT INTO {$this->table} (" . join(',', $fields) . ") VALUES (:" . join(", :", $fields) . ")");

        foreach ($map as $field => $value) {
            $stmt->bindValue(':' . $field, $value);
        }
        
        $result = $stmt->execute();

        if (!$result || !$this->db->lastInsertRowID()) {
            throw new DatabaseException($this->db, 'Failed to insert row');
        }

        return $this->db->lastInsertRowID();
    }

    /**
     * @param int $id
     * @param array $map field value map
     * @return int
     * @throws DatabaseException
     */
    public function update($id, array $map) {
        $fields = [];

        $query = "UPDATE SET {$this->table} SET ";

        foreach ($map as $field => $value) {
            $fields[] = ':' . $field . ' = :' . $field;
        }

        $stmt = $this->db->prepare($query . join(',', $fields) . ' where :' . $this->id_field . ' = :id');

        foreach ($map as $field => $value) {
            $stmt->bindValue(':' . $field, $value);
        }

        $stmt->bindValue(':id', $id);
        $result = $stmt->execute();

        if (!$result || !$this->db->changes()) {
            throw new DatabaseException($this->db, 'Failed to update row');
        }

        return $this->db->changes();
    }

    /**
     * @param int $id
     * @param array $map field value map
     * @return int
     * @throws DatabaseException
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE " . $this->id_field . " = :id");

        $stmt->bindValue(':id', $id);
        $result = $stmt->execute();

        if (!$result || !$this->db->changes()) {
            throw new DatabaseException($this->db, 'Failed to delete row');
        }

        return $this->db->changes();
    }

    /**
     * Process any query condition
     */
    protected function queryConditions($wheres) {
        $where = '';

        if (count($wheres)) {
            $whereArray = [];

            foreach ($wheres as $field => $value) {
                $whereArray[] = $field . ' = :' . $field;
            }

            $where = ' WHERE ' . join(' AND ', $whereArray);
        }

        return $where;
    }
}