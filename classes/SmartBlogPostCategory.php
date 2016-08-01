<?php


class SmartBlogPostCategory extends ObjectModel
{
    public $id_smart_blog_post_category;
    public $id_author;
    public static $definition = array(
        'table' => 'smart_blog_post_category',
        'primary' => 'id_smart_blog_post_category',
        'multilang' => false,
        'fields' => array(
            'id_smart_blog_post_category' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'id_smart_blog_category' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
        ),
    );

    public static function getToltalByCategory($id_lang, $id_category, $limit_start, $limit)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('smart_blog_post_lang', 'sbpl');
        $sql->innerJoin('smart_blog_post', 'sbp', 'sbp.`id_smart_blog_post` = sbpl.`id_smart_blog_post`');
        $sql->where('sbpl.`id_lang` = '.(int) $id_lang);
        $sql->where('sbp.`active` = 1');
        $sql->where('sbpl.`id_smart_blog_post` = sbp.`id_smart_blog_post`');
        $sql->where('sbp.`id_category` = '.(int) $id_category);
        $sql->orderBy('sbp.`id_smart_blog_post` DESC');
        $sql->limit((int) $limit, (int) $limit_start);

        if (!$posts = Db::getInstance()->executeS($sql)) {
            return false;
        }

        $i = 0;
        $BlogCategory = new SmartBlogCategory();
        foreach ($posts as $post) {
            $result[$i]['id_post'] = $post['id_smart_blog_post'];
            $result[$i]['viewed'] = $post['viewed'];
            $result[$i]['meta_title'] = $post['meta_title'];
            $result[$i]['meta_description'] = $post['meta_description'];
            $result[$i]['short_description'] = $post['short_description'];
            $result[$i]['content'] = $post['content'];
            $result[$i]['meta_keyword'] = $post['meta_keyword'];
            $result[$i]['id_category'] = $post['id_category'];
            $result[$i]['link_rewrite'] = $post['link_rewrite'];
            $result[$i]['cat_link_rewrite'] = $BlogCategory->getCategoryLinkRewrite($post['id_category']);
            $employee = new  Employee($post['id_author']);

            $result[$i]['lastname'] = $employee->lastname;
            $result[$i]['firstname'] = $employee->firstname;
            if (file_exists(_PS_MODULE_DIR_.'smartblog/images/'.$post['id_smart_blog_post'].'.jpg')) {
                $image = $post['id_smart_blog_post'];
                $result[$i]['post_img'] = $image;
            } else {
                $result[$i]['post_img'] = 'no';
            }
            $result[$i]['created'] = $post['created'];
            $i++;
        }

        return $result;
    }

}