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
 * @var Johncms\System\Users\User $user
 * @var Johncms\System\View\Render $view
 */

// News cleaning
if ($user->rights >= 7) {
    // Add an item to the navigation chain
    $nav_chain->add(__('Clear news'), '');
    if (! empty($_POST)) {
        $cl = isset($_POST['cl']) ? (int) ($_POST['cl']) : '';

        switch ($cl) {
            case '1':
                // We clean the news, older than 1 week
                $db->query('DELETE FROM `news` WHERE `time` <= ' . (time() - 604800));
                $db->query('OPTIMIZE TABLE `news`');
                $message = __('Delete all news older than 1 week');
                break;

            case '2':
                // Perform a full cleanup
                $db->query('TRUNCATE TABLE `news`');
                $message = __('Delete all news');
                break;
            default:
                // Clean messages older than 1 month
                $db->query('DELETE FROM `news` WHERE `time` <= ' . (time() - 2592000));
                $db->query('OPTIMIZE TABLE `news`;');
                $message = __('Delete all news older than 1 month');
        }

        echo $view->render(
            'system::pages/result',
            [
                'title'    => __('Clear news'),
                'message'  => $message,
                'type'     => 'alert-success',
                'back_url' => '/news/',
            ]
        );
    } else {
        echo $view->render('news::clear');
    }
} else {
    pageNotFound();
}
