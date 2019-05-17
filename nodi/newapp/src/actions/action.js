const INCREMENT = 'INCREMENT'
const DECREMENT = 'DECREMENT'


export function increment(){

	console.log('12345')
	return{
		type : INCREMENT
	}
}

export function  decrement(){
	return{
		type : DECREMENT
	}
}