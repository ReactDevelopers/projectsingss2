import * as React from 'react';
import {Props,  mapDispatchToProps, mapStateToProps} from '../../features/root-props';
import Loading from 'react-loading-bar';
import  {connect } from 'react-redux';
import moment from 'moment'

class LoaderBar extends React.Component <Props> {

    shouldComponentUpdate(nextProps: Props) {
        
        const show = this.props.rootState.loaderBar.show;
        const nextShow = nextProps.rootState.loaderBar.show;
        
        if(show !== nextShow) {

            return true;
        }
        return false;
    }

    // componentWillReceiveProps(nextProps: Props) {
    //     console.log('Next Props');
    //     console.log(moment().toLocaleString());
    //     console.log(nextProps.rootState.loaderBar);
    //     console.log(this.props.rootState.loaderBar);
    // }

    render () {
    
        const {show} = this.props.rootState.loaderBar;
        return (
            <>
              {/* {show ? <div className="load-bar">
  <div className="bar"></div>
  <div className="bar"></div>
  <div className="bar"></div>
</div>: ''} */}
              
            <Loading showSpinner={true} show={show} change={true} color="blue" />   
            </>
        )
    }
}
export default connect(mapStateToProps, mapDispatchToProps)(LoaderBar)