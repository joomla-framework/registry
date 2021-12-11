## Creating a Decorated Dispatcher

Occasionally, it may be necessary to add extra capabilities to the event dispatcher without adjusting how the dispatcher is used throughout the application. One way to do this is by using the decorator pattern to wrap a dispatcher with another class.

Since we have typehinted `Joomla\Event\DispatcherInterface` throughout our application instead of the concrete `Joomla\Event\Dispatcher` (or any other class which fulfills the interface), the only changes required in our application are to actually add the decorated class and to adjust the code which creates the dispatcher and injects it into other classes (typically a service definition in your dependency injection container).

### Example of a Decorated Dispatcher

In this example, we will decorate our application's original dispatcher with a dispatcher which supports the [PHP Debug Bar](http://phpdebugbar.com/) and will log measurements for the amount of time spent processing an event.

The decorated dispatcher below proxies all method calls declared as part of `Joomla\Event\DispatcherInterface` to the decorated dispatcher, and in the `dispatch()` method adds time measurements for each dispatched event. Note this means that your application must only use methods declared as part of the interface or add appropriate checks (i.e. `method_exists()`) in case a non-interface method is used.

```php
<?php
namespace App\Event;

use DebugBar\DebugBar;
use Joomla\Event\DispatcherInterface;
use Joomla\Event\EventInterface;
use Joomla\Event\SubscriberInterface;

/**
 * Decorating event dispatcher which adds support for the `maximebf/debugbar` package.
 */
final class DebugDispatcher implements DispatcherInterface
{
	/**
	 * Debug bar object
	 *
	 * @var  DebugBar
	 */
	private $debugBar;

	/**
	 * The delegated dispatcher
	 *
	 * @var  DispatcherInterface
	 */
	private $dispatcher;

	/**
	 * Event subscriber constructor.
	 *
	 * @param   DispatcherInterface  $dispatcher  The delegated dispatcher
	 * @param   DebugBar             $debugBar    Debug bar object
	 */
	public function __construct(DispatcherInterface $dispatcher, DebugBar $debugBar)
	{
		$this->debugBar   = $debugBar;
		$this->dispatcher = $dispatcher;
	}

	/**
	 * Attaches a listener to an event
	 *
	 * @param   string    $eventName  The event to listen to.
	 * @param   callable  $callback   A callable function
	 * @param   integer   $priority   The priority at which the $callback executed
	 *
	 * @return  boolean
	 */
	public function addListener(string $eventName, callable $callback, int $priority = 0): bool
	{
		return $this->dispatcher->addListener($eventName, $callback, $priority);
	}

	/**
	 * Adds an event subscriber.
	 *
	 * @param   SubscriberInterface  $subscriber  The subscriber.
	 *
	 * @return  void
	 */
	public function addSubscriber(SubscriberInterface $subscriber): void
	{
		$this->dispatcher->addSubscriber($subscriber);
	}

	/**
	 * Dispatches an event to all registered listeners.
	 *
	 * @param   string          $name   The name of the event to dispatch.
	 * @param   EventInterface  $event  The event to pass to the event handlers/listeners.
	 *
	 * @return  EventInterface  The event after being passed through all listeners.
	 */
	public function dispatch(string $name, ?EventInterface $event = null): EventInterface
	{
		/** @var \DebugBar\DataCollector\TimeDataCollector $collector */
		$collector = $this->debugBar['time'];
		$label     = 'dispatching ' . $name;

		$collector->startMeasure($label);

		$event = $this->dispatcher->dispatch($name, $event);

		// Needed because the application's before respond event may be cut short
		if ($collector->hasStartedMeasure($label))
		{
			$collector->stopMeasure($label);
		}

		return $event;
	}

	/**
	 * Clear the listeners in this dispatcher.
	 *
	 * If an event is specified, the listeners will be cleared only for that event.
	 *
	 * @param   string  $event  The event name.
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function clearListeners($event = null)
	{
		$this->dispatcher->clearListeners($event);

		return $this;
	}

	/**
	 * Count the number of registered listeners for the given event.
	 *
	 * @param   string  $event  The event name.
	 *
	 * @return  integer
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function countListeners($event)
	{
		return $this->dispatcher->countListeners($event);
	}

	/**
	 * Get the listeners registered to the given event.
	 *
	 * @param   string|null  $event  The event to fetch listeners for or null to fetch all listeners
	 *
	 * @return  callable[]  An array of registered listeners sorted according to their priorities.
	 */
	public function getListeners(?string $event = null)
	{
		return $this->dispatcher->getListeners($event);
	}

	/**
	 * Tell if the given listener has been added.
	 *
	 * If an event is specified, it will tell if the listener is registered for that event.
	 *
	 * @param   callable     $callback   The callable to check is listening to the event.
	 * @param   string|null  $eventName  An optional event name to check a listener is subscribed to.
	 *
	 * @return  boolean  True if the listener is registered, false otherwise.
	 */
	public function hasListener(callable $callback, ?string $eventName = null)
	{
		return $this->dispatcher->hasListener($callback, $eventName);
	}

	/**
	 * Removes an event listener from the specified event.
	 *
	 * @param   string    $eventName  The event to remove a listener from.
	 * @param   callable  $listener   The listener to remove.
	 *
	 * @return  void
	 */
	public function removeListener(string $eventName, callable $listener): void
	{
		$this->dispatcher->removeListener($eventName, $listener);
	}

	/**
	 * Removes an event subscriber.
	 *
	 * @param   SubscriberInterface  $subscriber  The subscriber.
	 *
	 * @return  void
	 */
	public function removeSubscriber(SubscriberInterface $subscriber): void
	{
		$this->dispatcher->removeSubscriber($subscriber);
	}
}
```

### Decorating the Service Definition

If using a dependency injection package which supports extending service definitions (such as the `joomla/di` package), you can configure your container to decorate the original service definition so that your new decorator is used throughout the application.

In the example below, we are creating a `Joomla\DI\ServiceProviderInterface` instance which should be conditionally loaded into an application based on if it is placed in a debug state (similar to the `JDEBUG` constant in the Joomla! CMS being set to true). The service provider will load the main debug bar class into the container as a service and decorate the event dispatcher.

```php
<?php
namespace App\Service;

use App\Event\DebugDispatcher;
use DebugBar\DebugBar;
use DebugBar\StandardDebugBar;
use Joomla\DI\Container;
use Joomla\DI\Exception\DependencyResolutionException;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;

/**
 * Debug bar service provider
 */
final class DebugBarProvider implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 */
	public function register(Container $container): void
	{
		$container->alias(StandardDebugBar::class, DebugBar::class)
			->share(
				DebugBar::class,
				function (Container $container): DebugBar
				{
					// We should only be loading the debug bar package in our dev environment, raise an error if this provider is loaded and the classes are not available
					if (!class_exists(StandardDebugBar::class))
					{
						throw new DependencyResolutionException(sprintf('The %s class is not loaded.', StandardDebugBar::class));
					}

					return new StandardDebugBar;
				}
			);

		$container->extend(
			DispatcherInterface::class,
			function (DispatcherInterface $dispatcher, Container $container): DispatcherInterface
			{
				return new DebugDispatcher($dispatcher, $container->get(DebugBar::class));
			}
		);
	}
}
```
