import React, { Component } from 'react'
import CSSModules from 'react-css-modules'
import styles from './Header.scss'


import Navigation from './Navigation/Navigation'

class Header extends Component {
    render() {
		return (
			<header styleName="header">
				<section styleName="HeaderBar">
					<Navigation />
				</section>
			</header>
		)
    }
}

export default CSSModules(Header, styles)
