<?php

namespace App\Core\System;

use JetBrains\PhpStorm\Pure;
use PDO;
use PDOException;
use PDOStatement;

abstract class Model {

    private string $table;

    public function __construct() {
        $this->table = str_replace('model', '', substr(strrchr(strtolower(get_class($this)), "\\"), 1));
    }

    public function connect(string $db_user = null, string $db_pass = null, string $db_name = DB_NAME): bool|PDO {
        $db_user = $db_user?:$_SESSION['username'];
        $db_pass = $db_pass?:$_SESSION['password'];

        try {
            $db = new PDO(DB_TYPE . ':dbname='. $db_name .';host='. DB_HOST, $db_user, $db_pass);
        }
        catch (PDOException $e) {
            return $e->getMessage();
        }

        return $db;
    }

    public function query(string $sql, array $params = null, object $db = null): bool|PDOStatement {
        $db = $db?:$this->connect();

        if (is_null($params)) {
            $query = $db->query($sql);
        } else {
            $query = $db->prepare($sql);

            foreach ($params as $param) {
                $query->bindValue($param[0], $param[1], $param[2]);
            }
            $query->execute();
        }

        $query->setFetchMode(PDO::FETCH_CLASS, get_class($this));
        return $query;
    }

    public function create(): bool|PDOStatement {
        $fields = [];
        $values = [];
        $params = [];

        foreach ($this as $k => $v) {
            if (!is_null($v) && $k !== 'table') {
                $fields[] = $k;
                $params[] = ":$k";
                $values[] = [":$k", $v, $this->match($k)];
            }
        }

        $field_list = implode(', ', $fields);
        $param_list = implode(', ', $params);

        return $this->query("INSERT INTO {$this->table} ({$field_list}) VALUES ({$param_list})", $values);
    }

    public function update(int $id): bool|PDOStatement {
        $fields = [];
        $values = [];

        foreach ($this as $k => $v) {
            if (!is_null($v) && $k !== 'table') {
                $fields[] = "$k = :$k";
                $values[] = [":$k", $v, $this->match($k)];
            }
        }

        $field_list = implode(', ', $fields);
        return $this->query("UPDATE {$this->table} SET {$field_list} WHERE id = $id", $values);
    }

    public function delete(int $id): bool|PDOStatement {
        return $this->query("DELETE FROM {$this->table} WHERE id = $id");
    }

    /**
     * @param int $id
     * @return bool|$this
     */
    public function findById(int $id): bool|self {
        return $this->query("SELECT * FROM {$this->table} WHERE id = $id LIMIT 1")->fetch();
    }

    /**
     * @param array $filter
     * @return bool|array|$this
     */
    public function findBy(array $filter): bool|array|self {
        $fields = [];
        $values = [];

        foreach ($filter as $k => $v) {
            $fields[] = "$k = :$k";
            $values[] = [":$k", $v, $this->match($k)];
        }

        $field_list = implode(' AND ', $fields);
        return $this->query("SELECT * FROM {$this->table} WHERE {$field_list}", $values)->fetchAll();
    }

    /**
     * @param array $filter
     * @return bool|PDOStatement|$this
     */
    public function findOneBy(array $filter): bool|PDOStatement|self {
        $fields = [];
        $values = [];

        foreach ($filter as $k => $v) {
            $fields[] = "$k = :$k";
            $values[] = [":$k", $v, $this->match($k)];
        }

        $field_list = implode(' AND ', $fields);
        return $this->query("SELECT * FROM {$this->table} WHERE {$field_list} LIMIT 1", $values)->fetch();
    }

    /**
     * @return bool|array|$this
     */
    public function countAll(): bool|array|self
    {
        return $this->query("SELECT COUNT(*) as nb_items FROM {$this->table}")->fetch();
    }

    /**
     * @param string $search_string
     * @param array $tables
     * @return bool|array|$this
     */
    public function countLike(string $search_string, array $search_columns): bool|array|self {
        $where_list = "";
        if(!empty($search_string)) {
            foreach ($search_columns as $column) {
                if (!empty($where_list)) $where_list =  $where_list. " OR " . $column . " LIKE '%$search_string%'";
                else $where_list = "WHERE " . $column . " LIKE '%$search_string%'";
            }
        }
        return $this->query("SELECT * FROM {$this->table} $where_list")->fetchAll();
    }

    /**
     * @param string $search_string
     * @param array $search_columns
     * @param int $first_of_page
     * @param int $nb_per_page
     * @return bool|array|$this
     */
    public function find(string $search_string, array $search_columns, int $first_of_page, int $nb_per_page, string $filter, string $order): bool|array|self
    {
        $where_list = "";
        if(!empty($search_string)) {
            foreach ($search_columns as $column) {
                if (!empty($where_list)) $where_list =  $where_list. " OR " . $column . " LIKE '%$search_string%'";
                else $where_list = "WHERE " . $column . " LIKE '%$search_string%'";
            }
        }
        $order_by = "";
        if(!empty($filter && $order)) {
            $order_by = "ORDER BY $filter $order";
        }
        return $this->query("SELECT * FROM {$this->table} $where_list $order_by LIMIT $first_of_page, $nb_per_page")->fetchAll();
    }

    /**
     * @return bool|array|$this
     */
    public function findPageRange($first_of_page, $nb_per_page): bool|array|self
    {
        return $this->query("SELECT * FROM {$this->table} LIMIT $first_of_page, $nb_per_page")->fetchAll();
    }

    /**
     * @return bool|string|$this
     */
    public function getTableName(): bool|string|self
    {
        $query = $this->query("SELECT * FROM {$this->table} LIMIT 0");
        $name = $query->getColumnMeta(0);
        return $name['table'];
    }

    /**
     * @return bool|array|$this
     */
    public function getColumnsNames(): bool|array|self
    {
        $query = $this->query("SELECT * FROM {$this->table} LIMIT 0");
        for ($i = 0; $i < $query->columnCount(); $i++) {
            $col = $query->getColumnMeta($i);
            $columns[] = $col['name'];
        }
        return $columns;
    }

    #[Pure] private function match(int|string $key): int {
        return match (gettype($key)) {
            'string', 'float' => PDO::PARAM_STR,
            'int' => PDO::PARAM_INT,
            'bool' => PDO::PARAM_BOOL
        };
    }

}
