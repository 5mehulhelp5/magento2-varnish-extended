<?php

declare(strict_types=1);

namespace Elgentos\VarnishExtended\Model\MarketingParameters\Events;

use Magento\Framework\Event\Observer;

class CatalogEavAttributeBeforeSave extends EventAbstract
{

    public function execute(Observer $observer): void
    {
        $attribute = $observer->getEvent()->getAttribute();
        $this->validateAttribute($attribute);
    }
}
