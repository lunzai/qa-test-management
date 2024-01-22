<?php

namespace app\components;

use yii\data\ActiveDataProvider as DataActiveDataProvider;

class ActiveDataProvider extends DataActiveDataProvider
{
    public function init()
    {
        parent::init();
        $this->pagination->defaultPageSize = (int) getenv('DEFAULT_PAGE_SIZE');
        $this->pagination->pageParam = getenv('PAGE_PARAM');
        $this->pagination->pageSizeParam = getenv('PAGE_SIZE_PARAM');
        $this->pagination->pageSizeLimit = [
            (int) getenv('PAGE_SIZE_LIMIT_MIN'),
            (int) getenv('PAGE_SIZE_LIMIT_MAX')
        ];
        $this->sort->sortParam = getenv('SORT_PARAM');
        if ($this->sort->defaultOrder === null) {
            $this->sort->defaultOrder = [
                'id' => SORT_DESC,
            ];
        }
    }
}
