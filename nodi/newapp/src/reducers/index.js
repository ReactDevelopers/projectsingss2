const initialState = {
	count : 0
}

const INCREMENT = 'INCREMENT'
const DECREMENT = 'DECREMENT'

export default function reducers(state =initialState, action){
	switch(action.type){
		case INCREMENT : 
		return {
			count :state.count + 1
		}
		case DECREMENT : 
		return {
			count : state.count - 1
		}
		default : 
		return {
			...state
		}
	}
}