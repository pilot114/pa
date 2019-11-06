<?php

namespace Component;

use PhpParser\ParserFactory;
use PhpParser\Error;

use Component\Parser\MyTraverser;

/**
 * TODO отображать прогресс
 */
class Parser
{
    private $finder;
    private $parser;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP5);
    }

    public function getListTokens($name)
    {
        $traverser = new MyTraverser();
        $traverser->addVisitor(new $name());

        $commonBuffer = [];

        foreach ($this->finder->files()->name('*.php') as $file) {
            $source = $file->getContents();
            try {
                $stms = $this->parser->parse($source);
            } catch (Error $e) {
                // файлы с ошибками парсинга - скорее всего просто нерабочий код
                // echo sprintf("Parse Error: %s %s \n", $file->getBasename(), $e->getMessage());
            }
            $traverser->traverse($stms);
            $visitor = $traverser->getVisitors()[0];
            foreach($visitor->getBuffer() as $string) {
                $commonBuffer[] = $string;
            }
        }
        return $commonBuffer;
    }
}
