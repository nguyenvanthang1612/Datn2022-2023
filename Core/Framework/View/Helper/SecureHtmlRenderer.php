<?php

namespace Magenest\Core\Framework\View\Helper;

use Magento\Framework\Math\Random;
use Magento\Framework\View\Helper\SecureHtmlRender\HtmlRenderer;
use Magento\Framework\View\Helper\SecureHtmlRender\SecurityProcessorInterface;

class SecureHtmlRenderer extends \Magento\Framework\View\Helper\SecureHtmlRenderer
{
    /**
     * @var HtmlRenderer
     */
    private $renderer;

    /**
     * @var SecurityProcessorInterface[]
     */
    private $processors;

    /**
     * @var Random
     */
    private $random;

    /**
     * @param HtmlRenderer $renderer
     * @param Random $random
     * @param SecurityProcessorInterface[] $processors
     */
    public function __construct(HtmlRenderer $renderer, Random $random, array $processors = [])
    {
        $this->renderer = $renderer;
        $this->random = $random;
        $this->processors = $processors;
        parent::__construct($renderer, $random, $processors);
    }

    /**
     * Render event listener script as a separate tag instead of an attribute.
     *
     * @param string $eventName Full event name like "onclick".
     * @param string $attributeJavascript JS that would've gone to an HTML attribute.
     * @param string $elementSelector CSS selector for the element we handle the event for.
     * @return string Result script tag.
     */
    public function renderEventListenerAsTag(
        string $eventName,
        string $attributeJavascript,
        string $elementSelector
    ): string {
        if (!$eventName || !$attributeJavascript || !$elementSelector || mb_strpos($eventName, 'on') !== 0) {
            throw new \InvalidArgumentException('Invalid JS event handler data provided');
        }

        $random = $this->random->getRandomString(10);
        $listenerFunction = 'eventListener' . $random;
        $elementName = 'listenedElement' . $random;
        $script = <<<script
            function {$listenerFunction} () {
                {$attributeJavascript};
            }
            var {$elementName}Array = document.querySelectorAll("{$elementSelector}");
            if({$elementName}Array.length !== 'undefined'){
                {$elementName}Array.forEach(function(element) {
                    if (element) {
                        element.{$eventName} = function (event) {
                            var targetElement = element;
                            if (event && event.target) {
                                targetElement = event.target;
                            }
                            {$listenerFunction}.apply(targetElement);
                        };
                    }
                });
            }
        script;

        return $this->renderTag('script', ['type' => 'text/javascript'], $script, false);
    }
}
