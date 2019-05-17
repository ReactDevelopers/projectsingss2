import React, { Component } from 'react';
import CSSModules from 'react-css-modules';
import PropTypes from 'prop-types';

import styles from './styles/checkbox.scss';

class Checkbox extends Component {
    constructor (props) {
		super();
		this.state = {
            checked: props.checked
        }
	};


    draw(e) {
        let el = e.target;
        let parent = el.parentNode
        let svg = parent.querySelector( 'svg' );
        let paths = [];
        const animDef = { speed : .2, easing : 'ease-in-out' };
        const checkmark = 'M16.667,62.167c3.109,5.55,7.217,10.591,10.926,15.75 c2.614,3.636,5.149,7.519,8.161,10.853c-0.046-0.051,1.959,2.414,2.692,2.343c0.895-0.088,6.958-8.511,6.014-7.3 c5.997-7.695,11.68-15.463,16.931-23.696c6.393-10.025,12.235-20.373,18.104-30.707C82.004,24.988,84.802,20.601,87,16';

        let path = document.createElementNS('http://www.w3.org/2000/svg', 'path' )
        svg.appendChild( path );
        path.setAttributeNS( null, 'd', checkmark );

        const length = path.getTotalLength();
        // Clear any previous transition
        //path.style.transition = path.style.WebkitTransition = path.style.MozTransition = 'none';
        // Set up the starting positions
        path.style.strokeDasharray = length + ' ' + length;
        path.style.strokeDashoffset = Math.floor( length ) - 1;

        // Trigger a layout so styles are calculated & the browser
        // picks up the starting position before animating
        path.getBoundingClientRect();
        // Define our transition
        path.style.transition = path.style.WebkitTransition = path.style.MozTransition  = 'stroke-dashoffset ' + animDef.speed + 's ' + animDef.easing + ' ' + 0 * animDef.speed + 's';
        // Go!
        path.style.strokeDashoffset = '0'; 
    }

    reset(e) {
        const el = e.target;
        const parent = el.parentNode;
        const path = parent.querySelector('svg > path');
        path.parentNode.removeChild(path);
    }

    handleClick(e) {
        if (this.state.checked) {
            this.reset(e)
        } else {
            this.draw(e)
        }
        this.setState({checked: !this.state.checked});
        
    }

    render() {
        return (
            <div styleName="chkbxContainer">
                <input 
                    id={this.props.id}
                    name={this.props.name}
                    type="checkbox"
                    onClick={this.handleClick.bind(this)} 
                    styleName="checkbox" />

                <label htmlFor={this.props.id} styleName="label">{this.props.label}</label>
                <svg styleName="svg" viewBox="0 0 100 100"></svg>
            </div>
        )
    }
}

Checkbox.PropTypes = {
    checked: PropTypes.bool,
    label: PropTypes.string
}

Checkbox.defaultProps = {
    checked: false
}


export default CSSModules(Checkbox, styles);