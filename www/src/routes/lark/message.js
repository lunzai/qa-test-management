import * as larkApi from '@app/api/lark';

export async function post(req, res) {
    const { issueNumber, testIssue, testCase, url, devLarkId, qaLarkId } = req.body;
    try {
        larkApi.message(issueNumber, testIssue, testCase, url, devLarkId, qaLarkId);
        res.end('OK');
    } catch (error) {
        res.statusCode = 500;
        res.end(JSON.stringify(error));
    }
    res.end('OK');
}

