<?php

include_once 'MDXExpression.php';

class MDXQuery
{
    /** @var string */
    protected string $with = '';

    /** @var string */
    protected string $from;

    /** @var array */
    protected array $select;

    public function with(string $with): MDXQuery
    {
        $this->with = $with;

        return $this;
    }

    public function from(string|MDXQuery $item): MDXQuery
    {
        if ($item instanceof MDXQuery) {
            $this->from = "($item)";
        } elseif ($item) {
            $this->from = "[$item]";
        }

        return $this;
    }

    public function select(string $on, string $expression): MDXQuery
    {
        $this->select[$on] = $expression;

        return $this;
    }

    public function export(): string
    {
        return trim(implode(' ', [
            $this->with ? "WITH $this->with" : null,
            "SELECT {$this->exportSelect()}",
            "FROM $this->from",
        ]));
    }

    protected function exportSelect(): string
    {
        $items = [];

        foreach ($this->select as $on => $expression) {
            $items[] = "$expression ON $on";
        }

        return implode(', ', $items);
    }

    public function __toString(): string
    {
        return $this->export();
    }
}
