<?php

/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondage: Framasoft (https://github.com/framasoft)
 *
 * =============================
 *
 * Ce logiciel est régi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Raphaël DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft)
 */

use Framadate\Services\LogService;
use Framadate\Services\PollService;
use Framadate\Services\SuperAdminService;
use Framadate\Utils;

include_once __DIR__ . '/app/inc/init.php';

const POLLS_PER_PAGE = 30;

if (!is_file(CONF_FILENAME)) {
    header(('Location: ' . Utils::get_server_name() . 'admin/check.php'));
    exit;
}

/* GET */
/*-----*/
$page = (int)filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
$page = ($page >= 1) ? $page : 1;

// Search
$search['poll'] = filter_input(INPUT_GET, 'poll', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
$search['title'] = filter_input(INPUT_GET, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$search['name'] = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$search['mail'] = filter_input(INPUT_GET, 'mail', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

/* SERVICES */
/* -------- */
$pollService = new PollService(new LogService());
$superAdminService = new SuperAdminService();

/* PAGE */
/* ---- */

$demoPoll = $pollService->findById('aqg259dth55iuhwm');
$nbcol = max($config['show_what_is_that'] + $config['show_the_software'] + $config['show_cultivate_your_garden'], 1);

$found = $superAdminService->findAllPolls($search, $page - 1, POLLS_PER_PAGE);
$polls = $found['polls'];
$count = $found['count'];
$total = $found['total'];

$smarty->assign('polls', $polls);
$smarty->assign('count', $count);
$smarty->assign('total', $total);
$smarty->assign('page', $page);
$smarty->assign('pages', ceil($count / POLLS_PER_PAGE));
$smarty->assign('show_what_is_that', $config['show_what_is_that']);
$smarty->assign('show_the_software', $config['show_the_software']);
$smarty->assign('show_cultivate_your_garden', $config['show_cultivate_your_garden']);
$smarty->assign('col_size', 12 / $nbcol);
$smarty->assign('demo_poll', $demoPoll);

$smarty->assign('title', __('Generic', 'Make your polls'));

$smarty->display('index.tpl');
