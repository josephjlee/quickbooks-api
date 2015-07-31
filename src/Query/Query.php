<?php
namespace Wheniwork\Quickbooks\Query;

use RuntimeException;

/**
 * Class Query
 *
 * Built based on https://developer.intuit.com/docs/0100_accounting/0300_developer_guides/querying_data
 *
 * @package Wheniwork\Quickbooks\Query
 */
class Query
{

    /**
     * Creates a new query
     *
     * @param $entity
     * @return static
     */
    public static function from($entity)
    {
        return new static($entity);
    }

    /**
     * @var string
     */
    private $entity;

    /**
     * @var string
     */
    private $select = '*';

    /**
     * @var array
     */
    private $where = [];

    /**
     * @var array
     */
    private $order = [];

    /**
     * @var integer
     */
    private $offset;

    /**
     * @var integer
     */
    private $take;

    /**
     * Creates a new query
     *
     * @param $entity
     */
    protected function __construct($entity)
    {
        $this->entity($entity);
    }

    /**
     * Which entity to query against
     *
     * @param $entity
     * @return $this
     */
    public function entity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Which properties should be in the results
     *
     * @param $select
     * @return $this
     */
    public function select($select)
    {
        $this->select = $select;
        return $this;
    }

    /**
     * How to filter the results
     *
     * @param string $property
     * @param string $comparison
     * @param string $value
     * @return $this
     */
    public function where($property, $comparison, $value)
    {
        $this->where[] = [$property, $comparison, (is_bool($value)) ? $value: sprintf("'%s'", $value)];
        return $this;
    }

    /**
     * How to order the results
     *
     * @param string $property
     * @param string $order
     * @return $this
     */
    public function order($property, $order = null)
    {
        $this->order[] = [$property, $order];
        return $this;
    }

    /**
     * Offset the results
     *
     * @param integer $number
     * @return $this
     */
    public function offset($number)
    {
        if ($number < 0) {
            throw new RuntimeException("Offset must be zero or above.");
        }

        $this->offset = $number;
        return $this;
    }

    /**
     * How many records should be in the result
     *
     * @param integer $number
     * @return $this
     */
    public function take($number)
    {
        if ($number > 500) {
            throw new RuntimeException("Take must be 500 or below.");
        }

        $this->take = $number;
        return $this;
    }

    /**
     * Build the query
     *
     * @return string
     */
    public function build()
    {
        $query = [];

        // Select
        $query[] = sprintf("SELECT %s", $this->select);

        // From
        $query[] = sprintf("FROM %s", $this->entity);

        // Where
        if ($this->where) {
            $where = [];
            foreach ($this->where as $value) {
                $where[] = implode(' ', $value);
            }

            // OR isn't implemented by QuickBooks
            $query[] = sprintf("WHERE %s", implode(' AND ', $where));
        }

        // Order
        if ($this->order) {
            $order = [];
            foreach ($this->order as $value) {
                $order[] = implode(' ', array_filter($value));
            }

            $query[] = sprintf("ORDERBY %s", implode(', ', $order));
        }

        // Offset
        if ($this->offset) {
            $query[] = sprintf("STARTPOSITION %s", $this->offset);
        }

        // Take
        if ($this->take) {
            $query[] = sprintf("MAXRESULTS %s", $this->take);
        }

        return implode(' ', $query);
    }
}
