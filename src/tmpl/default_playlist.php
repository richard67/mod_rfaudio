<?php

/**
 * @package     mod_rfaudio
 *
 * @copyright   (C) 2022 Richard Fath <https://www.richard-fath.de>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>
<div class="rfaudioplaylistwrapper rfaudioplaylistwrapper-<?php echo $module->id; ?> rfaudioplaylist-<?php echo $playlistPosition; ?>">
    <div class="rfaudioplaylisttop"> </div>
    <div class="rfaudioplaylist rfaudioplaylist-<?php echo $module->id; ?>">
        <?php if (array_values($playlist)[0]->position > 0) : ?>
        <ol class="rfaudioplaylist-list" start="0">
            <li class="rfaudioplaylist-item rfaudioplaylist-start"><button data-start="0"><?php echo Text::_('MOD_RFAUDIO_PLAYLIST_START'); ?></button></li>
        <?php else : ?>
        <ol class="rfaudioplaylist-list">
        <?php endif; ?>
            <?php foreach ($playlist as $item) : ?>
            <li class="rfaudioplaylist-item">
                <button data-start="<?php echo $item->position; ?>"><?php echo $item->title; ?>
                <?php if ($showItemDuration && !empty($item->duration)) : ?>
                <span class="rfaudio-duration">
                    <?php echo preg_replace('/^0[0]+' . Text::_('MOD_RFAUDIO_PLAYLIST_TIME_SEPARATOR') . '/', '', \DateTime::createFromFormat('U', round($item->duration))->format(Text::_('MOD_RFAUDIO_PLAYLIST_TIME_FORMAT'))); ?>
                </span>
                <?php endif; ?>
                </button>
            </li>
            <?php endforeach; ?>
        </ol>
    </div>
</div>
