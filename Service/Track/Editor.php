<?php

namespace West\AudioPlayer\Service\Track;

use West\AudioPlayer\Entity\Track;
use XF\Http\Upload;
use XF\Service\AbstractService;
use XF\Service\ValidateAndSavableTrait;
use XF\Util\File;

class Editor extends AbstractService
{
    use ValidateAndSavableTrait;

    protected ?Upload $file = null;
    private Track $track;

    public function __construct(\XF\App $app, Track $track)
    {
        parent::__construct($app);

        $this->track = $track;
    }

    public function setTitle(string $title)
    {
        $this->track->title = $title;
    }

    public function setDisplayOrder(int $displayOrder)
    {
        $this->track->display_order = $displayOrder;
    }

    public function setFileFromUpload(\XF\Http\Upload $upload)
    {
        $this->file = $upload;
        $this->track->type = $upload->getExtension();
        $this->track->track_date = \XF::$time;
    }

    protected function _validate()
    {
        $errorList = [];

        if ($this->file)
        {
            $this->file->isValid($errorList);
        }

        return $errorList;
    }

    protected function _save()
    {
        $track = $this->track;
        $file = $this->file;

        $em = $this->em();
        $em->beginTransaction();

        if ($track->save(true, false) && $this->file)
        {
            try
            {
                File::copyFileToAbstractedPath($file->getTempFile(), $track->getAbstractedDataPath());
            }
            catch (\RuntimeException $e)
            {
                \XF::logException($e);
                $em->rollback();
            }
        }

        $em->commit();
    }
}