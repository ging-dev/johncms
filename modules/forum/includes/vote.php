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
 * @var Johncms\Api\ToolsInterface $tools
 * @var Johncms\Api\UserInterface $user
 */

if ($user->isValid()) {
    $topic = $db->query("SELECT COUNT(*) FROM `forum_topic` WHERE `id` = '${id}' AND (`deleted` != '1' OR `deleted` IS NULL)")->fetchColumn();
    $vote = abs((int) ($_POST['vote']));
    $topic_vote = $db->query("SELECT COUNT(*) FROM `cms_forum_vote` WHERE `type` = '2' AND `id` = '${vote}' AND `topic` = '${id}'")->fetchColumn();
    $vote_user = $db->query("SELECT COUNT(*) FROM `cms_forum_vote_users` WHERE `user` = '" . $user->id . "' AND `topic` = '${id}'")->fetchColumn();

    if ($topic_vote == 0 || $vote_user > 0 || $topic == 0) {
        http_response_code(404);
        echo $view->render(
            'system::pages/result',
            [
                'title'         => _t('Forum'),
                'page_title'    => _t('Forum'),
                'type'          => 'alert-danger',
                'message'       => _t('Wrong data'),
                'back_url'      => '/forum/',
                'back_url_name' => _t('Back'),
            ]
        );
        exit;
    }

    $db->exec("INSERT INTO `cms_forum_vote_users` SET `topic` = '${id}', `user` = '" . $user->id . "', `vote` = '${vote}'");
    $db->exec("UPDATE `cms_forum_vote` SET `count` = count + 1 WHERE id = '${vote}'");
    $db->exec("UPDATE `cms_forum_vote` SET `count` = count + 1 WHERE topic = '${id}' AND `type` = '1'");

    echo $view->render(
        'system::pages/result',
        [
            'title'         => _t('Forum'),
            'page_title'    => _t('Forum'),
            'type'          => 'alert-success',
            'message'       => _t('Vote accepted'),
            'back_url'      => htmlspecialchars(getenv('HTTP_REFERER')),
            'back_url_name' => _t('Back'),
        ]
    );
} else {
    http_response_code(403);
    echo $view->render(
        'system::pages/result',
        [
            'title'         => _t('Access forbidden'),
            'type'          => 'alert-danger',
            'message'       => _t('Access forbidden'),
            'back_url'      => '/forum/',
            'back_url_name' => _t('Back'),
        ]
    );
}
