import * as React from 'react';
import * as bs from 'react-bootstrap';
import SweetAlert, {SweetAlertProps} from 'react-bootstrap-sweetalert';
import { connect } from 'react-redux';
import {RootState} from '../features/root-reducer';
import { Dispatch} from 'react-redux';
import {actions} from '../features/root-action';
//import swalAction from '../../actions/swal-action';
let SweetAlertStyles = require('react-bootstrap-sweetalert/lib/styles/SweetAlertStyles');
var Parser = require('html-react-parser');
class Swal extends React.Component<SweetAlertProps> {


    shouldComponentUpdate(nextProps: SweetAlertProps){

        if(nextProps.show !== this.props.show) {

            return true;
        }
        else if(nextProps.displayType !== this.props.displayType){

            return true;
        }

        return false;
    }
    onConfirm (){

        const {displayType, onConfirm, onSuccess, onError, close } = this.props;

        if(displayType === 2 && onConfirm ) {

            return  onConfirm;
        }
        else if( displayType === 1 && onSuccess){

            return onSuccess;
        }
        else if( displayType === 3 ){

            return () => null;
        }
        else if( displayType === 4 && onSuccess ){

            return onSuccess;
        }
        else if( displayType === 5 && onError ){

            return onError;
        }
        else {

            return this.props.close;
        }
    }

    confirmBtnBsStyle() {

        const {displayType, danger} = this.props;
        if(!danger && (displayType === 1 || displayType === 4) ){

            return 'success';
        }
        else if( !danger && (displayType === 2 || displayType === 3)) {

            return 'info';
        }
        else {

            return 'danger';
        }
        
    }
    
    render() {
       

        const {show, displayType, confirmBtnText,
            cancelBtnText,
            cancelBtnBsStyle,
            title,
            onCancel,
            message,
            close,
            danger,
            customClass,
        } = this.props;
        return (
            
            <SweetAlert 
                    show={show}
                    customClass={customClass}
                    success={displayType ===4 ? true: false}
                    danger={danger ? danger : displayType ===5 ? true: false}
                    info={!danger && [1, 2,3].indexOf(displayType) !== -1 ? true: false}
                    showConfirm={displayType ===3 ? false : true}
                    showCancel={displayType === 2 ? true : false}
                    confirmBtnText={confirmBtnText ? confirmBtnText : 'ok'}
                    cancelBtnText={cancelBtnText ? cancelBtnText : 'Cancel'}
                    confirmBtnBsStyle={this.confirmBtnBsStyle()}
                    cancelBtnBsStyle={cancelBtnBsStyle? cancelBtnBsStyle : 'default'}
                    title={title ? title :''}
                    onConfirm={this.onConfirm()}
                    closeOnClickOutside={displayType ===3 ? false : false}
                    style={SweetAlertStyles}
                    onCancel={onCancel ? onCancel : close}
                >
                {Parser(message)}
            </SweetAlert>
        );
    }
}

function mapStateToProps(state: RootState) {

	return {...state.swal};
}
const mapDispatchToProps = (dispatch: Dispatch<{}>) => {

    return {
        close: () => dispatch(actions.swal.close())
    }
}
/** 
 * show: true, false,
 * title: 
 * displayType: {1 => 'Just message', 2 => confirmation, 3 => wait, 4 => success, 5 => error },
 * message: 'message'
 * onSuccess: func,
 * onConfirm: func,
 * onCancel: func,
 * onError: func
 */
export default connect(mapStateToProps, mapDispatchToProps)(Swal);