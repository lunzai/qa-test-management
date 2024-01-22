<?php

namespace app\models;

use Yii;
use app\models\SoftDeleteModel;

class BaseTestModel extends SoftDeleteModel
{
    const TEST_RESULT_PASSED = 'Passed';
    const TEST_RESULT_FAILED = 'Failed';
    const TEST_RESULT_PENDING = 'Pending';
    const TEST_RESULT_UNABLE_TO_TEST = 'Unable To Test';

    public function isPassedTest()
    {
        return $this->test_status == self::TEST_RESULT_PASSED;
    }

    public function isFailedTest()
    {
        return $this->test_status == self::TEST_RESULT_FAILED;
    }
    
    public function isPendingTest()
    {
        return $this->test_status == self::TEST_RESULT_PENDING;
    }

    public function isUnableToTest()
    {
        return $this->test_status == self::TEST_RESULT_UNABLE_TO_TEST;
    }
}
