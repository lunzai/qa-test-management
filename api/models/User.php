<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;
use yii\filters\RateLimitInterface;
use app\models\SoftDeleteModel;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $email
 * @property string $display_name
 * @property string|null $job_role
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string|null $email_verification_token
 * @property int|null $email_verified_at
 * @property string|null $jwt_token
 * @property int|null $jwt_token_expired_at
 * @property int|null $rate_limit_allowance
 * @property int|null $rate_limit_allowance_updated_at
 * @property string|null $lark_id
 * @property string|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_by
 * @property int|null $deleted_by
 *
 * @property Group[] $starredGroups
 */
class User extends SoftDeleteModel implements IdentityInterface, RateLimitInterface
{
    const SCENARIO_UPDATE = 'update';

    const ROLE_USER = 'User';
    const ROLE_ADMIN = 'Admin';
    const ROLE_TIMELINE = 'Timeline';

    const JOB_ROLE_USER = 'User';
    const JOB_ROLE_DEVELOPER = 'Developer';
    const JOB_ROLE_QA = 'QA';
    const JOB_ROLE_PM = 'PM';
    const JOB_ROLE_MANAGEMENT = 'Management';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }
    
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['timestamp'] = TimestampBehavior::class;
        return $behaviors;
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[ self::SCENARIO_UPDATE ] = ['email', 'job_role', 'display_name', 'status', 'lark_id'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'password_hash', 'display_name'], 'required'],
            [['email_verified_at', 'jwt_token_expired_at', 'created_at', 'updated_at', 'rate_limit_allowance',
            'rate_limit_allowance_updated_at', 'deleted_by', 'deleted_at'], 'integer'],
            [['email', 'display_name', 'job_role', 'password_hash', 'password_reset_token', 'email_verification_token', 'jwt_token', 'status', 'lark_id'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['email'], 'email'],
            [['password_reset_token'], 'unique'],
            [['email_verification_token'], 'unique'],
            [['jwt_token'], 'unique'],
            [['lark_id'], 'unique'],
            [['lark_id'], 'match', 'pattern' => '/^[a-z0-9]{8,12}$/i'],
            [['status'], 'in', 'range' => array_values(self::getConstants('status'))],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'email' => Yii::t('app', 'Email'),
            'display_name' => Yii::t('app', 'Display Name'),
            'job_role' => Yii::t('app', 'Job Role'),
            'password_hash' => Yii::t('app', 'Password'),
            'password_reset_token' => Yii::t('app', 'Password Reset Token'),
            'email_verification_token' => Yii::t('app', 'Email Verification Token'),
            'email_verified_at' => Yii::t('app', 'Email Verified At'),
            'jwt_token' => Yii::t('app', 'Jwt Token'),
            'jwt_token_expired_at' => Yii::t('app', 'Jwt Token Expired At'),
            'rate_limit_allowance' => Yii::t('app', 'Rate Limit Allowance'),
            'rate_limit_allowance_updated_at' => Yii::t('app', 'Rate Limit Allowance Updated At'),
            'lark_id' => Yii::t('app', 'Lark User ID'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted_at' => 'Deleted By',
            'deleted_by' => 'Deleted By',
        ];
    }
    
    public function fields()
    {
        $fields = parent::fields();
        unset(
            $fields['password_hash'],
            $fields['password_reset_token'],
            $fields['email_verification_token'],
            $fields['email_verified_at'],
            $fields['jwt_token'],
            $fields['jwt_token_expired_at'],
            $fields['rate_limit_allowance'],
            $fields['rate_limit_allowance_updated_at']
        );
        
        $auth = Yii::$app->getAuthManager();
        $fields['roles'] = function ($model) use ($auth) {
            return array_keys($auth->getRolesByUser($model->id));
        };

        $fields['is_admin'] = function ($model) use ($auth) {
            return $auth->checkAccess($model->id, self::ROLE_ADMIN);
        };

        return $fields;
    }

    /**
     * Gets query for [[Groups]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStarredGroups()
    {
        return $this->hasMany(Group::className(), ['id' => 'group_id'])
            ->viaTable('group_user', ['user_id' => 'id'])
            ->orderBy(['{{group}}.created_at' => SORT_DESC, '{{group}}.id' => SORT_DESC]);
    }

    public static function find()
    {
        return new UserQuery(get_called_class());
    }
    
    public function getRateLimit($request, $action)
    {
        return [ getenv('API_RATELIMIT'), getenv('API_RATELIMIT_DURATION') ];
    }
    
    public function loadAllowance($request, $action)
    {
        return [ $this->rate_limit_allowance, $this->rate_limit_allowance_updated_at ];
    }
    
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        $this->rate_limit_allowance = $allowance;
        $this->rate_limit_allowance_updated_at = $timestamp;
        $this->save(false, ['rate_limit_allowance', 'rate_limit_allowance_updated_at']);
    }
    
    public static function findByEmail(string $value): ?User
    {
        return static::find()->byEmail($value)->one();
    }
    
    public static function findIdentity($id): ?User
    {
        return static::findOne($id);
    }
    
    public static function findIdentityByAccessToken($token, $type = null): ?User
    {
        $user = static::find()->jwtToken($token)->one();
        return $user && $user->isJwtTokenValid($token) ? $user : null;
    }
    
    public function isActive(): bool
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    public function getId(): int
    {
        return $this->id;
    }
    
    public function getToken(): ?string
    {
        return $this->jwt_token;
    }
    
    public function getAuthKey(): ?string
    {
        return false;
    }
    
    public function validateAuthKey($authKey): bool
    {
        return false;
    }
    
    public function generateOrRefreshJwtToken($jwtToken): void
    {
        if ($this->isJwtTokenValid($jwtToken)) {
            $this->refreshJwtTokenExpiry();
        } else {
            $this->generateJwtToken();
        }
        $this->save(false, ['jwt_token', 'jwt_token_expired_at']);
    }
    
    public function generateJwtToken(): void
    {
        $this->jwt_token = Yii::$app->getSecurity()->generateRandomString(128);
        $this->jwt_token_expired_at = time() + getenv('JWT_EXPIRY_DURATION');
    }
    
    public function revokeJwtToken(): void
    {
        $this->jwt_token = null;
        $this->jwt_token_expired_at = null;
        $this->save(false, ['jwt_token', 'jwt_token_expired_at']);
    }
    
    public function refreshJwtTokenExpiry(): void
    {
        $this->jwt_token_expired_at = time() + getenv('JWT_EXPIRY_DURATION');
        $this->save(false, ['jwt_token_expired_at']);
    }
    
    public function isJwtTokenValid($token): bool
    {
        if (!$token || !$this->jwt_token) {
            return false;
        }
        return Yii::$app->getSecurity()->compareString($token, $this->jwt_token) && $this->jwt_token_expired_at >= time();
    }
    
    public function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
    }
    
    public function validatePassword(string $password): bool
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_hash);
    }
    
    public function generatePasswordResetToken(): void
    {
        $this->password_reset_token = Yii::$app->getSecurity()->generateRandomString(32) . '_' . time();
    }
    
    public function isVerifiedEmail(): bool
    {
        return $this->email_verification_token === null && $this->email_verified_at !== null;
    }
    
    public function generateEmailVerificationToken(): void
    {
        $this->email_verification_token = Yii::$app->getSecurity()->generateRandomString(32);
    }
    
    public function removeEmailVerificationToken(): void
    {
        $this->email_verification_token = null;
    }
    
    public function removePasswordResetToken(): void
    {
        $this->password_reset_token = null;
    }
    
    public static function isPasswordResetTokenValid(string $token): bool
    {
        if (empty($token)) {
            return false;
        }
        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = getenv('PASSWORD_RESET_TOKEN_DURATION');
        return $timestamp + $expire >= time();
    }
    
    public function beforeSave($insert): bool
    {
        if ($insert) {
            if (!$this->isVerifiedEmail()) {
                $this->generateEmailVerificationToken();
            }
            $this->generateJwtToken();
        }
        return parent::beforeSave($insert);
    }
}

class UserQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere(['status' => User::STATUS_ACTIVE]);
    }
    
    public function jwtToken(string $value): \yii\db\ActiveQuery
    {
        return $this->andWhere(['jwt_token' => $value]);
    }
    
    public function byEmail(string $value): \yii\db\ActiveQuery
    {
        return $this->andWhere(['email' => $value]);
    }

    public function all($db = null)
    {
        return parent::all($db);
    }

    public function one($db = null)
    {
        return parent::one($db);
    }
}
