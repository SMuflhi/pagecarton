<?php
/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Ayoola_Extension_Import_Abstract
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Abstract.php 4.17.2012 7.55am ayoola $
 */

/**
 * @see Ayoola_Extension_Import_Exception 
 */
 
require_once 'Ayoola/Page/Layout/Exception.php';


/**
 * @category   PageCarton
 * @package    Ayoola_Extension_Import_Abstract
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

abstract class Ayoola_Extension_Import_Abstract extends Ayoola_Abstract_Table
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
     * 
     *
     * @var string
     */
	protected $_idColumn = 'extension_name';  
	
    /**
     * Identifier for the column to edit
     * 
     * param string
     */
	protected $_identifierKeys = array( 'extension_name' );
 		
    /**
     * Identifier for the column to edit
     * 
     * @var string
     */
	protected $_tableClass = 'Ayoola_Extension_Import_Table';
	
	
    /**
     * creates the form for creating and editing 
     * 
     * param string The Value of the Submit Button
     * param string Value of the Legend
     * param array Default Values
     */
	public function createForm( $submitValue = null, $legend = null, Array $values = null )
    {
	
		//	Form to create a new page
        $form = new Ayoola_Form( array( 'name' => $this->getObjectName() ) );
		$form->setParameter( array( 'no_fieldset' => true ) );
		$form->oneFieldSetAtATime = true;
		$fieldset = new Ayoola_Form_Element;
		$form->submitValue = $submitValue ;

		do 
		{
			$options = array( 
								'new' => 'Upload new Plugin',
								'update' => 'Update existing Plugin',
							);
			if( @$_REQUEST['extension_name'] )
			{
				$option = new Ayoola_Extension_Import_Table;
				if( $option = $option->selectOne( null, array( 'extension_name' => $_REQUEST['extension_name'] ) ) )
				{
					$fieldset->addElement( array( 'name' => 'extension_name', 'type' => 'Hidden', 'value' => $option['extension_name'] ) );
					$fieldset->addLegend( 'Update Plugin (' . $option['extension_title'] . ')' );
				}
				else
				{
					$fieldset->addLegend( 'Upload new Plugin' );
				}
			}
			else
			{
				$fieldset->addLegend( 'Upload new Plugin' );
			}
		}
		while( false );
		$fieldset->addElement( array( 'name' => 'plugin_url', 'label' => 'Plugin File (.tar.gz archive)', 'data-document_type' => 'application', 'type' => 'Document', 'value' => @$values['plugin_url'] ) );
		$fieldset->addElement( array( 'name' => 'article_url', 'type' => 'hidden', 'value' => @$values['article_url'] ) );
		$form->addFieldset( $fieldset );
		
		$this->setForm( $form );
    } 
	// END OF CLASS
}
