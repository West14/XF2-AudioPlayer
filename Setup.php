<?php

namespace West\AudioPlayer;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;

class Setup extends AbstractSetup
{
	use StepRunnerInstallTrait;
	use StepRunnerUpgradeTrait;
	use StepRunnerUninstallTrait;

    public function installStep1()
    {
        $this->createTable('xf_wap_track', function (\XF\Db\Schema\Create $table)
        {
            $table->addColumn('track_id', 'int')->autoIncrement();
            $table->addColumn('title', 'varchar', 128);
            $table->addColumn('type', 'varchar', 8);
            $table->addColumn('track_date', 'int')->setDefault(0);
            $table->addColumn('active', 'tinyint')->setDefault(1);
            $table->addColumn('display_order', 'int')->setDefault(0);
        });
    }

    public function uninstallStep1()
    {
        $this->dropTable('xf_wap_track');
    }
}