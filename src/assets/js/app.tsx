import { Navigation } from './modules/Navigation';

const navigation = new Navigation();


import * as React from 'react';
import * as ReactDOM from 'react-dom';
import { Announcement } from './react/components';

ReactDOM.render(
    <Announcement />,
    document.getElementById('announcements')
);