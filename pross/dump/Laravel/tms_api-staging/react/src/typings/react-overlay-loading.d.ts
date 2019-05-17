
declare module 'react-overlay-loading/lib/OverlayLoader' {

	import React = require("react");

	interface OverlayLoaderT {
		color?: string;
		loader?: string;
		text?: string;
		active: boolean;
		backgroundColor: string;
		opacity: string;

	}
	
	export default class OverlayLoader extends React.Component<OverlayLoaderT, any> {}
}