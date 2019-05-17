import { 
    Field, 
    Fields, 
    reduxForm, 
    ValidateCallback, 
    ConfigProps,  
    getFormSubmitErrors, 
    InjectedFormProps, 
    FormErrors, 
    FormAction, 
    FieldsProps, 
    WrappedFieldProps } from 'redux-form';

import  {connect } from 'react-redux';
import * as React from 'react';
import 'react-select/dist/react-select.css';
import Select, {ReactSelectProps, Option, Options, Async, HandlerRendererResult, OptionComponentProps} from 'react-select';
import {ApiEndPointI} from '../../../aep';
import { Label } from 'react-bootstrap';
import {RootState} from '../../../features/root-reducer';
import {actions, RootAction} from '../../../features/root-action';
import {FetchDataI} from '../../../reducers/fetch-reducer';
import {ServerResponse, ServerResponseListData} from '../../../features/root-props';
import { Dispatch} from 'react-redux';
import {FieldGeneralProps} from '../FormInit';

// export function renderNormalList(props: { [key: string]: any }) : HandlerRendererResult {

//     console.log('HHHHHHHHHHHHHHHHHHHHHHHH');
//     return <li>Testeennn</li>;
// }



export type WrappedSelectFieldProps = {
    // label?: string,
    // //className?: string,
    // elementclassName?: string,
    endPoint?: ApiEndPointI,
    server?: FetchDataI<ServerResponse>,
    //options?: Array<{value: string | number,  label: string}>, 
    valueKey?: string,
    labelKey?: string,
    isAsync?: boolean,
    appendOption?: Array<{[key: string]: any}>,
    filterInkeys?: Array<string>;
    //placeholder?: string,
    //multi?: boolean,
    serverError?: string | undefined,
    callApi?: (END_POINT: ApiEndPointI, data?: object, forceUpdate?: boolean ) => Promise<any>

} & WrappedFieldProps & ReactSelectProps & FieldGeneralProps;

export type SelectStateProps = {
    selected: Option<string | number> | Options<string | number> | string | string[] | number | number[] | boolean;
}


/**
 * HTML Text Element With the Error Message
 * @param param0 
 */
class  Input extends React.Component<WrappedSelectFieldProps, SelectStateProps>{

    constructor(props: WrappedSelectFieldProps) {

        super(props);
        this.handleChange = this.handleChange.bind(this);
        this.loadOptions = this.loadOptions.bind(this);
        this.filterItems = this.filterItems.bind(this);
        this.state = {
            selected: this.props.input.value
        }
    }

  

    handleChange(values: any) {

        const labelKey  = this.props.labelKey ? this.props.labelKey : 'name';
        const valueKey  = this.props.valueKey ? this.props.valueKey : 'id';

        const value = values && this.props.multi ? values.map(v => {return v[valueKey]}): ( values ? values[valueKey] : '');        
    
        this.props.input.onChange(value);
        this.setState({selected: values});
    }
       
    async loadOptions(input: string, callback: Function): Promise<any> {
                        
        
        if(!input) {
            if(this.props.endPoint){
                var endpoint = {...this.props.endPoint};
                endpoint.sectionName += '_';
                return Promise.resolve([{options: [], complete: false}]);
            }
        }

        if(this.props.endPoint)
        return this.populateRemoteList({...this.props.endPoint}, input, 10);
    }
    
    /**
     * 
     * @param input keywords
     * @param sizePerPage no of data should be fetch 
     */
    populateRemoteList(endpoint:ApiEndPointI, input: string, sizePerPage: number, forceUpdate?: boolean): Promise<any> {
        
        const props = this.props;

        endpoint.sectionName += '_dropdown_'+ props.input.name;
        if(props.endPoint && props.callApi) {

            var data = {searchdata: input, sortName: 'name', sortOrder: 'asc',sizePerPage: sizePerPage}

            return props.callApi(endpoint, data, forceUpdate).then((res: ServerResponse<ServerResponseListData<{[key: string]: string}>> )=> {
                // console.log('mmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm');
                // console.log(res);
                let SelectData: {options: Array<{[key: string]: string}>, complete: boolean }  = {options: [], complete: false};       
                SelectData.options = res.data && res.data && res.data.data ? res.data.data : [];
                console.log('SelectDataSelectDataSelectDataSelectData');
                console.log(SelectData);
                return Promise.resolve(SelectData);

            })
        }
        return Promise.resolve({
            options: [],
        });
    }

    filterItems(query: string, filterData: Array<string>): boolean {
       
        let isExist = false;
        filterData.map(function(el) {
            
            if( el.toString().toLowerCase().indexOf(query.toLowerCase()) !== -1 ) {
                isExist = true;
            }
        })

        return isExist;
    }
    componentWillMount() {
        // console.log('hhhhhhhhhhhhhhhhh');
        // console.log(t);
        if(!this.props.isAsync)
        if(this.props.endPoint && this.props.callApi) {
            let endPoint =  {...this.props.endPoint};
            endPoint.sectionName += '_dropdown';
            this.props.callApi(endPoint, {sizePerPage: -1}, true);
         }
    }
    
    render() {
        const {input, placeholder, pageSize, isFetching, optionComponent,disabled, isAsync, serverError, options, multi, meta: { touched , error, submitting}, server  } = this.props;
        const hasError = !!error && !!touched || !!serverError;
        
        const {selected } =  this.state;
        const isLoading = server && server.isFetching !== undefined ? server.isFetching : false;

        const SelectOption  = isAsync ? Async : Select;
        const labelKey  = this.props.labelKey ? this.props.labelKey : 'name';
        const valueKey  = this.props.valueKey ? this.props.valueKey : 'id';

        return (
        <>
            <SelectOption
            name={input.name}
            isLoading={isLoading}
            value={selected ? selected : input.value}
            onChange={this.handleChange}
            options={options}
            labelKey={labelKey}
            valueKey={valueKey}
            multi={multi}
            pageSize={pageSize}
            cache={false}
            optionComponent={optionComponent}
            // inputRenderer={ (pp: { [key: string]: any } ) => {return renderNormalList(pp) } }
            filterOption={(option: Option<object>, filter: string) => {
                
                var filterInvalues = [option[valueKey], option[labelKey]]; 

                this.props.filterInkeys && this.props.filterInkeys.map(v => {
                    if(option[v] !== undefined){
                        filterInvalues.push(option[v]);
                    }
                });
                var is = this.filterItems(filter, filterInvalues);

                return is;
            }}
            filterOptions={(options: Options<any>, filter: string, currentValues: Options<any>) => {
            
                return options.filter((item) => {
                    
                    var filterInvalues = [item[valueKey], item[labelKey]];  
                    this.props.filterInkeys && this.props.filterInkeys.map(v => {
                        if(item[v] !== undefined){

                            filterInvalues.push(item[v]);
                        }
                    });
                    var is = this.filterItems(filter, filterInvalues);
                    
                    if(input.value && input.value.indexOf(item[valueKey])  !== -1) {
                        return false;
                    }
                    return is;
                });
            }}
            disabled={isFetching || disabled}
            clearable={true}
            loadOptions={this.loadOptions}
            placeholder={isFetching ? 'Loading...': placeholder}
        />
            {hasError && <span className="help-block">{serverError? serverError : error }</span>}
        </>
        );
    }

}



function mapStateToProps(state: RootState, props: WrappedSelectFieldProps): WrappedSelectFieldProps {
    
    const server =  props.endPoint && props.endPoint.sectionName ? state.server[props.endPoint.sectionName+'_dropdown'] : undefined;
    let options: Array<{[key: string]: any}> = [];

    if(server) {
        
        if(props.appendOption) {
            props.appendOption.map(op => options.push(op));
        }

        server.response && server.response.data.map(opt => options.push(opt));
    }

    return {
      ...props,
      options: options.length ? options : props.options,
      server: server,
    }
}

/**
 * Inject the action into props
 * @param dispatch 
 */
function mapDispatchToProps(dispatch: Dispatch<RootAction>) { 
    return {
      callApi: (END_POINT: ApiEndPointI, data?: object, forceUpdate?: boolean ) =>  dispatch(actions.fetch.callApi(END_POINT, data, forceUpdate))
    }
}

export default connect(mapStateToProps, mapDispatchToProps)( Input); 