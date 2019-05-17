import React, { Component } from 'react'
import CSSModules from 'react-css-modules'
import styles from './Header.scss'


import Navigation from './Navigation/Navigation'

class Header extends Component {
    render() {
		return (
			<header styleName="header">
				<section styleName="HeaderBar">
					<h1>Laravel React starter application</h1>
					<Navigation />
				</section>
			</header>
		)
    }
}

export default CSSModules(Header, styles)
