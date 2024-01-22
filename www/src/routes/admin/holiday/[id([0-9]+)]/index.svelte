<script context="module">
    import Model from '@app/models/Holiday';
    import * as Api from '@app/api/holiday';
    import { loginIsValid } from '@app/accessControl';

    export async function preload({ params }, { token }) {
        const login = await loginIsValid(token);
        if (!login) {
            return this.redirect(302, '/user/logout');
        }
        try {
            const model = new Model({ token });
            const result = await model.find(params.id, { expand: 'country' });
            if (!result) {
                return this.error(500, '🙀 Oh no. Something wrong?!');
            }
            return { model }
        } catch (error) {
            this.error(500, error.name + ': ' + error.message);
        }
    }
</script>

<script>
    import Container from '../../../../components/Container.svelte';
    import Wrapper from '../../../../components/Wrapper.svelte';
    import Card from '../../../../components/Card.svelte';
    import Button from '../../../../components/Button.svelte';
    import DetailedRow from '../../../../components/DetailedRow.svelte';
    import Status from '../../../../components/Status.svelte';
    import PageTitle from '../../../../components/PageTitle.svelte';
    
    export let model;
    const title = `Holiday: ${model.get('title')}`;
</script>

<svelte:head>
    <title>{title}</title>
</svelte:head>

<PageTitle title="Admin: {title}">
</PageTitle>

<Container>
    <Card title={model.get('title')}>
        <Wrapper wrapperClass="sm:pt-0">

            <DetailedRow>
                <div slot="label">
                    {model.getLabel('title')}
                </div>
                <div slot="content">
                    {model.get('title')}
                </div>
            </DetailedRow>

            <DetailedRow>
                <div slot="label">
                    {model.getLabel('country_id')}
                </div>
                <div slot="content">
                    {model.getCountry().name}
                </div>
            </DetailedRow>

            <DetailedRow>
                <div slot="label">
                    {model.getLabel('start')}
                </div>
                <div slot="content">
                    {model.get('start')}
                </div>
            </DetailedRow>

            <DetailedRow>
                <div slot="label">
                    {model.getLabel('end')}
                </div>
                <div slot="content">
                    {model.get('end')}
                </div>
            </DetailedRow> 

            <DetailedRow>
                <div slot="label">
                    {model.getLabel('status')}
                </div>
                <div slot="content">                    
                    <Status status={model.get('status')} />
                </div>
            </DetailedRow>        

            <div class="">
                <Button 
                    size="sm"
                    label="Update"
                    href="/admin/holiday/{model.getId()}/update"
                    isLink={true}
                />
            </div>

        </Wrapper>
    </Card>
</Container>