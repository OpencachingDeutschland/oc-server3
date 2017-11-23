<?php

namespace OcTest\Utils;

use Symfony\Component\Intl\Exception\NotImplementedException;
use Twig_CompilerInterface;
use Twig_ExtensionInterface;
use Twig_LexerInterface;
use Twig_LoaderInterface;
use Twig_NodeInterface;
use Twig_NodeVisitorInterface;
use Twig_ParserInterface;
use Twig_RuntimeLoaderInterface;
use Twig_TokenParserInterface;
use Twig_TokenStream;

class TwigDummy extends \Twig_Environment
{
    public function __construct()
    {
    }

    public function getBaseTemplateClass()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function setBaseTemplateClass($class)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function enableDebug()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function disableDebug()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function isDebug()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function enableAutoReload()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function disableAutoReload()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function isAutoReload()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function enableStrictVariables()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function disableStrictVariables()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function isStrictVariables()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getCache($original = true)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function setCache($cache)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getCacheFilename($name)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getTemplateClass($name, $index = null)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getTemplateClassPrefix()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function render($name, array $context = [])
    {
        return implode('', $context);
    }

    public function display($name, array $context = [])
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function load($name)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function loadTemplate($name, $index = null)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function createTemplate($template)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function isTemplateFresh($name, $time)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function resolveTemplate($names)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function clearTemplateCache()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function clearCacheFiles()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getLexer()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function setLexer(Twig_LexerInterface $lexer)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function tokenize($source, $name = null)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getParser()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function setParser(Twig_ParserInterface $parser)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function parse(Twig_TokenStream $stream)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getCompiler()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function setCompiler(Twig_CompilerInterface $compiler)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function compile(Twig_NodeInterface $node)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function compileSource($source, $name = null)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function setLoader(Twig_LoaderInterface $loader)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getLoader()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function setCharset($charset)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getCharset()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function initRuntime()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function hasExtension($class)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function addRuntimeLoader(Twig_RuntimeLoaderInterface $loader)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getExtension($class)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getRuntime($class)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function addExtension(Twig_ExtensionInterface $extension)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function removeExtension($name)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function setExtensions(array $extensions)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getExtensions()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function addTokenParser(Twig_TokenParserInterface $parser)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getTokenParsers()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getTags()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function addNodeVisitor(Twig_NodeVisitorInterface $visitor)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getNodeVisitors()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function addFilter($name, $filter = null)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getFilter($name)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function registerUndefinedFilterCallback($callable)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getFilters()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function addTest($name, $test = null)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getTests()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getTest($name)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function addFunction($name, $function = null)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getFunction($name)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function registerUndefinedFunctionCallback($callable)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getFunctions()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function addGlobal($name, $value)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getGlobals()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function mergeGlobals(array $context)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getUnaryOperators()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function getBinaryOperators()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    public function computeAlternatives($name, $items)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    protected function initGlobals()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    protected function initExtensions()
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    protected function initExtension(Twig_ExtensionInterface $extension)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }

    protected function writeCacheFile($file, $content)
    {
        throw new NotImplementedException('this mehtod is not implemented in TwigDummy class');
    }
}
