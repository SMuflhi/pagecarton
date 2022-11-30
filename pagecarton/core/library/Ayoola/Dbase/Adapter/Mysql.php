<?php
/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Ayoola_Dbase_Adapter_Mysql
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Mysql.php 1.23.12 8.11 ayoola $
 */

/**
 * @see Ayoola_Dbase_Adapter_Interface
 */
 
require_once 'Ayoola/Dbase/Adapter/Interface.php';


/**
 * @category   PageCarton
 * @package    Ayoola_Dbase_Adapter_Mysql
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Ayoola_Dbase_Adapter_Mysql extends Ayoola_Dbase_Adapter_Abstract
{
    /**
     * Link to the database connection
     *
     * @var resource
     */
	protected $_link;
	
    /**
     * Last Database query
     *
     * @var string
     */
	protected $_lastQuery;
	
    /**
     * last prepared statement
     *
     * @var object
     */
	protected $_stmt;
	
    /**
     * Result from the last query
     *
     * @var object
     */
	protected $_lastResult;
	
    /**
     * Id from the last insert operation
     *
     * @var int
     */
	protected $_lastInsertId;

    /**
     * Constructor
     *
     * @param array Database Info
     * 
     */
    public function __construct( $databaseInfo = null )
    {
		if( ! is_null( $databaseInfo ) ){ $this->setDatabaseInfo( $databaseInfo ); }
    }
		
    /**
     * This method sets _link to a Value
     *
     * @param MySqli Link
     * @return null
     */
    public function setLink( $link = null )
    {
		if( ! is_resource( $link ) )
		{
        //    var_export( $this->getDatabaseInfo() );
            if( ! $dbInfo = Application_Database_Account::getInstance()->selectOne( null, array( 'database' => $this->getDatabaseInfo( 'database' ) ) ) )
            {
      //          $dbInfo = $this->getDatabaseInfo();
            }
        //    var_export( $this->getDatabaseInfo( 'database' ) );        
      //      var_export( $dbInfo );
         //   var_export( Application_Database_Account::getInstance()->select() );
        
		//	echo $this->getDatabaseInfo( 'username' );
			$link = mysqli_connect(  $dbInfo['hostname'] ? : $this->getDatabaseInfo( 'hostname' ), 
            $dbInfo['username'] ? : $this->getDatabaseInfo( 'username' ), 
            $dbInfo['password'] ? : $this->getDatabaseInfo( 'password' ), 
            $dbInfo['database'] ? : $this->getDatabaseInfo( 'database' ) 
            );
		}
		if ( $link )
		{ 
			// Ensure charset is utf-8
			mysqli_set_charset ( $link , 'utf8' );
			return $this->_link = $link; 
		}
		//	var_export( mysql_error()  );  
		require_once 'Ayoola/Dbase/Adapter/Exception.php';
		throw new Ayoola_Dbase_Adapter_Exception( 'CONNECTION FAILED TO DATABASE - ' . $this->getDatabaseInfo( 'database' ) );
    } 
	
    /**
     * This method returns _link
     *
     * @return MySqli Link
     */
    public function getLink()
    {
		if( is_null( $this->_link ) ){ $this->setLink(); }
		return $this->_link;
    } 
	
	/* 
	Connects to the database and 
	returns the link for that connection.
	@return resource
	*/	
    public function select( $databaseName = '' )
	{
		$databaseName = $databaseName ? : $this->getDatabaseInfo( 'database' );
    //    var_export( $this->getLink() );
        if ( mysqli_select_db( $this->getLink(), $databaseName ) ){ return true; }
    //	var_export( $this->getLink() );

		return true;
		require_once 'Ayoola/Dbase/Adapter/Exception.php';
		throw new Ayoola_Dbase_Adapter_Exception( 'CANNOT SELECT DATABASE - ' . $databaseName );
	}
        
    public function query( $query )
	{
		$query = (string) $query;
		$this->_lastQuery = $query;
    
        $result = false;
        if( function_exists( 'mysqli_query') )
        {
            $result = mysqli_query( $this->getLink(), $query );
        }

		$this->_lastResult = $result;
		if( $result )
		{
			$this->_lastResult = $result;
			return $result;
		}
		require_once 'Ayoola/Dbase/Adapter/Exception.php';    
		throw new Ayoola_Dbase_Adapter_Exception( 'DATABASE QUERY IS NOT SUCCESSFUL' );
	}

    /**
     * Returns last insert id
     *
     * @param void
     * @return int
     */
    public function getLastInsertId()
	{
		if( is_null( $this->_lastInsertId ) ){ $this->_lastInsertId = mysqli_insert_id( $this->getLink() ); }
        return $this->_lastInsertId;                
	}

    /**
     * Fetch Records as Multidimentional Array
     *
     * @param void
     * @return mixed
     */
    public function fetchAssoc()
	{
        return mysqli_fetch_assoc( $this->_lastResult );                
	}

    public function fetchAll()
    {
		$row = array();
		while( $data = $this->fetchAssoc() ){ $row[] = $data; }
		$this->freeResult();
		return $row;
    }	
    
    public function freeResult()
	{
        return mysqli_free_result( $this->_lastResult );                
	}
    
    public function insertId()
	{
        return mysqli_insert_id( $this->getLink() );                
	}
    
    public function numRow()
	{
	//	var_export( $this->_lastResult );
        return mysqli_num_rows( $this->_lastResult );                
	}
	
    /**
     * Returns list of tables 
     *
     * @param void
     * @return array
     */
    public function listTables()
    {
        $sql= 'SHOW TABLES';
		$this->query( $sql );
		$row = $this->fetchAll();
		return $row;
		
    } 
	
    /**
     * Returns details of a table
     *
     * @param 
     * @return array
     */
    public function getTableInfo( $tablename )
    {
        $sql= 'DESCRIBE ' . $tablename;
		$this->query( $sql );
		$row = $this->fetchAll();
		return $row;
    } 
	// END OF CLASS
}
