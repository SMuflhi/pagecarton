<?php
/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Ayoola_Access_AccessInformation_Abstract
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
 * @package    Ayoola_Access_AccessInformation_Abstract
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

abstract class Ayoola_Access_AccessInformation_Abstract extends Ayoola_Abstract_Table
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
	protected $_tableClass = 'Ayoola_Access_AccessInformation';
	
    /**
     * Key for the id column
     * 
     * @var array
     */
	protected $_identifierKeys = array( 'accessinformation_id' );
	  
	//	This is to implement the abstract method of the parent class. Not all inheriting classes needs a form
	public function createForm( $submitValue = null, $legend = null, Array $values = null )
	{
        $form = new Ayoola_Form( array( 'name' => $this->getObjectName() ) );
	//	$form->oneFieldSetAtATime = true;
		$form->submitValue = $submitValue ;
		$fieldset = new Ayoola_Form_Element;
		$fieldset->addElement( array( 'name' => 'display_name', 'placeholder' => 'e.g. John Smith', 'type' => 'InputText', 'value' => @$values['display_name'] ? : ( Ayoola_Application::getUserInfo( 'firstname' ) . ' ' . Ayoola_Application::getUserInfo( 'lastname' ) ) ) );
		$fieldset->addElement( array( 'name' => 'profile_description', 'placeholder' => 'Enter your profile description here...', 'type' => 'TextArea', 'value' => @$values['profile_description'] ) );
	
		$fieldset->addLegend( "Update profile information..." );
		$form->addFieldset( $fieldset ); 

		//	Profile picture
		$fieldset = new Ayoola_Form_Element; 
	
		//	Cover photo
		$form->addFieldset( $fieldset );
		
		//	Categories
		$i = 0;
		//	Build a separate demo form for the previous group
		$categoryForm = new Ayoola_Form( array( 'name' => 'categories...' )  );
		$categoryForm->setParameter( array( 'no_fieldset' => true, 'no_form_element' => true ) );
		$categoryForm->wrapForm = false;
		do
		{
				
			$categoryFieldset = new Ayoola_Form_Element; 
			$categoryFieldset->allowDuplication = true;
			$categoryFieldset->duplicationData = array( 'add' => '+ Add New Category', 'remove' => '- Remove Category', 'counter' => 'category_counter', );
			$categoryFieldset->container = 'span';
			$categoryFieldset->wrapper = 'white-background';
		
			$categoryFieldset->addElement( array( 'name' => 'post_categories', 'label' => 'Category <span name="category_counter">' . ( $i + 1 ). '</span> of <span name="category_counter_total">' . ( count( @$values['post_categories'] ) ? : 1 ) . '</span>', 'title' => 'Enter category name, e.g. Technology', 'placeholder' => 'e.g. Technology', 'type' => 'InputText', 'multiple' => 'multiple', 'value' => @$values['post_categories'][$i], ) ); 

			$i++;
			$categoryForm->addFieldset( $categoryFieldset );
		}
		while( isset( $values['post_categories'][$i] ) );    

		//	Put the questions in a separate fieldset
		$fieldset = new Ayoola_Form_Element; 
		$fieldset->allowDuplication = false;    
		$fieldset->container = 'span';
		
		//	add previous categories if available
		$fieldset->addElement( array( 'name' => 'post_category_build', 'type' => 'Html', 'value' => '', 'data-pc-element-whitelist-group' => 'post_categories' ), array( 'html' => '<p>Create personal categories to use for posts</p>' . $categoryForm->view(), 'fields' => 'post_categories' ) );	
		
		$categories = array();
		$filter = new Ayoola_Filter_Name();
		$filter->replace = '-';
		foreach( $this->getGlobalValue( 'post_categories' ) ? : array() as $each )
		{
			$categories[] = trim( $filter->filter( strtolower( $each ) ) , '-' );
		}
		$fieldset->addElement( array( 'name' => 'post_categories_id', 'data-pc-element-whitelist-group' => 'post_categories', 'multiple' => 'multiple', 'type' => 'Hidden', 'value' => null, ) ); 			
		$fieldset->addFilter( 'post_categories_id', array( 'DefiniteValue' => array( $categories ) ) ); 
		$form->addFieldset( $fieldset );
		
		
		//	Do we want to just edit some particular fields?
		$fieldsToEdit = array();
		if( ! empty( $_REQUEST['pc_profile_info_to_edit'] ) && is_string( $_REQUEST['pc_profile_info_to_edit'] ) )
		{
			$fieldsToEdit = array_map( 'trim', explode( ',', $_REQUEST['pc_profile_info_to_edit'] ) );
		}
		$form->setParameter( array( 'element_whitelist' => $fieldsToEdit ) );		
		$this->setForm( $form );
	}
	// END OF CLASS
}
