<?php
/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Ayoola_Extension_Import
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Import.php 4.17.2012 7.55am ayoola $
 */

/**
 * @see Ayoola_Extension_Abstract
 */
 
require_once 'Ayoola/Page/Layout/Abstract.php';


/**
 * @category   PageCarton
 * @package    Ayoola_Extension_Import
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Ayoola_Extension_Import extends Ayoola_Extension_Import_Abstract
{
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Import Plugin'; 
	
    /**
     * The method does the whole Class Process
     * 
     */
	protected function init()
    {
		try
		{ 

			$this->createForm( 'Continue', 'Import new plugin' );
			$this->setViewContent( $this->getForm()->view(), true );
            if( ! $values = $this->getForm()->getValues() ){ return false; } 
            $values += $this->getParameter( 'fake_values' ) ? : array();
			unset( $values['extension_id'] );

            //	Import mode
			if( @$values['upload'] )
			{ 
				$result = self::splitBase64Data( $values['upload'] );
				$filter = new Ayoola_Filter_Name();
				$filter->replace = '-';
				$customName = time();   
				$filename = CACHE_DIR . DS . $customName . '.' . $filter->filter( $result['extension'] );
				
				Ayoola_File::putContents( $filename, $result['data'] );
				$newFilename = array_shift( explode( '.', $filename ) ) . '.tar.gz';
				rename( $filename, $newFilename );
				$filename = $newFilename;
			//	var_export( $filename );
			}
			elseif( @$values['plugin_url'] )
			{ 
				$filename = Ayoola_Doc_Browser::getDocumentsDirectory() . DS . $values['plugin_url'];
			}
			elseif( $this->getParameter( 'path' ) )
			{ 
				$filename = $this->getParameter( 'path' );
			}

            //  switching status clears the cache where plugin is sometimes saved
            $tempFile = Ayoola_Doc_Browser::getDocumentsDirectory() . DS . 'plugin-temp.tar.gz';
            Ayoola_Doc::createDirectory( dirname( $tempFile ) );
            copy( $filename, $tempFile );
            $filename = $tempFile;

			if( file_exists( $filename ) )
			{ 
				$export = new Ayoola_Phar_Data( $filename );
                $extensionInfo = json_decode( file_get_contents( $export['extension_information'] ), true );
				if( empty( $extensionInfo['extension_name'] ) )
				{
					return false;
				}

				$result = false;

				try
				{
					$result = $this->getDbTable()->insert( $extensionInfo );
					//$result = $this->insertDb( $extensionInfo );
				}
				catch( Exception $e )
				{

				}


				$dir = @constant( 'EXTENSIONS_PATH' ) ? Ayoola_Application::getDomainSettings( EXTENSIONS_PATH ) : ( APPLICATION_DIR . DS . 'extensions' );
				$dir = $dir . DS . $extensionInfo['extension_name'];
				
				if( $values['extension_name'] )
				{
					if( ! is_dir( $dir ) )
					{
						$this->setViewContent( self::__( '<p class="boxednews badnews">ERROR: DIRECTORY TO UPDATE IS NOT AVAILABLE.</p>.' ) ); 
						return false;
					}
					//	Disable extension
					$class = new Ayoola_Extension_Import_Status( array( 'switch' => 'off', 'extension_name' => $extensionInfo['extension_name'] ) );
					$class->init();
					
					//	to update 
					$update = $extensionInfo;


					unset( $update['extension_name'] );
					$previousData = $this->getDbTable()->selectOne( null, array( 'extension_name' => $extensionInfo['extension_name'] ) );


					//	preserve settings
					$update['settings'] = $previousData['settings'];

					$this->getDbTable()->update( $update, array( 'extension_name' => $extensionInfo['extension_name'] ) );					
					
				}
				else
				{
					if( ! $result )
					{ 
						$this->setViewContent( self::__( '<p class="boxednews badnews">ERROR: COULD NOT SAVE PLUGIN DATA.</p>.' ) ); 
						return false;
					}
                }
                
				if( ! is_dir( $dir ) )
				{
					Ayoola_Doc::createDirectory( $dir );
                }

				$export->extractTo( $dir, null, true );
				unset( $export );
				unlink( $filename );

				$this->setViewContent(  '' . self::__( '<p class="goodnews">Plugin imported successfully. New plugins are deactivated by default when they are imported. <a class="" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Ayoola_Extension_Import_Status/?extension_name=' . $extensionInfo['extension_name'] . '">Turn on!</a></p>' ) . '', true  );
				//var_export( $extensionInfo );

				return true;  
			}
			else
			{
				$this->setViewContent(  '' . self::__( '<p class="badnews">Plugin file not found.</p>' ) . '', true  );
			}
		}
		catch( Exception $e )
		{ 
			$this->getForm()->setBadnews( $e->getMessage() );
			$this->setViewContent( $this->getForm()->view(), true );
			return false; 
		}
    } 

}
