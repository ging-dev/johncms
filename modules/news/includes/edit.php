<?php

/**
 * This file is part of JohnCMS Content Management System.
 *
 * @copyright JohnCMS Community
 * @license   https://opensource.org/licenses/GPL-3.0 GPL-3.0
 * @link      https://johncms.com JohnCMS Project
 */

declare(strict_types=1);

defined('_IN_JOHNCMS') || die('Error: restricted access');

/**
 * @var PDO $db
 * @var Johncms\System\Legacy\Tools $tools
 * @var Johncms\System\Users\User $user
 * @var Johncms\System\View\Render $view
 */

// Editing news
if ($user->rights >= 6) {
    // Add an item to the navigation chain
    $nav_chain->add(__('Edit news'), '');

    if (! $id) {
        echo $view->render(
            'system::pages/result',
            [
                'title'    => __('News'),
                'type'     => 'alert-danger',
                'message'  => __('Wrong data'),
                'back_url' => '/news/',
            ]
        );
        exit;
    }

    if (! empty($_POST)) {
        $error = [];

        if (empty($_POST['name'])) {
            $error[] = __('You have not entered news title');
        }

        if (empty($_POST['text'])) {
            $error[] = __('You have not entered news text');
        }

        $name = htmlspecialchars(trim($_POST['name']));
        $text = trim($_POST['text']);

        if (! $error) {
            $db->prepare(
                '
                      UPDATE `news` SET
                      `name` = ?,
                      `text` = ?
                      WHERE `id` = ?
                    '
            )->execute(
                [
                    $name,
                    $text,
                    $id,
                ]
            );
        } else {
            echo $view->render(
                'system::pages/result',
                [
                    'title'    => __('Edit news'),
                    'message'  => $error,
                    'type'     => 'alert-danger',
                    'back_url' => '/news/edit/' . $id,
                ]
            );
        }

        echo $view->render(
            'system::pages/result',
            [
                'title'    => __('Edit news'),
                'message'  => __('News changed'),
                'type'     => 'alert-success',
                'back_url' => '/news/',
            ]
        );
    } else {
        $res = $db->query("SELECT * FROM `news` WHERE `id` = '${id}'")->fetch();
        echo $view->render(
            'news::edit',
            [
                'id'   => $id,
                'name' => $res['name'],
                'text' => htmlentities($res['text'], ENT_QUOTES, 'UTF-8'),
            ]
        );
    }
} else {
    pageNotFound();
}
