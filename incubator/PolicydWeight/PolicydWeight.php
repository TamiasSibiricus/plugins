<?php
/**
 * i-MSCP PolicydWeight plugin
 * @copyright 2015 Laurent Declercq <l.declercq@nuxwin.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

/**
 * Class iMSCP_Plugin_PolicydWeight
 */
class iMSCP_Plugin_PolicydWeight extends iMSCP_Plugin_Action
{
	/**
	 * Register a callback for the given event(s)
	 *
	 * @param iMSCP_Events_Manager_Interface $eventsManager
	 * @return void
	 */
	public function register(iMSCP_Events_Manager_Interface $eventsManager)
	{
		$eventsManager->registerListener(iMSCP_Events::onBeforeEnablePlugin, $this);
	}

	/**
	 * onBeforeEnablePlugin event listener
	 *
	 * @param iMSCP_Events_Event $event
	 * @return void
	 */
	public function onBeforeEnablePlugin(iMSCP_Events_Event $event)
	{
		$this->checkCompat($event);
	}

	/**
	 * Plugin activation
	 *
	 * @throws iMSCP_Plugin_Exception
	 * @param iMSCP_Plugin_Manager $pluginManager
	 * @return void
	 */
	public function enable(iMSCP_Plugin_Manager $pluginManager)
	{
		$servicePort = $this->getConfigParam('policyd_weight_port', 12525) . ';tcp;POLICYD_WEIGHT;1;127.0.0.1';

		/** @var iMSCP_Config_Handler_Db $dbConfig */
		$dbConfig = iMSCP_Registry::get('dbConfig');

		if(!isset($dbConfig['PORT_POLICYD_WEIGHT'])) {
			$dbConfig['PORT_POLICYD_WEIGHT'] = $servicePort;
		} else {
			unset($dbConfig['PORT_POLICYD_WEIGHT']);
			$dbConfig['PORT_POLICYD_WEIGHT'] = $servicePort;
		}
	}

	/**
	 * Plugin deactivation
	 *
	 * @throws iMSCP_Plugin_Exception
	 * @param iMSCP_Plugin_Manager $pluginManager
	 * @return void
	 */
	public function disable(iMSCP_Plugin_Manager $pluginManager)
	{
		/** @var iMSCP_Config_Handler_Db $dbConfig */
		$dbConfig = iMSCP_Registry::get('dbConfig');
		unset($dbConfig['PORT_POLICYD_WEIGHT']);
	}

	/**
	 * Check plugin compatibility
	 *
	 * @param iMSCP_Events_Event $event
	 * @return void
	 */
	protected function checkCompat(iMSCP_Events_Event $event)
	{
		if($event->getParam('pluginName') == $this->getName()) {
			if(version_compare($event->getParam('pluginManager')->getPluginApiVersion(), '1.0.0', '<')) {
				set_page_message(
					tr('Your i-MSCP version is not compatible with this plugin. Try with a newer version.'), 'error'
				);

				$event->stopPropagation();
			}
		}
	}
}
