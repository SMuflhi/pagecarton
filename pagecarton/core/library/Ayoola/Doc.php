<?php
/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Ayoola_Doc
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Doc.php 12/18/2011 7.54PM ayoola $
 */

/**
 * @see Ayoola_
 */

require_once 'Ayoola/Doc/Abstract.php';  


/**
 * @category   PageCarton
 * @package    Ayoola_Doc
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Ayoola_Doc extends Ayoola_Doc_Abstract
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
     *
     * @var boolean
     */
	protected static $_counter = 0;

    /**
     * The paths to each document
     *
     * @var array
     */
	protected $_paths = array();
	
    /**
     * The Adapter to document
     *
     * @var Ayoola_Doc_Adapter
     */
	protected $_adapter;
	
    /**
     * The FullPath to the Current Document Directory
     *
     * @var string
     */
	protected static $_documentDirectory;
	
    /**
     * The title of the Document
     *
     * @var string
     */
	public $title;

    /**
     * Singleton instance
     *
     * @var self
     */
	protected static $_instance;

    /**
     * Constructor
     *
     * @param The path to the document
     * 
     */
    public function init()
    {
	//	$paths = (array) $this->getParameter( 'option' );
	//	var_export( $this->getParameter() );
/* 		foreach( $paths as $path )
		{
			$path ? $this->loadFile( $path ) : null;
		}
 */ }
	
    /**
     * Returns a singleton Instance
     *
     * @param void
     * @return self
     */
    public static function getInstance()
    {
		//if( is_null( self::$_instance ) ){ self::$_instance = new self; }
		return new self;
    } 	
	
    /**
     * Loads the file in format safe for output
     *
     * @param The path to the document
     * @return boolean
     */
    public function loadFile( $path )
    {
    //    var_export( $path );
    //    exit();
		require_once 'Ayoola/Loader.php';
		if( ! $absolutePath = Ayoola_Loader::checkFile( $path, array( 'prioritize_my_copy' => true ) ) )
		{
	//	var_export( $path );
			require_once 'Ayoola/Doc/Exception.php';
			throw new Ayoola_Doc_Exception( basename( $path ) . ' Not Found' );	
			return false;
		}
//		var_export(  Ayoola_Loader::getValidIncludePaths( $path ) );
//		var_export( $absolutePath );
//		exit();
		if( $includePaths = array_keys( Ayoola_Loader::getValidIncludePaths( $path ) ) )
		{
			$documentDirectory = array_shift( $includePaths );
			$documentDirectory = $documentDirectory . DS . DOCUMENTS_DIR;
			self::setDocumentDirectory( $documentDirectory );
						//var_export( self::getDocumentDirectory() );
					//	var_export( $documentDirectory );
		}
//		var_export( $absolutePath );
//		var_export( var_export( $documentDirectory ) );
//		exit();
		$this->setPaths( $absolutePath );
    } 
	
    /**
     * Sets the _adapters to a value
     *
     * @param string File Extention 
     * @return Ayoola_Doc_Adapter
     * @see Ayoola_Doc_Adapter
     */
    public function setAdapter( $paths = null )
    {
		$paths = $paths ? : $this->getPaths();
		require_once 'Ayoola/Doc/Adapter.php';
//		var_export( $paths );
        $this->_adapter = new Ayoola_Doc_Adapter( $paths );
    } 
	
    /**
     * Loads the adapter
     *
     * @param string File Extention 
     * @return Ayoola_Doc_Adapter
     * @see Ayoola_Doc_Adapter
     */
    public function getAdapter( $paths = null )
    {
		if( null ===$this->_adapter )
		{
			$this->setAdapter( $paths );
		}
		//var_export( $this->_adapter );
		return $this->_adapter;
    } 
	
    /**
     * Coverts a document uri to its dedicated domain counterparts 
     *   
     * @param string URI 
     * @return string http://{time()}.document.{domain}/{uri} or {uri}
     */
    public static function uriToDedicatedUrl( $uri, array $options = null )
    {
		$useQueryStrings = true;
		do
		{
			if( ! $uri || ! is_string( $uri ) ){ break; }
			if( strpos( $uri, '//' ) !== false ){ break; }   
			
			//	Localhosts
			if( ! strpos( $_SERVER['HTTP_HOST'], '.' ) )
			{
				$useQueryStrings = true;
			}
			try
			{
				$storage = new Ayoola_Storage(); 
				$storage->storageNamespace = __CLASS__ . Ayoola_Application::getUrlPrefix() . $uri . 's--d-d-sw' . Ayoola_Application::getDomainSettings( 'protocol' );
				$storage->setDevice( 'File' );
				if( ! $dedicatedUrl = $storage->retrieve() OR $options['disable_cache'] )  
				{
                   
                    //  delete web root link
                    $link = trim( $uri, '/ ' );
                    if( is_link( $link ) )
                    {
                        unlink( $link );
                    }
					$j = new Ayoola_Doc( array( 'option' => $uri ) );
					$j = str_replace( '/', DS, $j::getDocumentDirectory() . $uri ); 
					$j = @filemtime( Ayoola_Loader::checkFile( $j, array( 'prioritize_my_copy' => true ) ) );
					$domain = Ayoola_Page::getDefaultDomain();
					$domain = DOMAIN;
					$dedicatedUrl = Ayoola_Application::getDomainSettings( 'protocol' ) . "://{$domain}" . Ayoola_Application::getUrlPrefix() . "{$uri}?document_time={$j}";					
					$storage->store( $dedicatedUrl );
				}
			}
			catch( Exception $e )
			{ 
				null; 
			}
		}
		while( false );
		@$dedicatedUrl = $dedicatedUrl ? : $uri;    	
		return $dedicatedUrl;
    } 
	
    /**
     * This method sets the _documentDirectory to a value
     *
     * @param string The Document Directory
     * @return null
     */
    public static function setDocumentDirectory( $directory = null )
    {
		$directory = $directory ? : DOCUMENTS_DIR;
		self::$_documentDirectory = $directory;
    } 
	
    /**
     * This method returns the _documentDirectory property
     *
     * @param 
     * @return string The Document Directory
     */
    public static function getDocumentDirectory()
    {
		if( is_null( self::$_documentDirectory ) ){ self::setDocumentDirectory(); }
		return self::$_documentDirectory;
    } 
	
    /**
     * This method returns the path to a document
     *
     * @param string URI
     * @return string The Document PATH
     */
    public static function getDocumentPath( $uri )
    {
        $dir = self::getDocumentDirectory();
    //    if( ! $path = Ayoola_Loader::checkFile( 'documents/__/' . $uri, array( 'prioritize_my_copy' => true ) ) )

        //  looking for changed document only in the current site context so changes
        //  pc.com changes for child sites isn't proper
        if( ! $path = Ayoola_Doc_Browser::getDocumentsDirectory() .  '/__' . $uri OR ! is_file( $path)  )
        {
            $path = Ayoola_Loader::checkFile( 'documents' . $uri, array( 'prioritize_my_copy' => true )  );
        }
        return $path;
    } 
	
    /**
     * Returns the value of the _path property
     *
     * @return string Path to the Document
     */
    public function getPaths()
    {
        return (array) $this->_paths;
    } 
	
    /**
     * Sets the _path property to a value
     *
     * @param The path to the document
     */
    public function setPaths( $paths )
    {
		if( is_array( $paths ) )
		{
			$this->_paths = array_merge( $this->getPaths(), $paths );
		}
		elseif( is_string( $paths ) )
		{
			$this->_paths[] = $paths;
		}
    } 
	
    /**
     * Retrieves URI from directory
     *
     * @param string The path to the document
     * @return string URI
     */
    static public function pathToUri( $path )
    {	
		//	Retrieve the url from the path
		require_once 'Ayoola/Loader.php';
		$dirPaths[] = self::getDocumentDirectory();
		$fullDirPath = Ayoola_Loader::checkDirectory( self::getDocumentDirectory() );
		if( $fullDirPath
			&& ! in_array( $fullDirPath, $dirPaths )
			){ $dirPaths[] = $fullDirPath; }
		$paths = explode( PS, get_include_path() );
		foreach( $paths as $dirPath )
		{
			$dirPath = $dirPath . DS . DOCUMENTS_DIR;
			$path = str_ireplace( DS, '/', $path );		
			$dirPath = str_ireplace( DS, '/', $dirPath );		
			$uri = str_ireplace( $dirPath, '', $path );
		//	var_export( $dirPath );
			if( $uri != $path ){ break; }
		}
		//var_export( self::getDocumentDirectory() );
		//var_export( $path );
		return $uri;
    } 
	
    /**
     * Retrieves URI from directory
     *
     * @param string The path to the document
     * @return string URI
     */
    static public function uriToPath( $uri )
    {	
		//	Retrieve the url from the path
		require_once 'Ayoola/Loader.php';
		$fullDirPath = Ayoola_Loader::checkFile( DOCUMENTS_DIR . $uri );
		return $fullDirPath;
    } 
	
    /**
     * Retrieves URI from directory
     *
     * @param mixed The path(s) to the document
     * @return array Array of URIs
     */
    static public function pathsToUris( $paths )
    {	
		//	We allow scalar and array values
		$paths = (array) $paths;
		$values = array();
		foreach( $paths as $key => $value )
		{
			$key = self::pathToUri( $key );
			$value = self::pathToUri( $value );
			$values[$key] = $value;
		}
		//var_export( $uris );
		return $values;
    } 
	
    /**
     * Retrieve the files present in a directory
     *
     * @param string The Directory to look in
     * @return array filenames in the searched directories
     */
    static public function getFiles( $directory, array $options = null )
    {
		$keyZ = md5( __METHOD__ . serialize( func_get_args() ) . 'fff=-' );
//		$storageInfo = array( 'id' => $keyZ, 'device' => 'File', 'time_out' => 100000, );
	//	$storage = static::getObjectStorage( $storageInfo );
		
		if( empty( $options['no_cache'] ) )
		{
			if( isset( static::$_properties[__METHOD__][$keyZ] ) && ! is_null( static::$_properties[__METHOD__][$keyZ] ) )
			{
				return static::$_properties[__METHOD__][$keyZ];
			}
	//		if( $storage->retrieve() !== false )
			{
				//	dont know if this won't cause serious side effects'
		//		return $storage->retrieve();
			}
		}
	//		var_export( $storage->retrieve() );
	//	var_export( $directory );
		
	//	$storage->store( array() );		
		static::$_properties[__METHOD__][$keyZ] = array();

	//	var_export( get_called_class() );
		$files = array();    
	//	var_export( $directory );
		if ( ! is_dir( $directory ) ) 
		{
		//	var_export( $directory );
			return $files;
		//	throw new Ayoola_Doc_Exception( 'Invalid Directory - ' . $directory );   
		}
		if ( ! ( $handle = opendir( $directory ) ) ) 
		{
			return $files;
//			throw new Ayoola_Doc_Exception( 'Directory cannot be opened  for reading - ' . $directory );
		}
		while ( ( $filename = readdir( $handle ) ) !== false ) 
		{
			$file = $directory . DS . $filename;
			$file = str_replace( DS, '/', $file );
						//var_export( $file );
		//	self::v( $file ); 
			if( is_file( $file ) )
			{
				if( in_array( $file, $files ) )
				{
					continue;
				}
                $extension = explode( ".", strtolower( $file ) );
                $extension = array_pop( $extension );
                if( is_array( @$options['whitelist_extensions'] ) && ! in_array( $extension, @$options['whitelist_extensions'] ) )
                {
                    continue;
                }
                $basename = basename( strtolower( $file ) );
                if(  is_array( @$options['whitelist_basename'] ) && ! in_array( $basename, @$options['whitelist_basename'] ) )
                {
                    continue;
                }
	//			$files[$file] = $file; 
				$key = $file;
				if( ! empty( $options['key_function'] ) )
				{
					$key = $options['key_function'];
					$key = $key( $file );
					if( $key === false )
					{
				//		var_export( $key );
						continue;
					}
				}
				if( isset( $files[$key] ) ) 
				{
					while( isset( $files[$key] ) )
					{
						if( is_numeric( $key ) )
						{
							$key = strval( $key + ++self::$_counter + microtime( false) );
						}
						else
						{
							@$oldKey = $oldKey ? : $key;
							$key = $oldKey . @$counterI++;
						}
				//		self::v( $key ); 
					}
				}
				$key = str_replace( DS, '/', $key );
				$files[$key] = $file; 
			}
			elseif( trim( $filename, '.' ) && is_dir( $file ) && @$options['return_directories'] )
			{

				$files[$file] = $file; 
			}
			
			if( $filePath = Ayoola_Loader::checkFile( $file ) )
			{ 
				//	self::v( $filePath );
 				if( in_array( $filePath, $files ) )
				{
				//	self::v( $filePath );
					continue;
				}
                $extension = array_pop( explode( ".", strtolower( $file ) ) );
                if( is_array( @$options['whitelist_extensions'] ) && ! in_array( $extension, @$options['whitelist_extensions'] ) )
                {
                    continue;
                }
                $basename = basename( strtolower( $file ) );
                if(  is_array( @$options['whitelist_basename'] ) && ! in_array( $basename, @$options['whitelist_basename'] ) )
                {
                    continue;
                }
				//	self::v( $options['key_function'] ); 
 				$key = $filePath;
				if( ! empty( $options['key_function'] ) )
				{
					$key = $options['key_function'];
					$key = $key( $filePath );
				}
				if( isset( $files[$key] ) ) 
				{
					while( isset( $files[$key] ) )
					{
						if( is_numeric( $key ) )
						{
							$key = strval( $key + ++self::$_counter + microtime( false) );
						//	self::v( $filePath ); 
						}
						else
						{
							@$oldKey = $oldKey ? : $key;
							$key = $oldKey . @$counterI++;
						}
					}
				}
				$key = str_replace( DS, '/', $key );
				$filePath = str_replace( DS, '/', $filePath );
				$files[$key] = $filePath; 
		//		var_export( $key );
			}
		}
		closedir( $handle );
		ksort( $files, SORT_NUMERIC );
	//	( $files );
		$files = array_unique( $files );
	//	self::v( $files );
		if( empty( $options['no_cache'] ) )
		{
		//	$storage->store( $files );		
		}
		static::$_properties[__METHOD__][$keyZ] = $files;
		return $files;
    } 
	
    /**
     * Retrieve the files present in a directory
     *
     * @param string The Directory to look in
     * @return array filenames in the searched directories
     */
    static public function getFilesRecursive( $directory, array $options = null )
    {
		$files = self::getFiles( $directory, $options );
	//	var_export( $files );
		$directories = self::getDirectoriesRecursive( $directory );
	//	if( stripos( $directory, 'localuser' ) )
		{
	//		PageCarton_Widget::v( $files );
	//		PageCarton_Widget::v( $directories );
		}
		foreach( $directories as $directory )
		{
		//	$files = array_merge( $files, self::getFiles( $directory, $options ) );
		//	$files = @array_merge( $files, self::getFiles( $directory, $options ) ) ? : array();
		//	self::v( self::getFiles( $directory, $options ) );
			$files = @array_merge( $files, self::getFiles( $directory, $options ) ) ? : array();
		//	$files += self::getFiles( $directory, $options ) ? : array();
		}
	//	self::v( $files );
		ksort( $files, SORT_NUMERIC );
		return $files;
    } 
	
    /**
     * Retrieve the directories present in a directory
     *
     * @param string The Directory to look in
     * @return array Directories in the searched directories
     */
    static public function getDirectories( $directory )
    {
		$directories = array();
  //      if( basename( $directory ) === 'data' )
        {
   //    PageCarton_Widget::v( $directory );
  //     PageCarton_Widget::v( is_dir( trim( $directory ) ) );
    //   PageCarton_Widget::v( $directories );
        }
		if ( ! is_dir( $directory )) 
		{
			return array();
		//	throw new Ayoola_Doc_Exception( 'Invalid Directory - ' . $directory );
		}
		if ( ! ( $handle = opendir( $directory ) ) ) 
		{
			return array();
		//	throw new Ayoola_Doc_Exception( 'Directory cannot be opened  for reading - ' . $directory );
		}
		while ( ( $innerDirectoryBaseName = readdir( $handle ) ) !== false ) 
		{
			$innerDirectory = $directory . DS . $innerDirectoryBaseName;
		//	if( basename( $directory ) === 'data' )
			{
		//		PageCarton_Widget::v( $innerDirectoryBaseName );
		//		PageCarton_Widget::v( is_dir( $innerDirectory ) );
		//		PageCarton_Widget::v( trim( $innerDirectoryBaseName, '.' ) );
	//     PageCarton_Widget::v( is_dir( trim( $directory ) ) );
		//   PageCarton_Widget::v( $directories );
			}
			if ( trim( $innerDirectoryBaseName, '.' ) !== '' &&  is_dir( $innerDirectory )  )
			{ 
				$directories[] = $innerDirectory; 
			}
		}
		closedir( $handle );
  //      if( basename( $directory ) === 'data' )
        {
   	//	    PageCarton_Widget::v( $directory );
   	//	    PageCarton_Widget::v( $directories );
  //     PageCarton_Widget::v( is_dir( trim( $directory ) ) );
    //   PageCarton_Widget::v( $directories );
        }
		//var_export( $directories );
		return $directories;
    } 
	
    /**
     * Recursive version of getDocumentDirectories Method
     *
     * @param string The Directory to look in
     * @return array Directories in the searched directories
     */
    static public function getDirectoriesRecursive( $directory )
    {
		$recursiveDirectories = array();
		$directories = self::getDirectories( $directory );
        if( basename( $directory ) === 'data' )
        {
    //   PageCarton_Widget::v( $directory );
    //   PageCarton_Widget::v( $directories );
        }

		foreach( $directories as $directory )
		{
			$method = __FUNCTION__;
			$directorySDirectory = self::$method( $directory );
			$recursiveDirectories = array_merge( $recursiveDirectories, $directorySDirectory );
		}
		$recursiveDirectories = array_merge( $directories, $recursiveDirectories );
		//var_export( $directory );
		return $recursiveDirectories;
    } 
	
    /**
     * Attempts to remove dirs recursively in case
     *
     * @param string Path to Directory to be deleted
     * @return void
     */
    public static function removeDirectory( $dir, $deleteContent = false )
    {
		if( ! $deleteContent )
		{
			// Remove dir recursively
            while( is_dir( $dir ) && rmdir( $dir ) )
            { 
                $dir = dirname( $dir );
            }
		}
		else
		{
			self::deleteDirectoryPlusContent( $dir );
		}
		return ! is_dir( $dir );
    } 
	
    /**
     * Attempts to remove dirs recursively in case
     *
     * @param string Path to Directory to be deleted
     * @return void
     */
	public static function deleteDirectoryPlusContent($path) {
		if (!is_dir($path)) {
			return false;
		//	throw new Ayoola_Doc_Exception("$path is not a directory");
		}
		if (substr($path, strlen($path) - 1, 1) != '/') {
			$path .= '/';
		}
		$dotfiles = glob($path . '.*', GLOB_MARK);
		$files = glob($path . '*', GLOB_MARK);
		$files = array_merge($files, $dotfiles);
	//	var_export( '' . count( $files ) . '' );
		foreach ($files as $file) {
			if (basename($file) == '.' || basename($file) == '..') {
				continue;
			} else if (is_dir($file)) {
				self::deleteDirectoryPlusContent($file);
			} else {
			    //	var_export( $file );
                unlink($file);
			}
		}
		@rmdir( $path );
		return ! is_dir( $path );
	}	
	
	/**
	 * Copy a file, or recursively copy a folder and its contents
	 * @author      Aidan Lister <aidan@php.net>
	 * @version     1.0.1
	 * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
	 * @param       string   $source    Source path
	 * @param       string   $dest      Destination path
	 * @param       string   $permissions New folder creation permissions
	 * @return      bool     Returns true on success, false on failure
	 */
	public static function recursiveCopy($source, $dest, $permissions = 0755)  
	{
		// Check for symlinks
		if( is_link( $source ) ) {  
			return symlink(readlink($source), $dest);
		}

		// Simple copy for a file
		if (is_file($source)) {
			return copy($source, $dest);
        }
        elseif( ! is_dir( $source ) )
        {
            return false;
        }

		// Make destination directory
		if (!is_dir($dest)) {
			mkdir($dest, $permissions);
		}

		// Loop through the folder
        if( ! $dir = dir($source) )
        {
            return false;
        }
		while (false !== $entry = $dir->read()) {
			// Skip pointers
			if ($entry == '.' || $entry == '..') {
				continue;
			}

			// Deep copy directories
			self::recursiveCopy("$source/$entry", "$dest/$entry", $permissions);
		}

		// Clean up
		$dir->close();
		return true;
	}

	/**
     * Creates the directory
     * For the page and for template
     * @param void
     * @return boolean
     */
    public static function createDirectory( $dir, $permission = 0700, $recursive = true )
    {
		if( ! is_dir( $dir ) )
		{
			if( ! mkdir( $dir, $permission, true ) )
			{
			//	var_export( $dir );    
				return false; 
			}
		}
		//	Returns true altogether because this is not required per say
		return true;
    } 

	/**
     * 
     * @param string Full Path
     * @param string Path relative to
     * @return string directory
     */
    public static function getRelativePath( $path, $baseDir = PC_BASE )
    {
        $baseDir = str_ireplace( DS, '/', $baseDir );
        $path = str_ireplace( DS, '/', $path );
        
        $path = str_ireplace( $baseDir, '', $path );
        return $path;
    } 

	/**
     * 
     * @param void
     * @return boolean
     */
    public static function getLogo()
    {
		$logo = self::uriToDedicatedUrl( '/img/logo.png' );
		return $logo;
    } 
	
    /**
     * This method sets the _classOptions property to a value
     *
     * @param directory to Look in
     * @return void
     */
    public function setClassOptions( $directory = null )
    {
		$options = $this->getDbData();
		require_once 'Ayoola/Filter/SelectListArray.php';
		$filter = new Ayoola_Filter_SelectListArray( 'document_url', 'document_name');
		$options = $filter->filter( $options );
//		var_export( $options );
		$options = self::pathsToUris( $options );
		$this->_classOptions = (array) $options;
//		var_export( $this->_classOptions );
	} 	
	
    /**
     * This method returns the _classOptions property
     *
     * @param void
     * @return array
     */
    public function getClassOptions()
    {
		if( null === $this->_classOptions )
		{
			$this->setClassOptions();
		}
		//var_export( $this->_classOptions );
		return (array) $this->_classOptions;
    } 	
	
    /**
     * This method return the value of _viewOption property
     *
	 * @return mixed
     */
    public function getViewOption()
    {
		return $this->_viewOption;
    } 	
	
    /**
     * This method sets the _viewOption property to a value
     *
     * @param mixed The Value for the ViewableObjecect Option URI Path
     * @return string
     */
    public function setViewOption( $value )
    {
	//	var_export( is_file( $value ) );
		$path = array();
		$path['include'] = $value;
		if( $value )
		{
			if( ! is_file( $value ) )
			{
			//	require_once 'Ayoola/Filter/UriToPath.php';
			//	$filter = new Ayoola_Filter_UriToPath();
			//	$path = $filter->filter( $value );
			}
		}
    //    var_export( $path );
    //    exit();
        $realPath = self::getDocumentPath( $value );
        if( is_file( $value ) )
        {
            $realPath = $value;
        }
    //    var_export( $value );
    //    var_export( $realPath );
    //    exit();
		$this->loadFile( $realPath ); 
        //		$this->loadFile( $path['include'] ); 
	//	try{ $this->loadFile( $path['include'] ); } 
		//	Document not found
	//	catch( Ayoola_Doc_Exception $e ){ return false; }
    //    var_export( $path );
    //    exit();
		$this->_viewOption = $realPath;
    } 
	
    /**
	 * Just incoporating this - So that the layout can be more interative
	 * The layout editor will be able to pass a parameter to the viewable object				
     * @param mixed Parameter set from the layout editor
     * @return null
     */
    public function setViewParameter( $parameter )
	{
		//	If there is a view parameter, we should be in inline mode
		$this->getAdapter()->setInlineViewMode( true );
		$this->getAdapter()->title = $parameter;
	}

    /**
     * Makes this class viewable for ayoola class player
     *
     * @param string Filename
     * @return string Mark-up
     */
    public static function viewInLine( $viewParameter = null, $viewOption = null )
    {
		if( is_null( $viewParameter ) && is_null( $viewOption ) )
		{
			throw new Ayoola_Doc_Exception( 'We must have either a view parameter or option to run this method' );
		}
		$docView = self::getInstance();
		$docView->setViewParameter( $viewParameter );
		$docView->setViewOption( $viewOption );
		return $docView->view();
    } 	

    /**
     * Makes this class viewable for ayoola class player
     *
     * @param void
     * @return string Mark-up
     */
    public function view()
    {	
//		self::v( $this->getPaths() );
//		exit();
		$this->getAdapter()->setPaths( $this->getPaths()  );
	//	self::v( $this->getAdapter()->getLoaders() );
		return $this->getAdapter()->view();
    } 
	
    /**
     * Force the download of the document
     *
     * @param void
     * @return void
     */
    public function download()
    {
		$this->getAdapter( $this->getPaths() )->download();
    } 
	// END OF CLASS
}
