<?php
/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Application_Settings_Abstract
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Abstract.php 5.7.2012 11.53 ayoola $
 */

/**
 * @see Ayoola_Abstract_Playable
 */
 
require_once 'Ayoola/Abstract/Playable.php';

/**
 * @category   PageCarton
 * @package    Application_Settings_Abstract
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

abstract class Application_Settings_Abstract extends Ayoola_Abstract_Table
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
	protected static $_accessLevel = array( 99, 98 );
	
    /**
     * Settings
     * 
     * @var array
     */
	protected static $_settings;
	
    /**
     * Default Database Table
     *
     * @var string
     */
	protected $_tableClass = 'Application_Settings';
	
    /**
     * Identifier for the column to edit
     * 
     * @var array
     */
	protected $_identifierKeys = array( 'settingsname_name' );
	
    /**
     * Calls this after every successful settings change
     * 
     */ 
	public static function callback( $previousData, $newData )
    {

	}
	
	
	
    /**
     * Sets and Returns the setting
     * 
     */
	public static function retrieve( $key = null )
    {
		$class = get_called_class();
        $id = Ayoola_Application::getUrlPrefix() . Ayoola_Application::getApplicationNameSpace() . $class . $key;
		if( isset( self::$_settings[$class][$id] ) )
		{
            return self::$_settings[$class][$id];
        }
        
		$settings = Application_Settings_SettingsName::getInstance();
		if( $settingsNameInfo = $settings->selectOne( null, array( 'class_name' => $class ) ) )
		{
            $settingsNameToUse = $settingsNameInfo['settingsname_name'];
            self::$_settings[$class][$id] = self::getSettings( $settingsNameToUse, $key );
			return self::$_settings[$class][$id];
		}
		elseif( $extensionInfo = Ayoola_Extension_Import_Table::getInstance()->selectOne( null,  array( 'settings_class' => $class ) ) )
		{
            self::$_settings[$class][$id] = self::getSettings( $extensionInfo['extension_name'], $key );
			return self::$_settings[$class][$id];
		}
        self::$_settings[$class][$id] = false;
        return self::$_settings[$class][$id];

	}
	
    /**
     * Sets and Returns the setting
     * 
     */
	public static function getSettings( $settingsName, $key = null )
    {
		$id = Ayoola_Application::getUrlPrefix() . Ayoola_Application::getApplicationNameSpace() . $settingsName . $key;

		if( $settingsName == 'Page' )
		{

		}
		if( is_null( @self::$_settings[$settingsName][$id] ) )
		{
			$settings = Application_Settings::getInstance( $id );
			$settings = $settings->selectOne( null, array( 'settingsname_name' => $settingsName ) );
			if( empty( $settings['data'] ) )
			{ 
				if( ! isset( $settings['settings'] ) )
				{ 
		
					//	Not found in site settings. 
					//	Now lets look in the extension settings
					$table = Ayoola_Extension_Import_Table::getInstance( $id );

					if( ! $extensionInfo = $table->selectOne( null,  array( 'extension_name' => $settingsName ) ) )
					{
						self::$_settings[$settingsName][$id]  = false;

					}

					if( empty( $extensionInfo['settings'] ) )
					{
					//	settings getting lost in the subdomains with username
					//	workaround till we find lasting solution
						$domainSettings = Ayoola_Application::getDomainSettings();
						if( ! empty( $domainSettings['main_domain'] ) && $domainSettings['main_domain'] != $domainSettings['domain_name'] )
						{
							$settings = Application_Settings::getInstance( $id )->selectOne( null, array( 'settingsname_name' => $settingsName ), array( 'disable_cache' => true ) );

							if( ! empty( $settings['data'] ) )
							{
								static::$_settings[$settingsName][$id] = $settings['data'];
							}
							elseif( ! empty( $settings['settings'] ) )
							{
								static::$_settings[$settingsName][$id] = unserialize( $settings['settings'] );

							}
							else
							{
								static::$_settings[$settingsName][$id] = false;
							}
						}

					}
					else
					{
						static::$_settings[$settingsName][$id] =  $extensionInfo['settings'];
					}
				}
				else
				{
					static::$_settings[$settingsName][$id] = unserialize( $settings['settings'] );
				}
					
			}
			else
			{
				static::$_settings[$settingsName][$id] = $settings['data'];
			}
			
		}

		if( static::$_settings[$settingsName][$id] && is_string( static::$_settings[$settingsName][$id] ) )
		{
			static::$_settings[$settingsName][$id] = unserialize( static::$_settings[$settingsName][$id] );  
		}

	//	if( is_array( self::$_settings[$id] ) && array_key_exists( $key, self::$_settings[$id] ) )
		if( ! is_null( $key ) )
		{

			return @self::$_settings[$settingsName][$id][$key];
		}
		else
		{
			return self::$_settings[$settingsName][$id];
		}
    } 
	
    /**
     * creates the form for creating and editing
     * 
     * param string The Value of the Submit Button
     * param string Value of the Legend
     * param array Default Values
     */
	public function createForm( $submitValue = null, $legend = null, Array $values = null )
    {
        $form = new Ayoola_Form( array( 'name' => $this->getObjectName() ) );
		$form->setParameter( array( 'no_fieldset' => true ) );

		do
		{
			$formAvailable = false;

			if( empty( $values['class_name'] ) || ! class_exists( $values['class_name'] ) )
			{
				break;
			}
			$player = new $values['class_name'];
			if( $player instanceof Application_Settings_Interface )
			{
				$fieldsets = $player::getFormFieldsets( $values );
			}
			else
			{
				$player->createForm( null, null, $values );
				$fieldsets = $player->getForm()->getFieldsets();
			}

			foreach( $fieldsets as $fieldset ){ $form->addFieldset( $fieldset ); }
			$formAvailable = true;
			$form->submitValue = 'Save';
			return $this->setForm( $form );
		}
		while( false );
 		$this->setForm( $form );
    } 
	// END OF CLASS
}
