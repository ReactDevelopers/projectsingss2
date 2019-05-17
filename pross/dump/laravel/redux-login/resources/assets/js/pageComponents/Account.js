import React, {Component}  from 'react'
import CSSModules from 'react-css-modules'
import styles from './styles/Home.scss'


const Account = () => {
    return (
        <div styleName="Home">
            <div styleName="TextContainer">
                <h2>This is the account homepage and is a protected route</h2>
                <h3>Try navigating to here without being signed in - You'll get shown the login form</h3>
            </div>
        </div>
    );
}

export default CSSModules(Account, styles)
