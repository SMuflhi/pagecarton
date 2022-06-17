<?php
/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Ayoola_Object_Wrapper_Abstract
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Abstract.php 4.11.2012 6.16pm ayoola $
 */

/**
 * @see Ayoola_Abstract_Table
 */
 
require_once 'Ayoola/Abstract/Table.php';


/**
 * @category   PageCarton
 * @package    Ayoola_Object_Wrapper_Abstract
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

abstract class Ayoola_Object_Wrapper_Abstract extends Ayoola_Abstract_Table
{
	
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
	protected static $_accessLevel = 99;
	
    /**
     * Default Database Table
     *
     * @var string
     */
	protected $_tableClass = 'Ayoola_Object_Table_Wrapper';
	
    /**
     * Key for the id column
     * 
     * @var string
     */
	protected $_identifierKeys = array( 'wrapper_name' );
	
    /**
     * Key for the id column
     * 
     * @var string
     */
	protected $_idColumn = 'wrapper_name';
	
    /**
     * 
     * 
     * @var Ayoola_Object_Table_Wrapper
     */
	protected static $_wrapperTable;
		
    /**
     * 
     *
     */
	public static function wrapContent( $textToWrap, $wrapNameID )
	{
		if( $wrapNameID && trim( $textToWrap ) )  
		{
			//	var_export( $textToWrap );
			if( is_null( self::$_wrapperTable ) )
			{
				self::$_wrapperTable = new Ayoola_Object_Table_Wrapper;
			}
			if( $data = self::$_wrapperTable->selectOne( null, array( 'wrapper_name' => $wrapNameID ) ) )
			{
				$textToWrap = $data['wrapper_prefix'] . $textToWrap . $data['wrapper_suffix'];
			}
		} 
		   
		return $textToWrap;
	}
		
    /**
     * 
     *
     */
	public static function wrap( $textToWrap, $wrapNameID )
	{
		if( is_null( $textToWrap )  )
		{
			
		}
		elseif( is_object( $textToWrap ) && is_a( $textToWrap, 'Ayoola_Abstract_Viewable' ) )
		{
			$textToWrap = $textToWrap->view();
		}

		//	Function is now deprecated. Fix By sanitizing all pages.
		//	back to useful because of is_object
		return $textToWrap;
	}
	
    /**
     * 
     *
     */
	public function createForm( $submitValue = null, $legend = null, Array $values = null )
	{
        $form = new Ayoola_Form( array( 'name' => $this->getObjectName() ) );
		$fieldset = new Ayoola_Form_Element;
		$fieldset->addElement( array( 'name' => 'wrapper_label', 'placeholder' => 'Name of Wrapper', 'type' => 'InputText', 'value' => @$values['wrapper_label'] ) );
		$fieldset->addElement( array( 'name' => 'wrapper_prefix', 'placeholder' => 'e.g. &#x3C;div class=&#x22;wrapper_1&#x22;&#x3E;', 'type' => 'TextArea', 'value' => @$values['wrapper_prefix'] ) );
		$fieldset->addElement( array( 'name' => 'wrapper_suffix', 'placeholder' => 'e.g. &#x3C;/div&#x3E;', 'type' => 'TextArea', 'value' => @$values['wrapper_suffix'] ) );

		$fieldset->addRequirements( array( 'WordCount' => array( 1,2000 ) ) );
		$fieldset->addFilters( array( 'trim' => null ) );
		$fieldset->addRequirement( 'wrapper_label', array( 'WordCount' => array( 3,100 ), ) );
		$fieldset->addLegend( $legend );
		$form->addFieldset( $fieldset );  
		$form->submitValue = $submitValue;
		$this->setForm( $form );
	}
	// END OF CLASS
}
