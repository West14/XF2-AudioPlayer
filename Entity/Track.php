<?php

namespace West\AudioPlayer\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int $track_id
 * @property string $title
 * @property string $type
 * @property int $track_date
 * @property bool $active
 * @property int $display_order
 *
 * GETTERS
 * @property mixed $url
 */
class Track extends Entity
{
    public function getUrl()
    {
        return \XF::app()->applyExternalDataUrl(
            sprintf('wap_audio/%s.%s?%d', $this->track_id, $this->type, $this->track_date)
        );
    }

    public function getAbstractedDataPath()
    {
        return sprintf('data://wap_audio/%s.%s', $this->track_id, $this->type);
    }

    protected function _postDelete()
    {
        \XF\Util\File::deleteFromAbstractedPath($this->getAbstractedDataPath());
    }

    public static function getStructure(Structure $structure)
    {
        $structure->table = 'xf_wap_track';
        $structure->shortName = 'West\AudioPlayer:Track';
        $structure->primaryKey = 'track_id';
        $structure->columns = [
            'track_id' => ['type' => self::UINT, 'autoIncrement' => true],
            'title' => ['type' => self::STR, 'required' => true, 'maxLength' => 128],
            'type' => ['type' => self::STR, 'required' => true, 'maxLength' => 8],
            'track_date' => ['type' => self::UINT, 'default' => \XF::$time],
            'active' => ['type' => self::BOOL, 'default' => true],
            'display_order' => ['type' => self::UINT, 'default' => 0],
        ];

        $structure->getters = [
            'url' => true
        ];

        return $structure;
    }
}