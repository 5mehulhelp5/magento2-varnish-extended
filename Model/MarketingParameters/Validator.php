<?php

declare(strict_types=1);

namespace Elgentos\VarnishExtended\Model\MarketingParameters;

use Elgentos\VarnishExtended\Model\Config;
use Magento\Catalog\Api\Data\EavAttributeInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Validator
{
    const SEARCH_PAGE_SIZE = 50;

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly ProductAttributeRepositoryInterface $productAttributeRepository,
        private readonly SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
    ) {}

    public function isValid(?array $trackingParams = null): bool
    {
        $trackingParams = $trackingParams ?: $this->getTrackingParams();

        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();

        $attributes = $this->productAttributeRepository
            ->getList($searchCriteriaBuilder->create())
            ->getItems();

        foreach ($attributes as $attribute) {
            if ($this->attributeMatchesTrackingParam($attribute, $trackingParams)) {
                return false;
            }
        }

        return true;
    }

    public function getTrackingParams(): array
    {
        $trackingParameters = $this->scopeConfig->getValue(Config::XML_PATH_VARNISH_TRACKING_PARAMETERS);
        if (! is_array($trackingParameters)) {
            $trackingParameters = json_decode($trackingParameters, true);
        }

        return array_column($trackingParameters, 'param');
    }

    public function attributeMatchesTrackingParam(
        EavAttributeInterface $attribute,
        array $trackingParams
    ): bool {
        if (
            ! $attribute->getIsFilterable()
            && ! $attribute->getIsFilterableInGrid()
            && ! $attribute->getIsFilterableInSearch()
            && ! $attribute->getIsComparable()
            && ! $attribute->getIsSearchable()
        ) {
            return false;
        }

        return in_array(
            $attribute->getAttributeCode(),
            $trackingParams,
            true
        );
    }
}
