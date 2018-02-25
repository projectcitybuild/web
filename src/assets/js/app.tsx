import { Navigation } from './modules/Navigation';

const navigation = new Navigation();


import * as React from 'react';
import * as ReactDOM from 'react-dom';
import { AnnouncementManager } from './react/components';

ReactDOM.render(
    <AnnouncementManager />,
    document.getElementById('announcements')
);