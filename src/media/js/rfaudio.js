/**
 * @copyright  (C) 2022 Richard Fath <https://www.richard-fath.de>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
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
    let tmpTitle = '';
    let i = playlist.length - 1;

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

const allAudioDivs = document.querySelectorAll('div.rfaudioplayer');
allAudioDivs.forEach(audioDiv => {
  const myAudio = audioDiv.getElementsByTagName('audio')[0];
  const myStatus = audioDiv.querySelector('.rfaudiostatus');
  const myPlaylistItems = audioDiv.getElementsByTagName('li');

  const textSeeking = Joomla.Text._('MOD_RFAUDIO_SEEKING');

  let myPlaylist = [];

  for (let i = 0; i < myPlaylistItems.length; ++i) {
    const myPlaylistItem = myPlaylistItems[i].getElementsByTagName('a')[0];
    const item = {
      'start': parseFloat(myPlaylistItem.getAttribute('data-start')),
      'title': myPlaylistItem.innerHTML
    };
    myPlaylist[i] = item;
    myPlaylistItem.addEventListener('click', function () {
      seek(myAudio, item.start);
    });
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
