<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Blogcomment extends ObjectModel
{

    public $id_smart_blog_comment;
    public $id_parent;
    public $id_customer;
    public $id_post;
    public $name;
    public $email;
    public $website;
    public $content;
    public $active = 1;
    public $created;
    public static $definition = array(
        'table' => 'smart_blog_comment',
        'primary' => 'id_smart_blog_comment',
        'multilang' => false,
        'fields' => array(
            'id_parent' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'id_post' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'email' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'website' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'content' => array('type' => self::TYPE_HTML, 'validate' => 'isString'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'created' => array('type' => self::TYPE_DATE, 'validate' => 'isString'),
        ),
    );
    public $comment_child_loop = 0;
    public $comment_child_loop_depth = 2;

    public function __construct($id = null, $id_shop = null)
    {
        Shop::addTableAssociation('smart_blog_comment', array('type' => 'shop'));
        parent::__construct($id, $id_shop);
    }

    public function addComment($id_post, $comment, $value, $id_parent)
    {
        if ($id_parent == '' && $id_parent == null) {
            $id_parent = 0;
        }
        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'smart_blog_comment(id_post,name,email,content,website,id_parent, active) VALUES (' . (int) $id_post . ', \'' . $comment['name'] . '\', \'' . $comment['mail'] . '\', \'' . $comment['comment'] . '\', \'' . $comment['website'] . '\', ' . $id_parent . ', ' . $value . ')';
        if (!Db::getInstance()->execute($sql))
            return false;
    }

    public function getChildComment($id_parent)
    {

        $child_comments = NULL;

        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_comment 
                WHERE active=1 AND id_parent=' . $id_parent;
        if (!$child_comments = DB::getInstance()->executeS($sql))
            return false;
        $j = 0;

        if (isset($child_comments) && (count($child_comments) > 0)) {
            foreach ($child_comments as $key => $ch_comment) {
                $child_comments[$key]['content'] = str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n"),"<br/>",$ch_comment['content']);
                if ($this->comment_child_loop <= $this->comment_child_loop_depth) {
                    $coments_2 = $this->getChildComment($ch_comment['id_smart_blog_comment']);
                    if (count($coments_2) > 0)
                        $child_comments[$j]['child_comments'] = $coments_2;
                }
                $j++;
                $this->comment_child_loop++;
            }
        }
        return $child_comments;
    }

    public function getComment($id_post)
    {
 
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_comment 
                WHERE active=1  AND id_parent=0 AND id_post=' . $id_post;
        if (!$comments = DB::getInstance()->executeS($sql))
            return false;
        $i = 0;
        foreach ($comments as $key => $comment) {
            $comments[$key]['content'] = str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n"),"<br/>",$comment['content']);
            $coments = $this->getChildComment($comment['id_smart_blog_comment']);

            if (count($coments) > 0)
                $comments[$i]['child_comments'] = $coments;

            $i++;
            $this->comment_child_loop++;
        }
        return $comments;
    }

    public static function getLatestComments($id_lang = null)
    {
        $result = array();
        if ($id_lang == null) {
            $id_lang = (int) Context::getContext()->language->id;
        }
        if (Configuration::get('smartshowhomecomments') != '' && Configuration::get('smartshowhomecomments') != null) {
            $limit = Configuration::get('smartshowhomecomments');
        } else {
            $limit = 5;
        }
        $sql = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'smart_blog_comment bc INNER JOIN 
                ' . _DB_PREFIX_ . 'smart_blog_post_shop bps ON bc.id_post=bps.id_smart_blog_post and bps.id_shop = ' . (int) Context::getContext()->shop->id . '
                WHERE  bc.active= 1 ORDER BY bc.id_smart_blog_comment DESC limit 0, ' . $limit);

        $i = 0;
        foreach ($sql as $post) {
            $result[$i]['id_smart_blog_comment'] = $post['id_smart_blog_comment'];
            $result[$i]['id_parent'] = $post['id_parent'];
            $result[$i]['id_customer'] = $post['id_customer'];
            $result[$i]['id_post'] = $post['id_post'];
            $result[$i]['name'] = $post['name'];
            $result[$i]['email'] = $post['email'];
            $result[$i]['website'] = $post['website'];
            $result[$i]['active'] = $post['active'];
            $result[$i]['created'] = $post['created'];
            $result[$i]['content'] = str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n"),"<br/>",$post['content']);
            $SmartBlogPost = new SmartBlogPost();
            $result[$i]['link_rewrite'] = $SmartBlogPost->GetPostSlugById($post['id_post']);
            $result[$i]['slug'] = $SmartBlogPost->GetPostSlugById($post['id_post']);            
            $i++;
        }
        return $result;
    }

    public static function getToltalComment($id)
    {

        $sql = 'SELECT id_post FROM ' . _DB_PREFIX_ . 'smart_blog_comment 
                WHERE id_post=' . $id . ' 
                AND active=1';
        if (!$posts = Db::getInstance()->executeS($sql))
            return false;
        return count($posts);
    }

}
