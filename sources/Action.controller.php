<?php

/**
 * Abstract base class for controllers. Holds action_index and pre_dispatch
 *
 * @name      ElkArte Forum
 * @copyright ElkArte Forum contributors
 * @license   BSD http://opensource.org/licenses/BSD-3-Clause
 *
 * @version 1.1 dev
 *
 */

if (!defined('ELK'))
	die('No access...');

/**
 * Abstract base class for controllers.
 *
 * - Requires a default action handler, action_index().
 * - Defines an empty implementation for pre_dispatch() method.
 */
abstract class Action_Controller
{
	/**
	 * The event manager.
	 * @var object
	 */
	protected $_events = null;

	/**
	 * The current hook.
	 * @var string
	 */
	protected $_hook = '';

	/**
	 * Constructor...
	 * Requires the name of the controller we want to instantiate, lowercase and
	 * without the "_Controller" part.
	 *
	 * @param null|Event_Manager $eventManager - The event manager
	 */
	public function __construct($eventManager = null)
	{
		// A safety-net to remain backward compatibility
		if ($eventManager === null)
			$eventManager = new Event_Manager();

		$this->_events = $eventManager;

		$this->_initEventManager();
	}

	/**
	 * Initialize the event manager for the controller
	 *
	 * Uses the XXX_Controller name to define the set of event hooks to load
	 */
	protected function _initEventManager()
	{
		// Use the base controller name for the hook, ie post
		$this->_hook = str_replace('_Controller', '', get_class($this));

		$classes = $this->_loadModules();

		$this->_events->registerClasses($classes);

		$this->_events->setSource($this);
	}

	/**
	 * Public function to return the controllers generic hook name
	 */
	public function getHook()
	{
		return strtolower($this->_hook);
	}

	/**
	 * Finds the modules for a certain controller.
	 *
	 * @return string[] Class names to look for
	 */
	protected function _loadModules()
	{
		global $modSettings;

		$classes = array();
		$hook = strtolower(str_replace('_Controller', '', $this->_hook));
		$setting_key = 'modules_' . $hook;

		// For all the modules that have been registered see if we have a class to load for this hook area
		if (!empty($modSettings[$setting_key]))
		{
			$modules = explode(',', $modSettings[$setting_key]);
			foreach ($modules as $module)
			{
				$class = ucfirst($module) . '_' . ucfirst($hook) . '_Module';
				if (class_exists($class))
					$classes[] = $class;
			}
		}

		return $classes;
	}

	/**
	 * Default action handler.
	 *
	 * - This will be called by the dispatcher in many cases.
	 * - It may set up a menu, sub-dispatch at its turn to the method matching ?sa= parameter
	 * or simply forward the request to a known default method.
	 */
	abstract public function action_index();

	/**
	 * Called before any other action method in this class.
	 *
	 * - Allows for initializations, such as default values or
	 * loading templates or language files.
	 */
	public function pre_dispatch()
	{
		// By default, do nothing.
		// Sub-classes may implement their prerequisite loading,
		// such as load the template, load the language(s) file(s)
	}

	/**
	 * An odd function that allows events to request dependencies from properties
	 * of the class.
	 *
	 * @param string $dep - The name of the property the even wants
	 * @param mixed[] $dependencies - the array that will be filled with the
	 *                                references to the dependencies
	 */
	public function provideDependencies($dep, &$dependencies)
	{
		if (property_exists($this, $dep))
			$dependencies[$dep] = &$this->$dep;
		elseif (property_exists($this, '_' . $dep))
			$dependencies[$dep] = &$this->{'_' . $dep};
		elseif (array_key_exists($dep, $GLOBALS))
			$dependencies[$dep] = &$GLOBALS[$dep];
	}

	/**
	 * Shortcut to register an array of names as events triggered at a certain
	 * position in the code.
	 *
	 * @param string $name - Name of the trigger where the events will be executed.
	 * @param string $method - The method that will be executed.
	 * @param string[] $to_register - An array of classes to register.
	 */
	protected function _registerEvent($name, $method, $to_register)
	{
		foreach ($to_register as $class)
		{
			$this->_events->register($name, array($name, array($class, $method, 0)));
		}
	}
}