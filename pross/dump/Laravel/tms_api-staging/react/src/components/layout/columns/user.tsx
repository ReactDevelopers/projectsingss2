import { DataAlignType, PaginationPostion } from 'react-bootstrap-table';
import * as React from 'react';
import {
    TableColumnProps, 
    getSelectFilter, 
    getDateFilter, 
    getDateRangeFilter
} 
from '../../../plugins/Table';
import {Props} from '../../../features/root-props';
import { _printTextField, _printSelectOptionsField, _courseDates , _printSelectField } from '../../../plugins/TableCell';

import {PageType} from '../../placement/PlacementTable';
import ChangeStatus from '../../placement/ChangeStatus';
import Delete from '../../placement/Delete';

const dataAlign: DataAlignType = 'left';

/**
 * To Print the personnel_number
 */
export function personnelNumberCell(filterData: {[key: string]: any}, props: Props ) : TableColumnProps {

    return _printTextField('Per Id','personnel_number', filterData, '80px');
}

/**
 * TO Print the Department Cell
 * @param filterData 
 */
export function userDepartmentCell(filterData: {[key: string]: any}, props: Props ) : TableColumnProps {
    
    return _printSelectOptionsField(props, 'department_staff', 'dept_name', 'Dept', 'user_dept_name', filterData, '120px');    
}

/**
 * TO Print the Division Cell
 * @param filterData 
 */
export function userDivisionCell(filterData: {[key: string]: any}, props: Props ) : TableColumnProps {
    
    return _printTextField('Division','division', filterData, '120px');
}
/**
 * TO Print the Designation Cell
 * @param filterData 
 */
export function userDesignationCell(filterData: {[key: string]: any}, props: Props ) : TableColumnProps {
    
    return _printTextField('Designation','designation', filterData, '120px');
}

/**
 * TO Print the Branch Cell
 * @param filterData 
 */
export function userBranchCell(filterData: {[key: string]: any}, props: Props ) : TableColumnProps {
    
    return _printTextField('Branch','branch', filterData, '120px');
}

/**
 * TO Print the Email Cell
 * @param filterData 
 */
export function userEmailCell(filterData: {[key: string]: any}, props: Props ) : TableColumnProps {
    
    return _printTextField('Email','email', filterData, '120px');
}

/**
 * TO Print the Name Cell
 * @param filterData 
 */
export function userNameCell(filterData: {[key: string]: any}, props: Props ) : TableColumnProps {
    
    return _printTextField('Officer Name','name', filterData, '120px');
}

/**
 * TO Print the Role name Cell
 * @param filterData 
 */
export function userRoleNameCell(filterData: {[key: string]: any}, props: Props ) : TableColumnProps {
    
    return  _printSelectOptionsField(props, 'roles', 'title', 'Role', 'role_name', filterData, '120px');    
}


/**
 * TO Print the Supervisor Name Cell
 * @param filterData 
 */
export function userSupervisporNameCell(filterData: {[key: string]: any}, props: Props ) : TableColumnProps {
    
    return _printTextField('Supervisor','supervisor_name', filterData, '120px');
}

