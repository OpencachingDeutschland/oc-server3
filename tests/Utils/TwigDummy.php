<?php

namespace OcTest\Utils;

use Symfony\Component\Intl\Exception\NotImplementedException;
use Twig_ExtensionInterface;
use Twig_LoaderInterface;
use Twig_NodeVisitorInterface;
use Twig_RuntimeLoaderInterface;
use Twig_TokenParserInterface;
use Twig_TokenStream;

class TwigDummy extends \Twig_Environment
{
    public function __construct()
    {
    }

    public function getBaseTemplateClass(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function setBaseTemplateClass($class): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function enableDebug(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function disableDebug(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function isDebug(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function enableAutoReload(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function disableAutoReload(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function isAutoReload(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function enableStrictVariables(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function disableStrictVariables(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function isStrictVariables(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getCache($original = true): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function setCache($cache): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getCacheFilename($name): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getTemplateClass($name, $index = null): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getTemplateClassPrefix(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function render($name, array $context = [])
    {
        return implode('', $context);
    }

    public function display($name, array $context = []): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function load($name): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function loadTemplate($name, $index = null): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function createTemplate($template): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function isTemplateFresh($name, $time): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function resolveTemplate($names): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function clearTemplateCache(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function clearCacheFiles(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getLexer(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function setLexer(\Twig_Lexer $lexer): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function tokenize($source, $name = null): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getParser(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function setParser(\Twig_Parser $parser): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function parse(Twig_TokenStream $stream): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getCompiler(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function setCompiler(\Twig_Compiler $compiler): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function compile(\Twig_Node $node): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function compileSource($source, $name = null): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function setLoader(Twig_LoaderInterface $loader): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getLoader(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function setCharset($charset): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getCharset(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function initRuntime(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function hasExtension($class): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function addRuntimeLoader(Twig_RuntimeLoaderInterface $loader): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getExtension($class): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getRuntime($class): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function addExtension(Twig_ExtensionInterface $extension): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function removeExtension($name): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function setExtensions(array $extensions): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getExtensions(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function addTokenParser(Twig_TokenParserInterface $parser): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getTokenParsers(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getTags(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function addNodeVisitor(Twig_NodeVisitorInterface $visitor): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getNodeVisitors(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function addFilter($name, $filter = null): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getFilter($name): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function registerUndefinedFilterCallback($callable): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getFilters(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function addTest($name, $test = null): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getTests(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getTest($name): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function addFunction($name, $function = null): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getFunction($name): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function registerUndefinedFunctionCallback($callable): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getFunctions(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function addGlobal($name, $value): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getGlobals(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function mergeGlobals(array $context): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getUnaryOperators(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getBinaryOperators(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function computeAlternatives($name, $items): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    protected function initGlobals(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    protected function initExtensions(): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    protected function initExtension(Twig_ExtensionInterface $extension): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    protected function writeCacheFile($file, $content): void
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }
}
