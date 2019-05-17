import React, {Component}  from 'react'
import CSSModules from 'react-css-modules'
import styles from './styles/Home.scss'

class Page2 extends Component {

	render() {
		return (
			<div styleName="Home">
				<div styleName="TextContainer">
                    <h2>Just another page!!</h2>
				</div>
			</div>
		);
	}
}

export default CSSModules(Page2, styles)
