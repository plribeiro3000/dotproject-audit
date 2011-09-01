<?php
if (!defined('DP_BASE_DIR')) {
    die('You should not access this file directly.');
}

GLOBAL $AppUI, $project_id, $task_id, $deny, $canRead, $canEdit, $dPconfig, $cfObj, $m, $obj;
require_once($AppUI->getModuleClass('audit'));

global $allowed_folders_ary, $denied_folders_ary, $limited;

$cfObj = new CFileFolder();
$allowed_folders_ary = $cfObj->getAllowedRecords($AppUI->user_id);
$denied_folders_ary = $cfObj->getDeniedRecords($AppUI->user_id);

$limited = ((count($allowed_folders_ary) < $cfObj->countFolders()) ? true : false);

if (!$limited) {
    $canEdit = true;
} else if ($limited && array_key_exists($folder, $allowed_folders_ary)) {
    $canEdit = true;
} else {
    $canEdit = false;
}

$showProject = false;

if (getPermission('audit', 'edit')) {
    $q = new DBQuery();
    $q->addQuery('*');
    $q->addTable('auditors');
    $q->addJoin('projects', 'projects', 'projects.project_id = '.dPgetConfig('dbprefix', '').'auditors.project_id');
    $q->addJoin('users', 'users', 'users.user_id = '.dPgetConfig('dbprefix', '').'auditors.auditor_id');
    $q->addWhere('projects.project_id = '.$project_id);
    $q->setLimit(10);
    $list = $q->loadList(); ?>

    <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <? foreach($list as $item){?>
                <tr>
                    <td><?= $item['user_username'] ?></td>
                    <td><a href="?m=audit&amp;a=remove_audictor&amp;audictor_id=<?= $item['auditor_id'] ?>"><?= $AppUI->_('Delete')?></a></td>
                </tr>
                <? } ?>
            </tbody>
    </table>
<?
}
?>