import * as React from 'react';
import {Props, mapDispatchToProps, mapStateToProps} from '../../features/root-props';
import Template from '../layout/Template';
import  {connect } from 'react-redux';
import * as bs from 'react-bootstrap';
import { NavLink } from 'react-router-dom';
var FAS = require('react-fontawesome');
import { ReactElement } from 'react';

interface BreadcrumbProps extends Props {

    RightSideButton?: ReactElement<any>;
}
class Breadcrumb extends React.Component <BreadcrumbProps> {

    shouldComponentUpdate(nextProp: Props) {

        if(this.props.rootState.router.location && nextProp.rootState.router.location 
            && this.props.rootState.router.location.pathname !==  nextProp.rootState.router.location.pathname ){
                return true;
        }
        else if (this.props.helper.shouldUpdate( nextProp, this.props, ['auth_user'])){

            return true;
        }
        
        return false;
    }

    render() {
        const {RightSideButton, rootState: {router}} = this.props;
        const pathname = router && router.location ? router.location.pathname : null;
        const auth: {[key: string]: any} = this.props.helper.deepFind(this.props.rootState.server, 'auth_user.response.data',{});
        
        const layoutName = auth && auth.view_as === 'Admin' ? 'Admin Dashboard' : 'Viewer Dashboard';

        return (
            <bs.Grid fluid={true} className="container_breadcrumb">
                <bs.Grid>
                    <bs.Row>
                        <bs.Col sm={10}>
                            <bs.Breadcrumb>
                                <li className={ `breadcrumb-item ${pathname === '/dashboard' ? 'active':''}`}>
                                {pathname === '/dashboard' ? layoutName : <NavLink to="/dashboard" activeClassName="active" >
                                     {layoutName}
                                    </NavLink>}
                                </li> 
                                {this.props.breadcrumb && this.props.breadcrumb.map((v, k) => (
                                    <li key={`LiListItem_${k}`} className={ `breadcrumb-item ${pathname === v.url ? 'active':''}`} >
                                        {pathname !== v.url ? <NavLink to={v.url} activeClassName="active" key={`breadcrumb${k}`} >{v.title}</NavLink> :
                                            v.title }
                                    </li>
                                ))}
                            </bs.Breadcrumb>
                        </bs.Col>

                        {RightSideButton ? <bs.Col sm={2}>{RightSideButton }</bs.Col> : null}
                        
                    </bs.Row >
                </bs.Grid>
            </bs.Grid>
        )       
                            
    }
}
export default Breadcrumb;