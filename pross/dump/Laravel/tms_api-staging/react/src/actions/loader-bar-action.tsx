const SHOW_LOADER_BAR = 'SHOW_LOADER_BAR';
const HIDE_LOADER_BAR = 'HIDE_LOADER_BAR';
import { createAction } from 'typesafe-actions';

export const show = createAction(SHOW_LOADER_BAR, () => {
  return   {
            type: SHOW_LOADER_BAR,
            payload: {
                show: true
            }
        }    
})

export const hide = createAction(HIDE_LOADER_BAR, () => {
    return   {
              type: HIDE_LOADER_BAR,
              payload: {
                  show: false
              }
          }    
  })