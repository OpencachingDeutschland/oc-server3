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
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function setBaseTemplateClass($class)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function enableDebug()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function disableDebug()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function isDebug()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function enableAutoReload()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function disableAutoReload()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function isAutoReload()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function enableStrictVariables()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function disableStrictVariables()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function isStrictVariables()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getCache($original = true)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function setCache($cache)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getCacheFilename($name)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getTemplateClass($name, $index = null)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getTemplateClassPrefix()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function render($name, array $context = [])
    {
        return implode('', $context);
    }

    public function display($name, array $context = [])
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function load($name)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function loadTemplate($name, $index = null)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function createTemplate($template)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function isTemplateFresh($name, $time)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function resolveTemplate($names)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function clearTemplateCache()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function clearCacheFiles()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getLexer()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function setLexer(\Twig_Lexer $lexer)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function tokenize($source, $name = null)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getParser()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function setParser(\Twig_Parser $parser)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function parse(Twig_TokenStream $stream)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getCompiler()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function setCompiler(\Twig_Compiler $compiler)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function compile(\Twig_Node $node)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function compileSource($source, $name = null)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function setLoader(Twig_LoaderInterface $loader)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getLoader()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function setCharset($charset)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getCharset()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function initRuntime()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function hasExtension($class)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function addRuntimeLoader(Twig_RuntimeLoaderInterface $loader)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getExtension($class)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getRuntime($class)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function addExtension(Twig_ExtensionInterface $extension)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function removeExtension($name)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function setExtensions(array $extensions)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getExtensions()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function addTokenParser(Twig_TokenParserInterface $parser)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getTokenParsers()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getTags()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function addNodeVisitor(Twig_NodeVisitorInterface $visitor)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getNodeVisitors()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function addFilter($name, $filter = null)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getFilter($name)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function registerUndefinedFilterCallback($callable)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getFilters()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function addTest($name, $test = null)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getTests()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getTest($name)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function addFunction($name, $function = null)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getFunction($name)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function registerUndefinedFunctionCallback($callable)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getFunctions()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function addGlobal($name, $value)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getGlobals()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function mergeGlobals(array $context)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getUnaryOperators()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function getBinaryOperators()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    public function computeAlternatives($name, $items)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    protected function initGlobals()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    protected function initExtensions()
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    protected function initExtension(Twig_ExtensionInterface $extension)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }

    protected function writeCacheFile($file, $content)
    {
        throw new NotImplementedException('this mehtod is not implemented in ' . __CLASS__ . ' class');
    }
}
