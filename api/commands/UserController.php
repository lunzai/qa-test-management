<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\models\User;
use yii\helpers\Console;

class UserController extends Controller
{
    public function actionAssign($email, $role)
    {
        $this->stdout(PHP_EOL);
        $this->stdout(Console::ansiFormat('Assign role', [ Console::BOLD, Console::BG_BLUE, Console::UNDERLINE ]));
        $this->stdout(PHP_EOL);
        $this->stdout(PHP_EOL);

        $user = User::findByEmail($email);
        $auth = Yii::$app->authManager;
        $authRole = $auth->getRole(ucfirst($role));
        if (!$user || !$authRole) {
            $this->stderr('🤷 Not Found', Console::FG_RED);
            $this->stdout(PHP_EOL);
            $this->stdout(PHP_EOL);
            return ExitCode::UNSPECIFIED_ERROR;
        }
        $auth->assign($authRole, $user->id);
        $this->stdout('🎉 Done' . PHP_EOL, Console::FG_GREEN);
        $this->stdout(PHP_EOL);
        return ExitCode::OK;
    }

    public function actionRevoke($email, $role)
    {
        $this->stdout(PHP_EOL);
        $this->stdout(Console::ansiFormat('Revoke role', [ Console::BOLD, Console::BG_BLUE, Console::UNDERLINE ]));
        $this->stdout(PHP_EOL);
        $this->stdout(PHP_EOL);

        $user = User::findByEmail($email);
        $auth = Yii::$app->authManager;
        $authRole = $auth->getRole(ucfirst($role));
        if (!$user || !$authRole) {
            $this->stderr('🤷 Not Found', Console::FG_RED);
            $this->stdout(PHP_EOL);
            $this->stdout(PHP_EOL);
            return ExitCode::UNSPECIFIED_ERROR;
        }
        $auth->revoke($authRole, $user->id);
        $this->stdout('🎉 Done' . PHP_EOL, Console::FG_GREEN);
        $this->stdout(PHP_EOL);
        return ExitCode::OK;
    }

    public function actionRbac()
    {
        $this->stdout(PHP_EOL);
        $this->stdout(Console::ansiFormat('Re-initiate RBAC', [ Console::BOLD, Console::BG_BLUE, Console::UNDERLINE ]));
        $this->stdout(PHP_EOL);
        $this->stdout(PHP_EOL);

        $auth = Yii::$app->authManager;

        $adminRole = $auth->getRole(User::ROLE_ADMIN);
        if (!$adminRole) {
            $adminRole = $auth->createRole(User::ROLE_ADMIN);
            $auth->add($adminRole);
            $this->stdout('➕ Add ' . User::ROLE_ADMIN . PHP_EOL);
        } else {
            $this->stdout('✅ ' . User::ROLE_ADMIN . ' existed. Do nothing.' . PHP_EOL);
        }

        $userRole = $auth->getRole(User::ROLE_USER);
        if (!$userRole) {
            $userRole = $auth->createRole(User::ROLE_USER);
            $auth->add($userRole);
            $this->stdout('➕ Add ' . User::ROLE_USER . PHP_EOL);

            $auth->addChild($adminRole, $userRole);
            $this->stdout('➕ ' . User::ROLE_ADMIN . ' add child ' . User::ROLE_USER . PHP_EOL);
        } else {
            $this->stdout('✅ ' . User::ROLE_USER . ' existed. Do nothing.' . PHP_EOL);
        }

        $timelineRole = $auth->getRole(User::ROLE_TIMELINE);
        if (!$timelineRole) {
            $timelineRole = $auth->createRole(User::ROLE_TIMELINE);
            $auth->add($timelineRole);
            $this->stdout('➕ Add ' . User::ROLE_TIMELINE . PHP_EOL);

            $auth->addChild($adminRole, $timelineRole);
            $this->stdout('➕ ' . User::ROLE_ADMIN . ' add child ' . User::ROLE_TIMELINE . PHP_EOL);
        } else {
            $this->stdout('✅ ' . User::ROLE_TIMELINE . ' existed. Do nothing.' . PHP_EOL);
        }
        
        $this->stdout(PHP_EOL);
        $this->stdout(Console::ansiFormat('Assign default user\'s Role', [ Console::BOLD, Console::BG_BLUE, Console::UNDERLINE ]));
        $this->stdout(PHP_EOL);
        $this->stdout(PHP_EOL);

        $users = User::find()->all();
        foreach ($users as $user) {
            if (!$auth->getRolesByUser($user->id)) {
                $auth->assign($userRole, $user->id);
            }
        }
        $this->stdout('🎉 Done' . PHP_EOL, Console::FG_GREEN);
        $this->stdout(PHP_EOL);
        return ExitCode::OK;
    }

    public function actionChangePassword($email, $password)
    {
        $this->stdout(PHP_EOL);
        $this->stdout(Console::ansiFormat('Assign default user\'s Role', [ Console::BOLD, Console::BG_BLUE, Console::UNDERLINE ]));
        $this->stdout(PHP_EOL);
        $this->stdout(PHP_EOL);

        $passwordMinLength = (int) getenv('PASSWORD_LENGTH_MIN');
        if (strlen($password) < $passwordMinLength) {
            $this->stderr("🚫 Password needs to be at least {$passwordMinLength} characters.", Console::FG_RED);
            $this->stdout(PHP_EOL);
            $this->stdout(PHP_EOL);
            return ExitCode::UNSPECIFIED_ERROR;
        }
        $user = User::findByEmail($email);
        if (!$user) {
            $this->stderr('🤷 Not Found', Console::FG_RED);
            $this->stdout(PHP_EOL);
            $this->stdout(PHP_EOL);
            return ExitCode::UNSPECIFIED_ERROR;
        }
        $user->setPassword($password);
        if ($user->save()) {
            $this->stdout('🎉 Done', Console::FG_GREEN);
            $this->stdout(PHP_EOL);
            $this->stdout(PHP_EOL);
            return ExitCode::OK;
        } else {
            $this->stderr('🚫 NOT OK', Console::FG_RED);
            $this->stdout(PHP_EOL);
            $this->stdout(PHP_EOL);
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}
