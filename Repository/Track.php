<?php

namespace West\AudioPlayer\Repository;

use XF\Mvc\Entity\Repository;

class Track extends Repository
{
    public function findTracksForList()
    {
        return $this->finder('West\AudioPlayer:Track')
            ->order('display_order');
    }

    public function findTracksForWidget()
    {
        return $this->findTracksForList()
            ->where('active', true);
    }
}