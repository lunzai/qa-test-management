import * as JiraApi from '@app/api/jira';
import * as TestIssueApi from '@app/api/testIssue';
import moment from 'moment';
import { TEST_STATUS_FAILED } from '@app/constants';

export async function post(req, res) {
    const { issueId, issue, description, status, resultLink, issueLink } = req.body;
    try {
        if (status == TEST_STATUS_FAILED) { // shame the dev
            JiraApi.addStatusComment(
                issue, 
                status, 
                description,
                req.session.user.display_name,
                moment().format('MMMM Do YYYY, h:mm:ss a'),
                resultLink
            );
        }
        const response = await TestIssueApi.view(issueId, {}, req.session.token);
        if (response.success) {
            JiraApi.updateCustomField(issue, response.data.test_status, issueLink);
        }
        res.end('OK');
    } catch (error) {
        res.statusCode = 500;
        res.end(JSON.stringify(error));
    }
}

