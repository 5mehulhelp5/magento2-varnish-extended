<?php

namespace Elgentos\VarnishExtended\Model\MarketingParameters\Events;

use Magento\Framework\Event\Observer;

class CatalogEavAttributeBeforeSave extends EventAbstract
{

    public function execute(Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        $this->validateAttribute($attribute);
    }
}