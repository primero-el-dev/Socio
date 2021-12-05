<?php

namespace App\Doctrine\Function;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;

/**
 * "JSON_CONTAINS" "(" StringPrimary "," StringPrimary ")"
 */
class JsonContains extends FunctionNode
{
    // (1)
    public $jsonExpression = null;
    public $elementExpression = null;

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER); // (2)
        $parser->match(Lexer::T_OPEN_PARENTHESIS); // (3)
        $this->jsonExpression = $parser->StringPrimary(); // (4)
        $parser->match(Lexer::T_COMMA); // (5)
        $this->elementExpression = $parser->StringPrimary(); // (6)
        $parser->match(Lexer::T_CLOSE_PARENTHESIS); // (3)
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        return sprintf('JSON_CONTAINS(%s, %s)',
            $this->jsonExpression->dispatch($sqlWalker),
            $this->elementExpression->dispatch($sqlWalker)
        ); // (7)
    }
}
// {
// 	const FUNCTION_NAME = 'JSON_CONTAINS';
    
//     /** @var string[] */
//     protected $requiredArgumentTypes = [self::STRING_PRIMARY_ARG, self::STRING_PRIMARY_ARG];

//     /** @var string[] */
//     protected $optionalArgumentTypes = [self::STRING_PRIMARY_ARG];

//     /** @var bool */
//     protected $allowOptionalArgumentRepeat = false;
// }
