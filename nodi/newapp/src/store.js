import {createStore} from 'redux'
import Reducers from  './reducers'



const store = createStore(Reducers)

console.log(store.getState());

export default store
