<?php

include_once 'MDXQuery.php';

$expression = new MDXExpression();

/** Простой MDX запрос, состоящий из выражения меры и измерения */

$query = (new MDXQuery())
    ->from('Sales')
    ->select('COLUMNS', '[Measures].[Amount]')
    ->select('ROWS', '[Product].[Product].[Name]');

echo PHP_EOL . $query;

/** Применили кортеж */

$query->select('ROWS', $expression->tuple('[Product].[Product].[Name].&[Носок]', '[Product].[Product].[Name].&[Валенок]'));

echo PHP_EOL . $query;

/** Применили подзапрос 1 */

$subQuery = (new MDXQuery())
    ->from('Sales')
    ->select('COLUMNS', '[Date].[Date].[Month].&[202101]');

$query->from($subQuery);

echo PHP_EOL . $query;

/** Применили подзапрос 2 */

$subQuery
    ->from((new MDXQuery())
        ->from('Sales')
        ->select('COLUMNS', '[Product].[Category].[Id].&[10]')
    );

/** Применили набор */

$query = (new MDXQuery())
    ->from('Sales')
    ->select('COLUMNS', '[Measures].[Amount]')
    ->select('ROWS', $expression->set('[Product].[Product].[Name]', '[Date].[Year]'));

echo PHP_EOL . $query;

/** Применили еще набор */

$query->select('COLUMNS', $expression->set('[Measures].[Amount]', '[Measures].[Rest]'));

echo PHP_EOL . $query;

/** Применили WITH */

$query = (new MDXQuery())
    ->with('SET MySetName AS {[Measures].[Amount], [Measures].[Rest]}')
    ->from('Sales')
    ->select('COLUMNS', 'MySetName')
    ->select('ROWS', '[Product].[Product].[Name]');

echo PHP_EOL . $query;

/** Применили Range */

$subQuery = (new MDXQuery())
    ->from('Sales')
    ->select('COLUMNS', $expression->range('[Date].[Date].[Month].&[202101]', '[Date].[Date].[Month].&[202112]'));

$query->from($subQuery);

echo PHP_EOL . $query;

/** Применили NoneEmpty */

$query->select('ROWS', $expression->noneEmpty('[Product].[Product].[Name]', 'MySetName'));

echo PHP_EOL . $query;

/** Добавили в колонки ещё измерение */

$query->select('COLUMNS', $expression->set('[Address].[Town]', 'MySetName'));

echo PHP_EOL . $query;

/** Применили CrossJoin в колонках */

$query->select('COLUMNS', $expression->crossJoin('[Address].[Town]', 'MySetName'));

echo PHP_EOL . $query;

/** Применили CrossJoin и NoneEmpty в колонках */

$query->select(
    'COLUMNS',
    $expression->crossJoin($expression->noneEmpty('[Address].[Town]', 'MySetName'), 'MySetName')
);

echo PHP_EOL . $query;

/** Добавили ещё по измерению в колонки и строки */

$query->select(
    'COLUMNS',
    $expression->crossJoin(
        $expression->noneEmpty('[Address].[Town]', $expression->noneEmpty('[Branch].[Name]', 'MySetName')),
        'MySetName'
    )
)->select(
    'ROWS',
    $expression->noneEmpty(
        $expression->crossJoin(
            '[Product].[Product].[Name]',
            $expression->noneEmpty('[Date].[Date].[Day]', 'MySetName')
        ),
        'MySetName'
    )
);

echo PHP_EOL . $query;

/** Добавили ещё измерение в колонки */

$query->select(
    'COLUMNS',
    $expression->crossJoin(
        $expression->noneEmpty(
            $expression->crossJoin(
                '[Address].[Town]',
                $expression->noneEmpty(
                    $expression->crossJoin('[Branch].[Name]', $expression->noneEmpty('[SaleType].[Name]', 'MySetName')),
                    'MySetName'
                )
            ),
            'MySetName'
        ), 'MySetName')
);

echo PHP_EOL . $query;

/** Добавили ещё несколько наборов с CrossJoin в подзапрос */

$subQuery = (new MDXQuery())
    ->from((new MDXQuery())
        ->from('Sales')
        ->select('COLUMNS', $expression->range('[Date].[Date].[Month].&[202101]', '[Date].[Date].[Month].&[202112]'))
    )
    ->select('COLUMNS', $expression->crossJoin(
        $expression->set('[Product].[Product].&[134947954]', '[Product].[Product].&[134947981]', '[Product].[Product].&[11145970]', '[Product].[Product].&[101362503]'),
        $expression->set('[Address].[Region].&[77]', '[Address].[Region].&[54]'),
        $expression->set('[Branch].[Name].&[6332]', '[Branch].[Name].&[295]')
    ));

$query->from($subQuery);

echo PHP_EOL . $query;
