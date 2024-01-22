<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\BugFeature;
use yii\db\Expression;

/**
 * BugSearch represents the model behind the search form of `app\models\Bug`.
 */
class BugFeatureSearch extends BugFeature
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'reporter_user_id', 'qa_user_id', 'developer_user_id'], 'integer'],
            [['title', 'description', 'fix_status', 'priority', 'status', 'type', 'jira_number'], 'safe'],
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
        $query = BugFeature::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => new Expression(sprintf(
                    "FIELD(issue.status, '%s', '%s'), bug_feature.id DESC",
                    BugFeature::STATUS_ACTIVE,
                    BugFeature::STATUS_DELETED
                )),
            ]
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
            'reporter_user_id' => $this->reporter_user_id,
            'qa_user_id' => $this->reporter_user_id,
            'developer_user_id' => $this->reporter_user_id,
            'fix_status' => $this->fix_status,
            'priority' => $this->priority,
            'status' => $this->status,
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'jira_number', $this->jira_number])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
