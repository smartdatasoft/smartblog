<?php

$idtabs = array();
$idtabs[] = Tab::getIdFromClassName('AdminSmartBlog');
$idtabs[] = Tab::getIdFromClassName('AdminBlogCategory');
$idtabs[] = Tab::getIdFromClassName('AdminBlogcomment');
$idtabs[] = Tab::getIdFromClassName('AdminBlogPost');
$idtabs[] = Tab::getIdFromClassName('AdminImageType');
$idtabs[] = Tab::getIdFromClassName('AdminAboutUs');

foreach ($idtabs as $tabid) {
    if ($tabid) {
        $tab = new Tab($tabid);
        $tab->delete();
    }
}
