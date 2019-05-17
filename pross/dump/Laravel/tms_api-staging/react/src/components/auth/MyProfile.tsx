import * as React from 'react';
import {Props, mapDispatchToProps, mapStateToProps, ServerResponse} from '../../features/root-props';
import Template from '../layout/Template';
import API from '../../aep';
import  {connect } from 'react-redux';
import {actions as RootActions} from '../../features/root-action';

class MyPorfile extends React.Component<Props> {
    

    constructor(props: Props) {
        
        super(props);
    }

    shouldComponentUpdate(nextProps: Props) {

        return false;
    }

    render() {

        const breadcrumbs = [
            {title: 'My Profile', url: '/my-profile'},  
        ];

        const data: {[key:string]: any} =  this.props.helper.deepFind(this.props.rootState.server,'auth_user.response.data',{});

        return(
            <Template {...this.props} breadcrumb={breadcrumbs} >
            
            <div className="light-blue-border border-12 white-bg p-20">
            		<div className="lineframe-inner">
                        <table className="table table-bordered">
                            <tbody>
                                <tr> <td>Name:</td> <td>{data.name}</td></tr>
                                <tr> <td>Email:</td> <td>{data.email}</td></tr>
                                <tr> <td>Personnel Number:</td> <td>{data.personnel_number}</td></tr>
                                <tr> <td>Division:</td> <td>{data.division}</td></tr>
                                <tr> <td>Designation:</td> <td>{data.designation}</td></tr>
                            </tbody>
                        </table>
            		</div>
                </div>
            </Template>
        );
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(MyPorfile)