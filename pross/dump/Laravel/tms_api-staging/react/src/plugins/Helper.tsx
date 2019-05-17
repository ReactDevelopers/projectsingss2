import moment from 'moment';
import Cookie from 'js-cookie';
import { Props } from 'react';
import {Props as BaseProps} from '../features/root-props';
import { SelectFilterOptionsType } from 'react-bootstrap-table';
import { Switch } from 'react-router';

export interface HelperProps {

    dateFormat: string;
    dateTimeFormat: string;
    displayDate: (date: string, format?: string) => string;
    displayDateTime: (date: string, format?: string) => string;
    range: (start: number, stop: number, step?: number) => Array<number>;
    isLogin: () => boolean;
    deepFind: (obj: {[key: string]: any} | undefined, path:string, defaultValue?: any) => any;
    shouldUpdate: (next: BaseProps, prev: BaseProps, keys: Array<string>) => boolean;
    isArray: (val: any) => boolean;
    isObject: (val: any) => boolean;
    queryStringToObject: (url: string) => {[key: string]: any};
    queryString: (obj: Object, prefix?: string) => string;
    makeSelectListArray: (data: [{[key: string]: any}],  valueKey: string, labelKey: string) => SelectFilterOptionsType;
    strMatchInArray: (str: string, arr: Array<RegExp>) => number;
    isFloat: (n: any) => boolean;
}

class Helper implements HelperProps {
    
    dateFormat:string = 'DD/MM/YYYY';
    dateTimeFormat:string = 'DD/MM/YYYY LT';

    constructor() {
        this.displayDate = this.displayDate.bind(this);
        this.displayDateTime = this.displayDateTime.bind(this);
        this.range = this.range.bind(this);
        this.deepFind = this.deepFind.bind(this);
        this.shouldUpdate = this.shouldUpdate.bind(this);
        this.makeSelectListArray  = this.makeSelectListArray.bind(this);
    }
    /**
     * To Display the Date
     * @param date 
     */
    displayDate(date: string | null, format?: string) : string {
        
        return date ? moment(date).format(format? format : this.dateFormat) : 'N/A';
    }

    /**
     * To Display the DateTime
     * @param date 
     */
    displayDateTime(date: string | null, format?: string) : string {

        return date ? moment(date).format(format? format : this.dateTimeFormat) : 'N/A';
    }
    /**
     * TO get Array of selected Range
     * @param start 
     * @param stop 
     * @param step 
     */
    range(start: number, stop: number, step?: number): Array<number> {

        if (typeof stop == 'undefined') {
            // one param defined
            stop = start;
            start = 0;
        }
    
        if (typeof step == 'undefined') {
            step = 1;
        }
    
        if ((step > 0 && start >= stop) || (step < 0 && start <= stop)) {
            return [start];
        }
    
        var result = [];
        for (var i = start; step > 0 ? i < stop : i > stop; i += step) {
            result.push(i);
        }
    
        return result;
    }

    /**
     * TO Check user Login
     */
    isLogin() : boolean {

        return Cookie.get('access_token') ? true : false;
    }

    /**
     * Find the key deep in object.
     * @param obj 
     * @param path 
     */
    deepFind(obj: {[key: string]: any} | undefined, path:string, defaultValue?: any): any {

        var paths = path.split('.')
          , current = obj
          , i;
      
        for (i = 0; i < paths.length; ++i) {

          if (!current || current[paths[i]] === undefined) {
              
            return defaultValue;
          } else {
            current = current[paths[i]];
          }
        }
        return current;
    }

    shouldUpdate(next: BaseProps, prev: BaseProps, keys: Array<string>): boolean {

        var isChange: boolean = false;

        keys.map(key => {
            const nextState = next.rootState.server[key] ? next.rootState.server[key].shouldUpdate: false;
            const prevState = prev.rootState.server[key] ? prev.rootState.server[key].shouldUpdate : false;

            if(nextState !== prevState){
                isChange = true;
            }
            
        })
        return isChange;
    }

    /**
     * TO check given veriable is a array
     * @param value 
     */
    isArray (value: any) {

        return value && typeof value === 'object' && value.constructor === Array;
    }

    /**
     * Covert Query string to Object
     */
    queryStringToObject(url: string) : {[key: string]: any} {
        
        var params: {[key: string]: any} = {};
        
        var parser = document.createElement('a');
        parser.href = url;
        var query = parser.search.substring(1);
        var vars = query.split('&');
        for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split('=');
            params[pair[0]] = decodeURIComponent(pair[1]);
        }
        return params;
    }

    /**
     * Convert Query Object to Query String
     * @param obj 
     * @param prefix 
     */
    queryString(obj: Object, prefix?: string): string {
        var str = [],
          p;
        for (p in obj) {
          if (obj.hasOwnProperty(p)) {
            var k = prefix ? prefix + "[" + p + "]" : p,
              v = obj[p];
            str.push((v !== null && typeof v === "object") ?
              this.queryString(v, k) :
              encodeURIComponent(k) + "=" + encodeURIComponent(v? v : ''));
          }
        }
        return str.join("&");
      }

    /**
     * To check the given Veribale is Object
     * @param value 
     */
    isObject (value: any) {
        return value && typeof value === 'object' && value.constructor === Object;
    }
    makeSelectListArray(data: [{[key: string]: any}],  valueKey: string, labelKey: string) {
        
        var options: SelectFilterOptionsType = {};

        if(!this.isArray(data)) {
            return options;
        }

        data.map(v => {
            options[v[valueKey]] = v[labelKey];
        })

        return options;
    }

    strMatchInArray(str: string, arr: Array<RegExp>) {

        var index = -1;
        arr.map((v, k) => {
            if(str.match(v)){
                index = k;
            }
        });

        return index;
    }
    /**
     * To check the value data type is float the integer
     * @param n 
     */
    isFloat(n: any ){
        n = parseFloat(n);
        return Number(n) === n && n % 1 !== 0;
    }


}

export default Helper;