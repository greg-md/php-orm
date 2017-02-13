<?php

namespace Greg\Orm\Query;

interface InsertQueryStrategy extends QueryStrategy
{
    /**
     * @param string $table
     * @return $this
     */
    public function into(string $table);

    /**
     * @return bool
     */
    public function hasInto(): bool;

    /**
     * @return string
     */
    public function getInto(): string;

    /**
     * @return $this
     */
    public function clearInto();

    /**
     * @param array $columns
     * @return $this
     */
    public function columns(array $columns);

    /**
     * @return bool
     */
    public function hasColumns(): bool;

    /**
     * @return array
     */
    public function getColumns(): array;

    /**
     * @return $this
     */
    public function clearColumns();

    /**
     * @param array $values
     * @return $this
     */
    public function values(array $values);

    /**
     * @return bool
     */
    public function hasValues(): bool;

    /**
     * @return array
     */
    public function getValues(): array;

    /**
     * @return $this
     */
    public function clearValues();

    /**
     * @param array $data
     * @return $this
     */
    public function data(array $data);

    /**
     * @return $this
     */
    public function clearData();

    /**
     * @param SelectQueryStrategy $strategy
     * @return $this
     */
    public function select(SelectQueryStrategy $strategy);

    /**
     * @param string $sql
     * @param \string[] ...$params
     * @return $this
     */
    public function selectRaw(string $sql, string ...$params);

    /**
     * @return bool
     */
    public function hasSelect(): bool;

    /**
     * @return array
     */
    public function getSelect(): array;

    /**
     * @return $this
     */
    public function clearSelect();
}
