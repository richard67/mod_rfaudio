<?php

/**
 * @package     mod_rfaudio
 *
 * @copyright   (C) 2022 Richard Fath <https://www.richard-fath.de>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
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

$stylesheet = $params->get('stylesheet', 'rfaudio.css');

if ($stylesheet !== '-1') {
    $wa->registerAndUseStyle('mod_rfaudio', 'mod_rfaudio/' . $stylesheet);
}

$title             = Text::_($module->title);
$controlsHeight    = $params->get('controls_height', 45);
$showStatus        = $params->get('show_status', 0);
$image             = $params->get('image');
$imageAttribs      = HTMLHelper::_('cleanImageURL', $image)->attributes;
$imageHeight       = $imageAttribs['height'];
$imageWidth        = $imageAttribs['width'];
$playlistPosition  = $params->get('playlist_position', 'side2');
$playlistMinWidth  = empty($playlist) ? 0 : $params->get('playlist_min_width', 320);
$playlistMinWidth  = $playlistMinWidth > $imageWidth ? $imageWidth : $playlistMinWidth;
$playerHeight      = $imageHeight + $controlsHeight;
$downloadLink      = $params->get('download_link', '');
$showPlaylistItem  = $params->get('show_playlist_item', 0);
$showItemDuration  = $params->get('show_item_duration', 0);
$sources           = strpos($audioAttribs, ' src="') === false ? $params->get('sources') : [];

// Load JS language strings
Text::script('MOD_RFAUDIO_LOADING');
Text::script('MOD_RFAUDIO_SEEKING');

?>
<div class="rfaudioplayer" style="max-width: <?php echo (in_array($playlistPosition, ['side1', 'side2']) ? $imageWidth + $playlistMinWidth : $imageWidth); ?>px;">
    <div class="rfaudio <?php echo 'rfaudioplaylist-' . $playlistPosition; ?>" style="width: <?php echo $imageWidth; ?>px;">
        <div class="rfaudioimg">
            <?php if ($showStatus || $showPlaylistItem) : ?>
            <div class="rfaudiostatus"<?php echo $showStatus ? ' data-show-status="true"' : ''; ?><?php echo $showPlaylistItem ? ' data-show-title="true"' : ''; ?>> </div>
            <?php endif; ?>
            <?php echo LayoutHelper::render('joomla.html.image', ['src' => $image, 'title' => $title, 'alt' => $title, 'itemprop' => 'image',]); ?>
        </div>
        <div class="rfaudioctl" style="height: <?php echo $controlsHeight; ?>px;">
            <audio title="<?php echo $title; ?>"<?php echo $audioAttribs; ?> style="max-height: <?php echo $controlsHeight; ?>px;">
                <?php foreach ($sources as $source) : ?>
                <source src="<?php echo HTMLHelper::_('cleanImageURL', $source->file)->url; ?>" type="<?php echo $source->type; ?>" />
                <?php endforeach; ?>
                <?php echo Text::_('MOD_RFAUDIO_NO_BROWSER_SUPPORT'); ?>
                <?php if ($downloadLink) :
                    echo ' ' . Text::sprintf('MOD_RFAUDIO_USE_DOWNLOAD', $downloadLink);
                endif; ?>
             </audio>
        </div>
    </div>
    <?php if (!empty($playlist)) : ?>
        <?php require ModuleHelper::getLayoutPath('mod_rfaudio', 'default_playlist'); ?>
    <?php endif; ?>
</div>
