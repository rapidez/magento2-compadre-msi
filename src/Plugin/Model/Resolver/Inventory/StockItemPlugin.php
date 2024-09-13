<?php

namespace Rapidez\CompadreMsi\Plugin\Model\Resolver\Inventory;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\InventorySales\Model\GetProductSalableQty;
use Magento\InventorySales\Model\ResourceModel\GetAssignedStockIdForWebsite;
use Magento\InventorySalesApi\Api\AreProductsSalableInterface;
use Rapidez\Compadre\Model\Config;
use Rapidez\Compadre\Model\Resolver\Inventory\StockItem;
use Magento\Catalog\Model\Product;
use Magento\InventorySales\Model\IsProductSalableResult;

class StockItemPlugin
{
    public function __construct(
        private GetAssignedStockIdForWebsite $getAssignedStockIdForWebsite,
        private GetProductSalableQty $getProductSalableQty,
        private AreProductsSalableInterface $areProductSalable,
        private Config $config,
    ) {}

    public function aroundResolve(StockItem $subject, callable $proceed, Field $field, $context, ResolveInfo $info, array $value = null, array $args = null): array
    {
        $stockItem = $proceed($field, $context, $info, $value, $args);
        
        /** @var Product $product */
        $product = $value['model'];
        
        $websiteCode = $context->getExtensionAttributes()->getStore()->getWebsite()->getCode();
        $scopeId = $this->getAssignedStockIdForWebsite->execute($websiteCode);
        if ($this->config->isFieldExposed('qty')) {
            $stockItem['qty'] = $this->getProductSalableQty->execute($product->getSku(), $scopeId);
        }
        
        if ($this->config->isFieldExposed('in_stock')) {
            $qty = $this->areProductSalable->execute([$product->getSku()], $scopeId);
            /** @var IsProductSalableResult $result */
            $result = array_shift($qty);
            $stockItem['in_stock'] = $result->isSalable();
        }
        
        return $stockItem;
    }
}