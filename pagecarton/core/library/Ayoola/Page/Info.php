<?php

/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Ayoola_Page_Info
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Info.php Monday 2nd of October 2017 09:34PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class Ayoola_Page_Info extends PageCarton_Widget
{
	
    /**	
     *
     * @var boolean
     */
	public static $editorViewDefaultToPreviewMode = true;
	
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected static $_accessLevel = array( 0 );
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Page Info'; 

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...

            //  Output demo content to screen
		    $currentUrl = rtrim( Ayoola_Application::getRuntimeSettings( 'real_url' ), '/' ) ? : '/';
		    $settings = Application_Settings_Abstract::getSettings( 'SiteInfo' );  

			switch( $currentUrl )
			{
				case '/tools/classplayer':
				case '/object':
				case '/widgets':
					//	Do nothing.
					//	 had to go through this route to process for 0.00
					if( @$_REQUEST['url'] )
                    {
                        $currentUrl = $_REQUEST['url'];
                    }
                    $title = explode( "/", Ayoola_Application::getRuntimeSettings( 'url' ) );
                    $title = array_pop( $title );
                    $title = ucwords( $title );
                    if( ! empty( $_SERVER['HTTP_AYOOLA_PLAY_CLASS'] ) )
                    {
                        $title = $_SERVER['HTTP_AYOOLA_PLAY_CLASS'];
                    }
                    if( class_exists( $title ) && method_exists( $title, 'getObjectTitle' ) && $title::getObjectTitle() )
                    {
                        $title = $title::getObjectTitle() ? : $title;
                    }
                    else
                    {
                        $title = str_ireplace( array( 'Ayoola_', 'Application_', 'Article_', 'Object_', 'Classplayer_', ), '', $title );  
                        $title = ucwords( implode( ' ', explode( '_', $title ) ) );
                        $title = ucwords( implode( ' ', explode( '-', $title ) ) );
                    }
                break;
				default:

				break;
			}

            //  Output demo content to screen
            $url = $this->getParameter( 'url' ) ? : $currentUrl;
            $pageInfo = Ayoola_Page::getInfo( $url );
            if( ! $pageInfo['url'] )
            {
                $currentUrl = rtrim( Ayoola_Application::getRuntimeSettings( 'real_url' ), '/' ) ? : '/';
                $pageInfo = Ayoola_Page::getInfo( $this->getParameter( 'url' ) ? : $currentUrl );
            }

            if( empty( $pageInfo['description'] ) && self::hasPriviledge( array( 99, 98 ) ) )
            {
                @$pageInfo['description'] = $pageInfo['description'] ? : '' . self::__( 'Description for this page has not been set. Page Description will appear here when they become available.' ) . '';
            }
            if( empty( $pageInfo['title'] ) && self::hasPriviledge( array( 99, 98 ) ) )
            {
                @$pageInfo['title'] = $pageInfo['title'] ? : '' . self::__( 'Set This Page Title Here' ) . '';
            }

            if( $this->getParameter( 'use_site_defaults' ) )
            {
                if( empty( $pageInfo['title'] ) )
                {
                    $pageInfo['title'] = $settings['site_headline'] ? : '...';
                }
                if( empty( $pageInfo['cover_photo'] ) )
                {
                    $pageInfo['cover_photo'] = $settings['cover_photo'];
                }
                if( empty( $pageInfo['description'] ) )
                {
                    $pageInfo['description'] = $settings['site_description'];
                }
            }



            if( empty( $pageInfo['title'] ) )
            {

            }

            if( ! empty( $title ) )
            {
                $pageInfo['title'] = $title;
            }

            if( empty( $pageInfo['cover_photo'] ) )
            {
                $pageInfo['cover_photo'] = $settings['cover_photo'] ? : ( $this->getParameter( 'default_cover_photo' ) ? : '/img/placeholder-image.jpg' );
            }
            
            if( self::hasPriviledge( 98 ) )
            {
                $pageInfo['link_to_edit'] = '<a style="font-size:x-small; color:inherit;text-transform:uppercase;display:inline-block;" onclick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Ayoola_Page_Editor/?url=' . ( $pageInfo['url'] ? : $url ) . '&pc_form_element_whitelist=title,description,cover_photo,url\', \'page_refresh\' );" href="javascript:">[' . self::__( 'edit page headline and description' ) . ']</a>';
                $pageInfo['pc_no_data_filter'] = true;
            }

            $html = '<div class="pc_theme_parallax_background" style="background-image:     linear-gradient( rgba(0, 0, 0, 0.5),      rgba(0, 0, 0, 0.5)    ), url(\'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/Application_IconViewer/?url=' . ( $pageInfo['cover_photo'] ? : $settings['cover_photo'] ) . '&crop=1&max_width=1500&max_height=600\');">'; 
            $html .= $this->getParameter( 'css_class_of_inner_content' ) ? '<div class="' . $this->getParameter( 'css_class_of_inner_content' ) . '">' : null;
            $html .= '<h1>' . @$pageInfo['title'] . '</h1>';
            $html .= $pageInfo['description'] ? '<br><br><p>' . $pageInfo['description'] . '</p>' : null;
            $html .= self::hasPriviledge( array( 99, 98 ) ) ? '<br><br><p style="font-size:x-small;">' . $pageInfo['link_to_edit'] .  '</p>' : null;
            $html .= $this->getParameter( 'css_class_of_inner_content' ) ? '</div>' : null;
           
            $html .= '</div>';
            $this->setViewContent( $html );   
            $this->_objectTemplateValues = array_merge( $pageInfo ? : array(), $this->_objectTemplateValues ? : array() );

             // end of widget process
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
            $this->setViewContent(  '' . self::__( 'Theres an error in the code' ) . '', true  ); 
            return false; 
        }
	}
	
    /**
	 * Returns text for the "interior" of the Layout Editor
	 * The default is to display view and option parameters.
	 * 		
     * @param array Object Info
     * @return string HTML
     */
    public static function getHTMLForLayoutEditor( & $object )
	{

    }
	// END OF CLASS
}
