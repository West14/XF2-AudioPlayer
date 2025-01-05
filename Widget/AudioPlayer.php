<?php

namespace West\AudioPlayer\Widget;

use XF\Widget\AbstractWidget;

class AudioPlayer extends AbstractWidget
{
    public function render()
    {
        $trackList = $this->getTrackRepository()->findTracksForList()->fetch();

        return $this->renderer('widget_wap_audio_player', [
            'trackList' => $trackList
        ]);
    }

    public function getOptionsTemplate()
    {
        return null;
    }

    /**
     * @return \West\AudioPlayer\Repository\Track|\XF\Mvc\Entity\Repository
     */
    protected function getTrackRepository()
    {
        return $this->repository('West\AudioPlayer:Track');
    }
}