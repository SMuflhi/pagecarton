<?php
/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Application_Article_ViewPagination
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: View.php 5.11.2012 12.02am ayoola $
 */

/**
 * @see Application_Article_Abstract
 */
 
require_once 'Application/Article/Abstract.php';    

/**
 * @category   PageCarton
 * @package    Application_Article_ViewPagination
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Application_Article_ViewPagination extends Application_Article_Abstract
{
 	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'View Post Pagination'; 
	
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
     * Identifier for the column to edit
     * 
     * @var array
     */
	protected $_identifierKeys = array( 'article_name',  );

    /**
     * The xml document
     * 
     * @var Ayoola_Xml
     */
	protected $_xml;
	
    /**
     * The method does the whole Class Process
     * 
     */
	protected function init()
    {

		try
		{

			if( ! $data = $this->getIdentifierData() )
			{
				return false;				
			}

			if( ! self::isAllowedToView( $data ) )
			{				
				return false;
			}

			{
				$pagination = null;
                if( ! empty( $_REQUEST['pc_post_list_id'] ) )
                {
                    $postListId = $_REQUEST['pc_post_list_id'];
                }
                else
                {
					//	Prepare post viewing for next posts
					$storageForSinglePosts = self::getObjectStorage( array( 'id' => 'post_list_id' ) );
					
					$postListId = $storageForSinglePosts->retrieve();
					$postListData = Application_Article_ShowAll::getObjectStorage( array( 'id' => $postListId . '_single_post_pagination', 'device' => 'File' ) );

					if( ! $postListId || ! $postListData->retrieve() )
					{
						$parameters = array( 'true_post_type' => $data['true_post_type'], 'no_of_post_to_show' => 200, 'single_post_pagination' => true );
						if( ! empty( Ayoola_Application::$GLOBAL['post']['category_name'] ) )
						{
							$parameters['category_name'] = Ayoola_Application::$GLOBAL['post']['category_name'];
						}

						$class = new Application_Article_ShowAll( $parameters );
						$class->initOnce();
						$postListId = $storageForSinglePosts->retrieve();
					}
                }

				$postListData = Application_Article_ShowAll::getObjectStorage( array( 'id' => $postListId . '_single_post_pagination', 'device' => 'File' ) );

				$postListData = $postListData->retrieve();

				{
                    var_export( $postListData );
					$presentArticle = $data['article_url'];
                    {

						if( empty( $postListData[$presentArticle] ) )
						{
							$presentArticle = array_shift( array_keys( $postListData ) );
						}
						$postList = $postListData[$presentArticle];
                       	$postData = self::loadPostData( $postList );
						$presentArticle = $postList['pc_next_post'];

                    }

					if( ! empty( $postList['pc_next_post'] ) )
					{
						$nextPost = $postList['pc_next_post'];

						if( ! $nextPost = self::loadPostData( $nextPost ) )
						{
							//	if next is not valid article

						}

						$this->_objectTemplateValues['pc_next_post_title'] = $nextPost['article_title'];
						$this->_objectTemplateValues['pc_next_post_cover_photo'] = $nextPost['document_url'] ? : '/img/placeholder-image.jpg';
						$this->_objectTemplateValues['paginator_next_page'] = Ayoola_Application::getUrlPrefix() . $postList['pc_next_post'];
						$this->_objectTemplateValues['paginator_next_page_button'] = '<a onclick="this.href=this.href + location.search;" class="pc_paginator_next_page_button pc-btn" href="' . $this->_objectTemplateValues['paginator_next_page'] . '">"' . $nextPost['article_title'] . '" Next  &rarr; </a>';       

					}
					$postList['pc_previous_post'] = $postList['pc_previous_post'] ? : $postList['pc_next_post'];
					if( ! empty( $postList['pc_previous_post'] ) )
					{
						
						if( $previousPost = self::loadPostData( $postList['pc_previous_post'] ) )
						{

							$this->_objectTemplateValues['pc_previous_post_title'] = $previousPost['article_title'];
							$this->_objectTemplateValues['pc_previous_post_cover_photo'] = $previousPost['document_url'] ? : '/img/placeholder-image.jpg';
							$this->_objectTemplateValues['paginator_previous_page'] = Ayoola_Application::getUrlPrefix() . $postList['pc_previous_post'];
							$this->_objectTemplateValues['paginator_previous_page_button'] = '<a onclick="this.href=this.href + location.search;" class="pc_paginator_previous_page_button pc-btn" href="' . $this->_objectTemplateValues['paginator_previous_page'] . '"> &larr; Previous "' . $previousPost['article_title'] . '"</a>';
						}
					}
					$this->_objectTemplateValues['pc_next_post'] = $postList['pc_next_post'];
					$this->_objectTemplateValues['pc_previous_post'] = $postList['pc_previous_post'];
					$pagination .= @$this->_objectTemplateValues['paginator_previous_page_button'];
					$pagination .= @$this->_objectTemplateValues['paginator_next_page_button'];			
					$pagination = '<div class="pc_posts_distinguish_sets" id="' . $postListId . '">' . $pagination . '</div>';

				}

				$this->setViewContent( $pagination );
			}

		}
		catch( Exception $e )
		{ 

			$this->setViewContent(  '' . self::__( '<p class="badnews">' . $e->getMessage() . '</p>' ) . '', true  );
			return $this->setViewContent( self::__( '<p class="badnews">Error with article package.</p>' ) ); 
		}

    } 
	// END OF CLASS
}
