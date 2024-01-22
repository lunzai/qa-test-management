<script context="module">
    import TestCaseModel from '@app/models/TestCase';
    import { loginIsValid } from '@app/accessControl';

    export async function preload({ params }, { token }) {
        const login = await loginIsValid(token);
        if (!login) {
            return this.redirect(302, '/user/logout');
        }    
        try {
            let testCase = new TestCaseModel({ token });
            let result = await testCase.find(params.id, { expand: 'issue,issue.group,issue.qa,issue.developer' });
            if (result) {
                return { testCase };
            } else {
                return this.error(500, '🙀 Oh no. Something wrong?!');
            }
        } catch (error) {
            this.error(500, error.name + ': ' + error.message);
        }
    }
</script>

<script>
    import Container from '../../../components/Container.svelte';
    import Wrapper from '../../../components/Wrapper.svelte';
    import PageTitle from '../../../components/PageTitle.svelte';
    import Card from '../../../components/Card.svelte';
    import Button from '../../../components/Button.svelte';
    import DetailedRow from '../../../components/DetailedRow.svelte';
    import TestResultModel from '@app/models/TestResult';
    import moment from 'moment';
    import Form from '../_form.svelte';
    import { stores } from '@sapper/app';
    import { STATUS_ACTIVE } from '@app/constants';

    export let testCase;
    const issue = testCase.get('issue');
    const group = issue.group;
    
    const { session } = stores();
    const { token, user } = $session;
    const model = new TestResultModel({
        model: { 
            test_case_id: testCase.getId(),
            tester_user_id: user.id,
            status: STATUS_ACTIVE
        },
        token,
    });
    const title = `Test Case: ${testCase.get('short_description')} - Submit Test Result`;
    const breadcrumb = [
        { label: 'Home', href: '/' },
        { label: 'Group', href: '/group' },
        { label: group.name, href: `/group/${group.id}` },
        { label: issue.name, href: `/issue/${issue.id}` },
        { label: testCase.get('short_description'), href: `/issue/${issue.id}?tab=${testCase.getId()}` },
        { label: 'Submit Test Result' }
    ];
</script>

<svelte:head>
    <title>{title}</title>
</svelte:head>

<PageTitle {title} {breadcrumb}>
    <div class="">
        <Button 
            size="sm"
            label="Cancel"
            color="gray"
            href="/issue/{issue.id}?tab={testCase.getId()}"
            isLink={true}
        />
    </div>
</PageTitle>

<Container>
    <Card title="Submit Test Result">
        <Wrapper wrapperClass="sm:pt-0">

            <Form
                {model}
                {token}
                testCase={testCase.attributes}
            />

        </Wrapper>
    </Card>
</Container>