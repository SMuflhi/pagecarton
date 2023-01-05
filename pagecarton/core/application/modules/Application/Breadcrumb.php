<?php
/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Application_Breadcrumb
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Breadcrumb.php 5.11.2012 10.465am ayoola $
 */

/**
 * @see Ayoola_
 */
 
//require_once 'Ayoola/.php';


/**
 * @category   PageCarton
 * @package    Application_Breadcrumb
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Application_Breadcrumb extends Ayoola_Abstract_Table
{

    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Breadcrumb';      
	
    /**
     * Whether class is playable or not
     *
     * @var boolean
     */
	protected static $_playable = true;
	
    /**
     * Access level for player
     *
     * @var boolean
     */
	protected static $_accessLevel = 0;
	
    /**
     * Performs the process
     * 
     */
	public function init()
    {
		try
		{
			$this->setParameter( array( 'markup_template_no_cache' => true ) ); 
			$breadcrumb = Ayoola_Page::getBreadcrumb();
			if( count( $breadcrumb ) < 2 )
			{
				return false;
			}
			
			$homeDone = true;
			if( ! @$this->_parameter['markup_template'] ) 
			{
				$this->_parameter['markup_template_prefix'] = '<ol class="pc-breadcrumb">';
				if( Ayoola_Application::getUrlPrefix() && Ayoola_Application::getUrlPrefix() !== '/index.php' ) 
				{
					$this->_parameter['markup_template_prefix'] .= '<li><a href="/" title="' . self::__( 'Home Page' ) . '">' . self::__( 'Home' ) . '</a></li>';
					$homeDone = false;
				}
				else
				{
					$this->_parameter['markup_template_prefix'] .= '<li><a href="' . Ayoola_Application::getUrlPrefix() . '/" title="' . self::__( 'Home Page' ) . '">' . self::__( 'Home' ) . '</a></li>';
				}
				$this->_parameter['markup_template_suffix'] = 
				'
						</ol>
				';
				$this->_parameter['markup_template_active'] = '<li title="{{{description}}}">{{{title}}}</li>';
				$template =   
				'
						<li title="{{{description}}}"><a href="{{{url}}}" class="pc-breadcrumb-active" title="{{{description}}}">{{{title}}}</a></li>
				';
			
			}
			else 
			{
				$template = $this->_parameter['markup_template'];
			}
			$this->_parameter['markup_template'] = null;  
			$html = null;
			$i = 0; //	counter
			$j = 10; //	5 is our max articles to show
			$j = is_numeric( $this->getParameter( 'no_of_items_to_show' ) ) ? intval( $this->getParameter( 'no_of_items_to_show' ) ) : $j;
			$urlLog = array();
			$presentUri = Ayoola_Application::getPresentUri();
			foreach( $breadcrumb as $each )
			{
				$each = (array) $each;

				if( $i++ >= $j )
				{ 
					break; 
				}	
				if( isset( $urlLog[$each['url']] ) || ( $each['url'] == '/' && $homeDone ) || $each['url'] == '/object' || $each['url'] == '/tools/classplayer' )
				{   					
					continue; 
				}
				elseif( ! $each['title'] )
				{
					$x = array_map( 'ucwords', explode( '/', str_replace( '-', ' ',	 $each['url'] ) ) );  
					$each['title'] = array_pop( $x );  
				}
				if( ! $each['title'] )
				{
					continue;    
                }
                $each['title'] = '' . self::__( $each['title'] ) . '';
                $each['description'] = '' . self::__( $each['description'] ) . '';
				$urlLog[$each['url']] = $each['url'];
				if( $template )
				{
					if( ( trim( $each['url'], '/' ) === trim( $presentUri, '/' ) || trim( $each['url'], '/' ) == '404' ) && @$this->_parameter['markup_template_active'] )
					{ 
						$tempTemplate = @$this->_parameter['markup_template_active']; 
					}	
					else
					{
						$tempTemplate = $template; 
					}
					$each['url'] = Ayoola_Application::getUrlPrefix() . $each['url'];
					$html .= self::replacePlaceholders( $tempTemplate, $each + array( 'placeholder_prefix' => '{{{', 'placeholder_suffix' => '}}}', ) );
				} 
			}
			$this->_parameter['markup_template'] = $html ? : null;
		}
		catch( Ayoola_Exception $e )
		{ 
		//	var_export( $e->getMessage() );
			return false; 
		}
	}
	
	// END OF CLASS
}
