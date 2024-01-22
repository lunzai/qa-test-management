<script context="module">
    import Model from '@app/models/Country';
    import * as Api from '@app/api/country';
    import { loginIsValid } from '@app/accessControl';

    export async function preload({ params }, { token }) {
        const login = await loginIsValid(token);
        if (!login) {
            return this.redirect(302, '/user/logout');
        }
        try {
            const model = new Model({ token });
            const result = await model.find(params.id);
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
    import PageTitle from '../../../../components/PageTitle.svelte';

    export let model;
    const title = `Country: ${model.get('name')}`;
</script>

<svelte:head>
    <title>{title}</title>
</svelte:head>

<PageTitle title="Admin: {title}">
</PageTitle>

<Container>
    <Card title={model.get('name')}>
        <Wrapper wrapperClass="sm:pt-0">

            <DetailedRow>
                <div slot="label">
                    {model.getLabel('name')}
                </div>
                <div slot="content">
                    {model.get('name')}
                </div>
            </DetailedRow>

            <DetailedRow>
                <div slot="label">
                    {model.getLabel('iso2')}
                </div>
                <div slot="content">
                    {model.get('iso2')}
                </div>
            </DetailedRow>

            <DetailedRow>
                <div slot="label">
                    {model.getLabel('iso3')}
                </div>
                <div slot="content">
                    {model.get('iso3')}
                </div>
            </DetailedRow>           

            <div class="">
                <Button 
                    size="sm"
                    label="Update"
                    href="/admin/country/{model.getId()}/update"
                    isLink={true}
                />
            </div>

        </Wrapper>
    </Card>
</Container>