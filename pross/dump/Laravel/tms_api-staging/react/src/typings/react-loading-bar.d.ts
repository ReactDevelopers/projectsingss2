
declare module 'react-loading-bar' {

	import React = require("react");

	interface LoadingT {
		show: boolean;
		color?: string;
		change?:boolean;
		showSpinner?:boolean;
	}
	export default class Loading extends React.Component<LoadingT, any> {}
}