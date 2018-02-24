import * as React from 'react';
import * as Api from './api';
import * as Moment from 'moment';

interface InitialState {
    announcements: Array<Api.ApiTopic>,
};

export default class Component extends React.Component<{}, InitialState> {
    state: InitialState = {
        announcements: [],
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

    renderAnnouncement(announcement: Api.ApiTopic) : React.ReactNode {
        // why do you do this to me, javascript...
        const username = (
            announcement.details && 
            announcement.details.details && 
            announcement.details.details.created_by &&
            announcement.details.details.created_by.username
        ) || "";

        const post: Api.ApiPost = 
            announcement.details &&
            announcement.details.post_stream &&
            announcement.details.post_stream.posts
                ? announcement.details.post_stream.posts[0]
                : { 
                    cooked: "", 
                };

        const date = Moment(announcement.created_at);

        return (
            <article className="article card" key={announcement.id}>
                <div className="article__container">
                    <h2 className="article__heading">{ announcement.title }</h2>
                    <div className="article__date">{ date.format('ddd, Do \of MMMM, YYYY') }</div>

                    <div className="article__body">
                        { post.cooked }
                    </div>

                    <div className="article__author">
                        Posted by
                        <img src={`https://minotar.net/helm/${username}/16`} width="16" alt={username} />
                        <a href="#">{ username }</a>
                    </div>
                </div>
                <div className="article__footer">
                    <div className="stats-container">
                        <div className="stat">
                            <span className="stat__figure">{ announcement.posts_count}</span>
                            <span className="stat__heading">Comments</span>
                        </div>
                        <div className="stat">
                            <span className="stat__figure">{ announcement.views }</span>
                            <span className="stat__heading">Post Views</span>
                        </div>
                    </div>
                    <div className="actions">
                        <a className="button button--accent button--large" href={`http://forums.projectcitybuild.com/t/${announcement.slug}/${announcement.id}`}>
                            Read Post
                            <i className="fa fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </article>
        );
    }

    render() {
        if(!this.state.announcements) {
            return <div></div>;
        }
        return this.state.announcements.map(a => this.renderAnnouncement(a));
    }
}