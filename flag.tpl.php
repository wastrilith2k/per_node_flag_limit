<?php

/**
 * @file
 * Default theme implementation to display a flag link, and a message after the action
 * is carried out.
 *
 * Available variables:
 *
 * - $flag: The flag object itself. You will only need to use it when the
 *   following variables don't suffice.
 * - $flag_name_css: The flag name, with all "_" replaced with "-". For use in 'class'
 *   attributes.
 * - $flag_classes: A space-separated list of CSS classes that should be applied to the link.
 *
 * - $action: The action the link is about to carry out, either "flag" or "unflag".
 * - $last_action: The action, as a passive English verb, either "flagged" or
 *   "unflagged", that led to the current status of the flag.
 *
 * - $link_href: The URL for the flag link.
 * - $link_text: The text to show for the link.
 * - $link_title: The title attribute for the link.
 *
 * - $message_text: The long message to show after a flag action has been carried out.
 * - $after_flagging: This template is called for the link both before and after being
 *   flagged. If displaying to the user immediately after flagging, this value
 *   will be boolean TRUE. This is usually used in conjunction with immedate
 *   JavaScript-based toggling of flags.
 * - $setup: TRUE when this template is parsed for the first time; Use this
 *   flag to carry out procedures that are needed only once; e.g., linking to CSS
 *   and JS files.
 *
 * NOTE: This template spaces out the <span> tags for clarity only. When doing some
 * advanced theming you may have to remove all the whitespace.
 */

if ($setup) {
  drupal_add_css(drupal_get_path('module', 'flag') .'/theme/flag.css');
  drupal_add_js(drupal_get_path('module', 'flag') .'/theme/flag.js');
}
// Set defaults:
$show_link = TRUE;

if (arg(0) == 'node' && is_numeric(arg(1))):
  $nid = arg(1);
  $node = node_load(array('nid' => $nid));
  $type = $node->type;
  $limited = variable_get('per_node_flag_limit_' . $flag->name, FALSE);  
  
  if ($limited):

	// Get the flag limit.
    $limit = variable_get('per_node_flag_limit_'. $flag->name .  '_' . $type .  '_value', NULL);

	if ($flag->get_count($nid) >= $limit):
	
	  // Get the current user
	  global $user;
	  $flagging_user = FALSE;
	  
	  // Get users who flagged this content
	  $accounts = module_invoke('flag', 'get_content_flags', 'node', $nid, $flag->name);
      if (isset($accounts)) {
        foreach ($accounts as $uid => $data) {
		  if ($uid == $user->uid) $flagging_user = TRUE;
        }
      }
	
	  // Clear the flag link
	  if (!$flagging_user) $show_link = FALSE;
	endif;
  endif;	
endif;
?>
<span class="flag-wrapper flag-<?php echo $flag_name_css; ?>">
<?php
if ($show_link): 
?>
  <a href="<?php echo $link_href; ?>" title="<?php echo $link_title; ?>" class="<?php print $flag_classes ?>" rel="nofollow"><?php echo $link_text; ?></a><span class="flag-throbber">&nbsp;</span>
<?php
else:
?>
  <span class="<?php print $flag_classes ?>"><?php echo variable_get('per_node_flag_limit_' . $flag->name .  '_msg', ""); ?></span>
<?
endif;
?>  
  <?php if ($after_flagging): ?>
    <span class="flag-message flag-<?php echo $last_action; ?>-message">
      <?php echo $message_text; ?>
    </span>
  <?php endif; ?>
</span>