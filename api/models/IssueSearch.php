<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Issue;
use yii\db\conditions\OrCondition;
use yii\db\Expression;
use app\models\SoftDeleteModel;
use Yii;

/**
 * IssueSearch represents the model behind the search form of `app\models\Issue`.
 */
class IssueSearch extends Issue
{
    public $excludeDeleted = true;
    public $assignedTo;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'id', 'group_id', 'qa_user_id', 'developer_user_id',
                'total_count', 'passed_count', 'failed_count', 'pending_count',
                'assignedTo'
            ], 'integer'],
            [['name', 'description', 'jira_number', 'status', 'test_status', 'group_name', 'group_status'], 'safe'],
        ];
    }

    public function attributes()
    {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), [
            'group_name',
            'group_status'
        ]);
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
        $query = Issue::find()->joinWith(['group']);

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => new Expression(sprintf(
                    "FIELD(issue.status, '%s', '%s'), FIELD(issue.test_status, '%s', '%s', '%s', '%s'), issue.id DESC",
                    Issue::STATUS_ACTIVE,
                    Issue::STATUS_INACTIVE,
                    Issue::TEST_RESULT_FAILED,
                    Issue::TEST_RESULT_PENDING,
                    Issue::TEST_RESULT_PASSED,
                    Issue::TEST_RESULT_UNABLE_TO_TEST
                )),
            ]
        ]);

        $dataProvider->sort->attributes['group.name'] = [
            'asc' => ['group_name' => SORT_ASC],
            'desc' => ['group_name' => SORT_DESC],
        ];

        $this->load($params, '');

        if ($this->excludeDeleted) {
            $query->andWhere(['<>', 'issue.status', SoftDeleteModel::STATUS_DELETED]);
        }
        
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'issue.id' => $this->id,
            'group_id' => $this->group_id,
            'qa_user_id' => $this->qa_user_id,
            'developer_user_id' => $this->developer_user_id,
            'issue.total_count' => $this->total_count,
            'issue.passed_count' => $this->passed_count,
            'issue.failed_count' => $this->failed_count,
            'issue.pending_count' => $this->pending_count,
            'issue.status' => $this->status,
            'issue.test_status' => $this->test_status,
            'group.status' => $this->getAttribute('group_status'),
        ]);

        if ($this->assignedTo) {
            $query->andWhere(new OrCondition([
                'issue.qa_user_id = :id',
                'issue.developer_user_id = :id',
            ]), ['id' => $this->assignedTo]);
        } else {
            $query->andFilterWhere([
                'issue.qa_user_id' => $this->qa_user_id,
                'issue.developer_user_id' => $this->developer_user_id,
            ]);
        }

        $query->andFilterWhere(['like', 'issue.name', $this->name])
            ->andFilterWhere(['like', 'issue.description', $this->description])
            ->andFilterWhere(['like', 'issue.jira_number', $this->jira_number])
            ->andFilterWhere(['like', 'group.name', $this->getAttribute('group_name')]);

        return $dataProvider;
    }
}
