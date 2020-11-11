<?php

namespace Oc\Menu\Renderer;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\Renderer\Renderer;
use Knp\Menu\Renderer\RendererInterface;

/**
 * Class MainRenderer
 */
class MainRenderer extends Renderer implements RendererInterface
{
    /**
     * @var MatcherInterface
     */
    protected $matcher;

    /**
     * @var array
     */
    protected $defaultOptions;

    /**
     * @param string $charset
     */
    public function __construct(MatcherInterface $matcher, array $defaultOptions = array(), $charset = null)
    {
        $this->matcher = $matcher;
        $this->defaultOptions = array_merge(array(
            'depth' => null,
            'matchingDepth' => null,
            'currentAsLink' => true,
            'currentClass' => 'navigation-item--current',
            'ancestorClass' => null,
            'listClass' => 'navigation',
            'listLevelPrefixClass' => 'navigation--level-',
            'itemClass' => 'navigation__item navigation-item',
            'textClass' => 'navigation-item__text',
            'linkClass' => 'navigation-item__link',
            'firstClass' => 'navigation-item--first',
            'lastClass' => 'navigation-item--last',
            'compressed' => false,
            'allow_safe_labels' => false,
            'clear_matcher' => true,
            'leaf_class' => null,
            'branch_class' => null,
        ), $defaultOptions);

        parent::__construct($charset);
    }

    public function render(ItemInterface $item, array $options = array()): string
    {
        $options = array_merge($this->defaultOptions, $options);

        $childrenClass = (array) $item->getChildrenAttribute('class');

        if ($options['listClass']) {
            $childrenClass[] = $options['listClass'];
        }

        if ($options['listLevelPrefixClass']) {
            $childrenClass[] = $options['listLevelPrefixClass'] . $item->getLevel();
        }

        $childrenAttributes = $item->getChildrenAttributes();
        $childrenAttributes['class'] = implode(' ', $childrenClass);

        $html = $this->renderList($item, $childrenAttributes, $options);

        if ($options['clear_matcher']) {
            $this->matcher->clear();
        }

        return $html;
    }

    protected function renderList(ItemInterface $item, array $attributes, array $options): string
    {
        /**
         * Return an empty string if any of the following are true:
         *   a) The menu has no children eligible to be displayed
         *   b) The depth is 0
         *   c) This menu item has been explicitly set to hide its children
         */
        if (!$item->hasChildren() || 0 === $options['depth'] || !$item->getDisplayChildren()) {
            return '';
        }

        $html = $this->format('<ul' . $this->renderHtmlAttributes($attributes) . '>', 'ul', $item->getLevel(), $options);
        $html .= $this->renderChildren($item, $options);
        $html .= $this->format('</ul>', 'ul', $item->getLevel(), $options);

        return $html;
    }

    /**
     * Renders all of the children of this menu.
     *
     * This calls ->renderItem() on each menu item, which instructs each
     * menu item to render themselves as an <li> tag (with nested ul if it
     * has children).
     * This method updates the depth for the children.
     */
    protected function renderChildren(ItemInterface $item, array $options): string
    {
        // render children with a depth - 1
        if (null !== $options['depth']) {
            $options['depth'] = $options['depth'] - 1;
        }

        if (null !== $options['matchingDepth'] && $options['matchingDepth'] > 0) {
            $options['matchingDepth'] = $options['matchingDepth'] - 1;
        }

        $html = '';
        foreach ($item->getChildren() as $child) {
            $html .= $this->renderItem($child, $options);
        }

        return $html;
    }

    /**
     * Called by the parent menu item to render this menu.
     *
     * This renders the li tag to fit into the parent ul as well as its
     * own nested ul tag if this menu item has children
     */
    protected function renderItem(ItemInterface $item, array $options): string
    {
        // if we don't have access or this item is marked to not be shown
        if (!$item->isDisplayed()) {
            return '';
        }

        // create an array than can be imploded as a class list
        $class = (array) $item->getAttribute('class');

        if ($options['itemClass']) {
            $class[] = $options['itemClass'];
        }

        if ($this->matcher->isCurrent($item)) {
            $class[] = $options['currentClass'];
        } elseif ($this->matcher->isAncestor($item, $options['matchingDepth'])) {
            $class[] = $options['ancestorClass'];
        }

        if ($item->actsLikeFirst()) {
            $class[] = $options['firstClass'];
        }
        if ($item->actsLikeLast()) {
            $class[] = $options['lastClass'];
        }

        if ($item->hasChildren() && $options['depth'] !== 0) {
            if (null !== $options['branch_class'] && $item->getDisplayChildren()) {
                $class[] = $options['branch_class'];
            }
        } elseif (null !== $options['leaf_class']) {
            $class[] = $options['leaf_class'];
        }

        // retrieve the attributes and put the final class string back on it
        $attributes = $item->getAttributes();
        if (!empty($class)) {
            $attributes['class'] = implode(' ', $class);
        }

        // opening li tag
        $html = $this->format('<li' . $this->renderHtmlAttributes($attributes) . '>', 'li', $item->getLevel(), $options);

        // render the text/link inside the li tag
        //$html .= $this->format($item->getUri() ? $item->renderLink() : $item->renderLabel(), 'link', $item->getLevel());
        $html .= $this->renderLink($item, $options);

        // renders the embedded ul
        $childrenClass = (array) $item->getChildrenAttribute('class');
        if ($options['listClass']) {
            $childrenClass[] = $options['listClass'];
        }

        if ($options['listLevelPrefixClass']) {
            $childrenClass[] = $options['listLevelPrefixClass'] . $item->getLevel();
        }

        $childrenAttributes = $item->getChildrenAttributes();
        $childrenAttributes['class'] = implode(' ', $childrenClass);

        $html .= $this->renderList($item, $childrenAttributes, $options);

        // closing li tag
        $html .= $this->format('</li>', 'li', $item->getLevel(), $options);

        return $html;
    }

    /**
     * Renders the link in a a tag with link attributes or
     * the label in a span tag with label attributes
     *
     * Tests if item has a an uri and if not tests if it's
     * the current item and if the text has to be rendered
     * as a link or not.
     */
    protected function renderLink(ItemInterface $item, array $options = array()): string
    {
        if ($item->getUri() && (!$item->isCurrent() || $options['currentAsLink'])) {
            $text = $this->renderLinkElement($item, $options);
        } else {
            $text = $this->renderSpanElement($item, $options);
        }

        return $this->format($text, 'link', $item->getLevel(), $options);
    }

    protected function renderLinkElement(ItemInterface $item, array $options): string
    {
        $childrenClass = (array) $item->getChildrenAttribute('class');

        if ($options['linkClass']) {
            $childrenClass[] = $options['linkClass'];
        }

        $childrenAttributes = $item->getChildrenAttributes();
        $childrenAttributes['class'] = implode(' ', $childrenClass);

        return sprintf('<a href="%s"%s>%s</a>', $this->escape($item->getUri()), $this->renderHtmlAttributes($childrenAttributes), $this->renderLabel($item, $options));
    }

    protected function renderSpanElement(ItemInterface $item, array $options): string
    {
        $childrenClass = (array) $item->getChildrenAttribute('class');

        if ($options['textClass']) {
            $childrenClass[] = $options['textClass'];
        }

        $childrenAttributes = $item->getChildrenAttributes();
        $childrenAttributes['class'] = implode(' ', $childrenClass);

        return sprintf('<span%s>%s</span>', $this->renderHtmlAttributes($childrenAttributes), $this->renderLabel($item, $options));
    }

    protected function renderLabel(ItemInterface $item, array $options): string
    {
        if ($options['allow_safe_labels'] && $item->getExtra('safe_label', false)) {
            return $item->getLabel();
        }

        return $this->escape($item->getLabel());
    }

    /**
     * If $this->renderCompressed is on, this will apply the necessary
     * spacing and line-breaking so that the particular thing being rendered
     * makes up its part in a fully-rendered and spaced menu.
     *
     * @param string  $html    The html to render in an (un)formatted way
     * @param string  $type    The type [ul,link,li] of thing being rendered
     * @param int $level
     */
    protected function format($html, $type, $level, array $options): string
    {
        if ($options['compressed']) {
            return $html;
        }

        switch ($type) {
            case 'ul':
            case 'link':
                $spacing = $level * 4;
                break;

            case 'li':
                $spacing = $level * 4 - 2;
                break;
        }

        return str_repeat(' ', $spacing) . $html . "\n";
    }
}
