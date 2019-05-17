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

import * as React from 'react';
import {FieldGeneralProps} from '../FormInit';

export type WrappedSwitchFieldProps = {
  
    placeholder?: string,
    serverError?: string | undefined,
    id: string;
    trueValue: string;
    falseValue: string;
    onColor?:string;
    defaultChecked?: boolean;

} & WrappedFieldProps & FieldGeneralProps;

import Switch from "react-switch";

class SwitchElement extends React.Component<WrappedSwitchFieldProps> {
    
    constructor(props: WrappedSwitchFieldProps) {
      
      super(props);
      //this.state = { checked: false };
      this.handleChange = this.handleChange.bind(this);
      
    }

    componentWillMount() {

        if(this.props.input.value === this.props.trueValue){
            this.handleChange(true);
            
        }else{
            
            this.handleChange(this.props.defaultChecked !== undefined ? this.props.defaultChecked : false );
        }
    }

    handleChange(checked: boolean) {

      this.props.input.onChange(checked ? this.props.trueValue : this.props.falseValue);
    }
   
    render() {
        const {id,isFetching, input, trueValue,onColor } = this.props;
      return (
        <label htmlFor={`${id}`}>
          <Switch
            onChange={this.handleChange}
            checked={input.value  === trueValue ? true : false }
            onColor={onColor ? onColor : '#080'}
            disabled={isFetching}
            id={`${id}`}
          />
        </label>
      );
    }
  }

  export default SwitchElement;