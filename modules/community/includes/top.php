<?php

/**
 * This file is part of JohnCMS Content Management System.
 *
 * @copyright JohnCMS Community
 * @license   https://opensource.org/licenses/GPL-3.0 GPL-3.0
 * @link      https://johncms.com JohnCMS Project
 */

declare(strict_types=1);

use Johncms\UserProperties;

defined('_IN_JOHNCMS') || die('Error: restricted access');

$url = '/community/top/';

$tabs = [
    'forum' => [
        'name'   => __('Forum'),
        'url'    => $url,
        'active' => false,
    ],
    'guest' => [
        'name'   => __('Guestbook'),
        'url'    => $url . 'guest/',
        'active' => false,
    ],
    'comm'  => [
        'name'   => __('Comments'),
        'url'    => $url . 'comm/',
        'active' => false,
    ],
];

if ($config['karma']) {
    $tabs['karma'] = [
        'name'   => __('Karma'),
        'url'    => $url . 'karma/',
        'active' => false,
    ];
}


switch ($mod) {
    case 'guest':
        // Топ Гостевой
        $req = $db->query('SELECT * FROM `users` WHERE `postguest` > 0 ORDER BY `postguest` DESC LIMIT 9');
        $title = __('Most active in Guestbook');
        $active = 'guest';
        break;

    case 'comm':
        // Топ комментариев
        $req = $db->query('SELECT * FROM `users` WHERE `komm` > 0 ORDER BY `komm` DESC LIMIT 9');
        $title = __('Most commentators');
        $active = 'comm';
        break;

    case 'karma':
        // Топ Кармы
        if ($config['karma']) {
            $req = $db->query('SELECT *, (`karma_plus` - `karma_minus`) AS `karma` FROM `users` WHERE (`karma_plus` - `karma_minus`) > 0 ORDER BY `karma` DESC LIMIT 9');
            $title = __('Best Karma');
            $active = 'karma';
        }
        break;

    default:
        // Топ Форума
        $req = $db->query('SELECT * FROM `users` WHERE `postforum` > 0 ORDER BY `postforum` DESC LIMIT 9');
        $title = __('Most active in Forum');
        $active = 'forum';
}

$tabs[$active]['active'] = true;

$nav_chain->add($title);

$data = [
    'total'      => $req->rowCount(),
    'active_tab' => $active,
    'tabs'       => $tabs,
    'list'       => static function () use ($req, $user) {
        while ($res = $req->fetch()) {
            $res['user_id'] = $res['id'];
            $user_properties = new UserProperties();
            $user_data = $user_properties->getFromArray($res);
            $res = array_merge($res, $user_data);
            yield $res;
        }
    },
];

echo $view->render(
    'users::top',
    [
        'title'      => $title,
        'page_title' => $title,
        'data'       => $data,
    ]
);
