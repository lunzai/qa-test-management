<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TestResult;
use app\models\SoftDeleteModel;

/**
 * TestResultSearch represents the model behind the search form of `app\models\TestResult`.
 */
class TestResultSearch extends TestResult
{
    public $excludeDeleted = true;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'test_case_id', 'tester_user_id'], 'integer'],
            [['version', 'platform', 'actual_result', 'test_status', 'status'], 'safe'],
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
        $query = TestResult::find();

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
            'test_case_id' => $this->test_case_id,
            'tester_user_id' => $this->tester_user_id,
            'test_status' => $this->test_status,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'version', $this->version])
            ->andFilterWhere(['like', 'platform', $this->platform])
            ->andFilterWhere(['like', 'actual_result', $this->actual_result]);

        return $dataProvider;
    }
}
