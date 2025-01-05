<?php

namespace West\AudioPlayer\Service\Track;


use West\AudioPlayer\Entity\Track;
use XF\Http\Upload;
use XF\Service\AbstractService;
use XF\Service\ValidateAndSavableTrait;
use XF\Util\File;

class Creator extends AbstractService
{
    use ValidateAndSavableTrait;

    protected Track $track;
    protected Upload $file;

    protected ?string $title = null;

    public function __construct(\XF\App $app, Track $track, Upload $upload)
    {
        parent::__construct($app);
        $this->track = $track;
        $this->file = $upload;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function setDisplayOrder(int $displayOrder)
    {
        $this->track->display_order = $displayOrder;
    }

    protected function _validate(): array
    {
        $this->file->isValid($errorList);

        return $errorList;
    }

    protected function _save()
    {
        $track = $this->track;
        $file = $this->file;

        $track->title = $this->title ?: pathinfo($file->getFileName(), PATHINFO_FILENAME);
        $track->type = $file->getExtension();

        $em = $this->em();
        $em->beginTransaction();;

        if ($track->save(true, false))
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