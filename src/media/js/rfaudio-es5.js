(function () {
  'use strict';

  /**
   * @copyright  (C) 2022 Richard Fath <https://www.richard-fath.de>
   * @license    GNU General Public License version 2 or later; see LICENSE.txt
   */
  if (!Joomla) {
    throw new Error('Joomla API was not properly initialized');
  }

  function seek(el, pos) {
    el.currentTime = pos.toFixed(1);
    el.play();
  }

  function updchapter(elAudio, elStatus, playlist, txtSeeking) {
    if (elAudio.seeking) {
      elStatus.innerHTML = txtSeeking;
    } else if (elAudio.currentTime <= 0) {
      elStatus.innerHTML = '';
    } else {
      var tmpTitle = '';
      var i = playlist.length - 1;

      while (i >= 0) {
        if (elAudio.currentTime >= playlist[i].start) {
          tmpTitle = playlist[i].title;
          break;
        }

        i -= 1;
      }

      elStatus.innerHTML = tmpTitle;
    }
  }

  var allAudioPlayerDivs = document.querySelectorAll('div.rfaudioplayer');
  allAudioPlayerDivs.forEach(function (audioPlayerDiv) {
    var myAudio = audioPlayerDiv.getElementsByTagName('audio')[0];
    var myStatus = audioPlayerDiv.querySelector('.rfaudiostatus');
    var myPlaylistItems = audioPlayerDiv.getElementsByTagName('li');

    var textSeeking = Joomla.Text._('MOD_RFAUDIO_SEEKING');

    var myPlaylist = [];

    var _loop = function _loop(i) {
      var myPlaylistItem = myPlaylistItems[i].getElementsByTagName('a')[0];
      var item = {
        start: parseFloat(myPlaylistItem.getAttribute('data-start')),
        title: myPlaylistItem.innerHTML
      };
      myPlaylist[i] = item;
      myPlaylistItem.addEventListener('click', function () {
        seek(myAudio, item.start);
      });
    };

    for (var i = 0; i < myPlaylistItems.length; i += 1) {
      _loop(i);
    }

    myPlaylist[myPlaylistItems.length] = {
      start: myAudio.duration,
      title: ''
    };
    myAudio.addEventListener('durationchange', function () {
      myPlaylist[myPlaylistItems.length].start = myAudio.duration;
    });
    myAudio.addEventListener('timeupdate', function () {
      updchapter(myAudio, myStatus, myPlaylist, textSeeking);
    });
  });

})();
