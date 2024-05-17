!function ($, window, document) {
    'use strict'

    XF.WAudioPlayer = XF.Element.newHandler({
        songList: [
            { url: 'http://xf22.local/data/wap_audio/1.mp3', title: 'Lightfall' },
            { url: 'http://xf22.local/data/wap_audio/2.mp3', title: 'All In' },
            { url: 'http://xf22.local/data/wap_audio/3.mp3', title: 'All That Matters' },
            { url: 'http://xf22.local/data/wap_audio/4.mp3', title: 'Distant Sky' },
            { url: 'http://xf22.local/data/wap_audio/5.mp3', title: 'CloudArk' },
            { url: 'http://xf22.local/data/wap_audio/6.mp3', title: 'At the Gates' },
            { url: 'http://xf22.local/data/wap_audio/7.mp3', title: 'Future Unknown' },
            { url: 'http://xf22.local/data/wap_audio/8.mp3', title: 'Frontline' },
            { url: 'http://xf22.local/data/wap_audio/9.mp3', title: 'Service and Sacrifice' },
            { url: 'http://xf22.local/data/wap_audio/10.mp3', title: 'Herald of the Witness' },
        ]
    })


    XF.Element.register('w-audio-player', 'XF.WAudioPlayer')
} (jQuery, window, document)