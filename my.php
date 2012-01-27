<?php

// Load Elgg framework
require_once(dirname(dirname(dirname(__FILE__))) . '/engine/start.php');

// Ensure only logged-in users can see this page
gatekeeper();

$user = elgg_get_logged_in_user_entity();
elgg_set_page_owner_guid($user->get('guid'));

// Set the context to settings
elgg_set_context('settings');

$title = elgg_echo('ufcoe_roles:settings:my');

elgg_push_breadcrumb(elgg_echo('settings'), "settings/user/$user->username");
elgg_push_breadcrumb($title);

// display roles
$roles = ufcoe_roles_get_roles_array();

unset($roles[UFCOE_RoleReader::KEY_ALL]);

$body = '';

foreach ($roles as $system => $system_roles) {
    if (! empty($system_roles)) {
        $body .= "<h4>" . htmlspecialchars(elgg_echo('ufcoe_roles:key:' . $system)) . "</h4>";
        $body .= "<p>" . htmlspecialchars(implode(', ', $system_roles)) . "</p>";
    }
}

if ($body === '') {
    $body .= elgg_echo('ufcoe_roles:settings:no_roles');
}

$body = "<br>$body";

$params = array(
	'content' => $body,
	'title' => $title,
);
$body = elgg_view_layout('one_sidebar', $params);

echo elgg_view_page($title, $body);
