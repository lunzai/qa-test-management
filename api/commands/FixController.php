<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use app\models\Issue;
use app\models\Group;

class FixController extends Controller
{
    public function actionRecalculate()
    {
        $this->stdout(PHP_EOL);
        $this->stdout(Console::ansiFormat('Recalculate fix', [ Console::BOLD, Console::BG_BLUE, Console::UNDERLINE ]));
        $this->stdout(PHP_EOL);
        $this->stdout(PHP_EOL);

        $issues = Issue::find()->all();
        $groups = Group::find()->all();
        $issueCount = Issue::find()->count();
        $groupCount = Group::find()->count();

        $i = 0;
        Console::startProgress($i, $issueCount, 'Issue: ', 0.8);
        foreach ($issues as $model) {
            $model->recalculate();
            $i++;
            Console::updateProgress($i, $issueCount);
        }
        Console::endProgress();

        $i = 0;
        Console::startProgress(0, $groupCount, 'Group: ', 0.8);
        foreach ($groups as $model) {
            $model->recalculate();
            $i++;
            Console::updateProgress($i, $groupCount);
        }
        Console::endProgress();

        $this->stdout(PHP_EOL);
        $this->stdout(PHP_EOL);
        $this->stdout('🎉 Done' . PHP_EOL, Console::FG_GREEN);
        $this->stdout(PHP_EOL);
        return ExitCode::OK;
    }
}
