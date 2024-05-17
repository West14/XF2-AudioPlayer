<?php

namespace West\AudioPlayer\Widget;

use XF\Widget\AbstractWidget;

class AudioPlayer extends AbstractWidget
{
    public function render()
    {
        $songList = [
            1 => ['url' => 'http://xf22.local/data/wap_audio/1.mp3', 'title' => 'Lightfall'],
            2 => ['url' => 'http://xf22.local/data/wap_audio/2.mp3', 'title' => 'All In'],
            3 => ['url' => 'http://xf22.local/data/wap_audio/3.mp3', 'title' => 'All That Matters'],
            4 => ['url' => 'http://xf22.local/data/wap_audio/4.mp3', 'title' => 'Distant Sky'],
            5 => ['url' => 'http://xf22.local/data/wap_audio/5.mp3', 'title' => 'CloudArk'],
            6 => ['url' => 'http://xf22.local/data/wap_audio/6.mp3', 'title' => 'At the Gates'],
            7 => ['url' => 'http://xf22.local/data/wap_audio/7.mp3', 'title' => 'Future Unknown'],
            8 => ['url' => 'http://xf22.local/data/wap_audio/8.mp3', 'title' => 'Frontline'],
            9 => ['url' => 'http://xf22.local/data/wap_audio/9.mp3', 'title' => 'Service and Sacrifice'],
            10 => ['url' => 'http://xf22.local/data/wap_audio/10.mp3', 'title' => 'Herald of the Witness'],
        ];

        return $this->renderer('widget_wap_audio_player', [
            'songList' => $songList
        ]);
    }

    public function getOptionsTemplate()
    {
        return null;
    }
}