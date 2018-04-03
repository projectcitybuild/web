import axios, { AxiosRequestConfig, AxiosPromise } from 'axios';

interface ApiTopicList {
    topic_list: {
        topics: Array<ApiTopic>,
    },
}

export interface ApiTopic {
    id: number,
    title: string,
    slug: string,
    closed: boolean,
    like_count: number,
    posts_count: number,
    views: number,
    created_at: string,
    details?: ApiTopicDetail,
}

interface ApiTopicPoster {
    id: number,
    username: string,
    avatar_template: string,
}

export interface ApiTopicDetail {
    id: number,
    user_id: number,
    details: {
        created_by: ApiTopicPoster,
    },
    post_stream: {
        posts: Array<ApiPost>,
    },
}

export interface ApiPost {
    cooked: string,
    avatar_template: string,
}

/** 
 * Fetches a list of the most recent announcement
 * category topics
*/
export const getAnnouncements = async() : Promise<ApiTopicList> => {
    try {
        const response = await axios.get('http://forums.projectcitybuild.com/c/announcements-news/l/latest.json?_=' + Date.now());
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
export const getTopic = async(topicId: number) : Promise<ApiTopicDetail> => {
    try {
        let response = await axios.get(`http://forums.projectcitybuild.com/t/${topicId}.json`);
        if(response.status !== 200) {
            throw new Error(`${response.status} error while fetching announcements`);
        }
        return response.data;
    } catch(e) {
        console.error(e);
    }
}