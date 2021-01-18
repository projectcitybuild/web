import axios from 'axios';

interface DiscourseTopicList {
    topic_list: {
        topics: Array<DiscourseTopic>,
    },
}

export interface DiscourseTopic {
    id: number,
    title: string,
    slug: string,
    closed: boolean,
    like_count: number,
    posts_count: number,
    views: number,
    created_at: string,
}

interface DiscourseUser {
    id: number,
    username: string,
    avatar_template: string,
}

export type DiscourseTopicDetails = DiscourseTopic & {
    user_id: number,
    details: {
        created_by: DiscourseUser,
    },
    post_stream: {
        posts: Array<DiscoursePost>,
    },
}

export interface DiscoursePost {
    cooked: string,
    avatar_template: string,
}

/**
 * Fetches a list of the most recent announcement
 * category topics
 */
export const getAnnouncements = async() : Promise<DiscourseTopicList> => {
    try {
        const response = await axios.get('https://forums.projectcitybuild.com/c/announcements/l/latest.json?order=created');
        if(response.status !== 200) {
            throw new Error(`${response.status} error while fetching announcements`);
        }
        return response.data;
    } catch(e) {
        console.error(e);
        throw new Error(e);
    }
}

/**
 * Fetches data for a single topic.
 *
 * Required because discourse doesn't give the topic's
 * content when querying for a list of topics
 *
 * @param topicId
 */
export const getTopicDetails = async(topicId: number) : Promise<DiscourseTopicDetails> => {
    try {
        let response = await axios.get(`https://forums.projectcitybuild.com/t/${topicId}.json`);
        if(response.status !== 200) {
            throw new Error(`${response.status} error while fetching announcements`);
        }
        return response.data;
    } catch(e) {
        console.error(e);
    }
}
