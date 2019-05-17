import * as React from 'react';
import { Alert } from 'react-bootstrap';
const Image = require('../../scss/assets/images/loader.gif');

interface LoaderProps {
    show: boolean;
}

class Loader extends React.Component<LoaderProps> {

    shouldComponentUpdate(nextProp: LoaderProps) {

        if(nextProp.show !== this.props.show){
            return true;
        }
        
        return false;
    }
    render() {
        
        return <img src={Image} className="loadingImage" />
    }
}
export default Loader;