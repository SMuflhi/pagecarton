<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Ayoola_Page_Layout_MakeDefault
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: MakeDefault.php Saturday 16th of September 2017 05:40PM  $
 */

/**
 * @see PageCarton_Widget
 */

class Ayoola_Page_Layout_MakeDefault extends Ayoola_Page_Layout_Abstract
{
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Set as site theme'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public static function this( $themeName )
    {    
		try
		{ 
            
            //  Code that runs the widget goes here...

            $each = new Application_Settings_Editor( array( 'settingsname_name' => 'Page' ) );
            $settings = Ayoola_Page_Settings::retrieve();
            $settings['default_layout'] = $themeName;
            $each->fakeValues = $settings;
            if( ! $each->init() )
            {
                return false;
            }

            //  some pages were not working fine after this
            Application_Cache_Clear::viewInLine();
             // end of widget process
             return true;
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
            return false; 
        }
	}

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            
            //  Code that runs the widget goes here...
			if( ! $data = $this->getIdentifierData() ){ return false; }
                        
			$this->createConfirmationForm( 'Confirm', 'Set  "' . $data['layout_label'] . '" as the main site theme' );
			$this->setViewContent( $this->getForm()->view(), true);
            if( ! $values = $this->getForm()->getValues() )
            { 
                return false; 
            }

            if( ! self::this( $data['layout_name'] ) )
            {
                $this->setViewContent( self::__( '<p class="badnews">An error was encountered while changing the theme.</p>' ) ); 
                return false;
            }
            $this->setViewContent(  '' . self::__( '<p class="goodnews">Theme successfully set as main site theme. <a href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/PageCarton_NewSiteWizard">New Website Wizard</a></p>' ) . '', true  );   

            //  some pages were not working fine after this
            Application_Cache_Clear::viewInLine();
             // end of widget process
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
            $this->setViewContent(  '' . self::__( 'Theres an error in the code' ) . '', true  ); 
            return false; 
        }
	}
	// END OF CLASS
}
