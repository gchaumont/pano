<?php

namespace Pano\Query;

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
}
