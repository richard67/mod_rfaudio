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
    el.currentTime = pos;
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
  function setstatus(el, txt) {
    el.innerHTML = txt;
  }
  function clearstatus(el, txt) {
    if (el.innerHTML === txt) el.innerHTML = '';
  }
  var allAudioPlayerDivs = document.querySelectorAll('div.rfaudioplayer');
  allAudioPlayerDivs.forEach(function (audioPlayerDiv) {
    var myAudio = audioPlayerDiv.getElementsByTagName('audio')[0];
    var myStatus = audioPlayerDiv.querySelector('.rfaudiostatus');
    var showStatus = myStatus ? !!myStatus.getAttribute('data-show-status') : false;
    var showTitle = myStatus ? !!myStatus.getAttribute('data-show-title') : false;
    var myPlaylistItems = audioPlayerDiv.getElementsByTagName('li');
    var textSeeking = '';
    if (showStatus) {
      var textLoading = Joomla.Text._('MOD_RFAUDIO_LOADING').replace('&hellip;', "\u2026");
      textSeeking = Joomla.Text._('MOD_RFAUDIO_SEEKING').replace('&hellip;', "\u2026");
      myAudio.addEventListener('loadstart', function () {
        if (myAudio.networkState === 2) {
          setstatus(myStatus, textLoading);
        }
      });
      myAudio.addEventListener('waiting', function () {
        if (myAudio.networkState === 2) {
          setstatus(myStatus, textLoading);
        }
      });
      myAudio.addEventListener('canplay', function () {
        clearstatus(myStatus, textLoading);
      });
      myAudio.addEventListener('playing', function () {
        clearstatus(myStatus, textLoading);
      });
      myAudio.addEventListener('seeking', function () {
        setstatus(myStatus, textSeeking);
      });
      myAudio.addEventListener('seeked', function () {
        clearstatus(myStatus, textSeeking);
      });
    }
    if (showTitle) {
      var myPlaylist = [];
      var _loop = function _loop() {
        var myPlaylistItem = myPlaylistItems[i].getElementsByTagName('button')[0];
        var item = {
          start: parseFloat(myPlaylistItem.getAttribute('data-start')).toFixed(1),
          title: myPlaylistItem.innerHTML
        };
        myPlaylist[i] = item;
        myPlaylistItem.addEventListener('click', function () {
          seek(myAudio, item.start);
        });
      };
      for (var i = 0; i < myPlaylistItems.length; i += 1) {
        _loop();
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
    } else {
      var _loop2 = function _loop2() {
        var myPlaylistItem = myPlaylistItems[_i].getElementsByTagName('button')[0];
        myPlaylistItem.addEventListener('click', function () {
          seek(myAudio, parseFloat(myPlaylistItem.getAttribute('data-start')).toFixed(1));
        });
      };
      for (var _i = 0; _i < myPlaylistItems.length; _i += 1) {
        _loop2();
      }
    }
  });

})();
