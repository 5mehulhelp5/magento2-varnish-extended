<?php

namespace Elgentos\VarnishExtended\Model\MarketingParameters\Events;

use Magento\Framework\Event\Observer;

class SystemConfigSave extends EventAbstract
{
    public function execute(Observer $observer)
    {
        $configData = $observer->getEvent()->getData('configData', 'groups');
        if (! isset($configData['full_page_cache']['groups']['varnish']['fields']['tracking_parameters']['value'])) {
            return;
        }

        $trackingParameters = array_column(
            $configData['full_page_cache']['groups']['varnish']['fields']['tracking_parameters']['value'],
            'param'
        );

        if (count($trackingParameters) === 1 && isset($trackingParameters['__empty'])) {
            return;
        }

        unset($trackingParameters[array_search('__empty', $trackingParameters)]);

        $this->validateParams($trackingParameters);
    }
}