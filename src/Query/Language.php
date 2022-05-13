<?php

namespace Pano\Query;

use Pano\Query\Context\StartOfInput;

class Language
{
    protected static array $operators = [
        '>=', '<=', ':', '<', '>',
    ];

    protected array $booleans = [
        'and', 'or', 'not',
    ];

    protected array $contexts = [
        // stage  => expected
        'start_of_input' => ['(', 'NOT', 'end_of_input', 'field_name', 'value', 'whitespace'],

        // start of query
        'field_or_value' => [':', '<', '<=', '>', '>=', 'AND', 'OR', 'end_of_input', 'whitespace'], // if operator ->is field

        'value' => ['AND', 'OR', 'end of input', 'whitespace'],

        'not' => ['(', 'field_name', 'value'],
        'and_or' => ['(', 'value', 'field_name'],

        'operator' => ['(', '{', 'value', 'whitespace'],

        // inside parentheses
        'in_bool' => [')', 'AND', 'OR', 'whitespace'], // error when end of input

        'start_of_bool' => ['(', 'NOT', 'field_name', 'value', 'whitespace'], // error when end of bool
    ];

    public static function parse(string $query)
    {
        // Expected "(", NOT, end of input, field name, value, whitespace but ":" found. :test ^

        // Expected "(", NOT, value, whitespace but ")" found. network :()awin or adtraction) and advertisements_count>150 ----------^

        // Expected ":", "<", "<=", ">", ">=", AND, OR, end of input, whitespace but ")" found. network :awin or adtraction) and advertisements_count>150

        $context = new StartOfInput();

        $context->parse($query);

        $i = strlen($query);

        for ($i = 0; $i < $length; ++$i) {
            $before = substr($query, 0, $i);
            $after = substr($query, $i);

            if (in_array($query[$i], ['\'', '"'])) {
                if (false === $insideBrackets) {
                    $insideBrackets = $query[$i];
                } elseif (false !== $insideBrackets && $query[$i] == $insideBrackets) { // end of bracket
                    $insideBrackets = false;
                    $parts[] = substr($before, $lastIndex, $i);
                    $lastIndex = $i;
                }
            }

            if (false !== $insideBrackets) {
                continue;
            }

            foreach (static::$operators as $operator) {
                if (str_starts_with($after, $operator)) {
                    $parts[] = substr($query, $lastIndex, $i);
                    $parts[] = $operator;
                    $lastIndex = $i + strlen($operator);
                    //$before is a field (possibly with NOT operator)
                }
            }
        }
        dd($parts);

        return $splitQuery;
    }

    /**
     * Get type suggestions.
     */
    public function suggest(Resource $resource, string $query): array
    {
        $query = $this->parse($query);

        collect($resource->fields)
            ->filter(fn ($field) => str_starts_with($field->name, $query->last))
        ;
    }
}
