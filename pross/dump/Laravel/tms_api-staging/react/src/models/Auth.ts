import  { UserI } from './User';

export interface AuthI extends UserI {

    token: string;
    view_as: 'Admin' | 'Viewer';
}

var  Model  = function (dataObj: any) {

    let signals: {[key: string] : any} = {};
  
    observeData(dataObj)
  
    return {
      ...dataObj,
      observe,
    }
  
    function observe (property: string, signalHandler: any) {
      if(!signals[property]) signals[property] = []
  
      signals[property].push(signalHandler)
    }
  
    function makeReactive (obj: {[key: string] : any}, key: string) {
      let val = obj[key];
  
      Object.defineProperty(obj, key, {
        get () {
          return val
        },
        set (newVal) {
          val = newVal;
        }
      })
    }
  
    function observeData (obj: {[key: string] : any}) {
      for (let key in obj) {
        if (obj.hasOwnProperty(key)) {
          makeReactive(obj, key)
        }
      }
    }
  }

class Auth<AuthI> {

    hello() {
        console.log('kkkkkkkkkkkk...');
    }
    // data: {[key: string]: any};

    // constructor(data: {[key: string]: any}) {
    //     this.data = Model(data);
    // }

    // get(key: string) {

    //     return this.data[key] ? this.data[key] : null
    // }
    // //set(key stru)
  }

  Object.setPrototypeOf(Model.prototype, Auth);

// export class Auth<AuthI> {

//     //data: {[name: string]: any };

//     constructor(data: AuthI) {
//         //this.data = data;
//         console.log('Hell...........');
//         observeData(data);
//     }
//     make(data: AuthI) {
//         console.log('Make..');
//         console.log(data);
//         observeData(data);
//     }
// }

// function observeData (obj: {[key: string]: any}) {
//     for (let key in obj) {
//       if (obj.hasOwnProperty(key)) {
//           console.log('ttttttttttt')
//         makeReactive(obj, key)
//       }
//     }
// }
// function makeReactive (obj: {[key: string]: any}, key: string) {
//     let val = obj[key]
  
//     Object.defineProperty(obj, key, {
//       get () {
//         return val // Simply return the cached value
//       },
//       set (newVal) {
//         val = newVal // Save the newVal
//       }
//     })
//   }

// Object.defineProperty(Text.prototype, "Text", {
//     get: function () { return this.getText(); },
//     set: function (value) { this.setText(value); },
//     enumerable: true,
//     configurable: true
// });

// var handler = {
//     get(target: {[key:string]: any}, name: string) {
//       if (getters.indexOf(name) != -1) {
//         return target[name];
//       }
//       throw new Error('Getter "' + name + '" not found in "Person"');
//     },
//     set(target: {[key:string]: any}, name: string) {

//       if (setters.indexOf(name) != -1) {
//         return target[name];
//       }
//       throw new Error('Setter "' + name + '" not found in "Person"');
//     }
// };

export default Model;