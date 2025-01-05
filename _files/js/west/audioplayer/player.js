!function (window, document) {
    'use strict'

    XF.WAudioPlayer = XF.Element.newHandler({
        trackList: null,

        target: null,
        menu: null,
        header: null,
        playPauseBtn: null,
        currentTime: null,
        duration: null,
        progressBar: null,
        volumeControl: null,

        player: null,
        currentTrack: null,
        mouseOverProgress: false,

        loop: 'playlist',
        volumeLevel: null,

        init: function ()
        {
            this.target = this.$target[0]
            this.menu = document.querySelector('.menu-trackChooser')
            this.header = this.target.querySelector('.block-minorHeader')
            this.playPauseBtn = this.target.querySelector('.playbackControls-play')
            this.duration = this.target.querySelector('.playbackProgress-duration')
            this.currentTime = this.target.querySelector('.playbackProgress-currentTime')
            this.progressBar = this.target.querySelector('.playbackProgress-bar')

            this.trackList = [...this.target.querySelectorAll('.menu-trackRow')].map(x => {
                return {
                    id: parseInt(x.dataset.trackId),
                    url: x.dataset.trackUrl,
                    title: x.textContent,
                    menuItem: x
                }
            })

            this.initPlayer()

            this.progressBar.addEventListener('click', XF.proxy(this, 'progressClick'))
            this.progressBar.addEventListener('mouseover', _ => this.mouseOverProgress = true)
            this.progressBar.addEventListener('mouseout', _ => this.mouseOverProgress = false)

            this.target.querySelector('.playbackControls-play')
                .addEventListener('click', XF.proxy(this, 'playClick'))

            this.target.querySelector('.playbackControls-prev')
                .addEventListener('click', XF.proxy(this, 'playPrev'))

            this.target.querySelector('.playbackControls-next')
                .addEventListener('click', XF.proxy(this, 'playNext'))

            let that = this
            this.target.querySelectorAll('.menu-trackRow').forEach(x => {
                x.addEventListener('click', XF.proxy(that, 'trackClick'))
            })

            this.volumeControl = this.target.querySelector('.playbackControls-volume')
            this.volumeControl.addEventListener('click', XF.proxy(this, 'volumeClick'))

            this.loopControl = this.target.querySelector('.playbackControls-loop')
            this.loopControl.addEventListener('click', XF.proxy(this, 'loopClick'))

            XF.Element.applyHandler($(this.volumeControl), 'wap-volume-tooltip', {
                playerId: this.$target.xfUniqueId()
            })

            this.loadTrack(this.trackList[0])
            this.setVolume(0.1)
            this.updateLoopIcon(this.loop, this.loop)
        },


        play: function ()
        {
            this.player.play()
        },

        pause: function ()
        {
            this.player.pause()
        },

        initPlayer: function ()
        {
            if (!this.player)
            {
                const player = this.player = new Audio()

                player.addEventListener('loadeddata', XF.proxy(this, 'updateDuration'))
                player.addEventListener('timeupdate', XF.proxy(this, 'updateProgress'))

                player.addEventListener('play', XF.proxy(this, 'onPlayerPlay'))
                player.addEventListener('pause', XF.proxy(this, 'onPlayerPause'))
                player.addEventListener('ended', XF.proxy(this, 'onPlayerEnded'))
            }
        },

        loadTrack: function (newTrack)
        {
            const oldTrack = this.currentTrack
            this.currentTrack = newTrack
            this.player.src = this.currentTrack.url
            this.player.load()

            this.updateTrackTitle()
            this.updateMenu(oldTrack, newTrack)
        },

        playByIndex: function (idx)
        {
            this.loadTrack(this.trackList[idx])
            this.play()
        },

        setVolume: function (volume)
        {
            this.player.volume = volume

            this.updateVolumeIcon()
        },

        formatTime: function (time)
        {
            const minutes= Math.floor(time / 60)
            const seconds = Math.round(time % 60).toString().padStart(2, '0')

            return `${minutes}:${seconds}`
        },

        getVolumeLevel: function ()
        {
            if (this.player.muted)
            {
                return 'muted'
            }

            const volume = this.player.volume
            const thresholdMap = [
                [  0, 'off'],
                [0.3, 'low'],
                [0.6, 'medium'],
                [  1, 'high']
            ]

            for (const [threshold, level] of thresholdMap)
            {
                if (volume <= threshold)
                {
                    return level
                }
            }
        },

        updateProgress: function (e)
        {
            this.currentTime.innerText = this.formatTime(e.target.currentTime)
            if (this.mouseOverProgress)
            {
                return
            }

            this.progressBar.value = e.target.currentTime
        },

        updateDuration: function (e)
        {
            this.duration.innerText = this.formatTime(e.target.duration)
            this.progressBar.max = e.target.duration
        },

        updateTrackTitle: function ()
        {
            this.header.innerText = this.currentTrack.title
        },

        updateVolumeIcon: function ()
        {
            const level= this.getVolumeLevel()
            if (this.volumeLevel !== level)
            {
                this.volumeControl.classList.remove('volumeLevel-' + this.volumeLevel)
                this.volumeControl.classList.add('volumeLevel-' + level)
                this.volumeLevel = level
            }
        },

        updateLoopIcon: function (prevLoop, newLoop)
        {
            this.loopControl.classList.remove('playbackControls-loop--' + prevLoop)
            this.loopControl.classList.add('playbackControls-loop--' + newLoop)
        },

        updateMenu: function (oldTrack, newTrack)
        {
            if (!oldTrack)
            {
                return
            }

            oldTrack.menuItem.classList.remove('is-selected')
            newTrack.menuItem.classList.add('is-selected')
        },

        onPlayerPlay: function ()
        {
            this.playPauseBtn.classList.replace('playbackControls-play', 'playbackControls-pause')
        },

        onPlayerPause: function ()
        {
            this.playPauseBtn.classList.replace('playbackControls-pause', 'playbackControls-play')
        },

        onPlayerEnded: function ()
        {
            if (this.loop === 'track')
            {
                this.player.currentTime = 0
                this.play()
            } else if (this.loop === 'playlist')
            {
                this.playNext()
            }
        },

        volumeClick: function ()
        {
            this.player.muted = !this.player.muted;

            this.updateVolumeIcon()
        },

        loopClick: function ()
        {
            const prevLoopState = this.loop

            this.loop = this.loop === 'off'
                ? 'playlist'
                : (this.loop === 'playlist' ? 'track' : 'off')

            this.updateLoopIcon(prevLoopState, this.loop)
        },

        playClick: function ()
        {
            this.player.paused
                ? this.play()
                : this.pause()
        },

        playPrev: function ()
        {
            const prevIdx = this.trackList.indexOf(this.currentTrack) - 1
            const realIdx = prevIdx < 0
                ? this.trackList.length + prevIdx
                : prevIdx

            this.playByIndex(realIdx)
        },

        playNext: function ()
        {
            const nextIdx = this.trackList.indexOf(this.currentTrack) + 1
            const realIdx = nextIdx > this.trackList.length - 1
                ? nextIdx - this.trackList.length
                : nextIdx

            this.playByIndex(realIdx)
        },

        trackClick: function (e)
        {
            const newTrack = this.trackList.find(x => x.id === parseInt(e.target.dataset.trackId))

            this.loadTrack(newTrack)
            this.play()

            this.menu.dispatchEvent(new Event("menu:close"))
        },

        progressClick: function (e)
        {
            this.player.currentTime = parseInt(e.target.value)
        },
    })

    XF.Element.register('w-audio-player', 'XF.WAudioPlayer')
} (window, document)