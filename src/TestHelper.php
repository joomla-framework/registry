<?php
/**
 * Part of the Joomla Framework Test Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Test;

use PHPUnit\Framework\TestCase;

/**
 * Static helper methods to assist unit testing PHP code.
 *
 * @since  1.0
 */
class TestHelper
{
	/**
	 * Helper method that gets a protected or private property in a class by reflection.
	 *
	 * @param   string|object  $objectOrClass  The object from which to return the property value.
	 * @param   string         $propertyName   The name of the property to return.
	 *
	 * @return  mixed  The value of the property.
	 *
	 * @since   1.0
	 * @throws  \InvalidArgumentException if property not available.
	 * @throws  \ReflectionException
	 */
	public static function getValue($objectOrClass, $propertyName)
	{
		$refl = new \ReflectionClass($objectOrClass);

		// First check if the property is easily accessible.
		if ($refl->hasProperty($propertyName))
		{
			$property = $refl->getProperty($propertyName);
			$property->setAccessible(true);

			return $property->getValue($objectOrClass);
		}

		// Hrm, maybe dealing with a private property in the parent class.
		if (get_parent_class($objectOrClass))
		{
			$property = new \ReflectionProperty(get_parent_class($objectOrClass), $propertyName);
			$property->setAccessible(true);

			return $property->getValue($objectOrClass);
		}

		$class = \is_string($objectOrClass) ? $objectOrClass : \get_class($objectOrClass);
		throw new \InvalidArgumentException(sprintf('Invalid property [%s] for class [%s]', $propertyName, $class));
	}

	/**
	 * Helper method that invokes a protected or private method in a class by reflection.
	 *
	 * Example usage:
	 *
	 * $this->assertTrue(TestHelper::invoke($this->object, 'methodName', 123));
	 * where 123 is the input parameter for your method
	 *
	 * @param   object  $object         The object on which to invoke the method.
	 * @param   string  $methodName     The name of the method to invoke.
	 * @param   array   ...$methodArgs  The arguments to pass forward to the method being called
	 *
	 * @return  mixed
	 *
	 * @since   1.0
	 * @throws  \ReflectionException
	 */
	public static function invoke($object, $methodName, ...$methodArgs)
	{
		$method = new \ReflectionMethod($object, $methodName);
		$method->setAccessible(true);

		return $method->invokeArgs(\is_object($object) ? $object : null, $methodArgs);
	}

	/**
	 * Helper method that sets a protected or private property in a class by relfection.
	 *
	 * @param   object  $object        The object for which to set the property.
	 * @param   string  $propertyName  The name of the property to set.
	 * @param   mixed   $value         The value to set for the property.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  \ReflectionException
	 */
	public static function setValue($object, $propertyName, $value)
	{
		$refl = new \ReflectionClass($object);

		// First check if the property is easily accessible.
		if ($refl->hasProperty($propertyName))
		{
			$property = $refl->getProperty($propertyName);
			$property->setAccessible(true);

			$property->setValue($object, $value);
		}
		elseif (get_parent_class($object))
		{
			// Hrm, maybe dealing with a private property in the parent class.
			$property = new \ReflectionProperty(get_parent_class($object), $propertyName);
			$property->setAccessible(true);

			$property->setValue($object, $value);
		}
	}
}
