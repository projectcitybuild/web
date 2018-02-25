import * as React from 'react';
import * as Api from './api';
import * as Moment from 'moment';
import { default as Announcement } from './announcement';

interface InitialState {
    announcements: Array<Api.ApiTopic>,
    randomSeed: number,
    seeds: Array<number>,
    error: string,
};

export default class Component extends React.Component<{}, InitialState> {
    state: InitialState = {
        announcements: [],
        randomSeed: 6,
        seeds: [],
        error: null,
    };

    async componentDidMount() {
        let hasError = false;
        let response;
        try {
            response = await Api.getAnnouncements();

            if(!response) {
                throw new Error('Empty response from server');
            }
        } catch(e) {
            const error: Error = e;
            this.setState({ error: error.message });
            hasError = true;
        }

        if(hasError) {
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

       

    renderError(error: string) : React.ReactNode {
        return (
            <div className="alert alert--warning">
                <h3 className="alert__header"><i className="fas fa-exclamation-circle"></i> Failed to fetch announcements</h3>
                <p className="alert__message">{ error }</p>
            </div>
        )
    }

    render() {
        if(this.state.error) {
            return this.renderError(this.state.error);
        }

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