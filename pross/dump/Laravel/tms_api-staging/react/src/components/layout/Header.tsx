import * as React from 'react';
import {Props} from '../../features/root-props';
import * as bs from 'react-bootstrap';
import Auth, {AuthI} from '../../models/Auth';
import {actions} from '../../features/root-action';
import API from '../../aep';
import { NavLink, Link } from 'react-router-dom';
var logo =  require('../../scss/assets/images/logo_tms.png');

export default class Header extends React.Component<Props> {
    
    constructor(props: Props) {
        super(props);
        this.logOut =  this.logOut.bind(this);
        this.switchView  = this.switchView.bind(this);
    }
    /**
     * component will update when the auth user data will change.
     * @param nextProps 
     */
    shouldComponentUpdate(nextProps: Props) {

        if(nextProps.rootState.server['auth_user'].shouldUpdate !== this.props.rootState.server['auth_user'].shouldUpdate) {
            return true;
        }
        return false;        
    }

    logOut() {
        //console.log('.,,,,,,,,,,,,,,,,,');
        this.props.callApi(API.LOGOUT);
    }

    switchView(view_as: 'Admin' | 'Viewer') {
       
        var changeViewAs = view_as === 'Viewer'? 'Admin' : 'Viewer';
        localStorage.setItem('tms_view_as', changeViewAs);
        
        // Change Global State
        var response = this.props.helper.deepFind(this.props.rootState.server,'auth_user.response',{});
        response.data.view_as = changeViewAs;

        this.props.dispatch(actions.fetch.receivedApiResponse(API.AUTH_USER, response));

        this.props.history.push('/dashboard');
    }

    render () {

        const server_auth_data =  this.props.rootState.server['auth_user'];
        const auth_user: AuthI = server_auth_data && server_auth_data.response && server_auth_data.response.data ? server_auth_data.response.data : {};
        const {name, personnel_number, role_id, view_as} = auth_user;
     

        const  useuPullDownMenu = {
            bsStyle: 'default',
            noCaret: true,
            title: name ? name: '..',
            id: 'header_pull_downmenu'
        };

        return (
        
        <bs.Grid fluid={true} className="header_wrap">
            <bs.Grid>
            <bs.Row>
                <bs.Col sm={7}><h3 className="header_title left">
                <Link to="/">
                    <img src={logo} alt="Training MANAGEMENT SYSTEM" height={36} title="Training MANAGEMENT SYSTEM" />
                </Link>
                </h3></bs.Col>
                <bs.Col sm={5} className="account-info">
                    <div className="user-options">
                        <div className="admin_name">
                            <span>
                              {name} ({personnel_number})                          
                            </span>
                        </div>
                        <bs.DropdownButton {...useuPullDownMenu} >
                            <bs.MenuItem eventKey="1"
                             onClick={(e: any) => {this.props.history.push('/my-profile')}}
                            >                               
                            <span className="icon-user assistant-icon"></span>My Profile
                             
                            </bs.MenuItem>
                            { (role_id === 1 || role_id === 3) && view_as ?
                                
                                <bs.MenuItem eventKey="2"  onClick={() => {this.switchView(view_as)}}>
                                    <span className="icon-user logout-icon"></span>{view_as === 'Admin'? 'Switch As Viewer': 'Switch As Admin'}
                                </bs.MenuItem>
                                :null }
                            <bs.MenuItem eventKey="3"  onClick={this.logOut}>
                                <span className="icon-user logout-icon"></span>Log out
                            </bs.MenuItem>
                        </bs.DropdownButton>
                    </div>
                </bs.Col>
            </bs.Row>   
            </bs.Grid>
        </bs.Grid>
        )
    }

}