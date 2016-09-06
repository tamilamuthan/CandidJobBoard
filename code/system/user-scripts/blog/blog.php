<?php

class SJB_Blog_Blog extends SJB_Function
{
    public function execute()
    {
        $tp = SJB_System::getTemplateProcessor();

        $params = SJB_UrlParamProvider::getParams();
        if ($params) {
            $param = array_shift($params);
            switch ($param) {
                case 'rss':
                    header('Content-Type: application/rss+xml');
                    $posts = SJB_BlogManager::getBlogPosts('date', 'DESC', SJB_Request::getVar('page', 1), 10, true);
                    $tp->assign('posts', $posts);
                    $tp->display('blog_rss.tpl');
                    exit();
                    break;
                default:
                    $post = SJB_BlogManager::getBlogPostInfoBySid($param, true);
                    if ($post) {
                        $tp->assign('post', $post);
                        $tp->display('blog_item.tpl');
                    } else {
                        echo SJB_System::executeFunction('miscellaneous', '404_not_found');
                    }
                    break;
            }
            return;
        }

        $posts = SJB_BlogManager::getBlogPosts('date', 'DESC', SJB_Request::getVar('page', 1), 10, true);
        $tp->assign('posts', $posts);
        $tp->display('blog.tpl');
    }
}