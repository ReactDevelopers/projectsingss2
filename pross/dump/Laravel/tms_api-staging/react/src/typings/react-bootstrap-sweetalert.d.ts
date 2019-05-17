
declare module 'react-bootstrap-sweetalert' {

	import React = require("react");

	interface SweetAlertProps {
		type?: string;
		title?: string;
		customClass?: string;
		text?:string;
		message?: string | React.ReactElement<any>;
		onCancel?: Function;
		onConfirm?: Function;
		onSuccess?: Function;
		onError?: Function;
		close?: Function;
		showConfirm?: boolean;
		btnSize?: string;
		confirmBtnText?: string;
		confirmBtnBsStyle?: string;
		show?: boolean;
		showCancel?: boolean;
		cancelBtnBsStyle?: string;
		displayType?: number;
		cancelBtnText?: any;
		danger?: boolean;
		success?: boolean;
		info?: boolean;
		style?: any;
    	closeOnClickOutside?: boolean;
	}
	export default class Sweetalert extends React.Component<SweetAlertProps, any> {}
}

