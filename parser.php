<?php

require './bootstrap.php';

use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class MyNodeVisitor extends NodeVisitorAbstract
{
    private $tokenTypes = [];

    private $vars = [];

    public function leaveNode(Node $node) {
      // interest types https://github.com/nikic/PHP-Parser/tree/master/test/code/parser
      /*
Stmt_Trait
Stmt_TraitUse
Stmt_Interface
Expr_Instanceof
Stmt_Namespace

Name_FullyQualified
Name

Stmt_Class
Stmt_ClassMethod

Expr_New

Stmt_Throw

Param
Arg

Expr_StaticCall
Expr_MethodCall
Expr_FuncCall

Expr_ClassConstFetch
Expr_PropertyFetch
Stmt_Property

Expr_Assign - присваивание
Expr_Variable - переменная

информативно
Expr_BinaryOp_Concat - конкатенации
Expr_BinaryOp_Equal / Expr_BinaryOp_Identical - точное и неточное сравнение
Expr_Array / Expr_ArrayItem / Expr_ArrayDimFetch - массивы
Stmt_Return
Scalar_String
      */

        $type = $node->getType();
        if (!isset($this->tokenTypes[$type])) {
          $this->tokenTypes[$type] = 0;
        }
        $this->tokenTypes[$type]++;

        if ($type === 'Expr_Variable') {
          if (!isset($this->vars[$node->name])) {
            $this->vars[$node->name] = 0;
          }
          $this->vars[$node->name]++;
        }
    }

    public function getTokenTypes()
    {
        return $this->tokenTypes;
    }

    public function getVars()
    {
        return $this->vars;
    }
}

$inDir  = '/home/oleg/sources/job/scripts/Job';

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
$traverser = new NodeTraverser;
$prettyPrinter = new PrettyPrinter\Standard;

$visitor = new MyNodeVisitor();
$traverser->addVisitor($visitor);

$files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($inDir));
$files = new \RegexIterator($files, '/\.php$/');

foreach ($files as $file) {
    try {
        $code = file_get_contents($file);
        $stmts = $parser->parse($code);
        $stmts = $traverser->traverse($stmts);
    } catch (PhpParser\Error $e) {
        echo 'Parse Error: ', $e->getMessage();
    }
}

$tokens = $visitor->getVars();
asort($tokens);
dd($tokens);