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
$playlistMinHeight = $params->get('playlist_min_height', 120);
$playlistMinHeight = $playlistMinHeight > $playerHeight ? $playerHeight : $playlistMinHeight;
$playerMaxWidth    = in_array($playlistPosition, ['side1', 'side2']) ? $imageWidth + $playlistMinWidth : $imageWidth;
$playlistWrapStyle = in_array($playlistPosition, ['side1', 'side2'])
    ? '-webkit-box-flex: 1; -ms-flex: 1 1 ' . $playlistMinWidth . 'px; flex: 1 1 ' . $playlistMinWidth . 'px; max-width: ' .  $imageWidth . 'px;'
    : '-webkit-box-flex: 0; -ms-flex: 0 1 ' . $imageWidth . 'px; flex: 0 1 ' . $imageWidth . 'px;';
$downloadLink      = $params->get('download_link', '');
$showPlaylistItem  = $params->get('show_playlist_item', 0);
$showItemDuration  = $params->get('show_item_duration', 0);
$sources           = strpos($audioAttribs, ' src="') === false ? $params->get('sources') : [];

$wa->addInlineStyle(
    "div.rfaudioplayer-{$module->id} { max-width: {$playerMaxWidth}px; }
div.rfaudio-{$module->id} { width: {$imageWidth}px; }
div.rfaudioctl-{$module->id} { height: {$controlsHeight}px; }
div.rfaudioctl-{$module->id} audio { max-height: {$controlsHeight}px; }
div.rfaudioplaylistwrapper-{$module->id} { {$playlistWrapStyle} }
div.rfaudioplaylist-{$module->id} { -webkit-box-flex: 1; -ms-flex: 1 1 {$playlistMinHeight}px; flex: 1 1 {$playlistMinHeight}px; max-height: {$playerHeight}px; }"
);

// Load JS language strings
Text::script('MOD_RFAUDIO_LOADING');
Text::script('MOD_RFAUDIO_SEEKING');

?>
<div class="rfaudioplayer rfaudioplayer-<?php echo $module->id; ?>">
    <div class="rfaudio rfaudio-<?php echo $module->id; ?> rfaudioplaylist-<?php echo $playlistPosition; ?>">
        <div class="rfaudioimg">
            <?php if ($showStatus || $showPlaylistItem) : ?>
            <div class="rfaudiostatus"<?php echo $showStatus ? ' data-show-status="true"' : ''; ?><?php echo $showPlaylistItem ? ' data-show-title="true"' : ''; ?>> </div>
            <?php endif; ?>
            <?php echo LayoutHelper::render('joomla.html.image', ['src' => $image, 'title' => $title, 'alt' => $title, 'itemprop' => 'image',]); ?>
        </div>
        <div class="rfaudioctl rfaudioctl-<?php echo $module->id; ?>">
            <audio title="<?php echo $title; ?>"<?php echo $audioAttribs; ?>>
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
