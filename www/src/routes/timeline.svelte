<script context="module">
    import { loginIsValid } from '@app/accessControl';
    
    let resources;
    let events;
    let holidays;
    let countries;

    export async function preload({ params }, { token }) {
        const login = await loginIsValid(token);
        if (!login) {
            return this.redirect(302, '/user/logout');
        }
    }
</script>

<script>
    import Card from '../components/Card.svelte';
	import Container from '../components/Container.svelte';
    import PageTitle from '../components/PageTitle.svelte';
    import Wrapper from '../components/Wrapper.svelte';
    import Modal from '../components/Modal.svelte';
    import TimelineModel from '@app/models/Timeline';
    import ActiveTextInput from '../components/input/ActiveText.svelte';
    import Button from '../components/Button.svelte';
    import * as alert from '../components/alert/alert.js';
    import * as timelineApi from '@app/api/timeline';
    import moment from 'moment';
    import { onMount } from 'svelte';
    import { stores } from '@sapper/app';
    import { onDestroy } from 'svelte';

    import '@fullcalendar/core/main.css';
    import '@fullcalendar/timeline/main.css';
    import '@fullcalendar/resource-timeline/main.css';

    const { session } = stores();
    const { token } = $session;

    const eventColor = '#90CDF4';
    const holidayColor = '#81E6D9';
    const timelineEventSourceId = 'timeline-event';
    const holidayEventSourceId = 'timeline-holiday';
    const timelineRefreshInterval = 10000;
    const resourceRefreshInterval = 30000;
    let calendar;
    let isLoading = false;
    let deleteIsLoading = false;
    let model = new TimelineModel({ token });
    let showModal = false;

    const breadcrumb = [
        { label: 'Home', href: '/' },
        { label: 'Timeline', href: '/timeline' },
    ];

    onMount(async () => {
        const { Calendar } = await import('@fullcalendar/core');
        const { default: interactionPlugin } = await import('@fullcalendar/interaction');
        const { default: resourceTimelinePlugin } = await import('@fullcalendar/resource-timeline');
        
        const calendarEl = document.getElementById('calendar');
		calendar = new Calendar(calendarEl, {
            // themeSystem: 'standard',
            height: 'auto',
            contentHeight: 'auto',
            schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
            defaultView: 'resourceTimeline',
            plugins: [ resourceTimelinePlugin, interactionPlugin ],
            header: false,
            editable: true, 
            selectable: true, 
            resourceEditable: true,
            eventResourceEditable: true,
            droppable: true,
            allDayDefault: true,
            weekNumberCalculation: 'ISO',
            firstDay: 1,
            validRange: {
                start: moment().subtract(6, 'M').format('YYYY-MM-DD'),
                end: moment().add(6, 'M').format('YYYY-MM-DD'),
            },
            duration: { weeks: 2 },
            dateIncrement: { weeks:1 },
            slotDuration: { day: 1 },
            slotLabelFormat: [
                { month: 'long', year: 'numeric' }, // top level of text
                { day: 'numeric', weekday: 'short' } // lower level of text
            ],
            resourceAreaWidth: '15%',
            resourceGroupField: 'type',
            resourceColumns: [
                {
                    labelText: '',
                    field: 'title'
                },
            ],
            resources: async () => {
                let resources = [];
                let countries = [];

                const getResource = await timelineApi.resource(token);
                if (getResource.success) {
                    resources = getResource.data;
                }
                const getCountry = await timelineApi.country(token);
                if (getCountry.success) {
                    countries = getCountry.data;
                    countries.forEach(r => {
                        r.eventAllow = () => false;
                        r.dropAccept = () => false;
                        r.selectable = false;
                        r.resourceEditable = false;
                    });
                }
                return [ ...countries, ...resources ];
            },
            eventSources: [
                {
                    id: timelineEventSourceId,
                    backgroundColor: eventColor,
                    borderColor: eventColor,
                    events: async () => {
                        let data = [];
                        const response = await timelineApi.list(token);
                        if (response.success) {
                            data = response.data;
                        }
                        return data;
                    },
                },
                {
                    id: holidayEventSourceId,
                    backgroundColor: holidayColor,
                    borderColor: holidayColor,
                    editable: false,
                    events: async () => {
                        let data = [];
                        const response = await timelineApi.holiday(token);
                        if (response.success) {
                            data = response.data;
                        }
                        return data;
                    },
                }
            ],
            // to create new event
            select: e => { // select date range
                let start = e.startStr;
                let end = e.endStr;
                let resourceId = e.resource.id;
                if (isNaN(resourceId)) {
                    return calendar.unselect();
                }
                setModel(resourceId, null, start, end);
                showModal = true;
            },

            // to update event
            eventClick: e => { // click on existing event
                let eventId = parseInt(e.event.id, 10);
                let start = moment(e.event.start).format('YYYY-MM-DD');
                let end = e.event.end ? moment(e.event.end).format('YYYY-MM-DD') : start;
                let title = e.event.title;
                let resourceId = e.event.getResources()[0].id;
                if (isNaN(resourceId)) {
                    return calendar.unselect();
                } else {
                    resourceId = parseInt(resourceId, 10);
                }
                setModel(resourceId, title, start, end, eventId);
                showModal = true;
            },

            // slience update
            eventDrop: e => { // drag and drop 
                let eventId = e.event.id;
                let start = moment(e.event.start).format('YYYY-MM-DD');
                let end = e.event.end ? moment(e.event.end).format('YYYY-MM-DD') : start;
                let fromResourceId = e.oldResource ? e.oldResource.id : e.event.getResources()[0].id;
                let toResourceId = e.newResource ? e.newResource.id : fromResourceId;
                if (!updateModel(eventId, toResourceId, start, end)) {
                    e.revert();
                    alert.danger(`❗️Unable to update. Some server error.`);
                }
            },
            eventResize: e => { // resize and change duration
                let eventId = e.event.id;
                let start = moment(e.event.start).format('YYYY-MM-DD');
                let end = e.event.end ? moment(e.event.end).format('YYYY-MM-DD') : start;
                let resourceId = e.event.getResources()[0].id;
                if (!updateModel(eventId, resourceId, start, end)) {
                    e.revert();
                    alert.danger(`❗️Unable to update. Some server error.`);
                }
            },
            
        });
        calendar.render();
    });
    
    function updateModel(id, userId, start, end) {
        model.set('id', id);
        model.set('user_id', userId);
        const event = calendar.getEventById(model.getId());
        model.set('title', event.title);
        model.set('start', start);
        model.set('end', end);
        if (model.save()) {
            model.reset();
            return true;
        }
        model.reset();
        return false;
    }

    function setModel(userId, title, start, end, id = null) {
        model.set('id', id);
        model.set('user_id', userId);
        model.set('title', title);
        model.set('start', start);
        model.set('end', end);
        model = model;
    }    

    function handleDelete() {
        deleteIsLoading = true;
        const event = calendar.getEventById(model.getId());
        if (model.delete()) {
            event.remove();
        } else {
            alert.danger(`❗️Unable to delete. Some server error.`);
        }
        deleteIsLoading = false;
        showModal = false;
        model.reset();
    }

    function handleCancel() {
        model.reset();
        model = model;
        showModal = false;
    }

    async function handleSubmit() {
        if (model.validateForm() === false) {
            model = model;
            return false;
        }
        isLoading = true;
        const isNewRecord = model.isNewRecord();
        let action = isNewRecord ? 'created' : 'updated';
        if (await model.save() === true) {
            if (isNewRecord) {
                calendar.addEvent({
                    id: model.getId(),
                    resourceId: model.get('user_id'),
                    start: model.get('start'),
                    end: model.get('end'),
                    title: model.get('title'),
                    backgroundColor: eventColor,
                    borderColor: eventColor,
                });
            } else {
                const event = calendar.getEventById(model.getId());
                if (event) {
                    event.setDates(model.get('start'), model.get('end'), { allDay: true });
                    event.setProp('title', model.get('title'));
                }
            }            
            showModal = false;
            model.reset();
        } else if (!model.hasErrors()) {
            alert.danger(`❗️Unable to ${action}. Some server error.`);
        }
        isLoading = false; 
        model = model;
        return false;
    }

    onInterval(async () => {
        calendar.getEventSourceById(timelineEventSourceId).refetch();
    }, timelineRefreshInterval);

    onInterval(async () => {
        calendar.refetchResources();
    }, resourceRefreshInterval);

    function onInterval(callback, milliseconds) {
        const interval = setInterval(callback, milliseconds);
        onDestroy(() => {
            clearInterval(interval);
        });
    }
</script>

<svelte:head>
	<title>Timetable</title>
    <link rel="stylesheet" href="css/timeline.css">
</svelte:head>

<PageTitle title="Timeline" {breadcrumb}>
</PageTitle>

<Modal
    id="form-modal"
    title="Add Record"
    bind:visible={showModal}
>
    <form on:submit|preventDefault={handleSubmit}>
        <div>
            <ActiveTextInput 
                containerClass="mb-6"
                {model}
                attribute="title"
                placeholder=""
            />
            <ActiveTextInput 
                containerClass="mb-6"
                {model}
                attribute="start"
                placeholder="YYYY-MM-DD"
            />
            <ActiveTextInput 
                containerClass="mb-6"
                {model}
                attribute="end"
                placeholder="YYYY-MM-DD"
            />
        </div>
        <div class="flex justify-between">
            <div class="flex justify-start">
                <Button 
                    label="{model.isNewRecord() ? 'Save' : 'Update'}" 
                    type="submit" 
                    color="{model.isNewRecord() ? 'green' : 'blue'}"
                    isLoading={isLoading} 
                    disabled={model.hasErrors()}
                />

                <Button 
                    on:click={handleCancel}
                    buttonClass="ml-2"
                    label="Cancel" 
                    type="button"
                    color="gray"
                />
            </div>
            <div class="flex justify-end">
                {#if !model.isNewRecord()}
                    <Button 
                        on:click={handleDelete}
                        isLoading={deleteIsLoading}
                        buttonClass="ml-2"
                        type="button"
                        color="red"
                        icon="fas fa-trash-alt"
                    />
                {/if}
            </div>
        </div>
    </form>
</Modal>

<Container>
    <Card>
        <Wrapper>
            <div class="flex justify-between mb-6">
                <div class="flex justify-start items-start">
                    
                </div>
                <div class="flex items-stretch justify-end">
                    <button 
                        class="rounded bg-gray-700 hover:bg-gray-800 px-6 h-10 focus:outline-none font-semibold mr-1 text-white"
                        on:click={() => { calendar.today() }}
                    >
                        Today
                    </button>
                    <button 
                        class="rounded-tl rounded-bl bg-gray-700 hover:bg-gray-800 h-10 w-10 focus:outline-none flex justify-center"
                        on:click={() => { calendar.incrementDate({ weeks: -1 }) }}
                    >
                        <svg class="text-white h-6 w-6 transform rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M9 18l6-6-6-6"/>
                        </svg>
                    </button>
                    <button 
                        class="rounded-tr rounded-br bg-gray-700 hover:bg-gray-800 h-10 w-10 focus:outline-none flex justify-center"
                        on:click={() => { calendar.next() }}
                    >
                        <svg class="text-white h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M9 18l6-6-6-6"/>
                        </svg>
                    </button>
                </div>            
            </div>
            <div id="calendar"></div>
        </Wrapper>
    </Card>
</Container>
