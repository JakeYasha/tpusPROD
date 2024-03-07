<?php

namespace App\Classes;

class Dispatcher extends \Sky4\Dispatcher {

	public function dispatch($url) {
		$params = [
			'action_class_name' => '',
			'action_method_name' => 'execute',
			'action_params' => [],
			'actions' => [],
			'args' => [],
			'controller_name' => 'App\\Controller\\Index',
			'method_name' => 'actionIndex',
			'options' => [],
			'location' => null
		];
		list($args, $options) = $this->parseUrl($url);

		if (isset($args[0]) && isset($args[1]) && isset($args[2])) {
			if ($args[2] === 'show') {
				if ($args[1] === 'price' || $args[1] === 'firm') {
					unset($args[0]);
					app()->response()->redirect('/' . implode('/', $args) . '/', 301);
				}
			}
		}

		if (isset($args[0])) {
			/* work with location */
			if (preg_match("~([-0-9]+)$~", $args[0], $arr)) {
				$loc = explode('-', $arr[1]);
				if (count($loc) === 1) {
					if ($arr[1] === '76004' && count($args) === 1) {
						app()->response()->redirect('/');
					}
					$params['location'] = $arr[1];
				} else {
					$params['location'] = $arr[1];
				}
				unset($args[0]);
				if ($arr[1] !== '0') {
                    $_request_uri = $_SERVER['REQUEST_URI'];
					$_SERVER['REQUEST_URI'] = str_replace('//', '/', preg_replace('(/' . $arr[1] . ')', '/', $_request_uri, 1));
				}
			}

			$args = empty($args) ? [] : array_values($args);
		}

		if ($args) {
			$action_class_name = ['App', 'Action'];
			$action_params = $args;
			foreach ($args as $i => $arg) {
				if ($arg) {
					$action_class_name[] = \Sky4\Utils::getActionClassNameForDispatcher($arg);
					if (isset($action_params[$i])) {
						unset($action_params[$i]);
					}
					$params['actions'][] = [
						'class_name' => implode('\\', $action_class_name),
						'params' => array_values($action_params)
					];
				}
			}

			foreach ($params['actions'] as $action) {
				if (classExists($action['class_name'])) {
					$params['action_class_name'] = $action['class_name'];
					$params['action_params'] = $action['params'];
				}
			}
		}

		if (isset($args[0])) {
			if (!preg_match('/[^a-z0-9\-]+/i', $args[0])) {
				$params['controller_name'] = \Sky4\Utils::getControllerClassNameForDispatcher($args[0]);
				unset($args[0]);
				if (isset($args[1]) && !preg_match('/[^a-z0-9\-]+/i', $args[1])) {
					$params['method_name'] = \Sky4\Utils::getControllerMethodNameForDispatcher($args[1]);
					unset($args[1]);
				}
			}
			foreach ($args as $arg) {
				$params['args'][] = $arg;
			}
		}
		foreach ($options as $option_name => $option_value) {
			$params['options'][$option_name] = $option_value;
		}

		return $params;
	}

}
