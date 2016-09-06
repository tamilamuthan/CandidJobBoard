<?php

class SJB_Admin_Blog_Blog extends SJB_Function
{
    public function execute()
    {
        $errors = array();
        $messages = array();
        $action = SJB_Request::getVar('action', false);
        $tp = SJB_System::getTemplateProcessor();

        switch ($action) {
            case 'edit':
                $postId = SJB_Request::getVar('id', false);

                if (!$postId) {
                    break;
                } else {
                    $postInfo = SJB_BlogManager::getBlogPostInfoBySid($postId);
                    $postInfo = array_merge($postInfo, $_REQUEST);

                    $blogPost = new SJB_BlogPost($postInfo);
                    $blogPostForm = new SJB_Form($blogPost);
                    $blogPostForm->registerTags($tp);

                    $formSubmitted = SJB_Request::getVar('form_submit', false);
                    if ($formSubmitted && $blogPostForm->isDataValid($errors)) {
                        $blogPost->setSID($postId);
                        SJB_BlogDBManager::saveBlogPost($blogPost);
                        SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/blog/');
                    } else {
                        $formFields = $blogPostForm->getFormFieldsInfo();

                        $tp->assign('form_fields', $formFields);
                        $tp->assign('postId', $postId);

                        $metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
                        $tp->assign(
                            "METADATA",
                            array(
                                "form_fields" => $metaDataProvider->getFormFieldsMetadata($formFields),
                            )
                        );
                    }
                }

                $tp->assign('errors', $errors);
                $tp->display('edit_blog_post.tpl');
                return;
                break;

            case 'add':
                $blogPost = new SJB_BlogPost($_REQUEST);
                $blogPostForm = new SJB_Form($blogPost);
                $blogPostForm->registerTags($tp);

                $formSubmitted = SJB_Request::getVar('form_submit', false);
                if ($formSubmitted && $blogPostForm->isDataValid($errors)) {
                    SJB_BlogDBManager::saveBlogPost($blogPost);
                    if ($blogPost->getSID()) {
                        SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/blog/");
                        exit;
                    } else {
                        $errors[] = 'UNABLE_TO_ADD_BLOG_POST';
                    }
                } else {
                    if (!$formSubmitted) {
                        $blogPost->setPropertyValue('date', date('Y-m-d'));
                    }

                    $blogPostForm = new SJB_Form($blogPost);
                    $blogPostForm->registerTags($tp);
                    $formFields = $blogPostForm->getFormFieldsInfo();

                    $tp->assign('form_fields', $formFields);

                    $metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
                    $tp->assign(
                        "METADATA",
                        array(
                            "form_fields" => $metaDataProvider->getFormFieldsMetadata($formFields),
                        )
                    );
                }
                $tp->assign('errors', $errors);
                $tp->display('add_blog_post.tpl');
                return;
                break;

            case 'delete':
                $itemSIDs = SJB_Request::getVar('posts');
                foreach ($itemSIDs as $sid => $item) {
                    SJB_BlogManager::delete($sid);
                }
                break;

            case 'activate':
                $itemSIDs = SJB_Request::getVar('posts');
                foreach ($itemSIDs as $sid => $item) {
                    SJB_BlogManager::activate($sid);
                }
                break;

            case 'deactivate':
                $itemSIDs = SJB_Request::getVar('posts');
                foreach ($itemSIDs as $sid => $item) {
                    SJB_BlogManager::deactivate($sid);
                }
                break;

            case 'delete_image':
                $postId = SJB_Request::getVar('id');

                SJB_BlogManager::deleteBlogPostImage($postId);

                SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/blog/?action=edit&id={$postId}");
                break;
        }

        $paginator = new SJB_BlogPagination();
        $paginator->setItemsCount(SJB_BlogManager::getAllPostsCount());

        $posts  = SJB_BlogManager::getBlogPosts($paginator->sortingField, $paginator->sortingOrder, $paginator->currentPage, $paginator->itemsPerPage);
        $paginationInfo = $paginator->getPaginationInfo();
        $tp->assign('paginationInfo', $paginationInfo);
        $tp->assign('messages', $messages);
        $tp->assign('errors', $errors);
        $tp->assign('posts', $posts);
        $tp->display('blog.tpl');
    }
}
