<?php
namespace app\controllers;

use Yii;
use app\components\ActiveController;
use yii\web\UnauthorizedHttpException;
use yii\db\Query;

class CypressController extends ActiveController
{
    public $modelClass = 'app\models\User';

    public function actions(): array
    {
        return [];
    }

    public function actionCleanUp()
    {
        $db = Yii::$app->getDb();
        $user = Yii::$app->getUser()->getIdentity();
        if (!$user || !preg_match('/^.*cypress.*@whitecoat\.(com\.sg|global)$/i', $user->email)) {
            throw new UnauthorizedHttpException('Unauthorized access');
        }
        $tables = ['test_result', 'test_case', 'issue', 'group'];
        foreach ($tables as $table) {
            $db->createCommand()
                ->delete($table, 'created_by = :userId', ['userId' => $user->id])
                ->execute();
        }
        $results = (new Query())
            ->select('file_path')
            ->from('file')
            ->where(['created_by' => $user->id])
            ->all();
        foreach ($results as $file) {
            Yii::$app->fs->delete($file['file_path']);
        }
        $db->createCommand()
            ->delete('file', 'created_by = :userId', ['userId' => $user->id])
            ->execute();
        $db->createCommand()
            ->delete('user', 'id = :userId', ['userId' => $user->id])
            ->execute();
    }
}
