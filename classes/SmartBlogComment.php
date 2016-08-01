<?php

class SmartBlogComment extends ObjectModel
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

    /**
     * Blogcomment constructor.
     * @param int|null $id
     * @param int|null $id_shop
     */
    public function __construct($id = null, $id_shop = null)
    {
        Shop::addTableAssociation('smart_blog_comment', array('type' => 'shop'));
        parent::__construct($id, $id_shop);
    }

    /**
     * Add comment
     *
     * @param int $id_post Post ID
     * @param string $comment Comment
     * @param int $value Active
     * @param int $id_parent Parent ID
     * @return bool Whether comment has been successfully added
     * @throws PrestaShopDatabaseException
     */
    public function addComment($id_post, $comment, $value, $id_parent)
    {
        if ($id_parent == '' && $id_parent == null) {
            $id_parent = 0;
        }

        return Db::getInstance()->insert(
            'smart_blog_comment',
            array(
                'id_post' => (int) $id_post,
                'name' => pSQL($comment['name']),
                'email' => pSQL($comment['email']),
                'content' => pSQL($comment['comment'], true),
                'website' => pSQL($comment['website']),
                'id_parent' => (int) $id_parent,
                'active' => (int) $value,
            )
        );
    }

    /**
     * Get child comment
     *
     * @param int $idParent Parent SmartBlogComment ID
     * @return array|bool|false|mysqli_result|null|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public function getChildComment($idParent)
    {
        $child_comments = null;

        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('smart_blog_comment', 'sbc');
        $sql->where('sbc.`active` = 1');
        $sql->where('sbc.`id_parent` = '.(int) $idParent);

        if (!$child_comments = DB::getInstance()->executeS($sql)) {
            return false;
        }
        $j = 0;

        if (isset($child_comments) && (count($child_comments) > 0)) {
            foreach ($child_comments as $ch_comment) {

                if ($this->comment_child_loop <= $this->comment_child_loop_depth) {
                    $coments_2 = $this->getChildComment($ch_comment['id_smart_blog_comment']);
                    if (count($coments_2) > 0) {
                        $child_comments[$j]['child_comments'] = $coments_2;
                    }
                }
                $j++;
                $this->comment_child_loop++;
            }
        }

        return $child_comments;
    }

    /**
     * Get comment
     *
     * @param int $idPost SmartBlogPost ID
     * @return array|bool|false|mysqli_result|null|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public function getComments($idPost)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('smart_blog_comment', 'sbc');
        $sql->where('sbc.`active` = 1');
        $sql->where('sbc.`id_parent` = 0');
        $sql->where('sbc.`id_post` = '.(int) $idPost);
        if (!$comments = DB::getInstance()->executeS($sql)) {
            return false;
        }
        $i = 0;
        foreach ($comments as $comment) {
            $coments = $this->getChildComment($comment['id_smart_blog_comment']);

            if (count($coments) > 0) {
                $comments[$i]['child_comments'] = $coments;
            }

            $i++;
            $this->comment_child_loop++;
        }

        return $comments;
    }

    /**
     * Get latest comments
     *
     * @param null|int $idLang Language ID
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public static function getLatestComments($idLang = null)
    {
        if (empty($idLang)) {
            $idLang = (int) Context::getContext()->language->id;
        }
        if (Configuration::get('smartshowhomecomments') != '' && Configuration::get('smartshowhomecomments') != null) {
            $limit = Configuration::get('smartshowhomecomments');
        } else {
            $limit = 5;
        }
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('smart_blog_comment', 'sbc');
        $sql->innerJoin('smart_blog_post_lang', 'sbpl', 'sbc.`id_post` = sbpl.`id_smart_blog_post`');
        $sql->innerJoin('smart_blog_post_shop', 'sbps', 'sbc.`id_post` = sbps.`id_smart_blog_post`');
        $sql->where('sbc.`active` = 1');
        $sql->where('sbps.`id_shop` = '.(int) Context::getContext()->shop->id);
        $sql->where('sbpl.`id_lang` = '.(int) $idLang);
        $sql->orderBy('sbc.`id_smart_blog_comment` DESC');
        $sql->limit((int) $limit, 0);
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $i = 0;
        foreach ($result as $post) {
            $result[$i]['id_smart_blog_comment'] = $post['id_smart_blog_comment'];
            $result[$i]['id_parent'] = $post['id_parent'];
            $result[$i]['id_customer'] = $post['id_customer'];
            $result[$i]['id_post'] = $post['id_post'];
            $result[$i]['name'] = $post['name'];
            $result[$i]['email'] = $post['email'];
            $result[$i]['website'] = $post['website'];
            $result[$i]['active'] = $post['active'];
            $result[$i]['created'] = $post['created'];
            $result[$i]['content'] = $post['content'];
            $SmartBlogPost = new  SmartBlogPost();
            $result[$i]['slug'] = $SmartBlogPost->GetPostSlugById($post['id_post']);
            $i++;
        }

        return $result;
    }

    /**
     * Get total posts
     *
     * @param int $id SmartBlogPost ID
     * @return bool|int
     * @throws PrestaShopDatabaseException
     */
    public static function getTotalPosts($id)
    {
        $sql = new DbQuery();
        $sql->select('COUNT(sbc.`id_post`)');
        $sql->from('smart_blog_comment', 'sbc');
        $sql->where('sbc.`id_post` = '.(int) $id);
        $sql->where('sbc.`active` = 1');
        if (!$posts = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql)) {
            return false;
        }

        return $posts;
    }
}
