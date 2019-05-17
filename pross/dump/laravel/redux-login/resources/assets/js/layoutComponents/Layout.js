import React, {Component} from 'react'


import Header from './Header/Header'
import ModalConductor from '../sharedComponents/ModalConductor'

class Layout extends Component{
	render() {
		return (
			<div>
				<Header />
				<div className="content">
					{ this.props.children }
				</div>
				<ModalConductor />
			</div>
		)
	}
}

export default Layout
