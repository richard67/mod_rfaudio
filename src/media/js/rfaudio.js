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
    let tmpTitle = '';
    let i = playlist.length - 1;

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

const allAudioPlayerDivs = document.querySelectorAll('div.rfaudioplayer');
allAudioPlayerDivs.forEach(audioPlayerDiv => {
  const myAudio = audioPlayerDiv.getElementsByTagName('audio')[0];
  const myStatus = audioPlayerDiv.querySelector('.rfaudiostatus');
  const myPlaylistItems = audioPlayerDiv.getElementsByTagName('li');

  const textSeeking = Joomla.Text._('MOD_RFAUDIO_SEEKING');

  const myPlaylist = [];

  for (let i = 0; i < myPlaylistItems.length; i += 1) {
    const myPlaylistItem = myPlaylistItems[i].getElementsByTagName('a')[0];
    const item = {
      start: parseFloat(myPlaylistItem.getAttribute('data-start')),
      title: myPlaylistItem.innerHTML
    };
    myPlaylist[i] = item;
    myPlaylistItem.addEventListener('click', () => {
      seek(myAudio, item.start);
    });
  }

  myPlaylist[myPlaylistItems.length] = {
    start: myAudio.duration,
    title: ''
  };
  myAudio.addEventListener('durationchange', () => {
    myPlaylist[myPlaylistItems.length].start = myAudio.duration;
  });
  myAudio.addEventListener('timeupdate', () => {
    updchapter(myAudio, myStatus, myPlaylist, textSeeking);
  });
});
