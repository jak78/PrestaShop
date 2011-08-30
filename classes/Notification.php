<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 6856 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Notification
{	
	public $types;
	
	public function __construct()
	{
		$this->types = array('order', 'message', 'customer');
	}
	
	/**
	 * getLastElements return all the notifications (new order, new customer registration, and new customer message)
	 * Get all the notifications 
	 *
	 * @return array containing the notifications
	 */
	public function getLastElements()
	{
		$notifications = array();
		$sql = 'SELECT id_last_order, id_last_message, id_last_customer 
				FROM `'._DB_PREFIX_.'employee` 
				WHERE `id_employee` = '.(int)$this->context->employee->id;
		$employee_infos = Db::getInstance()->getRow($sql);
				
		foreach ($this->types as $type)
			$notifications[$type] = Notification::getLastElementsIdsByType($type, $employee_infos['id_last_'.$type]);
				
		return $notifications;		
	}
	
	/**
	 * getLastElementsIdsByType return all the element ids to show (order, customer registration, and customer message)
	 * Get all the element ids
	 *
	 * @return array containing the notifications
	 */
	public static function getLastElementsIdsByType($type, $id_last_element)
	{
	
		if ($type == 'order' || $type == 'message')
		{
			$sql = 'SELECT id_order 
					FROM `'._DB_PREFIX_.(($type == 'order') ? $type.'s' : $type).'` 
					WHERE `id_'.$type.'` > '.$id_last_element.' 
					ORDER BY `id_'.$type.'` DESC LIMIT 5');
		}
		else
		{
			$sql = 'SELECT id_'.$type.' 
					FROM `'._DB_PREFIX_.$type.'` 
					WHERE `id_'.$type.'` > '.$id_last_element.' 
					ORDER BY `id_'.$type.'` DESC LIMIT 5');
		}
		
		return Db::getInstance()->ExecuteS($sql);
	}	
	
	/**
	 * updateEmployeeLastElement return 0 if the field doesn't exists in Employee table.
	 * Updates the last element seen by the employee 
	 *
	 * @param boolean $type contains the field name of the Employee table
	 * @return boolean if type exists or not
	 */
	public function updateEmployeeLastElement($type)
	{	
		if (in_array($type, $this->types))
		{
			// We update the last item viewed
			$sql = 'UPDATE `'._DB_PREFIX_.'employee` 
					SET `id_last_'.$type.'` = (SELECT MAX(`id_'.$type.'`) 
					FROM `'._DB_PREFIX_.(($type == 'order') ? $type.'s' : $type).'`) 
					WHERE `id_employee` = '.(int)$this->context->employee->id);
			Db::getInstance()->Execute($sql);
			
			return true;		
		} 
		else 
		{ 
			return false;
		}
	}	
}
?>