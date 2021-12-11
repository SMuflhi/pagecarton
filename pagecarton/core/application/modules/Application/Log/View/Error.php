<?php
/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Application_Log_View_Error
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Error.php 10.3.2012 7.55am ayoola $
 */

/**
 * @see Application_Log_Abstract
 */
 
//require_once 'Application/Log/Abstract.php';


/**
 * @category   PageCarton
 * @package    Application_Log_View_Error
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Application_Log_View_Error extends Application_Log_View_Abstract
{
	
    /**
     * Table where log goes to
     * 
     * @var string
     */
	protected static $_logTable = 'Application_Log_View_Error_Log';
		
    /**
     * Creates a log
     * 
     */
	public static function log( $message )
	{
		var_export( $message );
		$log = array( 'error_message' => $message, 'error_time' => time() );
		$mailInfo["subject"] = "Application Error";
		$mailInfo["body"] = $message;
		try
		{
	//		Ayoola_Application_Notification::mail( $mailInfo );
		}
        catch( Ayoola_Exception $e ){ null; }
        function_exists( 'http_response_code' ) ? http_response_code(500) : null;
		$message = "There is error on this page please reload your browser to continue. If this persist, contact the administrator. You can also go back to the <a href=\'/\'>homepage</a>";
	//	trigger_error( $message );
		echo "<div class='badnews'>$message</div>";
	//	echo $message;
	//	var_export( static::getLogTable() );
		$result = self::getLogTable()->insert( $log );
 	//	var_export( $result );
   }
	// END OF CLASS
}
