<?php

/**
 * @package     mod_rfaudio
 *
 * @copyright   (C) 2022 Richard Fath <https://www.richard-fath.de>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

if (empty($audioAttribs)) {
    return;
}

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $app->getDocument()->getWebAssetManager();
$wa->registerAndUseScript('mod_rfaudio.es5', 'mod_rfaudio/rfaudio-es5.min.js', [], ['nomodule' => true, 'defer' => true], ['core']);
$wa->registerAndUseScript('mod_rfaudio', 'mod_rfaudio/rfaudio.min.js', [], ['type' => 'module'], ['mod_rfaudio.es5', 'core']);

$stylesheet = $params->get('stylesheet', '-1');

if ($stylesheet !== '-1') {
    $wa->registerAndUseStyle('mod_rfaudio', 'mod_rfaudio/' . $stylesheet);
}

$title        = Text::_($module->title);
$downloadLink = $params->get('download_link', '');
$playlist     = $params->get('playlist');
$sources      = strpos($audioAttribs, ' src="') === false ? $params->get('sources') : [];

// Load JS language strings
Text::script('MOD_RFAUDIO_SEEKING');

?>
<div class="rfaudioplayer">
<div class="rfaudio">
<div class="rfaudioimg">
<div class="rfaudiostatus"> </div>
<?php echo LayoutHelper::render('joomla.html.image', ['src' => $params->get('image'), 'title' => $title, 'alt' => $title, 'itemprop' => 'image',]); ?>
</div>
<div class="rfaudioctl">
<audio title="<?php echo $title; ?>"<?php echo $audioAttribs; ?>>
<?php foreach ($sources as $source) : ?>
<source src="<?php echo HTMLHelper::_('cleanImageURL', $source->file)->url; ?>" type="<?php echo $source->type; ?>" />
<?php endforeach; ?>
<?php echo Text::_('MOD_RFAUDIO_NO_BROWSER_SUPPORT'); ?>
<?php if ($downloadLink) : ?>
<?php echo ' ' . Text::sprintf('MOD_RFAUDIO_USE_DOWNLOAD', $downloadLink); ?>
<?php endif; ?>
</audio>
</div>
</div>
<?php if (!empty($playlist)) : ?>
<div class="rfaudioplaylist">
<ul>
<?php if ($playlist->playlist0->position > 0) : ?>
<li><a data-start="0"><?php echo Text::_('MOD_RFAUDIO_PLAYLIST_START'); ?></a></li>
<?php endif; ?>
<?php $count = 0; ?>
<?php foreach ($playlist as $item) : ?>
<li><a data-start="<?php echo $item->position; ?>"><?php echo ++$count; ?>. <?php echo $item->title; ?></a></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>
</div>
