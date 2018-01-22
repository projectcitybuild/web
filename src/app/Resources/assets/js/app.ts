import Navigation from "./components/Navigation";

const navigation = new Navigation();


import * as React from 'react';
import * as ReactDOM from 'react-dom';
import { BanList } from './react/components/BanList';

ReactDOM.render(<BanList />, document.getElementById('banlist'));