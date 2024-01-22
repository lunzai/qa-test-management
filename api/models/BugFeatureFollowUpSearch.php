<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\BugFeatureFollowUp;

/**
 * BugFeatureFollowUpSearch represents the model behind the search form of `app\models\BugFeatureFollowUp`.
 */
class BugFeatureFollowUpSearch extends BugFeatureFollowUp
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'bug_feature_id', 'actor_user_id', 'is_resolved'], 'integer'],
            [['status', 'description', 'resolved_detail'], 'safe'],
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
        $query = BugFeatureFollowUp::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, '');

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'bug_feature_id' => $this->bug_feature_id,
            'actor_user_id' => $this->actor_user_id,
            'is_resolved' => $this->is_resolved,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'resolved_detail', $this->resolved_detail]);

        return $dataProvider;
    }
}
