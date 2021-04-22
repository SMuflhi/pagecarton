<?php
/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Ayoola_Doc_Upload_Link
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Link.php 4.17.2012 7.55am ayoola $
 */

/**
 * @see Ayoola_Doc_Upload_Exception   
 */
 
require_once 'Ayoola/Doc/Exception.php';


/**
 * @category   PageCarton
 * @package    Ayoola_Doc_Upload_Link
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)  
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Ayoola_Doc_Upload_Link extends Ayoola_Doc_Upload_Abstract
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
	protected static $_accessLevel = 0;
		
    /**
     * Does the class process
     * 
     */
	public function init()
    {
		try
		{
			$docSettings = Ayoola_Doc_Settings::getSettings( 'Documents' );
			
			//	everyone must have a viewer 
			@$docSettings['allowed_viewers'] = @$docSettings['allowed_viewers'] ? : array();
			@$docSettings['allowed_viewers'][] = 98;	// allow us to user domain owners
			if( ! Ayoola_Abstract_Table::hasPriviledge( @$docSettings['allowed_viewers'] ) )
			{ 
				$message = '';
				$this->_objectData['badnews'][1] = '' . self::__( 'You are not allowed to use the file manager' ) . '';
				$this->setViewContent( $message );
				return false;
			}
			$plainUrl = @$_REQUEST['image_url'] ? : ( $this->getParameter( 'suggested_url' ) ? : $this->getParameter( 'image_url' ) );
			$imageUrl = $plainUrl;    
			$path = Ayoola_Loader::checkFile( 'documents' . $imageUrl );
			if( Ayoola_Application::getUrlPrefix() && $plainUrl[0] === '/' )
			{
				$imageUrl = Ayoola_Application::getUrlPrefix() . $plainUrl;
			}

            switch( @array_pop( explode( '.', strtolower( $imageUrl ) ) ) )
			{
				case 'jpg':
				case 'jpeg':
				case 'png':  
				case 'gif':
					if( $imageUrl AND $imageInfo = Application_Slideshow_Abstract::getImageInfo( $plainUrl ) )
					{
						if( ! empty( $imageInfo['width'] ) && ! empty( $imageInfo['height'] ) )
						{ 
							$imageInfo['image_preview'] = $imageUrl; 
							$imageInfo['suggested_url'] = $plainUrl; 
							if( ! empty( $_REQUEST['crop'] ) )
							{
								$imageInfo['crop'] = true; 
							}
							$imageInfo['preview_text'] = $this->getParameter( 'preview_text' ) . ' ' . @$_REQUEST['preview_text'] . ' ' . $imageInfo['width'] . ' x ' . $imageInfo['height']; 
						}
						$this->setParameter( $imageInfo );
					}
				break;
				case 'ico':
					//	Add support for ico files
					$imageInfo['image_preview'] = $imageUrl; 
					$imageInfo['suggested_url'] = $plainUrl; 
					if( ! empty( $_REQUEST['crop'] ) )
					{
						 $imageInfo['crop'] = true; 
					}
					$this->setParameter( $imageInfo );
				break;
			}
			$name = $this->getParameter( 'field_name' );
			$imageId = 'x' . md5( $name . microtime() );
			$js = null;
			if( ! $this->getParameter( 'ignore_width_and_height' ) )  
			{
				$js .= 'ayoola.image.maxWidth = \'' . $this->getParameter( 'width' ) . '\'; ';
				$js .= 'ayoola.image.maxHeight = \'' . $this->getParameter( 'height' ) . '\';';
			}

            @$suggestedUrl = ( $this->getParameter( 'suggested_url' ) ? : $imageInfo['suggested_url'] );
			if( $plainUrl && ! $suggestedUrl )
			{
				if( $dedicatedUri = Ayoola_Doc::uriToDedicatedUrl( $plainUrl ) )   
				{
					$suggestedUrl = $plainUrl;
				}
			}
			$js .= 'ayoola.image.suggestedUrl = \'' . $suggestedUrl . '\';';
			$js .= 'ayoola.image.cropping.crop = ' . ( $this->getParameter( 'crop' ) ? 'true' : 'false' ) . ';';
				
			//	use image id to ensure only one preview change when update is made
			$js .= 'ayoola.image.imageId = \'' . $imageId . '\';'; 
			
			//	Make the upload link
		    //	Application_Javascript::addFile( '/js/objects/spin.min.js' );
 			Application_Javascript::addCode( 
											'
												ayoola.events.add
												( 
													window, 
													"load", 
													function()
													{
														ayoola.image.setAfterStateChangeCallback( ayoola.image.setStatus );
													} 
												);
											' 
											);
			$jsSetFieldName = 'ayoola.image.fieldName=\'' . $this->getParameter( 'field_name' ) . '\'; ayoola.image.fieldNameValue=\'' . $this->getParameter( 'field_name_value' ) . '\'; ' . $js;
			$optionName = $name . '_option';

            $dropZoneName = $name . '_drop_zone';
			$previewZoneName = $imageId . '_preview_zone';
			$previewImageName = $imageId . '_preview_zone_image'; 

			//	Let the changes to the fieldName changes the preview
 			Application_Javascript::addCode( 
											'
												ayoola.events.add
												( 
													window, 
													"load", 
													function()
													{
														var a = document.getElementsByName( \'' . $name . '\' );
														var previewChanges = function( e )
														{
															var target = a[0] || ayoola.events.getTarget( e );
															//	alert( target );
															var c = document.getElementsByName( \'' . $previewImageName . '\' );
															for( var b = 0; b < c.length; b++ )
															{ 
															//	alert( target );
															//	alert( c[b] );
																c[b].src = target.value;
															}
														}
														var initFormElements = function()
														{
															for( var b = 0; b < a.length; b++ )
															{ 
															//	alert( a[b] );
															//	var f = function(){ previewChanges( a[b] ) }
														
																ayoola.events.add( a[b], "change", previewChanges );
																var d = ayoola.form.elementValueChangeCallbacks["' . $name . '"]
																ayoola.form.elementValueChangeCallbacks["' . $name . '"] = d ? d : [];
																ayoola.form.elementValueChangeCallbacks["' . $name . '"].push( function(){ previewChanges( a[b] ) } );
																
															}
														}
														initFormElements();
														ayoola.xmlHttp.setAfterStateChangeCallback( initFormElements );
													} 
												);
											' 
											);
 			
			$dropZoneJs = ' var a = document.getElementsByName(\'' . $dropZoneName . '\'); for( var b = 0; b < a.length; b++ ){ ayoola.image.setDropZone( a[b] ); a[b].style.display == \'none\' ? a[b].style.display=\'block\' : a[b].style.display=\'none\'; } ';
			$showMenuJs = ' var a = document.getElementsByName(\'' . $optionName . '\'); for( var b = 0; b < a.length; b++ ){ a[b].style.display == \'none\' ? a[b].style.display=\'inline-block\' : a[b].style.display=\'none\'; }  this.style.display=\'inline-block\';  this.innerHTML=\'' . self::__( 'Show/Hide Upload Options' ) . '...\';';

            $uri = $plainUrl;
			$uri = Ayoola_Application::getUrlPrefix() . '/widgets/Application_IconViewer?url=' . $plainUrl;

            if( ! is_string( $uri ) )
			{
				$uri = null;
			}
			$filter = new Ayoola_Filter_FileSize();
			$html = '
				<div title="' . self::__( 'This is a LIVE preview of the selected file' ) . '" style="display:block;clear:both; text-align:center;max-height:80%;" class="" >
					<img name="' . $previewImageName . '" src="' . 
					( ( $uri ? 
					$uri : 
					( is_string( $this->getParameter( 'field_name' ) ) ? 
					@$this->getGlobalValue( $this->getParameter( 'field_name' ) ) : null ) ? : 'http://placehold.it/' . 
					( $this->getParameter( 'width' ) ? : '300' ) . 'x' .
					( $this->getParameter( 'height' ) ? : '300' ) . '&text=' .   
					
					( 'Preview' ) . '' ) ) . '"  class="" onClick="" style="max-height:50vh;"  > 
					<div style="margin:1em; font-size:x-small;">
						' . ( is_file( $path ) ? ( '
						' . self::__( 'FILE URL' ) . ': <a target="_blank" href="' . ( $imageUrl ) . '">' .  $plainUrl . '</a><br>
						' . self::__( 'FILE SIZE' ) . ': ' . $filter->filter( filesize( $path ) ) . '<br>
						' ) : null ) . ' 

						' . ( $this->getParameter( 'width' ) ? ( '
						' . self::__( 'DIMENSION' ) . ': ' . $this->getParameter( 'width' ) . ' / ' . $this->getParameter( 'height' ) . ' <br>
						' ) : null ) . ' 
					</div>
				</div>
				<div title="' . self::__( 'Click here to select a file or drag and drop file here' ) . '" style="text-align:center;" class="" name="upload_through_ajax_link">
					<div title="' . self::__( 'Select an option' ) . '" style="display:block;" >
						<span name="' . @$optionName . '" onClick="' . @$js . ' ' . @$jsSelectElement . ' ' . @$jsSetFieldName . ' ayoola.image.clickBrowseButton( { accept: \'' . $this->getParameter( 'file_types_to_accept' ) . '\', } ); " title="' . self::__( 'Click here to upload a file' ) . '" class="pc-btn"  >
                        ' . self::__( 'Upload new' ) . '
						</span>
						<span name="' . $optionName . '" onClick="' . $js . ' ' . $dropZoneJs . ' ' . $jsSetFieldName . ' " title="" class="pc-btn">
                        ' . self::__( 'Drag N Drop' ) . '
						</span>
					</div>
					<div name="' . $dropZoneName . '" style="max-width:100%;display:none;text-align:center;" title="' . self::__( 'Drag and drop files here' ) . '" class="boxednews centerednews badnews">	
						<img src="' . Ayoola_Application::getUrlPrefix() . '/public/drag_and_drop.png?document_time=1" onClick="" style="max-height:7em;max-width:100%;"  >
					</div>
					<div name="' . $previewZoneName . '" style="max-width:100%;" title="' . self::__( 'Upload preview here' ) . '" class="">	
					</div>
				</div>
			';
			$this->setViewContent( $html );
		}
		catch( Application_Slideshow_Exception $e )
		{ 
			return false; 
		}
	}
	
	// END OF CLASS
}
