<?php

class MDXExpression
{
    public function tuple(string ...$expression): string
    {
        return sprintf(count($expression) > 1 ? '(%s)' : '%s', implode(', ', $expression));
    }

    public function set(string ...$expression): string
    {
        return sprintf(count($expression) > 1 ? '{%s}' : '%s', implode(', ', $expression));
    }

    public function range(string $start, string $end): string
    {
        return sprintf('{%s:%s}', $start, $end);
    }

    public function noneEmpty(string $expression1, string $expression2): string
    {
        return "NONEMPTY($expression1, $expression2)";
    }

    public function crossJoin(string ...$expression): string
    {
        return implode(' * ', $expression);
    }
}
