<?php

/**
 * @package     mod_rfaudio
 *
 * @copyright   (C) 2022 Richard Fath <https://www.richard-fath.de>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Module\RfAudio\Site\Helper;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Registry\Registry;

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
}
