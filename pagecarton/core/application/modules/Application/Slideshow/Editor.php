<?php
/**
 * PageCarton
 *
 * LICENSE
 *
 * @Slideshow   Ayoola
 * @package    Application_Slideshow_Editor
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Editor.php 4.17.2012 7.55am ayoola $
 */

/**
 * @see Application_Slideshow_Abstract
 */
 
require_once 'Application/Slideshow/Abstract.php';


/**
 * @Slideshow   Ayoola
 * @package    Application_Slideshow_Editor
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Application_Slideshow_Editor extends Application_Slideshow_Abstract
{
	
    /**
     * The method does the whole Class Process
     * 
     */
	protected function init()
    {
		try
		{ 
			$this->_createDefaultSlideshow();
			if( ! $data = self::getIdentifierData() )
			{ 
				$this->_identifier = array( 'slideshow_name' => $this->getParameter( 'slideshow_name' ) );
				self::setIdentifierData();
				if( ! $data = self::getIdentifierData() )
				{ 
					if( self::hasPriviledge( 98 ) )
					{
						$data = $this->_identifier;
					}
				}
			}
			$this->createForm( 'Save', 'Editing "' . $data['slideshow_title'] . '"', $data );
			$this->setViewContent( $this->getForm()->view(), true );
			
			if( ! $values = $this->getForm()->getValues() ){ return false; }
			if( ! $this->updateDb() )
			{ 
				return false;
			}
			$this->setViewContent(  '' . self::__( '<div class="boxednews goodnews" style="clear:both;">Slideshow settings saved successfully. </div>' ) . '', true  ); 
			switch( $values['slideshow_type'] )
			{
				case 'post':
					$this->setViewContent( self::__( '<a href="' . Ayoola_Application::getUrlPrefix() . '/widgets/Application_Article_Creator?article_type=' .  @$values['slideshow_article_type'] . '&category=' .  @$values['category_name'] . '" class="boxednews pc-bg-color">Add new post</a>' ) );    
				break;
				default:
					$this->setViewContent( self::__( '<a href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_Slideshow_Manage/?slideshow_name=' .  ( @$values['slideshow_name'] ? : $data['slideshow_name'] ) . '" class="boxednews pc-bg-color">Update photos</a>' ) ); 
				break;
			}
		}
		catch( Application_Slideshow_Exception $e ){ return false; }
    } 
	// END OF CLASS
}
