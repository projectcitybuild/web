import * as React from 'react';
import * as Api from './api';
import * as Moment from 'moment';
import { default as Announcement } from './announcement';

interface InitialState {
    announcements: Array<Api.ApiTopic>,
    randomSeed: number,
    seeds: Array<number>,
};

export default class Component extends React.Component<{}, InitialState> {
    state: InitialState = {
        announcements: [],
        randomSeed: 6,
        seeds: [],
    };

    async componentDidMount() {
        const response = await Api.getAnnouncements();
        if(!response) {
            return;
        }

        const announcements = response.topic_list.topics.slice(0, 3);

        this.setState({
            announcements: announcements,
        });

        if(
            response.topic_list && 
            response.topic_list.topics && 
            response.topic_list.topics.length > 0
        ) {
            announcements.forEach(async topic => {
                const post = await Api.getTopic(topic.id);
                if(!post) return;

                // state is immutable, so grab the state first, edit it,
                // then re-set it
                const storedTopics = this.state.announcements;
                const storedTopicIndex = this.state.announcements.findIndex(t => t.id === topic.id);
                storedTopics[storedTopicIndex].details = post;

                this.setState({
                    announcements: storedTopics,
                });
            });
        }
    }

       

    render() {
        return this.state.announcements.map((value, index) => {
            if(!this.state.seeds[index]) {
                this.state.seeds[index] = Math.random() * 100;
            }
            const seed = this.state.seeds[index];

            return (
                <Announcement 
                    announcement={value} 
                    seed={seed}
                    />
            );
        });
    }
}