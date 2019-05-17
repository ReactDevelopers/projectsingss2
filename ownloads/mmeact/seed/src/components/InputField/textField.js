import React from 'react';

const inputField = ( {

field: {...fields},
        form: {touched, errors},
        ...props
    }) => (

    <div className="form-group">
        <input className="form-control b-r-0" {...props} {...fields} invalid={Boolean(touched[fields.name] && errors[fields.name])}/>
        {touched[fields.name] && errors[fields.name] ? <div className="error-message"><label className="c-pink">{errors[fields.name]}</label></div> : ''}
    </div>

);

export default inputField;
