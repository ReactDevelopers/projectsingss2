import * as React from 'react';
import {Props, mapDispatchToProps, mapStateToProps} from '../../features/root-props';
import Template from '../layout/Template';
import  {connect } from 'react-redux';
import Auth, {AuthI} from '../../models/Auth';
import AdminNav from './AdminNav';
import ViewerNav from './ViewerNav';
class Dashboard extends React.Component <Props> {

    render() {
        const server_auth_data =  this.props.rootState.server['auth_user'];
        const auth_user: AuthI = server_auth_data && server_auth_data.response && server_auth_data.response.data ? server_auth_data.response.data : {};
        const {name, personnel_number, role_id, view_as} = auth_user;
        console.log('dddddddddddddddddddddddddddfffffffffffff');
        console.log(view_as);
        return (
            <Template {...this.props} >
                { view_as === 'Admin' ? <AdminNav {...this.props}/> : <ViewerNav {...this.props}/> }
            </Template>
        )
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(Dashboard)