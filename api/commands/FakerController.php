<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Group;
use app\models\Issue;
use Faker\Factory;

class FakerController extends Controller
{
    public $faker;

    public function init()
    {
        parent::init();
        $this->faker = Factory::create();
    }

    public function actionGroup($count = 10, $prefix = 'Sprint ')
    {
        $time = time();
        $success = 0;
        $this->stdout("Faker: Create {$count} group..." . PHP_EOL);
        for ($i = 0; $i < $count; $i++) {
            $name = $prefix . Yii::$app->security->generateRandomString(6);
            $model = new Group(['name' => $name, 'status' => Group::STATUS_ACTIVE]);
            if ($model->save()) {
                $success++;
            }
        }
        $this->stdout("Success: {$success}" . PHP_EOL);
        return ExitCode::OK;
    }

    public function actionIssue($groupId, $count = 10)
    {
        $time = time();
        $success = 0;
        $this->stdout("Faker: Create {$count} issue for group ID#{$groupId}..." . PHP_EOL);
        for ($i = 0; $i < $count; $i++) {
            $model = new Issue([
                'name' => $this->faker->sentence(rand(6, 12), true),
                'status' => Issue::STATUS_ACTIVE,
                'group_id' => $groupId,
            ]);
            if ($model->save()) {
                $success++;
            } else {
                var_dump($model->errors);
                // $this->stderr(implode(', ', $model->errors));
            }
        }
        $this->stdout("Success: {$success}" . PHP_EOL);
        return ExitCode::OK;
    }
}
