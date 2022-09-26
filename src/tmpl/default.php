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

$stylesheet = $params->get('stylesheet', 'rfaudio.css');

if ($stylesheet !== '-1') {
    $wa->registerAndUseStyle('mod_rfaudio', 'mod_rfaudio/' . $stylesheet);
}

$title             = Text::_($module->title);
$controlsHeight    = $params->get('controls_height', 45);
$showStatus        = $params->get('show_status', 0);
$playlistMinHeight = $params->get('playlist_min_height', 120);
$playlistMinWidth  = $params->get('playlist_min_width', 320);
$image             = $params->get('image');
$imageAttribs      = HTMLHelper::_('cleanImageURL', $image)->attributes;
$imageHeight       = $imageAttribs['height'];
$imageWidth        = $imageAttribs['width'];
$playerHeight      = $imageHeight + $controlsHeight;
$downloadLink      = $params->get('download_link', '');
$playlist          = $params->get('playlist');
$showPlaylistItem  = $params->get('show_playlist_item', 0);
$sources           = strpos($audioAttribs, ' src="') === false ? $params->get('sources') : [];

// Load JS language strings
Text::script('MOD_RFAUDIO_LOADING');
Text::script('MOD_RFAUDIO_SEEKING');

?>
<div class="rfaudioplayer" style="max-width: <?php echo ($imageWidth + $playlistMinWidth); ?>px; max-height: <?php echo ($playerHeight + $playlistMinHeight); ?>px;">
    <div class="rfaudio">
        <div class="rfaudioimg" style="max-width: <?php echo $imageWidth; ?>px; max-height: <?php echo $imageHeight; ?>px;">
            <?php if ($showStatus || $showPlaylistItem) : ?>
            <div class="rfaudiostatus"<?php echo $showStatus ? ' data-show-status="true"' : ''; ?><?php echo $showPlaylistItem ? ' data-show-title="true"' : ''; ?>> </div>
            <?php endif; ?>
            <?php echo LayoutHelper::render('joomla.html.image', ['src' => $image, 'title' => $title, 'alt' => $title, 'itemprop' => 'image',]); ?>
        </div>
        <div class="rfaudioctl" style="width: 100%; max-height: <?php echo $controlsHeight; ?>px;">
            <audio title="<?php echo $title; ?>"<?php echo $audioAttribs; ?> style="width: 100%; max-height: <?php echo $controlsHeight; ?>px;">
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
    <div class="rfaudioplaylistwrapper" style="flex: 1 1 <?php echo $playlistMinWidth; ?>px; min-width: <?php echo floor($playlistMinWidth / ($imageWidth + $playlistMinWidth) * 100.0); ?>%; max-width: <?php echo $imageWidth; ?>px;">
        <div class="rfaudioplaylist" style="flex: 1 1 <?php echo $playlistMinHeight; ?>px; max-height: <?php echo $playerHeight; ?>px;">
            <?php if ($playlist->playlist0->position > 0) : ?>
            <ol class="rfaudioplaylist-list" start="0">
                <li class="rfaudioplaylist-item rfaudioplaylist-start"><a data-start="0"><?php echo Text::_('MOD_RFAUDIO_PLAYLIST_START'); ?></a></li>
            <?php else : ?>
            <ol class="rfaudioplaylist-list">
            <?php endif; ?>
                <?php foreach ($playlist as $item) : ?>
                <li class="rfaudioplaylist-item"><a data-start="<?php echo $item->position; ?>"><?php echo $item->title; ?></a></li>
                <?php endforeach; ?>
            </ol>
        </div>
    </div>
    <?php endif; ?>
</div>
