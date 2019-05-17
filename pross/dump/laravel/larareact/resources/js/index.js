const config = require('./config/manish.js');
import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter as Router, Link, Route, Switch } from 'react-router-dom';
import Blog from './components/blogs/Blog.js';
import BlogArticle from './components/blogs/BlogArticle.js';
import Example from './components/items/Example.js';
//import Master from './components/Master';
import CreateItem from './components/items/CreateItem.js';
import DisplayItem from './components/items/DisplayItem.js';
import EditItem from './components/EditItem';

export default class Index extends Component {
    render() {
        return (
            <div className="container">
                <Router>
                    <div>
                        <nav className="navbar navbar-default">
                            <div className="container-fluid">
                                <div className="navbar-header">
                                    <a className="navbar-brand" href={config.url.path}>Home</a>
                                </div>
                                <ul className="nav navbar-nav">
                                    <li><Link to={config.url.path}>Home</Link></li>
                                    <li><Link to={config.url.path+"blog"}>Blogs</Link></li>
                                    <li><Link to={config.url.path+"items"}>Items</Link></li>
                                </ul>
                            </div>
                        </nav>

                        <Route path={config.url.path} exact component={Example}></Route>
                        <Route path={config.url.path+'blog'} exact component={Blog}></Route>
                        <Route path={config.url.path+'blog/:id'} exact render={props=> <BlogArticle{...props} />}></Route>
                        <Route path={config.url.path+'items'} exact component={DisplayItem}></Route>
                        <Route path={config.url.path+"add-item"} exact component={CreateItem} />
                        <Route path={config.url.path+"edit/:id"} exact component={EditItem} />
                    </div>
                </Router>
            </div>
        );
    }
}

if (document.getElementById('example')) {
    ReactDOM.render(<Index />, document.getElementById('example'));
}
