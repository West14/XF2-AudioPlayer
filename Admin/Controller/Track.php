<?php

namespace West\AudioPlayer\Admin\Controller;

use XF\Admin\Controller\AbstractController;
use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\AbstractReply;

class Track extends AbstractController
{
    public function actionIndex()
    {
        $trackFinder = $this->getTrackRepo()
            ->findTracksForList();

        return $this->view('West\AudioPlayer:Track\Index', 'wap_track_index', [
            'trackList' => $trackFinder->fetch(),
        ]);
    }

    public function actionToggle()
    {
        /** @var \XF\ControllerPlugin\Toggle $plugin */
        $plugin = $this->plugin('XF:Toggle');
        return $plugin->actionToggle('West\AudioPlayer:Track');
    }

    public function actionUpload()
    {
        if ($this->isPost())
        {
            $em = $this->em();

            foreach ($this->request()->getFile('tracks', true) as $trackFile)
            {
                $track = $em->create('West\AudioPlayer:Track');

                /** @var \West\AudioPlayer\Service\Track\Editor $trackUploadService */
                $trackUploadService = $this->service('West\AudioPlayer:Track\Creator', $track, $trackFile);
                if (!$trackUploadService->validate($errorList))
                {
                    return $this->error($errorList);
                }

                $trackUploadService->save();
            }

            return $this->redirect($this->buildLink('wap-tracks'));
        }
        else
        {
            return $this->view('West\AudioPlayer:Track\Upload', 'wap_track_upload');
        }
    }

    public function actionAdd()
    {
        return $this->trackAddEdit(
            $this->em()->create('West\AudioPlayer:Track')
        );
    }

    public function actionEdit(ParameterBag $params)
    {
        return $this->trackAddEdit(
            $this->assertTrackExists($params->track_id)
        );
    }

    public function actionSave(ParameterBag $params)
    {
        $this->assertPostOnly();

        if ($params->track_id)
        {
            $track = $this->assertTrackExists($params->track_id);
        }
        else
        {
            $track = $this->em()->create('West\AudioPlayer:Track');
        }

        $this->trackSaveProcess($track)->run();

        return $this->redirect($this->buildLink('wap-tracks'));
    }

    public function actionSort()
    {
        $trackRepo = $this->getTrackRepo();
        $trackFinder = $trackRepo->findTracksForList();
        $trackList = $trackFinder->fetch();

        if ($this->isPost())
        {
            $sortData = $this->filter('tracks', 'json-array');

            /** @var \XF\ControllerPlugin\Sort $sorter */
            $sorter = $this->plugin('XF:Sort');
            $sorter->sortFlat($sortData, $trackList);

            return $this->redirect($this->buildLink('wap-tracks'));
        }
        else
        {
            return $this->view('West\AudioPlayer:Track\Sort', 'wap_track_sort', [
                'trackList' => $trackList
            ]);
        }
    }

    public function actionDelete(ParameterBag $params)
    {
        $track = $this->assertTrackExists($params->track_id);

        /** @var \XF\ControllerPlugin\Delete $plugin */
        $plugin = $this->plugin('XF:Delete');
        return $plugin->actionDelete(
            $track,
            $this->buildLink('wap-tracks/delete', $track),
            $this->buildLink('wap-tracks/edit', $track),
            $this->buildLink('wap-tracks'),
            $track->title
        );
    }

    protected function trackSaveProcess(\West\AudioPlayer\Entity\Track $track)
    {
        $input = $this->filter([
            'title' => 'str',
            'display_order' => 'uint'
        ]);

        $form = $this->formAction();
        $request = $this->request();

        $trackFile = $request->getFile('track');
        if ($track->isInsert())
        {
            if (!$trackFile)
            {
                return $form->logError(\XF::phrase('wap_you_must_upload_a_file_when_creating_a_new_track'));
            }

            /** @var \West\AudioPlayer\Service\Track\Creator $service */
            $service = $this->service(
                'West\AudioPlayer:Track\Creator',
                $track,
                $request->getFile('track')
            );
        }
        else
        {
            /** @var \West\AudioPlayer\Service\Track\Editor $service */
            $service = $this->service('West\AudioPlayer:Track\Editor', $track);

            if ($trackFile)
            {
                $service->setFileFromUpload($trackFile);
            }
        }

        $service->setTitle($input['title']);
        $service->setDisplayOrder($input['display_order']);

        return $form->basicValidateServiceSave($service);
    }

    protected function trackAddEdit(\West\AudioPlayer\Entity\Track $track): AbstractReply
    {
        return $this->view('West\AudioPlayer:Track\Edit', 'wap_track_edit', [
            'track' => $track
        ]);
    }

    /**
     * @param $id
     * @param $with
     * @param $phraseKey
     * @return \XF\Mvc\Entity\Entity|\West\AudioPlayer\Entity\Track
     * @throws \XF\Mvc\Reply\Exception
     */
    protected function assertTrackExists($id, $with = null, $phraseKey = null)
    {
        return $this->assertRecordExists('West\AudioPlayer:Track', $id, $with, $phraseKey);
    }

    /**
     * @return \XF\Mvc\Entity\Repository|\West\AudioPlayer\Repository\Track
     */
    public function getTrackRepo()
    {
        return $this->repository('West\AudioPlayer:Track');
    }
}