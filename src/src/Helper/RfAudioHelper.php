<?php

/**
 * @package     mod_rfaudio
 *
 * @copyright   (C) 2022 Richard Fath <https://www.richard-fath.de>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Module\RfAudio\Site\Helper;

use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Helper for mod_rfaudio
 *
 * @since  1.0.0
 */
class RfAudioHelper
{
    /**
     * Build the audio attributes string
     *
     * @param   Registry  $params  The module parameters.
     *
     * @return  string
     *
     * @since   1.0.0
     */
    public function getAudioAttributes(Registry $params)
    {
        $sources = $params->get('sources');

        if (empty($sources)) {
            return '';
        }

        $audioAttribs = ' preload="' . $params->get('preload') . '"';

        foreach (['autoplay', 'controls', 'loop', 'muted'] as $attribute) {
            $audioAttribs .= $params->get($attribute) ? ' ' . $attribute . '="' . $attribute . '"' : '';
        }

        // Use src attribute if there is only one source file
        if (!isset($sources->sources1)) {
            $audioAttribs .= ' src="' . HTMLHelper::_('cleanImageURL', $sources->sources0->file)->url . '"';
        }

        return $audioAttribs;
    }

    /**
     * Get playlist from module parameters
     *
     * @param   Registry  $params  The module parameters.
     *
     * @return  array
     *
     * @since   2.0.0
     */
    private function getPlaylistFromParams(Registry $params)
    {
        return ArrayHelper::fromObject($params->get('playlist'), false);
    }

    /**
     * Get playlist from WEBVTT file
     *
     * @param   Registry  $params  The module parameters.
     *
     * @return  array
     *
     * @since   2.0.0
     */
    private function getPlaylistFromFileWebvtt(Registry $params)
    {
        $playlist = [];

        $file = $params->get('playlist_file', '');

        if (empty($file)) {
            return $playlist;
        }

        $file = Path::clean(JPATH_ROOT . '/media/mod_rfaudio/vtt/' . $file);

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $item = null;

        foreach ($lines as $line) {
            if (preg_match('/^([^->\s]+)\s+-->\s+([^->\s]+)\s*$/', $line, $matches)) {
                $base = floatval(\DateTime::createFromFormat('Y-m-d H:i:s.u', '2000-01-01 00:00:00.000')->format('U.u'));
                $start = floatval(\DateTime::createFromFormat('Y-m-d H:i:s.u', '2000-01-01 ' . $matches[1])->format('U.u'));
                $end = floatval(\DateTime::createFromFormat('Y-m-d H:i:s.u', '2000-01-01 ' . $matches[2])->format('U.u'));
                $item = new \stdClass();
                $item->position = round($start - $base, 3);
                $item->duration = round($end - $start, 3);
                $item->title    = '';
            } elseIf (!empty($item) && !strpos($line, 'WEBVTT')) {
                $item->title = $line;
                $playlist[] = $item;
                $item = null;
            }
        }

        return $playlist;
    }

    /**
     * Pre-process the playlist
     *
     * @param   Registry  $params  The module parameters.
     *
     * @return  array
     *
     * @since   2.0.0
     */
    public function getPlaylist(Registry $params)
    {
        if ($params->get('playlist_source', '0') === '1') {
            return $this->getPlaylistFromFileWebvtt($params);
        }

        return $this->getPlaylistFromParams($params);
    }
}
