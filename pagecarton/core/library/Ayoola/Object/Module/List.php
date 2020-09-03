<?php
/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Ayoola_Object_Module_List
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: List.php 5.11.2012 12.02am ayoola $
 */

/**
 * @see Ayoola_Object_Module_Abstract
 */
 
require_once 'Ayoola/Object/Abstract.php';


/**
 * @category   PageCarton
 * @package    Ayoola_Object_Module_List
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Ayoola_Object_Module_List extends Ayoola_Object_Module_Abstract
{
		
    /**
     * The method does the whole Class Process
     * 
     */
	protected function init()
    {
		$this->setViewContent( $this->getList(), true );
    } 
	
    /**
     * creates the list of the available subscription packages on the Ayoola
     * 
     */
	public function createList()
    {
		require_once 'Ayoola/Paginator.php';
		$list = new Ayoola_Paginator();
		$list->pageName = $this->getObjectName();
		$list->listTitle = 'List of Application Modules Available on this Application';
		$list->setData( $this->getDbData() );
		$list->setKey( $this->getIdColumn() );
	//	var_export( $this->getDbData() );
		$list->setNoRecordMessage( 'You have not created any module yet' );
		$list->createList(  
			array(
				'module_name' => '<a rel="shadowbox;height=300px;width=300px;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Ayoola_Object_Module_Editor/?' . $this->getIdColumn() . '=%KEY%">%FIELD%</a>', 
		// 		'auth_name' => null, 
				'<a title="Delete" rel="shadowbox;height=300px;width=300px;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Ayoola_Object_Module_Delete/?' . $this->getIdColumn() . '=%KEY%"><i class="fa fa-trash" aria-hidden="true"></i></a>', 
			)
		);
		//var_export( $list );
		return $list;
    } 
	// END OF CLASS
}
