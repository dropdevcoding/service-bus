<?php
/**
 * This file is part of the prooph/service-bus.
 * (c) 2014-2016 prooph software GmbH <contact@prooph.de>
 * (c) 2015-2016 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\ServiceBus\Plugin\InvokeStrategy;

use Prooph\Common\Event\ActionEvent;
use Prooph\Common\Event\ActionEventEmitter;
use Prooph\Common\Event\ActionEventListenerAggregate;
use Prooph\Common\Event\DetachAggregateHandlers;
use Prooph\ServiceBus\MessageBus;

abstract class AbstractInvokeStrategy implements ActionEventListenerAggregate
{
    use DetachAggregateHandlers;

    /**
     * @var int
     */
    protected $priority = 0;

    /**
     * @param mixed $handler
     * @param mixed $message
     */
    abstract protected function invoke($handler, $message): void;

    public function attach(ActionEventEmitter $events): void
    {
        $this->trackHandler($events->attachListener(MessageBus::EVENT_INVOKE_HANDLER, $this, $this->priority));
    }

    public function __invoke(ActionEvent $e): void
    {
        $message = $e->getParam(MessageBus::EVENT_PARAM_MESSAGE);
        $handler = $e->getParam(MessageBus::EVENT_PARAM_MESSAGE_HANDLER);

        $this->invoke($handler, $message);
        $e->setParam(MessageBus::EVENT_PARAM_MESSAGE_HANDLED, true);
    }
}
