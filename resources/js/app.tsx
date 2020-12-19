import { Navigation } from './modules/Navigation';

const navigation = new Navigation();


import * as React from 'react';
import * as ReactDOM from 'react-dom';
import { AnnouncementManager } from './react/components';

let announcementContainer = document.getElementById('announcements');
if (announcementContainer) {
    ReactDOM.render(
        <AnnouncementManager />,
        announcementContainer
    );
}