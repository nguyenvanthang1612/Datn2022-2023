<?php

namespace Magenest\TrackingOrder\Block\Adminhtml;

class Color extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param \Magento\Backend\Block\Template\Context $ksContext
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $ksContext,
        array $ksData = []
    ) {
        parent::__construct($ksContext, $ksData);
    }

    /**
     * color picker
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $ksElement)
    {
        $ksHtml = $ksElement->getElementHtml();
        $ksValue = $ksElement->getData('value');

        $ksHtml .= '<script type="text/javascript">
            require(["jquery","jquery/colorpicker/js/colorpicker"], function ($) {
                $(document).ready(function () {
                    var $el = $("#' . $ksElement->getHtmlId() . '");
                    $el.css("backgroundColor", "'. $ksValue .'");

                    // Attach the color picker
                    $el.ColorPicker({
                        color: "'. $ksValue .'",
                        onChange: function (hsb, hex, rgb) {
                            $el.css("backgroundColor", "#" + hex).val("#" + hex);
                        }
                    });
                });
            });
            </script>';
        return $ksHtml;
    }
}
