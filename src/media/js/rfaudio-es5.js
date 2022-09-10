(function () {
  'use strict';

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

        i--;
      }

      elStatus.innerHTML = tmpTitle;
    }
  }

  var allAudioDivs = document.querySelectorAll('div.rfaudioplayer');
  allAudioDivs.forEach(function (audioDiv) {
    var myAudio = audioDiv.getElementsByTagName('audio')[0];
    var myStatus = audioDiv.querySelector('.rfaudiostatus');
    var myPlaylistItems = audioDiv.getElementsByTagName('li');

    var textSeeking = Joomla.Text._('MOD_RFAUDIO_SEEKING');

    var myPlaylist = [];

    var _loop = function _loop(i) {
      var myPlaylistItem = myPlaylistItems[i].getElementsByTagName('a')[0];
      var item = {
        'start': parseFloat(myPlaylistItem.getAttribute('data-start')),
        'title': myPlaylistItem.innerHTML
      };
      myPlaylist[i] = item;
      myPlaylistItem.addEventListener('click', function () {
        seek(myAudio, item.start);
      });
    };

    for (var i = 0; i < myPlaylistItems.length; ++i) {
      _loop(i);
    }

    myPlaylist[myPlaylistItems.length] = {
      'start': myAudio.duration,
      'title': ''
    };
    myAudio.addEventListener('durationchange', function () {
      myPlaylist[myPlaylistItems.length].start = myAudio.duration;
    });
    myAudio.addEventListener('timeupdate', function () {
      updchapter(myAudio, myStatus, myPlaylist, textSeeking);
    });
  });

})();
