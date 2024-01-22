<?php

namespace app\models;

use yii\base\Model;
use app\components\ActiveDataProvider;
use app\models\Group;
use yii\db\Expression;
use app\models\SoftDeleteModel;

/**
 * GroupSearch represents the model behind the search form of `app\models\Group`.
 */
class GroupSearch extends Group
{
    public $excludeDeleted = true;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'id', 'total_count', 'passed_count', 'failed_count', 'pending_count',
                //'created_at', 'created_by', 'updated_at', 'updated_by'
            ], 'integer'],
            [['name', 'status', 'test_status'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Group::find();

        // add conditions that should always apply here
        $defaultOrder = new Expression(sprintf("FIELD(status, '%s', '%s')", Group::STATUS_ACTIVE, Group::STATUS_INACTIVE));
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => $defaultOrder,
            ],
        ]);

        $this->load($params, '');

        if ($this->excludeDeleted) {
            $query->andWhere(['<>', 'status', SoftDeleteModel::STATUS_DELETED]);
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'test_status' => $this->test_status,
            'total_count' => $this->total_count,
            'passed_count' => $this->passed_count,
            'failed_count' => $this->failed_count,
            'pending_count' => $this->pending_count,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
