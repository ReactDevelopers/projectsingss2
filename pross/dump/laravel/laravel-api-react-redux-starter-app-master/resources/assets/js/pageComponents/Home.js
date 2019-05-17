import React, {Component}  from 'react'
import CSSModules from 'react-css-modules'
import styles from './styles/Home.scss'


const Home = () => {
    return (
        <div styleName="Home">
            <div styleName="TextContainer">
                <h2>Laravel / React Starter application</h2>
                <h3>Created by <a href="http://tomkeyte.com" target="_blank">Tom Keyte</a></h3>

                <p>A starter app that can be used as a boilerplate for building laravel api powered apps using the Dingo API Package and JWT tokens provided by TymonDesigns\jwt-auth.</p>
                <p>This demo includes user sign up/sign in, modal components, locally scoped styling using React Css Modules, react component transitions using Css-Transition-Group</p>

                <h4>See the <a href="https://github.com/TomKeyte/laravel-api-react-redux-starter-app" target="_blank">Github Repo</a> for more info.</h4>
            </div>
        </div>
    );
}

export default CSSModules(Home, styles)
