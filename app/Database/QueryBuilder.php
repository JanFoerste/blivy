<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Database;


use Blivy\Exception\QueryBuilderException;

class QueryBuilder extends DB
{
    /**
     * @var string $type
     */
    protected $type;

    /**
     * @var string $table
     */
    protected $table;

    /**
     * @var string $select
     */
    protected $select;

    /**
     * @var array $where
     */
    protected $where = [];

    /**
     * @var array $whereVals
     */
    protected $whereVals = [];

    /**
     * @var array $insert
     */
    protected $insert = [];

    /**
     * @var array $set
     */
    protected $set = [];

    /**
     * @var string $joinType
     */
    protected $joinType;

    /**
     * @var string $joinTable
     */
    protected $joinTable;

    /**
     * @var string $joinOn
     */
    protected $joinOn;

    /**
     * @var array $options
     */
    protected $options = [];

    /**
     * @var string $query
     */
    protected $query;

    /**
     * ### QueryBuilder constructor.
     * ### Ensures that a database is connected
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ### Defines the main table to be used
     *
     * @param string $table
     * @return $this|QueryBuilder
     */
    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * ### Sets the type to 'select' and includes all fields to be selected
     *
     * @param string $select
     * @return $this|QueryBuilder
     */
    public function select($select = '*')
    {
        $this->type = 'select';
        $this->select = $select;

        return $this;
    }

    /**
     * ### Sets the type to 'insert' and stores data array to be saved
     *
     * @param array $data
     * @return $this|QueryBuilder
     */
    public function insert($data)
    {
        $this->type = 'insert';
        $this->insert = $data;

        return $this;
    }

    /**
     * ### Sets the type to 'update'
     *
     * @return $this|QueryBuilder
     */
    public function update()
    {
        $this->type = 'update';

        return $this;
    }

    /**
     * ### Pushes and appends given WHERE statements
     *
     * @param string $col
     * @param null|string $op
     * @param null|string $var
     * @return $this|QueryBuilder
     */
    public function where($col, $op = null, $var = null)
    {
        // ### Assuming to use '=' as operator if none is given
        if (func_num_args() == 2) {
            $var = $op;
            $op = '=';
        }

        // ### If there already is a where statement, switch to AND
        if (count($this->where) > 0) {
            return $this->andWhere($col, $op, $var);
        }

        $str = "WHERE $col $op :";
        $this->baseWhere($str, $col, $var);
        return $this;
    }

    /**
     * ### Pushes and appends given AND statements (extends WHERE)
     *
     * @param string $col
     * @param null|string $op
     * @param null|string $var
     * @return $this|QueryBuilder
     */
    public function andWhere($col, $op = null, $var = null)
    {
        // ### Assuming to use '=' as operator if none is given
        if (func_num_args() == 2) {
            $var = $op;
            $op = '=';
        }
        $str = "AND $col $op :";
        $this->baseWhere($str, $col, $var);
        return $this;
    }

    /**
     * ### Pushes and appends given OR statements (extends WHERE)
     *
     * @param string $col
     * @param null|string $op
     * @param null|string $var
     * @return $this|QueryBuilder
     */
    public function orWhere($col, $op = null, $var = null)
    {
        // ### Assuming to use '=' as operator if none is given
        if (func_num_args() == 2) {
            $var = $op;
            $op = '=';
        }
        $str = "OR $col $op :";
        $this->tryClusterWhere($str, $col, $var);
        return $this;
    }

    /**
     * @param string $str
     * @param string $col
     * @param string $var
     * @return $this|QueryBuilder
     */
    private function baseWhere($str, $col, $var)
    {
        $count = $this->countUp($this->where, $col);
        $this->where[$col . $count] = ' ' . $str . $this->filterForDot($col) . $count;
        $this->appendWhereValue($this->filterForDot($col), $var);
        return $this;
    }

    /**
     * ### Tries to cluster OR statements into brackets if applicable
     *
     * @param string $str
     * @param string $col
     * @param string $var
     * @return $this|QueryBuilder
     */
    private function tryClusterWhere($str, $col, $var)
    {
        if (!array_key_exists($col, $this->where)) {
            $this->baseWhere($str, $col, $var);
            return $this;
        }

        $ex = trim($this->where[$col]);
        $ex_op = trim(substr($ex, 0, strpos($ex, ' ')));
        $ex_str = trim(substr($ex, strpos($ex, ' ')));

        $count = $this->countUp($this->where, $col);
        $put = $ex_op . ' ( ' . $ex_str . ' ' . $str . $this->filterForDot($col) . $count . ' )';

        $this->where[$col] = $put;
        $this->appendWhereValue($this->filterForDot($col), $var);

        return $this;
    }

    /**
     * ### Filters out a dot from the column string
     *
     * @param string $col
     * @return string
     */
    private function filterForDot($col)
    {
        if (strpos($col, '.') > -1) {
            $col = substr($col, strpos($col, '.') + 1);
        }

        return $col;
    }

    /**
     * ### Stores the set values
     *
     * @param array $values
     * @return $this|QueryBuilder
     */
    public function set($values)
    {
        $this->set = $values;

        return $this;
    }

    /**
     * ### Alias for leftJoin by default
     *
     * @param string $table
     * @param array $on
     * @return $this|QueryBuilder
     * @throws QueryBuilderException
     */
    public function join($table, array $on)
    {
        $this->leftJoin($table, $on);
        return $this;
    }

    /**
     * ### Sets the left join type
     *
     * @param string $table
     * @param array $on
     * @return $this
     * @throws QueryBuilderException
     */
    public function leftJoin($table, array $on)
    {
        if (sizeof($on) < 2 || sizeof($on) > 3) {
            throw new QueryBuilderException('The on statement requires 2 or 3 parameters.');
        }

        $this->joinType = 'left';
        $this->baseJoin($table, $on);
        return $this;
    }

    /**
     * ### Sets the right join type
     *
     * @param string $table
     * @param array $on
     * @return $this|QueryBuilder
     * @throws QueryBuilderException
     */
    public function rightJoin($table, array $on)
    {
        if (sizeof($on) < 2 || sizeof($on) > 3) {
            throw new QueryBuilderException('The on statement requires 2 or 3 parameters.');
        }

        $this->joinType = 'right';
        $this->baseJoin($table, $on);
        return $this;
    }

    /**
     * ### Implodes on statement and sets join table
     *
     * @param string $table
     * @param array $on
     * @return $this|QueryBuilder
     */
    private function baseJoin($table, array $on)
    {
        // ### Assuming to use '=' as operator if none is given
        if (sizeof($on) === 2) {
            $on[2] = $on[1];
            $on[1] = '=';
        }

        $this->joinTable = $table;
        $str = implode(' ', $on);
        $this->joinOn = $str;

        return $this;
    }

    /**
     * ### Appends the given WHERE/AND value to our array for
     * ### PDO's prepared statements.
     *
     * @param string $col
     * @param string $var
     * @return $this|QueryBuilder
     */
    private function appendWhereValue($col, $var)
    {
        $count = $this->countUp($this->whereVals, $col);
        $this->whereVals[$col . $count] = $var;

        return $this;
    }

    /**
     * ### Helper class to count up and prevent overwritten values
     *
     * @param array $arr
     * @param string $col
     * @return int|string
     */
    private function countUp($arr, $col)
    {
        $count = 0;

        if (isset($arr[$col])) {
            $set = true;
            while ($set) {
                $count++;
                if (isset($arr[$col . $count])) {
                    continue;
                }
                $set = false;
            }
        } else {
            $count = '';
        }

        return $count;
    }

    /**
     * Builds and prepares the current Query
     *
     * @return \PDOStatement
     * @throws QueryBuilderException
     */
    private function prepare()
    {
        $db = $this->getInstance();
        $this->buildQuery();

        try {
            $prepare = $db->prepare($this->query);
        } catch (\PDOException $e) {
            throw new QueryBuilderException($e->getMessage());
        }

        return $prepare;
    }

    /**
     * ### Binds WHERE/AND/OR values and executes the query
     *
     * @param int $amount
     * @return array|\PDO
     * @throws QueryBuilderException
     */
    public function get($amount = -1)
    {
        $prepare = $this->prepare();
        try {
            $prepare = $this->bindValues($prepare, $this->whereVals);
            $prepare->execute();
            $all = $prepare->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new QueryBuilderException($e->getMessage());
        }

        if ($amount > -1) {
            $all = array_slice($all, 0, $amount);
        }

        return $all;
    }

    /**
     * ### Returns the generated query without appended values
     *
     * @return string
     */
    public function getQuery()
    {
        $this->buildQuery();
        return $this->query;
    }

    /**
     * ### Gets all results and filters out the first one
     *
     * @return array|\PDO
     * @throws QueryBuilderException
     */
    public function first()
    {
        $all = $this->get();
        return $all[0];
    }

    /**
     * ### Runs the set insert query
     *
     * @return bool
     * @throws QueryBuilderException
     */
    public function store()
    {
        $prepare = $this->prepare();

        try {
            $prepare = $this->bindValues($prepare, $this->insert);
            $prepare->execute();
        } catch (\PDOException $e) {
            throw new QueryBuilderException($e->getMessage());
        }

        return true;
    }

    /**
     * ### Builds the basic query to be executed later
     *
     * @return $this|QueryBuilder
     * @throws QueryBuilderException
     */
    private function buildQuery()
    {
        $this->createBaseQuery();
        $this->processSelectStatements();
        $this->processTableSelection();
        $this->processInsertStatements();
        $this->processWhereStatements();
        $this->processJoinStatements();
        $this->processAdditionalOptions();

        return $this;
    }

    /**
     * ### Sets the query type and creates the base query
     *
     * @return $this|QueryBuilder
     * @throws QueryBuilderException
     */
    private function createBaseQuery()
    {
        switch ($this->type) {
            case 'select':
                $base = 'SELECT {SELECT} FROM {TABLE} {JOIN} {WHERE} {OPTIONS};';
                break;
            case 'insert':
                $base = 'INSERT INTO {TABLE}{COLUMNS} VALUES{VALUES};';
                break;
            case 'update':
                $base = 'UPDATE {TABLE} SET {UPDATES} {WHERE};';
                break;
            default:
                throw new QueryBuilderException('Query type not set!');
        }

        $this->query = $base;

        return $this;
    }

    /**
     * ### Replaces the {SELECT} part with the specified columns
     *
     * @return $this|QueryBuilder
     */
    private function processSelectStatements()
    {
        if (!isset($this->select)) {
            return $this;
        }

        $this->query = str_replace('{SELECT}', $this->select, $this->query);

        return $this;
    }

    /**
     * ### Replaces the {TABLE} part with the selected base table
     *
     * @return $this|QueryBuilder
     * @throws QueryBuilderException
     */
    private function processTableSelection()
    {
        if (!isset($this->table)) {
            throw new QueryBuilderException('No table selected!');
        }

        $this->query = str_replace('{TABLE}', $this->table, $this->query);

        return $this;
    }

    private function processInsertStatements()
    {
        if (count($this->insert) < 1 || $this->type !== 'insert') {
            return $this;
        }

        $tmp_col = '(';
        $tmp_val = '(';
        foreach ($this->insert as $col => $value) {
            $tmp_col = $tmp_col . $col . ',';
            $tmp_val = $tmp_val . ':' . $col . ',';
        }

        $tmp_col = substr($tmp_col, 0, -1) . ')';
        $tmp_val = substr($tmp_val, 0, -1) . ')';

        $this->query = str_replace('{COLUMNS}', $tmp_col, $this->query);
        $this->query = str_replace('{VALUES}', $tmp_val, $this->query);

        return $this;
    }

    /**
     * ### Replaces the {WHERE} part with all WHERE/AND/OR statements
     *
     * @return $this|QueryBuilder
     */
    private function processWhereStatements()
    {
        if (count($this->where) > 0) {
            $tmp = '';
            foreach ($this->where as $item) {
                $tmp = $tmp . $item . ' ';
            }

            $this->query = str_replace('{WHERE}', $tmp, $this->query);
        } else {
            $this->query = str_replace('{WHERE}', '', $this->query);
        }

        return $this;
    }

    private function processJoinStatements()
    {
        if (!isset($this->joinType) || !isset($this->joinTable) || !isset($this->joinOn)) {
            $this->query = str_replace('{JOIN}', '', $this->query);
            return $this;
        }

        switch ($this->joinType) {
            case 'left':
                $tmp_str = 'LEFT JOIN ';
                break;
            case 'right':
                $tmp_str = 'RIGHT JOIN ';
                break;
            default:
                throw new QueryBuilderException('Fatal Error: Join type not set!');
        }

        $tmp_str = $tmp_str . $this->joinTable;
        $tmp_str = $tmp_str . ' ON (' . $this->joinOn . ')';
        $this->query = str_replace('{JOIN}', $tmp_str, $this->query);

        return $this;
    }

    /**
     * ### Processes and replaces additional options
     *
     * @return $this|QueryBuilder
     */
    private function processAdditionalOptions()
    {
        if (count($this->options) <= 0) {
            $this->query = str_replace('{OPTIONS}', '', $this->query);
            return $this;
        }

        return $this;
    }

    /**
     * ### Binds values to the PDO instance
     *
     * @param array $values
     * @param \PDOStatement $prepare
     * @return \PDOStatement
     */
    private function bindValues($prepare, $values)
    {
        $types = [
            'string' => \PDO::PARAM_STR,
            'integer' => \PDO::PARAM_INT,
            'boolean' => \PDO::PARAM_BOOL,
            'NULL' => \PDO::PARAM_NULL
        ];

        foreach ($values as $col => $val) {

            $prepare->bindValue(':' . $col, $val, $types[gettype($val)]);
        }

        return $prepare;
    }
}