<?php

namespace app\controllers;

use Yii;
use app\components\ActiveController;
use app\models\Timeline;
use app\models\User;
use app\models\Holiday;
use app\models\Country;
use yii\db\Expression;
use yii\caching\DbQueryDependency;
use yii\db\Query;

class TimelineController extends ActiveController
{
    public $modelClass = 'app\models\Timeline';

    const HALF_YEAR = 86400 * 180;

    public function actions(): array
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        $time = time();
        $start = $time - self::HALF_YEAR;
        $end = $time + self::HALF_YEAR;
        $cache = Yii::$app->getCache();
        $dependency = new DbQueryDependency([
            'reusable' => true,
            'query' => (new Query())->select('MAX(updated_at)')
                ->from('timeline')
        ]);
        return $cache->getOrSet(
            'timeline-event',
            function () use ($start, $end) {
                return Timeline::find()
                    ->select([
                        'timeline.id', 'timeline.title', 'timeline.start', 'timeline.end',
                        'user.job_role AS type', 'timeline.user_id AS resourceId'
                    ])
                    ->joinWith('user', false)
                    ->active()
                    ->startAfter($start)
                    ->endBefore($end)
                    ->asArray()
                    ->all();
            },
            86400 * 30,
            $dependency
        );
    }

    public function actionResource()
    {
        $auth = Yii::$app->getAuthManager();
        $timelineRole = $auth->getRole(User::ROLE_TIMELINE);
        if (!$timelineRole) {
            return [];
        }
        $dependency = new DbQueryDependency([
            'reusable' => true,
            'query' => (new Query())->select('MAX(created_at)')
                ->from('auth_assignment')
                ->andWhere(['item_name' => $timelineRole->name])
        ]);
        $cache = Yii::$app->getCache();
        return $cache->getOrSet(
            'timeline-resource',
            function () use ($auth) {
                $userIds = $auth->getUserIdsByRole(User::ROLE_TIMELINE);
                return User::find()
                    ->select(['id', 'type' => 'job_role', 'title' => 'display_name'])
                    ->active()
                    ->andWhere(['id' => $userIds])
                    ->asArray()
                    ->all();
            },
            86400 * 30,
            $dependency
        );
    }

    public function actionCountry()
    {
        $time = time();
        $start = $time - self::HALF_YEAR;
        $end = $time + self::HALF_YEAR;
        $cache = Yii::$app->getCache();
        $dependency = new DbQueryDependency([
            'reusable' => true,
            'query' => (new Query())->select('MAX(updated_at)')
                ->from('country')
        ]);
        return $cache->getOrSet(
            'timeline-country',
            function () use ($start, $end) {
                return Country::find()
                    ->select([
                        'country.iso2 AS id',
                        'country.name AS title',
                        new Expression('\'Holiday\' AS type')
                    ])
                    ->distinct('country.id')
                    ->joinWith('holidays', false, 'RIGHT JOIN')
                    ->andWhere(['>=', 'holiday.start_ts', $start])
                    ->andWhere(['<=', 'holiday.end_ts', $end])
                    ->asArray()
                    ->all();
            },
            86400 * 30,
            $dependency
        );
    }

    public function actionHoliday()
    {
        $time = time();
        $start = $time - self::HALF_YEAR;
        $end = $time + self::HALF_YEAR;
        $cache = Yii::$app->getCache();
        $dependency = new DbQueryDependency([
            'reusable' => true,
            'query' => (new Query())->select('MAX(updated_at)')
                ->from('holiday')
        ]);
        return $cache->getOrSet(
            'timeline-holiday',
            function () use ($start, $end) {
                return Holiday::find()
                    ->joinWith('country', false)
                    ->select([
                        'CONCAT(\'holiday-\', holiday.id) AS id', 'holiday.title', 'country.iso2 AS resourceId',
                        'holiday.start', 'holiday.end'
                    ])
                    ->andWhere(['>=', 'holiday.start_ts', $start])
                    ->andWhere(['<=', 'holiday.end_ts', $end])
                    ->asArray()
                    ->all();
            },
            86400 * 30,
            $dependency
        );
    }
}
