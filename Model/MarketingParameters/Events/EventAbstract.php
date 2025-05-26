<?php

namespace Elgentos\VarnishExtended\Model\MarketingParameters\Events;

use Elgentos\VarnishExtended\Model\MarketingParameters\Notification;
use Elgentos\VarnishExtended\Model\MarketingParameters\Validator;
use Magento\Catalog\Api\Data\EavAttributeInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\FlagManager;

abstract class EventAbstract implements ObserverInterface
{
    public function __construct(
        private readonly FlagManager $flagManager,
        private readonly Validator   $validator,
    ) {}

    public function validateParams(array $trackingParams): void
    {
        if ($this->validator->isValid($trackingParams)) {
            $this->flagManager->deleteFlag(Notification::VARNISH_MARKETING_PARAMS);

            return;
        }

        $this->flagManager->saveFlag(Notification::VARNISH_MARKETING_PARAMS, true);
    }

    public function validateAttribute(EavAttributeInterface $attribute): void
    {
        $validator = $this->validator;
        if ($validator->attributeMatchesTrackingParam($attribute, $validator->getTrackingParams())) {
            $this->flagManager->saveFlag(Notification::VARNISH_MARKETING_PARAMS, true);

            return;
        }

        $this->flagManager->deleteFlag(Notification::VARNISH_MARKETING_PARAMS);
    }

}