<?php
/**
 * PageCarton 
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Application_Article_Abstract
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Abstract.php 4.17.2012 7.55am ayoola $
 */

/**
 * @see Application_Article_Exception 
 */
 
require_once 'Application/Article/Exception.php';

/**
 * @category   PageCarton
 * @package    Application_Article_Abstract
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

abstract class Application_Article_Abstract extends Ayoola_Abstract_Table
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
     * Url used in "Playing" article posts
     *
     * @var string
     */
	protected static $_postUrl;
	
    /**
     * Options to force on writers or editors
     *
     * @var array
     */
	protected static $_forcedValues;
	
    /**
     * Options to force on writers or editors
     *
     * @var array
     */
	protected static $_optionalValues = array(  );
	
    /**
     * 
     *
     * @var array
     */
	protected static $_otherFormFields = array(  );
	
    /**
     * Identifier for the column to edit
     * 
     * @var array
     */
	protected $_identifierKeys = array( 'article_url' );
	
    /**
     * 
     * 
     * @var array
     */
	protected static $_defaultPostElements = array( 'article', 'cover-photo', 'category' );
	
    /**
     * Error messages to show in List of Posts
     * 
     * @var array
     */
	protected $_badnews = array();
	
    /**
     * Module files directory namespace
     * 
     * @var string
     */
	protected static $_moduleDir = 'articles';	
	
    /**
     * Module files directory namespace
     * 
     * @var string
     */
	protected $_postTable = 'Application_Article_Table';	

    /**
     * 
     * 
     * @var string
     */
	protected static $_itemName;
	
    /**
     * 
     * 
     * @var string
     */
	protected $_idColumn = 'article_url';	
	
    /**
     * Module files directory namespace
     * 
     * @var string
     */
	protected static $editorInitialized;	
	
    /**
     * 
     * 
     * @var bool
     */
	protected static $_postViewed;	
	
    /**
     * Identifier for the column to edit
     * 
     * @var string
     */
	protected $_tableClass = 'Application_Article';	
	
    /**
     * 
     * 
     */
	public static function getViewsCount( array & $data )   
	{
		//	set this using different method
		if( static::$_itemName )
		{
			return intval( $data['views_count_total'] );
		}

		if( ! isset( $data['views_count_total'] ) )
		{
			$data['views_count'] = count( Application_Article_Views::getInstance()->select( null, array( 'article_url' => $data['article_url'] ) ) );
			$data['views_count_total'] =  $data['views_count'];
			$secondaryValues = array( 'article_url' => $data['article_url'], 'views_count_total' => $data['views_count_total'] );
			self::saveArticleSecondaryData( $secondaryValues );
		}
		$data['views_count'] = $data['views_count_total'];
		return intval( $data['views_count_total'] );
	}
	
    /**
     * 
     * 
     */
	public static function getAudioPlayCount( array & $data )   
	{

		//	set this using different method
		if( static::$_itemName )
		{
			return intval( $data['views_count_total'] );
		}
		if( ! isset( $data['audio_play_count_total'] ) )
		{

			$data['audio_play_count'] = count( Application_Article_Type_Audio_Table::getInstance()->select( null, array( 'article_url' => $data['article_url'] ) ) );
			$data['audio_play_count_total'] =  $data['audio_play_count'];
			$secondaryValues = array( 'article_url' => $data['article_url'], 'audio_play_count_total' => $data['audio_play_count_total'] );
			self::saveArticleSecondaryData( $secondaryValues );
		}

		$data['audio_play_count'] = $data['audio_play_count_total'];
		return intval( $data['audio_play_count_total'] );
	}
	
    /**
     * 
     * 
     */
	public static function getDownloadCount( array & $data )   
	{
		if( static::$_itemName )
		{
			return intval( $data['views_count_total'] );
		}
		//	set this using different method
		if( ! isset( $data['download_count_total'] ) )
		{
			$data['download_count'] = count( Application_Article_Type_Download_Table::getInstance()->select( null, array( 'article_url' => $data['article_url'] ) ) );
			$data['download_count_total'] =  $data['download_count'];
			$secondaryValues = array( 'article_url' => $data['article_url'], 'download_count_total' => $data['download_count_total'] );
			self::saveArticleSecondaryData( $secondaryValues );
		}
		$data['download_count'] = $data['download_count_total'];
		return intval( $data['download_count_total'] );
	}
	
    /**
     * 
     * 
     */
	public static function getCommentsCount( array & $data )   
	{
		if( static::$_itemName )
		{
			return intval( $data['views_count_total'] );
		}
		//	set this using different method
		if( ! isset( $data['comments_count_total'] ) )
		{
			$data['comments_count'] = count( Application_CommentBox_Table::getInstance()->select( null, array( 'article_url' => $data['article_url'] ) ) );
			$data['comments_count_total'] =  $data['comments_count'];
			$secondaryValues = array( 'article_url' => $data['article_url'], 'comments_count_total' => $data['comments_count_total'] );
			self::saveArticleSecondaryData( $secondaryValues );
		}
		$data['comments_count'] = $data['comments_count_total'];

		return intval( $data['comments_count_total'] );
	}
	
    /**
     * Compliments the parent
     * 
     * param array Allowed Access Levels
     * return boolean
     */
	public static function isAllowedToEdit( array $data )   
	{
		$articleSettings = Application_Article_Settings::getSettings( 'Articles' );
		$articleSettings['allowed_editors'][] = 98;

		if( 
			self::isOwner( @$data['user_id'] ) 
			|| self::hasPriviledge( $articleSettings['allowed_editors'] ? : 98 ) 
			|| strtolower( Ayoola_Application::getUserInfo( 'username' ) ) === strtolower( $data['username'] )  
		)
		{ 
			return true; 
		}
		return false;
	}
	
    /**
     * Compliments the parent
     * 
     * param array Allowed Access Levels
     * return boolean
     */
	public static function isAllowedToView( array $data )   
	{
		if( $postTypeInfo = Application_Article_Type_Abstract::getOriginalPostTypeInfo( $data['article_type'] ) )
		{

			if( ! empty( $postTypeInfo['view_auth_level'] ) && ! Ayoola_Abstract_Table::hasPriviledge( $postTypeInfo['view_auth_level'] ) )
			{ 
				return false;
			}
		}

		if( 
				( 
					trim( @$data['publish'] )
					|| self::isOwner( @$data['user_id'] ) 
					|| @in_array( 'publish', @$data['article_options'] ) 
					|| strtolower( Ayoola_Application::getUserInfo( 'username' ) ) === strtolower( $data['username']  )
				)
			&&
				(
					self::hasPriviledge( @$data['auth_level'] ) 
					|| strtolower( Ayoola_Application::getUserInfo( 'username' ) ) === strtolower( $data['username']  )
				)
			
		)
		{ 
			return true; 
		}

		return false;
	}
	
    /**
     * 
     * 
     */
	public function filterData( &$data )
    {  

	}
	
    /**
     * 
     * 
     */
	public static function sanitizeData( &$data )
    {  

	}
		
    /**
     * returns the article folder
     * 
     */
	public static function getFolder()  
    {

		return Ayoola_Application::getDomainSettings( APPLICATION_PATH ) . DS . AYOOLA_MODULE_FILES .  DS . static::$_moduleDir; 
	}
	
    /**
     * returns the article folder
     * 
     */
	public static function getBackupFolder()  
    {

		return Ayoola_Application::getDomainSettings( APPLICATION_PATH ) . DS . AYOOLA_MODULE_FILES . DS . 'backup' .  DS . static::$_moduleDir; 
	}
	
    /**
     * returns the article folder
     * 
     */
	public static function getSecondaryFolder()  
    {

		return Ayoola_Application::getDomainSettings( APPLICATION_PATH ) . DS . AYOOLA_MODULE_FILES . DS . 'secondary' .  DS . static::$_moduleDir; 
	}
	
    /**
     * Save the article
     * 
     */
	public static function updateProfile( $values )
    {

/* 		if( $values['profile_url'] )
		{
			//	Let's save some info into the owners account
			if( $profileInfo = Application_Profile_Abstract::getProfileInfo( $values['profile_url'] ) )
			{

			}

			@$profileInfo['posts'] = $profileInfo['posts'] ? : array();

			$profileInfo['posts']['all'][$values['article_url']] = array( 'article_url' => $values['article_url'], 'file_size' => $values['file_size'] );
			$profileInfo['posts']['size'][$values['article_url']] = $values['file_size'];
			$profileInfo['posts_count_all'] = count( $profileInfo['posts']['all'] );
			$profileInfo['posts_file_size'] = array_sum( $profileInfo['posts']['size'] );
			
			if( intval( $values['auth_level'] ) === 97 )
			{
				$profileInfo['posts']['private'][$values['article_url']] = $values['article_url'];
				$profileInfo['posts_count_private'] = count( $profileInfo['posts']['private'] );     
			}

			Application_Profile_Abstract::saveProfile( $profileInfo );  

		}
 */	}
	
    /**
     * Save the article
     * 
     */
	public static function saveArticleSecondaryData( $values )
    {
		$secDir = self::getSecondaryFolder() . $values['article_url'];

		if( $previousData = @json_decode( file_get_contents( $secDir ), true ) )
		{
			$values += $previousData;
		}

		Ayoola_Doc::createDirectory( dirname( $secDir ) );

		$values['has_secondary_data'] = true;   
		Ayoola_File::putContents( $secDir, json_encode( $values ) );  
		return true;
	}
	
    /**
     * Save the article
     * 
     */
	public static function saveArticle( $values )
    {

		if( empty( $values['article_url'] ) )
		{
			return false;
		}
		$values['file_size'] = intval( strlen( var_export( $values, true ) ) );
		
/* 		$validator = new Ayoola_Validator_UserRestrictions();
		$validator->username = $values['username'];
		if( ! $validator->validate( null ) )
		{
			throw new Application_Article_Exception( $validator->getBadnews() );
		}
 */		//  self::updateProfile( $values );
		if( is_file( self::getFolder() . $values['article_url'] ) )
		{
			//	Back up the file before replacing it. 
			$backupFolder = self::getBackupFolder() . $values['article_url'] . DS . time() . '.backup';    
			Ayoola_Doc::createDirectory( dirname( $backupFolder ) );
			copy( self::getFolder() . $values['article_url'], $backupFolder );
		}
		if( ! empty( $values['document_url_base64'] ) ||  ! empty( $values['download_base64'] ) )
		{

			$secondaryValues = array( 'article_url' => $values['article_url'], 'document_url_base64' => $values['document_url_base64'], 'download_base64' => $values['download_base64'], );
			unset( $values['document_url_base64'], $values['download_base64'] );
			$values['has_secondary_data'] = true;   
			self::saveArticleSecondaryData( $secondaryValues );
        }
        if( $values['download_url'][0] === '/' )
        {
            $values['file_size'] = intval( filesize( Ayoola_Doc::getDocumentsDirectory() . @$values['download_url'] ) );
        }
        elseif( stripos( ':', $values['download_url'][0] ) !== false )
        {
            $values['file_size'] = intval( filesize( $values['download_url'][0] ) );
        }
        elseif(  @$values['download_path'] )
        {
            $values['file_size'] = intval( filesize( @$values['download_path'] ) );
        }

        $values['article_modified_date'] = time();
        
        //	we now using json
        Ayoola_File::putContents( self::getFolder() . $values['article_url'], json_encode( $values ) ); 

		// and we want to use tables for sorting categories and all
		$table = Application_Article_Table::getInstance();
		if( $table->select( null, array( 'article_url' => $values['article_url'] ) ) )
		{
			$table->delete( array( 'article_url' => $values['article_url'] ) );
		}
		if( ! empty( $values['profile_url'] ) )
		{
			$values['profile_url'] = strtolower( $values['profile_url'] );
		}
		$table->insert( $values );
	 	return true;

	}
	 
    /**
     * Returns the url used in displaying posts
     * 
     * @param void
     */
	public static function getPostUrl() 
    {
		if( self::$_postUrl ){ return self::$_postUrl; }
		$articleSettings = Application_Article_Settings::getSettings( 'Articles' );
		self::$_postUrl = rtrim( @$articleSettings['post_url'] ? : '/posts/', '/' );
		return self::$_postUrl;
	}
	
    /**
     * Overides the parent class
     * 
     */
	public static function loadPostData( $data )
    {		
		if( is_array( $data ) )
		{
			if( ! empty( $data['article_url'] ) )
			{
				$data = $data['article_url'];
			}
			else
			{
				return false; 
			}
		}
		if( ! is_file( $data ) )
		{
			$data = self::getFolder() . $data;
			if( ! is_file( $data ) )
			{
				return false;
			}
		}

		//	now we using JSON for this
        if( ! $jsonData = json_decode( file_get_contents( $data ), true ) )
        {
            $ex = explode( 'module_files/articles', $data );
            $ex = array_pop( $ex );
            $backupFolder = self::getBackupFolder() . $ex;  
            if( $files = Ayoola_Doc::getFilesRecursive( $backupFolder ) )
            {
                asort( $files );
                $file = array_pop( $files );
                if( ! $jsonData = json_decode( file_get_contents( $file ), true ) )
                {

                }
            }
        }
        
        if( empty( $jsonData ) )
		{
			//	compatibility

			
			//	Check file before it is included.
            // Get the shell output from the syntax check command
            //  this check is causing so much php processes in some cpanels

		
			// Try to find the parse error text and chop it off

		
			// If the error text above was matched, throw an exception containing the syntax error

			if( ! $data || $count > 0 )  
			{
				return false;
			}
			$data = include $data;

		//	if( @$data['has_secondary_data'] )
			{
				$filename = self::getSecondaryFolder() . $data['article_url'];
				if( is_file( $filename ) )
				{

					//	Check file before it is included.
					// Get the shell output from the syntax check command
					$output = shell_exec('php -l "'.$filename.'"');
				
					// Try to find the parse error text and chop it off
					$syntaxError = preg_replace("/Errors parsing.*$/", "", $output, -1, $count);
				
					// If the error text above was matched, throw an exception containing the syntax error

					if( $count > 0 )
					{
						return false;
					}
					if( $data2 = include $filename )
					{
						$data += $data2;
					}

				}
			}
			if( $data )
			{
				//	Change to json

				try
				{
					self::saveArticle( $data );
				}
				catch( Exception $e )
				{
					//	some error came up about table.xml not available.

				}

			}
		}
		else
		{
			$data = $jsonData;
			{
				$filename = self::getSecondaryFolder() . $data['article_url'];
				if( is_file( $filename ) )
				{
					//	Check file before it is included.
				// Get the shell output from the syntax check command
					if( $data2 = json_decode( file_get_contents( $filename ), true ) )
					{
						$data = $data2 + $data;
					}

				}
			}
		}

		$storage = self::getObjectStorage( array( 'id' => __CLASS__ . 'xxweeff', 'device' => 'File', 'time_out' => 10000, ) );
        $presetValues = $storage->retrieve();  
        
		if( ! is_array( $presetValues ) )
		{
			$presetValues = Application_Article_Type::getInstance()->selectOne( null, array( 'post_type_id' => $data['article_type'] ) );
			$storage->store( $presetValues );
		}
		if( ! empty( $presetValues['preset_keys'] ) && ! empty( $presetValues['preset_values'] ) )
		{
            $presetValues = array_combine( $presetValues['preset_keys'], $presetValues['preset_values'] );
            $data = is_array( $data ) ? $data : array();
            $presetValues = is_array( $presetValues ) ? $presetValues : array();
			$data += $presetValues;
		}

		return $data;
	}
	
    /**
     * Overides the parent class
     * 
     */
	public function setIdentifierData( $identifier = NULL )
    {
		// Comes from a file
		if( ! $data = $this->getParameter( 'data' ) )
		{
			$url = Ayoola_Application::getRequestedUri();

			try
			{
				$articleUrl = $this->getIdentifier();
			}
			catch( Exception $e )
			{

			}

			$url = $articleUrl[$this->getIdColumn()] ? : ( @$_GET['article_url'] ? : $url );
			$url = $this->getParameter( 'article_url' ) ? : $url;

			$filename = self::getFolder() . $url;
			$data = self::loadPostData( $filename );
 		}

		if( ! $data || ! is_array( $data ) )
		{
			return false;
		}

		if( get_class( $this ) === 'Application_Article_View' )
		{
			
			$description = trim( $data['article_description'] );
			if( empty( $data['article_description'] ) && ! empty( $data['article_content'] ) )
			{
				$description = substr( strip_tags( $data['article_content'] ), 0, 501 ) . '...';
			}
				//	dont duplicate
			if( ! self::$_postViewed )
			{
				self::$_postViewed = true;

				$pageInfo = array(
					'description' => $description,
					'title' => trim( $data['article_title'] . ' - ' .  Ayoola_Page::getCurrentPageInfo( 'title' ), '- ' )
				);

				Ayoola_Page::setCurrentPageInfo( $pageInfo );

				//	Log into the database 
                self::getViewsCount( $data );
				$table = Application_Article_Views::getInstance();
				$table->insert( array(
										'username' => strtolower( Ayoola_Application::getUserInfo( 'username' ) ),
										'article_url' => $data['article_url'],
										'timestamp' => time(),
								) 
				);
				$secondaryValues = array( 'article_url' => $data['article_url'], 'views_count_total' => @++$data['views_count_total'] );
				self::saveArticleSecondaryData( $secondaryValues );
			}
		}

		$this->_identifierData = $data;  
    } 
	
    /**
     * Article info
     * 
     * @var array
     */
	protected static $_articleInfo;
	
    /**
     * Get the data of the article. Do this to save memory and load time
     * 
     */
	public static function getArticleInfo( $articleUrl = null )
    {
		if( self::$_articleInfo ){ return self::$_articleInfo; }
		$class = new Application_Article_View();
		self::$_articleInfo = $class->getIdentifierData();
		if( ! self::$_articleInfo )
		{ 
            // breaking autopopulation of words 

		}
		return self::$_articleInfo;
    } 
	
    /**
     * Returns Quick link for article
     * 
     */
	public function getQuickPostLinks( array $values = null )
    {
        $links = '';
        if (self::isAllowedToView($values)) 
        {
            $eachPostTypeInfo = Application_Article_Type_Abstract::getOriginalPostTypeInfo($values['article_type']);

            $links .= '<a class="pc-btn" href="' . Ayoola_Application::getUrlPrefix() . '' . $values['article_url'] . '">' . sprintf(self::__('View  %s'), $eachPostTypeInfo['post_type']) . '<i class="fa fa-eye pc_give_space"></i></a>';
            

            $links .= '<a class="pc-btn" href="' . Ayoola_Application::getUrlPrefix() . '/widgets/Application_Article_Creator?article_type=' . $values['article_type'] . '">' . sprintf(self::__('Create new %s post'), $eachPostTypeInfo['post_type']) . '<i class="fa fa-plus pc_give_space"></i></a>';
            $links .= '<a class="pc-btn" href="' . Ayoola_Application::getUrlPrefix() . '/widgets/Application_Share?article_url=' . $values['article_url'] . '">' . sprintf(self::__('Share %s '), $eachPostTypeInfo['post_type']) . '<i class="fa fa-share pc_give_space"></i><i class="fa fa-facebook pc_give_space"></i><i class="fa fa-instagram pc_give_space"></i><i class="fa fa-twitter pc_give_space"></i><i class="fa fa-whatsapp pc_give_space"></i></a>';

            if (! empty($_REQUEST['post_list'])) {
                $post = self::loadPostData($_REQUEST['post_list']);
                if (! in_array($values['article_url'], $post['post_list'])) {
                    $post['post_list'][] = $values['article_url'];
                    self::saveArticle($post);
                    $links .= '' . sprintf(self::__('%s added to %s'), '<a href="' . Ayoola_Application::getUrlPrefix() . '' . $values['article_url'] . '">' . $values['article_title'] . '</a>', '<a href="' . Ayoola_Application::getUrlPrefix() . '' . $post['article_url'] . '">' . $post['article_title'] . '</a>') . '';
                }
            } elseif ($eachPostTypeInfo['article_type'] === 'post-list' || in_array('post-list', $eachPostTypeInfo['post_type_options'])) {
                $links .= '<a class="pc-btn" href="' . Ayoola_Application::getUrlPrefix() . '/widgets/Application_Article_PostList_Sort?article_url=' . $values['article_url'] . '">' . sprintf(self::__('Sort %s'), $eachPostTypeInfo['post_type']) . '<i class="fa fa-sort pc_give_space"></i></a>';
            } else {
                $links .= '<a class="pc-btn" href="' . Ayoola_Application::getUrlPrefix() . '/widgets/Application_Article_PostList_Add?article_url=' . $values['article_url'] . '">' . sprintf(self::__('Add this %s to a list'), $eachPostTypeInfo['post_type']) . '<i class="fa fa-plus pc_give_space"></i></a>';
            }

            if (self::isAllowedToEdit($values)) {
                $links .= '<a class="pc-btn" href="' . Ayoola_Application::getUrlPrefix() . '/widgets/Application_Article_Editor?article_url=' . $values['article_url'] . '">' . sprintf(self::__('Edit %s'), $eachPostTypeInfo['post_type']) . '<i class="fa fa-edit pc_give_space"></i></a>';

                $links .= '<a class="pc-btn badnews" href="' . Ayoola_Application::getUrlPrefix() . '/widgets/Application_Article_Delete?article_url=' . $values['article_url'] . '">' . sprintf(self::__('Delete %s'), $eachPostTypeInfo['post_type']) . '<i class="fa fa-trash pc_give_space"></i></a>';
            }


            if (Ayoola_Page::getPreviousUrl()) {
                $links .= '<a class="pc-btn" href="' . Ayoola_Page::getPreviousUrl() . '"><i class="fa fa-chevron-left pc_give_space"></i>' . sprintf(self::__('Go Back')) . '</a>';
            }
        }
        return $links;

    } 
	
    /**
     * Returns an HTML to display categories
     * 
     * param mixed Category Id
     */
	public static function getCategories( $categoryIds, array $displayOptions = null )
    {
		$html = null;

		$class = Application_Category::getInstance();
		$options = $class->select( null, array( 'category_name' => $categoryIds ) ) ? : array();
		
		//	compatibility
		//$options += $class->select( null, array( 'category_id' => $categoryIds ) ) ? : array();

		$i = 0;
		foreach( $options as $each )
		{
			if( $displayOptions['template'] )
			{
				$html .= str_ireplace( array( '{{{category_url}}}', '{{{category_label}}}', '{{{category_name}}}' ), array( Ayoola_Application::getUrlPrefix() . self::getPostUrl() . '/category/' . $each['category_name'], $each['category_label'], $each['category_name'] ), $displayOptions['template'] );
				$html .= count( $options ) === ++$i ? null : $displayOptions['glue']; 
			}
			else
			{
				$each['category_label'] = @$_GET['category'] === $each['category_name'] ? "<strong> {$each['category_label']} </strong>" : "{$each['category_label']}";
				$html .= '<a style="" href="' . Ayoola_Application::getUrlPrefix() . self::getPostUrl() . '/category/' . $each['category_name'] . '/"> ' . $each['category_label'] . ' </a>';
				$html .= count( $options ) === ++$i ? null : $displayOptions['glue']; 
			}
		}

		return $html;
    } 
	
    /**
     * Sets up JS Required to autoload new posts
     * 
     * @param string Post List ID
     * @param int Post List ID
     * 
     */
	public function autoLoadNewPosts( $postListId, $offset = 0 )
    {

		if( empty( $_GET['pc_post_list_autoload'] ) && ( $this->getParameter( 'pagination' ) || $this->getParameter( 'pc_post_list_autoload' ) ) )
		{

			Application_Javascript::addCode
			( 
				'
				var pc_autoloadPostPageNumber_' . $postListId . ' = "' . $offset . '";
				var pc_autoloadFunc_' . $postListId . ' = function( done ) 
					{
						var a = document.createElement( "div" ); 
						a.innerHTML = "<div title=\"Loading more...\" style=\"text-align: center;\"><img style=\"width:unset;max-width:unset;\" alt=\"Loading more...\" src=\"' . Ayoola_Application::getUrlPrefix() . '/loading.gif?document_time=1\" ></div>";
						var b = document.getElementById( "' . $postListId . '_pagination" );
						b.appendChild( a );
						var url = "' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/name/' . get_class( $this ) . '/?pc_post_list_autoload=1&pc_post_list_id=' . $postListId . '&list_page_number=" + pc_autoloadPostPageNumber_' . $postListId . ';
						var ajax = ayoola.xmlHttp.fetchLink( { url: url, container: b, noSplash: true, insertBefore: true } );
						var v = function()
						{
							if( ayoola.xmlHttp.isReady( ajax ) )
							{	
								var b = document.getElementById( "' . $postListId . '_pagination" );
								b.innerHTML = "";
								if( ! ajax.responseText )
								{ 
									return false;
								}
								if( ajax.responseText.indexOf( "pc_no_post_to_show" ) > -1 )
								{ 
									return false;
								}
								
								pc_autoloadPostPageNumber_' . $postListId . '++;
								done();
							}		
						}	
						ayoola.events.add( ajax, "readystatechange", v );
						// 1. fetch data from the server
						// 2. insert it into the document
						// 3. call done when we are done

					};		
				var options = 
				{
					distance: ' . ( $this->getParameter( 'autoload_distance' ) ? : 5000 ) . ',
					callback: pc_autoloadFunc_' . $postListId . '
				} 
					
				// setup infinite scroll

				' 
			);	
		}
    }
    
    
		
    /**
     * 
     * 
     */
	public static function getMyAuthProfiles()
    {
        if( ! self::hasPriviledge( $articleSettings['allowed_editors'] ) )
        {
            $profiles = Application_Profile_Abstract::getMyProfiles();
            if( ! empty( $values['profile_url'] ) && ! in_array( $values['profile_url'], $profiles ) )
            {
                $profiles[] = $values['profile_url'];
            }
            $profiles = array_combine( $profiles, $profiles );
        }
        else
        {
            $table = "Application_Profile_Table";
            $table = $table::getInstance( $table::SCOPE_PRIVATE );
            $table->getDatabase()->getAdapter()->setAccessibility( $table::SCOPE_PRIVATE );
            $table->getDatabase()->getAdapter()->setRelationship( $table::SCOPE_PRIVATE );
            $profiles = $table->select( null, null, array( 'workaround-to-avoid-cache' ) );
            $filter = new Ayoola_Filter_SelectListArray( 'profile_url', 'display_name' );
            $profiles = $filter->filter( $profiles );
        }

        return $profiles;    
    }

		
    /**
     * Returns form for quiz post questions
     * 
     * @return Ayoola_Form
     * 
     */
	public static function quizQuestions( $values, $groupIds = null, $j = null )
    {

        $i = 0; // question count

        //	Build a separate demo form for the previous group
        $questionForm = new Ayoola_Form( array( 'name' => 'questions...' )  );
        $questionForm->setParameter( array( 'no_fieldset' => true, 'no_form_element' => true ) );
        $questionForm->wrapForm = false;  
        $authProfiles = self::getMyAuthProfiles();
        do
        {
            //	Put the questions in a separate fieldset
            $questionFieldset = new Ayoola_Form_Element; 
            $questionFieldset->allowDuplication = true;
            $questionFieldset->duplicationData = array( 'add' => '+ Add New Question Below', 'remove' => '- Remove Above Question', 'counter' => 'question_counter' . $j . '', );
            $questionFieldset->container = 'span';

            $questionFieldset->wrapper = 'white-background';								

            //  who added this?
            $defaultProfile = Application_Profile_Abstract::getMyDefaultProfile();
            $defaultProfile = $defaultProfile['profile_url'];
    
            $questionFieldset->addElement( array( 'name' => 'question_profile_url', 'multiple' => 'multiple', 'onchange' => 'ayoola.div.manageOptions( { database: "Application_Profile_Table", listWidget: "Application_Profile_ShowAll", values: "profile_url", labels: "display_name", element: this } );', 'label' => 'Post Question As', 'type' => count( $profiles ) > 1 ? 'Select' : 'Hidden', 'value' => @$values['question_profile_url'] ? : $defaultProfile ), $authProfiles + array( '__manage_options' => '[Manage Profiles]' ) );

            $questionFieldset->addElement( array( 'name' => 'quiz_question' . @$groupIds[$j], 'data-html' => '1', 'data-pc-element-whitelist-group' => 'questions_and_answers', 'multiple' => 'multiple', 'rows' => '1', 'label' => ( 'Question <span name="question_counter' . $j . '">' . ( $i + 1 ) . '</span> of <span name="question_counter' . $j . '_total">' . ( count( @$values['quiz_question' . @$groupIds[$j]] ) ? : 1 ) . '</span>' ), 'placeholder' => 'Enter question here...', 'title' => 'Double-Click here to launch the advanced editor', 'type' => 'TextArea', 'value' => @$values['quiz_question' . $groupIds[$j]][$i] ? : Ayoola_Form::getGlobalValue( 'quiz_question' . @$groupIds[$j], null , $i ) ) );  

            //	Option 1
            $questionFieldset->addElement( array( 'name' => 'quiz_option1' . @$groupIds[$j], 'data-pc-element-whitelist-group' => 'questions_and_answers', 'data-html' => '1', 'multiple' => 'multiple', 'rows' => '1', 'label' => 'First Option', 'placeholder' => 'Enter option 1', 'type' => 'TextArea', 'value' => @$values['quiz_option1' . $groupIds[$j]][$i] ? : Ayoola_Form::getGlobalValue( 'quiz_option1' . @$groupIds[$j], null , $i ) ) );

            //	Option 2
            $questionFieldset->addElement( array( 'name' => 'quiz_option2' . @$groupIds[$j], 'data-pc-element-whitelist-group' => 'questions_and_answers', 'data-html' => '1', 'multiple' => 'multiple', 'rows' => '1', 'label' => 'Second Option', 'placeholder' => 'Enter option 2', 'type' => 'TextArea', 'value' => @$values['quiz_option2' . @$groupIds[$j]][$i] ? : Ayoola_Form::getGlobalValue( 'quiz_option2' . @$groupIds[$j], null , $i ) ) );
            
            //	Option 3
            $questionFieldset->addElement( array( 'name' => 'quiz_option3' . @$groupIds[$j], 'data-pc-element-whitelist-group' => 'questions_and_answers', 'data-html' => '1', 'multiple' => 'multiple', 'rows' => '1', 'label' => 'Third Option', 'placeholder' => 'Enter option 3', 'type' => 'TextArea', 'value' => @$values['quiz_option3' . $groupIds[$j]][$i] ? : Ayoola_Form::getGlobalValue( 'quiz_option3' . @$groupIds[$j], null , $i ) ) );

            //	Option 4
            $questionFieldset->addElement( array( 'name' => 'quiz_option4' . @$groupIds[$j], 'data-pc-element-whitelist-group' => 'questions_and_answers', 'data-html' => '1', 'multiple' => 'multiple', 'rows' => '1', 'label' => 'Fourth Option', 'placeholder' => 'Enter option 4', 'type' => 'TextArea', 'value' => @$values['quiz_option4' . $groupIds[$j]][$i] ? : Ayoola_Form::getGlobalValue( 'quiz_option4' . @$groupIds[$j], null , $i ) ) );

            //	Solution
            $questionFieldset->addElement( array( 'name' => 'quiz_answer_notes' . @$groupIds[$j], 'data-pc-element-whitelist-group' => 'questions_and_answers', 'data-html' => '1', 'multiple' => 'multiple', 'rows' => '1', 'label' => 'Answer Notes and Workings', 'placeholder' => 'Enter the information that will be displayed to user as the answer workings...', 'type' => 'TextArea', 'value' => @$values['quiz_answer_notes' . $groupIds[$j]][$i] ? : Ayoola_Form::getGlobalValue( 'quiz_answer_notes' . @$groupIds[$j], null , $i ) ) );
            
            //	Correct Answer
            $questionFieldset->addElement( array( 'name' => 'quiz_correct_option' . @$groupIds[$j], 'data-pc-element-whitelist-group' => 'questions_and_answers', 'multiple' => 'multiple', 'label' => 'Correct Option', 'placeholder' => '', 'type' => 'Select', 'value' => @$values['quiz_correct_option' . $groupIds[$j]][$i] ? : Ayoola_Form::getGlobalValue( 'quiz_correct_option' . @$groupIds[$j], null , $i ) ), array_combine( range( 1, 4 ), range( 1, 4 ) ) );

            //	We need to save the keys to use later so this information may save in the real fieldset
            $i++;
            
            $questionForm->addFieldset( $questionFieldset );
        }
        while( isset( $values['quiz_question' . @$groupIds[$j]][$i] ) );
        return $questionForm;
    }

    /**
     * Returns an HTML to display #hashtags
     * 
     */
	public static function getHashTags( $values )
    {
		$html = null;
		$html .= ' <ul style="list-style:none;display:inline-block;margin-left:0;"> ';

		foreach( $values as $each )
		{
			$filter = new Ayoola_Filter_Name();
			$filter->replace = '-';
			$value = $filter->filter( strtolower( $each ) );
			if( ! $value ){ continue; }
			$html .= ' <li style="display:inline-block;">';
			$url = '' . self::getPostUrl() . '/tag/' . $value . '/';
			$content = ' <a href="' . $url . '"> #' . $each . ' </a> ';
			$html .= @$_GET['tag'] === $value ? "<strong> {$content} </strong>" : "<span> {$content} </span>";
			
			$html .= ' </li> ';
		}
		$html .= ' </ul> ';

		return $html;
    } 
	
    /**
     * Returns an HTML to display footer for messages
     * 
     */
	public static function getDefaultPostView( $data )
    {

		if( ! $image = Ayoola_Doc::uriToDedicatedUrl( @$data['document_url_uri'] ) )
		{
			$image = $data['document_url_cropped'] ;
		}
		$link = 'a title="View ' . $data['article_title'] . '" style="color:inherit;" href="' . Ayoola_Application::getUrlPrefix() . $data['article_url'] . '"';
		$realPost = true;
		if( stripos( $data['article_url'], '/tools/classplayer') === 0 )
		{
			$link .= ' onclick="this.href=\'javascript:\';ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . $data['article_url'] . '\', \'page_refresh\' );"';
			$realPost = false;
		}
		$header = 'h2';
		if( $data['article_url'] === Ayoola_Application::getPresentUri() )
		{
			$link = 'span';
			$header = 'h1';
		}

		$html = null;
		$html .= '<div style="-webkit-box-shadow: 0 10px 6px -6px #777;-moz-box-shadow: 0 10px 6px -6px #777;box-shadow: 0 10px 6px -6px #777; margin-bottom:3em;">';
		$html .= '<' . $link . '>';
		$html .= '<div  class="pc_theme_parallax_background" style="background-image: linear-gradient(      rgba(0, 0, 0, 0.7),      rgba(0, 0, 0, 0.7)  ),    url(\'' . $image . '\'); ">';
        $html .= $data['css_class_of_inner_content'] ? '<div class="' .$data['css_class_of_inner_content'] . '">' : null;
		$html .= '<div style="float:right;background-color:#000;padding:10px;border-radius:10px;">' . @$data['post_type'] . '</div>';
		$html .= '<' . $header . '>' . $data['article_title'] . '</' . $header . '>';
		$html .= @$data['article_description'] ? '<br><p>' . $data['article_description'] . '</p>' : null;
		$html .= $realPost && $data['button_value'] ? '<br><p><button class="pc-btn"> ' . $data['button_value'] . ' </button></p>' : null;
        $html .= $data['css_class_of_inner_content'] ? '</div>' : null;
		$html .= '</div>';
		$html .= '</' . $link . '>';
		$html .= '<div class="pc_theme_parallax_background" style="font-size:x-small;text-transform:uppercase;background-image: linear-gradient(      rgba(0, 0, 0, 0.5),      rgba(0, 0, 0, 0.5)  ); ">';
        $html .= $data['css_class_of_inner_content'] ? '<div class="' .$data['css_class_of_inner_content'] . '">' : null;

		$html .= @$data['item_old_price'] ? '
		<span style="font-size:small;">
		<span class="pc_posts_option_items" style="text-decoration:line-through;" >' . $data['item_old_price'] . '</span> 
		</span>
		' : null;
		$html .= @$data['item_price'] ? '
		<span style="font-size:small;">
		<span class="pc_posts_option_items" >' . $data['item_price_with_currency'] . '</span> 
		</span>
		' : null;
		$html .= '<span class="pc_posts_option_items">' . self::filterTime( $data ) . '</span>';
		if( ! empty( $data['profile_url'] ) )
		{
			$html .= ( '<a href="' . Ayoola_Application::getUrlPrefix() . '/' . $data['profile_url'] . '" class="pc_posts_option_items"> Posted By ' . ( @$data['display_name'] ? : $data['profile_url'] ) . '</a>' );
		}
		
		if( isset( $data['views_count'] ) )
		{
			$html .= '<span class="pc_posts_option_items">' . $data['views_count'] . ' <span >views</span></span>';
		}
		if( isset( $data['download_count'] ) )
		{
			$html .= '<span class="pc_posts_option_items">' . $data['download_count'] . ' <span >downloads</span></span>';
		}
		if( isset( $data['audio_play_count'] ) )
		{
			$html .= '<span class="pc_posts_option_items">' . $data['audio_play_count'] . ' <span >plays</span></span>';
		}
		$html .= $data['category_text'] ? '<span class="pc_posts_option_items"> in ' . $data['category_text'] . ' </span>' : null;  

		$html .= self::isAllowedToEdit( $data ) && $realPost ? '  
		<a  class="pc_posts_option_items" onclick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_Article_Editor/?article_url=' . $data['article_url'] . '&\', \'page_refresh\' );" href="javascript:"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> 
		<a  class="pc_posts_option_items" onclick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_Article_Delete/?article_url=' . $data['article_url'] . '&\', \'page_refresh\' );" href="javascript:"> delete </a>
		' : null;
		
		$shareLink = '
		<a class="a2a_dd" href="#">Share</a>
		<script>
		var a2a_config = a2a_config || {};
		a2a_config.linkurl = "' . Ayoola_Page::getCanonicalUrl( $data['article_url'] ) . '";
		</script>
		<script async src="https://static.addtoany.com/menu/page.js"></script>
		<!-- AddToAny END -->
		';
		$html .= '<span style="display: inline-block; color:inherit; margin-right:2em;">' . $shareLink . ' </span>';  

        $html .= $data['css_class_of_inner_content'] ? '</div>' : null;
		$html .= '</div>';
		$html .= '</div>';
		return $html;
	}
	
    /**
     * Returns an HTML to display footer for messages
     * 
     */
	public static function isDownloadable( &$data )
    {
		if( @$data['download_url'] )
		{
			return true;
		}
		elseif( @$data['download_path'] )
		{
			return true;
		}
		elseif( @$data['download_base64'] )
		{
			return true;
		}
		return false;
	}
	
    /**
     * Returns an HTML to display footer for messages
     * 
     */
	public static function filterTime( $data )
    {
		$filter = new Ayoola_Filter_Time();
		$html = null;

		if( @$data['article_creation_date'] )
		{

			$html .= $filter->filter( $data['article_creation_date'] );
		}
		else
		{

			$html .= $filter->filter( @$data['article_modified_date'] ? : ( time() - 3 ) ); 
		}
		return $html;
	
	}
	
    /**
     * Returns an HTML to display footer for messages
     * 
     */
	public static function getFooter( $data )
    {
		$html = null;
		$html .= '<ul style="margin:0;padding:0;list-style:none;display:inline-block;">';
		{
			$html .= '<li style="display:inline-block;margin:0.5em;" title="Articles not published are only visible to the writer.">' . self::filterTime( $data ) . '</li>';
			$html .= '<strong>Published:</strong><li style="display:inline-block;margin:0.5em;">' . ( $data['publish'] ? 'Yes' : 'No' ) . '</li>';
		}
		$html .= '</ul>';

		return $html;
    }
	
    /**
     * Returns an HTML to display footer for messages
     * 
     */
	public static function initHTMLEditor()
    {
		if( self::$editorInitialized )
		{
			return false;
		}
		$allowedCoders =  Application_Settings_Abstract::getSettings( 'Forms', 'coders_access_group' ); 
		if( ! Ayoola_Form::hasPriviledge( $allowedCoders ? : 98 ) )
		{
			return false;
		}
		self::$editorInitialized = true;
		
		Application_Javascript::addFile( '/js/objects/ckeditor/ckeditor.js' );
		Application_Javascript::addCode 
		(  
			'ayoola.xmlHttp.setAfterStateChangeCallback
			( 
				function()
				{ 
					try
					{
						//	destroy all instances of ckeditor everytime state changes.
						for( name in CKEDITOR.instances )
						{
							CKEDITOR.instances[name].destroy();
						}
					}
					catch( e )
					{
					
					}
				}
			)' 
		);
		Application_Javascript::addCode
		( 
			'ayoola.events.add
			( 
				window, "load", 
				function()
				{ 
					ayoola.xmlHttp.callAfterStateChangeCallbacks();
				} 
			);' 
		
		);
		Application_Javascript::addCode 
		(  
			'ayoola.xmlHttp.setAfterStateChangeCallback
			( 
				function()
				{ 
					//	Retrieve all the stylesheets in the doc and attach them to the editor
					var a = document.getElementsByTagName( "link" );
					var d = new Array();
					for( var b = 0; b < a.length; b++ )
					{
						if( ! a[b].href.search( /css/ ) || a[b].href.search( /css/ ) == -1 ) 
						{ 
							continue; 
						}
						
						d.push( a[b].href );
					}

					var a = document.getElementsByTagName( "textarea" );

					var initCKEditor = function( target )
					{
						CKEDITOR.plugins.addExternal( "uploadimage", "' . Ayoola_Application::getUrlPrefix() . '/js/objects/ckeditor/plugins/uploadimage/plugin.js", "" );
						CKEDITOR.plugins.addExternal( "confighelper", "' . Ayoola_Application::getUrlPrefix() . '/js/objects/ckeditor/plugins/confighelper/plugin.js", "" );
						CKEDITOR.config.extraPlugins = "confighelper,uploadimage,autogrow,tableresize,codesnippet";
						CKEDITOR.config.removePlugins = "maximize,resize,elementspath";
						CKEDITOR.config.allowedContent  = true;
						CKEDITOR.config.filebrowserUploadUrl = "' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Ayoola_Doc_Upload_Ajax/?";  
						CKEDITOR.replace
						( 
							target,
							{
								height: 50,
								toolbar : 
								[
									{ name: "insert", items: [ "Image", "Table", "SpecialChar", "CodeSnippet" ] },
									{ name: "basicstyles", groups: [ "basicstyles", "cleanup" ], items: [ "Bold", "Italic", "Underline", "Strike", "Subscript", "Superscript", "-", "RemoveFormat" ] },
									{ name: "paragraph", groups: [ "list", "indent", "blocks", "align" ], items: [ "NumberedList", "BulletedList", "-", "Blockquote", "-", "JustifyLeft", "JustifyCenter", "JustifyRight", "JustifyBlock", "-" ] },
									{ name: "links", items: [ "Link", "Unlink" ] },
									{ name: "styles", items: [ "Format", "Font", "FontSize" ] },
									{ name: "colors", items: [ "TextColor", "BGColor" ] },
								],
								autoGrow_minHeight : 50,
								autoGrow_maxHeight : 400,
							}
						);
						CKEDITOR.config.codeSnippet_theme = "pojoaque";
					}
					var f = function( e )
					{

						try
						{
							try
							{
								//	destroy all instances of ckeditor everytime state changes.
								for( name in CKEDITOR.instances )
								{
									CKEDITOR.instances[name].destroy();
								}
							}
							catch( e )
							{
							
							}
							var target = ayoola.events.getTarget( e );

							initCKEditor( target );
						}
						catch( e )
						{
							//	throws exception if article content is not available
						}
					}
					for( var b = 0; b < a.length; b++ )
					{

						switch( a[b].name  )
						{
							case "article_content":
							case "' . Ayoola_Form::hashElementName( 'article_content' ) . '":
								initCKEditor( a[b] );
							break;
							default:

								if( ! a[b].getAttribute( "data-html" ) && a[b].getAttribute( "data-document_type" ) != "html" )
								{
									break;
								}

								ayoola.events.add( a[b], "dblclick", f );
							break;
						}
					}
				}
			)' 
		);
		return true;
    } 
	
    /**
     * creates the form
     * 
     * param string The Value of the Submit Button
     * param string Value of the Legend
     * param array Default Values
     */
	public function createForm( $submitValue = null, $legend = null, Array $values = null )
    {
		//	Form to create a new page
		//	What form can we use?
		@$formToUse = $values['form_name'] ? : $_REQUEST['form_name'];
		@$formToUse = $values['form_name'] ? : $this->fakeValues['form_name'];

 		$form = new Ayoola_Form( array( 'name' => $this->getObjectName(), 'data-not-playable' => true, 'id' => $this->getObjectName() . @$values['article_url'] ) );   		
		$form->setParameter( array( 'no_fieldset' => true ) );  
		
		//	Do we want to just edit some particular fields?
		$fieldsToEdit = array();
		if( ! empty( $_REQUEST['pc_post_info_to_edit'] ) && is_string( $_REQUEST['pc_post_info_to_edit'] ) )
		{
			$fieldsToEdit = array_map( 'trim', explode( ',', @$_REQUEST['pc_post_info_to_edit'] ) );
			$fieldsToEdit[] = 'article_type';
			$fieldsToEdit[] = 'true_post_type';
		}
		
		$form->oneFieldSetAtATime = $this->hashFormElementName;   
		$form->oneFieldSetAtATime = false;   
		$form->submitValue = 'Save' ;
		$fieldset = new Ayoola_Form_Element;
		$fieldset->hashElementName = $this->hashFormElementName;
		if( $formToUse && ! @$_REQUEST['default_form'] )
		{
			$class = new Ayoola_Form_View( array( 'no_init' => true ) );
			$class->setParameter( array( 'default_values' => $values, 'form_name' => $formToUse ) );

			$class->createForm( 'Create Post', ''  );

			$form2 = $class->getForm();
			$fieldsets = $form2->getFieldsets();

			$form->requiredElements = is_array( $form->requiredElements ) ? $form->requiredElements : array() + is_array( $form2->requiredElements ) ? $form2->requiredElements : array();
			$form->setParameter( $form2->getParameter() );
			foreach( $fieldsets as $key => $each ) 
			{
			//	var_export( $key )
				
				$each->hashElementName = $this->hashFormElementName;
				$each->addElement( array( 'name' => 'form_name', 'type' => 'Hidden', 'value' => @$formToUse ) );
				$form->addFieldset( $each );
			}
			$this->setForm( $form );
			return;
		}
		else
		{
			//	let the form_name be saved if available

		}
		
		$articleSettings = Application_Article_Settings::getSettings( 'Articles' );

		//	Let's know the kind of post that we are working on.

		@$values['article_type'] = $this->getParameter( 'article_types' ) ? : $values['article_type'];   
		if( ! @$values['article_type'] || ! empty( $_REQUEST['article_type'] ) )
		{ 

			@$values['article_type'] = $_REQUEST['article_type'] ? : $values['article_type']; 
			@$values['article_type'] = $_REQUEST[Ayoola_Form::hashElementName( 'article_type')] ? : $values['article_type']; 
		}
		$values['article_type'] = $values['article_type'] ? : $this->getGlobalValue( 'article_type' ); 

		
		$postTypesAvailable = Application_Article_Type_TypeAbstract::getMyAllowedPostTypes();

		if( ! empty( $_REQUEST['article_type'] ) )
		{
			if( empty( $postTypesAvailable[$_REQUEST['article_type']] ) )
			{
				$postTypesAvailable[$_REQUEST['article_type']] = ucwords( str_replace( '-', ' ', $_REQUEST['article_type'] ) );
			}
			
		}

		if( ! empty( $values['article_type'] ) )
		{
			if( empty( $postTypesAvailable[$values['article_type']] ) )
			{
				$postTypesAvailable[$values['article_type']] = ucwords( str_replace( '-', ' ', $values['article_type'] ) );
			}
			
		}

		$tempOptions = array_keys( $postTypesAvailable );

		$articleTypeWeUsing = $values['article_type'] ? : array_shift( $tempOptions );
		
		//	Check if post type is registered in the post db

		if( $postTypeInfo = Application_Article_Type_Abstract::getOriginalPostTypeInfo( $articleTypeWeUsing ) )
		{

			if( ! empty( $postTypeInfo['auth_level'] ) && ! Ayoola_Abstract_Table::hasPriviledge( $postTypeInfo['auth_level'] ) )
			{ 
				//	Current user not authorized to use this post type
				$postTypeInfo = array();
				header( 'Location: ' . Ayoola_Application::getUrlPrefix() . '/404' );
				exit();
			}

			$values['true_post_type'] = $postTypeInfo['article_type'];
			$values['post_type'] = $postTypeInfo['post_type'];
		}
		else
		{
			$values['true_post_type'] = $values['article_type'];
			$values['post_type'] = @$postTypesAvailable[$values['article_type']] ? : $values['article_type'];
		}

		if( ! empty( $_REQUEST['article_type'] ) && ( ! empty( $_REQUEST['true_post_type'] ) ||  ! empty( $_REQUEST['post_type_custom_fields'] ) ) && self::hasPriviledge( array( 99, 98 ) ) )
		{
			//	auto setup post type
			$postTypeInfoForThisPost = array( 
									'post_type' => $articleTypeWeUsing, 
									'article_type' => @$_REQUEST['true_post_type'], 
									'post_type_custom_fields' => @$_REQUEST['post_type_custom_fields'], 
									'post_type_options' => @array_map( 'trim', explode( ',', $_REQUEST['post_type_options'] ) ), 
									'post_type_options_name' => @array_map( 'trim', explode( ',', $_REQUEST['post_type_options_name'] ) ), 
                                );
                                
            if( ! $postTypeInfo )
            {
                $classToCreatePostType = new Application_Article_Type_Creator( array( 'fake_values' => array( 'post_type' => ucwords( str_replace( array( '-', ' ' ), ' ', $articleTypeWeUsing ) ) ) + $postTypeInfoForThisPost ) );
                $result = $classToCreatePostType->view();
                $values['true_post_type'] = $postTypeInfoForThisPost['article_type'];
                $values['post_type'] = $postTypeInfoForThisPost['post_type'];
                $postTypeInfo = $postTypeInfoForThisPost; 
            }
            elseif( 
                strtolower( $postTypeInfoForThisPost['article_type'] ) != strtolower( $postTypeInfo['article_type'] )
                || $postTypeInfoForThisPost['post_type_custom_fields'] != $postTypeInfo['post_type_custom_fields'] 
                || $postTypeInfoForThisPost['post_type_options'] != $postTypeInfo['post_type_options'] 
                || $postTypeInfoForThisPost['post_type_options_name'] != $postTypeInfo['post_type_options_name'] 
                )
            {

                $postTypeInfo = $postTypeInfoForThisPost + $postTypeInfo; 
                $toUpdate = $postTypeInfo;
                unset( $toUpdate['post_type'] );
                $response = Application_Article_Type::getInstance()->update( $toUpdate, array( 'post_type_id' => $articleTypeWeUsing ) );

            }

		}

		$values['post_type'] = $values['post_type'] ? : $articleTypeWeUsing;
		$values['true_post_type'] = $values['true_post_type'] ? : $articleTypeWeUsing;

		$typeDisplay = 'Select';
		if( ! empty( $_REQUEST['article_type'] ) )
		{
			$typeDisplay = 'Hidden';
        }
        asort( $postTypesAvailable );

		$fieldset->addElement( array( 'name' => 'article_type', 'label' => 'Post Type', 'onchange'=> 'window.location.search += \'&article_type=\' + this.value + \'\';', 'type' => $typeDisplay, 'value' => $articleTypeWeUsing ), $postTypesAvailable );

		$fieldset->addElement( array( 'name' => 'true_post_type', 'type' => 'Hidden', 'value' => @$values['true_post_type'] ? : @$values['article_type'] ) );
		
		$postTypeLabel = $this->getParameter( 'post_type_label' ) ? : ucwords( $values['post_type'] );
		
		//	Title
		$fieldset->addElement( array( 'name' => 'article_title', 'label' => $postTypeLabel . ' Title', 'placeholder' => 'Enter a title for the ' . $postTypeLabel . ' here...', 'type' => 'InputText', 'value' => @$values['article_title'] ) );

		$fieldset->addRequirement( 'article_title', array( 'WordCount' => array( 3,200 ) ) );
		$fieldset->addRequirement( 'article_description', array( 'WordCount' => array( 0, 5000 ) ) );
	
		//	addRequirements
	
		//	options
		$options =  array( 
							'article' => 'Write an article about this', 
							'publish' => 'Publish (Allow the public to see)', 
							'requirement' => 'Request viewers of this post to provide some information about them', 
							'keywords' => 'Enter keywords for this post', 
						);

		if( ! @$values['article_options'] )
		{
			$values['article_options'] = array();
			if( ! isset( $values['publish'] ) || @$values['publish'] )
			{
				$values['article_options'][] = 'publish';
			}
			if( @$values['article_content'] )
			{
				$values['article_options'][] = 'article';
			}
			if( @$values['article_tags'] )
			{
				$values['article_options'][] = 'keywords';
			}
			if( @$values['article_requirements'] )
			{
				$values['article_options'][] = 'requirement';
			}
			
		}
	
		$fieldset->addLegend( $legend );
		$form->addFieldset( $fieldset ); 

		//	supplementary form

		if( ! empty( $postTypeInfo['supplementary_form'] ) )
		{
			$supplementaryForm = $postTypeInfo['supplementary_form'];
			$parameters = array( 'form_name' => $supplementaryForm, 'default_values' => $values );  

			$orderFormClass = new Ayoola_Form_View( $parameters );
			
			if( $orderFormClass->getForm() )
			{
				foreach( $orderFormClass->getForm()->getFieldsets() as $each )  
				{
					$form->addFieldset( $each );
				}
				$form->submitValue = 'Save...';
			}
		}
        if( ! empty( $postTypeInfo['view_widget'] ) && Ayoola_Object_Embed::isWidget( $postTypeInfo['view_widget'] ) )
		{
			$supplementaryForm = $postTypeInfo['supplementary_form'];
            $parameters = array( 'default_values' => $values );  

			$orderFormClass = new $postTypeInfo['view_widget']( $parameters );
			$orderFormClass->createForm( null, null, $values );
			if( $orderFormClass->getForm() )
			{
				foreach( $orderFormClass->getForm()->getFieldsets() as $each )  
				{
					$form->addFieldset( $each );
				}
				$form->submitValue = 'Save...';
			}
		}

		$fieldset = new Ayoola_Form_Element;
		$fieldset->hashElementName = $this->hashFormElementName;

		//	internal forms to use
		$features = is_array( @$postTypeInfo['post_type_options'] ) && ( count( $postTypeInfo['post_type_options'] ) !== 1 || $postTypeInfo['post_type_options'][0] !== '' ) ? $postTypeInfo['post_type_options'] : static::$_defaultPostElements;  

		$featuresPrefix = is_array( @$postTypeInfo['post_type_options_name'] ) ? $postTypeInfo['post_type_options_name'] : array();

		//	compatibility so article_description may be editable
		//	when it is no longer in the post type info
		if( ! in_array( 'description', $features ) && @$values['article_description'] )
		{
			$features[] = 'description';
			$featuresPrefix[] = '';
		}
		if( ! in_array( @$values['true_post_type'], $features ) )
		{
			$features[] = @$values['true_post_type'];
			$featuresPrefix[] = '';
		}
        $featureCount = array();
        $authOptions = array( 0 => 'Public', 97 => 'Private (Invited viewers only)', 98 => 'Only Me' );


		foreach( $features as $key => $eachPostType )
		{	
            $eachPostType = strtolower( trim( $eachPostType ) );
			$featurePrefix = @$featuresPrefix[$key];
			if( empty( $featureCount[$eachPostType] ) )
			{
				$featureCount[$eachPostType] = 1;
			}
			else
			{
				if( empty( $featurePrefix ) )
				{
					$featurePrefix = $featureCount[$eachPostType];
				}
				$featureCount[$eachPostType]++;
            }
			switch( $eachPostType )
			{
				case 'description':
					//	Description
					$fieldset->addElement( array( 'name' => 'article_description', 'label' => '' . $postTypeLabel . ' Description', 'placeholder' => 'Describe this ' . $postTypeLabel . ' in a few words...', 'type' => 'TextArea', 'value' => @$values['article_description'] ) );
				break;  
				case 'privacy':
					$fieldset->addElement( array( 'name' => 'auth_level', 'label' => 'Privacy', 'type' => 'Select', 'value' => is_numeric( @$values['auth_level'] ) ? $values['auth_level'] : 0 ), $authOptions );
					$fieldset->addRequirement( 'auth_level', array( 'InArray' => array_keys( $authOptions ) ) );
				break;  
                case 'cover-photo':
                    
					//	Cover photo

					$fieldName = ( $fieldset->hashElementName ? Ayoola_Form::hashElementName( 'document_url' ) : 'document_url' );
					$fieldName64 = ( $fieldset->hashElementName ? Ayoola_Form::hashElementName( 'document_url_base64' ) : 'document_url_base64' );

					$fieldset->addElement( array( 'name' => 'document_url', 'label' => 'Cover Photo', 'placeholder' => 'Cover Photo for this ' . $postTypeLabel . '', 'type' => 'Document', 'value' => @$values['document_url'] ) );
					$fieldset->addRequirement( 'document_url', array( 'NotEmpty' => null ) );
				break;  
				case 'category':   

					//	Categories
					$table = Application_Category::getInstance();
					
					//	Now allowing users to create their own personal categories
					
					//	Get information about the user access information
					$userInfo = Ayoola_Access::getAccessInformation();
					$categories = array();
					if( ! empty( $userInfo['post_categories_id'] ) && ! empty( $userInfo['post_categories'] ) )
					{
						$categories += array_combine( $userInfo['post_categories_id'], $userInfo['post_categories'] );  

					}
					require_once 'Ayoola/Filter/SelectListArray.php';
					$filter = new Ayoola_Filter_SelectListArray( 'category_name', 'category_label');
					if( ! empty( $articleSettings['allowed_categories'] ) )
					{
						$siteCategories = $table->select( null, array( 'category_name' => $articleSettings['allowed_categories'] ) );
						if( $siteCategories  )
						{
							foreach( $siteCategories as $key => $value )
							{
								if( ! $siteCategories[$key]['category_label'] )
								{
									$siteCategories[$key]['category_label'] = $siteCategories[$key]['category_name'];        
								}

								if( $inner = $table->select( null, array( 'parent_category' => $siteCategories[$key]['category_name'] ) ) )
								{
									$categories[$siteCategories[$key]['category_label']] = $filter->filter( $inner );
								}

							}
							$siteCategories = $filter->filter( $siteCategories );
						}
						else
						{

						}
					}
					elseif( $siteCategories = $table->select() )
					{
						$siteCategories = $filter->filter( $siteCategories );
					}
					if( is_array( $siteCategories ) )
					{
						$categories += $siteCategories;  
					}

					if( self::hasPriviledge( 98 ) )
					{
						@$addCategoryLink .= ( '<a rel="spotlight;changeElementId=' . get_class( $this ) . '" title="Add new Category" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_Settings_Editor/settingsname_name/Articles/">Manage Categories</a>' );
					}
					$currentCategories =  is_array( @$values['category_name'] ) ? $values['category_name'] : array();

					if( ! empty( $_GET['category_name'] ) )
					{

						if( is_string( $_GET['category_name'] ) )
						{
							$_GET['category_name'] = array_map( 'trim', explode( ',', $_GET['category_name'] ) );
							$_REQUEST['category_name'] = $_GET['category_name'];
						}
						$currentCategories +=  is_array( $_GET['category_name'] ) ? $_GET['category_name'] : array();
						$categories['Preset Categories'] = array_combine( $_GET['category_name'], $_GET['category_name'] );
					}
					$categories ? $fieldset->addElement( array( 'name' => 'category_name', 'label' => 'Categories ' . $addCategoryLink, 'type' => 'SelectMultiple', 'value' => $currentCategories  ), $categories ? : array() ) : null;
				break;  
				case 'book':
					$fieldset->addElement( array( 'name' => 'isbn', 'label' => 'ISBN', 'type' => 'InputText', 'value' => @$values['isbn']  ) );
				break;  
				case 'gallery':
					$fieldset->addElement( array( 'name' => 'images' . $featurePrefix, 'label' => $postTypeLabel . ' Images ' . $featurePrefix, 'type' => 'Document', 'data-document_type' => 'image', 'multiple' => 'multiple', 'data-multiple' => 'multiple', 'value' => @$values['images' . $featurePrefix]  ) );
				break;  
				case 'examination':
				case 'test':
                case 'quiz':
                    
					$form->oneFieldSetAtATime = true;   

                    $fieldsetOption = new Ayoola_Form_Element;
				    $fieldsetOption->hashElementName = $this->hashFormElementName;

					$quizOptions = array(
											'quiz_subgroups' => 'This quiz has subgroups',
											'random' => 'Randomize Questions',
											'save_results' => 'Save Test Results',
											'edit_questions' => 'Load or edit questions bank now', 
											'no_correction' => 'Hide correction from the user when test is concluded',
											'hide_result' => 'Hide the test results from user after submission',
										);
                    $fieldsetOption->addElement( array( 'name' => 'quiz_options', 'label' => 'Quiz Options', 'type' => 'Checkbox', 'value' => @$values['quiz_options'] ? : array( 'save_results', 'random', 'edit_questions' ) ), $quizOptions );
                        
                    //	time
					$fieldsetOption->addElement( array( 'name' => 'quiz_time', 'placeholder' => 'e.g. 900', 'label' => 'Maximum Test Time (in secs)', 'type' => 'InputText', 'value' => @$values['quiz_time'] ) );
                    $fieldsetOption->addFilter( 'quiz_time', array( 'Int' => null ) );
                    //  var_export( $values['questions_auth_level'] );
                    $fieldsetOption->addElement( array( 'name' => 'questions_auth_level', 'label' => 'Who can contribute to questions', 'type' => 'Select', 'value' => is_numeric( $values['questions_auth_level'] ) ? $values['questions_auth_level'] : 98 ), $authOptions );
					
				    $form->addFieldset( $fieldsetOption );
					
					$groupIds = $this->getGlobalValue( 'quiz_subgroup_id' ) ? : @$values['quiz_subgroup_id'];

					$groupQuestions = $this->getGlobalValue( 'quiz_subgroup_question' ) ? : @$values['quiz_subgroup_question'];
					if( in_array( 'group_questions', $fieldsToEdit ) || ( is_array( $this->getGlobalValue( 'quiz_options' ) ) && in_array( 'quiz_subgroups', $this->getGlobalValue( 'quiz_options' ) ) ) )
					{
						$i = 0;
						//	Build a separate demo form for the previous group
						$questionForm = new Ayoola_Form( array( 'name' => 'categories...' )  );
						$questionForm->setParameter( array( 'no_fieldset' => true, 'no_form_element' => true ) );
						$questionForm->wrapForm = false;
						
						do
						{
							//	Put the questions in a separate fieldset
							$categoryFieldset = new Ayoola_Form_Element; 
							$categoryFieldset->allowDuplication = true;
							$categoryFieldset->duplicationData = array( 'add' => '+ Add New Category Below', 'remove' => '- Remove Above Category', 'counter' => 'subgroup_counter', );
							$categoryFieldset->container = 'span';
							
							$categoryFieldset->addElement( array( 'name' => 'quiz_subgroup_id', 'label' => 'Unique Subgroup ID', 'placeholder' => 'e.g. Subgroup 1', 'type' => 'Hidden', 'multiple' => 'multiple', 'value' => @$values['quiz_subgroup_id'][$i] ? : $groupIds[$i] ) );
							$categoryFieldset->addElement( array( 'name' => 'quiz_subgroup_question', 'label' => 'Subgroup Question or Instructions', 'placeholder' => 'e.g. Use this information to answer the following questions', 'type' => 'TextArea', 'data-html' => '1', 'multiple' => 'multiple', 'value' => @$values['quiz_subgroup_question'][$i] ? : $groupQuestions[$i] ) );
													
							$i++;
							$categoryFieldset->addLegend( 'Sub-group  <span name="subgroup_counter">' . $i . '</span> of <span name="subgroup_counter_total">' . ( ( count( @$values['quiz_subgroup_id'] ) ? : count( $groupQuestions ) ) ? : 1 ) . '</span>' );			   			
							$questionForm->addFieldset( $categoryFieldset );

						}
						while( isset( $values['quiz_subgroup_id'][$i] ) || isset( $groupQuestions[$i] ) );
						
						//	Put the questions in a separate fieldset
						$categoryFieldset = new Ayoola_Form_Element; 
						$categoryFieldset->allowDuplication = false;
						$categoryFieldset->container = 'span';
						
						//	add previous categories if available
						$categoryFieldset->addElement( array( 'name' => 'group_questions', 'data-pc-ignore-field' => true, 'type' => 'Html', 'value' => '' ), array( 'html' => $questionForm->view(), 'fields' => 'quiz_subgroup_id,quiz_subgroup_question' ) );
						
						//	Autogenerate group ids
						if( $groupIds )
						{
							foreach( $groupIds as $eachKey => $eachGroup )  
							{
								if( ! $eachGroup )
								{
									$groupIds[$eachKey] = md5( $groupQuestions[$eachKey] );
								}
							}
							$categoryFieldset->addFilter( 'quiz_subgroup_id', array( 'DefiniteValue' => array( $groupIds ) ) );						
						}
						//	Add only the last one into the main form
						$form->addFieldset( $categoryFieldset );
					}					
					
					if( in_array( 'questions_and_answers', $fieldsToEdit ) || ( is_array( $this->getGlobalValue( 'quiz_options' ) ) && in_array( 'edit_questions', $this->getGlobalValue( 'quiz_options' ) ) ) )
					{
                        $j = 0; 
                        
                        // group count
						//	Separate form for category confirmation
						//	Do this later after questions have been set so the max questions could equal total questions
						$questionConfForm = new Ayoola_Form( array( 'name' => 'categories-conf...' )  );
						$questionConfForm->setParameter( array( 'no_fieldset' => true, 'no_form_element' => true ) );
                        $questionConfForm->wrapForm = false;

                        //	Do this for all each categories and don't forget the "uncategorized"
						while( $j <= count( @$groupIds ) && $j < 9 )
						{
							//	autogenerate group ids
							if( isset( $groupIds[$j] ) )
							{
								//	Randomly generate IDs for group questions
								@$groupIds[$j] = $groupIds[$j] ? : md5( $groupQuestions[$j] );
							}
							else
							{
								//	This is causing infinite loop
							}


                            $questionForm = self::quizQuestions( $values, $groupIds, $j );
							
							//	Put the questions in a separate fieldset
							$questionFieldset = new Ayoola_Form_Element; 
							$questionFieldset->allowDuplication = false;

							$questionFieldset->container = 'span';
							
							//	add previous questions if available
							$subGroupHeading = $groupQuestions[$j] ? '<p style="font-size:large;">Question Sub Group - #' . ( $j + 1 ) . '</p><p>Sub-group Question/Instruction:</p><p><blockquote>' . ( @$groupQuestions[$j] ? : 'Uncategorized Questions' ) . ' </blockquote> </p>' : null;
							$subGroupHeading = Ayoola_Object_Wrapper_Abstract::wrap( $subGroupHeading, 'white-content-theme-border' );

							$totalQuestionCount = @$totalQuestionCount ? : 0;
							$questionCount = count( $this->getGlobalValue( 'quiz_question' . @$groupIds[$j] ) ? : array() );
							$totalQuestionCount += $questionCount;
							
							$questionFieldset->addElement( array( 'name' => 'question_count' . @$groupIds[$j], 'data-pc-element-whitelist-group' => 'questions_and_answers', 'label' => '', 'type' => 'Hidden', 'value' => null ) );
							$questionFieldset->addFilter( 'question_count' . @$groupIds[$j], array( 'DefiniteValue' => $questionCount ) );

							$questionElementList = 'quiz_question' . @$groupIds[$j] . ',quiz_option1' . @$groupIds[$j] . ',quiz_option2' . @$groupIds[$j] . ',quiz_option3' . @$groupIds[$j] . ',quiz_option4' . @$groupIds[$j] . ',quiz_correct_option' . @$groupIds[$j] . ',quiz_answer_notes' . @$groupIds[$j];
							$questionFieldset->addElement( array( 'name' => 'questions_and_answers', 'data-pc-ignore-field' => true, 'data-pc-element-whitelist-group' => 'questions_and_answers', 'type' => 'Html', 'value' => '' ), array( 'html' => ( $subGroupHeading . $questionForm->view() ), 'parameters' => array( 'data-pc-element-whitelist-group' => 'questions_and_answers' ), 'fields' => $questionElementList ) );
														
							//	Add only the last one into the main form
							$form->addFieldset( $questionFieldset );

							//	Do this later after questions have been set so the max questions could equal total questions
							$categoryFieldset2 = new Ayoola_Form_Element; 
							$categoryFieldset2->container = 'span';

							$categoryFieldset2->addElement( array( 'name' => 'quiz_subgroup_question_demo', 'label' => 'Number of questions to pick from this category (' . count( $this->getGlobalValue( 'quiz_question' . @$groupIds[$j] ) ) . ' total questions set)', 'placeholder' => 'e.g. Use this information to answer the following questions', 'type' => 'TextArea', 'data-html' => '1', 'style' => 'display:block;', 'multiple' => 'multiple', 'disabled' => 'disabled', 'value' => ( @$values['quiz_subgroup_question'][$j] ? : @$groupQuestions[$j] ) ? : 'Uncategorized Questions' ) );
							$categoryFieldset2->addElement( array( 'name' => 'quiz_subgroup_question_max', 'label' => ' ', 'placeholder' => '', 'style' => '', 'type' => 'Select', 'multiple' => 'multiple', 'value' => @$values['quiz_subgroup_question_max'][$j] ? : count( $this->getGlobalValue( 'quiz_question' . @$groupIds[$j] ) ) ), array_combine( range( 0, count( $this->getGlobalValue( 'quiz_question' . @$groupIds[$j] ) ) ),  range( 0, count( $this->getGlobalValue( 'quiz_question' . @$groupIds[$j] ) ) ) ) );
							$questionConfForm->addFieldset( $categoryFieldset2 );    
					
							$j++;
						}
						//	Put the questions in a separate fieldset
						$questionConfFieldsetX = new Ayoola_Form_Element; 
                        $questionConfFieldsetX->hashElementName = $this->hashFormElementName;
												
						//	Now lets review category questions
						$questionConfFieldsetX->addElement( array( 'name' => 'previous_forms', 'data-pc-ignore-field' => true, 'type' => 'Html', 'value' => '' ), array( 'html' => ( '<h3>How many questions per test per subgroup?</h3>' . $questionConfForm->view() ), 'fields' => 'quiz_subgroup_question_max' ) );
						
						$questionConfFieldsetX->addElement( array( 'name' => 'total_question_count', 'data-pc-element-whitelist-group' => 'questions_and_answers', 'type' => 'Hidden', 'value' => null ) );
						$questionConfFieldsetX->addFilter( 'total_question_count', array( 'DefiniteValue' => $totalQuestionCount ) );
						
						$questionConfFieldsetX->addElement( array( 'name' => 'total_question_displayed', 'data-pc-element-whitelist-group' => '', 'type' => 'Hidden', 'value' => null ) );
						$questionConfFieldsetX->addFilter( 'total_question_displayed', array( 'DefiniteValue' => array_sum( $this->getGlobalValue( 'quiz_subgroup_question_max' ) ) ) );

						//	Review Questions and Set  
						$form->addFieldset( $questionConfFieldsetX );
					}
				break;
				case 'product':
				case 'subscription':
					$fieldset->addElement( array( 'name' => 'item_old_price' . $featurePrefix, 'label' => 'Old price', 'placeholder' => '0.00', 'type' => 'InputText', 'value' => @$values['item_old_price' . $featurePrefix] ) );
					$fieldset->addElement( array( 'name' => 'item_price' . $featurePrefix, 'label' => 'Current price', 'placeholder' => '0.00', 'type' => 'InputText', 'value' => @$values['item_price' . $featurePrefix] ) );
					$fieldset->addElement( array( 'name' => 'no_of_items_in_stock' . $featurePrefix, 'type' => 'InputText', 'value' => @$values['no_of_items_in_stock' . $featurePrefix] ) );   
				break;
				case 'subscription-options':
                    $fieldset->addElement( array( 'name' => 'subscription_selections' . $featurePrefix, 'label' => $postTypeLabel . ' Options ' . $featurePrefix, 'placeholder' => 'e.g. blue', 'type' => 'MultipleInputText', 'value' => @$values['subscription_selections' . $featurePrefix] ), @$values['subscription_selections' . $featurePrefix] );
				break;
				case 'multi-price':

					$i = 0;
					do
					{
						$fieldsetX = new Ayoola_Form_Element; 
						$fieldsetX->hashElementName = false;
						$fieldsetX->duplicationData = array( 'add' => 'New Option', 'remove' => 'Remove Option', 'counter' => 'pricing_option_counter', );

						$fieldsetX->container = 'div';
						$form->wrapForm = false;
						$fieldsetX->addElement( array( 'name' => 'price_option_title' . $featurePrefix, 'style' => 'max-width: 40%;', 'label' => '', 'placeholder' => 'Option Name', 'type' => 'InputText', 'multiple' => 'multiple', 'value' => @htmlspecialchars( $values['price_option_title' . $featurePrefix][$i] ) ) );
						$fieldsetX->addElement( array( 'name' => 'price_option_price' . $featurePrefix, 'style' => 'max-width: 40%;', 'label' => '', 'placeholder' => 'Separate Option Price', 'type' => 'InputText', 'multiple' => 'multiple', 'value' => @$values['price_option_price' . $featurePrefix][$i] ) );
						$fieldsetX->allowDuplication = true;  
						$fieldsetX->placeholderInPlaceOfLabel = true;

						$i++;
						$fieldsetX->addLegend( 'Pricing Option <span name="pricing_option_counter">' . $i .  '</span>' );
						$form->oneFieldSetAtATime = false;   
						$form->addFieldset( $fieldsetX );
					}
					while( ! empty( $values['price_option_title'][$i] ) || ! empty( $values['price_option_price'][$i] ) );
				break;
				case 'poll':
					//	Poll
					$fieldset->addElement( array( 'name' => 'poll_question', 'type' => $values['article_type'] == 'poll' ? 'InputText' : 'Hidden', 'value' => @$values['poll_question'] ) );
					@$values['poll_options'] = is_array( $values['poll_options'] ) ? array_combine( $values['poll_options'], $values['poll_options'] ) : array();

					$fieldset->addElement( array( 'name' => 'poll_options', 'type' => $values['article_type'] == 'poll' ? 'MultipleInputText' : 'Hidden', 'value' => @$values['poll_options'] ), $values['poll_options'] );
					@$values['poll_option_preset_votes'] = is_array( $values['poll_option_preset_votes'] ) ? array_combine( $values['poll_option_preset_votes'], $values['poll_option_preset_votes'] ) : array();

					$fieldset->addElement( array( 'name' => 'poll_option_preset_votes', 'type' => $values['article_type'] == 'poll' ? 'MultipleInputText' : 'Hidden', 'value' => @$values['poll_option_preset_votes'] ), $values['poll_option_preset_votes'] );
				break;
				case 'video':
					//	video
					$fieldset->addElement( array( 'name' => 'video_url' . $featurePrefix, 'type' => 'InputText', 'value' => @$values['video_url' . $featurePrefix] ) );
					$fieldset->addRequirement( 'video_url' . $featurePrefix, array( 'NotEmpty' => null ) );
				break;
				case 'link':
					//	link
					$fieldset->addElement( array( 'name' => 'link_url' . $featurePrefix, 'type' => 'InputText', 'value' => @$values['link_url' . $featurePrefix] ) );
					$fieldset->addRequirement( 'link_url' . $featurePrefix, array( 'NotEmpty' => null ) );
				break;
				case 'date':
				case 'datetime':
				case 'event':
				case 'events':  
					//	event
					//	retrieve birthday
					if( @$values['date'] )
					{
						switch( strlen( $values['date'] ) )
						{
							case 8:
								$values['year'] = $values['date'][0] . $values['date'][1] . $values['date'][2] . $values['date'][3];
								$values['month'] = $values['date'][4] . $values['date'][5];
								$values['day'] = $values['date'][6] . $values['date'][7];
							break;
							default:
								@list( $values['year'], $values['month'], $values['day'] ) = explode( '-', $values['date'] );
							break; 
						}
					}

					
					//	Month
					$options = array_combine( range( 1, 12 ), array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ) );
					$birthMonthValue = intval( @strlen( $values['month'] ) === 1 ? ( '0' . @$values['month'] ) : @$values['month'] );
					$birthMonthValue = intval( $birthMonthValue ?  : $this->getGlobalValue( 'month' . $featurePrefix ) );
					$fieldset->addElement( array( 'name' => 'month' . $featurePrefix, 'label' => $postTypeLabel . ' Date', 'style' => 'min-width:0px;width:100px;display:inline-block;;margin-right:0;', 'type' => 'Select', 'value' => $birthMonthValue ), array( 'Month' ) + $options ); 
					$fieldset->addRequirement( 'month', array( 'InArray' => array_keys( $options ) ) );
					if( strlen( $this->getGlobalValue( 'month' . $featurePrefix ) ) === 1 )
					{
						$fieldset->addFilter( 'month', array( 'DefiniteValue' => '0' . $this->getGlobalValue( 'month' . $featurePrefix ) ) );
					}
					
					//	Day
					$options = range( 1, 31 );
					$options = array_combine( $options, $options );
					$birthDayValue = intval( @strlen( $values['day'] ) === 1 ? ( '0' . @$values['day'] ) : @$values['day'] );
					$birthDayValue = intval( $birthDayValue ?  : $this->getGlobalValue( 'day' . $featurePrefix ) );
					$fieldset->addElement( array( 'name' => 'day' . $featurePrefix, 'label' => '', 'style' => 'min-width:0px;width:100px;display:inline-block;;margin-right:0;', 'type' => 'Select', 'value' => $birthDayValue ), array( 'Day' ) + $options );
					$fieldset->addRequirement( 'day' . $featurePrefix, array( 'InArray' => array_keys( $options ) ) );
					if( strlen( $this->getGlobalValue( 'day' . $featurePrefix ) ) === 1 )
					{
						$fieldset->addFilter( 'day' . $featurePrefix, array( 'DefiniteValue' => '0' . $this->getGlobalValue( 'day' . $featurePrefix ) ) );
					}
					
					//	Year
					//	10 years and 10 years after todays date
					$options = range( date( 'Y' ) + 10, date( 'Y' ) - 10 );
					$options = array_combine( $options, $options );
					$fieldset->addElement( array( 'name' => 'year' . $featurePrefix, 'label' => '', 'style' => 'min-width:0px;width:100px;display:inline-block;margin-right:0;', 'type' => 'Select', 'value' => @$values['year'] ? : date( 'Y' ) ), array( 'Year' ) + $options );
					$fieldset->addRequirement( 'year' . $featurePrefix, array( 'InArray' => array_keys( $options ) ) );
					$options = range( 0, 23 );
					foreach( $options as $key => $each )
					{
						if( strlen( $options[$key] ) < 2 )  
						{
							$options[$key] = '0' . $options[$key];
						}
					}
					$fieldset->addElement( array( 'name' => 'time_hour' . $featurePrefix, 'label' => ' ', 'style' => 'min-width:0px;width:100px;', 'type' => 'Select', 'value' => @$values['time_hour' . $featurePrefix] ), array( 'Hour' ) +  array_combine( $options, $options ) );
					$fieldset->addRequirement( 'time_hour' . $featurePrefix, array( 'InArray' => array_keys( $options ) ) );
					$options = range( 0, 59 );
					foreach( $options as $key => $each )
					{
						if( strlen( $options[$key] ) < 2 )    
						{
							$options[$key] = '0' . $options[$key];
						}
					}
					$fieldset->addElement( array( 'name' => 'time_minutes' . $featurePrefix, 'label' => ' ', 'style' => 'min-width:0px;width:100px;', 'type' => 'Select', 'value' => @$values['time_minutes' . $featurePrefix] ), array( 'Minute' ) + array_combine( $options, $options ) );
					$fieldset->addRequirement( 'time_minutes' . $featurePrefix, array( 'InArray' => array_keys( $options ) ) );

					//	datetime combined
					$fieldset->addElement( array( 'name' => 'datetime' . $featurePrefix, 'label' => 'Timestamp', 'placeholder' => 'YYYY-MM-DD HH:MM', 'type' => 'Hidden', 'value' => @$values['datetime' . $featurePrefix] ) );
					$datetime = $this->getGlobalValue( 'year' . $featurePrefix );
					$datetime .= '-';
					$datetime .= strlen( $this->getGlobalValue( 'month' . $featurePrefix ) ) === 1 ? ( '0' . $this->getGlobalValue( 'month' . $featurePrefix ) ) : $this->getGlobalValue( 'month' . $featurePrefix );
					$datetime .= '-';
					$datetime .= strlen( $this->getGlobalValue( 'day' . $featurePrefix ) ) === 1 ? ( '0' . $this->getGlobalValue( 'day' . $featurePrefix ) ) : $this->getGlobalValue( 'day' . $featurePrefix );
					$datetime .= ' ';
					$datetime .= strlen( $this->getGlobalValue( 'time_hour' . $featurePrefix ) ) === 1 ? ( '0' . $this->getGlobalValue( 'time_hour' . $featurePrefix ) ) : $this->getGlobalValue( 'time_hour' . $featurePrefix );
					$datetime .= ':';
					$datetime .= strlen( $this->getGlobalValue( 'time_minutes' . $featurePrefix ) ) === 1 ? ( '0' . $this->getGlobalValue( 'time_minutes' . $featurePrefix ) ) : $this->getGlobalValue( 'time_minutes' . $featurePrefix );
					$fieldset->addFilter( 'datetime' . $featurePrefix, array( 'DefiniteValue' => $datetime ) );
				break;
				case 'location':

					$fieldset->addElement( array( 'name' => 'address' . $featurePrefix, 'label' => $postTypeLabel . ' Address Line 1', 'placeholder' => 'e.g. 12 Adebisi Street', 'type' => 'InputText', 'value' => @$values['address' . $featurePrefix] ) );  
					$fieldset->addElement( array( 'name' => 'addresss2' . $featurePrefix, 'label' => $postTypeLabel . ' Address Line 2', 'placeholder' => 'e.g. Nustreams Conference & Culture Centre', 'type' => 'InputText', 'value' => @$values['addresss2' . $featurePrefix] ) );  
					$fieldset->addElement( array( 'name' => 'city' . $featurePrefix, 'label' => $postTypeLabel . ' City', 'placeholder' => 'e.g. Ibadan', 'type' => 'InputText', 'value' => @$values['city' . $featurePrefix] ) );
					$fieldset->addElement( array( 'name' => 'province' . $featurePrefix, 'label' => $postTypeLabel . ' - State, Province or Region', 'placeholder' => '', 'type' => 'InputText', 'value' => @$values['province' . $featurePrefix] ) );
					$fieldset->addElement( array( 'name' => 'country' . $featurePrefix, 'label' => $postTypeLabel . ' - Country', 'placeholder' => '', 'type' => 'InputText', 'value' => @$values['country' . $featurePrefix] ) );
				break;
				case 'article':
					$fieldset->addElement( array( 'name' => 'article_content' . $featurePrefix, 'data-html' => '1', 'label' => '' . $postTypeLabel . ' write up  ' . $featurePrefix, 'rows' => '10', 'placeholder' => 'Enter content here...', 'type' => 'TextArea', 'value' => @$values['article_content' . $featurePrefix] ? : @$values['article_description' . $featurePrefix] ) );
					$fieldset->addRequirement( 'article_content' . $featurePrefix, array( 'NotEmpty' => null ) );
				break;
				case 'audio':
				case 'music':
				case 'message':
				case 'e-book':
				case 'document':
				case 'file':
				case 'download':
					//	Downloads
					$downloadOptions = array( 
												'require_user_info' => 'Require log-in before download', 
												'download_notification' => 'Notify me on every download', 
											);

					
					$fieldset->addElement( array( 'name' => 'download_url' . $featurePrefix, 'label' => 'Download File', 'placeholder' => 'e.g. http://example.com/path/to/file.mp3', 'type' => 'Document', 'optional' => 'optional', 'value' => @$values['download_url' . $featurePrefix] ) );
					$fieldset->addRequirement( 'download_url' . $featurePrefix, array( 'NotEmpty' => null ) );
				break;
                default:

                    if( $eachPostTypeInfo = Application_Article_Type_Abstract::getOriginalPostTypeInfo( $eachPostType ) )
                    {
            
                        
                    }
                    if( $postTypeInfo['view_widget'] !== $eachPostTypeInfo['view_widget'] && ! empty( $eachPostTypeInfo['view_widget'] ) && Ayoola_Object_Embed::isWidget( $eachPostTypeInfo['view_widget'] ) )
                    {
                        $orderFormClass = new $eachPostTypeInfo['view_widget']();
                        $orderFormClass->createForm( null, null, $values );
                        if( $orderFormClass->getForm() )
                        {
                            foreach( $orderFormClass->getForm()->getFieldsets() as $each )  
                            {
                                $form->addFieldset( $each );
                            }
                            $form->submitValue = 'Save...';
                        }
                    }
                break;
			}
		}
		if( ! empty( $postTypeInfo['post_type_custom_fields'] ) )
		{
			$supplementaryFields = array_map( 'trim', explode( ',', $postTypeInfo['post_type_custom_fields'] ) );
			foreach( $supplementaryFields as $each )
			{
				if( array_key_exists( $each, $form->getNames() ) || array_key_exists( $form::hashElementName( $each ), $form->getNames() ) )
				{
					continue;
				}
				$fieldset->addElement( array( 'name' => $each, 'type' => 'text', 'value' => @$values[$each] ) );
				
			}
		}
		if( ! empty( $postTypeInfo['post_type_options'] ) && in_array( 'multi-price', $postTypeInfo['post_type_options'] ) )
		{
			
		}

		//	Inject other form fields
		foreach( static::$_otherFormFields as $eachField )
		{
			$fieldset->addElement( array( 'name' => $eachField, 'type' => 'Hidden', ) );
		}
		//	Use tiny editor

		static::initHTMLEditor();

		$defaultProfile = Application_Profile_Abstract::getMyDefaultProfile();
		$defaultProfile = $defaultProfile['profile_url'];

        if( ! $profiles = self::getMyAuthProfiles() )
        {
            $profiles = array();
        }
        
		$fieldset->addElement( array( 'name' => 'profile_url',  'onchange' => 'ayoola.div.manageOptions( { database: "Application_Profile_Table", listWidget: "Application_Profile_ShowAll", values: "profile_url", labels: "display_name", element: this } );', 'label' => 'Post as', 'type' => count( $profiles ) > 1 ? 'Select' : 'Hidden', 'value' => @$values['profile_url'] ? : $defaultProfile ), $profiles + array( '__manage_options' => '[Manage Profiles]' ) );


		if( @$values['requirement_name'] || ( is_array( Ayoola_Form::getGlobalValue( 'article_options' ) ) && in_array( 'requirement', Ayoola_Form::getGlobalValue( 'article_options' ) ) ) )
		{
			$options = Ayoola_Form_Requirement::getInstance();
			$options = $options->select();
			if( $options ) 
			{
				require_once 'Ayoola/Filter/SelectListArray.php';
				$filter = new Ayoola_Filter_SelectListArray( 'requirement_name', 'requirement_label' );
				$options = $filter->filter( $options );
				if( $values['article_type'] == 'subscription' ) 
				{
					//	Shipping and billing address for subscription
					$options += array( 'billing_address' => 'Billing Address', 'shipping_address' => 'Shipping Address', );
				}
				$fieldset->addElement( array( 'name' => 'article_requirements', 'label' => 'Select information required from viewers of this ' . $postTypeLabel . ' (advanced)', 'type' => 'Checkbox', 'value' => @$values['article_requirements'] ), $options );

			}
	
		}
		
		
		//	Publish

		//	retain this because we need to bring this up in custom forms

		$fieldset->addElement( array( 'name' => 'user_restrictions', 'type' => 'Hidden', 'value' => null ) );
		$fieldset->addRequirement( 'user_restrictions', array( 'UserRestrictions' => null ) );

		$fieldset->addFilters( array( 'trim' => null, 'FormatArticle' => null ) );
		$form->addFieldset( $fieldset );

		$form->setParameter( array( 'element_whitelist' => $fieldsToEdit ) );
		$this->setForm( $form );
    } 
	// END OF CLASS
}
