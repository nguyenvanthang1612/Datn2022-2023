<?php

namespace Magenest\TrackingOrder\Block;

use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Magento\Catalog\Model\ProductRepository;

class OrderStatus extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Magento\Framework\Registry
     */
    protected $ksRegistry;

    /**
     * @var AddressRenderer
     */
    protected $ksAddressRenderer;

    /**
     * @var ProductRepository
     */
    protected $ksProductRepository;

    /**
     * @param \Magento\Framework\App\Action\Context $ksContext
     * @param \Magento\Framework\Registry $ksRegistry
     * @param AddressRenderer $ksAddressRenderer
     * @param ProductRepository $ksProductRepository
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $ksContext,
        \Magento\Framework\Registry $ksRegistry,
        AddressRenderer $ksAddressRenderer,
        ProductRepository $ksProductRepository
    ) {
        $this->ksRegistry = $ksRegistry;
        $this->ksAddressRenderer = $ksAddressRenderer;
        $this->ksProductRepository = $ksProductRepository;
        parent::__construct($ksContext);
    }

    /**
     * Order Collection
     * @return String
     */
    public function getOrderData()
    {
        return $this->ksRegistry->registry('orderdata');
    }

    /**
     * Getch Product Object by product id
     * @return Object
     */
    public function getKsProductById($ksProductId)
    {
        try {
            $ksProduct = $this->ksProductRepository->getById($ksProductId);
        } catch (\Exception $e) {
            $ksProduct = false;
        }
        return $ksProduct;
    }

    /**
     * Get html of invoice totals block
     *
     * @param   \Magento\Sales\Model\Order\Invoice $ksInvoice
     * @return  string
     */
    public function getKsInvoiceTotalsHtml($ksInvoice)
    {
        $ksHtml = '';
        $ksTotals = $this->getChildBlock('ks_invoice_totals');
        if ($ksTotals) {
            $ksTotals->setInvoice($ksInvoice);
            $ksTotals->setOrder($ksInvoice->getOrder());
            $ksHtml = $ksTotals->toHtml();
        }
        return $ksHtml;
    }

    /**
     * Get creditmemo totals block html
     *
     * @param   \Magento\Sales\Model\Order\Creditmemo $ksCreditmemo
     * @return  string
     */
    public function getKsCreditMemoHtml($ksCreditmemo)
    {
        $ksTotals = $this->getChildBlock('ks_creditmemo_totals');
        $ksHtml = '';
        if ($ksTotals) {
            $ksTotals->setCreditmemo($ksCreditmemo);
            $ksTotals->setOrder($ksCreditmemo->getOrder());
            $ksHtml = $ksTotals->toHtml();
        }
        return $ksHtml;
    }

    /**
     * Returns string with formatted address
     *
     * @param Address $address
     * @return null|string
     */
    public function getKsFormattedAddress(Address $ksAddress)
    {
        return $this->ksAddressRenderer->format($ksAddress, 'html');
    }

    /**
     * bundle products
     * @return  string
     */
    public function getKsProductOptionBlock($ksItem)
    {
        $ksResult = "";
        if ($ksOptions = $this->getKsSelectedOptions($ksItem)) {
            foreach ($ksOptions as $ksOption) :
                $ksResult .= '<b>'.$ksOption['label']."</b>  :  ".$ksOption['value']."<br>";
            endforeach;
        }

        return $ksResult;
    }

    /**
     * check the attribute of configurable attribute
     * @return  string
     */

    public function getKsSelectedOptions($ksItem)
    {
        $ksResult = [];
        $ksOptions = $ksItem->getProductOptions();
        if ($ksOptions) {
            if (isset($ksOptions['options'])) {
                $ksResult = array_merge($ksResult, $ksOptions['options']);
            }
            if (isset($ksOptions['additional_options'])) {
                $ksResult = array_merge($ksResult, $ksOptions['additional_options']);
            }
            if (isset($ksOptions['attributes_info'])) {
                $ksResult = array_merge($ksResult, $ksOptions['attributes_info']);
            }
        }
        return $ksResult;
    }

    /**
     * Format total value based on order currency
     *
     * @param   \Magento\Framework\DataObject $total
     * @return  string
     */
    public function formatValue($ksPrice)
    {
        return $this->getOrderData()->formatPrice($ksPrice);
    }

    /**
     * Return the total amount minus discount
     *
     * @param OrderItem|InvoiceItem|CreditmemoItem $item
     * @return mixed
     */
    public function getTotalAmount($ksItem)
    {
        $ksTotalAmount = $ksItem->getRowTotal()
            + $ksItem->getTaxAmount()
            + $ksItem->getDiscountTaxCompensationAmount()
            + $ksItem->getWeeeTaxAppliedRowAmount()
            - $ksItem->getDiscountAmount();

        return $ksTotalAmount;
    }
}
