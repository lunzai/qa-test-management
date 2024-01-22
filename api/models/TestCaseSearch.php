<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TestCase;
use app\models\SoftDeleteModel;

/**
 * TestCaseSearch represents the model behind the search form of `app\models\TestCase`.
 */
class TestCaseSearch extends TestCase
{
    public $excludeDeleted = true;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'issue_id'], 'integer'],
            [
                [
                    'description', 'platform', 'pre_condition', 'replicate_step',
                    'short_description', 'expected_result', 'test_status', 'status'
                ], 'safe'
            ],
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
        $query = TestCase::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'issue_id' => $this->issue_id,
            'status' => $this->status,
            'test_status' => $this->test_status,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'short_description', $this->short_description])
            ->andFilterWhere(['like', 'platform', $this->platform])
            ->andFilterWhere(['like', 'pre_condition', $this->pre_condition])
            ->andFilterWhere(['like', 'replicate_step', $this->replicate_step])
            ->andFilterWhere(['like', 'expected_result', $this->expected_result]);

        return $dataProvider;
    }
}
