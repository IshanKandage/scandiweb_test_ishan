<?php
declare(strict_types=1);

namespace Test\Scandiweb_Test\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Attribute\Source\Visibility;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class CreateProductPatch implements DataPatchInterface, PatchRevertableInterface
{
    private $moduleDataSetup;
    private $productCollectionFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ProductCollectionFactory $productCollectionFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): void
    {
        $this->moduleDataSetup->startSetup();

        // Create a new simple product
        $product = new Product();
        $product->setTypeId(ProductType::TYPE_SIMPLE);
        $product->setAttributeSetId(4);
        $product->setName('Test Product');
        $product->setSku('test-product');
        $product->setPrice(10.99);
        $product->setStatus(Status::STATUS_ENABLED);
        $product->setVisibility(Visibility::VISIBILITY_BOTH);
        $product->setWebsiteIds([1]);
        $product->setStockData([
            'qty' => 100,
            'is_in_stock' => 1
        ]);
        $product->save();

        $this->moduleDataSetup->endSetup();
    }

    public function revert(): void
    {
        $this->moduleDataSetup->startSetup();

        // Find and delete the product
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToFilter('sku', 'test-product');
        foreach ($productCollection as $product) {
            $product->delete();
        }

        $this->moduleDataSetup->endSetup();
    }
}
