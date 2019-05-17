import * as React from 'react';
import * as ReactDOM from 'react-dom';
import API, {ApiEndPointI} from '../aep';
import * as bs from 'react-bootstrap';
import Loader from 'Components/layout/Loader';
import  {connect } from 'react-redux';
import 'react-bootstrap-table/css/react-bootstrap-table.css';
// import 'abortcontroller-polyfill/dist/abortcontroller-polyfill-only';
// const controller = new AbortController();
var $ = require('jquery');
import SelectFilter from './TableFilters/SelectFilter';
import DateFilter from './TableFilters/DateFilter';
import DateRangeFilter, {ParamValueProps} from './TableFilters/DateRangeFilter';
import moment, { Moment } from 'moment';

import {ServerDataI, FetchDataI, InitialListRequest} from '../reducers/fetch-reducer';
import { actions as rootActions } from '../features/root-action';

// SearchField, SearchPanelProps , 
import { 
    BootstrapTable, 
    SelectRowMode,
    SelectRow,
    ButtonGroupProps, 
    TableHeaderColumn, 
    ColumnDescription,
    CustomFilter,
    SortOrder,
    FilterData,
    SelectFilterOptionsType,
    SearchFieldProps,
    TableHeaderColumnProps, 
    PaginationPostion } from 'react-bootstrap-table';

import { Props, ServerResponse, ServerResponseListData, ListRequest, mapDispatchToProps, mapStateToProps } from "../features/root-props";
import actions from 'redux-form/lib/actions';

const Mode: SelectRowMode = 'checkbox';
/**
 * Table column interface
 */
export interface TableColumnProps extends TableHeaderColumnProps {

    title: string;
}

export interface TableProps extends Props {

    paginationPosition: PaginationPostion;
    columns: TableColumnProps[];
    exportFileName?: string;
    endPoint: ApiEndPointI;
    defaultData?: {[key: string]:  any};
    trClassName ?: string | ((rowData: any, rowIndex: number) => string);
    search?: boolean;
    batchDeleteBtn?: boolean;
    batchDeleteEndPoint?:ApiEndPointI;
    showSelectColumn?: boolean;
    afterPageDropDown?: Array<React.ComponentType<{requestData: ListRequest, rootProps: TableProps }>>;
}
export interface TableArchitecture {

}

class Table extends React.Component <TableProps> implements TableArchitecture {
    
    constructor(props: TableProps) {        
        super(props);
        this.onPageChange = this.onPageChange.bind(this);
        this.searchField  = this.searchField.bind(this);
        this.onSearchChange = this.onSearchChange.bind(this);
        this.onSortChange = this.onSortChange.bind(this);
        this.onSizePerPageList = this.onSizePerPageList.bind(this);
        this.onPageSizeChange = this.onPageSizeChange.bind(this);
        this.createCustomButtonGroup = this.createCustomButtonGroup.bind(this);
        this.exportData = this.exportData.bind(this);
        this.onFilterChange  = this.onFilterChange.bind(this);
        this.selectedRow = this.selectedRow.bind(this);
      
    }
    public onPageChange(page: number, sizePerPage: number) {

        this.props.callApi(this.props.endPoint, {page: page});
    }
    public onSearchChange(e: any) {

        this.props.callApi(this.props.endPoint, {searchdata: e.target.value });
    }
    /**
     * This function works when user sorts the list.
     * @type {void}
     */
    onSortChange(sortName: string, sortOrder: SortOrder ): void {

        this.props.callApi(this.props.endPoint, {sortName: sortName, sortOrder: sortOrder });
    }
    /**
     * This function work when user changes the page size
     * @type {void}
     */
    onSizePerPageList(sizePerPage: number): void {
        
        this.props.callApi(this.props.endPoint, {sizePerPage: sizePerPage, page: 0 });
    }
    /**
     * Display the total record information
     * @param  {[number]} start: number        [description]
     * @param  {[number]} to:    number        [description]
     * @param  {[number]} total: number        [description]
     * @return {[void]}        [description]
     */
   renderShowsTotal(start: number, to: number, total: number): React.ReactElement<HTMLElement> {
        return (
        <p  className="page-count-info">
            Showing { start } to { to } of { total } entries
        </p>
        );
    }

   /**
     * Handle the event when user changes the Page size lenth
     * @param  {[type]} eventKey: any     Selected page length
     * @return {[type]}           void
     */
    onPageSizeChange(eventKey: any): void {

        this.onSizePerPageList(eventKey);
    }

    onFilterChange(filterObject: FilterData) : void {

        const filterKeys = Object.keys(filterObject);
        var customFilter: {[key: string]: any} = {};
        console.log('filterObject', );
        const requestData = this.getRequestData(this.props)
        // console.log(filterObject);
        

        const defaultData = this.props.defaultData ? {...this.props.defaultData} : {};
        const defaultFilterData = defaultData.customFilters ? defaultData.customFilters : {};
        const defaultFilterNames = Object.keys(defaultFilterData);
        //const is
        filterKeys.map((v: string ) => {

            const filterColumn = filterObject[v];
            
            let value = '';
            let comparator = '=';

            if(filterColumn.type === 'NumberFilter') {

                value = filterColumn.value.number;
                comparator = filterColumn.value.comparator;
            }
            else if(filterColumn.type === 'TextFilter') {
                value = filterColumn.value;
                comparator = 'LIKE'
            }
            else if(filterColumn.type === 'SelectFilter') {

                value = filterColumn.value;
                comparator = '='
            }
            else {

                comparator = filterColumn.value.callbackParameters.comparator;
                value = filterColumn.value.callbackParameters.value;
            }

            customFilter[v] = { value: value, comparator: comparator };
        });

        // Check for default data
        defaultFilterNames.map((fieldName: string ) => {
            if(customFilter[fieldName] === undefined && defaultFilterData[fieldName] !== undefined) {
                
                const dv = defaultFilterData[fieldName];
                const filterKeyData = this.props.helper.deepFind(requestData,'customFilters.'+fieldName);

                if( filterKeyData === undefined) {
                    customFilter[fieldName] = { value: dv.value, comparator: dv.comparator };
                }
                else {
                   customFilter[fieldName]  =  filterKeyData;
                }
            }
        })


        this.props.callApi(this.props.endPoint, {customFilters: customFilter });

    }
    /**
     * Rendering the pageSize downdown before the search field.
     * @param  {[type]} props: ButtonGroupProps [description]
     * @return {[type]}        [description]
     */
    createCustomButtonGroup = (props: ButtonGroupProps, requestData: ListRequest ) => {
        const total_selected = requestData.selected.length;

        console.log('this.props.batchDeleteBtn');
        console.log(total_selected);
        console.log(this.props.batchDeleteBtn);
        return (
            <>
              <span>
              <span>Show </span>
              <bs.DropdownButton
                 title={requestData.sizePerPage}
                 id="dropdown-size-large"
              >
                <bs.MenuItem eventKey={5} onSelect={this.onPageSizeChange}>5</bs.MenuItem>
                <bs.MenuItem eventKey={10} onSelect={this.onPageSizeChange}>10</bs.MenuItem>
                <bs.MenuItem eventKey={50} onSelect={this.onPageSizeChange}>50</bs.MenuItem>
                <bs.MenuItem eventKey={100} onSelect={this.onPageSizeChange}>100</bs.MenuItem>
                <bs.MenuItem eventKey={200} onSelect={this.onPageSizeChange}>200</bs.MenuItem>
                <bs.MenuItem eventKey={500} onSelect={this.onPageSizeChange}>500</bs.MenuItem>
                <bs.MenuItem eventKey={1000} onSelect={this.onPageSizeChange}>1000</bs.MenuItem>
              </bs.DropdownButton>
              <span> entries {total_selected ? '& Selcted Row(s) '+ total_selected  : null}</span>
              </span>
                { this.props.exportFileName ? <bs.Button className="btn btn-primary export-btn" onClick={this.exportData}>Export</bs.Button> : null}
                {this.props.batchDeleteBtn && total_selected ? <bs.Button className="btn btn-danger export-btn" onClick={() => this.deleteConfirm(requestData)}>Delete</bs.Button> : null}

                {this.props.afterPageDropDown ? this.props.afterPageDropDown.map(v => {
                        
                    return React.createElement(v, {requestData: requestData, rootProps: this.props});

                }) : null}
              </>
         );
     }

     /**
     * To delete the Selected Rows
     * @param e 
     * @param row 
     */
    deleteConfirm(requestData: ListRequest ) {

        if(this.props.batchDeleteEndPoint) {

            this.props.swal.confirm('Are you sure you want to delete the selected record(s) ?', () => {
                this.props.swal.wait('Deleting...');

                this.props.batchDeleteEndPoint ? this.props.callApi(this.props.batchDeleteEndPoint, {ids: requestData.selected})
                    .then(() => {

                        this.props.dispatch(rootActions.fetch.storeSelectRows(this.props.endPoint, []));
                        this.props.swal.success('Selected record(s) has/have been deleted..');
                        this.props.callApi(this.props.endPoint);

                    }).catch((resposne: ServerResponse) => {

                        this.props.swal.error(resposne.message? resposne.message : 'Server Error.' );
                    }) : null;
            })
        }
    }

     exportData() {

       let endPoint = {...this.props.endPoint};
       endPoint.shouldResponseStore = false;
       endPoint.saveRequest = false;
       var fName = this.props.exportFileName ? this.props.exportFileName : 'test.xlsx';
       this.props.callApi(endPoint, {export: true})
         .then((blob: any) => {

            //this.props.dispatch(rootActions.fetch.storeSelectRows(this.props.endPoint, []));
            if(window.navigator.msSaveOrOpenBlob) {
                
                window.navigator.msSaveBlob(blob, fName);
            }
            else{

                var downloadLink = window.document.createElement('a');
                downloadLink.href = window.URL.createObjectURL(new Blob([blob], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=UTF-8' }));
                downloadLink.download = fName;
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);

            }
         });
     }

    
     shouldComponentUpdate(nextProps: TableProps) {
        
        return this.props.helper.shouldUpdate(nextProps, this.props, [this.props.endPoint.sectionName,'options']);

     }
     componentWillMount() {

        let endPoint = {...this.props.endPoint};
        //endPoint.signal = controller.signal;
        const defaultData = this.props.defaultData ? {...this.props.defaultData} : {};
        let requestData: {[key: string]: any} = this.getRequestData(this.props);

        
        const defaultDataKeys = Object.keys(defaultData);
        defaultDataKeys.map((v) => {

            if(requestData && !requestData[v] ){
                delete requestData[v];
            }
        })   
        
        // console.log('defaultDatadsjjjjjjjjjjjjjjh');
        // console.log(defaultData);
        // console.log(requestData);
       const defaultFDataKeys =  Object.keys(defaultData.customFilters ? defaultData.customFilters : {});
        // console.log('%HHHHHHHHHHHHHHHHHH');
        // console.log(requestData);
       defaultFDataKeys.map((v: string) => {
            if(requestData.customFilters !== undefined && requestData.customFilters[v] === undefined){
                requestData.customFilters[v] = defaultData.customFilters[v];
            }
            else if(requestData.customFilters === undefined) {
                requestData.customFilters = {};
                requestData.customFilters[v] = defaultData.customFilters[v];
            }
       })
        
        const data = {...requestData};
        console.log(data);  
        this.props.callApi(endPoint, data);

     }
     /**
      * Get a perticular section data
      * @param props
      */
     getSectionData(props?: TableProps) : FetchDataI<ServerResponse<ServerResponseListData>> | null {
        props = props ? props : this.props;
        const {  endPoint } = this.props;
        
        const SectionData: FetchDataI<ServerResponse<ServerResponseListData>> | null = 
            props.rootState.server[endPoint.sectionName] ? props.rootState.server[endPoint.sectionName] : null;
        
        return SectionData;
     }

     

     /**
      * Get the Request Data
      * @param props 
      */
     getRequestData(props ?: TableProps) : ListRequest {
        
        props = props ? props :this.props;
        const SectionData = this.getSectionData(props);
        
        const requestData  = SectionData ? SectionData.requestData : {...InitialListRequest, selected: [] };
        return requestData;
     }

     searchField(props: SearchFieldProps, req: ListRequest) {
        
        return <input type="text" 
            onChange={this.onSearchChange} 
            value={req.searchdata}
            className="form-control"
            placeholder="Search"
            />
     }

     /**
      * TO Display the checkbox for selecting the rows.
      * @param requestData 
      */
     selectedRow(requestData: ListRequest): SelectRow {

        return {
            mode: Mode, 
            columnWidth: '30px',
            selected: requestData.selected,
            onSelect: (row: any, isSelected: boolean, event: any, rowIndex: number) => {

                  var aleardySelected = requestData.selected;

                  if(isSelected) {

                      aleardySelected.push(row.id);
                  }else {

                      const index = aleardySelected.indexOf(row.id);
                      if(index !== -1){
                          aleardySelected.splice(index,1);
                      }
                  }                  

                  this.props.dispatch(rootActions.fetch.storeSelectRows(this.props.endPoint, aleardySelected));

            },
            onSelectAll: (isSelected: boolean, rows: Array<{[key: string]: any}>) => {
              //console.log('Is Here..............');
              var aleardySelected = requestData.selected;
              if(isSelected) {
                  // console.log('action for selectded');
                  // console.log(rows);
                  
                  rows.map(function(v) {
                    const index = aleardySelected.indexOf(v.id);
                    if(index === -1){
                      aleardySelected.push(v.id);
                    }
                  })
              } 
              else {
                // console.log('action for unselectded');
                // console.log(rows);
                  rows.map(function(v) {
                      const index = aleardySelected.indexOf(v.id);
                      if(index !== -1){
                          aleardySelected.splice(index,1);
                      }
                  })
              }

              this.props.dispatch(rootActions.fetch.storeSelectRows(this.props.endPoint, aleardySelected));

              return aleardySelected;

            }
            
        }
     }

    /**
     * The function renders the data on the web page.
     * @return {React.ReactElement} 
     */
    public render() {
        
        const { columns, paginationPosition, endPoint, defaultData, search, showSelectColumn } = this.props;

        console.log('Table is rendering...');
        
        const SectionData = this.getSectionData(this.props);
        const isFetching = SectionData ? SectionData.isFetching : true;

        const requestData  = this.getRequestData(this.props);

        const { page, sizePerPage, sortName, sortOrder, searchdata, customFilters  } = requestData;
        const data = SectionData && SectionData.response ? SectionData.response.data : null;
        const list = data ?  data.data : [];

        
        const options = {
          sizePerPage: sizePerPage,
          sizePerPageList: [25 ,100, 200, 500, 1000 ],
          page: data ? data.current_page : 1,
          defaultSortName: sortName ? sortName :  (defaultData && defaultData.sortName ? defaultData.sortName : null),
          defaultSortOrder: sortOrder ? sortOrder :  (defaultData && defaultData.sortOrder ? defaultData.sortOrder : null),
          noDataText: isFetching ? <Loader show={isFetching}/>: 'No Data',
          paginationPosition: paginationPosition,
          onPageChange: this.onPageChange,
          onSearchChange: this.onSearchChange,
          onSortChange: this.onSortChange,
          onSizePerPageList: this.onSizePerPageList,
          paginationShowsTotal: this.renderShowsTotal,
          defaultSearch:  undefined,
          onFilterChange: this.onFilterChange,
          searchField: (fprops: SearchFieldProps) => { return this.searchField(fprops,  requestData) },
         // trClassName:
          //searchField: this.createCustomSearchField,
          btnGroup: (btn: ButtonGroupProps)  => {  return  this.createCustomButtonGroup(btn, requestData)},
        };

        let BTSetitng = {

          footer: false,
          search: search !== undefined ? search : true,
          pagination: true,
          data: list,
          striped: true,
          remote: true,
          selectRow: showSelectColumn === undefined || showSelectColumn ===true ? this.selectedRow(requestData): undefined,
          fetchInfo: { dataTotalSize: data ? data.total: 0 },
          hover: true,
          trClassName:  this.props.trClassName,
          options: options,
          //onFilterChange: this.onFilterChange,
        };

        return (
            <div className={isFetching ? 'loading': 'ready'}>
            <BootstrapTable {...BTSetitng} >
                {(() => {
                    
                    return columns.map( (column: TableColumnProps, index) => {

                        let columnProps = {...column};
                        delete columnProps.title;
                        return < TableHeaderColumn key={`templateTableColumn${index}`} 
                            {...columnProps}>
                           <div className="th-title"> {column.title}</div>
                        </TableHeaderColumn>;
                    });

                })()}
            </BootstrapTable>
            </div>
        );

    }
}

/**
 * Function to display the Select filter in the Table Header
 * @param options 
 * @param value 
 * @param shouldUpdate 
 */
export function getSelectFilter(options: SelectFilterOptionsType, value: string | number | boolean, shouldUpdate: boolean, valueKey?: string, lableKey?: string, whenDataNotLoad?: SelectFilterOptionsType ) : CustomFilter {

    
    return {
        type: 'CustomFilter', 
        getElement: (filterHandler: Function, customFilterParameters: any) => {

            return <SelectFilter 
                callback={customFilterParameters.callback} 
                filterHandler ={filterHandler}
                shouldUpdate={shouldUpdate} 
                options={options}
                placeHolder="All"
                valueKey={valueKey}
                labelKey={lableKey}
                whenDataNotLoad={whenDataNotLoad}
                param={customFilterParameters.callbackParameters}
                />
        },
        customFilterParameters: {
            callback: (cell, param) => { return true},
            callbackParameters: {
                value: value,
                comparator: '='
            }
        }
    }
}

/**
 * Function to display the Select filter in the Table Header
 * @param options 
 * @param value 
 * @param shouldUpdate 
 */
export function getDateFilter(value: string, comparator: string, maxDate?: string, minDate?: string) : CustomFilter {
    return {
        type: 'CustomFilter', 
        getElement: (filterHandler: Function, customFilterParameters: any) => {
            const mxDate = maxDate ? moment(maxDate) : undefined;
            const mnDate = minDate ? moment(minDate) : undefined;

            return <DateFilter maxDate={mxDate} minDate={mnDate}
                    callback={customFilterParameters.callback} 
                    filterHandler ={filterHandler} 
                    param={customFilterParameters.callbackParameters}
                    />
        },
        customFilterParameters: {
            callback: (cell, param) => { return true},
            callbackParameters: {
                value: value,
                comparator: comparator,
            }
        }
    }
}

/**
 * Function to display the Select filter in the Table Header
 * @param options 
 * @param value 
 * @param shouldUpdate 
 */
export function getDateRangeFilter(value: ParamValueProps) : CustomFilter {
    return {
        type: 'CustomFilter', 
        getElement: (filterHandler: Function, customFilterParameters: any) => {
            return <DateRangeFilter
                    callback={customFilterParameters.callback} 
                    filterHandler ={filterHandler} 
                    param={customFilterParameters.callbackParameters}
                    />
        },
        customFilterParameters: {
            callback: (cell, param) => { return true},
            callbackParameters: {
                value: value,
                comparator: 'date-range',
            }
        }
    }
}

//DateRangeFilter

export default Table;