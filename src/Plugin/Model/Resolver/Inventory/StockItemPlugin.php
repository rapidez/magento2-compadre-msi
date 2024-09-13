<?php

namespace Rapidez\CompadreMsi\Plugin\Model\Resolver\Inventory;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\InventorySales\Model\GetProductSalableQty;
use Magento\InventorySales\Model\ResourceModel\GetAssignedStockIdForWebsite;
use Magento\InventorySalesApi\Api\AreProductsSalableInterface;
use Rapidez\Compadre\Model\Config;
use Rapidez\Compadre\Model\Resolver\Inventory\StockItem;

class StockItemPlugin
{
        public function __construct(
        private GetAssignedStockIdForWebsite $getAssignedStockIdForWebsite,
        private GetProductSalableQty $getProductSalableQty,
        private AreProductsSalableInterface $areProductSalable,
        private Config $config,
    ) {}

    public function aroundResolve(StockItem $subject, callable $proceed, Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $stockItem = $proceed($field, $context, $info, $value, $args);

        /** @var ProductInterface $product */
        $product = $value['model'];
        
        $websiteCode = $context->getExtensionAttributes()->getStore()->getWebsite()->getCode();
        $scopeId = $this->getAssignedStockIdForWebsite->execute($websiteCode);
        if ($this->config->isFieldExposed('qty')) {
            $stockItem['qty'] = $this->getProductSalableQty->execute($product->getSku(), $scopeId);
        }
        
        if ($this->config->isFieldExposed('in_stock')) {
            $qty = $this->areProductSalable->execute([$product->getSku()], $scopeId);
            $stockItem['in_stock'] = array_shift($qty)->isSalable;
        }
        
        return $stockItem;
    }
}