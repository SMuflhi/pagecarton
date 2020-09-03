<?php
/**
 * PageCarton
 *
 * LICENSE
 *
 * @Slideshow   Ayoola
 * @package    Application_Slideshow_List
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: List.php 5.11.2012 12.02am ayoola $
 */

/**
 * @see Application_Slideshow_Abstract
 */
 
require_once 'Application/Slideshow/Abstract.php';


/**
 * @Slideshow   Ayoola
 * @package    Application_Slideshow_List
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Application_Slideshow_List extends Application_Slideshow_Abstract
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
     * creates the list of the available subscription packages on the application
     * 
     */
	public function createList()
    {
		require_once 'Ayoola/Paginator.php';
		$list = new Ayoola_Paginator();
		$list->pageName = $this->getObjectName();
		$list->listTitle = 'List of Slideshows on this Application';
		$data = $this->getDbData();
		if( count( $data ) > 20 )
		{
			set_time_limit( 0 );
			foreach( $data as $key => $each )
			{
				if( empty( $each['slideshow_type'] ) && empty( $each['slideshow_image'] ) )
				{
					$this->getDbTable()->delete( array( 'slideshow_id' => $each['slideshow_id'] ) );
					unset( $data[$key] );
				}
			}	
		}
		$list->setData( $data );
	//	$this->setIdColumn( 'Slideshow_name' );
		$list->setKey( $this->getIdColumn() );
		$list->setNoRecordMessage( 'There are no slideshows on this application yet. <a title="Create a slideshow" rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_Slideshow_Creator/">Create one!</a>' );
		$list->createList(      
			array(
				'slideshow_title' => '<a title="Edit slideshow information" rel="shadowbox;" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_Slideshow_Editor/?' . $this->getIdColumn() . '=%KEY%">[%FIELD%]</a>', 
				'<a title="Delete slideshow" rel="shadowbox;height=300px;width=300px;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_Slideshow_Delete/?' . $this->getIdColumn() . '=%KEY%"><i class="fa fa-trash" aria-hidden="true"></i></a>', 
			)
		);
		//var_export( $list );
		return $list;
    } 
	// END OF CLASS
}
